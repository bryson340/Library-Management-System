<?php
session_start();
include_once 'Database.php';
include_once 'User.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    if($user->login($_POST['username'], $_POST['password'])) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['role'] = $user->role;
        header("Location: dashboard.php"); 
        exit();
    } else {
        $message = "Invalid Credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | Library Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <h2 style="color:var(--primary);">Library Management</h2>
            <p style="color:#777; margin-bottom:20px;">Please sign in to continue</p>
            
            <?php if($message) { echo "<p style='color:red; background:#fee; padding:10px; border-radius:4px;'>$message</p>"; } ?>
            
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px;">Login Access</button>
            </form>
        </div>
    </div>
</body>
</html>