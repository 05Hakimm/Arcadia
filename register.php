<?php
session_start();

// Si déjà connecté, rediriger vers le profil
if (!empty($_SESSION['user_id'])) {
  header('Location: profile.php');
  exit;
}

require_once 'backend/db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm = $_POST['confirm'];

  // Vérif champs vides
  if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
    $message = "Tous les champs sont obligatoires.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "Email invalide.";
  } elseif ($password !== $confirm) {
    $message = "Les mots de passe ne correspondent pas.";
  } else {
    // Vérifie si email déjà existant
    $check = $bdd->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->rowCount() > 0) {
      $message = "Cet email est déjà utilisé.";
    } else {
      // Hashage du mot de passe
      $hash = password_hash($password, PASSWORD_DEFAULT);

      // Insertion en BDD
      $stmt = $bdd->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
      $stmt->execute([$username, $email, $hash]);

      // Redirige vers la page de connexion
      header('Location: login.php?register=success');
      exit;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Inscription – Arcadia</title>
  <link rel="stylesheet" href="style/styles.css">
  <link rel="stylesheet" href="style/navbar.css">
  <link rel="stylesheet" href="style/login.css">
  <script src="https://kit.fontawesome.com/a4bdf5a9b9.js" crossorigin="anonymous"></script>
</head>

<body>
  <?php include 'partials/nav.php'; ?>

  <main>
    <div class="login-box">
      <h2>Inscription</h2>
      <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <form method="POST">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="email" name="email" placeholder="Adresse email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <input type="password" name="confirm" placeholder="Confirmer le mot de passe" required>
        <button type="submit">S'inscrire</button>
      </form>

      <p style="margin-top: 10px;">Déjà un compte ?
        <a href="login.php" style="color:#0ff;">Connecte-toi</a>
      </p>
    </div>
  </main>
</body>

</html>