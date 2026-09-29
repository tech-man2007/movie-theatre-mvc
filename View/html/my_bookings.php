<?php
session_start();
require_once '../../Model/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT b.*, m.title FROM bookings b JOIN movies m ON b.movie_id = m.id WHERE b.user_id = ? ORDER BY b.booking_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$history = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings - LCU Cinemas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Booking History</h2>
        <a href="dashboard.php" class="btn btn-outline-warning">Dashboard</a>
    </div>
    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Movie</th>
                <th>Seats</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $history->fetch_assoc()): ?>
            <tr>
                <td>#LCU-<?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['seats']); ?></td>
                <td>₹<?php echo $row['total_price']; ?></td>
                <td><?php echo $row['booking_date']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
