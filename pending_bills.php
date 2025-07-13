<?php
session_start();
include "db.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch pending (unpaid) bills
$sql = "SELECT bills.id, customers.name, bills.bill_date, bills.amount, bills.status 
        FROM bills 
        JOIN customers ON bills.customer_id = customers.id 
        WHERE bills.status = 'unpaid' 
        ORDER BY bills.bill_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pending Bills - Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body, html {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      height: 100vh;
      display: flex;
      background: #f0f2f5;
    }

    .sidebar {
      width: 250px;
      background-color: #007acc;
      color: white;
      display: flex;
      flex-direction: column;
      padding: 20px;
    }

    .sidebar h2 {
      margin-bottom: 30px;
      font-weight: 700;
      font-size: 1.8rem;
      text-align: center;
      letter-spacing: 1.2px;
    }

    .sidebar a {
      color: white;
      text-decoration: none;
      padding: 12px 15px;
      margin-bottom: 10px;
      border-radius: 8px;
      font-weight: 600;
      transition: background-color 0.3s;
    }

    .sidebar a:hover, .sidebar a.active {
      background-color: #005fa3;
    }

    .main-content {
      flex-grow: 1;
      padding: 40px 50px;
      overflow-y: auto;
    }

    h1 {
      color: #007acc;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgb(0 0 0 / 0.1);
    }

    th, td {
      text-align: left;
      padding: 15px 20px;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #007acc;
      color: white;
    }

    tr:hover {
      background-color: #e6f3ff;
    }

    .sidebar .logout-btn {
      margin-top: auto;
      background-color: #ff4d4d;
      padding: 12px;
      border-radius: 8px;
      text-align: center;
      font-weight: 700;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .sidebar .logout-btn:hover {
      background-color: #cc0000;
    }
  </style>
</head>
<body>

  <nav class="sidebar">
    <h2>Admin Menu</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="view_customers.php">View Customers</a>
    <a href="view_bills.php">View Bills</a>
    <a href="admin_generate_bill.php">Generate Bills</a>
    <a href="remove_users.php">Remove Users</a>
    <a href="pending_bills.php" class="active">Pending Bills</a>
    <a href="view_complaints.php">Complaints</a>
    <form action="logout.php" method="post" class="logout-btn">
      <button type="submit" style="background:none; border:none; color:white; font-weight:700; width:100%; cursor:pointer;">Logout</button>
    </form>
  </nav>

  <main class="main-content">
    <h1>Pending Bills</h1>

    <?php if ($result && $result->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>Bill ID</th>
            <th>Customer Name</th>
            <th>Bill Date</th>
            <th>Amount</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($bill = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($bill['id']) ?></td>
              <td><?= htmlspecialchars($bill['name']) ?></td>
              <td><?= htmlspecialchars($bill['bill_date']) ?></td>
              <td>$<?= number_format($bill['amount'], 2) ?></td>
              <td style="color: #d9534f; font-weight: 700;"><?= htmlspecialchars(ucfirst($bill['status'])) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No pending bills found.</p>
    <?php endif; ?>
  </main>

</body>
</html>
