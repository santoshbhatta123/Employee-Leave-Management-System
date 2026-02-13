<?php
require_once '../config/database.php';
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM leave_requests WHERE user_id = $user_id AND status = 'pending' ORDER BY applied_date DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Leaves</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pending Leave Requests</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="navigation">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="apply_leave.php">Apply Leave</a></li>
                <li><a href="pending_leaves.php" class="active">Pending Leaves</a></li>
                <li><a href="leave_history.php">Leave History</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </div>
        
        <div class="content">
            <div class="card">
                <h3>Your Pending Leave Requests</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Applied Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $row['leave_type']; ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($row['start_date'])); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($row['end_date'])); ?></td>
                                    <td><?php echo $row['days_requested']; ?></td>
                                    <td><?php echo $row['reason']; ?></td>
                                    <td><?php echo date('d-m-Y H:i', strtotime($row['applied_date'])); ?></td>
                                    <td>
                                        <span class="badge badge-pending">Pending</span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No pending leave requests</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>