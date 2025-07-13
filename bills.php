<?php
session_start();
include "db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Get user id from users table
$userSql = "SELECT id FROM users WHERE username = ?";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("s", $username);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();
$user_id = $user['id'] ?? 0;

if (!$user_id) die("User not found.");

// Get customer_id from customers table using user_id
$custStmt = $conn->prepare("SELECT id FROM customers WHERE user_id = ?");
$custStmt->bind_param("i", $user_id);
$custStmt->execute();
$custResult = $custStmt->get_result();
$customer = $custResult->fetch_assoc();
$customer_id = $customer['id'] ?? 0;

if (!$customer_id) die("Customer ID not found.");

// Handle tabs
$tab = $_GET['tab'] ?? 'due';
$validTabs = ['due', 'history'];
$tab = in_array($tab, $validTabs) ? $tab : 'due';

// Query based on tab
if ($tab === 'due') {
    $sql = "SELECT id, bill_date, units_consumed, due_date, amount FROM bills 
            WHERE customer_id = ? AND status IN ('unpaid', 'generated')
            ORDER BY bill_date DESC";
} else {
    $sql = "SELECT id, bill_date, units_consumed, due_date, amount, status FROM bills 
            WHERE customer_id = ? AND status = 'paid'
            ORDER BY bill_date DESC";
}
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Bills</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }
    .sidebar {
      height: 100vh; width: 250px; position: fixed;
      top: 0; left: 0; background-color: #007acc;
      padding-top: 60px; color: white;
    }
    .sidebar h4 { text-align: center; margin-bottom: 20px; }
    .sidebar a { display: block; padding: 15px 25px; color: white; text-decoration: none; transition: background 0.3s; }
    .sidebar a:hover, .sidebar a.active { background-color: #005f99; }
    .content { margin-left: 250px; padding: 30px; }
    .header { font-size: 1.5rem; font-weight: 600; margin-bottom: 20px; color: #007acc; }
    table { background: white; border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.1); width: 100%; }
    th, td { padding: 12px; text-align: center; vertical-align: middle; border-bottom: 1px solid #ddd; }
    th { background-color: #007acc; color: white; border-radius: 12px 12px 0 0; }
  </style>
</head>
<body>

<div class="sidebar">
  <h4>Customer Panel</h4>
  <a href="view_profile.php">👤 View Profile</a>
  <a href="bills.php" class="active">📜 Bills</a>
  <a href="transactions.php">💰 Transactions</a>
  <a href="submit_complaint.php">📝 Submit Complaints</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<div class="content">
  <div class="container">
    <div class="header">Your Bills</div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3">
      <li class="nav-item">
        <a class="nav-link <?= $tab == 'due' ? 'active' : '' ?>" href="?tab=due">Due Bills</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $tab == 'history' ? 'active' : '' ?>" href="?tab=history">Billing History</a>
      </li>
    </ul>

    <!-- Table -->
    <table class="table table-hover">
      <thead>
        <tr>
          <?php if ($tab === 'due'): ?>
            <th>Bill Date</th>
            <th>Units</th>
            <th>Due Date</th>
            <th>Amount</th>
            <th>Dues Payable</th>
            <th>Action</th>
          <?php else: ?>
            <th>Bill No</th>
            <th>Bill Date</th>
            <th>Units</th>
            <th>Amount</th>
            <th>Due Date</th>
            <th>Status</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <?php if ($tab === 'due'): ?>
                <td><?= date('Y-m-d', strtotime($row['bill_date'])) ?></td>
                <td><?= $row['units_consumed'] ?></td>
                <td><?= date('Y-m-d', strtotime($row['due_date'])) ?></td>
                <td>$<?= number_format($row['amount'], 2) ?></td>
                <td>$<?= number_format($row['amount'], 2) ?></td>
                <td>
                  <button class="btn btn-sm btn-success" onclick="confirmPay(<?= $row['id'] ?>)">Pay</button>
                </td>
              <?php else: ?>
                <td>#<?= $row['id'] ?></td>
                <td><?= date('Y-m-d', strtotime($row['bill_date'])) ?></td>
                <td><?= $row['units_consumed'] ?></td>
                <td>$<?= number_format($row['amount'], 2) ?></td>
                <td><?= date('Y-m-d', strtotime($row['due_date'])) ?></td>
                <td><?= ucfirst($row['status']) ?></td>
              <?php endif; ?>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6">No bills found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function confirmPay(billId) {
  if (confirm("Are you sure you want to pay this bill now?")) {
    window.location.href = "pay_bill.php?bill_id=" + billId;
  } else {
    alert("Payment cancelled.");
  }
}
</script>

</body>
</html>
