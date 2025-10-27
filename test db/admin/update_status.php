<?php
include "../db.php";

if(isset($_POST['order_id'], $_POST['status'])){
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $query = "UPDATE orders SET order_status='$status' WHERE order_id='$order_id'";
    echo mysqli_query($conn, $query) ? "success" : "error";
}
?>
