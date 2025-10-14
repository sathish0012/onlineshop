<?php
// 1. Session-ஐ தொடங்கவும்
session_start();

// 2. அனைத்து session variables-ஐ அழிக்கவும்
$_SESSION = array();

// 3. cookie-இல் உள்ள session-ஐ அழிக்கவும் (உதாரணமாக, PHPSESSID)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Session-ஐ அழிக்கவும்
session_destroy();

// 5. பயனரை login பக்கத்திற்கு அனுப்பவும்
header("Location: login.php"); // login.php என்ற பக்கத்திற்கு அனுப்புகிறது
exit(); // Redirect செய்த பிறகு, ஸ்கிரிப்ட்டின் செயல்பாட்டை நிறுத்தவும்
?>
