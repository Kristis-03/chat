<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: users.php");
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Вход</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="modal-container" id="modal">
        <div class="modal">
            <h2>Вход</h2>
            <?php if ($error): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form class="auth-form" method="POST">
                <input type="text" name="username" placeholder="Логин" required>
                <div class="password-container">
                    <input type="password" id="passwordInput" name="password" placeholder="Пароль" required>
                    <span class="show-password">👁</span>
                </div>
                <button type="submit">Войти</button>
            </form>
            <div class="auth-links">
                <a href="register.php">Зарегистрироваться</a>
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