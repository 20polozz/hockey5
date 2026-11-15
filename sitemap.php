<?php
require_once __DIR__ . '/api/db.php';
require_once __DIR__ . '/includes/i18n.php'; // get AVAILABLE_LANGS

$domain = 'https://www.hockey5.pro';
$locales = ['es', 'en', 'ca', 'fr', 'pt']; // Fallback in case AVAILABLE_LANGS isn't simple to use standalone

// Core static pages
$pages = [
    '',
    '/contacto',
    '/politica-privacidad',
    '/politica-cookies',
    '/terminos-condiciones',
    '/politica-publicidad',
    '/aviso-legal',
    '/sobre-nosotros',
    '/hoqueigrid',
    '/hockey5-wordle',
    '/guess-menu',
    '/clubs-menu',
    '/como-jugar-grid',
    '/como-jugar-wordle',
    '/como-jugar-guess',
];

header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

$date = date('Y-m-d');

// 1. Output static pages
foreach ($locales as $lang) {
    foreach ($pages as $page) {
        $url = rtrim($domain . '/' . $lang . $page, '/');
        
        echo "  <url>\n";
        echo "    <loc>{$url}</loc>\n";
        echo "    <lastmod>{$date}</lastmod>\n";
        echo "    <changefreq>weekly</changefreq>\n";
        echo "    <priority>" . ($page === '' ? '1.0' : '0.8') . "</priority>\n";
        
        // Hreflang setup could go here, but omitted for simplicity
        
        echo "  </url>\n";
    }
}

echo '</urlset>';
