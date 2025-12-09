<?php
session_start();
require_once 'backend/db_connect.php';

// Recupere la recherche si elle existe
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Prepare la requete en fonction de la recherche
if ($search) {
    $stmt = $bdd->prepare("SELECT * FROM games WHERE title LIKE :search ORDER BY title ASC");
    $stmt->execute(['search' => "%$search%"]);
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $games = $bdd->query("SELECT * FROM games ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);
}
?>
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Arcadia – Liste des jeux</title>
    <link rel="icon" href="asset/Logo.png" type="image/png">
    <link rel="stylesheet" href="style/styles.css">
    <link rel="stylesheet" href="style/navbar.css">
    <script src="https://kit.fontawesome.com/a4bdf5a9b9.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'partials/nav.php'; ?>

    <main class="games-container">

        <!-- Barre de recherche -->
        <div class="search-container">
            <form method="GET" action="index.php">
                <input type="text" name="search" class="search-input" placeholder="Rechercher un jeu..."
                    value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <?php if (empty($games)): ?>
            <p class="no-games">Aucun jeu disponible pour le moment 🎮</p>
        <?php else: ?>
            <div class="game-grid">
                <?php foreach ($games as $rom): ?>
                    <div class="game-card" onclick="window.location.href='play.php?id=<?= $rom['id'] ?>'">
                        <img src="<?= htmlspecialchars(ltrim($rom['cover_path'], '/')) ?>"
                            alt="<?= htmlspecialchars($rom['title']) ?>">
                        <h3><?= htmlspecialchars($rom['title']) ?></h3>
                        <p><?= htmlspecialchars($rom['console']) ?></p>
                    </div>
                <?php endforeach; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <div class="game-card add-card" id="addGameBtn">
                        <div class="plus-sign">+</div>
                        <h3>Ajouter un jeu</h3>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <script>
        const addBtn = document.getElementById('addGameBtn');
        const modal = document.getElementById('addGameModal');
        const closeBtn = document.querySelector('.close-btn');

        if (addBtn) {
            addBtn.addEventListener('click', () => modal.style.display = 'block');
        }
        closeBtn.addEventListener('click', () => modal.style.display = 'none');
        window.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });
    </script>

</body>

</html>