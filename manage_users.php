<?php
require_once '../config/database.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

$users_query = "SELECT * FROM users WHERE role = 'user' ORDER BY full_name";
$users_result = mysqli_query($conn, $users_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Users</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="navigation">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php" class="active">Manage Users</a></li>
                <li><a href="edit_user.php" class="active">Edit User</li>
                <li><a href="pending_leaves.php">Pending Leaves</a></li>
            </ul>
        </div>
        
        <div class="content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>User Management</h2>
                <a href="add_user.php" class="btn btn-success">Add New User</a>
            </div>
            
            <div class="card">
                <h3>All Users</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($users_result) > 0): ?>
                            <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                                <tr>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo $user['full_name']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td><?php echo $user['phone']; ?></td>
                                    <td><?php echo $user['department']; ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-info">Edit</a>
                                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>