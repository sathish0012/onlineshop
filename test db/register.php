<?php
session_start();
include "db.php"; // Make sure this connects to your database

if(isset($_POST['submit'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $role = "user";

    // Prepare SQL statement
    $sql = "INSERT INTO users (name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if($stmt){
        mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $password, $phone, $address, $role);

        if(mysqli_stmt_execute($stmt)){
            echo "<script>
                    alert('Registered Successfully');
                    window.location.href='login.php'; // Redirect to login page
                  </script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error preparing statement: " . mysqli_error($conn) . "');</script>";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up</title>
<style>
/* Body and container styling */
body {
  background-color: #fce4ec;
  font-family: Arial, sans-serif;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  margin: 0;
}

form {
  background-color: #fff;
  padding: 40px;
  border-radius: 15px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  width: 380px;
  display: flex;
  flex-direction: column;
  gap: 15px;
  text-align: center;
}

h2.shop {
  color: #c2185b;
  font-size: 2.5rem;
  margin-bottom: 25px;
}

input[type="text"],
input[type="email"],
input[type="password"],
input[type="tel"],
textarea {
  padding: 12px 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  width: 100%;
  box-sizing: border-box;
  transition: border-color 0.3s;
}

input:focus,
textarea:focus {
  border-color: #f06292;
  outline: none;
}

textarea {
  resize: vertical;
  min-height: 80px;
}

input[type="submit"] {
  background-color: #f06292;
  color: white;
  padding: 12px;
  border: none;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s;
  text-transform: uppercase;
}

input[type="submit"]:hover {
  background-color: #c2185b;
}

/* Password wrapper */
.password-wrappers {
  position: relative;
  display: flex;
  align-items: center;
}

.password-wrappers input {
  flex: 1;
  padding-right: 40px;
}

.password-wrappers span {
  position: absolute;
  right: 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
}
</style>
</head>
<body>
<form action="register.php" method="post">
  <h2 class="shop">Shop</h2>
  <input type="text" name="name" placeholder="Enter Your Name" required>
  <input type="email" name="email" placeholder="Enter Your Email" required>
  <div class="password-wrappers">
    <input type="password" name="password" id="password" placeholder="Enter Your Password" required>
    <span onclick="togglePassword('password', this)">
      <!-- Eye Open SVG -->
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
        <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
        <circle cx="12" cy="12" r="2.5"/>
      </svg>
    </span>
  </div>
  <input type="text" name="phone" placeholder="Enter Your Mobile Number" required>
  <textarea name="address" placeholder="Enter Your Address"></textarea>
  <input type="submit" name="submit" value="Sign Up">
</form>

<script>
function togglePassword(id, span){
  const input = document.getElementById(id);
  if(input.type === "password"){
    input.type = "text";
    // Closed eye
    span.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="40">
        <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
        <line x1="1" y1="1" x2="23" y2="23" stroke="black" stroke-width="2"/>
    </svg>`;
  } else {
    input.type = "password";
    // Open eye
    span.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
        <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
        <circle cx="12" cy="12" r="2.5"/>
    </svg>`;
  }
}
</script>
</body>
</html>
