<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Customer Dashboard - Electricity Management System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: #f0f4f8;
      color: #333;
    }

    header {
      background-color: #007acc;
      padding: 20px;
      text-align: center;
      color: white;
      font-size: 1.5rem;
      font-weight: bold;
    }

    .container {
      display: flex;
    }

    nav {
      width: 250px;
      background-color: #004f8f;
      min-height: 100vh;
      padding-top: 20px;
    }

    nav ul {
      list-style-type: none;
    }

    nav ul li {
      margin: 15px 0;
    }

    nav ul li a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 12px 20px;
      border-radius: 4px;
      transition: background 0.3s;
    }

    nav ul li a:hover {
      background-color: #0066cc;
    }

    main {
      flex: 1;
      padding: 40px;
    }

    h2 {
      margin-bottom: 20px;
      color: #007acc;
    }

    .card {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <header>
    Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (Customer)
  </header>

  <div class="container">
    <nav>
      <ul>
        <li><a href="view_profile.php">View Profile</a></li>
        <li><a href="bills.php">Bills</a></li>
        <li><a href="transactions.php">Transactions</a></li>
        <li><a href="submit_complaint.php">Submit Complaint</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>

    <main>
      <h2>Customer Dashboard</h2>
      <div class="card">
        <p>Use the menu to navigate through your account features like checking your profile, viewing bills and transactions, and submitting complaints.</p>
      </div>
    </main>
  </div>
</body>
</html>
