<?php
session_start(); // Start the session

// Destroy all session data
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session

// Optional: Clear session cookies if necessary
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to the sign-in page after logging out
header("Location: signin.html");
exit();
?>
