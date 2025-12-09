<?php
session_start();

//verifie si on est co sinon on bouge 
if (empty($_SESSION['user_id'])) {
    //si c'est une requete ajax on repond une erreur
    if (isset($_GET['ajax'])) {
        http_response_code(403);
        exit;
    }
    echo "Connectez-vous pour utiliser la messagerie";
    exit;
}

require_once 'backend/db_connect.php';

$user_id = $_SESSION['user_id'];

//si c'est pour envoyer un message en ajax
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $receiver_id = (int) $_POST['receiver_id'];
    $message = trim($_POST['message']);

    //verifie si on est pote avec le gars
    $check = $bdd->prepare("SELECT * FROM friendships WHERE ((requester_id = ? AND requested_id = ?) OR (requester_id = ? AND requested_id = ?)) AND status = 'accepted'");
    $check->execute([$user_id, $receiver_id, $receiver_id, $user_id]);

    if ($check->fetch() && !empty($message)) {
        //met le message dans la bdd
        $stmt = $bdd->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $receiver_id, $message]);
        echo "ok";
    } else {
        echo "error";
    }
    exit;
}

//si c'est pour recuperer les messages en ajax
if (isset($_GET['action']) && $_GET['action'] === 'get_messages') {
    $friend_id = (int) $_GET['friend_id'];
    $last_id = isset($_GET['last_id']) ? (int) $_GET['last_id'] : 0;

    //recupere les messages entre nous plus grand que last_id
    $msg_stmt = $bdd->prepare("
        SELECT m.*, u.username as sender_name
        FROM messages m
        JOIN users u ON u.id = m.sender_id
        WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
        AND m.id > ?
        ORDER BY m.created_at ASC
    ");
    $msg_stmt->execute([$user_id, $friend_id, $friend_id, $user_id, $last_id]);
    $messages = $msg_stmt->fetchAll(PDO::FETCH_ASSOC);

    //renvoie du json
    header('Content-Type: application/json');
    echo json_encode($messages);
    exit;
}

//recupere tout mes potes pour la liste
$friends_stmt = $bdd->prepare("
    SELECT u.id, u.username
    FROM friendships f
    JOIN users u ON (u.id = f.requested_id AND f.requester_id = ?) OR (u.id = f.requester_id AND f.requested_id = ?)
    WHERE (f.requester_id = ? OR f.requested_id = ?) AND f.status = 'accepted'
    ORDER BY u.username
");
$friends_stmt->execute([$user_id, $user_id, $user_id, $user_id]);
$friends = $friends_stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link rel="stylesheet" href="style/chat.css?v=<?= time() . '1' ?>">
</head>

<body class="chat-body">
    <div class="chat-container">
        <!-- la barre a gauche avec les amis -->
        <div class="friends-sidebar">
            <h4>Mes amis</h4>
            <?php if (empty($friends)): ?>
                <p class="no-friends">Aucun ami</p>
            <?php else: ?>
                <ul class="friends-list-chat">
                    <?php foreach ($friends as $friend): ?>
                        <li class="friend-item" data-id="<?= $friend['id'] ?>">
                            <!-- quand on clique ca charge les messages en js -->
                            <a href="javascript:void(0)" onclick="selectFriend(<?= $friend['id'] ?>, this)">
                                <?= htmlspecialchars($friend['username']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- la zone ou on parle a droite -->
        <div class="chat-area">
            <div id="no-chat-selected" class="no-chat-selected">
                <p>Sélectionnez un ami pour commencer à discuter</p>
            </div>

            <div id="chat-interface" style="display: none; height: 100%; flex-direction: column;">
                <div class="chat-messages" id="messages-container">
                    <!-- les messages vont apparaitre ici -->
                </div>

                <!-- le formulaire pour ecrire -->
                <form id="chat-form" class="chat-form" onsubmit="sendMessage(event)">
                    <input type="hidden" id="receiver_id" name="receiver_id" value="">
                    <input type="text" id="message-input" name="message" placeholder="Tapez votre message..." required
                        autocomplete="off">
                    <button type="submit">Envoyer</button>
                </form>
            </div>
        </div>
    </div>

    <!-- on passe l'id de l'user au js -->
    <script>
        const CURRENT_USER_ID = <?= $user_id ?>;
    </script>
    <script src="js/chat.js"></script>
</body>

</html>