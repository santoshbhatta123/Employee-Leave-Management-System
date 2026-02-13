<?php
require_once '../config/database.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $department = mysqli_real_escape_string($conn, trim($_POST['department']));
    
    if (strlen($username) < 4) {
        $error = 'Username must be at least 4 characters long';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
        $error = 'Full name can only contain letters and spaces';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        $error = 'Phone number must be 10-15 digits only';
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $department)) {
        $error = 'Department can only contain letters and spaces';
    } else {
        $check_query = "SELECT * FROM users WHERE username = '$username'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Username already exists';
        } else {
            $check_email = "SELECT * FROM users WHERE email = '$email'";
            $check_email_result = mysqli_query($conn, $check_email);
            
            if (mysqli_num_rows($check_email_result) > 0) {
                $error = 'Email already registered';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $query = "INSERT INTO users (username, password, full_name, email, phone, department, role) 
                          VALUES ('$username', '$hashed_password', '$full_name', '$email', '$phone', '$department', 'user')";
                
                if (mysqli_query($conn, $query)) {
                    $success = 'User added successfully';
                    header('Location: manage_users.php');
                    exit();
                } else {
                    $error = 'Failed to add user';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
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
            <h1>Add New User</h1>
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
                <h3>Create New User Account</h3>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="" id="addUserForm">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" id="username" required minlength="4">
                        <small>At least 4 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" id="password" required minlength="6">
                        <small>At least 6 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" id="full_name" required>
                        <small>Letters and spaces only</small>
                        <div class="error-message" id="nameError">Only letters and spaces are allowed</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" id="phone" required maxlength="15">
                        <small>10-15 digits only</small>
                        <div class="error-message" id="phoneError">Only numbers allowed (10-15 digits)</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" name="department" id="department" required>
                        <small>Letters and spaces only</small>
                        <div class="error-message" id="deptError">Only letters and spaces are allowed</div>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-success">Create User</button>
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
        
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
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