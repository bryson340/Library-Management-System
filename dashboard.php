<?php
session_start();
// Admin Guard
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

include_once 'Database.php';
include_once 'Book.php';

$database = new Database();
$db = $database->getConnection();
$book = new Book($db);
$stmt = $book->read();

// --- STATS LOGIC ---
$q1 = "SELECT COUNT(*) as issued FROM issued_books WHERE status = 'issued'";
$issued_books = $db->query($q1)->fetch(PDO::FETCH_ASSOC)['issued'];

$q2 = "SELECT SUM(quantity) as available FROM books";
$available_books = $db->query($q2)->fetch(PDO::FETCH_ASSOC)['available'];

$total_books = $issued_books + $available_books;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard | Library Management</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid var(--primary); }
        .stat-number { font-size: 2.5rem; font-weight: bold; color: var(--dark); margin: 10px 0; }
        .stat-label { color: #777; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-brand">📚 Library Management</div>
        <div>
            <span style="margin-right: 15px;">Admin: <b><?php echo ucfirst($_SESSION['role']); ?></b></span>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="stats-grid">
            <div class="stat-card" style="border-color: #4a90e2;">
                <div class="stat-label">Total Asset Count</div>
                <div class="stat-number"><?php echo $total_books ? $total_books : 0; ?></div>
            </div>
            <div class="stat-card" style="border-color: #e74c3c;">
                <div class="stat-label">Currently Issued</div>
                <div class="stat-number"><?php echo $issued_books; ?></div>
            </div>
            <div class="stat-card" style="border-color: #2ecc71;">
                <div class="stat-label">Available on Shelf</div>
                <div class="stat-number"><?php echo $available_books ? $available_books : 0; ?></div>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div style="display:flex; align-items:center; gap: 20px;">
                <h2 style="margin:0;">Book Inventory</h2>
                <input type="text" id="searchBox" placeholder="Search title, author, or ID..." 
                       style="width:300px; padding:10px; border:2px solid #ddd; border-radius:20px; outline:none;">
            </div>
            <div>
                <a href="records.php" class="btn" style="background-color:#6c757d; margin-right:10px;">📄 View All Records</a>
                <a href="add_book.php" class="btn btn-success">+ Add New Book</a>
            </div>
        </div>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'issued'): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; border-radius:6px; margin-bottom:20px;">
                ✅ Success: Book has been issued to the user.
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Cover</th> <th>ID</th> <th>Title</th> <th>Author</th> <th>Genre</th> <th>Stock</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td>
                        <img src="uploads/<?php echo $row['cover_image']; ?>" 
                             style="width:50px; height:70px; object-fit:cover; border-radius:4px;">
                    </td>
                    <td>#<?php echo $row['book_id']; ?></td>
                    <td><b><?php echo $row['title']; ?></b></td>
                    <td><?php echo $row['author']; ?></td>
                    <td><?php echo $row['genre']; ?></td>
                    <td>
                        <div style="display:flex; flex-direction:column; gap:5px;">
                            <?php if ($row['quantity'] > 0): ?>
                                <span style="color:var(--secondary); font-weight:bold;"><?php echo $row['quantity']; ?> Copies Left</span>
                                <div style="display:flex; gap:5px;">
                                    <a href="issue_book.php?id=<?php echo $row['book_id']; ?>" class="btn btn-primary" style="padding:2px 10px; font-size:0.8rem;">Issue</a>
                                    <a href="edit_book.php?id=<?php echo $row['book_id']; ?>" class="btn" style="background-color:#ffc107; color:black; padding:2px 10px; font-size:0.8rem;">Edit</a>
                                </div>
                            <?php else: ?>
                                <span style="color:var(--danger); font-weight:bold;">Out of Stock</span>
                                <a href="edit_book.php?id=<?php echo $row['book_id']; ?>" class="btn" style="background-color:#ffc107; color:black; padding:2px 10px; font-size:0.8rem; width:fit-content;">Edit</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <script>
        document.getElementById("searchBox").addEventListener("keyup", function() {
            let searchText = this.value;
            let formData = new FormData();
            formData.append('query', searchText);
            fetch('search.php', { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => { document.getElementById("tableBody").innerHTML = data; });
        });
    </script>
</body>
</html>