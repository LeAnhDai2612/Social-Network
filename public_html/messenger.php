<?php
session_name('USER_SESSION');

session_start();
$myId = $_SESSION['user_id'];
$receiverId = $_GET['user'] ?? 2; // user muốn chat, giả sử là user #2
?>

<!DOCTYPE html>
<html>
<head>
  <title>Messenger</title>
  <style>
    #chat-box { border: 1px solid #ccc; height: 300px; overflow-y: scroll; padding: 10px; margin-bottom: 10px; }
    .me { text-align: right; color: blue; }
    .other { text-align: left; color: green; }
  </style>
</head>
<body>

<h3>Nhắn tin với người dùng #<?= htmlspecialchars($receiverId) ?></h3>

<div id="chat-box"></div>

<textarea id="msg-input" placeholder="Nhập tin nhắn..."></textarea><br>
<button onclick="sendMessage()">Gửi</button>

<script>
const myId = <?= $myId ?>;
const receiverId = <?= $receiverId ?>;

function loadMessages() {
  fetch(`../core/get_messages.php?user_id=${receiverId}`)
    .then(res => res.json())
    .then(data => {
      const box = document.getElementById('chat-box');
      box.innerHTML = '';
      data.messages.forEach(msg => {
        const div = document.createElement('div');
        div.className = msg.sender_id == myId ? 'me' : 'other';
        div.textContent = msg.content;
        box.appendChild(div);
      });
      box.scrollTop = box.scrollHeight;
    });
}

function sendMessage() {
  const content = document.getElementById('msg-input').value;
  if (!content) return;
  fetch('../core/send_message.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ receiver_id: receiverId, content })
  }).then(() => {
    document.getElementById('msg-input').value = '';
    loadMessages();
  });
}

loadMessages();
setInterval(loadMessages, 2000);
</script>

</body>
</html>
