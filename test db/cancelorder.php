<?php
session_start();
include "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    // Safely get the order ID
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);

    // Check if the order exists and belongs to this user
    $check_sql = "SELECT product_id, payment_status FROM orders WHERE order_id='$order_id' AND user_id='$user_id' LIMIT 1";
    $res = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($res) > 0) {
        $order = mysqli_fetch_assoc($res);
        $current_status = strtolower($order['payment_status']);

        // Allow cancellation only if the order is pending or success
        if ($current_status === 'pending' || $current_status === 'success') {
            $cancel_date = date('Y-m-d H:i:s'); // Current date and time

            // Cancel the order and store cancellation time
            $update_sql = "UPDATE orders 
                           SET payment_status='Cancelled', cancel_date='$cancel_date' 
                           WHERE order_id='$order_id' AND user_id='$user_id'";
            if (mysqli_query($conn, $update_sql)) {
                
                // Optional: Restore product stock
                $product_id = $order['product_id'];
                mysqli_query($conn, "UPDATE products SET stock = stock + 1 WHERE id='$product_id'");

                $_SESSION['msg'] = "Order #$order_id has been successfully cancelled on $cancel_date.";
            } else {
                $_SESSION['msg'] = "Error cancelling the order: " . mysqli_error($conn);
            }
        } else {
            $_SESSION['msg'] = "This order cannot be cancelled. Current status: " . ucfirst($order['payment_status']);
        }
    } else {
        $_SESSION['msg'] = "Order not found.";
    }

    // Redirect back to myorders.php
    header("Location: myorders.php");
    exit();
} else {
    // Prevent direct access
    header("Location: myorders.php");
    exit();
}
?>
