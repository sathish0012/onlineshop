<?php
include "db.php";

if(isset($_POST['submit'])){
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $phone = $_POST['phone'];
  $address = $_POST['address'];
  $role = "user"; // The extra single quote was removed.

  // Using a prepared statement for security 🛡️
  $sql = "INSERT INTO users(name, email, password, phone, address, role) VALUES(?, ?, ?, ?, ?, ?)";
  
  $stmt = mysqli_prepare($conn, $sql);

  if ($stmt) {
    // Bind parameters to the prepared statement
    mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $password, $phone, $address, $role);
    
    // Execute the statement
    if(mysqli_stmt_execute($stmt)){
      echo "<div class='register-success'>Registered Successfully</div>";

    } else {
      echo "Error! : " . mysqli_error($conn);
    }
    
    // Close the statement
    mysqli_stmt_close($stmt);
  } else {
    echo "Error preparing statement: " . mysqli_error($conn);
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up</title>
    <style>
      /* Body and container styling */
body {
  background-color: #fce4ec; /* Light pink background */
  font-family: Arial, sans-serif;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  margin: 0;
}

/* Form container */
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

/* Header styling */
h2.shop {
  color: #c2185b; /* Dark pink color */
  font-size: 2.5rem;
  margin-bottom: 25px;
}

/* Input fields and textarea styling */
input[type="text"],
input[type="email"],
input[type="password"],
input[type="tel"], /* Added for better mobile number handling */
textarea {
  padding: 12px 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  width: 100%;
  box-sizing: border-box; /* Ensures padding doesn't affect total width */
  transition: border-color 0.3s;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
input[type="tel"]:focus,
textarea:focus {
  border-color: #f06292; /* Focus color */
  outline: none;
}

textarea {
  resize: vertical; /* Allows vertical resizing */
  min-height: 80px;
}

/* Submit button styling */
input[type="submit"] {
  background-color: #f06292; /* Pink color */
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
  background-color: #c2185b; /* Darker pink on hover */
}

.register-success {
  background-color: #d4edda; /* Light green */
  color: #155724; /* Dark green text */
  padding: 10px 20px;
  border: 1px solid #c3e6cb;
  border-radius: 8px;
  margin-top: 20px;
  width: 100%; /* Fit inside container */
  text-align: center;
  font-weight: bold;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

    </style>
  </head>
  <body>
   <form action="register.php" method="post">
    <h2 class="shop">shop</h2>
  <input type="text" name="name" placeholder="Enter Your Name" required />
  <input type="email" name="email" placeholder="Enter Your Email" required />
  <input type="password" name="password" placeholder="Enter Your Password" required />
  <input type="text" name="phone" placeholder="Enter Your Mobile Number" required />
  <textarea name="address" placeholder="Enter Your Address"></textarea>
  <input type="submit" name="submit" value="Sign Up">
</form>

  </body>
</html>
