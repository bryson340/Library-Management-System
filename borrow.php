<?php
session_start();
include_once 'Database.php';
include_once 'Book.php';

// Check if user is logged in AND a book_id was sent
if(isset($_SESSION['user_id']) && isset($_GET['id'])) {
    
    $database = new Database();
    $db = $database->getConnection();
    $book = new Book($db);

    $book_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Call the borrow function
    if($book->borrow($book_id, $user_id)) {
        header("Location: dashboard.php?msg=success");
    } else {
        echo "Error borrowing book.";
    }
} else {
    header("Location: dashboard.php");
}
?>