<?php
session_start();

// Restrict access: only logged-in users
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role     = $_SESSION['role'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .menu { margin-top: 20px; }
        .menu a { display: inline-block; margin-right: 15px; text-decoration: none; color: #007BFF; }
        .menu a:hover { text-decoration: underline; }
        .welcome { font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>🎉 Welcome to Dashboard</h2>
    <p class="welcome">Hello, <?php echo htmlspecialchars($username); ?>!</p>
    <p>Your role: <b><?php echo ucfirst($role); ?></b></p>

    <div class="menu">
        <a href="profile.php">👤 My Profile</a>
        <a href="settings.php">⚙ Settings</a>
        
        <?php if ($role === 'admin') { ?>
            <a href="useres.php">👑 Manage Users</a>
        <?php } ?>
        
        <a href="login.php?action=logout">🚪 Logout</a>
    </div>
</body>
</html>
