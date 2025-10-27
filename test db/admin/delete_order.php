<?php
session_start();
include "../db.php";

if(!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin'){
    header("Location: ../index.php");
    exit();
}

if(isset($_GET['order_id'])){
    $order_id = $_GET['order_id'];

    // Delete order
    $sql = "DELETE FROM orders WHERE order_id='$order_id'";
    if(mysqli_query($conn, $sql)){
        header("Location: vieworders.php?msg=deleted");
        exit();
    } else {
        die("Error deleting order: " . mysqli_error($conn));
    }
} else {
    header("Location: vieworders.php");
    exit();
}
?>
