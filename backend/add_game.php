<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Accès refusé.');
}

require_once 'db_connect.php';

$title = $_POST['title'] ?? '';
$console = $_POST['console'] ?? '';
$file_path = $_POST['file_path'] ?? '';
$cover_path = $_POST['cover_path'] ?? '';

if ($title && $console && $file_path && $cover_path) {
    $stmt = $bdd->prepare("INSERT INTO games (title, console, file_path, cover_path) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $console, $file_path, $cover_path]);
    header('Location: /Arcadia/index.php');
    exit;
} else {
    echo "⚠️ Données manquantes.";
}
?>

