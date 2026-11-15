<?php
$keysToAdd = [
    'menu.disclaimer' => [
        'es' => 'Aviso Legal',
        'en' => 'Disclaimer',
        'ca' => 'Avís Legal',
        'fr' => 'Avis Légal',
        'pt' => 'Aviso Legal'
    ]
];

$localesDir = __DIR__ . '/../locales';
$files = glob($localesDir . '/*.php');

foreach ($files as $file) {
    $lang = basename($file, '.php');
    if (!isset($keysToAdd['menu.disclaimer'][$lang])) {
        $lang = 'en'; 
    }

    $content = file_get_contents($file);
    
    $insertPos = strpos($content, "'menu.terms'");
    if ($insertPos !== false) {
        $inject = "\n        'menu.disclaimer' => '" . addslashes($keysToAdd['menu.disclaimer'][$lang]) . "',\n";
        
        if (strpos($content, "'menu.disclaimer'") === false) {
             $content = substr_replace($content, $inject, $insertPos, 0);
        }
    }
    
    file_put_contents($file, $content);
    echo "Updated $file\n";
}
