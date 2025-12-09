<?php
// backend/serve_rom.php

$baseDir = __DIR__ . '/../'; // Remonte d'un cran pour être à la racine
$romPath = isset($_GET['path']) ? $_GET['path'] : '';

// Sécurité : empêche de remonter dans l'arborescence
if (strpos($romPath, '..') !== false) {
    die("Accès interdit.");
}

$fullPath = realpath($baseDir . $romPath);

// Vérifie que le fichier est bien dans le dossier roms
if (!$fullPath || strpos($fullPath, realpath($baseDir . 'roms')) !== 0) {
    // Si le fichier direct n'existe pas, on regarde si c'est un fichier splitté
    // On reconstruit le chemin théorique
    $theoreticalPath = $baseDir . $romPath;

    // On cherche les parties .001, .002, etc.
    $part1 = $theoreticalPath . '.001';

    if (file_exists($part1)) {
        // C'est un fichier splitté !

        // Headers pour le téléchargement/stream
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($theoreticalPath) . '"');

        // On boucle sur les parties
        $i = 1;
        while (true) {
            $partFile = $theoreticalPath . '.' . sprintf('%03d', $i);
            if (!file_exists($partFile)) {
                break;
            }
            readfile($partFile);
            $i++;
        }
        exit;
    }

    http_response_code(404);
    die("Fichier introuvable.");
}

// Si le fichier existe tel quel (pas splitté)
if (file_exists($fullPath)) {
    header('Content-Type: application/octet-stream');
    header('Content-Length: ' . filesize($fullPath));
    header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
    readfile($fullPath);
    exit;
}
?>