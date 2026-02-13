<?php
require_once '../config/database.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

if ($search) {
    $users_query = "SELECT * FROM users WHERE role = 'user' AND (username LIKE '%$search%' OR full_name LIKE '%$search%' OR email LIKE '%$search%' OR department LIKE '%$search%') ORDER BY full_name";
} else {
    $users_query = "SELECT * FROM users WHERE role = 'user' ORDER BY full_name";
}
$users_result = mysqli_query($conn, $users_query);

$total_users_query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$total_users_result = mysqli_query($conn, $total_users_query);
$total_users = mysqli_fetch_assoc($total_users_result)['total'];

$pending_leaves_query = "SELECT COUNT(*) as pending FROM leave_requests WHERE status = 'pending'";
$pending_leaves_result = mysqli_query($conn, $pending_leaves_query);
$pending_leaves = mysqli_fetch_assoc($pending_leaves_result)['pending'];

$approved_leaves_query = "SELECT COUNT(*) as approved FROM leave_requests WHERE status = 'approved'";
$approved_leaves_result = mysqli_query($conn, $approved_leaves_query);
$approved_leaves = mysqli_fetch_assoc($approved_leaves_result)['approved'];

$rejected_leaves_query = "SELECT COUNT(*) as rejected FROM leave_requests WHERE status = 'rejected'";
$rejected_leaves_result = mysqli_query($conn, $rejected_leaves_query);
$rejected_leaves = mysqli_fetch_assoc($rejected_leaves_result)['rejected'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="navigation">
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="pending_leaves.php">Pending Leaves</a></li>
            </ul>
        </div>
        
        <div class="content">
            <h2>System Overview</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h4>Total Users</h4>
                    <div class="stat-value"><?php echo $total_users; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Pending Leaves</h4>
                    <div class="stat-value"><?php echo $pending_leaves; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Approved Leaves</h4>
                    <div class="stat-value"><?php echo $approved_leaves; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Rejected Leaves</h4>
                    <div class="stat-value"><?php echo $rejected_leaves; ?></div>
                </div>
            </div>
            
            <div class="card">
                <h3>All Users</h3>
                
                <div class="search-box">
                    <form method="GET" action="">
                        <input type="text" name="search" placeholder="Search users by name, email, or department..." value="<?php echo $search; ?>">
                    </form>
                </div>
                
                <div class="user-grid">
                    <?php if (mysqli_num_rows($users_result) > 0): ?>
                        <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                            <div class="user-card">
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                </div>
                                <h4><?php echo $user['full_name']; ?></h4>
                                <p><?php echo $user['department']; ?></p>
                                <p style="font-size: 12px; color: #6c757d;"><?php echo $user['email']; ?></p>
                                <div class="action-buttons">
                                    <a href="view_user_profile.php?id=<?php echo $user['id']; ?>" class="btn btn-info">View Profile</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No users found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>