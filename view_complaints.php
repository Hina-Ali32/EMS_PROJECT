<?php
session_start();
include "db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle "Mark as Resolved" action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resolve_id'])) {
    $complaintId = intval($_POST['resolve_id']);
    $updateSql = "UPDATE complaints SET status = 'resolved' WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("i", $complaintId);
    $stmt->execute();
    $stmt->close();

    header("Location: view_complaints.php");
    exit();
}

// Fetch all complaints, newest first by id
$sql = "SELECT id, name, message, status FROM complaints ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Complaints - Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
<style>
  body, html {
    margin: 0; padding: 0;
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
    vertical-align: middle;
  }
  th {
    background-color: #007acc;
    color: white;
  }
  tr:hover {
    background-color: #e6f3ff;
  }
  .status-resolved {
    color: green;
    font-weight: 700;
  }
  .status-pending {
    color: orange;
    font-weight: 700;
  }
  .resolve-btn {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  .resolve-btn:hover {
    background-color: #218838;
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
    <a href="pending_bills.php">Pending Bills</a>
    <a href="view_complaints.php" class="active">Complaints</a>

    <form action="logout.php" method="post" class="logout-btn">
      <button type="submit" style="background:none; border:none; color:white; font-weight:700; width:100%; cursor:pointer;">Logout</button>
    </form>
  </nav>

  <main class="main-content">
    <h1>Customer Complaints</h1>

    <?php if ($result && $result->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>Complaint ID</th>
            <th>Customer Name</th>
            <th>Complaint</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($complaint = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($complaint['id']) ?></td>
              <td><?= htmlspecialchars($complaint['name']) ?></td>
              <td><?= htmlspecialchars($complaint['message']) ?></td>
              <td class="status-<?= strtolower(htmlspecialchars($complaint['status'])) ?>">
                <?= htmlspecialchars(ucfirst($complaint['status'])) ?>
              </td>
              <td>
                <?php if (strtolower($complaint['status']) !== 'resolved'): ?>
                  <form method="post" style="margin:0;">
                    <input type="hidden" name="resolve_id" value="<?= htmlspecialchars($complaint['id']) ?>" />
                    <button type="submit" class="resolve-btn" onclick="return confirm('Mark this complaint as resolved?');">Mark Resolved</button>
                  </form>
                <?php else: ?>
                  <span style="color: green; font-weight: 600;">Resolved</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No complaints found.</p>
    <?php endif; ?>
  </main>

</body>
</html>
