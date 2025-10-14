<?php
session_start();
include "db.php"; // Make sure db.php defines $conn (MySQLi connection)

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ✅ Correct SQL syntax ("FROM" not "form")
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // ✅ Check if user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // ✅ Check password
        if ($row['password'] === $password) { // if you use password_hash() later, use password_verify()
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name']; 
            $_SESSION['user_role'] = $row['role'];
            echo "<script>alert('Login successful!'); window.location='index.php';</script>";
        } else {
            echo "<div class='error-message'>Incorrect password. Please try again.</div>";
        }
    } else {
        echo "<div class='error-message'>No account found with that email.</div>";
        
         echo "<div class='error-message'>Go to sign up </div>";
    
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
/* General Body and Container Styling */
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

/* Form Styling */
form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

/* Header Styling */
h2.shop {
  color: #c2185b;
  font-size: 2rem;
  margin-bottom: 20px;
}

/* Input Fields Styling */
input[type="email"],
input[type="password"] {
  padding: 12px 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  margin-bottom: 20px;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

input[type="email"]:focus,
input[type="password"]:focus {
  border-color: #f06292;
  outline: none;
}

/* Submit Button Styling */
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

/* Link Styling */
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

/* Error Message Styling */
.error-message {
  background-color: #f8d7da;
  color: #721c24;
  padding: 10px 20px;
  border: 1px solid #f5c6cb;
  border-radius: 8px;
  margin-bottom: 20px;
  width: 350px;
  text-align: center;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
</style>
</head>
<body>
<div class="login-container">
    <form action="login.php" method="post">
        <h2 class="shop">Shop</h2>
        <input type="email" name="email" placeholder="Enter Your Email" required />
        <input type="password" name="password" placeholder="Enter Your Password" required />
        <input type="submit" name="submit" value="Login">
        <p>Don't have an account? <a href="register.php">Sign Up</a></p>
        
        
    
    </form>
</div>
</body>
</html>
