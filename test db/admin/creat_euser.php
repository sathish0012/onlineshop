<?php
session_start();
include "../db.php";

// Only admin can access
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Handle form submission
if(isset($_POST['submit'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if($password !== $confirm_password){
        $_SESSION['error'] = "Passwords do not match!";
    } else {
        // Hash password
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $sql = "INSERT INTO users (name, email, phone, role, password, created_at) 
                VALUES ('$name', '$email', '$phone', '$role', '$password_hashed', NOW())";
        if(mysqli_query($conn, $sql)){
            $_SESSION['success'] = "User created successfully!";
            header("Location: users.php");
            exit();
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create User</title>
<style>
body { 
    font-family: Arial, sans-serif; 
    background: #f3f3f3; 
    margin:0; 
    padding:0; 
}

.container { 
    max-width: 500px; 
    margin: 50px auto; 
    padding: 20px; 
    background:#fff; 
    border-radius:10px; 
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
}

h2 { 
    text-align:center; 
    color:#7b1fa2; 
    margin-bottom:20px; 
}

label { 
    display:block; 
    margin:10px 0 5px; 
    font-weight:bold; 
}

input, select { 
    width:100%; 
    padding:8px 10px; 
    margin-bottom:10px; 
    border-radius:5px; 
    border:1px solid #ccc; 
    box-sizing: border-box; /* Ensure proper width */
}

.button { 
    background:#ab47bc; 
    color:#fff; 
    border:none; 
    padding:10px 15px; 
    border-radius:5px; 
    cursor:pointer; 
    font-weight:bold; 
    text-decoration: none;
}

.button:hover { 
    background:#7b1fa2; 
}
.buttons { 
    margin-left: 100px;
    background:#ab47bc; 
    margin: 10px;
    color:#fff; 
    border:none; 
    padding:10px 100px; 
    border-radius:5px; 
    cursor:pointer; 
    font-size: 15px;
    font-weight:bold; 
    text-decoration: none;
}

.buttons:hover { 
    background:#7b1fa2; 
}

.message { 
    text-align:center; 
    font-weight:bold; 
    margin-bottom:15px; 
}

.message.success { color:green; }
.message.error { color:red; }

/* Password toggle wrapper */
.password-wrapper { 
    position: relative; 
    display: flex;
    align-items: center; /* Vertically center input and icon */
}

.password-wrapper input {
    flex: 1; /* Input takes full width except the icon */
    padding-right: 35px; /* Space for the eye icon */
}

.password-wrapper span { 
    position: absolute; 
    right:10px; 
    cursor:pointer; 
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    margin-bottom: 10px;
    margin-right: 10px;
}

</style>
</head>
<body>

<div class="container">
<h2>Create New User</h2>

<!-- Messages -->
<?php if(isset($_SESSION['success'])): ?>
<div class="message success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if(isset($_SESSION['error'])): ?>
<div class="message error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <label>Name</label>
    <input type="text" name="name" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Mobile</label>
    <input type="text" name="phone" required>

    <label>Role</label>
    <select name="role" required>
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select>

    <label>Password</label>
    <div class="password-wrapper">
        <input type="password" name="password" id="password" required>
        <span onclick="togglePassword('password', this)">
            <!-- Eye Open SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
                <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
                <circle cx="12" cy="12" r="2.5"/>
            </svg>
        </span>
    </div>

    <label>Confirm Password</label>
    <div class="password-wrapper">
        <input type="password" name="confirm_password" id="confirm_password" required>
        <span onclick="togglePassword('confirm_password', this)">
            <!-- Eye Open SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
                <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
                <circle cx="12" cy="12" r="2.5"/>
            </svg>
        </span>
    </div>

   <center> <button type="submit" name="submit" class="buttons">Create User</button></center>
</form>

<div style="text-align:center; margin-top:15px;">
    <a href="users.php" class="button">⬅ Back to Users</a>
</div>
</div>

<script>
function togglePassword(id, span){
    const input = document.getElementById(id);
    if(input.type === "password"){
        input.type = "text";
        // Change to closed eye
        span.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
            <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
            <line x1="1" y1="1" x2="23" y2="23" stroke="black" stroke-width="2"/>
        </svg>`;
    } else {
        input.type = "password";
        // Change to open eye
        span.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
            <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
            <circle cx="12" cy="12" r="2.5"/>
        </svg>`;
    }
}
</script>


</body>
</html>
