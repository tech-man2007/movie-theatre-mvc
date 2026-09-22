<?php
// Catch form submissions from the Views
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Database verification logic will go here tomorrow
        echo "Login attempt received for: " . htmlspecialchars($email);
        
    } elseif ($action === 'register') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Database insert logic will go here tomorrow
        echo "Registration attempt received for: " . htmlspecialchars($name);
    }
} else {
    echo "Invalid request method.";
}
?>
