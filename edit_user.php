<?php
require_once '../config/database.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $department = mysqli_real_escape_string($conn, trim($_POST['department']));
    
    if (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
        $error = 'Full name can only contain letters and spaces';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        $error = 'Phone number must be 10-15 digits only';
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $department)) {
        $error = 'Department can only contain letters and spaces';
    } else {
        $query = "UPDATE users SET full_name = '$full_name', email = '$email', phone = '$phone', department = '$department' WHERE id = $user_id";
        
        if (mysqli_query($conn, $query)) {
            $success = 'User updated successfully';
        } else {
            $error = 'Failed to update user';
        }
    }
}

$user_query = "SELECT * FROM users WHERE id = $user_id AND role = 'user'";
$user_result = mysqli_query($conn, $user_query);

if (mysqli_num_rows($user_result) == 0) {
    header('Location: manage_users.php');
    exit();
}

$user = mysqli_fetch_assoc($user_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .input-error {
            border: 2px solid #dc3545 !important;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
        
        .form-group small {
            color: #6c757d;
            font-size: 12px;
            display: block;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Edit User</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="navigation">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php" class="active">Manage Users</a></li>
                <li><a href="pending_leaves.php">Pending Leaves</a></li>
            </ul>
        </div>
        
        <div class="content">
            <div class="card">
                <h3>Edit User Information</h3>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="" id="editUserForm">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" value="<?php echo $user['username']; ?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" id="full_name" value="<?php echo $user['full_name']; ?>" required>
                        <small>Letters and spaces only</small>
                        <div class="error-message" id="nameError">Only letters and spaces are allowed</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="email" value="<?php echo $user['email']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" id="phone" value="<?php echo $user['phone']; ?>" required maxlength="15">
                        <small>10-15 digits only</small>
                        <div class="error-message" id="phoneError">Only numbers allowed (10-15 digits)</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" name="department" id="department" value="<?php echo $user['department']; ?>" required>
                        <small>Letters and spaces only</small>
                        <div class="error-message" id="deptError">Only letters and spaces are allowed</div>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-success">Update User</button>
                        <a href="manage_users.php" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        const fullNameInput = document.getElementById('full_name');
        const phoneInput = document.getElementById('phone');
        const departmentInput = document.getElementById('department');
        
        fullNameInput.addEventListener('input', function() {
            const nameRegex = /^[a-zA-Z\s]*$/;
            const errorDiv = document.getElementById('nameError');
            
            if (!nameRegex.test(this.value)) {
                this.classList.add('input-error');
                errorDiv.style.display = 'block';
                this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
            } else {
                this.classList.remove('input-error');
                errorDiv.style.display = 'none';
            }
        });
        
        phoneInput.addEventListener('input', function() {
            const phoneRegex = /^[0-9]*$/;
            const errorDiv = document.getElementById('phoneError');
            
            if (!phoneRegex.test(this.value)) {
                this.classList.add('input-error');
                errorDiv.style.display = 'block';
                this.value = this.value.replace(/[^0-9]/g, '');
            } else if (this.value.length > 0 && (this.value.length < 10 || this.value.length > 15)) {
                this.classList.add('input-error');
                errorDiv.style.display = 'block';
            } else {
                this.classList.remove('input-error');
                errorDiv.style.display = 'none';
            }
        });
        
        departmentInput.addEventListener('input', function() {
            const deptRegex = /^[a-zA-Z\s]*$/;
            const errorDiv = document.getElementById('deptError');
            
            if (!deptRegex.test(this.value)) {
                this.classList.add('input-error');
                errorDiv.style.display = 'block';
                this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
            } else {
                this.classList.remove('input-error');
                errorDiv.style.display = 'none';
            }
        });
        
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            const nameRegex = /^[a-zA-Z\s]+$/;
            const phoneRegex = /^[0-9]{10,15}$/;
            const deptRegex = /^[a-zA-Z\s]+$/;
            
            if (!nameRegex.test(fullNameInput.value)) {
                e.preventDefault();
                alert('Full name can only contain letters and spaces!');
                fullNameInput.focus();
                return false;
            }
            
            if (!phoneRegex.test(phoneInput.value)) {
                e.preventDefault();
                alert('Phone number must be 10-15 digits only!');
                phoneInput.focus();
                return false;
            }
            
            if (!deptRegex.test(departmentInput.value)) {
                e.preventDefault();
                alert('Department can only contain letters and spaces!');
                departmentInput.focus();
                return false;
            }
        });
    </script>
</body>
</html>