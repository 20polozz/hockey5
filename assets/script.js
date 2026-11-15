
/**
 * Mobile Dropdowns Fix
 * On touch devices, the first tap on a menu with a dropdown 
 * should open the sub-menu. The second tap should follow the link.
 */
const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
dropdownToggles.forEach(btn => {
  btn.addEventListener('click', function(e) {
    // Only apply logic on small screens or touch-based interaction
    if (window.innerWidth <= 850 || ('ontouchstart' in window)) {
      const parent = this.closest('.lang-dropdown');
      const isAlreadyOpen = parent.classList.contains('menu-open');

      // Close other open menus
      document.querySelectorAll('.lang-dropdown.menu-open').forEach(openMenu => {
        if (openMenu !== parent) openMenu.classList.remove('menu-open');
      });

      if (!isAlreadyOpen) {
        // First click: open menu, prevent navigation
        e.preventDefault();
        parent.classList.add('menu-open');
      } else {
        // Second click: let it navigate if it's an <a>
        // (no preventDefault here)
      }
    }
  });
});

// Close menus when clicking outside
document.addEventListener('click', function(e) {
  if (!e.target.closest('.lang-dropdown')) {
    document.querySelectorAll('.lang-dropdown.menu-open').forEach(openMenu => {
      openMenu.classList.remove('menu-open');
    });
  }
});

// Teams strip (OK Liga)
const strip = document.getElementById("teamStrip");
if (strip) {
  // Data now comes from MySQL via PHP API
  fetch("./api/okliga_teams.php", { cache: "no-store" })
    .then(r => r.json())
    .then(teams => {
      strip.innerHTML = "";
      teams.forEach(t => {
        const el = document.createElement("div");
        el.className = "team-pill";
        el.textContent = t.name;
        const span = document.createElement("span");
        span.textContent = t.city ? `· ${t.city}` : "";
        el.appendChild(span);
        strip.appendChild(el);
      });
    })
    .catch(() => {
      strip.innerHTML = "<div class='team-pill'>No s'han pogut carregar els equips</div>";
    });
}
