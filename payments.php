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

// Handle form submission for new payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['make_payment'])) {
    $bill_id = intval($_POST['bill_id'] ?? 0);
    $payment_amount = floatval($_POST['payment_amount'] ?? 0);
    $payment_date = $_POST['payment_date'] ?? '';

    if ($bill_id && $payment_amount > 0 && $payment_date) {
        // Check total paid so far for this bill
        $stmt = $pdo->prepare("SELECT amount FROM bills WHERE id = ?");
        $stmt->execute([$bill_id]);
        $bill = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$bill) {
            $error = "Selected bill does not exist.";
        } else {
            $bill_amount = $bill['amount'];

            // Get total payments made for this bill
            $stmt = $pdo->prepare("SELECT IFNULL(SUM(amount),0) as total_paid FROM payments WHERE bill_id = ?");
            $stmt->execute([$bill_id]);
            $total_paid = $stmt->fetchColumn();

            $new_total_paid = $total_paid + $payment_amount;

            if ($new_total_paid > $bill_amount) {
                $error = "Payment exceeds the bill amount. You can only pay up to ₹" . number_format($bill_amount - $total_paid, 2);
            } else {
                // Insert payment
                $stmt = $pdo->prepare("INSERT INTO payments (bill_id, amount, payment_date) VALUES (?, ?, ?)");
                $stmt->execute([$bill_id, $payment_amount, $payment_date]);

                if ($new_total_paid == $bill_amount) {
                    $success = "Payment recorded. Bill is now fully paid.";
                } else {
                    $success = "Partial payment recorded. Remaining balance: ₹" . number_format($bill_amount - $new_total_paid, 2);
                }
            }
        }
    } else {
        $error = "Please fill all payment fields correctly.";
    }
}

// Handle CSV download request
if (isset($_GET['download_csv'])) {
    $filter_customer = intval($_GET['filter_customer'] ?? 0);
    $filter_start = $_GET['filter_start'] ?? '';
    $filter_end = $_GET['filter_end'] ?? '';

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="payment_history.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Payment ID', 'Customer', 'Bill Amount', 'Payment Amount', 'Payment Date']);

    $sql = "SELECT payments.id, customers.name AS customer_name, bills.amount AS bill_amount, payments.amount AS payment_amount, payments.payment_date
            FROM payments
            JOIN bills ON payments.bill_id = bills.id
            JOIN customers ON bills.customer_id = customers.id
            WHERE 1=1";

    $params = [];

    if ($filter_customer) {
        $sql .= " AND customers.id = ?";
        $params[] = $filter_customer;
    }
    if ($filter_start) {
        $sql .= " AND payments.payment_date >= ?";
        $params[] = $filter_start;
    }
    if ($filter_end) {
        $sql .= " AND payments.payment_date <= ?";
        $params[] = $filter_end;
    }

    $sql .= " ORDER BY payments.payment_date DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['id'],
            $row['customer_name'],
            number_format($row['bill_amount'], 2),
            number_format($row['payment_amount'], 2),
            $row['payment_date']
        ]);
    }
    fclose($output);
    exit();
}

// Fetch customers for dropdown
$customersStmt = $pdo->query("SELECT id, name FROM customers ORDER BY name");
$customers = $customersStmt->fetchAll(PDO::FETCH_ASSOC);

// Get filters from GET parameters (for filtering form and query)
$filter_customer = intval($_GET['filter_customer'] ?? 0);
$filter_start = $_GET['filter_start'] ?? '';
$filter_end = $_GET['filter_end'] ?? '';

// Prepare SQL to fetch payments with filters
$sql = "SELECT payments.id, customers.name AS customer_name, bills.amount AS bill_amount, payments.amount AS payment_amount, payments.payment_date
        FROM payments
        JOIN bills ON payments.bill_id = bills.id
        JOIN customers ON bills.customer_id = customers.id
        WHERE 1=1";

$params = [];

if ($filter_customer) {
    $sql .= " AND customers.id = ?";
    $params[] = $filter_customer;
}
if ($filter_start) {
    $sql .= " AND payments.payment_date >= ?";
    $params[] = $filter_start;
}
if ($filter_end) {
    $sql .= " AND payments.payment_date <= ?";
    $params[] = $filter_end;
}

$sql .= " ORDER BY payments.payment_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch unpaid bills for payment form (bills with balance > 0)
$sqlUnpaid = "SELECT bills.id, customers.name, bills.amount,
              bills.amount - IFNULL((SELECT SUM(amount) FROM payments WHERE bill_id = bills.id), 0) AS balance
              FROM bills
              JOIN customers ON bills.customer_id = customers.id
              HAVING balance > 0
              ORDER BY bills.billing_date DESC";

$unpaidStmt = $pdo->query($sqlUnpaid);
$unpaidBills = $unpaidStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total payments collected (filtered)
$totalPayments = 0.0;
foreach ($payments as $p) {
    $totalPayments += floatval($p['payment_amount']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Payments - Electricity Management System</title>
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
/>
<style>
  /* Include your previous CSS for sidebar and layout here, or link external CSS */
  /* For brevity, I will include minimal CSS for layout */

  body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f0f4f8;
  color: #333;
  font-size: 18px; /* Increased from 16px */
  line-height: 1.6;
}

.left-sidebar {
  width: 270px; /* Slightly wider */
  background: #1e40af;
  color: #f3f4f6;
  position: fixed;
  height: 100%;
  padding-top: 30px;
}

.left-sidebar h2 {
  font-size: 2.4rem; /* Slightly bigger */
  margin-left: 30px;
  margin-bottom: 40px;
}

.left-sidebar nav a {
  display: flex;
  align-items: center;
  padding: 14px 30px;
  color: #cbd5e1;
  font-size: 1.2rem; /* Increased font size */
  font-weight: 600;
  margin-bottom: 12px;
  border-left: 6px solid transparent;
  transition: all 0.2s ease;
  border-radius: 4px 0 0 4px;
}

.left-sidebar nav a:hover,
.left-sidebar nav a.active {
  background: #2563eb;
  border-left-color: #93c5fd;
  color: #f3f4f6;
}

.left-sidebar nav a i {
  font-size: 1.6rem; /* Larger icons */
  margin-right: 10px;
}

.content {
  margin-left: 270px; /* Match sidebar width */
  padding: 50px 70px; /* More padding */
  width: 100%;
  max-width: 1200px;
}

h1 {
  margin-bottom: 30px;
  font-weight: 700;
  font-size: 2.8rem; /* Slightly bigger */
}

.message {
  padding: 16px 25px; /* Bigger padding */
  border-radius: 8px;
  margin-bottom: 25px;
  font-size: 1.1rem;
}

form.payment-form {
  background: white;
  border-radius: 10px;
  padding: 30px 35px; /* More padding */
  box-shadow: 0 0 20px rgba(0,0,0,0.08);
  margin-bottom: 35px;
  max-width: 650px;
  font-size: 1.1rem;
}

form.payment-form label {
  display: block;
  margin-bottom: 12px;
  font-weight: 700;
}

form.payment-form select,
form.payment-form input[type="number"],
form.payment-form input[type="date"] {
  width: 100%;
  padding: 14px 16px;
  font-size: 1.1rem;
  border-radius: 8px;
  border: 1.5px solid #cbd5e1;
  margin-bottom: 25px;
  box-sizing: border-box;
}

form.payment-form button {
  background: #1e40af;
  color: white;
  font-weight: 700;
  border: none;
  padding: 15px 30px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1.2rem;
  transition: background 0.3s ease;
}

form.payment-form button:hover {
  background: #2563eb;
}

.filters {
  background: white;
  padding: 25px 30px;
  border-radius: 10px;
  margin-bottom: 35px;
  max-width: 750px;
  box-shadow: 0 0 20px rgba(0,0,0,0.08);
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  align-items: flex-end;
  font-size: 1.1rem;
}

.filters label {
  font-weight: 700;
}

.filters select,
.filters input[type="date"] {
  padding: 10px 14px;
  border-radius: 8px;
  border: 1.5px solid #cbd5e1;
  font-size: 1.1rem;
}

.filters button {
  background: #2563eb;
  border: none;
  color: white;
  font-weight: 700;
  padding: 12px 28px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1.2rem;
  transition: background 0.3s ease;
}

.filters button:hover {
  background: #1e40af;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 10px;
  box-shadow: 0 0 20px rgba(0,0,0,0.08);
  font-size: 1.1rem;
}

table thead {
  background: #1e40af;
  color: white;
  font-size: 1.2rem;
}

table th, table td {
  padding: 15px 22px;
  text-align: left;
  border-bottom: 1.5px solid #e2e8f0;
}

table tbody tr:hover {
  background: #e0ebff;
}

.total-payments {
  margin-top: 20px;
  font-weight: 700;
  font-size: 1.4rem;
  color: #1e40af;
}

</style>
</head>
<body>
  <aside class="left-sidebar">
    <h2>EMS</h2>
    <nav>
      <a href="dashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a>
      <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
      <a href="billing.php"><i class="fas fa-file-invoice"></i> Billing</a>
      <a href="payments.php" class="active"><i class="fas fa-money-check-dollar"></i> Payments</a>
      <a href="complaints.php"><i class="fas fa-comments"></i> Complaints</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
  </aside>

  <main class="content">
    <h1>Payments</h1>

    <?php if ($error): ?>
      <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Payment Form -->
    <form class="payment-form" method="POST" action="">
      <label for="bill_id">Select Bill (Unpaid/Partial):</label>
      <select name="bill_id" id="bill_id" required>
        <option value="">-- Select a Bill --</option>
        <?php foreach ($unpaidBills as $bill): ?>
          <option value="<?= $bill['id'] ?>">
            <?= htmlspecialchars($bill['name']) ?> — Bill Amount: ₹<?= number_format($bill['amount'], 2) ?> — Balance: ₹<?= number_format($bill['balance'], 2) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="payment_amount">Payment Amount (₹):</label>
      <input type="number" step="0.01" min="0.01" name="payment_amount" id="payment_amount" required placeholder="Enter amount" />

      <label for="payment_date">Payment Date:</label>
      <input type="date" name="payment_date" id="payment_date" required value="<?= date('Y-m-d') ?>" />

      <button type="submit" name="make_payment">Make Payment</button>
    </form>

    <!-- Filter Form -->
    <form class="filters" method="GET" action="">
      <div>
        <label for="filter_customer">Filter by Customer:</label>
        <select name="filter_customer" id="filter_customer">
          <option value="0">All Customers</option>
          <?php foreach ($customers as $cust): ?>
            <option value="<?= $cust['id'] ?>" <?= $filter_customer === (int)$cust['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cust['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label for="filter_start">Start Date:</label>
        <input type="date" id="filter_start" name="filter_start" value="<?= htmlspecialchars($filter_start) ?>" />
      </div>

      <div>
        <label for="filter_end">End Date:</label>
        <input type="date" id="filter_end" name="filter_end" value="<?= htmlspecialchars($filter_end) ?>" />
      </div>

      <div>
        <button type="submit">Apply Filters</button>
      </div>

      <div>
        <button type="submit" name="download_csv" value="1" formmethod="GET" formaction="payments.php">
          Download CSV
        </button>
      </div>
    </form>

    <!-- Payments Table -->
    <table>
      <thead>
        <tr>
          <th>Payment ID</th>
          <th>Customer</th>
          <th>Bill Amount (₹)</th>
          <th>Payment Amount (₹)</th>
          <th>Payment Date</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$payments): ?>
          <tr><td colspan="5" style="text-align:center;">No payments found.</td></tr>
        <?php else: ?>
          <?php foreach ($payments as $payment): ?>
            <tr>
              <td><?= $payment['id'] ?></td>
              <td><?= htmlspecialchars($payment['customer_name']) ?></td>
              <td><?= number_format($payment['bill_amount'], 2) ?></td>
              <td><?= number_format($payment['payment_amount'], 2) ?></td>
              <td><?= htmlspecialchars($payment['payment_date']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <div class="total-payments">
      <strong>Total Payments Collected: ₹<?= number_format($totalPayments, 2) ?></strong>
    </div>
  </main>
</body>
</html>
