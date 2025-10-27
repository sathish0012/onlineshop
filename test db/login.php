<?php
session_start();
include "db.php"; // Make sure db.php defines $conn (MySQLi connection)

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Simple password check (consider hashing for security)
        if ($row['password'] === $password) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name']; 
            $_SESSION['user_role'] = $row['role'];

            // Redirect based on role
            if ($row['role'] === 'admin') {
                echo "<script>
                        alert('Admin login successful!');
                        window.location='admin/dashboard.php';
                      </script>";
            } else {
                echo "<script>
                        alert('Login successful!');
                        window.location='index.php';
                      </script>";
            }
        } else {
            echo "<script>alert('Incorrect password. Please try again.');</script>";
        }
    } else {
        echo "<script>
                alert('No account found with that email. Please sign up.');
                window.location='register.php';
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<style>
body {
  background-color: #fce4ec;
  font-family: Arial, sans-serif;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  margin: 0;
  flex-direction: column;
}

.login-container {
  background-color: #fff;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 350px;
  text-align: center;
}

form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

h2.shop {
  color: #c2185b;
  font-size: 2rem;
  margin-bottom: 20px;
}

input[type="email"],
input[type="password"],
input[type="text"] {
  padding: 12px 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  height: 45px;           /* ✅ fixed consistent height */
  transition: border-color 0.3s ease;
  box-sizing: border-box;
  width: 100%;
}


input[type="email"]:focus,
input[type="password"]:focus {
  border-color: #f06292;
  outline: none;
}

input[type="submit"] {
  background-color: #f06292;
  color: #fff;
  padding: 12px;
  border: none;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

input[type="submit"]:hover {
  background-color: #c2185b;
}

p {
  margin-top: 15px;
  color: #555;
}

a {
  color: #f06292;
  text-decoration: none;
  font-weight: bold;
}

a:hover {
  text-decoration: underline;
}

.show-password {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 8px;
  font-size: 0.9rem;
  color: #555;
}
</style>
</head>
<body>

<div class="login-container">
    <form action="login.php" method="post">
        <h2 class="shop">Shop</h2>
        <input type="email" name="email" placeholder="Enter Your Email" required />
        <input type="password" name="password" id="password" placeholder="Enter Your Password" required />

        <label class="show-password">
            <input type="checkbox" onclick="togglePassword()"> Show Password
        </label>

        <input type="submit" name="submit" value="Login">
        <p>Don't have an account? <a href="register.php">Sign Up</a></p>
    </form>
</div>

<script>
function togglePassword() {
  const pass = document.getElementById("password");
  pass.type = pass.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
