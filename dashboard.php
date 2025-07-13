<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard - Electricity Management System</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .dashboard {
            padding: 50px 30px;
            border-radius: 20px;
            text-align: center;
            width: 480px;
            /* subtle background for container */
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        }

        h1 {
            margin-bottom: 50px;
            font-size: 2.8rem;
            font-weight: 700;
            text-shadow: 0 2px 6px rgba(0,0,0,0.5);
        }

        .nav-button {
            display: block;
            margin: 20px 0;
            padding: 30px 0;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 18px;
            font-size: 22px;
            font-weight: 700;
            color: white;
            text-decoration: none;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            user-select: none;
        }

        .nav-button:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: translateY(-7px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        /* Icon spacing */
        .nav-button span.icon {
            font-size: 28px;
            margin-right: 15px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['username']); ?></h1>
        <a class="nav-button" href="customers.php"><span class="icon">👥</span> Customers</a>
        <a class="nav-button" href="billing.php"><span class="icon">💡</span> Billing</a>
        <a class="nav-button" href="payments.php"><span class="icon">💳</span> Payments</a>
        <a class="nav-button" href="complaints.php"><span class="icon">📩</span> Complaints</a>
        <a class="nav-button" href="logout.php"><span class="icon">🚪</span> Logout</a>
    </div>
</body>
</html>
