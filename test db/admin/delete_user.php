<?php
session_start();
include "../db.php";

// Only admin can access
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = intval($_GET['id']);

// Fetch user info
$user_res = mysqli_query($conn, "SELECT email, role FROM users WHERE id='$user_id' LIMIT 1");
if (mysqli_num_rows($user_res) == 0) {
    header("Location: users.php");
    exit();
}

$user = mysqli_fetch_assoc($user_res);

// Protect special admin
$protected_admin_email = "sathish19ucs3121@gmail.com";

if ($user['role'] === 'admin' && $user['email'] === $protected_admin_email) {
    // Cannot delete this admin
    $_SESSION['error'] = "Cannot delete the super admin!";
    header("Location: users.php");
    exit();
}

// Delete user
mysqli_query($conn, "DELETE FROM users WHERE id='$user_id'");
$_SESSION['success'] = "User deleted successfully!";
header("Location: users.php");
exit();
