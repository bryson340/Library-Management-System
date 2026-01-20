<?php
session_start(); // Important: Start session to check admin role
include_once 'Database.php';

if (!isset($_POST['query'])) { exit(); }

$database = new Database();
$db = $database->getConnection();
$search = "%" . $_POST['query'] . "%";

$query = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ? ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$search, $search]);

if ($stmt->rowCount() == 0) {
    echo "<tr><td colspan='6' style='text-align:center;'>No books found...</td></tr>";
} else {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        
        // 1. Image Column
        echo "<td>
                <img src='uploads/" . $row['cover_image'] . "' 
                style='width:50px; height:70px; object-fit:cover; border-radius:4px; box-shadow:0 2px 5px rgba(0,0,0,0.1);'>
              </td>";

        // 2. Data Columns
        echo "<td>#" . $row['book_id'] . "</td>";
        echo "<td><b>" . $row['title'] . "</b></td>";
        echo "<td>" . $row['author'] . "</td>";
        echo "<td><span style='background:#eee; padding:4px 8px; border-radius:4px; font-size:0.85rem;'>" . $row['genre'] . "</span></td>";
        
        // 3. Action Column
        echo "<td><div style='display:flex; gap:5px;'>";
        
        // Borrow Button Logic
        if ($row['is_available']) {
            echo "<a href='borrow.php?id=" . $row['book_id'] . "' class='btn btn-primary' style='padding:5px 15px; font-size:0.9rem;'>Borrow</a>";
        } else {
            echo "<span style='color:var(--danger); font-weight:600; font-size:0.9rem; padding:5px;'>Issued</span>";
        }

        // Edit Button Logic (Only for Admin)
        if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
            echo "<a href='edit_book.php?id=" . $row['book_id'] . "' class='btn' style='background-color:#ffc107; color:black; padding:5px 15px; font-size:0.9rem;'>Edit</a>";
        }

        echo "</div></td>";
        echo "</tr>";
    }
}
?>