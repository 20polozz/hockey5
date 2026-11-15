// js/autocomplete.js
export const Autocomplete = {
    setup(input, getCandidates, onSelect) {
        let debounceTimer;

        input.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            const val = e.target.value.trim();

            if (val.length < 1) {
                onSelect(null, 'clear'); // Close suggestions
                return;
            }

            debounceTimer = setTimeout(() => {
                const matches = getCandidates(val);
                onSelect(matches, 'render');
            }, 100);
        });

        // Keyboard Nav
        input.addEventListener('keydown', (e) => {
            const suggestions = document.querySelectorAll('.suggestion-item');
            const active = document.querySelector('.suggestion-item.active');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!suggestions.length) return;
                if (!active) {
                    suggestions[0].classList.add('active');
                } else {
                    active.classList.remove('active');
                    const next = active.nextElementSibling;
                    if (next) {
                        next.classList.add('active');
                        next.scrollIntoView({ block: 'nearest' });
                    }
                    else {
                        suggestions[0].classList.add('active'); // loop
                        suggestions[0].scrollIntoView({ block: 'nearest' });
                    }
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (!suggestions.length) return;
                if (!active) {
                    suggestions[suggestions.length - 1].classList.add('active');
                    suggestions[suggestions.length - 1].scrollIntoView({ block: 'nearest' });
                } else {
                    active.classList.remove('active');
                    const prev = active.previousElementSibling;
                    if (prev) {
                        prev.classList.add('active');
                        prev.scrollIntoView({ block: 'nearest' });
                    }
                    else {
                        suggestions[suggestions.length - 1].classList.add('active');
                        suggestions[suggestions.length - 1].scrollIntoView({ block: 'nearest' });
                    }
                }
            } else if (e.key === 'Enter') {
                if (active) {
                    e.preventDefault();
                    active.click();
                } else if (suggestions.length > 0) {
                    // Auto-select first suggestion if none active
                    e.preventDefault();
                    suggestions[0].click();
                }
            } else if (e.key === 'Escape') {
                onSelect(null, 'clear');
            }
        });

        // Blur handling - tricky because click fires after blur.
        // Usually handled by a document click listener closing if outside.
    }
};
