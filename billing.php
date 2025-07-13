<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$dbname = 'electricityms';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['generate_bill'])) {
        $customer_id = intval($_POST['customer_id'] ?? 0);
        $units_consumed = intval($_POST['units_consumed'] ?? 0);
        $billing_date = $_POST['billing_date'] ?? '';
        $due_date = $_POST['due_date'] ?? '';
        $amount = floatval($_POST['amount'] ?? 0);
        $dues_payable = floatval($_POST['dues_payable'] ?? 0);
        $status = 'Pending'; // default status

        if ($customer_id > 0 && $units_consumed > 0 && $billing_date && $due_date && $amount > 0) {
            $stmt = $pdo->prepare("INSERT INTO bills (customer_id, amount, billing_date, due_date, status, units_consumed, dues_payable) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$customer_id, $amount, $billing_date, $due_date, $status, $units_consumed, $dues_payable]);
            $success = "Bill generated successfully.";
        } else {
            $error = "Please fill all fields correctly to generate a bill.";
        }
    }

    if (isset($_POST['remove_bill'])) {
        $bill_id = intval($_POST['bill_id'] ?? 0);
        if ($bill_id) {
            $stmt = $pdo->prepare("DELETE FROM bills WHERE id = ?");
            $stmt->execute([$bill_id]);
            $success = "Bill removed successfully.";
        } else {
            $error = "Invalid bill selected for removal.";
        }
    }

    // New: Handle payment action
    if (isset($_POST['pay_bill'])) {
        $bill_id = intval($_POST['bill_id'] ?? 0);
        if ($bill_id) {
            // Update bill status to 'Paid' and dues_payable to 0
            $stmt = $pdo->prepare("UPDATE bills SET status = 'Paid', dues_payable = 0 WHERE id = ?");
            $stmt->execute([$bill_id]);
            $success = "Bill marked as paid successfully.";
        } else {
            $error = "Invalid bill selected for payment.";
        }
    }
}

// Fetch customers for dropdown
$customersStmt = $pdo->query("SELECT id, name FROM customers ORDER BY name");
$customers = $customersStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch billing history with new columns
$billsStmt = $pdo->query("
    SELECT bills.id, customers.name, bills.units_consumed, bills.billing_date, bills.due_date, bills.amount, bills.dues_payable, bills.status
    FROM bills 
    JOIN customers ON bills.customer_id = customers.id
    ORDER BY bills.billing_date DESC, bills.id DESC
");
$bills = $billsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Billing - Electricity Management System</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
  /* Your existing CSS unchanged */
  * {
    box-sizing: border-box;
  }
  body, html {
    margin: 0; padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f1f5f9;
  }
  .left-sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    background: #1e40af;
    color: white;
    padding-top: 40px;
    box-shadow: 2px 0 10px rgba(0,0,0,0.2);
  }
  .left-sidebar h2 {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 40px;
    color: #bfdbfe;
    text-shadow: 0 0 5px #bfdbfe;
  }
  .left-sidebar nav {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 0 20px;
  }
  .left-sidebar nav a {
    color: #dbeafe;
    text-decoration: none;
    font-weight: bold;
    padding: 14px 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: 0.3s ease;
  }
  .left-sidebar nav a:hover,
  .left-sidebar nav a.active {
    background: #2563eb;
    color: #fff;
  }
  .main-content {
    margin-left: 250px;
    padding: 40px;
    min-height: 100vh;
    background: #f9fafb;
  }
  .content-block {
    background: white;
    padding: 50px;
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.1);
    max-width: 1200px;
    margin: auto;
  }
  h2 {
    font-size: 2rem;
    color: #1e3a8a;
    text-align: center;
    margin-bottom: 30px;
  }
  form {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    margin-bottom: 40px;
  }
  form select, form input {
    padding: 12px;
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    font-size: 16px;
    width: 250px;
    background: #fff;
  }
  form button {
    padding: 12px 30px;
    border: none;
    border-radius: 12px;
    background: #2563eb;
    color: white;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
  }
  form button:hover {
    background: #1e3a8a;
  }
  .error, .success {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
  }
  .error {
    color: #dc2626;
  }
  .success {
    color: #16a34a;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  table, th, td {
    border: 1px solid #cbd5e1;
  }
  th, td {
    padding: 14px;
    text-align: left;
  }
  th {
    background: #2563eb;
    color: white;
  }
  tr:nth-child(even) {
    background: #f1f5f9;
  }
  tr:hover {
    background: #e0f2fe;
  }
  /* Additional button styles to match Remove button */
  .btn-remove, .btn-pay {
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    color: white;
    font-weight: bold;
    font-size: 14px;
  }
  .btn-remove {
    background: #dc2626;
  }
  .btn-pay {
    background: #16a34a;
    margin-left: 5px;
  }
  .btn-remove:hover {
    background: #b91c1c;
  }
  .btn-pay:hover {
    background: #15803d;
  }
</style>
</head>
<body>

<div class="left-sidebar">
  <h2>EMS</h2>
  <nav>
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
    <a href="customers.php"><i class="fas fa-users"></i>Customers</a>
    <a href="billing.php" class="active"><i class="fas fa-file-invoice-dollar"></i>Billing</a>
    <a href="payments.php"><i class="fas fa-credit-card"></i>Payments</a>
    <a href="complaints.php"><i class="fas fa-envelope-open-text"></i>Complaints</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
  </nav>
</div>

<div class="main-content">
  <div class="content-block">
    <h2>Generate New Bill</h2>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
      <select name="customer_id" required>
        <option value="" disabled selected>Select Customer</option>
        <?php foreach ($customers as $cust): ?>
          <option value="<?= $cust['id'] ?>"><?= htmlspecialchars($cust['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <input type="number" name="units_consumed" placeholder="Units Consumed" min="1" required>

      <input type="date" name="billing_date" required>

      <input type="date" name="due_date" required>

      <input type="number" name="amount" placeholder="Amount (₹)" min="0.01" step="0.01" required>

      <input type="number" name="dues_payable" placeholder="Dues Payable (₹)" min="0" step="0.01" required>

      <button type="submit" name="generate_bill">Generate</button>
    </form>

    <h2>Billing History</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Bill Date</th>
          <th>Units Consumed</th>
          <th>Due Date</th>
          <th>Amount (₹)</th>
          <th>Dues Payable (₹)</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($bills): ?>
          <?php foreach ($bills as $bill): ?>
            <tr>
              <td><?= $bill['id'] ?></td>
              <td><?= htmlspecialchars($bill['name']) ?></td>
              <td><?= htmlspecialchars($bill['billing_date']) ?></td>
              <td><?= $bill['units_consumed'] ?></td>
              <td><?= htmlspecialchars($bill['due_date']) ?></td>
              <td><?= number_format($bill['amount'], 2) ?></td>
              <td><?= number_format($bill['dues_payable'], 2) ?></td>
              <td><?= htmlspecialchars($bill['status']) ?></td>
              <td>
                <form method="POST" style="margin:0; display:inline;">
                  <input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">
                  <button type="submit" name="remove_bill"
                    class="btn-remove"
                    onclick="return confirm('Are you sure you want to remove this bill?');">
                    Remove
                  </button>
                </form>

                <?php if ($bill['status'] === 'Pending'): ?>
                  <form method="POST" style="margin:0; display:inline;">
                    <input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">
                    <button type="submit" name="pay_bill" class="btn-pay">
                      Pay
                    </button>
                  </form>
                <?php else: ?>
                  <!-- No pay button if already Paid -->
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="9" style="text-align:center;">No bills found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
