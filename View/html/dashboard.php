<?php
session_start();
require_once '../Model/db.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$movies = $conn->query("SELECT * FROM movies");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - LCU Cinemas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="bg-dark text-light container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
        <div>
            <a href="my_bookings.php" class="btn btn-outline-warning me-2">My Bookings</a>
            <a href="../../Controller/logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>

    <h3 class="text-warning mb-4">Now Showing</h3>
    <div class="row">
        <?php while ($movie = $movies->fetch_assoc()): 
            // Handle banner path correctly whether it's a URL or local upload
            $banner = $movie['banner'];
            if (strpos($banner, 'http') !== 0) {
                // If stored relative, prepend '../' for user view
                $banner = '../' . ltrim($banner, './');
            }
        ?>
            <div class="col-md-4 mb-4">
                <div class="card bg-secondary text-white h-100 shadow">
                    <img src="<?php echo htmlspecialchars($banner); ?>" class="card-img-top" alt="Movie Banner" style="height: 350px; object-fit: cover;">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title text-warning fw-bold"><?php echo htmlspecialchars($movie['title']); ?></h5>
                            <p class="card-text text-light mb-1">Language: <?php echo htmlspecialchars($movie['language']); ?></p>
                            <p class="card-text text-muted">Duration: <?php echo htmlspecialchars($movie['duration']); ?> mins</p>
                        </div>
                        <a href="booking.php?movie_id=<?php echo $movie['id']; ?>" class="btn btn-warning fw-bold mt-3">Book Tickets</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
