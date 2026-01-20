<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

include_once 'Database.php';
include_once 'Book.php';

$database = new Database();
$db = $database->getConnection();
$book = new Book($db);
$stmt = $book->read();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard | Library Pro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-brand">📚 Library Pro</div>
        <div>
            <span style="margin-right: 15px;">Welcome, <b><?php echo ucfirst($_SESSION['role']); ?></b></span>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div style="display:flex; align-items:center; gap: 20px;">
                <h2 style="margin:0;">Book Repository</h2>
                <input type="text" id="searchBox" placeholder="Search books or authors..." 
                       style="width:300px; padding:10px; border:2px solid #ddd; border-radius:20px; outline:none; transition:0.3s;">
            </div>
            <div>
                <a href="my_books.php" class="btn btn-primary">My Borrowed Books</a>
                <?php if($_SESSION['role'] == 'admin'): ?>
                    <a href="add_book.php" class="btn btn-success">+ Add Book</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; border-radius:6px; margin-bottom:20px;">
                Success! Book issued successfully.
            </div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
             <div style="background:#fff3cd; color:#856404; padding:15px; border-radius:6px; margin-bottom:20px;">
                Book details updated successfully.
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Cover</th> <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td>
                        <img src="uploads/<?php echo $row['cover_image']; ?>" 
                             style="width:50px; height:70px; object-fit:cover; border-radius:4px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
                    </td>
                    
                    <td>#<?php echo $row['book_id']; ?></td>
                    <td><b><?php echo $row['title']; ?></b></td>
                    <td><?php echo $row['author']; ?></td>
                    <td><span style="background:#eee; padding:4px 8px; border-radius:4px; font-size:0.85rem;"><?php echo $row['genre']; ?></span></td>
                    <td>
                        <div style="display:flex; gap:5px;">
                            <?php if ($row['is_available']): ?>
                                <a href="borrow.php?id=<?php echo $row['book_id']; ?>" class="btn btn-primary" style="padding:5px 15px; font-size:0.9rem;">Borrow</a>
                            <?php else: ?>
                                <span style="color:var(--danger); font-weight:600; font-size:0.9rem; padding:5px;">Issued</span>
                            <?php endif; ?>

                            <?php if($_SESSION['role'] == 'admin'): ?>
                                <a href="edit_book.php?id=<?php echo $row['book_id']; ?>" class="btn" style="background-color:#ffc107; color:black; padding:5px 15px; font-size:0.9rem;">Edit</a>
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

            fetch('search.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById("tableBody").innerHTML = data;
            });
        });
    </script>
</body>
</html>