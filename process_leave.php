<?php
require_once '../config/database.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

if (isset($_GET['action']) && $_GET['action'] == 'approve') {
    $leave_id = intval($_GET['leave_id']);
    
    $query = "UPDATE leave_requests SET status = 'approved', processed_date = NOW() WHERE id = $leave_id";
    mysqli_query($conn, $query);
    
    header('Location: pending_leaves.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'reject') {
    $leave_id = intval($_POST['leave_id']);
    $user_id = intval($_POST['user_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    $query = "UPDATE leave_requests SET status = 'rejected', processed_date = NOW() WHERE id = $leave_id";
    mysqli_query($conn, $query);
    
    $message_query = "INSERT INTO messages (user_id, leave_id, message) VALUES ($user_id, $leave_id, '$message')";
    mysqli_query($conn, $message_query);
    
    header('Location: pending_leaves.php');
    exit();
}

header('Location: pending_leaves.php');
exit();
?>