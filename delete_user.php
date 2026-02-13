<?php
require_once '../config/database.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$check_query = "SELECT * FROM users WHERE id = $user_id AND role = 'user'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    $delete_query = "DELETE FROM users WHERE id = $user_id";
    mysqli_query($conn, $delete_query);
}

header('Location: manage_users.php');
exit();
?>