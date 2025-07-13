<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard - Electricity Management System</title>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
  body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    background: #f0f2f5;
  }
  .sidebar {
    position: fixed;
    height: 100vh;
    width: 250px;
    background: #007acc;
    color: #fff;
    display: flex;
    flex-direction: column;
    padding-top: 20px;
  }
  .sidebar h2 {
    text-align: center;
    margin-bottom: 30px;
    font-weight: 700;
  }
  .sidebar nav a {
    padding: 15px 25px;
    display: flex;
    align-items: center;
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.3s;
  }
  .sidebar nav a i {
    margin-right: 15px;
    min-width: 20px;
    text-align: center;
  }
  .sidebar nav a:hover,
  .sidebar nav a.active {
    background: #005fa3;
  }
  .content {
    margin-left: 250px;
    padding: 40px;
  }
  .header {
    font-size: 1.8rem;
    font-weight: 700;
    color: #007acc;
    margin-bottom: 40px;
  }
  .cards {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
    gap: 20px;
  }
  .card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgb(0 0 0 / 0.1);
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: transform 0.2s ease;
  }
  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgb(0 0 0 / 0.15);
  }
  .card i {
    font-size: 2.5rem;
    color: #007acc;
    margin-bottom: 15px;
  }
  .card h3 {
    font-weight: 600;
    font-size: 1.3rem;
    margin-bottom: 10px;
  }
  .logout {
    margin-top: 50px;
    text-align: center;
  }
  .logout a {
    display: inline-block;
    padding: 12px 40px;
    background: #ff4d4d;
    color: #fff;
    border-radius: 30px;
    font-weight: 700;
    text-decoration: none;
    transition: background 0.3s;
  }
  .logout a:hover {
    background: #cc0000;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .sidebar {
      width: 100%;
      height: auto;
      position: relative;
      flex-direction: row;
      padding: 0;
      overflow-x: auto;
    }
    .sidebar h2 {
      flex: 1;
      padding: 15px;
      margin-bottom: 0;
      font-size: 1.5rem;
    }
    .sidebar nav a {
      flex: none;
      padding: 15px 20px;
      font-size: 0.9rem;
      white-space: nowrap;
    }
    .content {
      margin-left: 0;
      padding: 20px;
    }
    .cards {
      grid-template-columns: 1fr 1fr;
    }
  }
  @media (max-width: 480px) {
    .cards {
      grid-template-columns: 1fr;
    }
  }
</style>
</head>
<body>

  <div class="sidebar">
    <h2>Admin Panel</h2>
    <nav>
      <a href="view_customers.php"><i class="fas fa-users"></i> View All Customers</a>
  
      <a href="view_bills.php"><i class="fas fa-file-invoice-dollar"></i> View All Bills</a>
      <a href="admin_generate_bill.php"><i class="fas fa-plus-circle"></i> Generate Bills</a>
      <a href="remove_users.php"><i class="fas fa-user-times"></i> Remove Users</a>
      <a href="pending_bills.php"><i class="fas fa-hourglass-half"></i> View Pending Bills</a>
      <a href="view_complaints.php"><i class="fas fa-envelope-open-text"></i> View All Complaints</a>
    </nav>
  </div>

  <div class="content">
    <div class="header">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</div>

    <div class="cards">
      <div class="card" onclick="location.href='view_customers.php';">
        <i class="fas fa-users"></i>
        <h3>View All Customers</h3>
        <p>See all registered customers in the system</p>
      </div>
      
      <div class="card" onclick="location.href='view_bills.php';">
        <i class="fas fa-file-invoice-dollar"></i>
        <h3>View All Bills</h3>
        <p>Review billing records and transactions</p>
      </div>
      <div class="card" onclick="location.href='admin_generate_bill.php';">
        <i class="fas fa-plus-circle"></i>
        <h3>Generate Bills</h3>
        <p>Create new billing records for customers</p>
      </div>
      <div class="card" onclick="location.href='remove_users.php';">
        <i class="fas fa-user-times"></i>
        <h3>Remove Users</h3>
        <p>Manage and delete user accounts</p>
      </div>
      <div class="card" onclick="location.href='pending_bills.php';">
        <i class="fas fa-hourglass-half"></i>
        <h3>Pending Bills</h3>
        <p>Check unpaid or overdue bills</p>
      </div>
      <div class="card" onclick="location.href='view_complaints.php';">
        <i class="fas fa-envelope-open-text"></i>
        <h3>View Complaints</h3>
        <p>View all customer complaints and feedback</p>
      </div>
    </div>

    <div class="logout">
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>

</body>
</html>
