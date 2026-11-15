// js/data.js
export const Data = {
    allPlayers: [],

    async load() {
        try {
            const url = (window.GUESS_CONFIG && window.GUESS_CONFIG.jsonUrl) ? window.GUESS_CONFIG.jsonUrl : './data/players.json';
            const res = await fetch(url);
            if (!res.ok) throw new Error("Error loading players from " + url);
            this.allPlayers = await res.json();
            return true;
        } catch (e) {
            console.error(e);
            return false;
        }
    },

    /**
     * Filters players based on the league code.
     * Logic:
     * - 'ok-liga', 'oklliga' -> league_id: 1
     * - 'placard', 'liga-placard', 'portugal' -> league_id: 2
     * - 'internacional' -> ALL players
     * - others (nhl, etc) -> fallback to league string matching if id not found, or empty.
     */
    getPlayersByLeague(leagueCode) {
        if (!leagueCode) return [];

        const code = leagueCode.toLowerCase();

        // 1. Internacional = ALL
        if (code === 'internacional') {
            return this.allPlayers;
        }

        // 2. Map codes to IDs
        let targetId = null;
        if (['ok-liga', 'oklliga'].includes(code)) targetId = 1;
        else if (['placard', 'liga-placard', 'portugal'].includes(code)) targetId = 2;

        if (targetId !== null) {
            return this.allPlayers.filter(p => p.league_id === targetId);
        }

        // 3. Fallback for other leagues (NHL, etc) based on string
        // The previous map logic is still useful for these if needed, 
        // but user only specified requirements for OK Liga, Portugal, Internacional.
        // Let's keep a simple map for others just in case.
        const map = {
            'nhl': 'NHL',
            'ahl': 'AHL',
            'shl': 'SHL',
            'liiga': 'Liiga'
        };
        const targetName = map[code];
        if (targetName) {
            return this.allPlayers.filter(p => p.league === targetName);
        }

        return [];
    },

    /**
     * Returns a SINGLE random player based on the league version.
     */
    getRandomPlayer(leagueCode) {
        const players = this.getPlayersByLeague(leagueCode);
        if (!players || players.length === 0) return null;
        return players[Math.floor(Math.random() * players.length)];
    }
};
