<?php
session_start();
require_once '../../Model/db.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$movie_id =$_GET['movie_id'] ?? 1;

// Fetch movie details
$stmt =$conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);$stmt->execute();
$movie =$stmt->get_result()->fetch_assoc();

// Fetch already booked seats for this movie
$booked_stmt =$conn->prepare("SELECT seats FROM bookings WHERE movie_id = ?");
$booked_stmt->bind_param("i", $movie_id);$booked_stmt->execute();
$booked_result =$booked_stmt->get_result();

$occupied_seats = [];
while ($row =$booked_result->fetch_assoc()) {
    $seats_array = explode(',',$row['seats']);
    foreach ($seats_array as$s) {
        $occupied_seats[] = trim($s);
    }
}

// Define Tiered Rows with individual prices and color themes
$row_tiers = [     'A' => ['price' => 150, 'theme' => 'info', 'name' => 'Standard (₹150)'],     'B' => ['price' => 200, 'theme' => 'warning', 'name' => 'Executive (₹200)'],     'C' => ['price' => 300, 'theme' => 'success', 'name' => 'Premium (₹300)'],     'D' => ['price' => 400, 'theme' => 'danger', 'name' => 'VIP Recliner (₹400)'] ];$seats_per_row = 6;
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
        .seat-btn:focus, .seat-btn:active, .seat-btn:hover {
            box-shadow: none !important;
            outline: none !important;
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
        <h2 class="text-warning text-center"><?php echo htmlspecialchars($movie['title']); ?></h2>
        
        <!-- Pricing Legend -->
        <div class="row text-center my-3 g-2">
            <?php foreach ($row_tiers as $r_key =>$tier): ?>
                <div class="col-6 col-md-3">
                    <span class="badge bg-<?php echo $tier['theme']; ?> text-dark w-100 py-2">
                        Row <?php echo $r_key; ?>: ₹<?php echo$tier['price']; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Screen Visual -->
        <div class="my-4 text-center">
            <div class="screen mb-2"></div>
            <small class="text-uppercase text-muted">Screen This Way</small>
        </div>

        <!-- Seat Grid with Tiered Colors -->
        <div class="text-center my-4">
            <?php foreach ($row_tiers as $r =>$tier): ?>
                <div class="d-flex justify-content-center align-items-center mb-2">
                    <span class="me-3 fw-bold text-<?php echo $tier['theme']; ?>" style="width: 25px;"><?php echo $r; ?></span>
                    <?php for ($i = 1; $i <=$seats_per_row; $i++):$seat_id = $r .$i;
                        $is_occupied = in_array($seat_id,$occupied_seats);
                    ?>
                        <?php if ($is_occupied): ?>
                            <button type="button" class="btn btn-secondary seat-btn" disabled><?php echo $i; ?></button>
                        <?php else: ?>
                            <button type="button" 
                                    class="btn btn-outline-<?php echo $tier['theme']; ?> seat-btn" 
                                    data-seat="<?php echo $seat_id; ?>" 
                                    data-row="<?php echo $r; ?>"
                                    onclick="toggleSeat(this)">
                                <?php echo $i; ?>
                            </button>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Booking Summary Form -->
        <form action="../../Controller/booking.php" method="POST" class="card bg-secondary text-white p-4 mt-4">
            <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
            <input type="hidden" name="seats" id="seats-input" required>

            <!-- Show Date & Time Selection -->
            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <label class="form-label">Show Date</label>
                    <input type="date" name="show_date" class="form-control bg-dark text-light border-secondary" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Show Time</label>
                    <select name="show_time" class="form-control bg-dark text-light border-secondary" required>
                        <option value="">Select Time</option>
                        <option value="10:00 AM">10:00 AM</option>
                        <option value="01:30 PM">01:30 PM</option>
                        <option value="05:00 PM">05:00 PM</option>
                        <option value="09:00 PM">09:00 PM</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <span>Selected Seats: <strong id="selected-seats-display" class="text-warning">None</strong></span>
                <span>Total: <strong class="text-warning fs-5">₹<span id="total-price-display">0</span></strong></span>
            </div>

            <button type="submit" id="submit-btn" class="btn btn-warning fw-bold w-100" disabled>Confirm & Pay</button>
        </form>
    </div>

    <script>
    // Row pricing mapping in JavaScript
    const rowPrices = {
        'A': 150,
        'B': 200,
        'C': 300,
        'D': 400
    };

    let selectedSeats = [];

    function toggleSeat(btn) {
        const seat = btn.getAttribute('data-seat');
        const row = btn.getAttribute('data-row');

        if (selectedSeats.includes(seat)) {
            selectedSeats = selectedSeats.filter(s => s !== seat);
            btn.classList.remove(`btn-${getRowTheme(row)}`);
            btn.classList.add(`btn-outline-${getRowTheme(row)}`);
        } else {
            selectedSeats.push(seat);
            btn.classList.remove(`btn-outline-${getRowTheme(row)}`);
            btn.classList.add(`btn-${getRowTheme(row)}`);
        }

        // Instantly force focus loss to clear highlights
        btn.blur();
        btn.blur();

        // Calculate dynamic total price based on individual seat row prices
        let totalPrice = 0;
        selectedSeats.forEach(s => {
            const r = s.charAt(0);
            totalPrice += rowPrices[r] || 200;
        });

        // Update UI & Hidden Form Input
        document.getElementById('seats-input').value = selectedSeats.join(',');
        document.getElementById('selected-seats-display').innerText = selectedSeats.length > 0 ? selectedSeats.join(', ') : 'None';
        document.getElementById('total-price-display').innerText = totalPrice;
        document.getElementById('submit-btn').disabled = selectedSeats.length === 0;
    }

    function getRowTheme(row) {
        const themes = { 'A': 'info', 'B': 'warning', 'C': 'success', 'D': 'danger' };
        return themes[row] || 'warning';
    }
    </script>
</body>
</html>
