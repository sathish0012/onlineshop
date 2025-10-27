<?php
session_start();
include "../db.php";

// Only allow logged-in admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch existing settings
$settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings LIMIT 1"));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = mysqli_real_escape_string($conn, $_POST['site_name']);
    $admin_email = mysqli_real_escape_string($conn, $_POST['admin_email']);
    $admin_password = mysqli_real_escape_string($conn, $_POST['admin_password']);
    $currency = mysqli_real_escape_string($conn, $_POST['currency']);
    $tax = floatval($_POST['tax']);
    $shipping = floatval($_POST['shipping']);
    $maintenance = isset($_POST['maintenance']) ? 1 : 0;

    // Handle logo upload
    if(isset($_FILES['logo']) && $_FILES['logo']['size'] > 0){
        $target_dir = "../image/";
        $logo_name = time() . "_" . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], $target_dir.$logo_name);
    } else {
        $logo_name = $settings['logo']; // keep existing
    }

    mysqli_query($conn, "UPDATE settings SET 
        site_name='$site_name', 
        admin_email='$admin_email', 
        admin_password='$admin_password', 
        currency='$currency', 
        tax='$tax', 
        shipping='$shipping', 
        maintenance='$maintenance',
        logo='$logo_name'
        WHERE id=1");

    $success = "Settings updated successfully!";
    $settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings LIMIT 1"));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Settings</title>
<style>
body { margin:0; font-family: 'Segoe UI', sans-serif; background:#e8f0fe; color:#1c1c1c; }
.sidebar { width:220px; background:#1a237e; position:fixed; top:0; left:0; height:100vh; padding-top:55px; box-shadow:2px 0 8px rgba(0,0,0,0.15); }
.sidebar ul { list-style:none; padding:0; margin:0; }
.sidebar li { margin-bottom:10px; }
.sidebar a { display:block; padding:12px 25px; text-decoration:none; color:#e8eaf6; font-size:16px; border-radius:8px; transition:0.3s; }
.sidebar a.active, .sidebar a:hover { background:#3949ab; color:#fff; }
.toggle-btn { display:none; position:fixed; top:10px; left:10px; background:#3949ab; color:#fff; border:none; padding:10px 15px; border-radius:5px; cursor:pointer; z-index:1001; font-size:18px; }
.content { margin-left:240px; padding:20px; max-width:900px; }
.container { background:#fff; padding:30px; border-radius:10px; box-shadow:0 6px 15px rgba(0,0,0,0.1); }
h2 { text-align:center; color:#3949ab; margin-bottom:25px; }
form label { display:block; margin-top:15px; font-weight:bold; }
form input[type="text"], form input[type="email"], form input[type="password"], form input[type="number"], form select { width:100%; padding:10px; margin-top:5px; border-radius:5px; border:1px solid #ccc; }
form input[type="file"] { margin-top:5px; }
form button { margin-top:25px; width:100%; padding:12px; background:#3949ab; color:#fff; border:none; border-radius:6px; font-size:16px; cursor:pointer; transition:0.3s; }
form button:hover { background:#1a237e; }
.success { color:#43a047; margin-bottom:15px; text-align:center; font-weight:bold; }
@media(max-width:768px){ .toggle-btn{display:block;} .sidebar{transform:translateX(-100%);} .sidebar.show{transform:translateX(0);} .content{margin-left:0; padding:15px;} }
</style>
</head>
<body>

<button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<div class="sidebar" id="sidebar">
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="addproduct.php">Add Product</a></li>
        <li><a href="display.php">Update Products</a></li>
        <li><a href="vieworders.php">View Orders</a></li>
        <li><a href="users.php">Users</a></li>
        <li><a class="active" href="settings.php">Settings</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="content">
    <div class="container">
        <h2>⚙ Admin Settings</h2>
        <?php if(isset($success)): ?>
            <p class="success"><?= $success ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <label for="site_name">Site Name</label>
            <input type="text" name="site_name" id="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>

            <label for="logo">Site Logo</label>
            <?php if(!empty($settings['logo'])): ?>
                <img src="../image/<?= $settings['logo'] ?>" alt="Logo" style="height:50px;margin-bottom:10px;">
            <?php endif; ?>
            <input type="file" name="logo" id="logo" accept="image/*">

            <label for="admin_email">Admin Email</label>
            <input type="email" name="admin_email" id="admin_email" value="<?= htmlspecialchars($settings['admin_email'] ?? '') ?>" required>

            <label for="admin_password">Admin Password</label>
            <input type="password" name="admin_password" id="admin_password" value="<?= htmlspecialchars($settings['admin_password'] ?? '') ?>" required>

            <label for="currency">Default Currency</label>
            <select name="currency" id="currency" required>
                <option value="₹" <?= ($settings['currency']=='₹')?'selected':'' ?>>₹ INR</option>
                <option value="$" <?= ($settings['currency']=='$')?'selected':'' ?>>$ USD</option>
                <option value="€" <?= ($settings['currency']=='€')?'selected':'' ?>>€ EUR</option>
            </select>

            <label for="tax">Tax (%)</label>
            <input type="number" name="tax" id="tax" value="<?= $settings['tax'] ?? 0 ?>" step="0.01" required>

            <label for="shipping">Shipping Cost</label>
            <input type="number" name="shipping" id="shipping" value="<?= $settings['shipping'] ?? 0 ?>" step="0.01" required>

<label>
    <input type="checkbox" name="maintenance" 
        <?= (!empty($settings) && isset($settings['maintenance']) && $settings['maintenance']==1) ? 'checked' : '' ?>>
    Enable Maintenance Mode
</label>

            <button type="submit">Save Settings</button>
        </form>
    </div>
</div>

<script>
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('show');
}
</script>

</body>
</html>
