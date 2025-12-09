<?php
session_start();

//si on est deja co on degage sur le profil
if (!empty($_SESSION['user_id'])) {
  header('Location: profile.php');
  exit;
}

require_once 'backend/db_connect.php';



$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  //cherche le gars dans la bdd
  $stmt = $bdd->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  //verifie le mdp
  if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_admin'] = $user['is_admin'];

    //redirige vers l'accueil
    header('Location: index.php');
    exit;
  } else {
    $message = "❌ Email ou mot de passe incorrect.";
  }
  if (isset($_GET['register']) && $_GET['register'] === 'success') {
    $message = "✅ Compte créé avec succès ! Connecte-toi.";
  }

}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Connexion – Arcadia</title>
  <link rel="stylesheet" href="style/styles.css">
  <link rel="stylesheet" href="style/navbar.css">
  <link rel="stylesheet" href="style/login.css">
  <script src="https://kit.fontawesome.com/a4bdf5a9b9.js" crossorigin="anonymous"></script>
</head>

<body>
  <?php include 'partials/nav.php'; ?>

  <main>
    <div class="login-box">
      <h2>Connexion</h2>
      <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
      <?php endif; ?>

      <form method="POST">
        <input type="email" name="email" placeholder="Adresse email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
      </form>

      <p style="margin-top: 10px;">Pas encore de compte ?
        <a href="register.php" style="color:#0ff;">Inscris-toi</a>
      </p>
    </div>
  </main>
</body>

</html>