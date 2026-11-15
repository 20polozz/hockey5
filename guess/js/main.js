// js/main.js
import { Data } from './data.js';
import { State } from './state.js';
import { UI } from './ui.js';
import { Autocomplete } from './autocomplete.js';
import { Compare } from './compare.js';

// Get Config from Window (injected by PHP)
const CONFIG = window.GUESS_CONFIG || { league: 'ok-liga' };

async function init() {
    // 1. Load Data
    const ok = await Data.load();
    if (!ok) {
        UI.showStatus(CONFIG.tr?.error_loading || "Error cargando base de datos.", 'error');
        return;
    }

    // 2. Init State
    State.init(CONFIG.league);

    // 3. Check if we need a new game
    const leaguePlayers = Data.getPlayersByLeague(CONFIG.league);

    if (!leaguePlayers.length) {
        if (CONFIG.league !== 'internacional') {
            UI.showStatus(CONFIG.tr?.no_players || "No hay jugadores en esta liga :(", 'error');
            // return; // Don't return, maybe we can select another league? But config is fixed.
        }
        // If international and empty, wait.. international implementation in Data.js returns allPlayers.
    }

    if (!State.targetPlayer) {
        if (!State.startNewGame(leaguePlayers)) {
            UI.showStatus(CONFIG.tr?.error_init || "Error iniciando partida...", 'error');
            return;
        }
    }

    // 4. Restore UI
    restoreGameUI();

    // 5. Setup Listeners
    setupInteractions(leaguePlayers);
}

function restoreGameUI() {
    UI.clearBoard();

    // Replay attempts
    State.attempts.forEach(p => {
        const comp = Compare.check(p, State.targetPlayer);
        UI.renderRow(p, comp);
    });

    if (State.isGameOver) {
        // If last attempt matches target, we won.
        // But giveUp adds target too. 
        // We can check if the user *actually* guessed it or if we forced it.
        // Actually State doesn't track "won" bool explicitly, just isGameOver.
        // We infer win if the user has manually guessed it?
        // Let's assume if game over and target is in attempts, we show end.
        // We need to distinguish win/lose message.
        // Simple logic: if the user clicked "Give Up", we showed modal.
        // If we restore, we just show "Game Over".
        const win = State.attempts.some(a => a.id === State.targetPlayer.id) && !userGaveUpMarker(); // Hard to track giveup persistency without flag.
        // For simplicity: If hidden flag "gaveUp" exists? 
        // Or just show "Game Over".
        UI.endGame(false, State.targetPlayer.name, handleRestart, handleHome); // Default message
        // Better: State should save "status": "won" | "lost" | "playing"
    }
}

// Temporary helper until I update State structure properly or just assume "Won" if won.
function userGaveUpMarker() {
    // If we want to persist "Given Up", we'd need a field in State.
    // purely visual for now.
    return false;
}

function setupInteractions(availablePlayers) {
    // Autocomplete
    Autocomplete.setup(UI.elements.input, (query) => {
        const q = query.toLowerCase();
        // Filter: name includes query
        return availablePlayers.filter(p =>
            p.name.toLowerCase().includes(q) ||
            (p.team && p.team.toLowerCase().includes(q))
        ).slice(0, 8);
    }, (matches, action) => {
        if (action === 'clear') {
            UI.hideSuggestions();
        } else if (action === 'render') {
            UI.renderSuggestionList(matches, (selectedPlayer) => {
                submitGuess(selectedPlayer);
                UI.hideSuggestions();
                UI.elements.input.value = '';
                UI.elements.input.focus();
            });
        }
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.answerbar')) UI.hideSuggestions();
    });

    // Restart
    UI.elements.btnRestart.addEventListener('click', () => {
        State.clear();
        State.startNewGame(availablePlayers);
        UI.clearBoard();
        UI.elements.input.focus();
    });

    // Report
    if (UI.elements.btnOpenReport) {
        UI.elements.btnOpenReport.addEventListener('click', () => {
            UI.showReportModal();
        });
    }

    // Give Up
    UI.elements.btnGiveUp.addEventListener('click', () => {
        if (State.isGameOver) return;

        UI.showConfirmModal(() => {
            State.giveUp();

            // Render the reveal
            const comp = Compare.check(State.targetPlayer, State.targetPlayer);
            UI.renderRow(State.targetPlayer, comp, 'giveup-reveal');

            UI.endGame(false, State.targetPlayer.name, handleRestart, handleHome);
        });
    });
}

function submitGuess(player) {
    if (State.isGameOver) return;

    if (State.attempts.some(a => a.id === player.id)) {
        UI.showToast(CONFIG.tr?.already_selected || "YA SELECCIONADO", CONFIG.tr?.already_tried || "¡Ya has intentado este jugador!");
        return;
    }

    State.addAttempt(player);
    const comp = Compare.check(player, State.targetPlayer);
    UI.renderRow(player, comp);

    if (State.isGameOver) {
        UI.endGame(true, State.targetPlayer.name, handleRestart, handleHome);
    }
}

function handleRestart() {
    State.clear();
    window.location.reload();
}

function handleHome() {
    State.clear();
    window.location.href = '/';
}

init();
