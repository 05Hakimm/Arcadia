<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<nav class="navbar">
  <div class="nav-center">
    <a href="index.php" class="nav-logo-link">
      <img src="asset/Logo.png" alt="Arcadia" class="nav-logo">
    </a>
  </div>

  <div class="nav-right">
    <?php if (!empty($_SESSION['user_id'])): ?>
      <!-- ✅ Si connecté → icône seule qui mène vers le profil -->
      <a href="profile.php" class="login-btn" title="Profil">
        <i class="fa-solid fa-user"></i>
      </a>
    <?php else: ?>
      <!-- ❌ Si non connecté → icône mène vers connexion -->
      <a href="login.php" class="login-btn" title="Connexion">
        <i class="fa-solid fa-user"></i>
      </a>
    <?php endif; ?>

    <img src="asset/avatar.png" alt="Avatar" class="avatar-icon">
  </div>

  <?php if (!empty($_SESSION['user_id'])): ?>
    <!-- Bouton chat en bas à droite sur toutes les pages -->
    <img src="asset/chat.png" alt="Chat icon" class="chat-icon">
    <button class="chat-btn" title="Messagerie">
      <i class="fa-solid fa-comments"></i>
    </button>
    <!-- Script pour gérer la popup de chat -->
    <script src="js/chat.js"></script>
  <?php endif; ?>

</nav>