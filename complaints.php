<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// DB connection (adjust your credentials)
$host = 'localhost';
$dbname = 'electricityms';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

$success = '';
$error = '';

// Handle complaint submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_complaint'])) {
        $customer_id = intval($_POST['customer_id'] ?? 0);
        $complaint_text = trim($_POST['complaint_text'] ?? '');

        if ($customer_id && $complaint_text !== '') {
            $stmt = $pdo->prepare("INSERT INTO complaints (customer_id, complaint_text, complaint_date, status) VALUES (?, ?, NOW(), 'Pending')");
            $stmt->execute([$customer_id, $complaint_text]);
            $success = "Complaint added successfully.";
        } else {
            $error = "Please select a customer and enter complaint details.";
        }
    }

    // Handle complaint resolve
    if (isset($_POST['resolve_complaint'])) {
        $complaint_id = intval($_POST['complaint_id'] ?? 0);
        if ($complaint_id) {
            $stmt = $pdo->prepare("UPDATE complaints SET status = 'Resolved' WHERE id = ?");
            $stmt->execute([$complaint_id]);
            $success = "Complaint marked as resolved.";
        }
    }

    // Handle complaint deletion
    if (isset($_POST['delete_complaint'])) {
        $complaint_id = intval($_POST['complaint_id'] ?? 0);
        if ($complaint_id) {
            $stmt = $pdo->prepare("DELETE FROM complaints WHERE id = ?");
            $stmt->execute([$complaint_id]);
            $success = "Complaint deleted successfully.";
        }
    }
}

// Fetch customers for dropdown (to add complaint)
$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch unprocessed complaints
$pendingComplaints = $pdo->query("SELECT complaints.id, customers.name, complaints.complaint_text, complaints.complaint_date, complaints.status 
    FROM complaints 
    JOIN customers ON complaints.customer_id = customers.id
    WHERE complaints.status = 'Pending'
    ORDER BY complaints.complaint_date DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all complaints
$allComplaints = $pdo->query("SELECT complaints.id, customers.name, complaints.complaint_text, complaints.complaint_date, complaints.status 
    FROM complaints 
    JOIN customers ON complaints.customer_id = customers.id
    ORDER BY complaints.complaint_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Complaints - Electricity Management System</title>
<style>
  /* Basic reset */
  * { box-sizing: border-box; }
  body, html { margin: 0; padding: 0; height: 100%; font-family: Arial, sans-serif; }
  /* Left Sidebar */
  .left-sidebar {
    width: 250px;
    background: #1e40af;
    color: #e0e7ff;
    height: 100vh;
    position: fixed;
    padding-top: 40px;
    display: flex;
    flex-direction: column;
    box-shadow: 2px 0 12px rgba(0,0,0,0.25);
  }
  .left-sidebar h2 {
    text-align: center;
    margin-bottom: 40px;
    font-size: 2rem;
  }
  .left-sidebar nav a {
    color: #dbeafe;
    padding: 15px 20px;
    text-decoration: none;
    display: block;
    font-weight: 600;
    transition: background 0.3s ease;
  }
  .left-sidebar nav a:hover,
  .left-sidebar nav a.active {
    background: #2563eb;
    color: white;
  }

  /* Main Content */
  .main-content {
    margin-left: 250px;
    padding: 40px;
    min-height: 100vh;
    background: #f0f4f8;
  }

  h1 {
    color: #1e40af;
    margin-bottom: 20px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 40px;
  }
  table, th, td {
    border: 1px solid #ccc;
  }
  th, td {
    padding: 12px;
    text-align: left;
  }
  th {
    background: #2563eb;
    color: white;
  }

  button {
    background: #2563eb;
    color: white;
    border: none;
    padding: 8px 14px;
    cursor: pointer;
    border-radius: 6px;
    font-weight: 600;
  }
  button:hover {
    background: #1e40af;
  }

  .success {
    color: green;
    margin-bottom: 20px;
  }
  .error {
    color: red;
    margin-bottom: 20px;
  }

  form.add-complaint {
    background: white;
    padding: 20px;
    margin-bottom: 40px;
    border-radius: 8px;
    box-shadow: 0 0 8px rgba(0,0,0,0.1);
  }
  form.add-complaint label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
  }
  form.add-complaint select, form.add-complaint textarea {
    width: 100%;
    padding: 8px;
    margin-bottom: 16px;
    border-radius: 6px;
    border: 1px solid #ccc;
    resize: vertical;
  }

  /* Tabs Styles */
  .tabs {
    margin-bottom: 20px;
  }
  .tab-btn {
    background: #2563eb;
    color: white;
    border: none;
    padding: 10px 16px;
    margin-right: 8px;
    cursor: pointer;
    border-radius: 6px 6px 0 0;
    font-weight: bold;
  }
  .tab-btn.active {
    background: #1e40af;
  }
  .tab-content {
    display: none;
  }
  .tab-content.active {
    display: block;
  }
</style>
</head>
<body>

<div class="left-sidebar">
  <h2>EMS</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="customers.php">Customers</a>
    <a href="billing.php">Billing</a>
    <a href="payments.php">Payments</a>
    <a href="complaints.php" class="active">Complaints</a>
    <a href="logout.php">Logout</a>
  </nav>
</div>

<div class="main-content">

  <h1>Add New Complaint</h1>

  <?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" class="add-complaint">
    <label for="customer_id">Customer:</label>
    <select name="customer_id" id="customer_id" required>
      <option value="">-- Select Customer --</option>
      <?php foreach ($customers as $customer): ?>
        <option value="<?= $customer['id'] ?>"><?= htmlspecialchars($customer['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <label for="complaint_text">Complaint Details:</label>
    <textarea name="complaint_text" id="complaint_text" rows="4" required></textarea>

    <button type="submit" name="add_complaint">Add Complaint</button>
  </form>

  <div class="tabs">
    <button class="tab-btn active" onclick="showTab('pending', event)">Unprocessed Complaints</button>
    <button class="tab-btn" onclick="showTab('all', event)">All Complaints</button>
  </div>

  <!-- Pending Complaints Tab -->
  <div id="pending" class="tab-content active">
    <h1>Unprocessed Complaints</h1>
    <?php if (count($pendingComplaints) === 0): ?>
      <p>No pending complaints.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Complaint</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pendingComplaints as $complaint): ?>
            <tr>
              <td><?= $complaint['id'] ?></td>
              <td><?= htmlspecialchars($complaint['name']) ?></td>
              <td><?= htmlspecialchars($complaint['complaint_text']) ?></td>
              <td><?= htmlspecialchars($complaint['complaint_date']) ?></td>
              <td>
                <form method="POST" style="display:inline-block" onsubmit="return confirm('Mark complaint as resolved?');">
                  <input type="hidden" name="complaint_id" value="<?= $complaint['id'] ?>">
                  <button type="submit" name="resolve_complaint">Resolve</button>
                </form>

                <form method="POST" style="display:inline-block" onsubmit="return confirm('Delete this complaint?');">
                  <input type="hidden" name="complaint_id" value="<?= $complaint['id'] ?>">
                  <button type="submit" name="delete_complaint" style="background:#dc2626;">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <!-- All Complaints Tab -->
  <div id="all" class="tab-content">
    <h1>All Complaints</h1>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Complaint</th>
          <th>Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($allComplaints as $complaint): ?>
          <tr>
            <td><?= $complaint['id'] ?></td>
            <td><?= htmlspecialchars($complaint['name']) ?></td>
            <td><?= htmlspecialchars($complaint['complaint_text']) ?></td>
            <td><?= htmlspecialchars($complaint['complaint_date']) ?></td>
            <td><?= htmlspecialchars($complaint['status']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>

<script>
function showTab(tabId, event) {
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));

  document.getElementById(tabId).classList.add('active');
  event.currentTarget.classList.add('active');
}
</script>

</body>
</html>
