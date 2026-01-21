<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

include_once 'Database.php';
$database = new Database();
$db = $database->getConnection();

// Fetch ALL issued books + User Details
$query = "SELECT i.issue_id, b.book_id, b.title, i.user_name, i.user_address, i.user_phone, i.issue_date, i.return_date, i.status 
          FROM issued_books i 
          JOIN books b ON i.book_id = b.book_id 
          ORDER BY i.status ASC, i.issue_date DESC";
$stmt = $db->prepare($query);
$stmt->execute();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Records | Library Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">📚 Library Management</div>
        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </nav>

    <div class="container" style="max-width:1100px;">
        <h2>Circulation Records</h2>
        <p style="color:#666;">Monitor all active loans and history.</p>

        <table>
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Recipient Details</th>
                    <th>Date Issued</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                    $deadline = $row['return_date'] ? $row['return_date'] : date('Y-m-d', strtotime($row['issue_date']. ' + 7 days'));
                    $is_overdue = (date("Y-m-d") > $deadline && $row['status'] == 'issued');
                ?>
                <tr style="<?php echo $row['status'] == 'returned' ? 'opacity:0.6; background:#f9f9f9;' : ''; ?>">
                    <td><b><?php echo $row['title']; ?></b></td>
                    
                    <td>
                        <strong><?php echo htmlspecialchars($row['user_name']); ?></strong><br>
                        <small>📞 <?php echo htmlspecialchars($row['user_phone']); ?></small><br>
                        <small>🏠 <?php echo htmlspecialchars($row['user_address']); ?></small>
                    </td>

                    <td><?php echo $row['issue_date']; ?></td>
                    
                    <td style="<?php echo $is_overdue ? 'color:var(--danger); font-weight:bold;' : ''; ?>">
                        <?php echo $deadline; ?>
                        <?php if($is_overdue) echo "<br><small>OVERDUE</small>"; ?>
                    </td>
                    
                    <td>
                        <?php if($row['status'] == 'issued'): ?>
                            <span style="color:var(--primary); font-weight:bold;">Active Loan</span>
                        <?php else: ?>
                            <span style="color:var(--secondary);">Returned</span>
                        <?php endif; ?>
                    </td>
                    
                    <td>
                        <?php if ($row['status'] == 'issued'): ?>
                            <a href="return.php?issue_id=<?php echo $row['issue_id']; ?>&book_id=<?php echo $row['book_id']; ?>" 
                               class="btn btn-danger" style="font-size:0.8rem; padding:5px 10px;">Receive Return</a>
                        <?php else: ?>
                            Completed
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>