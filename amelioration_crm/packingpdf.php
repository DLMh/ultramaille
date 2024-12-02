<?php 
if (isset($_GET['file'])) {
    $file = $_GET['file']; // Nettoyer l'entrée utilisateur
  
    // Chemin sécurisé
    $baseDir = rtrim(realpath(__DIR__ . '/pdfs'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    $filePath = realpath($baseDir . basename($file));
    // Normaliser les séparateurs pour éviter les problèmes sous Windows
    $baseDir = str_replace('\\', '/', $baseDir);
    $filePath = str_replace('\\', '/', $filePath);

    if ($filePath && strpos($filePath, $baseDir) === 0 && file_exists($filePath) && is_readable($filePath)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
        readfile($filePath);
        exit;
    } else {
        echo "Fichier introuvable ou accès refusé.";
    }
} else {
    echo "Paramètre 'file' manquant.";
}

?>