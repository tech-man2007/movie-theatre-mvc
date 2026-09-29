<?php
session_start();
require_once '../../Model/db.php';

$booking_id = $_GET['booking_id'] ?? 0;
$stmt = $conn->prepare("SELECT b.*, m.title FROM bookings b JOIN movies m ON b.movie_id = m.id WHERE b.id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket Confirmation - LCU Cinemas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light container py-5">
    <div class="card bg-secondary text-white p-4 max-w-sm mx-auto shadow-lg">
        <h3 class="text-warning">LCU Cinemas - Booking Confirmed</h3>
        <hr>
        <p><strong>Movie:</strong> <?php echo htmlspecialchars($ticket['title']); ?></p>
        <p><strong>Seats:</strong> <?php echo htmlspecialchars($ticket['seats']); ?></p>
        <p><strong>Total Paid:</strong> ₹<?php echo htmlspecialchars($ticket['total_price']); ?></p>
        <p><strong>Booking ID:</strong> #LCU-<?php echo $ticket['id']; ?></p>
        <a href="dashboard.php" class="btn btn-warning mt-3 fw-bold">Back to Dashboard</a>
    </div>
</body>
</html>
