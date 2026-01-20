<?php
session_start();
include_once 'Database.php';
include_once 'Book.php';

if(isset($_SESSION['user_id']) && isset($_GET['issue_id']) && isset($_GET['book_id'])) {
    
    $database = new Database();
    $db = $database->getConnection();
    $book = new Book($db);

    $issue_id = $_GET['issue_id'];
    $book_id = $_GET['book_id'];

    $fine = $book->returnBook($issue_id, $book_id);

    if($fine !== false) {
        // Redirect with a success message showing the fine
        $msg = ($fine > 0) ? "Book returned! Late Fine: $$fine" : "Book returned successfully! No Fine.";
        header("Location: my_books.php?msg=" . urlencode($msg));
    } else {
        echo "Error returning book.";
    }
} else {
    header("Location: dashboard.php");
}
?>