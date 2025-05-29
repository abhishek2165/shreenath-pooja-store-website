<?php
session_start();
$_SESSION = array();
session_unset();
session_destroy();

// If 'rememberMe' cookie is used
if (isset($_COOKIE['rememberMe'])) {
    setcookie('rememberMe', '', time() - 3600, '/');
}

header("Location: login.html");
exit();
?>
