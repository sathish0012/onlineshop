<?php
session_start();
include "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $user_id = $_SESSION['user_id'];

    // Update the order status and set cancellation date
    $cancel_date = date('Y-m-d H:i:s'); // current timestamp
    $sql = "UPDATE orders 
            SET payment_status = 'Cancelled', cancel_date = ? 
            WHERE order_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $cancel_date, $order_id, $user_id);
    if ($stmt->execute()) {
        $_SESSION['msg'] = "Order #$order_id has been cancelled successfully.";
    } else {
        $_SESSION['msg'] = "Failed to cancel order. Please try again.";
    }

    header("Location: myorders.php");
    exit();
} else {
    header("Location: myorders.php");
    exit();
}
