<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée.']);
    exit;
}

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentification requise.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$gameId = isset($_POST['game_id']) ? (int) $_POST['game_id'] : 0;
$elapsedSeconds = isset($_POST['elapsed_seconds']) ? (int) $_POST['elapsed_seconds'] : 0;

if ($gameId <= 0 || $elapsedSeconds <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètres invalides.']);
    exit;
}

require_once __DIR__ . '/db_connect.php';

try {


    $stmt = $bdd->prepare("
        INSERT INTO user_game_times (user_id, game_id, total_seconds, last_played_at)
        VALUES (:user_id, :game_id, :elapsed, NOW())
        ON DUPLICATE KEY UPDATE
            total_seconds = total_seconds + VALUES(total_seconds),
            last_played_at = NOW()
    ");

    $stmt->execute([
        ':user_id' => $userId,
        ':game_id' => $gameId,
        ':elapsed' => $elapsedSeconds,
    ]);

    echo json_encode(['status' => 'ok']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur.']);
}

