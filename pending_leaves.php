<?php
require_once '../config/database.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

$query = "SELECT lr.*, u.full_name, u.department 
          FROM leave_requests lr 
          JOIN users u ON lr.user_id = u.id 
          WHERE lr.status = 'pending' 
          ORDER BY lr.applied_date DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Leave Requests</title>
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
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="pending_leaves.php" class="active">Pending Leaves</a></li>
            </ul>
        </div>
        
        <div class="content">
            <div class="card">
                <h3>All Pending Leave Requests</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Applied Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $row['full_name']; ?></td>
                                    <td><?php echo $row['department']; ?></td>
                                    <td><?php echo $row['leave_type']; ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($row['start_date'])); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($row['end_date'])); ?></td>
                                    <td><?php echo $row['days_requested']; ?></td>
                                    <td><?php echo $row['reason']; ?></td>
                                    <td><?php echo date('d-m-Y H:i', strtotime($row['applied_date'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="approveLeave(<?php echo $row['id']; ?>)" class="btn btn-success">Approve</button>
                                            <button onclick="openRejectModal(<?php echo $row['id']; ?>, <?php echo $row['user_id']; ?>)" class="btn btn-danger">Reject</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align: center;">No pending leave requests</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Reject Leave Request</h3>
                <span class="close" onclick="closeRejectModal()">&times;</span>
            </div>
            <form id="rejectForm" method="POST" action="process_leave.php">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="leave_id" id="reject_leave_id">
                <input type="hidden" name="user_id" id="reject_user_id">
                
                <div class="form-group">
                    <label>Reason for Rejection</label>
                    <textarea name="message" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-danger">Submit Rejection</button>
            </form>
        </div>
    </div>
    
    <script>
        function approveLeave(leaveId) {
            if (confirm('Are you sure you want to approve this leave request?')) {
                window.location.href = 'process_leave.php?action=approve&leave_id=' + leaveId;
            }
        }
        
        function openRejectModal(leaveId, userId) {
            document.getElementById('reject_leave_id').value = leaveId;
            document.getElementById('reject_user_id').value = userId;
            document.getElementById('rejectModal').style.display = 'block';
        }
        
        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('rejectModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>