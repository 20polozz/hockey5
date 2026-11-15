// assets/hockeygrid.js

const root = document.getElementById("hockey-grid-root");

// ====== Config (ve de index.php) ======
const CFG = window.HOCKEYGRID_CONFIG || {};
// Base URL for PHP API (set in index.php). Default: ../api
const API_BASE = (CFG.apiBase || "../api").replace(/\/+$/, "");

function apiUrl(path, params = {}) {
  const u = new URL(`${API_BASE}/${path}`, window.location.href);
  Object.entries(params).forEach(([k, v]) => {
    if (v !== undefined && v !== null && v !== "") u.searchParams.set(k, String(v));
  });
  return u.toString();
}
// Default to a relative dir so it works under subfolders (e.g., /hoquei-5/hockeygrid/)
const JSON_DIR = CFG.jsonDir || "espana";

// Detecta modes pel jsonDir
const DIR_LC = (JSON_DIR || "").toString().toLowerCase();
const IS_PORTUGAL = DIR_LC.includes("portugal");
const IS_INTERNATIONAL = DIR_LC.includes("international") || DIR_LC.includes("internacional");

// Equip files per país
const SPAIN_TEAMS_FILE = "oklliga_teams.json";
const PORTUGAL_TEAMS_FILE = "placard_teams.json";

// Per mode normal: espanya -> oklliga_teams.json, portugal -> placard_teams.json
// Per internacional: no usem un únic TEAMS_FILE, carreguem els dos i fem union
const TEAMS_FILE =
  CFG.teamsFile ||
  (IS_PORTUGAL ? PORTUGAL_TEAMS_FILE : SPAIN_TEAMS_FILE);

// URL opcional per tornar a "home" (si no ve de config, fem servir la carpeta actual)
const HOME_URL = CFG.homeUrl || new URL("./", window.location.href).toString(); // carpeta on estàs

// ---------- Persistència (mateix tauler fins acabar o rendir-se) ----------
// Guardem l'estat a localStorage per lliga/directori (espana/portugal/internacional)
// perquè amb F5 es mantingui el mateix tauler i (opcionalment) també el progrés.
const STORAGE_KEY = `hockeygrid_state__${DIR_LC || "default"}`;
const STORAGE_VERSION = 1;

function safeParseJSON(str) {
  try { return JSON.parse(str); } catch { return null; }
}

function loadSavedState() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return null;
    const st = safeParseJSON(raw);
    if (!st || st.v !== STORAGE_VERSION) return null;
    if (st.gameOver) return null;
    if (!Array.isArray(st.cols) || !Array.isArray(st.rows)) return null;
    if (st.cols.length !== 3 || st.rows.length !== 3) return null;
    return st;
  } catch {
    return null;
  }
}

function saveState(state) {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
  } catch {
    // ignore
  }
}

function clearSavedState() {
  try { localStorage.removeItem(STORAGE_KEY); } catch { /* ignore */ }
}

function jsonCandidates(filename, base, dirOverride = null) {
  const DIR = dirOverride ?? JSON_DIR;

  const abs = encodeURI(`${DIR}/${filename}`);
  const relFolder = (DIR || "").replace(/^\//, "");
  const rel = encodeURI(`${relFolder}/${filename}`);

  return [
    new URL(abs, base).toString(),
    new URL(rel, base).toString(),
    new URL(`assets/${filename}`, base).toString(),
    new URL(`./assets/${filename}`, base).toString(),
    new URL(filename, base).toString(),
    new URL(`../assets/${filename}`, base).toString(),
    new URL(`../${filename}`, base).toString(),
  ];
}

let allPlayers = [];

let teamLogosRaw = {};
let teamLogosNorm = new Map();

let allowedTeamsNorm = null;
let intlSpainTeamsNorm = null;
let intlPortugalTeamsNorm = null;

let colTeams = [];
let rowTeams = [];

let solutionAssignment = null;

let gameRevealed = false;
let winShown = false;

// ---------- Helpers ----------
function norm(s) {
  return (s || "")
    .toString()
    .trim()
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "");
}

// --- Normalització equips ---
function normalizeTeamName(team) {
  const t = norm(team);

  const map = new Map([
    // Espanya
    ["barca", "barça"],
    ["fc barcelona", "barça"],
    ["f c barcelona", "barça"],
    ["fcb", "barça"],
  ]);

  // ✅ Portugal aliases típics
  // (val també en internacional, perquè també hi haurà equips de Portugal)
  [
    // Benfica
    ["sl benfica", "benfica"],
    ["s l benfica", "benfica"],
    ["sport lisboa e benfica", "benfica"],
    ["benfica", "benfica"],

    // Porto
    ["fc porto", "porto"],
    ["f c porto", "porto"],
    ["futebol clube do porto", "porto"],
    ["porto", "porto"],

    // Sporting
    ["sporting cp", "sporting"],
    ["sporting c p", "sporting"],
    ["sporting clube de portugal", "sporting"],
    ["sporting", "sporting"],

    // Barcelos
    ["oc barcelos", "barcelos"],
    ["o c barcelos", "barcelos"],
    ["barcelos", "barcelos"],

    // Variants amb diacrítics / errors comuns
    ["paço de arcos", "paco de arcos"],
    ["paçao de arcos", "paco de arcos"],
  ].forEach(([a, b]) => map.set(a, b));

  return map.get(t) || t;
}

function isAllowedTeam(teamName) {
  if (!allowedTeamsNorm) return true;
  return allowedTeamsNorm.has(norm(normalizeTeamName(teamName)));
}

// --- Logos ---
function buildTeamLogoMaps(obj) {
  teamLogosRaw = obj || {};
  teamLogosNorm = new Map();
  for (const [k, v] of Object.entries(teamLogosRaw)) {
    teamLogosNorm.set(norm(k), v);
  }
}

function getTeamLogo(teamName) {
  if (!teamName) return null;
  if (teamLogosRaw[teamName]) return teamLogosRaw[teamName];
  return teamLogosNorm.get(norm(teamName)) || null;
}

function titleCaseTeam(team) {
  if (!team) return "";
  const raw = team.toString().trim();
  if (raw.length <= 4 && raw === raw.toUpperCase()) return raw;
  return raw
    .split(/\s+/)
    .map((w) => (w.length ? w[0].toUpperCase() + w.slice(1) : w))
    .join(" ");
}

function shuffleInPlace(arr) {
  for (let i = arr.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [arr[i], arr[j]] = [arr[j], arr[i]];
  }
  return arr;
}

function pickRandomFromArray(arr, k) {
  const copy = arr.slice();
  shuffleInPlace(copy);
  return copy.slice(0, k);
}

// ---------- Players -> teams ----------
function playerTeamsSet(p) {
  const teams = new Set();

  if (p.current_team) teams.add(normalizeTeamName(p.current_team));

  if (Array.isArray(p.previous_team)) {
    p.previous_team.forEach((x) => {
      const cleaned = normalizeTeamName(x);
      if (cleaned && cleaned !== "null" && cleaned !== "undefined") teams.add(cleaned);
    });
  }

  return teams;
}

function isValidForCell(player, rowTeam, colTeam) {
  const teams = playerTeamsSet(player);
  const needRow = normalizeTeamName(rowTeam);
  const needCol = normalizeTeamName(colTeam);
  return teams.has(needRow) && teams.has(needCol);
}

// ---------- Toast ----------
function showToast(title, msg) {
  const old = document.querySelector(".toast");
  if (old) old.remove();

  const t = document.createElement("div");
  t.className = "toast";

  // If title is missing or empty, just show msg
  if (!title) {
    t.innerHTML = `<div class="t-msg">${msg}</div>`;
  } else {
    t.innerHTML = `
       <div class="t-title">${title}</div>
       <div class="t-msg">${msg}</div>
     `;
  }

  document.body.appendChild(t);

  requestAnimationFrame(() => t.classList.add("show"));

  setTimeout(() => {
    t.classList.remove("show");
    setTimeout(() => t.remove(), 200);
  }, 2000); // 2s visible
}

// ---------- Autocomplete ----------
function getMatches(q, limit = 8) {
  const nq = norm(q);
  if (!nq) return [];

  const starts = [];
  const includes = [];

  for (const p of allPlayers) {
    const n = norm(p.name);
    if (n.startsWith(nq)) starts.push(p);
    else if (n.includes(nq)) includes.push(p);
    if (starts.length >= limit) break;
  }

  return starts.concat(includes).slice(0, limit);
}

function renderSuggestions(list) {
  suggestionsEl.innerHTML = "";

  if (!list.length) {
    suggestionsEl.classList.remove("show");
    return;
  }

  list.forEach((p) => {
    const btn = document.createElement("button");
    btn.type = "button";
    btn.className = "suggestion-item";
    btn.textContent = p.name;

    btn.addEventListener("click", (e) => {
      // En prémer una suggerència, la col·loquem directament a la graella
      // sense necessitat de prémer Enter.
      e.preventDefault();
      suggestionsEl.classList.remove("show");
      submitPlayer(p);
      inputEl.value = "";
      inputEl.focus();
    });

    suggestionsEl.appendChild(btn);
  });

  suggestionsEl.classList.add("show");
}

// ---------- Matching per garantir 9 jugadors diferents ----------
function buildCandidatesForGrid(rows, cols) {
  const cells = [];
  for (const r of rows) {
    for (const c of cols) {
      const list = [];
      for (const p of allPlayers) {
        if (isValidForCell(p, r, c)) list.push(p);
      }
      cells.push({ key: `${r}||${c}`, candidates: list });
    }
  }
  cells.sort((a, b) => a.candidates.length - b.candidates.length);
  return cells;
}

function findUniqueAssignment(rows, cols) {
  const cells = buildCandidatesForGrid(rows, cols);
  if (cells.some((c) => c.candidates.length === 0)) return null;

  const used = new Set();
  const assignment = new Map();

  function backtrack(i) {
    if (i === cells.length) return true;

    const cell = cells[i];
    const cand = cell.candidates.slice();
    shuffleInPlace(cand);

    for (const p of cand) {
      const k = norm(p.name);
      if (used.has(k)) continue;

      used.add(k);
      assignment.set(cell.key, p);

      if (backtrack(i + 1)) return true;

      assignment.delete(cell.key);
      used.delete(k);
    }
    return false;
  }

  return backtrack(0) ? assignment : null;
}

// ---------- Adjacency + tria equips garantint solució ----------
function buildAdjacency(players) {
  const adj = new Map();

  const ensure = (t) => {
    if (!adj.has(t)) adj.set(t, new Set());
    return adj.get(t);
  };

  for (const p of players) {
    const ts = Array.from(playerTeamsSet(p));
    for (let i = 0; i < ts.length; i++) {
      for (let j = i + 1; j < ts.length; j++) {
        const a = ts[i];
        const b = ts[j];
        ensure(a).add(b);
        ensure(b).add(a);
      }
    }
  }

  return adj;
}

function intersect3(aSet, bSet, cSet) {
  if (!aSet || !bSet || !cSet) return [];
  const sets = [aSet, bSet, cSet].sort((x, y) => x.size - y.size);
  const [s1, s2, s3] = sets;
  const out = [];
  for (const v of s1) {
    if (s2.has(v) && s3.has(v)) out.push(v);
  }
  return out;
}

function hasInternationalMix(arr) {
  // REQUIREMENT UPDATE: International grid now uses all teams, no enforced mix of Spain/Portugal.
  return true;
}

function pickRandomTeamsWithSolutions(players) {
  solutionAssignment = null;

  const adj = buildAdjacency(players);

  const candidates = Array.from(adj.keys()).filter(
    (t) => (adj.get(t)?.size || 0) >= 3 && isAllowedTeam(t)
  );

  candidates.sort((a, b) => adj.get(b).size - adj.get(a).size);

  const N = Math.min(90, candidates.length);
  const top = candidates.slice(0, N);

  for (let tries = 0; tries < 15000; tries++) {
    const cols = pickRandomFromArray(top, 3);
    const [c1, c2, c3] = cols;

    const rowsCandidates = intersect3(adj.get(c1), adj.get(c2), adj.get(c3))
      .filter((r) => !cols.includes(r))
      .filter((r) => isAllowedTeam(r));

    if (rowsCandidates.length < 3) continue;

    const rows = pickRandomFromArray(rowsCandidates, 3);

    if (!hasInternationalMix(cols) || !hasInternationalMix(rows)) continue;

    const assignment = findUniqueAssignment(rows, cols);
    if (assignment) {
      solutionAssignment = assignment;
      return { cols, rows };
    }
  }

  showToast("Error", CFG.tr.error_not_enough_data);
  return { cols: [], rows: [] };
}

// ---------- UI Grid ----------
function headerInnerHTML(team) {
  const label = titleCaseTeam(team);
  const logo = getTeamLogo(label) || getTeamLogo(team);

  return `
    ${logo
      ? `<img class="logo" src="${logo}" alt="${label}" loading="lazy" referrerpolicy="no-referrer" />`
      : `<div class="logo-badge" aria-hidden="true">${label.slice(0, 3).toUpperCase()}</div>`
    }
    <div class="strip">${label}</div>
  `;
}

function buildGrid() {
  root.innerHTML = "";

  const grid = document.createElement("div");
  grid.className = "grid";

  const brand = document.createElement("div");
  brand.className = "tile brand";
  brand.innerHTML = `
    <div class="brand-text">
      <span class="a">HOCKEY</span><span class="b">5</span><br/>
      <span class="b">GRID</span>
    </div>
  `;
  grid.appendChild(brand);

  colTeams.forEach((t) => {
    const d = document.createElement("div");
    d.className = "tile header header-top";
    d.innerHTML = headerInnerHTML(t);
    grid.appendChild(d);
  });

  rowTeams.forEach((r) => {
    const rowH = document.createElement("div");
    rowH.className = "tile header left";
    rowH.innerHTML = headerInnerHTML(r);
    grid.appendChild(rowH);

    colTeams.forEach((c) => {
      const cell = document.createElement("div");
      cell.className = "tile playable";
      cell.dataset.row = r;
      cell.dataset.col = c;
      cell.innerHTML = `<div class="plus">+</div>`;
      grid.appendChild(cell);
    });
  });

  root.appendChild(grid);
}

// ---------- Validació usuari ----------
function findPlayerByName(inputName) {
  const needle = norm(inputName);
  return allPlayers.find((p) => norm(p.name) === needle) || null;
}

function clearNoPlaceMessage() {
  if (statusEl.classList.contains("no-place")) {
    statusEl.classList.remove("no-place");
    updateStatus();
  }
}

function setNoPlaceMessage(playerName) {
  statusEl.textContent = CFG.tr.status_no_place_for.replace('%s', playerName);
  statusEl.classList.add("no-place");
}

function initials(name) {
  const parts = (name || "").trim().split(/\s+/).filter(Boolean);
  const a = parts[0]?.[0] || "";
  const b = parts.length > 1 ? parts[parts.length - 1][0] : "";
  return (a + b).toUpperCase();
}

function setCellValue(cell, playerName) {
  cell.dataset.playerKey = norm(playerName);
  cell.innerHTML = `
    <div class="player-card">
      <div class="player-initials">
        <div class="big">${initials(playerName)}</div>
        <div class="small">${playerName}</div>
      </div>
    </div>
  `;
}

function countCorrect() {
  return document.querySelectorAll(".tile.playable.answer-filled.user-correct").length;
}

function updateStatus() {
  const filled = countCorrect();
  statusEl.textContent = filled ? CFG.tr.status_correct_count.replace('%s', filled) : "";
}

function snapshotProgress() {
  const filled = Array.from(document.querySelectorAll(".tile.playable.answer-filled.user-correct"))
    .map((cell) => ({
      row: cell.dataset.row || "",
      col: cell.dataset.col || "",
      name: (cell.querySelector(".player-initials .small")?.textContent || "").trim(),
    }))
    .filter((x) => x.row && x.col && x.name);
  return filled;
}

function persistGameState(extra = {}) {
  // Només persistim si el joc està en marxa
  if (gameRevealed) return;

  const sol = solutionAssignment
    ? Array.from(solutionAssignment.entries()).map(([key, p]) => ({ key, name: p?.name || "" }))
    : [];

  saveState({
    v: STORAGE_VERSION,
    gameOver: false,
    cols: colTeams.slice(),
    rows: rowTeams.slice(),
    solution: sol,
    filled: snapshotProgress(),
    ...extra,
  });
}

function hideControlsAfterWinOrReveal() {
  if (answerbarEl) answerbarEl.style.display = "none";

  const possibleGiveUpButtons = [
    document.getElementById("reveal-btn"),
    document.getElementById("giveup-btn"),
    document.getElementById("surrender-btn"),
    document.querySelector(".reveal-btn"),
    document.querySelector(".giveup-btn"),
    document.querySelector(".surrender-btn"),
    document.querySelector("[data-action='reveal']"),
    document.querySelector("[data-action='giveup']"),
  ].filter(Boolean);

  possibleGiveUpButtons.forEach((b) => (b.style.display = "none"));
}

function lockGrid() {
  const cells = Array.from(document.querySelectorAll(".tile.playable"));
  for (const cell of cells) {
    cell.classList.add("locked");
    cell.setAttribute("aria-disabled", "true");
  }
}

function showWinPopup() {
  if (winShown) return;
  winShown = true;

  // Partida completada: permetre tauler nou en recarregar.
  clearSavedState();

  gameRevealed = true;
  lockGrid();
  hideControlsAfterWinOrReveal();

  const overlay = document.createElement("div");
  overlay.className = "win-overlay";

  const modal = document.createElement("div");
  modal.className = "win-card";

  // Icono (Check/Copa)
  const icon = document.createElement("div");
  icon.className = "win-icon";
  icon.innerHTML = "🏆";

  const title = document.createElement("div");
  title.className = "win-title";
  title.textContent = CFG.tr.win_title;

  const msg = document.createElement("div");
  msg.className = "win-text";
  msg.textContent = CFG.tr.win_desc;

  const btnRow = document.createElement("div");
  btnRow.className = "win-actions";

  const btnHome = document.createElement("button");
  btnHome.type = "button";
  btnHome.className = "win-btn secondary";
  btnHome.textContent = CFG.tr.btn_home;
  btnHome.addEventListener("click", () => (window.location.href = HOME_URL));

  const btnPlayAgain = document.createElement("button");
  btnPlayAgain.type = "button";
  btnPlayAgain.className = "win-btn primary";
  btnPlayAgain.textContent = CFG.tr.btn_play_again;
  btnPlayAgain.addEventListener("click", () => window.location.reload());

  btnRow.appendChild(btnHome);
  btnRow.appendChild(btnPlayAgain);

  modal.appendChild(icon);
  modal.appendChild(title);
  modal.appendChild(msg);
  modal.appendChild(btnRow);

  overlay.appendChild(modal);
  document.body.appendChild(overlay);
}

function maybeWin() {
  if (gameRevealed) return;
  if (countCorrect() === 9) showWinPopup();
}

function submitPlayer(player) {
  // Limitem l'ús d'un jugador a un màxim de 2 cops a tota la graella.
  // IMPORTANT: comptem per dataset.playerKey a TOTES les caselles (no només les que tenen classes),
  // perquè així no depenem de cap classe CSS/estat i evitem casos on el comptador fallava.
  const pk = norm(player.name);
  const usedCount = Array.from(document.querySelectorAll(".tile.playable"))
    .map((c) => c.dataset.playerKey)
    .filter((k) => k === pk).length;

  if (usedCount >= 2) {
    showToast(CFG.tr.toast_limit, CFG.tr.toast_limit_desc);
    return;
  }

  clearNoPlaceMessage();

  const empties = Array.from(document.querySelectorAll(".tile.playable:not(.answer-filled)"));
  let target = null;

  for (const cell of empties) {
    if (isValidForCell(player, cell.dataset.row, cell.dataset.col)) {
      target = cell;
      break;
    }
  }

  if (target) {
    setCellValue(target, player.name);
    target.classList.add("answer-filled", "user-correct");

    // Guarda progrés perquè amb F5 es mantingui.
    persistGameState();

    inputEl.value = "";
    suggestionsEl.classList.remove("show");

    updateStatus();
    maybeWin();
    return;
  }

  showToast(CFG.tr.toast_no_valid_pos, CFG.tr.toast_no_place.replace('%s', player.name));
}

function handleSubmit() {
  if (gameRevealed) return;

  const typed = inputEl.value.trim();
  if (!typed) return;

  const player = findPlayerByName(typed);
  if (!player) {
    showToast(CFG.tr.toast_invalid_player, CFG.tr.toast_not_exist.replace('%s', typed));
    return;
  }

  submitPlayer(player);
}

// ---------- Mostrar solució ----------
function revealSolutions() {
  if (gameRevealed) return;
  gameRevealed = true;

  // L'usuari s'ha rendit / ha revelat: permetre tauler nou en recarregar.
  clearSavedState();

  hideControlsAfterWinOrReveal();

  inputEl.value = "";
  inputEl.disabled = true;
  suggestionsEl.classList.remove("show");

  const used = new Set();
  document.querySelectorAll(".tile.playable.answer-filled").forEach((cell) => {
    if (cell.dataset.playerKey) used.add(cell.dataset.playerKey);
  });

  const cells = Array.from(document.querySelectorAll(".tile.playable"));
  for (const cell of cells) {
    cell.classList.add("locked");
    cell.setAttribute("aria-disabled", "true");

    if (cell.classList.contains("answer-filled")) continue;

    const r = cell.dataset.row;
    const c = cell.dataset.col;
    const key = `${r}||${c}`;

    let sol = solutionAssignment ? solutionAssignment.get(key) : null;
    if (sol && used.has(norm(sol.name))) sol = null;

    if (!sol) {
      for (const p of allPlayers) {
        const pk = norm(p.name);
        if (used.has(pk)) continue;
        if (isValidForCell(p, r, c)) {
          sol = p;
          break;
        }
      }
    }

    if (sol) {
      used.add(norm(sol.name));
      setCellValue(cell, sol.name);
      cell.classList.add("answer-filled", "solution-fill", "not-guessed");
    } else {
      cell.dataset.playerKey = "";
      cell.innerHTML = `
        <div class="player-card">
          <div class="player-initials">
            <div class="big">?</div>
            <div class="small">Sin datos</div>
          </div>
        </div>
      `;
      cell.classList.add("answer-filled", "solution-fill", "not-guessed");
    }
  }

  statusEl.classList.remove("no-place");
  statusEl.textContent = CFG.tr.status_solution;
}

// Exposa funcions per index.php
window.revealSolutions = revealSolutions;
window.getGridCols = () => colTeams.map((t) => titleCaseTeam(t));
window.getGridRows = () => rowTeams.map((t) => titleCaseTeam(t));
window.clearGridState = clearSavedState;

// ---------- DOM refs ----------
const inputEl = document.getElementById("player-input");
const statusEl = document.getElementById("status");
const answerbarEl = document.querySelector(".answerbar");
const suggestionsEl = document.getElementById("suggestions");

// ---------- Events ----------
inputEl.addEventListener("input", () => {
  if (gameRevealed) return;
  clearNoPlaceMessage();
  renderSuggestions(getMatches(inputEl.value.trim(), 8));
});

inputEl.addEventListener("keydown", (e) => {
  if (e.key === "Enter") {
    // Check if an item is active
    const active = suggestionsEl.querySelector(".suggestion-item.active");
    if (active) {
      e.preventDefault();
      active.click(); // Trigger the click handler (submitPlayer)
    } else {
      // If none active, check if there are ANY suggestions visible and pick the first one
      const first = suggestionsEl.querySelector(".suggestion-item");
      if (first) {
        e.preventDefault();
        first.click();
      } else {
        suggestionsEl.classList.remove("show");
        handleSubmit();
      }
    }
  } else if (e.key === "ArrowDown") {
    e.preventDefault();
    const items = suggestionsEl.querySelectorAll(".suggestion-item");
    if (!items.length) return;

    const active = suggestionsEl.querySelector(".suggestion-item.active");
    let next;

    if (!active) {
      next = items[0];
    } else {
      active.classList.remove("active");
      next = active.nextElementSibling;
      if (!next) next = items[0]; // Loop to top
    }

    if (next) {
      next.classList.add("active");
      next.scrollIntoView({ block: "nearest" });
    }
  } else if (e.key === "ArrowUp") {
    e.preventDefault();
    const items = suggestionsEl.querySelectorAll(".suggestion-item");
    if (!items.length) return;

    const active = suggestionsEl.querySelector(".suggestion-item.active");
    let prev;

    if (!active) {
      prev = items[items.length - 1]; // Start at bottom
    } else {
      active.classList.remove("active");
      prev = active.previousElementSibling;
      if (!prev) prev = items[items.length - 1]; // Loop to bottom
    }

    if (prev) {
      prev.classList.add("active");
      prev.scrollIntoView({ block: "nearest" });
    }
  } else if (e.key === "Escape") {
    suggestionsEl.classList.remove("show");
  }
});

document.addEventListener("click", (e) => {
  if (!answerbarEl.contains(e.target)) suggestionsEl.classList.remove("show");
});

// ---------- Fetch JSON robust ----------
async function fetchJsonFirstOk(urls) {
  let lastErr = null;

  for (const u of urls) {
    try {
      const res = await fetch(u, { cache: "no-store" });
      if (!res.ok) throw new Error(`HTTP ${res.status} (${u})`);

      const text = await res.text();
      if (text.trim().startsWith("<")) {
        throw new Error(`Resposta no-JSON (sembla HTML) a ${u}: ${text.slice(0, 120)}.`);
      }

      return JSON.parse(text);
    } catch (e) {
      lastErr = e;
    }
  }

  throw lastErr || new Error("No he pogut carregar cap URL");
}

// ---------- Init ----------
async function init() {
  try {
    if (window.location.protocol === "file:") {
      throw new Error(
        "Estàs obrint la web amb file://. Cal un servidor (php -S localhost:8000 o XAMPP) perquè fetch pugui llegir el JSON."
      );
    }

    // 1) Carrega whitelist d'equips des de MySQL via API
    //    espana -> oklliga, portugal -> placard, internacional -> union
    // 1) Carrega whitelist d'equips des de MySQL via API
    //    espana -> oklliga, portugal -> placard, internacional -> ALL
    try {
      if (IS_INTERNATIONAL) {
        // Updated logic: fetch ALL teams via single endpoint
        const teams = await fetchJsonFirstOk([apiUrl("hockeygrid_teams.php", { league: "internacional" })]);
        allowedTeamsNorm = new Set((teams || []).map((t) => norm(normalizeTeamName(t))));

        // Disable country-specific mix checks
        intlSpainTeamsNorm = null;
        intlPortugalTeamsNorm = null;
      } else {
        const lg = IS_PORTUGAL ? "portugal" : "espana";
        const teams = await fetchJsonFirstOk([apiUrl("hockeygrid_teams.php", { league: lg })]);
        allowedTeamsNorm = new Set((teams || []).map((t) => norm(normalizeTeamName(t))));
      }
    } catch (e) {
      console.warn("No he pogut carregar llista d’equips (no filtro equips):", e);
      allowedTeamsNorm = null;
    }

    // 2) Carrega players des de MySQL via API
    //    internacional -> tots; si no, espana o portugal.
    const merged = [];
    const seen = new Set();
    const leagueToLoad = IS_INTERNATIONAL ? "internacional" : (IS_PORTUGAL ? "portugal" : "espana");

    try {
      const data = await fetchJsonFirstOk([apiUrl("hockeygrid_players.php", { league: leagueToLoad })]);
      if (Array.isArray(data)) {
        for (const p of data) {
          const key = `${norm(p.name)}|${p.birthYear || ""}`;
          if (seen.has(key)) continue;
          seen.add(key);
          merged.push(p);
        }
      }
    } catch (e) {
      console.warn("No he pogut carregar players de l'API:", e);
    }

    function playerHasAllowedTeam(p) {
      if (!allowedTeamsNorm) return true;
      const teams = playerTeamsSet(p);
      for (const t of teams) {
        if (allowedTeamsNorm.has(norm(t))) return true;
      }
      return false;
    }

    // Internacional = tot aleatori dins l'unió (mateix filtre: “ha d’estar en equips permesos”)
    allPlayers = merged.filter(playerHasAllowedTeam);
    allPlayers.sort((a, b) => norm(a.name).localeCompare(norm(b.name)));

    // 3) teamlogos des de MySQL via API
    try {
      const leagueForLogos = IS_INTERNATIONAL ? "internacional" : (IS_PORTUGAL ? "portugal" : "espana");
      const logos = await fetchJsonFirstOk([apiUrl("teamlogos.php", { league: leagueForLogos })]);
      buildTeamLogoMaps(logos);
    } catch (e) {
      console.warn("No he pogut carregar teamlogos (API):", e);
    }

    // 4) Intenta recuperar una partida en curs (mateix tauler fins acabar)
    const saved = loadSavedState();
    if (saved) {
      colTeams = saved.cols.slice(0, 3);
      rowTeams = saved.rows.slice(0, 3);

      // Reconstruïm la solució (si existeix) un cop tenim allPlayers
      solutionAssignment = null;
      if (Array.isArray(saved.solution) && saved.solution.length) {
        const m = new Map();
        for (const it of saved.solution) {
          if (!it || !it.key || !it.name) continue;
          const p = findPlayerByName(it.name);
          if (p) m.set(it.key, p);
        }
        if (m.size) solutionAssignment = m;
      }

      buildGrid();

      // Restaura el progrés (si n'hi ha)
      if (Array.isArray(saved.filled)) {
        for (const f of saved.filled) {
          if (!f?.row || !f?.col || !f?.name) continue;
          const cell = document.querySelector(
            `.tile.playable[data-row="${CSS.escape(f.row)}"][data-col="${CSS.escape(f.col)}"]`
          );
          if (!cell) continue;
          // Només restaurem si el jugador és vàlid per la casella
          const p = findPlayerByName(f.name);
          if (p && isValidForCell(p, f.row, f.col)) {
            setCellValue(cell, p.name);
            cell.classList.add("answer-filled", "user-correct");
          }
        }
      }

      updateStatus();
      // Assegura que l'estat segueix coherent (per exemple, si hi ha canvis)
      persistGameState();
      return;
    }

    // 5) Nova partida
    const pick = pickRandomTeamsWithSolutions(allPlayers);
    colTeams = pick.cols;
    rowTeams = pick.rows;

    if (colTeams.length !== 3 || rowTeams.length !== 3) return;

    buildGrid();
    updateStatus();
    persistGameState();
  } catch (err) {
    console.error("INIT error:", err);
    showToast("Error carregant JSON", String(err));
  }
}

init();
