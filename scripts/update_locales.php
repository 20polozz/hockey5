<?php
$keysToAdd = [
    'menu.games' => [
        'es' => 'Juegos',
        'en' => 'Games',
        'ca' => 'Jocs',
        'fr' => 'Jeux',
        'pt' => 'Jogos'
    ],
    'menu.howto' => [
        'es' => 'Cómo Jugar',
        'en' => 'How to Play',
        'ca' => 'Com Jugar',
        'fr' => 'Comment Jouer',
        'pt' => 'Como Jogar'
    ],
    'blog.category.history' => [
        'es' => 'Historia',
        'en' => 'History',
        'ca' => 'Història',
        'fr' => 'Histoire',
        'pt' => 'História'
    ],
    'blog.category.players' => [
        'es' => 'Jugadores Famosos',
        'en' => 'Famous Players',
        'ca' => 'Jugadors Famosos',
        'fr' => 'Joueurs Célèbres',
        'pt' => 'Jogadores Famosos'
    ]
];

$localesDir = __DIR__ . '/../locales';
$files = glob($localesDir . '/*.php');

foreach ($files as $file) {
    $lang = basename($file, '.php');
    if (!isset($keysToAdd['menu.games'][$lang])) {
        // Fallback to English if lang not in list
        $lang = 'en'; 
    }

    $content = file_get_contents($file);
    
    // Simple way to inject the new keys right after we find menu.grid
    $insertPos = strpos($content, "'menu.grid'");
    if ($insertPos !== false) {
        $inject = "\n        'menu.games' => '" . addslashes($keysToAdd['menu.games'][$lang]) . "',\n" .
                  "        'menu.howto' => '" . addslashes($keysToAdd['menu.howto'][$lang]) . "',\n";
        
        // Before inserting, check if they already exist
        if (strpos($content, "'menu.games'") === false) {
             $content = substr_replace($content, $inject, $insertPos, 0);
        }
    }
    
    // Inject blog categories
    $insertPosBlog = strpos($content, "'blog.category.news'");
    if ($insertPosBlog !== false) {
        $injectBlog = "\n        'blog.category.history' => '" . addslashes($keysToAdd['blog.category.history'][$lang]) . "',\n" .
                      "        'blog.category.players' => '" . addslashes($keysToAdd['blog.category.players'][$lang]) . "',\n";
        
        if (strpos($content, "'blog.category.history'") === false) {
             $content = substr_replace($content, $injectBlog, $insertPosBlog, 0);
        }
    }
    
    file_put_contents($file, $content);
    echo "Updated $file\n";
}
