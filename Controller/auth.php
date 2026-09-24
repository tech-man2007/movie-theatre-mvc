<?php
require_once '../Model/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        // Always hash passwords for security
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        
        if ($stmt->execute()) {
            echo "Registration successful! <a href='../View/html/login.html'>Login here</a>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        
    } elseif ($action === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                echo "Welcome to LCU Cinemas, " . htmlspecialchars($row['name']) . "!";
                // Session logic will go here later
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "User not found.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

