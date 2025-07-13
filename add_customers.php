<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || $email === '') {
        header("Location: customers.php?error=Please fill in all fields");
        exit();
    }

    // Insert new customer
    try {
        $stmt = $pdo->prepare("INSERT INTO customers (name, email) VALUES (?, ?)");
        $stmt->execute([$name, $email]);
        header("Location: customers.php?success=Customer added successfully");
        exit();
    } catch (Exception $e) {
        header("Location: customers.php?error=Error adding customer");
        exit();
    }
} else {
    header("Location: customers.php");
    exit();
}
