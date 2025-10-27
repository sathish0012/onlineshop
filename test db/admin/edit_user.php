<?php
session_start();
include "../db.php"; // உன் DB connection இங்கே இருக்கணும் (mysqli $conn)

// Only admin can access
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Check if id provided
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = intval($_GET['id']);

// --- CONFIG ---
// If true, store password AS-IS (plain text). THIS IS INSECURE. Set to false to store hashed passwords.
$store_plain = true;
// --------------

// Fetch user safely using prepared statement
$stmt = $conn->prepare("SELECT id, name, email, phone, role, password FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    header("Location: users.php");
    exit();
}
$user = $res->fetch_assoc();
$stmt->close();

$error = '';

// Handle form submission
if (isset($_POST['submit'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // basic validation
    if ($name === '' || $email === '' || $phone === '' || $role === '') {
        $error = "Please fill in all required fields.";
    } else {
        if ($password !== '') {
            if ($store_plain) {
                // WARNING: storing plain text password is INSECURE
                $new_password_db = $password;
            } else {
                $new_password_db = password_hash($password, PASSWORD_DEFAULT);
            }

            $update_stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ?, password = ? WHERE id = ?");
            $update_stmt->bind_param("sssssi", $name, $email, $phone, $role, $new_password_db, $user_id);
        } else {
            // do not change password
            $update_stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ? WHERE id = ?");
            $update_stmt->bind_param("ssssi", $name, $email, $phone, $role, $user_id);
        }

        if ($update_stmt->execute()) {
            $update_stmt->close();
            header("Location: users.php");
            exit();
        } else {
            $error = "Update failed: " . htmlspecialchars($conn->error);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit User</title>
<style>
body { font-family: Arial, sans-serif; background: #f3f3f3; margin: 0; padding: 0; }
.container { max-width: 600px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
h2 { text-align: center; color: #7b1fa2; margin-bottom: 20px; }
form { display: flex; flex-direction: column; gap: 15px; }
label { font-weight: bold; }
input[type="text"], input[type="email"], input[type="password"], select { padding: 10px; border-radius: 5px; border: 1px solid #ccc; width: 100%; box-sizing: border-box; }
input[type="submit"], a.button { padding: 10px; background: #ab47bc; color: #fff; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; text-align: center; display: inline-block; }
input[type="submit"]:hover, a.button:hover { background: #7b1fa2; }
.note { font-size: 0.9rem; color: #555; }

/* Password wrapper for toggle eye */
.password-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.password-wrapper input {
    flex: 1;
    padding-right: 40px; /* space for eye icon */
}
.password-wrapper span {
    position: absolute;
    right: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
}
.error { color: red; font-size: 0.95rem; }
.warning { color: darkorange; font-size: 0.95rem; }
</style>
</head>
<body>

<div class="container">
<h2>Edit User</h2>

<?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($store_plain): ?>
<?php endif; ?>

<form method="post" action="">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>

    <label for="email">Email</label>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label for="phone">Phone</label>
    <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>

    <label for="role">Role</label>
    <select name="role" id="role" required>
        <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>

    <label for="password">Password <span class="note">(Leave blank to keep existing)</span></label>
    <div class="password-wrapper">
        <input type="password" name="password" id="password" placeholder="Enter new password">
        <span onclick="togglePassword('password', this)" title="Show / Hide password">
            <!-- Eye Open SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
                <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
                <circle cx="12" cy="12" r="2.5"/>
            </svg>
        </span>
    </div>

    <input type="submit" name="submit" value="Update User">
    <a href="users.php" class="button">Cancel</a>
</form>
</div>

<script>
function togglePassword(id, span){
    const input = document.getElementById(id);
    if(input.type === "password"){
        input.type = "text";
        // Change to closed eye (with strike)
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
