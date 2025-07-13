<?php
session_start();
include "db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Get customer id
$sql = "SELECT u.email, c.id FROM users u LEFT JOIN customers c ON u.email = c.email WHERE u.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !$user['id']) {
    die("Customer not found.");
}
$customer_id = $user['id'];

// Get bill_id from URL
if (!isset($_GET['bill_id']) || !is_numeric($_GET['bill_id'])) {
    die("Invalid bill ID.");
}
$bill_id = (int)$_GET['bill_id'];

// Check if bill belongs to customer and is unpaid or generated
$sql = "SELECT amount, status FROM bills WHERE id = ? AND customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bill_id, $customer_id);
$stmt->execute();
$billResult = $stmt->get_result();

if ($billResult->num_rows == 0) {
    die("Bill not found or does not belong to you.");
}

$bill = $billResult->fetch_assoc();
if ($bill['status'] === 'paid') {
    die("This bill is already paid.");
}

// Begin transaction to ensure data consistency
$conn->begin_transaction();

try {
    // Update bill status to paid
    $updateSql = "UPDATE bills SET status = 'paid' WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("i", $bill_id);
    $updateStmt->execute();

    if ($updateStmt->affected_rows === 0) {
        throw new Exception("Failed to update bill status.");
    }

    // Insert into transactions table
    $insertSql = "INSERT INTO transactions (customer_id, transaction_date, amount, payment_method, status, bill_id) VALUES (?, NOW(), ?, ?, 'completed', ?)";
    $paymentMethod = 'online'; // Example payment method; modify as needed
    $amount = $bill['amount'];

    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("idsi", $customer_id, $amount, $paymentMethod, $bill_id);
    $insertStmt->execute();

    if ($insertStmt->affected_rows === 0) {
        throw new Exception("Failed to record transaction.");
    }

    $conn->commit();

    // Redirect with success message
    header("Location: bills.php?msg=Payment successful");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Error processing payment: " . $e->getMessage());
}
?>
