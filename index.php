<?php

session_start();

// Cek status login session atau cookie remember me
if (isset($_SESSION['login']) || isset($_COOKIE['user_logged'])) {
    if (isset($_COOKIE['user_logged']) && !isset($_SESSION['login'])) {
        // Rekonstruksi session jika cookie ada
        $_SESSION['login'] = true;
        $_SESSION['username'] = $_COOKIE['user_logged'];
    }
    header("Location: dashboard.php");
    exit;
} else {
    header("Location: auth/login.php");
    exit;
}
?>