<?php
session_start(); 

// Session timeout: 10 minutes
$timeout = 600;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
}
$_SESSION['last_activity'] = time();

// Database connection
$DBServerName = "localhost";
$DBUserName   = "root";
$DBPassword   = "";
$DBName       = "login";

$conn = mysqli_connect($DBServerName, $DBUserName, $DBPassword, $DBName);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Path to the text file for logging
$logFile = __DIR__ . "/users_log.txt";

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    setcookie("user_email", "", time() - 3600, "/");
    setcookie("user_name", "", time() - 3600, "/");

    // ✅ Popup on logout + redirect
    echo "<script>alert('🚪 Session ended successfully!'); window.location.href='login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ===== REGISTER =====
    if ($action === 'register') {
        $username = trim($_POST['username']);
        $email    = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $contact  = trim($_POST['contactnumber']);
        $password = trim($_POST['password']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("❌ Invalid email format.");
        }

        $emailEscaped = mysqli_real_escape_string($conn, $email);
        $checkSql = "SELECT * FROM registration WHERE email_id='$emailEscaped'";
        $result = mysqli_query($conn, $checkSql);

        if (mysqli_num_rows($result) > 0) {
            echo "<h2>⚠ User Already Exists</h2>";
            mysqli_close($conn);
            exit();
        }

        $usernameEscaped = mysqli_real_escape_string($conn, $username);
        $contactEscaped  = mysqli_real_escape_string($conn, $contact);
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $insertSql = "INSERT INTO registration (username, email_id, contact_number, password)
                      VALUES ('$usernameEscaped', '$emailEscaped', '$contactEscaped', '$passwordHash')";

        if (mysqli_query($conn, $insertSql)) {
            echo "<h2>✅ Registration Successful</h2>";
            echo "<p>Username: $username</p>";
            echo "<p>Email: $email</p>";
            echo "<p>Contact: $contact</p>";

            $data = "REGISTERED | Username: $username | Email: $email | Contact: $contact" . PHP_EOL;
            file_put_contents($logFile, $data, FILE_APPEND);
        } else {
            echo "<p>❌ Database error: " . mysqli_error($conn) . "</p>";
        }

    // ===== LOGIN =====
    } elseif ($action === 'login') {
        $email    = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("❌ Invalid email format.");
        }

        $emailEscaped = mysqli_real_escape_string($conn, $email);
        $loginSql = "SELECT * FROM registration WHERE email_id='$emailEscaped'";
        $result = mysqli_query($conn, $loginSql);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $dbPassword = $row['password'];

            if (password_verify($password, $dbPassword)) {
                // ✅ Successful login
                session_regenerate_id(true); // prevent session fixation
                $_SESSION['username']  = $row['username'];
                $_SESSION['email']     = $row['email_id'];
                $_SESSION['logged_in'] = true;
                $_SESSION['role'] = $row['role']; // store user role in session

                // Remember me cookies
                if (!empty($_POST['remember'])) {
                    setcookie("user_email", $row['email_id'], time() + (86400 * 7), "/");
                    setcookie("user_name", $row['username'], time() + (86400 * 7), "/");
                }

                // ✅ Popup message on session start + redirect
                echo "<script>alert('✅ Session started successfully for {$row['username']}'); window.location.href='dashboard.php';</script>";
                exit();

            } else {
                echo "<h2>❌ Login Failed</h2>";
                echo "<p>Wrong password.</p>";
            }

        } else {
            echo "<h2>❌ Login Failed</h2>";
            echo "<p>No account found with this email.</p>";
        }

    } else {
        echo "❌ No action provided!";
    }
}

mysqli_close($conn);
?>
