<?php
session_start();
require_once '../../Model/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT b.*, m.title, m.language FROM bookings b JOIN movies m ON b.movie_id = m.id WHERE b.user_id = ? ORDER BY b.booking_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings - LCU Cinemas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="bg-dark text-light container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Booking History</h2>
        <a href="dashboard.php" class="btn btn-outline-warning">Back to Dashboard</a>
    </div>

    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-secondary text-center py-4" role="alert">
            <h4 class="text-warning">No bookings found!</h4>
            <p class="mb-3">You haven't booked any movie tickets yet.</p>
            <a href="dashboard.php" class="btn btn-warning fw-bold">Browse Movies</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Movie</th>
                        <th>Seats</th>
                        <th>Show Date & Time</th>
                        <th>Total Paid</th>
                        <th>Booked On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td class="fw-bold text-warning"><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><span class="badge bg-warning text-dark"><?php echo htmlspecialchars($row['seats']); ?></span></td>
                        <td><?php echo htmlspecialchars($row['show_date'] . ' ' . $row['show_time']); ?></td>
                        <td>₹<?php echo htmlspecialchars($row['total_price']); ?></td>
                        <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</body>
</html>
