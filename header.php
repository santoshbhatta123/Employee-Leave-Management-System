<?php
if (!isset($_SESSION)) {
    session_start();
}

function getPageTitle() {
    $page = basename($_SERVER['PHP_SELF'], '.php');
    $titles = [
        'dashboard' => 'Dashboard',
        'apply_leave' => 'Apply Leave',
        'pending_leaves' => 'Pending Leaves',
        'leave_history' => 'Leave History',
        'messages' => 'Messages',
        'profile' => 'Profile',
        'manage_users' => 'Manage Users',
        'add_user' => 'Add User',
        'edit_user' => 'Edit User',
        'view_user_profile' => 'View User Profile'
    ];
    return isset($titles[$page]) ? $titles[$page] : 'Leave Management System';
}

function isActivePage($page) {
    return basename($_SERVER['PHP_SELF']) == $page ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Employee Leave Management System">
    <meta name="author" content="Your Name">
    <title><?php echo getPageTitle(); ?> - Leave Management System</title>
    <link rel="stylesheet" href="<?php echo isset($base_url) ? $base_url : '../'; ?>css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo getPageTitle(); ?></h1>
            <div class="user-info">
                <?php if (isset($_SESSION['full_name'])): ?>
                    <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <a href="<?php echo isset($base_url) ? $base_url : '../'; ?>admin/logout.php" class="logout-btn">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo isset($base_url) ? $base_url : '../'; ?>user/logout.php" class="logout-btn">Logout</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (isset($_SESSION['role'])): ?>
            <div class="navigation">
                <ul>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li><a href="dashboard.php" class="<?php echo isActivePage('dashboard.php'); ?>">Dashboard</a></li>
                        <li><a href="manage_users.php" class="<?php echo isActivePage('manage_users.php'); ?>">Manage Users</a></li>
                        <li><a href="pending_leaves.php" class="<?php echo isActivePage('pending_leaves.php'); ?>">Pending Leaves</a></li>
                    <?php else: ?>
                        <li><a href="dashboard.php" class="<?php echo isActivePage('dashboard.php'); ?>">Dashboard</a></li>
                        <li><a href="apply_leave.php" class="<?php echo isActivePage('apply_leave.php'); ?>">Apply Leave</a></li>
                        <li><a href="pending_leaves.php" class="<?php echo isActivePage('pending_leaves.php'); ?>">Pending Leaves</a></li>
                        <li><a href="leave_history.php" class="<?php echo isActivePage('leave_history.php'); ?>">Leave History</a></li>
                        <li><a href="messages.php" class="<?php echo isActivePage('messages.php'); ?>">Messages</a></li>
                        <li><a href="profile.php" class="<?php echo isActivePage('profile.php'); ?>">Profile</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="content"></div>