<?php
session_start();
require_once '../../Model/db.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$movie_id = $_GET['movie_id'] ?? 1;
$current_date = date('Y-m-d');
$show_date = $_GET['show_date'] ?? $current_date;

// Available time slots
$all_slots = ['10:00 AM', '01:30 PM', '05:00 PM', '09:00 PM'];
$current_timestamp = time();

// Filter out past show times if selected date is today
$available_slots = [];
foreach ($all_slots as $slot) {
    if ($show_date > $current_date || strtotime($slot) > $current_timestamp) {
        $available_slots[] = $slot;
    }
}

// Default show time to the first available slot, or fallback if all passed
$default_slot = !empty($available_slots) ? $available_slots[0] : '';
$show_time = $_GET['show_time'] ?? $default_slot;

// Fetch movie details
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();

// Fetch already booked seats for this specific session
$occupied_seats = [];
if (!empty($show_time)) {
    $booked_stmt = $conn->prepare("SELECT seats FROM bookings WHERE movie_id = ? AND show_date = ? AND show_time = ?");
    $booked_stmt->bind_param("iss", $movie_id, $show_date, $show_time);
    $booked_stmt->execute();
    $booked_result = $booked_stmt->get_result();

    while ($row = $booked_result->fetch_assoc()) {
        $seats_array = explode(',', $row['seats']);
        foreach ($seats_array as $s) {
            $occupied_seats[] = trim($s);
        }
    }
}

$rows = ['A', 'B', 'C', 'D'];
$seats_per_row = 6;
$total_seats_count = count($rows) * $seats_per_row; // 24 seats
$is_sold_out = !empty($show_time) && (count($occupied_seats) >= $total_seats_count);
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
        /* Kill mobile touch sticky focus/active highlights */
        .seat-btn:focus, .seat-btn:active, .seat-btn:hover {
            box-shadow: none !important;
            outline: none !important;
        }
        .btn-outline-warning:active, .btn-outline-warning:focus, .btn-outline-warning:hover {
            background-color: transparent !important;
            color: #ffc107 !important;
            border-color: #ffc107 !important;
        }
        .btn-warning:active, .btn-warning:focus {
            background-color: #ffc107 !important;
            color: #000 !important;
        }
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-warning"><?php echo htmlspecialchars($movie['title']); ?></h2>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Back to Movies</a>
        </div>
        <p class="text-center text-muted">Ticket Price: ₹200 / seat</p>

        <!-- Session Selection Form -->
        <form method="GET" action="booking.php" class="card bg-secondary text-white p-3 mb-4">
            <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small">Show Date</label>
                    <input type="date" name="show_date" value="<?php echo $show_date; ?>" class="form-control bg-dark text-light border-secondary" required min="<?php echo $current_date; ?>" onchange="this.form.submit()">
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Show Time</label>
                    <select name="show_time" class="form-control bg-dark text-light border-secondary" required onchange="this.form.submit()">
                        <?php if (empty($available_slots)): ?>
                            <option value="">No shows available today</option>
                        <?php else: ?>
                            <?php foreach ($available_slots as $slot): ?>
                                <option value="<?php echo $slot; ?>" <?php echo $show_time === $slot ? 'selected' : ''; ?>><?php echo $slot; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </form>

        <?php if (empty($available_slots)): ?>
            <div class="alert alert-warning text-center py-4 shadow" role="alert">
                <h4 class="fw-bold">No More Shows Today</h4>
                <p class="mb-0">All show times for today have passed. Please select a future date above.</p>
            </div>
        <?php elseif ($is_sold_out): ?>
            <!-- Out of Stock Alert -->
            <div class="alert alert-danger text-center py-5 shadow" role="alert">
                <h3 class="text-danger fw-bold">OUT OF STOCK</h3>
                <p class="mb-0">All seats for this show on <strong><?php echo $show_date; ?></strong> at <strong><?php echo $show_time; ?></strong> are fully booked.</p>
            </div>
        <?php else: ?>
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
                <input type="hidden" name="show_date" value="<?php echo $show_date; ?>">
                <input type="hidden" name="show_time" value="<?php echo $show_time; ?>">
                <input type="hidden" name="seats" id="seats-input" required>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Selected Seats: <strong id="selected-seats-display" class="text-warning">None</strong></span>
                    <span>Total: <strong class="text-warning fs-5">₹<span id="total-price-display">0</span></strong></span>
                </div>

                <button type="submit" id="submit-btn" class="btn btn-warning fw-bold w-100" disabled>Confirm & Pay</button>
            </form>
        <?php endif; ?>
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

        // Instantly force focus loss to clear mobile touch highlights
        btn.blur();
        btn.blur(); // Double trigger for mobile Safari/Chrome touch renderers

        // Update UI & Hidden Form Input
        document.getElementById('seats-input').value = selectedSeats.join(',');
        document.getElementById('selected-seats-display').innerText = selectedSeats.length > 0 ? selectedSeats.join(', ') : 'None';
        document.getElementById('total-price-display').innerText = selectedSeats.length * ticketPrice;
        document.getElementById('submit-btn').disabled = selectedSeats.length === 0;
    }
    </script>
</body>
</html>
