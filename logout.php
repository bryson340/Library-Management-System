<?php
session_start();
session_destroy(); // Destroy all session data
header("Location: login.php"); // Go back to login
exit();
?>