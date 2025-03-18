<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];

    $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $email]);
    
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="modal-container" id="modal">
        <div class="modal">
            <h2>Регистрация</h2>
            <form class="auth-form" method="POST">
                <input type="text" name="username" placeholder="Логин" required>
                <div class="password-container">
                    <input type="password" id="passwordInput" name="password" placeholder="Пароль" required>
                    <span class="show-password">👁</span>
                </div>
                <input type="email" name="email" placeholder="Email" required>
                <button type="submit">Зарегистрироваться</button>
            </form>
            <div class="auth-links">
                <a href="index.php">Уже есть аккаунт?</a>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('.show-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('passwordInput');
            passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
            this.textContent = passwordInput.type === 'password' ? '👁' : '👁️';
        });
    </script>
</body>
</html>