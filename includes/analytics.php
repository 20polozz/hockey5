<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-48PXE00GP6"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-48PXE00GP6');

  // Event Tracking Script
  document.addEventListener('click', function(e) {
    // Find closest element with 'ga-track' class
    var target = e.target.closest('.ga-track');
    
    if (target) {
      // Get Language (CS, ES, EN, etc.)
      var lang = (document.documentElement.lang || 'es').toUpperCase();
      
      // Get Action and format it (e.g. "select_game" -> "Select Game")
      var rawAction = target.getAttribute('data-ga-action') || 'click';
      var formattedAction = rawAction.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
      
      // Construct Event Name: "ES - Select Game"
      var eventName = lang + ' - ' + formattedAction;

      // Optional: Get extra details
      var label = target.getAttribute('data-ga-label') || '';
      var league = target.getAttribute('data-ga-league') || '';

      // Send event
      // We use the constructed name as the Event Name
      gtag('event', eventName, {
        'event_category': 'User Interaction',
        'event_label': label,
        'league': league
      });
    }
  });
</script>
