// js/state.js
export const State = {
    currentLeague: '',
    targetPlayer: null,
    attempts: [], // Array of player objects
    isGameOver: false,

    init(league) {
        this.currentLeague = league;
        this.load();
    },

    startNewGame(players) {
        if (!players || !players.length) return false;

        this.targetPlayer = players[Math.floor(Math.random() * players.length)];
        this.attempts = [];
        this.isGameOver = false;
        this.save();
        return true;
    },

    addAttempt(player) {
        if (this.isGameOver) return;
        this.attempts.push(player);

        if (player.id === this.targetPlayer.id) {
            this.isGameOver = true;
        }

        this.save();
    },

    giveUp() {
        this.isGameOver = true;
        // Optionally add target as the last attempt to "reveal" it in the grid
        // ensuring we don't duplicate if they just guessed it (though giveUp shouldn't be called then)
        const last = this.attempts[this.attempts.length - 1];
        if (!last || last.id !== this.targetPlayer.id) {
            this.attempts.push(this.targetPlayer);
        }
        this.save();
    },

    getKey() {
        return `guess_v2_${this.currentLeague}`;
    },

    save() {
        if (!this.targetPlayer) return;
        const data = {
            target: this.targetPlayer,
            attempts: this.attempts,
            isGameOver: this.isGameOver,
            ts: Date.now()
        };
        localStorage.setItem(this.getKey(), JSON.stringify(data));
    },

    load() {
        try {
            const raw = localStorage.getItem(this.getKey());
            if (!raw) return;
            const data = JSON.parse(raw);

            // Validity check
            if (data.target) {
                this.targetPlayer = data.target;
                this.attempts = data.attempts || [];
                this.isGameOver = data.isGameOver || false;
            }
        } catch (e) { console.error("Load error", e); }
    },

    clear() {
        localStorage.removeItem(this.getKey());
        this.targetPlayer = null;
        this.attempts = [];
        this.isGameOver = false;
    }
};
