<?php

session_start();
$_SESSION = [];
session_unset();
session_destroy();

// Hapus cookie remember me jika ada
if (isset($_COOKIE['user_logged'])) {
    setcookie('user_logged', '', time() - 3600, "/");
}

header("Location: login.php");
exit;
?>