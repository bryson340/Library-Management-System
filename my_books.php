<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

include_once 'Database.php';
$database = new Database();
$db = $database->getConnection();

$user_id = $_SESSION['user_id'];
$query = "SELECT i.issue_id, b.book_id, b.title, i.issue_date, i.return_date, i.status 
          FROM issued_books i 
          JOIN books b ON i.book_id = b.book_id 
          WHERE i.user_id = ? ORDER BY i.issue_date DESC";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Books | Library Pro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">📚 Library Pro</div>
        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </nav>

    <div class="container">
        <h2>My Borrowed History</h2>

        <?php if(isset($_GET['msg'])): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; border-radius:6px; margin-bottom:20px; border-left: 5px solid #28a745;">
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Issued Date</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                    $deadline = $row['return_date'] ? $row['return_date'] : date('Y-m-d', strtotime($row['issue_date']. ' + 7 days'));
                    $is_overdue = (date("Y-m-d") > $deadline && $row['status'] == 'issued');
                ?>
                <tr>
                    <td><b><?php echo $row['title']; ?></b></td>
                    <td><?php echo $row['issue_date']; ?></td>
                    <td style="<?php echo $is_overdue ? 'color:var(--danger); font-weight:bold;' : ''; ?>">
                        <?php echo $deadline; ?>
                        <?php if($is_overdue) echo " (OVERDUE)"; ?>
                    </td>
                    <td>
                        <?php if($row['status'] == 'issued'): ?>
                            <span style="color:var(--primary); font-weight:600;">Active</span>
                        <?php else: ?>
                            <span style="color:var(--secondary); font-weight:600;">Returned</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'issued'): ?>
                            <a href="return.php?issue_id=<?php echo $row['issue_id']; ?>&book_id=<?php echo $row['book_id']; ?>" class="btn btn-danger" style="font-size:0.9rem;">Return Book</a>
                        <?php else: ?>
                            <span style="color:#aaa;">Completed</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>