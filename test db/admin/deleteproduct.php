<?php
session_start();
include "../db.php";

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == "admin") {
        if (isset($_GET['product_id'])) {
            $product_id =$_GET['product_id']; // secure casting
            $sql = "DELETE FROM products WHERE id = $product_id";
            $result = mysqli_query($conn, $sql);

            if (!$result) {
                echo "Error!: {$conn->error}";
            } else {
                echo "Deleted successfully!<a href='diplay.php>Go Back</a>";
            }
        } else {
            echo "No product selected to delete!";
        }
    } else {
        echo "Go for user dashboard";
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
