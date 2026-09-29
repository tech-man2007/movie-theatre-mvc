<?php
session_start();
require_once '../Model/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $movie_id = $_POST['movie_id'];
    $seats = $_POST['seats'];

    $seat_count = count(explode(',', $seats));
    $total_price = $seat_count * 200;

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, movie_id, seats, total_price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisd", $user_id, $movie_id, $seats, $total_price);

    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;
        header("Location: ../View/html/ticket.php?booking_id=" . $booking_id);
        exit();
    } else {
        echo "Failed to process booking.";
    }
}
?>
