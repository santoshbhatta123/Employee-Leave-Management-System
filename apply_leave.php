<?php
require_once '../config/database.php';
redirectIfNotLoggedIn();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $leave_type = mysqli_real_escape_string($conn, $_POST['leave_type']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $today = new DateTime('today');
    $days_requested = $end->diff($start)->days + 1;
    
    if ($start < $today) {
        $error = 'Cannot apply for leave on previous dates. Start date must be today or a future date';
    } elseif ($start > $end) {
        $error = 'End date must be after start date';
    } else {
        $query = "INSERT INTO leave_requests (user_id, leave_type, start_date, end_date, days_requested, reason) 
                  VALUES ($user_id, '$leave_type', '$start_date', '$end_date', $days_requested, '$reason')";
        
        if (mysqli_query($conn, $query)) {
            $success = 'Leave application submitted successfully';
        } else {
            $error = 'Failed to submit leave application';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Leave</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Apply for Leave</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="navigation">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="apply_leave.php" class="active">Apply Leave</a></li>
                <li><a href="pending_leaves.php">Pending Leaves</a></li>
                <li><a href="leave_history.php">Leave History</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </div>
        
        <div class="content">
            <div class="card">
                <h3>Leave Application Form</h3>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Leave Type</label>
                        <select name="leave_type" required>
                            <option value="">Select Leave Type</option>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Casual Leave">Casual Leave</option>
                            <option value="Annual Leave">Annual Leave</option>
                            <option value="Maternity Leave">Maternity Leave</option>
                            <option value="Paternity Leave">Paternity Leave</option>
                            <option value="Unpaid Leave">Unpaid Leave</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Reason</label>
                        <textarea name="reason" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>