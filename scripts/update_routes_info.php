<?php
$localesDir = __DIR__ . '/../locales';
$files = glob($localesDir . '/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Insert new routes
    $routeInsert = "'glosario' => 'pages/glosario.php',\n        'faq' => 'pages/faq.php',\n";
    $routeReverseInsert = "'glossary' => 'glosario',\n        'faq' => 'faq',\n";
    
    // Find where to insert routes
    $insertPosRoutes = strpos($content, "'grid' => 'regex:grid',");
    if ($insertPosRoutes !== false && strpos($content, "'glosario' =>") === false) {
         $content = substr_replace($content, $routeInsert, $insertPosRoutes, 0);
    }
    
    // Find where to insert reverse routes
    $insertPosReverse = strpos($content, "'grid_howto' => 'como-jugar-grid',");
    if ($insertPosReverse !== false && strpos($content, "'glossary' => 'glosario'") === false) {
         $content = substr_replace($content, $routeReverseInsert, $insertPosReverse, 0);
    }
    
    // Also add to Footer links in translations! We'll just append them to the Legal list.
    $insertMenu = "\n        'menu.faq' => 'FAQ',\n        'menu.glossary' => 'Glosario',\n";
    if (strpos($content, "'menu.faq'") === false) {
        $insertPosMenu = strpos($content, "'menu.disclaimer'");
        if ($insertPosMenu !== false) {
            $content = substr_replace($content, $insertMenu, $insertPosMenu, 0);
        }
    }
    
    file_put_contents($file, $content);
    echo "Updated $file\n";
}
