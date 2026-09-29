<?php
session_start();
require_once '../../Model/db.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: login.html");
    exit();
}

$movie_id = $_GET['movie_id'] ?? 1;
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Tickets - <?php echo htmlspecialchars($movie['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="bg-dark text-light container py-5">
    <h2 class="text-warning">Book Tickets for <?php echo htmlspecialchars($movie['title']); ?></h2>
    <p>Price per ticket: ₹200</p>

    <form action="../../Controller/booking.php" method="POST" class="mt-4 col-md-6">
        <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
        <div class="mb-3">
            <label class="form-label">Select Seats (comma separated, e.g. A1, A2):</label>
            <input type="text" name="seats" class="form-control" placeholder="A1, A2" required>
        </div>
        <button type="submit" class="btn btn-warning fw-bold">Confirm & Pay</button>
    </form>
</body>
</html>
