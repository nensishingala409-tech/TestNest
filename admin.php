<?php
session_start();

// ⏳ Restrict access: only logged-in admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    die("<h2>❌ Access Denied. Admins only!</h2>");
}

// DB connection
$DBServerName = "localhost";
$DBUserName   = "root";
$DBPassword   = "";
$DBName       = "login";
$conn = mysqli_connect($DBServerName, $DBUserName, $DBPassword, $DBName);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle delete user
if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM registration WHERE id=$userId");
    echo "<script>alert('🗑 User deleted successfully'); window.location.href='admin.php';</script>";
    exit();
}

// Handle status toggle
if (isset($_GET['toggle'])) {
    $userId = intval($_GET['toggle']);
    $res = mysqli_query($conn, "SELECT status FROM registration WHERE id=$userId");
    $row = mysqli_fetch_assoc($res);
    $newStatus = ($row['status'] === 'active') ? 'inactive' : 'active';
    mysqli_query($conn, "UPDATE registration SET status='$newStatus' WHERE id=$userId");
    echo "<script>alert('🔄 User status updated'); window.location.href='admin.php';</script>";
    exit();
}

// Fetch all users
$result = mysqli_query($conn, "SELECT id, username, email_id, contact_number, role, status FROM registration");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #f2f2f2; }
        a { text-decoration: none; margin: 0 5px; }
        .active { color: green; font-weight: bold; }
        .inactive { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h2>👑 Admin Dashboard</h2>
    <p>Welcome, <?php echo $_SESSION['username']; ?> | <a href="login.php?action=logout">Logout</a></p>

    <table>
        <tr>
            <th>ID</th><th>Username</th><th>Email</th><th>Contact</th><th>Role</th><th>Status</th><th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['email_id']); ?></td>
            <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
            <td><?php echo $row['role']; ?></td>
            <td class="<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></td>
            <td>
                <?php if ($row['role'] !== 'admin') { ?>
                    <a href="admin.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this user?')">🗑 Delete</a>
                    <a href="admin.php?toggle=<?php echo $row['id']; ?>">🔄 Toggle Status</a>
                <?php } else { echo "👑 Admin"; } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
<?php mysqli_close($conn); ?>
