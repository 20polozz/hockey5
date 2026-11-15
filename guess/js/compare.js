// js/compare.js
export const Compare = {
    check(guess, target) {
        const res = {
            name: this.isMatch(guess.name, target.name),
            country: this.isMatch(guess.country, target.country),
            team: this.isMatch(guess.team, target.team),

            // Year
            year: 'wrong', // correct, close, wrong
            yearDir: null, // up, down (what user needs to do)

            // Number
            number: 'wrong',
            numberDir: null
        };

        // Year Logic
        const gY = parseInt(guess.birthYear);
        const tY = parseInt(target.birthYear);
        const diffY = gY - tY; // 1990 - 2000 = -10 (Guessed too low, need UP)

        if (gY === tY) res.year = 'correct';
        else if (Math.abs(diffY) <= 1) res.year = 'close'; // Config requested <=1 for orange? User: "naranja si diferencia <= 1"

        if (diffY !== 0) {
            // If guesses 1990, target 2000. Diff -10. User needs to go UP. Arrow matches direction to target.
            // If User said: "si el año introducido > año real -> 'DOWN'".
            // Input 2005 > Real 2000. Arrow DOWN (reduce val). Correct.
            res.yearDir = diffY > 0 ? 'down' : 'up';
        }

        // Number Logic
        const gN = parseInt(guess.number || 0);
        const tN = parseInt(target.number || 0);
        const diffN = gN - tN;

        if (gN === tN) res.number = 'correct';
        else if (Math.abs(diffN) <= 2) res.number = 'close'; // Keep 2 for number?

        if (diffN !== 0 && gN !== 0 && tN !== 0) {
            res.numberDir = diffN > 0 ? 'down' : 'up';
        }

        return res;
    },

    isMatch(a, b) {
        return (a || '').toString().toLowerCase().trim() === (b || '').toString().toLowerCase().trim();
    }
};
