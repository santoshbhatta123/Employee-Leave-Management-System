</div>
    </div>
    
    <footer style="text-align: center; padding: 20px; margin-top: 30px; color: white; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px;">
        <p style="margin: 5px 0;">Employee Leave Management System</p>
        <p style="margin: 5px 0; font-size: 14px;">Developed for BCA Project | &copy; <?php echo date('Y'); ?> All Rights Reserved</p>
        <p style="margin: 5px 0; font-size: 12px;">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                Admin Panel | Logged in as: <?php echo $_SESSION['username']; ?>
            <?php elseif (isset($_SESSION['role'])): ?>
                User Panel | Logged in as: <?php echo $_SESSION['username']; ?>
            <?php endif; ?>
        </p>
    </footer>
    
    <script src="<?php echo isset($base_url) ? $base_url : '../'; ?>js/script.js"></script>
</body>
</html>