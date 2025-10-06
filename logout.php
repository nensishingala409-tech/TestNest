<?php
session_start();
session_unset();
session_destroy();

// Clear cookies
setcookie("remember_email", "", time() - 3600, "/");

header("Location: login.php");
exit;
?>
