<?php
declare(strict_types=1);

const DEFAULT_LANG = 'es';
const AVAILABLE_LANGS = ['es', 'ca', 'en', 'fr', 'pt'];

/*
 * Global translations storage.
 * Loaded by load_translations() or implicitly if needed.
 */
$GLOBALS['i18n_translations'] = [];
$GLOBALS['i18n_config'] = [];

/**
 * Load translations for a specific language.
 */
function load_translations(string $lang): void {
    if (!in_array($lang, AVAILABLE_LANGS)) {
        $lang = DEFAULT_LANG;
    }
    
    $file = __DIR__ . '/../locales/' . $lang . '.php';
    if (file_exists($file)) {
        $data = require $file;
        $GLOBALS['i18n_translations'] = $data['translations'] ?? [];
        $GLOBALS['i18n_config'] = $data;
    } else {
        // Fallback to default if file missing
        $file = __DIR__ . '/../locales/' . DEFAULT_LANG . '.php';
        if (file_exists($file)) {
            $data = require $file;
            $GLOBALS['i18n_translations'] = $data['translations'] ?? [];
            $GLOBALS['i18n_config'] = $data;
        }
    }
}

/**
 * Get translated string by key.
 */
function __(string $key): string {
    return $GLOBALS['i18n_translations'][$key] ?? $key;
}

/**
 * Get current language code.
 */
function current_lang(): string {
    return defined('CURRENT_LANG') ? CURRENT_LANG : DEFAULT_LANG;
}

/**
 * Generate a URL for the current language.
 * @param string $path The internal path or slug (e.g. 'contact'). 
 *                     If it matches a route ID, it will lookup the localized slug.
 *                     If not found, it keeps the path as-is.
 */
function url(string $path): string {
    $lang = current_lang();
    
    // Check if $path is a Route ID (e.g. 'contact')
    // We look it up in the current lang config
    $localizedSlug = get_localized_slug($path, $lang);
    
    // If it returns the same ID, it means either:
    // 1. No translation found (so we use ID as slug)
    // 2. The slug is identical to ID
    // In both cases, we just use it.
    
    // Special handling for home
    if ($path === 'home' || $localizedSlug === '') {
        return '/' . $lang;
    }

    return '/' . $lang . '/' . ltrim($localizedSlug, '/');
}

/**
 * Get the localized slug for a given route ID in a specific language.
 */
function get_localized_slug(string $routeId, string $targetLang): string {
    // We need to load the target lang config temporarily or cache it.
    // For simplicity, let's assume we can peek at the file or cache all.
    // Since we don't want to load all 4 files on every request, 
    // we might just load it if it's different from current.
    
    static $cached_configs = [];
    
    if (!isset($cached_configs[$targetLang])) {
        $file = __DIR__ . '/../locales/' . $targetLang . '.php';
        if (file_exists($file)) {
            $cached_configs[$targetLang] = require $file;
        } else {
            return $routeId;
        }
    }
    
    return $cached_configs[$targetLang]['routes_reverse'][$routeId] ?? $routeId;
}

/**
 * Helper to generate the URL for switching languages on the current page.
 * Requires the current route ID (e.g. 'contact', 'home') determined in index.php.
 */
function get_url_for_lang(string $targetLang): string {
    // We need the current abstract route ID.
    // This should be mapped in index.php or available globally.
    global $CURRENT_ROUTE_ID; // We will set this in index.php
    
    // Special handling for dynamic parameters (e.g. grid/ok-liga)
    global $CURRENT_ROUTE_PARAMS; // Array of params captured
    
    $slug = get_localized_slug($CURRENT_ROUTE_ID ?? 'home', $targetLang);
    
    if ($CURRENT_ROUTE_ID === 'hockeygrid_league') {
        // The slug might be 'grid' (ES) or 'xarxa' (CA).
        // Params[0] is the league. We assume league names are not translated for now 
        // OR we map them too (ok-liga is likely same).
        $league = $CURRENT_ROUTE_PARAMS[0] ?? '';
        return '/' . $targetLang . '/' . $slug . '/' . $league;
    }
    
    if ($CURRENT_ROUTE_ID === 'wordle_league') {
         $league = $CURRENT_ROUTE_PARAMS[0] ?? '';
         return '/' . $targetLang . '/' . $slug . '/' . $league;
    }

    if ($CURRENT_ROUTE_ID === 'guess_league') {
         $league = $CURRENT_ROUTE_PARAMS[0] ?? '';
         return '/' . $targetLang . '/' . $slug . '/' . $league;
    }

    if ($CURRENT_ROUTE_ID === 'clubs_league') {
         $league = $CURRENT_ROUTE_PARAMS[0] ?? '';
         return '/' . $targetLang . '/' . $slug . '/' . $league;
    }

    
    if ($slug === '') {
        return '/' . $targetLang; // Homepage
    }
    
    return '/' . $targetLang . '/' . $slug;
}

/**
 * Get the base path of the application (e.g. '' or '/subfolder').
 */
function base_path(): string {
    static $base;
    if ($base === null) {
        $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $base = rtrim($base, '/');
        if ($base === '.' || $base === '/') {
            $base = '';
        }
    }
    return $base;
}

/**
 * Generate a URL for an asset, respecting the base path.
 */
function asset_url(string $path): string {
    return base_path() . '/' . ltrim($path, '/');
}
