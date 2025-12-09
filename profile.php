<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'backend/db_connect.php';

$user_id = $_SESSION['user_id'];

// Gestion des actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_friend') {
        $friend_username = trim($_POST['friend_username'] ?? '');
        if ($friend_username) {
            $stmt = $bdd->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$friend_username]);
            $friend = $stmt->fetch();
            
            if ($friend && $friend['id'] != $user_id) {
                $friend_id = $friend['id'];
                // Vérifier si déjà ami
                $check = $bdd->prepare("SELECT * FROM friendships WHERE (requester_id = ? AND requested_id = ?) OR (requester_id = ? AND requested_id = ?)");
                $check->execute([$user_id, $friend_id, $friend_id, $user_id]);
                if (!$check->fetch()) {
                    $insert = $bdd->prepare("INSERT INTO friendships (requester_id, requested_id, status) VALUES (?, ?, 'pending')");
                    $insert->execute([$user_id, $friend_id]);
                    $message = "Demande envoyée à $friend_username";
                } else {
                    $message = "Déjà ami ou demande en cours";
                }
            } else {
                $message = "Utilisateur introuvable";
            }
        }
    }
    
    if ($action === 'accept_friend') {
        $request_id = $_POST['request_id'] ?? 0;
        $update = $bdd->prepare("UPDATE friendships SET status = 'accepted' WHERE id = ?");
        $update->execute([$request_id]);
        $message = "Demande acceptée";
    }
    
    if ($action === 'decline_friend') {
        $request_id = $_POST['request_id'] ?? 0;
        $delete = $bdd->prepare("DELETE FROM friendships WHERE id = ?");
        $delete->execute([$request_id]);
        $message = "Demande refusée";
    }
}

// Récupérer les infos utilisateur (soi-même ou un autre)
$view_user_id = $user_id;
if (isset($_GET['user'])) {
    $stmt = $bdd->prepare("SELECT id, username, email, is_admin, created_at FROM users WHERE username = ?");
    $stmt->execute([$_GET['user']]);
    $view_user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($view_user) {
        $view_user_id = $view_user['id'];
        $user = $view_user;
    } else {
        $user = null;
    }
} else {
    $stmt = $bdd->prepare("SELECT username, email, is_admin, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$user) {
    header('Location: profile.php');
    exit;
}

$is_own_profile = ($view_user_id == $user_id);

// Vérifier si on peut voir les temps de jeu (soi-même ou ami)
$can_see_times = $is_own_profile;
if (!$can_see_times) {
    $check_friend = $bdd->prepare("SELECT * FROM friendships WHERE ((requester_id = ? AND requested_id = ?) OR (requester_id = ? AND requested_id = ?)) AND status = 'accepted'");
    $check_friend->execute([$user_id, $view_user_id, $view_user_id, $user_id]);
    $can_see_times = $check_friend->fetch() !== false;
}

// Récupérer les temps de jeu et favoris
$game_times = [];
$favorites = [];

if ($can_see_times) {
    $stmt = $bdd->prepare("
        SELECT g.title, g.console, ugt.total_seconds, ugt.last_played_at 
        FROM user_game_times ugt 
        JOIN games g ON g.id = ugt.game_id 
        WHERE ugt.user_id = ? 
        ORDER BY ugt.total_seconds DESC
    ");
    $stmt->execute([$view_user_id]);
    $game_times = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //recupere les favoris
    $fav_stmt = $bdd->prepare("
        SELECT g.title, g.console, g.id
        FROM favorites f
        JOIN games g ON g.id = f.game_id
        WHERE f.user_id = ?
    ");
    $fav_stmt->execute([$view_user_id]);
    $favorites = $fav_stmt->fetchAll(PDO::FETCH_ASSOC);
}

$total_seconds = 0;
foreach ($game_times as $gt) {
    $total_seconds += $gt['total_seconds'];
}

// Récupérer les amis (seulement pour son propre profil)
$friends = [];
$requests = [];
if ($is_own_profile) {
    $stmt = $bdd->prepare("
        SELECT f.id, f.status, u.username, u.id as friend_id
        FROM friendships f
        JOIN users u ON (u.id = f.requested_id AND f.requester_id = ?) OR (u.id = f.requester_id AND f.requested_id = ?)
        WHERE (f.requester_id = ? OR f.requested_id = ?) AND f.status = 'accepted'
    ");
    $stmt->execute([$user_id, $user_id, $user_id, $user_id]);
    $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les demandes reçues
    $stmt = $bdd->prepare("
        SELECT f.id, u.username 
        FROM friendships f
        JOIN users u ON u.id = f.requester_id
        WHERE f.requested_id = ? AND f.status = 'pending'
    ");
    $stmt->execute([$user_id]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction simple pour formater le temps
function formatTime($seconds) {
    if ($seconds < 60) return $seconds . ' sec';
    $minutes = floor($seconds / 60);
    if ($minutes < 60) return $minutes . ' min';
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    return $hours . 'h ' . $mins . 'min';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Profil – <?= htmlspecialchars($user['username']) ?></title>
  <link rel="stylesheet" href="style/styles.css">
  <link rel="stylesheet" href="style/navbar.css">
  <link rel="stylesheet" href="style/profile.css">
  <script src="https://kit.fontawesome.com/a4bdf5a9b9.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include 'partials/nav.php'; ?>

  <main class="profile-main">
    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="profile-container">
      <div class="profile-header">
        <div class="avatar">
          <i class="fa-solid fa-user"></i>
        </div>
        <h1><?= htmlspecialchars($user['username']) ?></h1>
        <?php if ($user['is_admin']): ?>
          <span class="badge">Admin</span>
        <?php endif; ?>
        <p>Membre depuis <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
        <p class="email"><?= htmlspecialchars($user['email']) ?></p>
        <?php if ($is_own_profile): ?>
          <a href="logout.php" class="btn-logout">Se déconnecter</a>
        <?php endif; ?>
      </div>

      <div class="profile-content">
        <?php if ($is_own_profile): ?>
        <div class="card">
          <h2><i class="fa-solid fa-user-plus"></i> Ajouter un ami</h2>
          <form method="post">
            <input type="hidden" name="action" value="add_friend">
            <input type="text" name="friend_username" placeholder="Pseudo" required>
            <button type="submit">Envoyer</button>
          </form>
        </div>

        <div class="card">
          <h2><i class="fa-solid fa-inbox"></i> Demandes reçues (<?= count($requests) ?>)</h2>
          <?php if (empty($requests)): ?>
            <p class="empty">Aucune demande</p>
          <?php else: ?>
            <ul class="requests-list">
              <?php foreach ($requests as $req): ?>
                <li>
                  <span><?= htmlspecialchars($req['username']) ?></span>
                  <div>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="action" value="accept_friend">
                      <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                      <button type="submit" class="btn-small btn-accept">✓</button>
                    </form>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="action" value="decline_friend">
                      <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                      <button type="submit" class="btn-small btn-decline">✗</button>
                    </form>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <div class="card">
          <h2><i class="fa-solid fa-users"></i> Mes amis (<?= count($friends) ?>)</h2>
          <?php if (empty($friends)): ?>
            <p class="empty">Pas encore d'amis</p>
          <?php else: ?>
            <ul class="friends-list">
              <?php foreach ($friends as $friend): ?>
                <li>
                  <a href="profile.php?user=<?= urlencode($friend['username']) ?>">
                    <?= htmlspecialchars($friend['username']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="card">
          <h2><i class="fa-solid fa-star"></i> Jeux favoris</h2>
          <?php if (!$can_see_times): ?>
            <p class="empty">Profil privé</p>
          <?php else: ?>
            <?php if (empty($favorites)): ?>
                <p class="empty">Aucun favori</p>
            <?php else: ?>
                <div class="games-list">
                    <?php foreach ($favorites as $fav): ?>
                        <div class="game-item">
                            <div>
                                <strong><?= htmlspecialchars($fav['title']) ?></strong>
                                <span class="console"><?= htmlspecialchars($fav['console']) ?></span>
                            </div>
                            <a href="play.php?id=<?= $fav['id'] ?>" class="btn-small">Jouer</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <div class="card">
          <h2><i class="fa-solid fa-clock"></i> Temps de jeu</h2>
          <?php if (!$can_see_times): ?>
            <p class="empty">Profil privé - Ajoutez cette personne en ami pour voir ses temps de jeu</p>
          <?php else: ?>
          <div class="stats">
            <div>
              <strong><?= formatTime($total_seconds) ?></strong>
              <span>Total</span>
            </div>
            <div>
              <strong><?= count($game_times) ?></strong>
              <span>Jeux</span>
            </div>
          </div>
          
          <?php if (empty($game_times)): ?>
            <p class="empty">Aucun temps enregistré</p>
          <?php else: ?>
            <div class="games-list">
              <?php foreach ($game_times as $gt): ?>
                <div class="game-item">
                  <div>
                    <strong><?= htmlspecialchars($gt['title']) ?></strong>
                    <span class="console"><?= htmlspecialchars($gt['console']) ?></span>
                  </div>
                  <div class="time"><?= formatTime($gt['total_seconds']) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>
</body>
</html>