/* Hockey5 Wordle
 * - Guess the player's SURNAME in 6 tries
 * - Answer is server-side deterministic per league+puzzleId
 * - Each new game uses a random puzzleId (so the surname is random per game)
 */

(function () {
  const cfg = window.H5_WORDLE || { league: 'oklliga', apiBase: '/api', baseUrl: '' };

  const boardEl = document.getElementById('board');
  const kbEl = document.getElementById('keyboard');
  const toastEl = document.getElementById('toast');

  const giveUpBtn = document.getElementById('giveUpBtn');
  // We'll show the same end-of-game popup style used in Hockey Grid.

  const ALPHABET_ROWS = [
    'QWERTYUIOP',
    'ASDFGHJKL',
    'ZXCVBNM',
  ];

  const MAX_TRIES = 6;

  let answerLen = 5;
  let puzzleId = 0;
  let currentRow = 0;
  let currentCol = 0;
  let locked = false;
  let tiles = []; // [row][col] -> div
  let guesses = Array.from({ length: MAX_TRIES }, () => '');
  let results = Array.from({ length: MAX_TRIES }, () => null); // array of status per col
  let kbState = {}; // letter -> best status

  const ACTIVE_KEY = () => `h5_wordle_active_${cfg.league}`;
  const STORAGE_KEY = () => `h5_wordle_${cfg.league}_${puzzleId}`;

  function showToast(msg) {
    if (!toastEl) return;
    toastEl.textContent = msg;
    toastEl.classList.add('show');
    setTimeout(() => toastEl.classList.remove('show'), 1600);
  }

  function normalizeInput(s) {
    return (s || '')
      .toString()
      .trim()
      .toUpperCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^A-Z]/g, '');
  }

  function statusRank(st) {
    if (st === 'correct') return 3;
    if (st === 'present') return 2;
    if (st === 'absent') return 1;
    return 0;
  }

  function setKeyStatus(letter, st) {
    const prev = kbState[letter];
    if (statusRank(st) > statusRank(prev)) kbState[letter] = st;
  }

  function renderKeyboard() {
    kbEl.innerHTML = '';

    ALPHABET_ROWS.forEach((rowStr, idx) => {
      const row = document.createElement('div');
      row.className = 'w-krow';

      if (idx === 2) {
        // Enter
        row.appendChild(makeKey('ENTER', { big: true }));
      }

      for (const ch of rowStr) {
        row.appendChild(makeKey(ch));
      }

      if (idx === 2) {
        row.appendChild(makeKey('⌫', { big: true, action: 'BACKSPACE' }));
      }

      kbEl.appendChild(row);
    });
  }

  function makeKey(label, opts = {}) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'w-key' + (opts.big ? ' big' : '');
    btn.textContent = label;

    const action = opts.action || label;
    if (/^[A-Z]$/.test(label)) {
      const st = kbState[label];
      if (st) btn.classList.add(st);
    }

    btn.addEventListener('click', () => handleKey(action));
    return btn;
  }

  function renderBoard() {
    boardEl.style.gridTemplateRows = `repeat(${MAX_TRIES}, auto)`;
    boardEl.innerHTML = '';
    tiles = [];

    applyResponsiveSizing();

    for (let r = 0; r < MAX_TRIES; r++) {
      const row = document.createElement('div');
      row.className = 'w-row';
      row.style.gridTemplateColumns = `repeat(${answerLen}, auto)`;
      const rowTiles = [];

      for (let c = 0; c < answerLen; c++) {
        const t = document.createElement('div');
        t.className = 'w-tile';
        t.textContent = '';
        row.appendChild(t);
        rowTiles.push(t);
      }

      boardEl.appendChild(row);
      tiles.push(rowTiles);
    }
  }

  function applyResponsiveSizing() {
    // Make long surnames fit better on small screens by shrinking tiles.
    // We compute a tile size that keeps the row within the board wrapper width.
    try {
      const wrap = document.querySelector('.w-wrap') || document.body;
      const wrapWidth = Math.min(900, Math.floor(wrap.getBoundingClientRect().width || window.innerWidth));
      const baseGap = 8;
      const gaps = (answerLen - 1) * baseGap;
      // Leave some breathing room.
      const usable = Math.max(240, wrapWidth - 24 - gaps);
      const raw = Math.floor(usable / answerLen);
      const tile = Math.max(28, Math.min(44, raw));

      boardEl.style.setProperty('--tile', `${tile}px`);
      boardEl.style.setProperty('--gap', `${baseGap}px`);
    } catch {
      // Ignore sizing errors
    }
  }

  function updateBoardFromState() {
    for (let r = 0; r < MAX_TRIES; r++) {
      const g = guesses[r] || '';
      for (let c = 0; c < answerLen; c++) {
        const ch = g[c] || '';
        const t = tiles[r][c];
        t.textContent = ch;
        t.classList.toggle('filled', !!ch);
        t.classList.remove('correct', 'present', 'absent');
        if (results[r] && results[r][c]) t.classList.add(results[r][c]);
      }
    }
  }

  function handleKey(key) {
    if (locked) return;
    if (key === 'ENTER') return submit();
    if (key === 'BACKSPACE' || key === '⌫') return backspace();

    const ch = normalizeInput(key);
    if (!ch || ch.length !== 1) return;
    if (currentCol >= answerLen) return;
    const current = guesses[currentRow] || '';
    guesses[currentRow] = (current + ch).slice(0, answerLen);
    currentCol = guesses[currentRow].length;
    updateBoardFromState();
  }

  function backspace() {
    if (locked) return;
    const current = guesses[currentRow] || '';
    if (!current.length) return;
    guesses[currentRow] = current.slice(0, -1);
    currentCol = guesses[currentRow].length;
    updateBoardFromState();
  }

  async function submit() {
    if (locked) return;
    const guess = normalizeInput(guesses[currentRow]);
    if (guess.length !== answerLen) {
      showToast(cfg.tr.toast_complete);
      return;
    }

    locked = true;

    try {
      const res = await fetch(`${cfg.apiBase}/wordle_check.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ league: cfg.league, puzzleId, guess, row: currentRow }),
      });
      const data = await res.json();

      if (!res.ok || data?.ok === false) {
        showToast(data?.error || cfg.tr.toast_invalid);
        locked = false;
        return;
      }

      results[currentRow] = data.tiles;
      // Update keyboard state
      for (let i = 0; i < answerLen; i++) {
        setKeyStatus(guess[i], data.tiles[i]);
      }

      updateBoardFromState();
      renderKeyboard();

      const won = !!data.win;
      const done = !!data.done;

      // Use server answer if provided; otherwise, on win we can show the guessed word.
      const resolvedAnswer = data.answer || (won ? guess : null);

      // Persist
      saveState({
        answerLen,
        puzzleId,
        currentRow: won || done ? currentRow : currentRow + 1,
        guesses,
        results,
        kbState,
        finished: won || done,
        win: won,
        answer: resolvedAnswer,
      });

      if (won) {
        showToast(cfg.tr.toast_correct);
        finish(true, resolvedAnswer);
        return;
      }

      if (done) {
        finish(false, resolvedAnswer);
        return;
      }

      currentRow++;
      currentCol = 0;
      locked = false;
    } catch (e) {
      console.error(e);
      showToast(cfg.tr.toast_error);
      locked = false;
    }
  }

  function finish(win, answer) {
    locked = true;
    clearActiveGameMarker();

    if (win) {
      // Victory → show popup
      showEndPopup(true, answer);
    } else {
      // Loss → hide keyboard, show answer in red on board, add buttons
      if (kbEl) kbEl.style.display = 'none';
      if (giveUpBtn) giveUpBtn.style.display = 'none';

      // Show the correct answer in red on the next available row (or last row)
      if (answer && tiles.length) {
        const revealRow = Math.min(currentRow, MAX_TRIES - 1);
        const letters = answer.toString().toUpperCase().split('');
        for (let c = 0; c < answerLen; c++) {
          const t = tiles[revealRow][c];
          if (t) {
            t.textContent = letters[c] || '';
            t.classList.remove('filled', 'correct', 'present', 'absent');
            t.classList.add('revealed');
          }
        }
      }

      // Remove any existing lose-actions bar
      const existing = document.querySelector('.w-lose-actions');
      if (existing) existing.remove();

      // Create action buttons below the board (like Grid does)
      const actionsBar = document.createElement('div');
      actionsBar.className = 'w-lose-actions';

      const btnPlayAgain = document.createElement('button');
      btnPlayAgain.type = 'button';
      btnPlayAgain.textContent = cfg.tr.btn_play_again;
      btnPlayAgain.style.cssText = 'padding:10px 18px;border-radius:12px;border:0;background:rgba(23,200,234,.85);color:#001018;font-weight:900;cursor:pointer;box-shadow:0 16px 40px rgba(0,0,0,.35);';
      btnPlayAgain.addEventListener('click', () => startNewGame());

      actionsBar.appendChild(btnPlayAgain);

      // Report row: "Algun error? Reportar aquí"
      const reportRow = document.createElement('div');
      reportRow.style.cssText = 'margin-top:16px;display:flex;gap:12px;justify-content:center;align-items:center;color:rgba(255,255,255,.90);font-weight:750;width:100%;';

      const reportLabel = document.createElement('span');
      reportLabel.textContent = cfg.tr.report_label || 'Algun error?';

      const reportBtn = document.createElement('button');
      reportBtn.type = 'button';
      reportBtn.textContent = cfg.tr.report_btn || 'Reportar aquí';
      reportBtn.style.cssText = 'padding:8px 14px;border-radius:12px;border:0;background:rgba(23,200,234,.75);color:#001018;font-weight:900;cursor:pointer;';
      reportBtn.addEventListener('click', () => {
        const modal = document.getElementById('wReportModal');
        if (modal) modal.style.display = 'flex';
      });

      reportRow.appendChild(reportLabel);
      reportRow.appendChild(reportBtn);
      actionsBar.appendChild(reportRow);

      // Insert after the board
      const boardParent = boardEl.parentNode;
      const footerSection = document.querySelector('.w-footer');
      if (footerSection) {
        boardParent.insertBefore(actionsBar, footerSection);
      } else {
        boardParent.appendChild(actionsBar);
      }
    }
  }

  async function giveUp() {
    if (locked) return;

    // Show confirmation popup (Standardized Modal)
    const existing = document.getElementById('w-giveup-overlay');
    if (existing) existing.remove();

    const overlay = document.createElement('div');
    overlay.id = 'w-giveup-overlay';
    overlay.className = 'modal-overlay show';

    const card = document.createElement('div');
    card.className = 'modal-card';

    const closeX = document.createElement('button');
    closeX.type = 'button';
    closeX.textContent = 'X';
    closeX.className = 'modal-close';
    closeX.addEventListener('click', () => overlay.remove());

    const msg = document.createElement('div');
    msg.className = 'modal-title';
    msg.textContent = cfg.tr.giveup_confirm;

    const btnRow = document.createElement('div');
    btnRow.className = 'modal-actions';

    const btnYes = document.createElement('button');
    btnYes.type = 'button';
    btnYes.textContent = cfg.tr.giveup_yes;
    btnYes.className = 'modal-btn';
    btnYes.addEventListener('click', () => {
      overlay.remove();
      doGiveUp();
    });

    const btnNo = document.createElement('button');
    btnNo.type = 'button';
    btnNo.textContent = cfg.tr.giveup_no;
    btnNo.className = 'modal-btn';
    btnNo.addEventListener('click', () => overlay.remove());

    btnRow.appendChild(btnYes);
    btnRow.appendChild(btnNo);
    card.appendChild(closeX);
    card.appendChild(msg);
    card.appendChild(btnRow);
    overlay.appendChild(card);
    document.body.appendChild(overlay);
  }

  async function doGiveUp() {
    if (locked) return;
    locked = true;
    try {
      const r = await fetch(`${cfg.apiBase}/wordle_reveal.php?league=${encodeURIComponent(cfg.league)}&puzzleId=${encodeURIComponent(puzzleId)}`);
      const data = await r.json();
      if (!r.ok || data?.ok === false) {
        showToast(data?.error || 'No disponible');
        locked = false;
        return;
      }

      const ans = data.answer || null;

      // Persist finished lose
      saveState({
        answerLen,
        puzzleId,
        currentRow,
        guesses,
        results,
        kbState,
        finished: true,
        win: false,
        answer: ans,
      });

      finish(false, ans);
    } catch (e) {
      console.error(e);
      showToast(cfg.tr.toast_error);
      locked = false;
    }
  }

  function clearActiveGameMarker() {
    try { localStorage.removeItem(ACTIVE_KEY()); } catch { }
  }

  function setActiveGameMarker(pid) {
    try { localStorage.setItem(ACTIVE_KEY(), String(pid)); } catch { }
  }

  function getActiveGameMarker() {
    try {
      const v = localStorage.getItem(ACTIVE_KEY());
      const n = v ? parseInt(v, 10) : 0;
      return Number.isFinite(n) ? n : 0;
    } catch { return 0; }
  }

  function randomPuzzleId() {
    // 31-bit positive int
    return Math.floor(Math.random() * 2147483646) + 1;
  }

  function closeEndPopup() {
    const existing = document.getElementById('h5-wordle-end-overlay');
    if (existing) existing.remove();
  }

  function resetStateForNewGame(newPid, newLen) {
    answerLen = newLen;
    puzzleId = newPid;
    currentRow = 0;
    currentCol = 0;
    locked = false;
    guesses = Array.from({ length: MAX_TRIES }, () => '');
    results = Array.from({ length: MAX_TRIES }, () => null);
    kbState = {};

    renderBoard();
    renderKeyboard();
    updateBoardFromState();

    saveState({
      answerLen,
      puzzleId,
      currentRow: 0,
      guesses,
      results,
      kbState,
      finished: false,
      win: false,
      answer: null,
    });

    setActiveGameMarker(puzzleId);
    closeEndPopup();

    // Restore keyboard and give-up button if they were hidden after a loss
    if (kbEl) kbEl.style.display = '';
    if (giveUpBtn) giveUpBtn.style.display = '';
    const loseActions = document.querySelector('.w-lose-actions');
    if (loseActions) loseActions.remove();
  }

  async function startNewGame() {
    const newPid = randomPuzzleId();
    // Ask the server for the answer length for this league+puzzleId
    const r = await fetch(`${cfg.apiBase}/wordle_meta.php?league=${encodeURIComponent(cfg.league)}&puzzleId=${encodeURIComponent(newPid)}`);
    const meta = await r.json();
    if (!r.ok || meta?.ok === false) {
      showToast(meta?.error || 'No disponible');
      return;
    }

    resetStateForNewGame(meta.puzzleId || newPid, meta.length || 5);
  }

  function showEndPopup(win, answer) {
    closeEndPopup();
    clearActiveGameMarker();

    const overlay = document.createElement('div');
    overlay.id = 'h5-wordle-end-overlay';
    overlay.className = 'win-overlay';

    const card = document.createElement('div');
    card.className = 'win-card';

    // Icon
    const icon = document.createElement('div');
    icon.className = 'win-icon';
    icon.textContent = '🏆';

    const title = document.createElement('div');
    title.className = 'win-title';
    title.textContent = win ? cfg.tr.win_title_win : cfg.tr.win_title_lose;

    const msg = document.createElement('div');
    msg.className = 'win-text';
    if (win) {
      msg.textContent = cfg.tr.win_desc_win;
    } else {
      const a = (answer || '').toString().trim();
      msg.textContent = a ? cfg.tr.win_desc_lose.replace('%s', a) : cfg.tr.win_desc_lose_unknown;
    }

    const btnRow = document.createElement('div');
    btnRow.className = 'win-actions';

    const btnHome = document.createElement('button');
    btnHome.type = 'button';
    btnHome.className = 'win-btn secondary';
    btnHome.textContent = cfg.tr.btn_home;
    btnHome.addEventListener('click', () => (window.location.href = (cfg.baseUrl || '') + '/'));

    const btnPlayAgain = document.createElement('button');
    btnPlayAgain.type = 'button';
    btnPlayAgain.className = 'win-btn primary';
    btnPlayAgain.textContent = cfg.tr.btn_play_again;
    btnPlayAgain.addEventListener('click', () => startNewGame());

    btnRow.appendChild(btnHome);
    btnRow.appendChild(btnPlayAgain);

    card.appendChild(icon);
    card.appendChild(title);
    card.appendChild(msg);
    card.appendChild(btnRow);

    overlay.appendChild(card);
    document.body.appendChild(overlay);
  }

  function emojiFor(st) {
    if (st === 'correct') return '🟩';
    if (st === 'present') return '🟨';
    return '⬛';
  }

  async function share(win, answer) {
    const used = results.filter(Boolean);
    const lines = used.map(r => r.map(emojiFor).join('')).join('\n');
    const score = win ? used.length : 'X';

    const shareUrl = `${location.origin}${(cfg.baseUrl || '')}/jugar/hockey5-wordle/${cfg.league}`;
    const text = `#Hockey5Wordle ${cfg.league.toUpperCase()} · ${score}/${MAX_TRIES}\n${lines}\n${shareUrl}`;

    try {
      await navigator.clipboard.writeText(text);
      showToast(cfg.tr.toast_copied);
    } catch (e) {
      // Fallback
      const ta = document.createElement('textarea');
      ta.value = text;
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
      showToast(cfg.tr.toast_copied);
    }
  }

  function loadState() {
    try {
      const raw = localStorage.getItem(STORAGE_KEY());
      if (!raw) return null;
      return JSON.parse(raw);
    } catch { return null; }
  }

  function saveState(obj) {
    try { localStorage.setItem(STORAGE_KEY(), JSON.stringify(obj)); } catch { }
  }

  function restore(state) {
    answerLen = state.answerLen;
    puzzleId = state.puzzleId;
    guesses = state.guesses;
    results = state.results;
    kbState = state.kbState || {};
    currentRow = Math.min(state.currentRow || 0, MAX_TRIES - 1);
    currentCol = (guesses[currentRow] || '').length;

    renderBoard();
    updateBoardFromState();
    renderKeyboard();

    if (state.finished) {
      finish(!!state.win, state.answer || null);
    }
  }

  async function init() {
    // Reuse an unfinished game (same league) if present; otherwise start a new random game.
    const activePid = getActiveGameMarker();

    if (activePid > 0) {
      const r = await fetch(`${cfg.apiBase}/wordle_meta.php?league=${encodeURIComponent(cfg.league)}&puzzleId=${encodeURIComponent(activePid)}`);
      const meta = await r.json();
      if (!r.ok || meta?.ok === false) {
        // If something fails, just start a new game
        await startNewGame();
      } else {
        answerLen = meta.length;
        puzzleId = meta.puzzleId || activePid;

        const state = loadState();
        if (state && state.puzzleId === puzzleId && state.answerLen === answerLen && !state.finished) {
          restore(state);
          setActiveGameMarker(puzzleId);
        } else {
          resetStateForNewGame(puzzleId, answerLen);
        }
      }
    } else {
      await startNewGame();
    }

    // Physical keyboard
    window.addEventListener('keydown', (e) => {
      if (e.ctrlKey || e.metaKey || e.altKey) return;
      const k = e.key;
      if (k === 'Enter') return handleKey('ENTER');
      if (k === 'Backspace') return handleKey('BACKSPACE');
      if (/^[a-zA-Z]$/.test(k)) return handleKey(k.toUpperCase());
    });

    window.addEventListener('resize', () => applyResponsiveSizing());

    if (giveUpBtn) {
      giveUpBtn.addEventListener('click', () => giveUp());
    }
  }

  init().catch((e) => {
    console.error(e);
    showToast(cfg.tr.toast_init_error);
  });
})();
