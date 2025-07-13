<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "db.php";

// Ensure only admins can access
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success_message = "";
$error_message   = "";

// Fetch customers to populate dropdown
$customers = [];
$res = $conn->query("SELECT id, name FROM customers ORDER BY name ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $customers[] = $row;
    }
} else {
    die("Error fetching customers: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['generate_bill'])) {
    // Gather and validate inputs
    $customer_id    = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : 0;
    $units_consumed = isset($_POST['units_consumed']) ? intval($_POST['units_consumed']) : 0;
    $bill_date      = $_POST['bill_date']   ?? '';
    $due_date       = $_POST['due_date']    ?? '';
    $unit_rate      = 5;
    $status         = 'unpaid';

    if ($customer_id <= 0) {
        $error_message = "Please select a valid customer.";
    } elseif ($units_consumed <= 0) {
        $error_message = "Units consumed must be a positive number.";
    } elseif (!$bill_date || !$due_date) {
        $error_message = "Please select both Bill Date and Due Date.";
    } elseif ($due_date < $bill_date) {
        $error_message = "Due Date cannot be earlier than Bill Date.";
    } else {
        // Calculate amount
        $amount = $units_consumed * $unit_rate;

        // Prepare and execute insert
        $sql = "INSERT INTO bills (bill_date, units_consumed, customer_id, amount, due_date, status)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }

        $stmt->bind_param(
            "siidss",
            $bill_date,
            $units_consumed,
            $customer_id,
            $amount,
            $due_date,
            $status
        );

        if ($stmt->execute()) {
            $success_message = "Bill generated successfully for customer ID: $customer_id";
        } else {
            $error_message = "Error generating bill: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Bills Management</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f0f2f5;
      margin: 0;
      padding: 0;
      display: flex;
      height: 100vh;
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
      display: block;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #005fa3;
    }
    .logout-btn {
      margin-top: auto;
      background-color: #ff4d4d;
      padding: 12px;
      border-radius: 8px;
      text-align: center;
      font-weight: 700;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .logout-btn:hover {
      background-color: #cc0000;
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
    .tabs { display: flex; gap: 20px; margin-bottom: 30px; }
    .tab {
      padding: 10px 20px; background: #ddd; border-radius: 8px;
      cursor: pointer; font-weight: 600; user-select: none;
    }
    .tab.active { background: #007acc; color: white; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    form {
      background: white; padding: 25px; border-radius: 12px;
      max-width: 500px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    label, select, input, button {
      display: block; width: 100%; margin-bottom: 20px;
      font-size: 16px;
    }
    select, input[type="number"], input[type="date"], button {
      padding: 10px; border: 1px solid #ccc; border-radius: 8px;
    }
    button {
      background-color: #007acc; color: white;
      font-weight: 600; border: none; cursor: pointer;
    }
    button:hover { background-color: #005fa3; }
    .message { margin-bottom: 20px; padding: 10px; border-radius: 6px; }
    .success { background-color: #d4edda; color: #155724; }
    .error   { background-color: #f8d7da; color: #721c24; }
  </style>
  <script>
    function showTab(tabName) {
      document.querySelectorAll('.tab').forEach(t => t.classList.toggle('active', t.dataset.tab===tabName));
      document.querySelectorAll('.tab-content').forEach(c => c.classList.toggle('active', c.id===tabName));
    }
    window.onload = () => showTab('generate');
  </script>
</head>
<body>

<nav class="sidebar">
  <h2>Admin Menu</h2>
  <a href="admin_dashboard.php">Dashboard</a>
  <a href="view_customers.php">View Customers</a>
  <a href="view_bills.php">View Bills</a>
  <a href="admin_generate_bill.php" class="active">Generate Bills</a>
  <a href="remove_users.php">Remove Users</a>
  <a href="pending_bills.php">Pending Bills</a>
  <a href="view_complaints.php">Complaints</a>
  <form action="logout.php" method="post" class="logout-btn">
    <button type="submit" style="background:none;border:none;color:white;width:100%;">Logout</button>
  </form>
</nav>

<main class="main-content">
  <h1>Bills Management</h1>

  <div class="tabs">
    <div class="tab active" data-tab="generate" onclick="showTab('generate')">Generate New Bill</div>
  </div>

  <?php if ($success_message): ?>
    <div class="message success"><?= htmlspecialchars($success_message) ?></div>
  <?php elseif ($error_message): ?>
    <div class="message error"><?= htmlspecialchars($error_message) ?></div>
  <?php endif; ?>

  <div id="generate" class="tab-content active">
    <form method="post" novalidate>
      <input type="hidden" name="generate_bill" value="1" />

      <label for="customer_id">Customer:</label>
      <select name="customer_id" id="customer_id" required>
        <option value="">-- Select Customer --</option>
        <?php foreach ($customers as $c): ?>
          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="units_consumed">Units Consumed:</label>
      <input
        type="number" step="1" min="0"
        name="units_consumed" id="units_consumed"
        required placeholder="Enter units consumed"
      />

      <label for="bill_date">Bill Date:</label>
      <input
        type="date" name="bill_date" id="bill_date"
        required value="<?= date('Y-m-d') ?>"
      />

      <label for="due_date">Due Date:</label>
      <input
        type="date" name="due_date" id="due_date"
        required value="<?= date('Y-m-d', strtotime('+15 days')) ?>"
      />

      <button type="submit">Generate Bill</button>
    </form>
  </div>
</main>

</body>
</html>
