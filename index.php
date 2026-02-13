<?php
require_once 'config/database.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit();
} else {
    header('Location: login.php');
    exit();
}
?>