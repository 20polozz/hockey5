<?php
$keysToAdd = [
    'blog.category.basics' => [
        'es' => 'Conceptos Básicos',
        'en' => 'Hockey Basics',
        'ca' => 'Conceptes Bàsics',
        'fr' => 'Bases du Hockey',
        'pt' => 'Conceitos Básicos'
    ],
    'blog.category.strategy' => [
        'es' => 'Estrategia de Hockey',
        'en' => 'Hockey Strategy',
        'ca' => 'Estratègia d\'Hoquei',
        'fr' => 'Stratégie de Hockey',
        'pt' => 'Estratégia de Hóquei'
    ],
    'blog.category.training' => [
        'es' => 'Entrenamiento',
        'en' => 'Hockey Training',
        'ca' => 'Entrenament',
        'fr' => 'Entraînement',
        'pt' => 'Treino'
    ],
    'blog.category.equipment' => [
        'es' => 'Equipamiento',
        'en' => 'Hockey Equipment',
        'ca' => 'Equipament',
        'fr' => 'Équipement',
        'pt' => 'Equipamento'
    ]
];

$localesDir = __DIR__ . '/../locales';
$files = glob($localesDir . '/*.php');

foreach ($files as $file) {
    if (basename($file) === 'index.php') continue;
    $lang = basename($file, '.php');
    if (!isset($keysToAdd['blog.category.basics'][$lang])) {
        $lang = 'en'; 
    }

    $content = file_get_contents($file);
    
    // Simple way to inject the new keys right before 'blog.category.history'
    $insertPosBlog = strpos($content, "'blog.category.history'");
    if ($insertPosBlog !== false) {
        $injectBlog = "\n        'blog.category.basics' => '" . addslashes($keysToAdd['blog.category.basics'][$lang]) . "',\n" .
                      "        'blog.category.strategy' => '" . addslashes($keysToAdd['blog.category.strategy'][$lang]) . "',\n" .
                      "        'blog.category.training' => '" . addslashes($keysToAdd['blog.category.training'][$lang]) . "',\n" .
                      "        'blog.category.equipment' => '" . addslashes($keysToAdd['blog.category.equipment'][$lang]) . "',\n";
        
        if (strpos($content, "'blog.category.basics'") === false) {
             $content = substr_replace($content, $injectBlog, $insertPosBlog, 0);
        }
    }
    
    file_put_contents($file, $content);
    echo "Updated $file\n";
}
