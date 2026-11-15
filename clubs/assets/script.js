document.addEventListener('DOMContentLoaded', () => {
    // Config Injection
    const CFG = window.H5_CLUBS || {};
    const apiBase = CFG.apiBase || '/api'; // Fallback to /api
    const league = CFG.league || 'internacional';
    const I18N = CFG.tr || {};

    // State
    let currentTeamId = null;
    let validPlayers = []; // The team's players
    let slots = [null, null, null, null, null]; // 5 slots: GK, DEF, DEF, FWD, FWD
    let usedTeamIds = []; // Track teams used in this session
    const STORAGE_KEY = `h5_clubs_state_${league}`;

    // DOM Elements
    const els = {
        // Removed selector elements
        gameContainer: document.getElementById('game-container'),
        gameWrap: document.getElementById('game-wrap'),
        teamName: document.getElementById('team-name'),
        teamLogoPlaceholder: document.querySelector('.team-logo-placeholder'),
        teamLogo: document.getElementById('team-logo'),
        input: document.getElementById('player-input'),
        suggestions: document.getElementById('suggestions'),
        giveup: document.getElementById('giveup'),
        slots: document.querySelectorAll('.player-slot')
    };

    // --- GAME START: Load Team (Persistent) ---
    function startNewGame(forceNew = false) {
        // UI Reset
        if (window.H5_CLUBS_UI_RESET) window.H5_CLUBS_UI_RESET();

        const controlsArea = document.querySelector('.controls-area');
        const endActions = document.getElementById('endActions');

        // Show Controls, Hide Actions
        if (controlsArea) {
            controlsArea.style.display = 'flex';
            controlsArea.style.visibility = 'visible';
        }
        if (endActions) {
            endActions.classList.remove('show');
            endActions.style.display = 'none';
        }

        // Re-enable input
        if (els.input) {
            els.input.disabled = false;
            els.input.value = '';
        }

        resetGame();

        // Check for saved state unless forcing new game
        const saved = forceNew ? null : loadSavedState();

        if (saved && !saved.finished && saved.teamId) {
            loadingState(true);
            fetchTeamData(saved.teamId, saved);
        } else {
            usedTeamIds = []; // Reset on new game
            loadingState(true);
            fetchRandomTeam();
        }
    }

    function loadingState(isLoading) {
        els.teamName.textContent = isLoading ? "Loading..." : "";
    }

    function fetchRandomTeam() {
        let url = `${apiBase}/clubs.php?action=get_random_match&league=${league}`;
        if (usedTeamIds.length > 0) {
            url += `&exclude_ids=${usedTeamIds.join(',')}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                handleTeamData(data);
                saveCurrentState(); // Store new team
            })
            .catch(err => {
                console.error(err);
                els.teamName.textContent = "Error loading team";
            });
    }

    function showToast(title, msg) {
        const old = document.querySelector(".toast");
        if (old) old.remove();

        const t = document.createElement("div");
        t.className = "toast";

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
        }, 2000);
    }

    function fetchTeamData(id, savedState) {
        fetch(`${apiBase}/clubs.php?action=get_roster&team_id=${id}`)
            .then(res => res.json())
            .then(data => {
                handleTeamData(data);
                if (savedState) restoreProgress(savedState);
            })
            .catch(err => {
                console.error(err);
                // If fetch fails, fall back to new random team
                fetchRandomTeam();
            });
    }

    function handleTeamData(data) {
        loadingState(false);
        if (data.error) {
            showToast("Error", (I18N.errorLoad || "Error") + ": " + data.error);
            return;
        }

        // Render Team Info
        const team = data.team;
        currentTeamId = team.id;
        els.teamName.textContent = team.name;

        // Track used teams
        if (!usedTeamIds.includes((currentTeamId))) {
            usedTeamIds.push(currentTeamId);
        }

        if (team.logo) {
            els.teamLogo.src = team.logo;
            els.teamLogo.style.display = 'block';
            if (els.teamLogoPlaceholder) els.teamLogoPlaceholder.style.display = 'none';
        } else {
            els.teamLogo.style.display = 'none';
            if (els.teamLogoPlaceholder) els.teamLogoPlaceholder.style.display = 'block';
        }

        // Store Roster
        validPlayers = data.players;
    }

    function saveCurrentState(finished = false) {
        const state = {
            teamId: currentTeamId,
            usedTeamIds: usedTeamIds,
            finished: finished,
            slots: slots.map(s => s ? s.id : null) // Store only IDs
        };
        localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    }

    function loadSavedState() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : null;
        } catch (e) {
            return null;
        }
    }

    function restoreProgress(state) {
        if (state.usedTeamIds) usedTeamIds = state.usedTeamIds;
        if (!state.slots) return;
        state.slots.forEach((id, idx) => {
            if (id) {
                // Since this is multi-team, we can't easily find the player in the CURRENT validPlayers
                // if they belonged to a previous team. 
                // However, restoreProgress is called when loading the INITIAL state.
                // In a multi-team flow, the "currentTeamId" in state is the one we should fetch roster for.
                // But the PREVIOUS slots might contain players from other teams.
                // To fix this properly, we'd need to store player names/roles too, or fetch them.
                // For now, let's just make sure they ARE in the validPlayers (which they won't be if team changed).
                const p = validPlayers.find(v => v.id == id);
                if (p) fillSlot(idx, p);
                else {
                    // Fallback: if not in roster, we might need a more robust persistence.
                    // For now, multi-team persistence is tricky if we only store IDs.
                }
            }
        });
    }

    function fillSlot(index, player) {
        const targetSlot = els.slots[index];
        targetSlot.innerHTML = `<div class="slot-name">${player.name}</div>`;
        targetSlot.classList.remove('revealed', 'incorrect');
        targetSlot.classList.add('filled', 'correct');
        slots[index] = player;
    }

    function resetGame() {
        slots = [null, null, null, null, null];
        usedTeamIds = [];
        els.slots.forEach(slot => {
            slot.classList.remove('filled', 'correct', 'incorrect', 'revealed');
            slot.classList.remove('selected');
            slot.innerHTML = `<div class="slot-label">${slot.dataset.role}</div>`;
        });
        clearInput();
        currentTeamId = null;
        validPlayers = [];
    }

    function clearInput() {
        if (els.input) {
            els.input.value = '';
            els.input.focus();
        }
        if (searchTimeout) {
            clearTimeout(searchTimeout);
            searchTimeout = null;
        }
        if (els.suggestions) {
            els.suggestions.classList.remove('show');
            els.suggestions.innerHTML = ''; // Force clear content
        }
    }

    // --- GAME LOGIC ---

    // Valid roles map (0=GK, 1=DEF, 2=DEF, 3=FWD, 4=FWD)
    const roleMap = { 0: 'GK', 1: 'DEF', 2: 'DEF', 3: 'FWD', 4: 'FWD' };

    // Slot interaction - REMOVED MANUAL SELECTION
    // els.slots.forEach(slot => { ... });

    function selectPlayer(player) {
        // Check if player is already in a slot
        if (slots.some(s => s && s.id == player.id)) {
            clearInput();
            return;
        }

        // Find player details in roster
        const rosterPlayer = validPlayers.find(vp => vp.id == player.id);
        if (!rosterPlayer) return;

        // Auto-determine slot based on Role
        let targetIndex = -1;
        const role = rosterPlayer.role; // GK, DEF, FWD

        if (role === 'GK') {
            if (!slots[0]) targetIndex = 0;
        } else if (role === 'DEF') {
            if (!slots[1]) targetIndex = 1;
            else if (!slots[2]) targetIndex = 2;
        } else if (role === 'FWD') {
            if (!slots[3]) targetIndex = 3;
            else if (!slots[4]) targetIndex = 4;
        }

        // If no empty slot for this role, do nothing (or show error)
        if (targetIndex === -1) {
            showToast(I18N.errorSlotTitle || "SIN HUECO", I18N.errorSlot || "No hay más huecos libres para esta posición.");
            clearInput();
            return;
        }

        const targetSlot = els.slots[targetIndex];

        // Update Slot
        fillSlot(targetIndex, rosterPlayer);

        // Store state
        saveCurrentState();

        // Clear input
        clearInput();

        if (!checkWin()) {
            // Fetch next team!
            loadingState(true);
            fetchRandomTeam();
        }
    }

    // Win Interaction
    // Win Interaction
    // (Legacy winModal code removed)


    function showWinPopup() {
        const overlay = document.createElement("div");
        overlay.className = "win-overlay";

        const card = document.createElement("div");
        card.className = "win-card";

        // Icon
        const icon = document.createElement("div");
        icon.className = "win-icon";
        icon.textContent = "🏆";

        const title = document.createElement("div");
        title.className = "win-title";
        title.textContent = I18N.winTitle || "¡Felicidades!";

        const msg = document.createElement("div");
        msg.className = "win-text";
        msg.textContent = I18N.winDesc || "Has completado la plantilla correctamente.";

        const btnRow = document.createElement("div");
        btnRow.className = "win-actions";

        const btnHome = document.createElement("button");
        btnHome.type = "button";
        btnHome.className = "win-btn secondary";
        btnHome.textContent = I18N.btnHome || "Inicio";
        btnHome.addEventListener("click", () => (window.location.href = CFG.baseUrl || '/'));

        const btnPlayAgain = document.createElement("button");
        btnPlayAgain.type = "button";
        btnPlayAgain.className = "win-btn primary";
        btnPlayAgain.textContent = I18N.btnPlayAgain || "Jugar otra vez";
        btnPlayAgain.addEventListener("click", () => {
            startNewGame(true); // Force new team
            overlay.remove();
        });

        btnRow.appendChild(btnHome);
        btnRow.appendChild(btnPlayAgain);

        card.appendChild(icon);
        card.appendChild(title);
        card.appendChild(msg);
        card.appendChild(btnRow);

        overlay.appendChild(card);
        document.body.appendChild(overlay);
    }

    // (Legacy winPlayAgain listener removed)


    function checkWin() {
        const filled = slots.filter(s => s !== null).length;
        if (filled === 5) {
            setTimeout(() => {
                showWinPopup();
                revealSolution(true); // Show bottom actions too
            }, 200);
            return true;
        }
        return false;
    }

    // Give Up Interaction
    const giveupModal = document.getElementById('giveupModal');
    const giveupYes = document.getElementById('giveupYes');
    const giveupNo = document.getElementById('giveupNo');
    const giveupClose = document.getElementById('giveupClose');

    function openModal() {
        if (giveupModal) giveupModal.classList.add('show');
    }

    function closeModal() {
        if (giveupModal) giveupModal.classList.remove('show');
    }

    if (els.giveup) {
        els.giveup.addEventListener('click', () => {
            openModal();
        });
    }

    if (giveupYes) {
        giveupYes.addEventListener('click', () => {
            closeModal();
            revealSolution();
        });
    }

    if (giveupNo) {
        giveupNo.addEventListener('click', closeModal);
    }

    if (giveupClose) {
        giveupClose.addEventListener('click', closeModal);
    }

    // Close on overlay click
    if (giveupModal) {
        giveupModal.addEventListener('click', (e) => {
            if (e.target === giveupModal) closeModal();
        });
    }

    // End Actions
    const endActions = document.getElementById('endActions');
    const playAgainBtn = document.getElementById('playAgainBtn');
    const openReportBtn = document.getElementById('openReportBtn');
    const bottomBar = document.querySelector('.bottom-bar');

    if (playAgainBtn) {
        playAgainBtn.addEventListener('click', () => {
            startNewGame(true); // Force new team
        });
    }

    // Report Modal Elements
    const reportModal = document.getElementById('reportModal');
    const reportMsg = document.getElementById('report_message');
    const reportClose = document.getElementById('reportClose');
    const reportSubmit = document.getElementById('reportSubmit');

    function toggleReportModal(show) {
        if (reportModal) {
            if (show) {
                reportModal.classList.add('show');
                if (reportMsg) reportMsg.value = '';
            } else {
                reportModal.classList.remove('show');
            }
        }
    }

    if (openReportBtn) {
        openReportBtn.addEventListener('click', () => {
            toggleReportModal(true);
        });
    }

    if (reportClose) {
        reportClose.addEventListener('click', () => toggleReportModal(false));
    }

    // Close on overlay click
    if (reportModal) {
        reportModal.addEventListener('click', (e) => {
            if (e.target === reportModal) toggleReportModal(false);
        });
    }

    // Submit handled by standard HTML form POST in index.php

    function revealSolution(isWin = false) {
        // 1. Get unassigned players
        const assignedIds = slots.filter(s => s).map(s => s.id);
        const unassigned = validPlayers.filter(p => !assignedIds.includes(p.id));

        // 2. Fill slots
        slots.forEach((s, idx) => {
            if (!s) {
                const role = roleMap[idx];
                const candidate = unassigned.find(p => p.role === role);

                if (candidate) {
                    const slotEl = els.slots[idx];
                    slotEl.innerHTML = `
                        <div class="slot-name">${candidate.name}</div>
                    `;
                    // Mark as revealed (Red Style)
                    slotEl.classList.add('filled', 'revealed');

                    const uIdx = unassigned.indexOf(candidate);
                    if (uIdx > -1) unassigned.splice(uIdx, 1);
                }
            }
        });

        const controlsArea = document.querySelector('.controls-area');
        const endActions = document.getElementById('endActions');

        // 3. UI State: Hide input controls, Show End Actions
        if (controlsArea) controlsArea.style.display = 'none';

        // Only show persistent end actions (Play Again / Report) if IT IS NOT A WIN.
        // If it is a win, the victory popup already has Play Again / Home buttons.
        if (endActions && !isWin) {
            endActions.classList.add('show');
            endActions.style.display = 'flex';
        }

        // Disable input just in case
        if (els.input) els.input.disabled = true;

        // Save as finished
        saveCurrentState(true);
    }

    // --- SEARCH & INPUT HANDLING ---
    let searchTimeout = null;
    if (els.input) {
        els.input.addEventListener('input', () => {
            const q = els.input.value.trim();
            clearTimeout(searchTimeout);

            if (q.length < 2) {
                els.suggestions.classList.remove('show');
                els.suggestions.innerHTML = ''; // Force clear
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`${apiBase}/clubs.php?action=search_players&q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(players => {
                        // Prevent showing results if user cleared input while fetching
                        if (els.input.value.trim().length < 2) return;
                        renderSuggestions(players);
                    })
                    .catch(err => console.error("Search error:", err));
            }, 300);
        });

        // Hide suggestions on click outside (matches HockeyGrid behavior)
        document.addEventListener('click', (e) => {
            const wrapper = els.input.parentElement; // Assuming input is inside .input-wrapper
            if (wrapper && !wrapper.contains(e.target)) {
                els.suggestions.classList.remove('show');
            }
        });

        // Keydown support
        els.input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                const active = els.suggestions.querySelector('.suggestion-item.active');
                if (active) {
                    e.preventDefault();
                    active.click();
                } else {
                    const first = els.suggestions.querySelector('.suggestion-item');
                    if (first) {
                        e.preventDefault();
                        first.click();
                    }
                }
            } else if (e.key === 'ArrowDown') {
                const items = els.suggestions.querySelectorAll('.suggestion-item');
                if (items.length > 0) {
                    e.preventDefault();
                    const active = els.suggestions.querySelector('.suggestion-item.active');
                    let next;
                    if (active) {
                        active.classList.remove('active');
                        next = active.nextElementSibling;
                        if (!next) next = items[0]; // Loop to start
                    } else {
                        next = items[0];
                    }
                    if (next) {
                        next.classList.add('active');
                        next.scrollIntoView({ block: 'nearest' });
                    }
                }
            } else if (e.key === 'ArrowUp') {
                const items = els.suggestions.querySelectorAll('.suggestion-item');
                if (items.length > 0) {
                    e.preventDefault();
                    const active = els.suggestions.querySelector('.suggestion-item.active');
                    let prev;
                    if (active) {
                        active.classList.remove('active');
                        prev = active.previousElementSibling;
                        if (!prev) prev = items[items.length - 1]; // Loop to end
                    } else {
                        prev = items[items.length - 1];
                    }
                    if (prev) {
                        prev.classList.add('active');
                        prev.scrollIntoView({ block: 'nearest' });
                    }
                }
            } else if (e.key === 'Escape') {
                clearInput();
            }
        });
    }

    function renderSuggestions(players) {
        // Filter out players already in slots
        // slots is array of player objects (or null)
        const placedIds = slots.filter(s => s).map(s => s.id);
        const filtered = players.filter(p => !placedIds.includes(p.id));

        if (!filtered || filtered.length === 0) {
            els.suggestions.classList.remove('show');
            return;
        }

        els.suggestions.innerHTML = '';
        filtered.forEach(p => {
            const div = document.createElement('div');
            div.className = 'suggestion-item';
            div.innerHTML = `
                <div class="s-name">${p.name}</div>
            `;
            div.addEventListener('click', (e) => {
                e.preventDefault();
                els.suggestions.classList.remove('show');
                handlePlayerSelection(p);
            });
            els.suggestions.appendChild(div);
        });
        els.suggestions.classList.add('show');
    }

    function handlePlayerSelection(player) {
        // 1. Is player already in a slot?
        if (slots.some(s => s && s.id == player.id)) {
            showToast(I18N.errorRepeatTitle || "JUGADOR REPETIDO", I18N.alreadySelected || "Este jugador ya está colocado");
            clearInput();
            return;
        }

        // 2. Is player in the current team?
        const rosterPlayer = validPlayers.find(vp => vp.id == player.id);
        if (!rosterPlayer) {
            showToast(I18N.errorSquadTitle || "NO ESTÁ EN PLANTILLA", I18N.notInSquad || "Este jugador no entra en esta plantilla");
            clearInput();
            return;
        }

        // 3. Valid -> Place player
        selectPlayer(player);
    }

    // Start immediately
    startNewGame();
});
