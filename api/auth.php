<?php
require_once '../config/database.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action == 'login') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        header("Location: ../dashboard.php");
    } else {
        header("Location: ../index.php?error=1");
    }
    exit();
} elseif ($action == 'logout') {
    session_destroy();
    header("Location: ../index.php");
    exit();
}
?>