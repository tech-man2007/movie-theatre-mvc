<?php
session_start();
require_once '../Model/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../View/html/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $movie_id = $_POST['movie_id'];
    $seats = $_POST['seats'];
    $show_date = $_POST['show_date'];
    $show_time = $_POST['show_time'];

    // Calculate total price (₹200 per seat)
    $seat_array = explode(',', $seats);
    $total_price = count($seat_array) * 200;

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, movie_id, seats, show_date, show_time, total_price, booking_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iisssi", $user_id, $movie_id, $seats, $show_date, $show_time, $total_price);

    if ($stmt->execute()) {
        header("Location: ../View/html/my_bookings.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
