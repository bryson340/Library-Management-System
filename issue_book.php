<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: dashboard.php"); exit(); }

include_once 'Database.php';
include_once 'Book.php';

$database = new Database();
$db = $database->getConnection();
$book = new Book($db);

if(!isset($_GET['id'])) { header("Location: dashboard.php"); exit(); }
$book_id = $_GET['id'];
$book_details = $book->readOne($book_id);

if ($book_details['quantity'] <= 0) { die("Error: Out of stock."); }

$query = "SELECT user_id, username, role FROM users ORDER BY username ASC";
$stmt_users = $db->prepare($query);
$stmt_users->execute();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    
    if($book->issueBook($book_id, $user_id, $name, $address, $phone)) {
        header("Location: dashboard.php?msg=issued");
        exit();
    } else {
        $error = "Failed to issue book.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Issue Book | Library Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">📚 Library Management</div>
        <a href="dashboard.php" class="btn btn-primary">Cancel</a>
    </nav>

    <div class="container" style="max-width: 600px;">
        <h2>Issue Book</h2>
        
        <div style="background:#f8f9fa; padding:15px; border-radius:6px; margin-bottom:20px; border-left:4px solid var(--primary);">
            <p style="margin:0; font-size:0.9rem; color:#666;">Book Details:</p>
            <h3 style="margin:5px 0;"><?php echo $book_details['title']; ?></h3>
            <span style="font-size:0.85rem; color:var(--secondary);">Stock Left: <?php echo $book_details['quantity']; ?></span>
        </div>

        <form method="POST">
            <label>Link to User Account:</label>
            <select name="user_id" style="width:100%; padding:10px; margin-bottom:15px; border-radius:5px; border:1px solid #ddd;" required>
                <option value="">-- Select Registered User --</option>
                <?php while($user = $stmt_users->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo $user['user_id']; ?>">
                        <?php echo $user['username']; ?> (<?php echo ucfirst($user['role']); ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <h4 style="margin-top:20px; border-bottom:1px solid #eee; padding-bottom:10px;">Recipient Contact Details</h4>
            
            <label>Full Name:</label>
            <input type="text" name="name" placeholder="e.g. John Doe" required>

            <label>Phone Number:</label>
            <input type="text" name="phone" placeholder="e.g. +1 987 654 3210" required>

            <label>Address:</label>
            <textarea name="address" rows="3" placeholder="e.g. Room 304, Dorm B..." style="width:100%; padding:10px; margin:10px 0; border:2px solid #ddd; border-radius:6px;" required></textarea>

            <button type="submit" class="btn btn-success" style="width:100%; margin-top:10px;">Confirm Issue</button>
        </form>
    </div>
</body>
</html>