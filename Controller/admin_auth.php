<?php
session_start();
require_once '../Model/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

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
