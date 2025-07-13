<?php
session_start();
include "db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Step 1: Get user ID from users table
$sql = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

$user_id = $user['id'];

// Step 2: Get customer ID using user_id
$sql = "SELECT id FROM customers WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    die("Customer not found.");
}

$customer_id = $customer['id'];

// Step 3: Get transactions and related bill info
$sql = "SELECT t.id AS transaction_id, t.transaction_date, t.amount AS paid_amount, 
               t.payment_method, t.status,
               b.bill_date, b.amount AS bill_amount
        FROM transactions t
        LEFT JOIN bills b ON t.bill_id = b.id
        WHERE t.customer_id = ?
        ORDER BY t.transaction_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$transactions = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Your Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }
        .sidebar { height: 100vh; width: 250px; position: fixed; top: 0; left: 0; background-color: #007acc; padding-top: 60px; color: white; }
        .sidebar a { display: block; padding: 15px 25px; color: white; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background-color: #005f99; }
        .content { margin-left: 250px; padding: 30px; }
        .table-container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        h2 { color: #007acc; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h4 class="text-center">Customer Panel</h4>
    <a href="view_profile.php">👤 View Profile</a>
    <a href="bills.php">📜 Bills</a>
    <a href="transactions.php" class="active">💳 Transactions</a>
    <a href="submit_complaint.php">📝 Submit Complaint</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="content">
    <h2>Your Transactions</h2>
    <div class="table-container">
        <?php if ($transactions->num_rows > 0): ?>
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>Transaction No</th>
                        <th>Bill Date</th>
                        <th>Bill Amount ($)</th>
                        <th>Dues ($)</th>
                        <th>Paid Amount ($)</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Transaction Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $transactions->fetch_assoc()):
                    $billAmount = $row['bill_amount'] ?? 0;
                    $paidAmount = $row['paid_amount'] ?? 0;
                    $dues = max(0, $billAmount - $paidAmount);
                ?>
                    <tr>
                        <td>#<?= htmlspecialchars($row['transaction_id']) ?></td>
                        <td><?= $row['bill_date'] ? htmlspecialchars(date("Y-m-d", strtotime($row['bill_date']))) : 'N/A' ?></td>
                        <td><?= $billAmount > 0 ? number_format($billAmount, 2) : 'N/A' ?></td>
                        <td><?= number_format($dues, 2) ?></td>
                        <td><?= number_format($paidAmount, 2) ?></td>
                        <td><?= htmlspecialchars($row['payment_method']) ?></td>
                        <td><?= ucfirst(htmlspecialchars($row['status'])) ?></td>
                        <td><?= htmlspecialchars(date("Y-m-d H:i", strtotime($row['transaction_date']))) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No transactions found.</div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
