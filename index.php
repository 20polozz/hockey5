<?php
declare(strict_types=1);

/**
 * Front controller / router with Internationalization (i18n).
 * - Serves URLs prefixed with language code (e.g. /es/, /en/)
 * - Redirects root to default language or cookie preference.
 */

require_once __DIR__ . '/includes/i18n.php';

// 0. Skip physical files (css, js, images, etc.)
// First, normalize the path
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$physicalPath = __DIR__ . $requestUri;

// If it's a file or directory that really exists, don't route it
if (file_exists($physicalPath) && !is_dir($physicalPath)) {
    return false; // Let the server handle it
}

$path = trim($requestUri, '/');
$segments = explode('/', $path);
$firstSegment = $segments[0] ?? '';

// Exclude admin and assets from language prefixing
if ($firstSegment === 'admin' || $firstSegment === 'assets' || $firstSegment === 'hockeygrid') {
    return false; // Exit front controller
}

if (in_array($firstSegment, AVAILABLE_LANGS)) {
    // URL has language prefix
    $lang = $firstSegment;
    define('CURRENT_LANG', $lang);

    // Update cookie if different
    if (!isset($_COOKIE['hockey5_lang']) || $_COOKIE['hockey5_lang'] !== $lang) {
        setcookie('hockey5_lang', $lang, time() + 86400 * 30, '/');
    }

    // Strip language prefix to get internal path
    array_shift($segments);
    $internalPath = implode('/', $segments);
} else {
    // No language prefix
    // Check cookie or default
    $lang = $_COOKIE['hockey5_lang'] ?? DEFAULT_LANG;
    if (!in_array($lang, AVAILABLE_LANGS)) {
        $lang = DEFAULT_LANG;
    }

    // Redirect to language-prefixed URL
    // We preserve the current query string if any
    $queryString = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    $redirectPath = $path === '' ? '' : '/' . $path;
    
    header("Location: /" . $lang . $redirectPath . $queryString, true, 302);
    exit;
}

// Load translations for the detected language
load_translations(CURRENT_LANG);
$routes = $GLOBALS['i18n_config']['routes'] ?? [];

// 2. Routing Logic
// $internalPath is now the localized slug (e.g. 'contacto' or 'contact')

// Check for exact matches first
if (isset($routes[$internalPath]) && strpos($routes[$internalPath], 'regex:') === false) {
    // Identify Route ID for language switcher
    // We reverse lookup the slug in routes_reverse
    $reversed = $GLOBALS['i18n_config']['routes_reverse'] ?? [];
    $routeId = array_search($internalPath, $reversed);
    $GLOBALS['CURRENT_ROUTE_ID'] = $routeId ?: 'unknown';

    include __DIR__ . '/' . $routes[$internalPath];
    exit;
}

// 3. Regex / Pattern Matching for Games
foreach ($routes as $slug => $target) {
    if (strpos($target, 'regex:') === 0) {
        $feature = substr($target, 6); // 'grid', 'wordle'

        // Check if internalPath starts with this slug
        // Pattern: ^<slug>/(ok-liga|liga-placard|internacional)$
        // We use the slug from the config (e.g. 'grid' or 'xarxa')
        if ($slug && preg_match("~^" . preg_quote($slug, '~') . "/(ok-liga|liga-placard|internacional)$~", $internalPath, $m)) {
            
            if ($feature === 'grid') {
                $GLOBALS['CURRENT_ROUTE_ID'] = 'hockeygrid_league';
                $GLOBALS['CURRENT_ROUTE_PARAMS'] = [$m[1]];
                include __DIR__ . "/hockeygrid/{$m[1]}/index.php";
                exit;
            }
            
            if ($feature === 'wordle') {
                $GLOBALS['CURRENT_ROUTE_ID'] = 'wordle_league';
                $GLOBALS['CURRENT_ROUTE_PARAMS'] = [$m[1]];
                include __DIR__ . "/wordle/{$m[1]}/index.php";
                exit;
            }
        }
        
        // Handle sub-pages defined in original index.php?
        // 'hockey5-grid/como-jugar' -> now '/es/grid/como-jugar'?
        // The user didn't specify sub-page translation for 'como-jugar'.
        // But we should probably support it if we want full coverage.
        // Let's check for standard sub-route pattern like "$slug/como-jugar"
        // For now, I will add specific routes for 'como-jugar' in the routes config if needed,
        // OR generic handling. 
        // Original: 'hockey5-grid/como-jugar' => __DIR__ . '/hockeygrid/como-jugar.php',
        // We can add this to the routes array in locales.
    }
    
    // Guess Player Routing
    if (strpos($target, 'regex:') === 0 && substr($target, 6) === 'guess') {
         if (preg_match("~^guess/(ok-liga|placard|internacional)$~", $internalPath, $m)) {
            $GLOBALS['CURRENT_ROUTE_ID'] = 'guess_league';
            $GLOBALS['CURRENT_ROUTE_PARAMS'] = [$m[1]];
            include __DIR__ . "/guess/index.php";
            exit;
         }
    }

    // Clubs Routing
    if (strpos($target, 'regex:') === 0 && substr($target, 6) === 'clubs') {
        if (preg_match("~^clubs/(ok-liga|liga-placard|internacional)$~", $internalPath, $m)) {
           $GLOBALS['CURRENT_ROUTE_ID'] = 'clubs_league';
           $GLOBALS['CURRENT_ROUTE_PARAMS'] = [$m[1]];
           $_GET['league'] = $m[1];
           include __DIR__ . "/clubs/index.php";
           exit;
        }
   }
}

// 4. Back-compat / Fallbacks
// If we are here, no route matched.

// Old compat: /jugar/hockey5-grid
if ($internalPath === 'jugar/hockey5-grid') {
    include __DIR__ . '/hockeygrid/index.php';
    exit;
}

// 404
http_response_code(404);
$GLOBALS['CURRENT_ROUTE_ID'] = '404';
include __DIR__ . '/paginas_errores/404.php';
