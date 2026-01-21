<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: dashboard.php"); exit(); }

include_once 'Database.php';
include_once 'Book.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();
    $book = new Book($db);

    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $quantity = $_POST['quantity'];
    
    $image_name = "default.jpg";

    if(isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['cover_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        if(in_array(strtolower($filetype), $allowed)) {
            $new_filename = uniqid("book_") . "." . $filetype;
            if(move_uploaded_file($_FILES['cover_image']['tmp_name'], "uploads/" . $new_filename)) {
                $image_name = $new_filename;
            } else {
                $message = "Error moving file.";
            }
        } else {
            $message = "Invalid file type.";
        }
    }

    if(empty($message)) {
        if($book->create($title, $author, $genre, $image_name, $quantity)) {
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Database error.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Book | Library Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">📚 Library Management</div>
        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </nav>

    <div class="container" style="max-width: 500px;">
        <h2>Add New Book</h2>
        <?php if($message) { echo "<p style='color:red; background:#fee; padding:10px;'>$message</p>"; } ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Book Title:</label>
            <input type="text" name="title" required>

            <label>Author:</label>
            <input type="text" name="author" required>

            <label>Genre:</label>
            <input type="text" name="genre" required>

            <label>Quantity (Stock):</label>
            <input type="number" name="quantity" value="5" min="1" required style="width:100%; padding:10px; margin:10px 0; border:2px solid #ddd; border-radius:6px;">

            <label style="display:block; margin-top:15px;">Book Cover (Optional):</label>
            <input type="file" name="cover_image" style="margin-top:5px;">

            <button type="submit" class="btn btn-success" style="width:100%; margin-top:20px;">Save Book</button>
        </form>
    </div>
</body>
</html>