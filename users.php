<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id != ?");
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Пользователи</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Кнопка выхода -->
    <div class="logout-button" style="position: fixed; top: 20px; right: 20px;">
        <a href="logout.php" class="btn-logout">Выход</a>
    </div>

    <div class="users-container">
        <div class="users-list">
            <h2>Пользователи</h2>
            <ul>
                <?php foreach ($users as $user): ?>
                    <li onclick="openChat(<?= $user['id'] ?>)" 
                        data-user-id="<?= $user['id'] ?>">
                        <?= htmlspecialchars($user['username']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="chat-container" id="chatContainer" style="display: none;">
            <div class="chat-messages" id="messages"></div>
            <div class="send-form">
                <label class="file-button" for="fileInput">📎</label>
                <input type="file" id="fileInput" style="display: none;">
                <div class="file-preview" id="filePreview"></div>
                <textarea id="messageInput" placeholder="Введите сообщение..."></textarea>
                <button class="send-button" onclick="sendMessage()">➡️</button>
            </div>
        </div>
    </div>

    <script>
        let currentUserId = null;

        // Для нескольких файлов
        document.getElementById('fileInput').addEventListener('change', function() {
            const files = Array.from(this.files);
            const preview = document.getElementById('filePreview');
            
            if (files.length > 0) {
                const content = files.map(file => `
                    <div class="file-info">
                        <span class="file-icon">📄</span>
                        <span class="file-name">${file.name}</span>
                        <span class="file-size">(${formatFileSize(file.size)})</span>
                    </div>
                `).join('');
                preview.innerHTML = content;
                preview.classList.add('show');
            } else {
                preview.innerHTML = '';
                preview.classList.remove('show');
            }
        });

        function formatFileSize(size) {
            if (size < 1024) return `${size} байт`;
            const kb = size / 1024;
            return kb < 1024 
                ? `${kb.toFixed(1)} КБ` 
                : `${(kb / 1024).toFixed(1)} МБ`;
        }

        function openChat(userId) {
            currentUserId = userId;
            document.getElementById('chatContainer').style.display = 'block';
            fetchMessages(userId);
            
            // Добавьте прокрутку вниз
            setTimeout(() => {
                const messagesContainer = document.getElementById('messages');
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }, 100); // Задержка для завершения загрузки сообщений
        }


        function fetchMessages(userId) {
            fetch(`/messenger/get_messages.php?with=${userId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('messages').innerHTML = data;
                });
        }

        function sendMessage() {
            const message = document.getElementById('messageInput').value;
            const file = document.getElementById('fileInput').files[0];
            
            const formData = new FormData();
            formData.append('message', message);
            formData.append('file', file);
            
            fetch(`/messenger/send_message.php?with=${currentUserId}`, {
                method: 'POST',
                body: formData
            })
            .then(() => {
                document.getElementById('messageInput').value = '';
                document.getElementById('fileInput').value = '';
                document.getElementById('filePreview').classList.remove('show');
                
                const messagesContainer = document.getElementById('messages');
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                fetchMessages(currentUserId);
            });
        }


        // AJAX обновление сообщений
        setInterval(() => {
            if (currentUserId) {
                fetchMessages(currentUserId);
            }
        }, 1000);
    </script>
</body>
</html>
