<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    $to_user = $_GET['with'] ?? 0;
    
    // Обработка файла
    $file_path = '';
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $filename = basename($_FILES["file"]["name"]);
        $file_path = $target_dir . $filename;
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $file_path)) {
            die('Ошибка загрузки файла');
        }
    }

    $stmt = $pdo->prepare("INSERT INTO messages 
        (from_user, to_user, message, file_path) 
        VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'], 
        $to_user, 
        $message, 
        $file_path
    ]);
}
?>
