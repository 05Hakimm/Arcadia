//variable pour savoir a qui on parle
let currentFriendId = null;
//variable pour le timer
let chatInterval = null;
//dernier id de message recu
let lastMessageId = 0;

//ouvre ou ferme la popup
function toggleChat() {
    const popup = document.getElementById('chat-popup');

    if (!popup) {
        createChatPopup();
    } else {
        if (popup.style.display === 'none' || popup.style.display === '') {
            popup.style.display = 'flex';
            //si on parlait deja a qqn on relance le timer
            if (currentFriendId) {
                //on reset pas lastMessageId pour pas tout recharger
                loadMessages();
                startChatInterval();
            }
        } else {
            popup.style.display = 'none';
            stopChatInterval();
        }
    }
}

//cree la popup html
function createChatPopup() {
    const popup = document.createElement('div');
    popup.id = 'chat-popup';
    popup.className = 'first-open';
    //on utilise flex pour que le header et l'iframe s'empilent bien
    popup.style.display = 'flex';
    popup.style.flexDirection = 'column';

    //le header
    const header = document.createElement('div');
    header.className = 'chat-header';
    header.innerHTML = '<h3>Messagerie</h3><button onclick="toggleChat()" class="chat-close">✕</button>';
    //taille fixe pour le header
    header.style.flex = '0 0 auto';

    //l'iframe qui contient chat.php
    const iframe = document.createElement('iframe');
    iframe.id = 'chat-iframe';
    iframe.src = 'chat.php';
    iframe.frameBorder = '0';
    iframe.style.width = '100%';
    //l'iframe prend tout l'espace restant
    iframe.style.flex = '1';
    iframe.style.height = 'auto'; //important pour pas qu'il force 100%
    iframe.style.border = 'none';

    popup.appendChild(header);
    popup.appendChild(iframe);
    document.body.appendChild(popup);

    setTimeout(() => {
        popup.classList.remove('first-open');
    }, 300);
}
function selectFriend(friendId, element) {
    //si c'est le meme ami on fait rien
    if (currentFriendId === friendId) return;

    currentFriendId = friendId;
    lastMessageId = 0; //on reset pour charger tous les messages

    //met a jour l'input hidden
    document.getElementById('receiver_id').value = friendId;

    //gere la classe active
    document.querySelectorAll('.friend-item').forEach(el => el.classList.remove('active'));
    if (element) {
        element.classList.add('active');
    } else {
        //si on a pas l'element (appel auto), on le cherche
        const el = document.querySelector(`.friend-item[data-id="${friendId}"]`);
        if (el) el.classList.add('active');
    }

    //affiche l'interface de chat
    document.getElementById('no-chat-selected').style.display = 'none';
    document.getElementById('chat-interface').style.display = 'flex';

    //vide le container avant de charger
    document.getElementById('messages-container').innerHTML = '';

    //charge les messages direct
    loadMessages();

    //lance le timer pour actualiser
    startChatInterval();

    //focus sur l'input
    setTimeout(() => {
        const input = document.getElementById('message-input');
        if (input) input.focus();
    }, 100);
}

//charge les messages en ajax (json)
function loadMessages() {
    if (!currentFriendId) return;

    fetch(`chat.php?action=get_messages&friend_id=${currentFriendId}&last_id=${lastMessageId}&ajax=1`)
        .then(response => response.json())
        .then(messages => {
            if (messages.length === 0) return;

            const container = document.getElementById('messages-container');
            //on regarde si on etait en bas
            const isAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 100;

            messages.forEach(msg => {
                //on met a jour le dernier id
                if (msg.id > lastMessageId) {
                    lastMessageId = msg.id;
                }

                const div = document.createElement('div');
                const isMe = msg.sender_id == CURRENT_USER_ID;
                div.className = `message ${isMe ? 'sent' : 'received'}`;

                //format date
                const date = new Date(msg.created_at);
                const time = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');

                div.innerHTML = `
                    <strong>${escapeHtml(msg.sender_name)}:</strong>
                    <p>${escapeHtml(msg.message)}</p>
                    <span class="time">${time}</span>
                `;

                container.appendChild(div);
            });

            //si on etait en bas ou si c'est le premier chargement, on scroll en bas
            if (isAtBottom || lastMessageId === messages[messages.length - 1].id) {
                scrollToBottom();
            }
        })
        .catch(err => console.error('Erreur chargement messages', err));
}

//echappe le html pour la securite
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function (m) { return map[m]; });
}

//envoie le message en ajax
function sendMessage(e) {
    e.preventDefault();

    const input = document.getElementById('message-input');
    const message = input.value.trim();

    if (!message || !currentFriendId) return;

    const formData = new FormData();
    formData.append('action', 'send_message');
    formData.append('receiver_id', currentFriendId);
    formData.append('message', message);

    fetch('chat.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(result => {
            if (result === 'ok') {
                input.value = ''; //vide l'input
                loadMessages(); //recharge direct pour voir le message
                scrollToBottom(); //descend
            }
        })
        .catch(err => console.error('Erreur envoi message', err));
}

//scroll en bas
function scrollToBottom() {
    const container = document.getElementById('messages-container');
    if (container) {
        //petit timeout pour etre sur que le rendu est fini
        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 50);
    }
}

//lance le timer
function startChatInterval() {
    stopChatInterval();
    chatInterval = setInterval(loadMessages, 3000);
}

//arrete le timer
function stopChatInterval() {
    if (chatInterval) {
        clearInterval(chatInterval);
        chatInterval = null;
    }
}

//agrandit la zone quand on ecrit
document.addEventListener('DOMContentLoaded', function () {
    const messageInput = document.getElementById('message-input');
    const chatArea = document.querySelector('.chat-area');

    if (messageInput && chatArea) {
        messageInput.addEventListener('focus', function () {
            chatArea.classList.add('input-focused');
        });

        messageInput.addEventListener('blur', function () {
            if (messageInput.value.length === 0) {
                chatArea.classList.remove('input-focused');
            }
        });
    }

    //bouton chat global
    const chatBtn = document.querySelector('.chat-btn');
    if (chatBtn) {
        chatBtn.addEventListener('click', toggleChat);
    }
});
