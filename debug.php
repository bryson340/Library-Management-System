<?php
// debug.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'Database.php';

echo "<h2>Step 1: Testing Database Connection...</h2>";
$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "<span style='color:green'>Connection Successful!</span><br>";
} else {
    die("<span style='color:red'>Connection Failed. Check Database.php settings.</span>");
}

echo "<h2>Step 2: Looking for user 'admin'...</h2>";
$query = "SELECT * FROM users WHERE username = 'admin'";
$stmt = $db->prepare($query);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<span style='color:green'>User 'admin' found!</span><br>";
    echo "<strong>Stored Hash in DB:</strong> " . $row['password'] . "<br>";
} else {
    die("<span style='color:red'>User 'admin' NOT found. Run the INSERT SQL again.</span>");
}

echo "<h2>Step 3: Verifying password 'admin123'...</h2>";
$input_password = "admin123";

if (password_verify($input_password, $row['password'])) {
    echo "<h1 style='color:green'>SUCCESS: Password Matches!</h1>";
    echo "The login system should work. If it doesn't, the issue is likely in login.php logic.";
} else {
    echo "<h1 style='color:red'>FAILURE: Password Mismatch!</h1>";
    echo "The hash in your database is different from what 'admin123' generates.<br>";
    
    // Generate a fresh hash to fix it
    $new_hash = password_hash("admin123", PASSWORD_DEFAULT);
    echo "<br><strong>Copy this SQL to fix it:</strong><br>";
    echo "<textarea cols='100' rows='3'>UPDATE users SET password = '$new_hash' WHERE username = 'admin';</textarea>";
}
?>