<?php
session_start();
include "db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>View Profile - Customer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f9;
    }
    .sidebar {
      height: 100vh;
      width: 250px;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #007acc;
      padding-top: 60px;
      color: white;
    }
    .sidebar h4 {
      text-align: center;
      margin-bottom: 20px;
    }
    .sidebar a {
      display: block;
      padding: 15px 25px;
      color: white;
      text-decoration: none;
      transition: background 0.3s;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #005f99;
    }
    .content {
      margin-left: 250px;
      padding: 30px;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .profile-header {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 20px;
      color: #007acc;
    }
    .profile-info {
      font-size: 1.1rem;
    }
    .profile-info div {
      padding: 10px 0;
      border-bottom: 1px solid #ddd;
    }
    .label {
      font-weight: 600;
      color: #333;
      width: 150px;
      display: inline-block;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h4>Customer Panel</h4>
    <a href="view_profile.php" class="active">👤 View Profile</a>
    <a href="bills.php">📜 Bills</a>
    <a href="transactions.php">💰 Transaction</a>
    <a href="submit_complaint.php">📝 Submit Complaints</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Main Content -->
  <div class="content">
    <div class="container">
      <div class="profile-header">Your Profile</div>
      <div class="card p-4 profile-info">
        <div><span class="label">Username:</span> <?= htmlspecialchars($profile['username']) ?></div>
        <div><span class="label">Email:</span> <?= isset($profile['email']) && $profile['email'] ? htmlspecialchars($profile['email']) : 'Not set' ?></div>
        <div><span class="label">Role:</span> <?= htmlspecialchars($profile['role']) ?></div>
      </div>
    </div>
  </div>

</body>
</html>