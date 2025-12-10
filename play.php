<?php
session_start();
require_once 'backend/db_connect.php';
$gameId = $_GET['id'] ?? null;
$userId = $_SESSION['user_id'] ?? null;

if (!$gameId) {
  die("Aucun jeu sélectionné.");
}

$stmt = $bdd->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$gameId]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
  die("Jeu introuvable.");
}

//si on veut mettre en favoris
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_favorite' && $userId) {
  //verifie si deja favoris
  $check = $bdd->prepare("SELECT * FROM favorites WHERE user_id = ? AND game_id = ?");
  $check->execute([$userId, $gameId]);
  if ($check->fetch()) {
    //on vire
    $del = $bdd->prepare("DELETE FROM favorites WHERE user_id = ? AND game_id = ?");
    $del->execute([$userId, $gameId]);
  } else {
    //on ajoute
    $add = $bdd->prepare("INSERT INTO favorites (user_id, game_id) VALUES (?, ?)");
    $add->execute([$userId, $gameId]);
  }
  //on recharge pour voir le changement
  header("Location: play.php?id=$gameId");
  exit;
}

//on regarde si c'est un favoris pour l'affichage
$is_favorite = false;
if ($userId) {
  $fav = $bdd->prepare("SELECT * FROM favorites WHERE user_id = ? AND game_id = ?");
  $fav->execute([$userId, $gameId]);
  if ($fav->fetch()) {
    $is_favorite = true;
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($game['title']) ?> – Arcadia</title>
  <link rel="icon" href="asset/Logo.png" type="image/png">
  <link rel="stylesheet" href="data/emulator.css">
  <link rel="stylesheet" href="style/styles.css">
  <link rel="stylesheet" href="style/navbar.css">
  <link rel="stylesheet" href="style/play.css">
</head>

<body>
  <?php include 'partials/nav.php'; ?>

  <main class="play-container">
    <div class="game-header">
      <h1 class="game-title"><?= htmlspecialchars($game['title']) ?></h1>
      <?php if ($userId): ?>
        <form method="post" class="favorite-form">
          <input type="hidden" name="action" value="toggle_favorite">
          <button type="submit" class="btn-fav"
            title="<?= $is_favorite ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>">
            <?= $is_favorite ? '★' : '☆' ?>
          </button>
        </form>
      <?php endif; ?>
    </div>

    <div class="arcade-wrapper">
      <div class="screen-bezel">
        <div id="game"></div>
      </div>
      <div class="scanlines"></div>
    </div>
  </main>


  <script>
    EJS_player = '#game';
    EJS_core = 'nds';
    <?php
    $gamePath = $game['file_path'];
    // Use the serve_rom.php script to handle split files
    $serveUrl = 'backend/serve_rom.php?path=' . urlencode($gamePath);
    ?>
    EJS_gameUrl = '<?= $serveUrl ?>';

    EJS_biosUrl = '';
    EJS_startOnLoaded = true;

    <?php if ($userId): ?>
        (function () {
          const TRACKING_ENDPOINT = 'backend/track_time.php';
          const GAME_ID = <?= json_encode((int) $gameId) ?>;
          const MIN_DURATION_SECONDS = 5;
          let sessionStart = Date.now();
          let accumulatedSeconds = 0;

          function sendPlayTime(seconds) {
            if (seconds < MIN_DURATION_SECONDS) {
              return Promise.resolve();
            }

            const payload = new FormData();
            payload.append('game_id', GAME_ID);
            payload.append('elapsed_seconds', seconds);

            if (navigator.sendBeacon) {
              navigator.sendBeacon(TRACKING_ENDPOINT, payload);
              return Promise.resolve();
            }

            return fetch(TRACKING_ENDPOINT, {
              method: 'POST',
              body: payload,
              credentials: 'same-origin',
            }).catch(() => { });
          }

          function flush() {
            const now = Date.now();
            const deltaSeconds = Math.round((now - sessionStart) / 1000);
            if (deltaSeconds <= 0) {
              return;
            }
            accumulatedSeconds += deltaSeconds;
            sessionStart = now;

            if (accumulatedSeconds >= MIN_DURATION_SECONDS) {
              const secondsToSend = accumulatedSeconds;
              accumulatedSeconds = 0;
              sendPlayTime(secondsToSend);
            }
          }

          const intervalId = setInterval(flush, 60000);

          window.addEventListener('beforeunload', () => {
            clearInterval(intervalId);
            const now = Date.now();
            accumulatedSeconds += Math.round((now - sessionStart) / 1000);
            if (accumulatedSeconds >= MIN_DURATION_SECONDS) {
              sendPlayTime(accumulatedSeconds);
            }
            accumulatedSeconds = 0;
          });

          document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
              const now = Date.now();
              accumulatedSeconds += Math.round((now - sessionStart) / 1000);
              sessionStart = now;
              if (accumulatedSeconds >= MIN_DURATION_SECONDS) {
                sendPlayTime(accumulatedSeconds);
                accumulatedSeconds = 0;
              }
            } else {
              sessionStart = Date.now();
            }
          });
        })();
    <?php endif; ?>
  </script>
  <script src="data/loader.js"></script>
</body>

</html>