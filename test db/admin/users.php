<?php
session_start();
include "../db.php";

// Only admin can access
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch all users
$users_res = mysqli_query($conn, "SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC");

// Protected email (won't be editable)
$protected_email = 'sathish19ucs3121@gmail.com';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Users List</title>
<style>
body {
  background-color: #e8f0fe; /* soft blue background */
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  margin: 0;
  color: #1c1c1c;
}

/* TOGGLE BUTTON */
.toggle-btn {
  display: none;
  position: fixed;
  top: 10px;
  left: 10px;
  background: #3949ab;
  color: #fff;
  border: none;
  padding: 10px 15px;
  border-radius: 5px;
  cursor: pointer;
  z-index: 1001;
  font-size: 18px;
}

/* SIDEBAR */
.sidebar {
  width: 220px;
  background-color: #1a237e; /* deep indigo */
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  padding-top: 55px;
  box-shadow: 2px 0 8px rgba(0,0,0,0.15);
  transition: transform 0.3s ease;
  z-index: 1000;
}
.sidebar ul { list-style: none; padding: 0; margin: 0; }
.sidebar li { margin-bottom: 10px; }
.sidebar a {
  display: block;
  padding: 12px 25px;
  text-decoration: none;
  color: #e8eaf6; /* light text */
  font-size: 16px;
  border-radius: 8px;
  transition: 0.3s;
}
.sidebar a.active, .sidebar a:hover { background-color: #3949ab; color: #fff; }

/* CONTAINER */
.container {
  margin-left: 240px;
  padding: 20px;
  transition: margin-left 0.3s ease;
}

/* HEADER */
.header {
  display:flex;
  justify-content: space-between;
  align-items:center;
  flex-wrap: wrap;
  margin-bottom:20px;
}
h2 { color:#1a237e; font-weight:600; margin:0; }
.new-user-btn a {
  background:#3949ab;
  color:#fff;
  text-decoration:none;
  padding:3px 8px;
  margin-right: 20px;
  border-radius:5px;
  font-weight:20px;
  transition:0.3s;
}
.new-user-btn a:hover { background:#1a237e; }

/* MESSAGES */
.message { text-align:center; font-weight:bold; margin-bottom:15px; }
.message.success { color:#43a047; } /* green */
.message.error { color:#f44336; }   /* red */

/* TABLE */
.table-container {
  overflow-x:auto;
  background:#fff;
  border-radius:10px;
  box-shadow:0 6px 15px rgba(0,0,0,0.1);
  padding:15px;
}
table { width:100%; border-collapse:collapse; }
th, td {
  padding:12px 10px;
  text-align:center;
  font-size:14px;
  border-bottom:1px solid #e0e0e0;
  vertical-align: middle;
  white-space: nowrap;
}
th { background:#3949ab; color:#fff; font-weight:600; }
tr:hover { background:#d1c4e9; } /* light indigo hover */

/* STATUS LABEL */
.status {
  padding:5px 10px;
  border-radius:5px;
  color:#fff;
  font-weight:bold;
  font-size:13px;
  display:inline-block;
}
.status.admin { background:#43a047; }
.status.user { background:#1a237e; }

/* BUTTONS */
.button {
  padding:6px 12px;
  border-radius:5px;
  text-decoration:none;
  font-weight:bold;
  transition:0.3s;
  display:inline-block;
  margin:2px;
  text-align:center;
}
.button.disabled { background:#ccc; color:#666; cursor:not-allowed; pointer-events:none; }
.button.edit { background:#3949ab; color:#fff; }
.button.edit:hover { background:#1a237e; }
.button.delete { background:#f44336; color:#fff; }
.button.delete:hover { background:#d32f2f; }

/* MOBILE RESPONSIVE */
@media(max-width:1024px){
  .container { margin-left:0; padding:20px; }
  .toggle-btn { display:block; }
  .sidebar { transform:translateX(-250px); }
  .sidebar.show { transform:translateX(0); }
  .header { flex-direction: column; align-items:flex-start; gap:10px; }
  .new-user-btn { width:100%; text-align:right; margin-top:-40px; }
  .all {margin-left: 100px;}
}
@media(max-width:768px){
  table, th, td { font-size:12px; padding:8px; }
}


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
    <li><a  class="active"href="users.php">Users</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
</div>

<div class="container">
<div class="header">
    <h2 class="all" >All Users</h2>
    <div class="new-user-btn">
        <a href="creat_euser.php">+ Create New User</a>
    </div>
</div>

<!-- Messages -->
<?php if(isset($_SESSION['success'])): ?>
    <div class="message success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if(isset($_SESSION['error'])): ?>
    <div class="message error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="table-container">
<?php if(mysqli_num_rows($users_res) > 0): ?>
<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Mobile</th>
    <th>Role</th>
    <th>Joined</th>
    <th>Actions</th>
</tr>
<?php while($user = mysqli_fetch_assoc($users_res)):
    $is_protected = (strtolower($user['email']) === strtolower($protected_email));
?>
<tr>
    <td><?= htmlspecialchars($user['id']) ?></td>
    <td><?= htmlspecialchars($user['name']) ?></td>
    <td><?= htmlspecialchars($user['email']) ?></td>
    <td><?= htmlspecialchars($user['phone']) ?></td>
    <td><span class="status <?= htmlspecialchars($user['role']) ?>"><?= ucfirst($user['role']) ?></span></td>
    <td><?= date('d-m-Y', strtotime($user['created_at'])) ?></td>
    <td>
        <?php if($is_protected): ?>
            <a class="button disabled" title="This user cannot be edited">Edit</a>
            <a class="button disabled" title="This user cannot be deleted">Delete</a>
        <?php else: ?>
            <a href="edit_user.php?id=<?= $user['id'] ?>" class="button edit">Edit</a>
            <a href="delete_user.php?id=<?= $user['id'] ?>" class="button delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p style="text-align:center;">No users found.</p>
<?php endif; ?>
</div>
</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
}
</script>

</body>
</html>
