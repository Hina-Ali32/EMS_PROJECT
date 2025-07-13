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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name && $address && $phone && $email) {
        $stmt = $pdo->prepare("INSERT INTO customers (name, address, phone, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $address, $phone, $email]);
        header("Location: customers.php"); 
        exit();
    } else {
        $error = "Please fill in all fields.";
    }
}

$stmt = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Customers - Electricity Management System</title>
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
/>
<style>
    /* Reset */
    * {
        box-sizing: border-box;
    }
    body, html {
        margin: 0; padding: 0;
        height: 100%;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f0f4f8;
        color: #1f2937;
        display: flex;
        min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
        width: 250px;
        background: #2563eb; /* blue */
        color: white;
        display: flex;
        flex-direction: column;
        padding-top: 40px;
        box-shadow: 2px 0 12px rgba(0,0,0,0.3);
        position: fixed;
        height: 100vh;
        z-index: 10;
    }
    .sidebar h2 {
        text-align: center;
        margin-bottom: 50px;
        font-size: 2rem;
        letter-spacing: 4px;
        font-weight: 900;
        user-select: none;
        color: #e0e7ff;
        text-shadow: 0 0 8px #93c5fd;
    }
    .nav-links {
        display: flex;
        flex-direction: column;
        gap: 15px;
        padding-left: 0;
        margin: 0;
        list-style: none;
    }
    .nav-links a {
        display: flex;
        align-items: center;
        gap: 18px;
        color: #dbeafe;
        padding: 16px 28px;
        font-weight: 600;
        font-size: 1.1rem;
        text-decoration: none;
        border-left: 5px solid transparent;
        transition: all 0.3s ease;
        user-select: none;
        border-radius: 0 8px 8px 0;
    }
    .nav-links a:hover,
    .nav-links a.active {
        background: #1e40af;
        color: white;
        border-left: 5px solid #3b82f6;
        box-shadow: 0 0 10px #3b82f6;
    }
    .nav-links a i {
        min-width: 24px;
        font-size: 1.4rem;
    }

    /* Main Content */
    .main-content {
    margin-left: 250px;
    padding: 50px 60px;
    flex: 1;
    overflow-y: auto;
    background: #f0f4f8;  /* matches the body background */
    border-radius: 0 20px 20px 0;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    min-height: 100vh;
}


    h2 {
        font-size: 3rem;
        font-weight: 900;
        color: #2563eb;
        margin-bottom: 45px;
        text-align: center;
        letter-spacing: 1.5px;
        user-select: none;
        text-shadow: 0 1px 3px rgba(37, 99, 235, 0.5);
    }

    /* Form */
    form {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 24px;
        margin-bottom: 50px;
    }
    form input {
        padding: 16px 20px;
        border-radius: 14px;
        border: 2px solid #94a3b8;
        font-size: 17px;
        width: 260px;
        outline: none;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        font-weight: 600;
        color: #1f2937;
        background-color: #f9fafb;
        box-shadow: inset 1px 1px 5px rgba(0,0,0,0.05);
    }
    form input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 12px #2563ebaa;
        background-color: #fff;
    }
    form button {
        padding: 16px 45px;
        border-radius: 18px;
        border: none;
        background: #2563eb;
        color: white;
        font-size: 19px;
        font-weight: 900;
        cursor: pointer;
        transition: background 0.35s ease, box-shadow 0.35s ease;
        box-shadow: 0 6px 15px rgba(37, 99, 235, 0.5);
    }
    form button:hover {
        background: #1e40af;
        box-shadow: 0 10px 25px rgba(30, 64, 175, 0.8);
    }

    .error {
        color: #dc2626;
        font-weight: 700;
        margin-bottom: 24px;
        text-align: center;
        font-size: 1.2rem;
    }

    /* Table */
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 17px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        border-radius: 16px;
        overflow: hidden;
        background: white;
        color: #1e293b;
        user-select: none;
    }
    thead {
        background: #2563eb;
        color: white;
    }
    th, td {
        padding: 20px 25px;
        text-align: left;
        border-bottom: 1.5px solid #e0e7ff;
        font-weight: 600;
    }
    tbody tr:nth-child(even) {
        background: #f9fafb;
    }
    tbody tr:hover {
        background: #dbeafe;
    }
    th {
        font-weight: 900;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .sidebar {
            width: 60px;
            padding-top: 20px;
            box-shadow: none;
        }
        .sidebar h2 {
            display: none;
        }
        .nav-links a {
            justify-content: center;
            padding: 18px 10px;
            font-size: 0;
            border-left: none;
            border-radius: 0;
        }
        .nav-links a i {
            font-size: 1.8rem;
        }
        .nav-links a span {
            display: none;
        }
        .nav-links a.active {
            background: #2563eb;
            border-radius: 12px;
            box-shadow: 0 0 10px #3b82f6;
        }
        .main-content {
            margin-left: 60px;
            padding: 30px 20px;
            border-radius: 0;
        }
        form input {
            width: 100%;
            max-width: none;
        }
        form {
            flex-direction: column;
            gap: 18px;
            margin-bottom: 30px;
        }
    }
</style>
</head>
<body>

<div class="sidebar">
    <h2>EMS</h2>
    <nav class="nav-links">
        <a href="dashboard.php" class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
        </a>
        <a href="customers.php" class="<?= $currentPage == 'customers.php' ? 'active' : '' ?>">
            <i class="fas fa-users"></i><span>Customers</span>
        </a>
        <a href="billing.php" class="<?= $currentPage == 'billing.php' ? 'active' : '' ?>">
            <i class="fas fa-file-invoice-dollar"></i><span>Billing</span>
        </a>
        <a href="payments.php" class="<?= $currentPage == 'payments.php' ? 'active' : '' ?>">
            <i class="fas fa-credit-card"></i><span>Payments</span>
        </a>
        <a href="complaints.php" class="<?= $currentPage == 'complaints.php' ? 'active' : '' ?>">
            <i class="fas fa-envelope-open-text"></i><span>Complaints</span>
        </a>
        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i><span>Logout</span>
        </a>
    </nav>
</div>

<div class="main-content">
    <h2>Customers</h2>

    <?php if (!empty($error)) : ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off" spellcheck="false" novalidate>
        <input type="text" name="name" placeholder="Full Name" required />
        <input type="text" name="address" placeholder="Address" required />
        <input type="tel" name="phone" placeholder="Phone Number" required pattern="[0-9+\-\s]{7,15}" title="Enter a valid phone number"/>
        <input type="email" name="email" placeholder="Email Address" required />
        <button type="submit">Add Customer</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Added On</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($customers): ?>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= htmlspecialchars($customer['name']) ?></td>
                        <td><?= htmlspecialchars($customer['address']) ?></td>
                        <td><?= htmlspecialchars($customer['phone']) ?></td>
                        <td><?= htmlspecialchars($customer['email']) ?></td>
                        <td><?= date('M d, Y', strtotime($customer['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; padding: 20px;">No customers found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
