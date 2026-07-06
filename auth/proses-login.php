<?php
// auth/proses-login.php
session_start();
require_once '../config/database.php';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Validasi murni menggunakan Bcrypt bawaan PHP
    if ($user && password_verify($password, $user['password'])) {
        
        $_SESSION['login'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];

        if (isset($_POST['remember'])) {
            setcookie('user_logged', $user['username'], time() + (60 * 60 * 24 * 30), "/");
        }

        header("Location: ../dashboard.php");
        exit;
    } else {
        header("Location: login.php?error=1");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>