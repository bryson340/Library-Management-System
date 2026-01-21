<?php
class Book {
    private $conn;
    private $table_name = "books";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Read all books
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // 2. Add a new book (With Quantity)
    public function create($title, $author, $genre, $image, $quantity) {
        $query = "INSERT INTO " . $this->table_name . " (title, author, genre, cover_image, quantity) VALUES (:title, :author, :genre, :image, :quantity)";
        $stmt = $this->conn->prepare($query);

        $title = htmlspecialchars(strip_tags($title));
        $author = htmlspecialchars(strip_tags($author));
        $genre = htmlspecialchars(strip_tags($genre));
        $image = htmlspecialchars(strip_tags($image));
        $quantity = htmlspecialchars(strip_tags($quantity));

        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":author", $author);
        $stmt->bindParam(":genre", $genre);
        $stmt->bindParam(":image", $image);
        $stmt->bindParam(":quantity", $quantity);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // 3. Issue Book (Decrements Stock & Saves Contact Info)
    public function issueBook($book_id, $user_id, $name, $address, $phone) {
        // A. Check Stock
        $check_query = "SELECT quantity FROM " . $this->table_name . " WHERE book_id = :book_id";
        $stmt_check = $this->conn->prepare($check_query);
        $stmt_check->bindParam(":book_id", $book_id);
        $stmt_check->execute();
        $row = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['quantity'] > 0) {
            // B. Decrease Stock
            $query1 = "UPDATE " . $this->table_name . " SET quantity = quantity - 1 WHERE book_id = :book_id";
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->bindParam(":book_id", $book_id);
            
            // C. Save Issue Record with Details
            $query2 = "INSERT INTO issued_books (user_id, book_id, user_name, user_address, user_phone) 
                       VALUES (:user_id, :book_id, :name, :address, :phone)";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(":user_id", $user_id);
            $stmt2->bindParam(":book_id", $book_id);
            $stmt2->bindParam(":name", $name);
            $stmt2->bindParam(":address", $address);
            $stmt2->bindParam(":phone", $phone);

            if($stmt1->execute() && $stmt2->execute()) {
                return true;
            }
        }
        return false;
    }

    // 4. Return Book (Increments Stock)
    public function returnBook($issue_id, $book_id) {
        // A. Calculate fine
        $query_check = "SELECT return_date FROM issued_books WHERE issue_id = :issue_id";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(":issue_id", $issue_id);
        $stmt_check->execute();
        $row = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        $fine = 0;
        $today = date("Y-m-d");
        
        if ($row && $today > $row['return_date']) {
            $date1 = date_create($row['return_date']);
            $date2 = date_create($today);
            $diff = date_diff($date1, $date2);
            $days_late = $diff->format("%a");
            $fine = $days_late * 2;
        }

        // B. Update status
        $query_issue = "UPDATE issued_books SET status = 'returned', return_date = :today WHERE issue_id = :issue_id";
        $stmt_issue = $this->conn->prepare($query_issue);
        $stmt_issue->bindParam(":today", $today);
        $stmt_issue->bindParam(":issue_id", $issue_id);

        // C. Increase Quantity
        $query_book = "UPDATE books SET quantity = quantity + 1 WHERE book_id = :book_id";
        $stmt_book = $this->conn->prepare($query_book);
        $stmt_book->bindParam(":book_id", $book_id);

        if($stmt_issue->execute() && $stmt_book->execute()) {
            return $fine;
        }
        return false;
    }

    // 5. Get Single Book
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE book_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 6. Update Book Details (Now updates Quantity too)
    public function update($id, $title, $author, $genre, $cover_image, $quantity) {
        $query = "UPDATE " . $this->table_name . " 
                  SET title = :title, author = :author, genre = :genre, cover_image = :cover_image, quantity = :quantity 
                  WHERE book_id = :id";
        
        $stmt = $this->conn->prepare($query);

        $title = htmlspecialchars(strip_tags($title));
        $author = htmlspecialchars(strip_tags($author));
        $genre = htmlspecialchars(strip_tags($genre));
        $cover_image = htmlspecialchars(strip_tags($cover_image));
        $quantity = htmlspecialchars(strip_tags($quantity));

        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":author", $author);
        $stmt->bindParam(":genre", $genre);
        $stmt->bindParam(":cover_image", $cover_image);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":id", $id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>