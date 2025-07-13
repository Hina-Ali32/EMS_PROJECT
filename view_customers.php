<?php
session_start();
include "db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch customers from DB without meter numbers
$sql = "SELECT customers.* FROM customers ORDER BY customers.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>View Customers - Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
<style>
  /* Reset & base */
  body, html {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    height: 100vh;
    display: flex;
    background: #f0f2f5;
  }

  /* Sidebar styles */
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

  /* Main content styles */
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

  /* Logout button inside sidebar */
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
    <a href="view_customers.php" class="active">View Customers</a>
    <a href="view_bills.php">View Bills</a>
    <a href="admin_generate_bill.php">Generate Bills</a>
    <a href="remove_users.php">Remove Users</a>
    <a href="pending_bills.php">Pending Bills</a>
    <a href="view_complaints.php">Complaints</a>

    <form action="logout.php" method="post" class="logout-btn">
      <button type="submit" style="background:none; border:none; color:white; font-weight:700; width:100%; cursor:pointer;">Logout</button>
    </form>
  </nav>

  <main class="main-content">
    <h1>All Customers</h1>

    <?php if ($result && $result->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Customer Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No customers found.</p>
    <?php endif; ?>
  </main>

</body>
</html>
