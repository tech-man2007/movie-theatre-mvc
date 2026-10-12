<?php
session_start();
require_once '../../Model/db.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$movie_id = $_GET['movie_id'] ?? 1;

// Fetch movie details
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();

// Fetch already booked seats for this movie
$booked_stmt = $conn->prepare("SELECT seats FROM bookings WHERE movie_id = ?");
$booked_stmt->bind_param("i", $movie_id);
$booked_stmt->execute();
$booked_result = $booked_stmt->get_result();

$occupied_seats = [];
while ($row = $booked_result->fetch_assoc()) {
    $seats_array = explode(',', $row['seats']);
    foreach ($seats_array as $s) {
        $occupied_seats[] = trim($s);
    }
}

$rows = ['A', 'B', 'C', 'D'];
$seats_per_row = 6;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Seats - <?php echo htmlspecialchars($movie['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .seat-btn { width: 45px; height: 45px; margin: 4px; font-weight: bold; }
        .screen {
            background: #e0e0e0;
            height: 8px;
            width: 100%;
            border-radius: 4px;
            box-shadow: 0 3px 10px rgba(255,255,255,0.7);
        }
    </style>
</head>
<body class="bg-dark text-light container py-5">
    <div class="max-w-lg mx-auto">
        <h2 class="text-warning text-center"><?php echo htmlspecialchars($movie['title']); ?></h2>
        <p class="text-center text-muted">Ticket Price: ₹200 / seat</p>

        <!-- Screen Visual -->
        <div class="my-4 text-center">
            <div class="screen mb-2"></div>
            <small class="text-uppercase text-muted">Screen This Way</small>
        </div>

        <!-- Seat Grid -->
        <div class="text-center my-4">
            <?php foreach ($rows as $r): ?>
                <div class="d-flex justify-content-center align-items-center mb-1">
                    <span class="me-3 fw-bold text-warning" style="width: 20px;"><?php echo $r; ?></span>
                    <?php for ($i = 1; $i <= $seats_per_row; $i++): 
                        $seat_id = $r . $i;
                        $is_occupied = in_array($seat_id, $occupied_seats);
                    ?>
                        <?php if ($is_occupied): ?>
                            <button type="button" class="btn btn-secondary seat-btn" disabled><?php echo $i; ?></button>
                        <?php else: ?>
                            <button type="button" class="btn btn-outline-warning seat-btn" data-seat="<?php echo $seat_id; ?>" onclick="toggleSeat(this)"><?php echo $i; ?></button>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Booking Summary Form -->
        <form action="../../Controller/booking.php" method="POST" class="card bg-secondary text-white p-4 mt-4">
            <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
            <input type="hidden" name="seats" id="seats-input" required>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span>Selected Seats: <strong id="selected-seats-display" class="text-warning">None</strong></span>
                <span>Total: <strong class="text-warning fs-5">₹<span id="total-price-display">0</span></strong></span>
            </div>
            
            <button type="submit" id="submit-btn" class="btn btn-warning fw-bold w-100" disabled>Confirm & Pay</button>
        </form>
    </div>

    <script>
    let selectedSeats = [];
    const ticketPrice = 200;

    function toggleSeat(btn) {
        const seat = btn.getAttribute('data-seat');
        
        if (selectedSeats.includes(seat)) {
            selectedSeats = selectedSeats.filter(s => s !== seat);
            btn.classList.remove('btn-warning');
            btn.classList.add('btn-outline-warning');
        } else {
            selectedSeats.push(seat);
            btn.classList.remove('btn-outline-warning');
            btn.classList.add('btn-warning');
        }

        // Fixes the sticky UI highlight issue instantly
        btn.blur();

        // Update UI & Hidden Form Input
        document.getElementById('seats-input').value = selectedSeats.join(',');
        document.getElementById('selected-seats-display').innerText = selectedSeats.length > 0 ? selectedSeats.join(', ') : 'None';
        document.getElementById('total-price-display').innerText = selectedSeats.length * ticketPrice;
        document.getElementById('submit-btn').disabled = selectedSeats.length === 0;
    }
</script>
</body>
</html>
