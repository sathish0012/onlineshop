<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $product_price = $_POST['product_price'];
    $first_name = $_POST['first_name'];
    $mobile = $_POST['mobile'];
    $alt_mobile = $_POST['alt_mobile'];
    $email = $_POST['email'];
    $state = $_POST['state'];
    $house_no = $_POST['house_no'];
    $full_address = $_POST['full_address'];
    $landmark = $_POST['landmark'];
    $payment_method = $_POST['payment_method'];

    // Fetch product name
    $product_query = mysqli_query($conn, "SELECT name FROM products WHERE id='$product_id'");
    $product_row = mysqli_fetch_assoc($product_query);

    $_SESSION['order'] = [
        'user_id' => $_SESSION['user_id'],
        'product_id' => $product_id,
        'product_name' => $product_row['name'],
        'product_price' => $product_price,
        'first_name' => $first_name,
        'mobile' => $mobile,
        'alt_mobile' => $alt_mobile,
        'email' => $email,
        'state' => $state,
        'house_no' => $house_no,
        'full_address' => $full_address,
        'landmark' => $landmark,
        'payment_method' => $payment_method
    ];

    header("Location: payment_summary.php");
    exit();
}
?>
