<?php
session_start();
require 'config.php';

$with = $_GET['with'] ?? 0;
$current_user = $_SESSION['user_id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM messages 
    WHERE (from_user = ? AND to_user = ?) 
    OR (from_user = ? AND to_user = ?)
    ORDER BY created_at");
    
$stmt->execute([$current_user, $with, $with, $current_user]);

while ($message = $stmt->fetch()): ?>
    <div class="message <?= $message['from_user'] == $current_user ? 'my-message' : 'other-message' ?>">
        <div class="message-content">
            <?= htmlspecialchars($message['message']) ?>
            <?php if ($message['file_path']): ?>
                <a href="<?= $message['file_path'] ?>" download>
                    <?= basename($message['file_path']) ?>
                </a>
            <?php endif; ?>
        </div>
        <div class="message-time">
            <?= date('H:i', strtotime($message['created_at'])) ?>
        </div>
    </div>
<?php endwhile; ?>
