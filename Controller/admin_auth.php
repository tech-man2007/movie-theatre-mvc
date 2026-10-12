<?php
session_start();
require_once '../Model/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Predefined Super Admin override
    if ($email === 'admin@lcinemas.com' && $password === 'admin123') {
        $_SESSION['admin_id'] = 999;
        $_SESSION['admin_name'] = 'Super Admin';
        header("Location: ../View/html/admin/dashboard.php");
        exit();
    }

    // Database check for other admins with role='admin'
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password']) && $row['role'] === 'admin') {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            header("Location: ../View/html/admin/dashboard.php");
            exit();
        }
    }
    
    header("Location: ../View/html/admin/login.php?error=unauthorized");
    exit();
}
?>
