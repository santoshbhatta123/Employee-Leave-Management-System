<?php
require_once '../config/database.php';
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

$query = "SELECT m.*, lr.leave_type, lr.start_date, lr.end_date 
          FROM messages m 
          JOIN leave_requests lr ON m.leave_id = lr.id 
          WHERE m.user_id = $user_id 
          ORDER BY m.created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Admin Messages</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="navigation">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="apply_leave.php">Apply Leave</a></li>
                <li><a href="pending_leaves.php">Pending Leaves</a></li>
                <li><a href="leave_history.php">Leave History</a></li>
                <li><a href="messages.php" class="active">Messages</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </div>
        
        <div class="content">
            <div class="card">
                <h3>Messages from Admin</h3>
                
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="message-box">
                            <div class="message-header">
                                <strong>Re: <?php echo $row['leave_type']; ?> 
                                (<?php echo date('d-m-Y', strtotime($row['start_date'])); ?> to 
                                <?php echo date('d-m-Y', strtotime($row['end_date'])); ?>)</strong>
                                <span><?php echo date('d-m-Y H:i', strtotime($row['created_at'])); ?></span>
                            </div>
                            <div class="message-content">
                                <?php echo nl2br($row['message']); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info">No messages from admin</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>