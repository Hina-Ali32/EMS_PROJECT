<?php
session_start();
include "db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

// Get logged-in user's id from users table
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'] ?? 0;

$customer_id = 0;
if ($user_id) {
    // Get customer id from customers table using user_id
    $stmt2 = $conn->prepare("SELECT id FROM customers WHERE user_id = ?");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $customer = $result2->fetch_assoc();
    $customer_id = $customer['id'] ?? 0;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $complaint_msg = trim($_POST['complaint'] ?? '');

    if ($name === '') {
        $error = "Please enter your name.";
    } elseif ($complaint_msg === '') {
        $error = "Please enter your complaint message.";
    } elseif ($customer_id == 0) {
        $error = "Invalid customer ID. Cannot submit complaint.";
    } else {
        $insertStmt = $conn->prepare("INSERT INTO complaints (customer_id, name, message, status) VALUES (?, ?, ?, 'pending')");
        $insertStmt->bind_param("iss", $customer_id, $name, $complaint_msg);
        if ($insertStmt->execute()) {
            $message = "Complaint submitted successfully.";
        } else {
            $error = "Error submitting complaint: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Submit Complaint - Customer</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f4f6f9;
    margin: 0;
    padding: 0;
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
  h2 {
    color: #007acc;
    font-weight: 600;
    margin-bottom: 20px;
  }
  form {
    background: white;
    padding: 25px;
    max-width: 700px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
  }
  label {
    font-weight: 600;
    color: #333;
  }
  input[type="text"], textarea {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 1.1rem;
    margin-top: 5px;
    margin-bottom: 15px;
    resize: vertical;
  }
  button {
    padding: 12px 25px;
    background: #007acc;
    border: none;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1.1rem;
    transition: background-color 0.3s;
  }
  button:hover {
    background-color: #005f99;
  }
  .message {
    margin-bottom: 20px;
    padding: 15px;
    border-radius: 8px;
    font-size: 1rem;
  }
  .error {
    background: #f8d7da;
    color: #842029;
    border: 1px solid #f5c2c7;
  }
  .success {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
  }
</style>
</head>
<body>

  <div class="sidebar">
    <h4>Customer Panel</h4>
    <a href="customer_dashboard.php">🏠 Dashboard</a>
    <a href="view_profile.php">👤 View Profile</a>
    <a href="bills.php">📜 Bills</a>
    <a href="transactions.php">💰 Transactions</a>
    <a href="submit_complaint.php" class="active">📝 Submit Complaint</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <div class="content">
    <h2>Submit Complaint</h2>

    <?php if ($error): ?>
      <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($message): ?>
      <div class="message success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" action="">
      <label for="name">Your Name</label>
      <input
        type="text"
        id="name"
        name="name"
        placeholder="Enter your full name"
        value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>"
        required
      />

      <label for="complaint">Your Complaint</label>
      <textarea
        id="complaint"
        name="complaint"
        placeholder="Describe your issue or complaint here..."
        required
      ><?= isset($_POST['complaint']) ? htmlspecialchars($_POST['complaint']) : '' ?></textarea>

      <button type="submit">Submit Complaint</button>
    </form>
  </div>

</body>
</html>
