// js/ui.js
const CONFIG = window.GUESS_CONFIG || {};
export const UI = {
    elements: {
        container: document.getElementById('guesses-container'),
        status: document.getElementById('game-status'),
        input: document.getElementById('player-input'),
        suggestions: document.getElementById('suggestions'),
        endActions: document.getElementById('end-actions'),
        btnGiveUp: document.getElementById('btn-giveup'),
        btnRestart: document.getElementById('btn-restart'),
        inputRow: document.querySelector('.input-row'),
        toast: document.getElementById('toast'),
        toastTitle: document.querySelector('#toast .t-title'),
        toastMsg: document.querySelector('#toast .t-msg'),
        // Modal elements
        modalOverlay: document.getElementById('giveup-modal'),
        modalClose: document.getElementById('modal-close'),
        modalYes: document.getElementById('modal-yes'),
        modalNo: document.getElementById('modal-no'),

        // Report elements
        reportOverlay: document.getElementById('reportModal'),
        reportClose: document.getElementById('reportClose'),
        reportSend: document.getElementById('reportSubmit'),
        reportText: document.getElementById('report_message'),
        btnOpenReport: document.getElementById('btn-open-report')
    },

    showConfirmModal(onConfirm) {
        const el = this.elements;
        if (!el.modalOverlay) return;

        el.modalOverlay.classList.add('show');

        // Handlers (oneshot to avoid stacked listeners, or better: separate cleanup)
        // Simple approach: clone nodes to clear listeners or use named handlers. 
        // For simplicity in this small app: one-off onclicks.

        const close = () => {
            el.modalOverlay.classList.remove('show');
            clearListeners();
        };

        const confirm = () => {
            close();
            onConfirm();
        };

        const clearListeners = () => {
            el.modalYes.onclick = null;
            el.modalNo.onclick = null;
            el.modalClose.onclick = null;
        };

        el.modalYes.onclick = confirm;
        el.modalNo.onclick = close;
        el.modalClose.onclick = close;

        // Also close on background click
        el.modalOverlay.onclick = (e) => {
            if (e.target === el.modalOverlay) close();
        };
    },

    showReportModal() {
        const el = this.elements;
        if (!el.reportOverlay) return;

        el.reportText.value = ''; // Reset
        el.reportOverlay.classList.add('show');

        const close = () => el.reportOverlay.classList.remove('show');

        el.reportClose.onclick = close;
        el.reportOverlay.onclick = (e) => { if (e.target === el.reportOverlay) close(); };

        // Submit handled by standard HTML form POST in index.php
        el.reportSend.onclick = null;
    },

    showToast(title, msg, duration = 2000) {
        if (!this.elements.toast) return;

        this.elements.toastTitle.textContent = title || "";
        this.elements.toastMsg.textContent = msg || "";

        if (!title) this.elements.toastTitle.style.display = 'none';
        else this.elements.toastTitle.style.display = 'block';

        this.elements.toast.classList.add('show');

        if (this.toastTimeout) clearTimeout(this.toastTimeout);
        this.toastTimeout = setTimeout(() => {
            this.elements.toast.classList.remove('show');
        }, duration);
    },

    clearBoard() {
        this.elements.container.innerHTML = '';
        this.elements.status.textContent = '';
        this.elements.status.className = '';
        this.elements.input.value = '';
        this.elements.input.disabled = false;
        this.elements.endActions.classList.remove('show');
        this.elements.inputRow.style.display = 'flex';
        this.elements.suggestions.classList.remove('show');
    },

    showStatus(msg, type = 'normal') {
        this.elements.status.textContent = msg;
        this.elements.status.className = type === 'error' ? 'status-error' : '';
    },

    renderSuggestionList(matches, onSelect) {
        this.elements.suggestions.innerHTML = "";

        if (!matches.length) {
            const div = document.createElement('div');
            div.className = 'suggestion-item disabled';
            div.textContent = CONFIG.tr?.no_results || 'Sin resultados';
            this.elements.suggestions.appendChild(div);
            this.elements.suggestions.classList.add("show");
            return;
        }

        matches.forEach(p => {
            const div = document.createElement('div');
            div.className = 'suggestion-item';
            div.textContent = p.name;
            div.dataset.id = p.id;
            div.addEventListener('click', () => onSelect(p));
            this.elements.suggestions.appendChild(div);
        });
        this.elements.suggestions.classList.add("show");
    },

    hideSuggestions() {
        this.elements.suggestions.classList.remove("show");
    },

    renderRow(player, comparison, extraClass = '') {
        // comparison = { name: bool, country: bool, team: bool, number: bool, year: 'correct'|'close'|'wrong', yearDir: 'up'|'down'|null }
        const row = document.createElement('div');
        row.className = `guess-row slide-in ${extraClass}`;

        // Helper to get class
        // ... (rest is same, just need to ensure I don't break it)
        const getCls = (isCorrect) => isCorrect ? 'status-correct' : 'status-wrong';

        // 1. Name
        const nameCell = document.createElement('div');
        nameCell.className = `guess-cell cell-pill ${getCls(comparison.name)}`;
        nameCell.textContent = player.name;
        row.appendChild(nameCell);

        // 2. Country
        const countryCell = document.createElement('div');
        countryCell.className = `guess-cell ${getCls(comparison.country)}`;
        countryCell.innerHTML = `<span>${player.country}</span>`;
        row.appendChild(countryCell);

        // 3. Year
        const yearCell = document.createElement('div');
        let yClass = 'status-wrong';
        if (comparison.year === 'correct') yClass = 'status-correct';
        if (comparison.year === 'close') yClass = 'status-close';

        yearCell.className = `guess-cell ${yClass}`;
        yearCell.textContent = player.birthYear;

        if (comparison.yearDir) {
            const arrow = document.createElement('div');
            arrow.className = `indicator indicator-${comparison.yearDir}`; // css handles content
            yearCell.appendChild(arrow);
        }
        row.appendChild(yearCell);

        // 4. Team
        const teamCell = document.createElement('div');
        teamCell.className = `guess-cell cell-pill ${getCls(comparison.team)}`;
        teamCell.style.fontSize = '0.75em';
        teamCell.textContent = player.team;
        row.appendChild(teamCell);

        // 5. Number
        const numCell = document.createElement('div');
        let nClass = 'status-wrong';
        if (comparison.number === 'correct') nClass = 'status-correct';
        if (comparison.number === 'close') nClass = 'status-close';

        numCell.className = `guess-cell ${nClass}`;
        numCell.textContent = player.number || '-';

        if (comparison.numberDir) {
            const arrow = document.createElement('div');
            arrow.className = `indicator indicator-${comparison.numberDir}`;
            numCell.appendChild(arrow);
        }
        row.appendChild(numCell);

        this.elements.container.prepend(row);
    },

    endGame(win, targetName, onRestart, onHome) {
        this.elements.input.disabled = true;
        this.elements.inputRow.style.display = 'none';

        // Hide standard end actions if using win popup
        // this.elements.endActions.classList.add('show'); 

        if (win) {
            this.showWinPopup(onRestart, onHome);
        } else {
            // Give up handling (red styling was already applied by main.js)
            // Just show status or maybe a "Defeat" popup?
            // For now, keep existing status behavior or maybe leverage endActions for defeat
            this.elements.endActions.classList.add('show');
            this.showStatus("", '');
        }
    },

    showWinPopup(onRestart, onHome) {
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
        title.textContent = CONFIG.tr?.win_title || "¡Felicidades!";

        const msg = document.createElement("div");
        msg.className = "win-text";
        msg.textContent = CONFIG.tr?.win_desc || "Has encontrado al jugador";

        const btnRow = document.createElement("div");
        btnRow.className = "win-actions";

        const btnHome = document.createElement("button");
        btnHome.type = "button";
        btnHome.className = "win-btn secondary";
        btnHome.textContent = CONFIG.tr?.btn_home || "Inicio";
        btnHome.addEventListener("click", () => {
            if (onHome) onHome();
            else window.location.href = '/';
        });

        const btnPlayAgain = document.createElement("button");
        btnPlayAgain.type = "button";
        btnPlayAgain.className = "win-btn primary";
        btnPlayAgain.textContent = CONFIG.tr?.btn_play_again || "Jugar otra vez";
        btnPlayAgain.addEventListener("click", () => {
            // We must clear state before reloading to ensure a new game starts
            // Since State is imported in main.js but not here, we need access to it.
            // OR, simpler: just clear the localStorage key directly here if we know it.
            // But the key depends on league.
            // Better: Init restart via a method that main.js exposes or just rely on localStorage clearing if we can import State?
            // UI.js doesn't import State. It exports UI.
            // Let's check imports in ui.js. It imports CONFIG.

            // Issue: UI.js doesn't have access to State.
            // We should probably pass a callback to showWinPopup for "onRestart".
            // Refactoring showWinPopup to accept onRestart callback.
            if (onRestart) onRestart();
            else window.location.reload();
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
};
