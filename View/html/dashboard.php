<?php
session_start();
require_once '../../Model/db.php';

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_name'])) {
    header("Location: login.html");
    exit();
}

// Fetch all movies from the database
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
<body class="bg-dark text-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
            <a href="../../Controller/logout.php" class="btn btn-outline-danger">Logout</a>
        </div>

        <div class="row">
            <?php while($row = $movies->fetch_assoc()): ?>
            <div class="col-md-4 mb-3">
                <div class="card bg-secondary text-white shadow">
                    <div class="card-body">
                        <h5 class="card-title text-warning fw-bold"><?php echo $row['title']; ?></h5>
                        <p class="card-text">Duration: <?php echo $row['duration']; ?> mins</p>
                        <p class="card-text">Language: <?php echo $row['language']; ?></p>
                        <button class="btn btn-warning w-100 fw-bold">Book Tickets</button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
