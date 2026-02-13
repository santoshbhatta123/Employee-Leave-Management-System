<?php
require_once '../config/database.php';
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

$total_query = "SELECT COUNT(*) as total FROM leave_requests WHERE user_id = $user_id";
$total_result = mysqli_query($conn, $total_query);
$total_leaves = mysqli_fetch_assoc($total_result)['total'];

$pending_query = "SELECT COUNT(*) as pending FROM leave_requests WHERE user_id = $user_id AND status = 'pending'";
$pending_result = mysqli_query($conn, $pending_query);
$pending_leaves = mysqli_fetch_assoc($pending_result)['pending'];

$approved_query = "SELECT COUNT(*) as approved FROM leave_requests WHERE user_id = $user_id AND status = 'approved'";
$approved_result = mysqli_query($conn, $approved_query);
$approved_leaves = mysqli_fetch_assoc($approved_result)['approved'];

$rejected_query = "SELECT COUNT(*) as rejected FROM leave_requests WHERE user_id = $user_id AND status = 'rejected'";
$rejected_result = mysqli_query($conn, $rejected_query);
$rejected_leaves = mysqli_fetch_assoc($rejected_result)['rejected'];

$recent_query = "SELECT * FROM leave_requests WHERE user_id = $user_id ORDER BY applied_date DESC LIMIT 5";
$recent_result = mysqli_query($conn, $recent_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Employee Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="navigation">
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="apply_leave.php">Apply Leave</a></li>
                <li><a href="pending_leaves.php">Pending Leaves</a></li>
                <li><a href="leave_history.php">Leave History</a></li>
                <li><a href="message.php">Messages</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </div>
        
        <div class="content">
            <h2>Dashboard Overview</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h4>Total Leaves</h4>
                    <div class="stat-value"><?php echo $total_leaves; ?></div>
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
                <h3>Recent Leave Applications</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Applied Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($recent_result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($recent_result)): ?>
                                <tr>
                                    <td><?php echo $row['leave_type']; ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($row['start_date'])); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($row['end_date'])); ?></td>
                                    <td><?php echo $row['days_requested']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $row['status']; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d-m-Y', strtotime($row['applied_date'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No leave applications found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>