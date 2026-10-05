<?php
session_start();
require_once '../Model/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        
        try {
            if ($stmt->execute()) {
                header("Location: ../View/html/login.php?msg=registered");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                header("Location: ../View/html/register.php?error=email_exists");
                exit();
            } else {
                echo "Database Error: " . $e->getMessage();
            }
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
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                header("Location: ../View/html/dashboard.php");
                exit();
            } else {
                header("Location: ../View/html/login.php?error=invalid_password");
                exit();
            }
        } else {
            header("Location: ../View/html/login.php?error=user_not_found");
            exit();
        }
        $stmt->close();
    }
}
$conn->close();
?>
