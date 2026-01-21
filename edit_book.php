<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: dashboard.php"); exit(); }

include_once 'Database.php';
include_once 'Book.php';

$database = new Database();
$db = $database->getConnection();
$book = new Book($db);

if(isset($_GET['id'])) {
    $book_id = $_GET['id'];
    $details = $book->readOne($book_id);
    if(!$details) { die("Error: Book not found."); }
} else {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $quantity = $_POST['quantity'];
    
    $current_image = $_POST['current_image']; 
    $final_image = $current_image; 

    if(isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['cover_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        if(in_array(strtolower($filetype), $allowed)) {
            $new_filename = uniqid("book_") . "." . $filetype;
            if(move_uploaded_file($_FILES['cover_image']['tmp_name'], "uploads/" . $new_filename)) {
                $final_image = $new_filename;
            }
        }
    }

    if($book->update($book_id, $title, $author, $genre, $final_image, $quantity)) {
        header("Location: dashboard.php?msg=updated");
        exit();
    } else {
        echo "Error updating book record.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Book | Library Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">📚 Library Management</div>
        <a href="dashboard.php" class="btn btn-primary">Cancel</a>
    </nav>

    <div class="container" style="max-width: 500px;">
        <h2>Edit Book Details</h2>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="current_image" value="<?php echo $details['cover_image']; ?>">

            <label>Book Title:</label>
            <input type="text" name="title" value="<?php echo $details['title']; ?>" required>

            <label>Author:</label>
            <input type="text" name="author" value="<?php echo $details['author']; ?>" required>

            <label>Genre:</label>
            <input type="text" name="genre" value="<?php echo $details['genre']; ?>" required>

            <label>Quantity (Stock):</label>
            <input type="number" name="quantity" value="<?php echo $details['quantity']; ?>" min="0" required 
                   style="width:100%; padding:10px; margin:10px 0; border:2px solid #ddd; border-radius:6px;">

            <label style="display:block; margin-top:15px;">Current Cover:</label>
            <?php if($details['cover_image']): ?>
                <img src="uploads/<?php echo $details['cover_image']; ?>" style="width:80px; border-radius:4px; margin-bottom:10px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
            <?php else: ?>
                <p>No cover uploaded.</p>
            <?php endif; ?>
            
            <label style="display:block;">Change Cover (Optional):</label>
            <input type="file" name="cover_image">

            <button type="submit" class="btn btn-success" style="width:100%; margin-top:20px;">Update Book</button>
        </form>
    </div>
</body>
</html>