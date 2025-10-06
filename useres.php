<?php
session_start();

// ✅ Restrict access: only admin can view
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    echo "<h2>❌ Access Denied</h2>";
    echo "<p>You are not authorized to view this page.</p>";
    echo "<a href='dashboard.php'>⬅ Back to Dashboard</a>";
    exit();
}

// DB connection
$DBServerName = "localhost";
$DBUserName   = "root";
$DBPassword   = "";
$DBName       = "login";

$conn = mysqli_connect($DBServerName, $DBUserName, $DBPassword, $DBName);
if (!$conn) die("Connection failed: " . mysqli_connect_error());

if (isset($_POST['delete'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $sql = "DELETE FROM registration WHERE email_id='$email' AND role!='admin' LIMIT 1"; // prevent deleting admins
    if ($conn->query($sql)) {
        echo "<p class='msg success'>✅ User with email '$email' deleted successfully.</p>";
    } else {
        echo "<p class='msg error'>❌ Error deleting: " . $conn->error . "</p>";
    }
}

// ✅ Update user
if (isset($_POST['update'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $contact  = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // (⚠️ You might want to hash passwords here, but I left as plain for consistency with your code)
    $sql = "UPDATE registration 
            SET username='$username', contact_number='$contact', password='$password'
            WHERE email_id='$email' AND role!='admin' LIMIT 1";

    if ($conn->query($sql)) {
        echo "<p class='msg success'>✅ User with email '$email' updated successfully.</p>";
    } else {
        echo "<p class='msg error'>❌ Error updating: " . $conn->error . "</p>";
    }
}

// ✅ Search
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT username, email_id, contact_number, password, role 
            FROM registration 
            WHERE username LIKE '%$search%' OR email_id LIKE '%$search%'";
} else {
    $sql = "SELECT username, email_id, contact_number, password, role FROM registration";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin Panel</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f7f9fc;
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #1d33f7;
            text-align: center;
        }

        .msg {
            width: 90%;
            margin: 10px auto;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        form { margin: 0; }

        .search-box {
            max-width: 400px;
            margin: 0 auto 20px auto;
            display: flex;
            gap: 8px;
        }

        .search-box input[type=text] {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .search-box button {
            background: #1d33f7;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        .search-box button:hover { background: #1426b0; }

        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #eee;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #1d33f7;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }
        tr:nth-child(even) { background: #f9f9f9; }
        tr:hover { background: #f1f4ff; }

        input[type=text] {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        button[name="update"] {
            background: #28a745;
            color: white;
        }
        button[name="update"]:hover { background: #218838; }

        button[name="delete"] {
            background: #dc3545;
            color: white;
        }
        button[name="delete"]:hover { background: #b02a37; }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #1d33f7;
            font-weight: bold;
            transition: 0.3s;
        }
        a:hover { color: #1426b0; }
    </style>
</head>
<body>

<h2>👑 Admin Panel - Manage Users</h2>
<p style="text-align:center;">Welcome, <?= htmlspecialchars($_SESSION['username']) ?> | <a href="dashboard.php">⬅ Back to Dashboard</a></p>

<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Search by username or email" value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>

<table>
    <tr>
        <th>Username</th>
        <th>Email</th>
        <th>Contact Number</th>
        <th>Password</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <form method="POST">
            <td><input type="text" name="username" value="<?= htmlspecialchars($row['username']) ?>"></td>
            <td><?= htmlspecialchars($row['email_id']) ?></td>
            <td><input type="text" name="contact_number" value="<?= htmlspecialchars($row['contact_number']) ?>"></td>
            <td>
                <input type="hidden" name="email" value="<?= htmlspecialchars($row['email_id']) ?>">
                <?php if ($row['role'] !== 'admin') { ?>
                    <button type="submit" name="update">Update</button>
                    <button type="submit" name="delete" onclick="return confirm('Delete this user?')">Delete</button>
                <?php } else { ?>
                    👑 (Admin)
                <?php } ?>
            </td>
        </form>
    </tr>
    <?php } ?>
</table>

</body>
</html>
<?php $conn->close(); ?>
