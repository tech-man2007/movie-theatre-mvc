<?php
session_start();
require_once '../../../Model/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle Add Movie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_movie'])) {
    $title = $_POST['title'];
    $duration = $_POST['duration'];
    $language = $_POST['language'];
    $banner = $_POST['banner'] ?: 'https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=600&q=80';
    
    $stmt = $conn->prepare("INSERT INTO movies (title, duration, language, banner) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $title, $duration, $language, $banner);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

// Handle Actions (Promote, Demote, Delete User, Delete Movie)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($_GET['action'] === 'promote') {
        $conn->query("UPDATE users SET role = 'admin' WHERE id = $id");
    } elseif ($_GET['action'] === 'demote') {
        $conn->query("UPDATE users SET role = 'user' WHERE id = $id");
    } elseif ($_GET['action'] === 'delete_user') {
        // Clear child foreign key bookings first, then delete user
        $conn->query("DELETE FROM bookings WHERE user_id = $id");
        $conn->query("DELETE FROM users WHERE id = $id");
    } elseif ($_GET['action'] === 'delete_movie') {
        $conn->query("DELETE FROM bookings WHERE movie_id = $id");
        $conn->query("DELETE FROM movies WHERE id = $id");
    }
    header("Location: dashboard.php");
    exit();
}

$users = $conn->query("SELECT * FROM users");
$movies = $conn->query("SELECT * FROM movies");
$all_bookings = $conn->query("SELECT b.*, u.name as user_name, m.title as movie_title FROM bookings b JOIN users u ON b.user_id = u.id JOIN movies m ON b.movie_id = m.id ORDER BY b.booking_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - LCU Cinemas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Admin Management Panel</h2>
        <a href="../../../Controller/logout.php" class="btn btn-outline-danger">Logout</a>
    </div>

    <!-- Add Movie Section -->
    <div class="card bg-secondary text-white p-4 mb-4">
        <h4 class="text-warning mb-3">Add New Movie & Banner</h4>
        <form method="POST" class="row g-3">
            <input type="hidden" name="add_movie" value="1">
            <div class="col-md-3">
                <input type="text" name="title" class="form-control" placeholder="Movie Title" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="duration" class="form-control" placeholder="Duration (mins)" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="language" class="form-control" placeholder="Language" required>
            </div>
            <div class="col-md-3">
                <input type="url" name="banner" class="form-control" placeholder="Banner Image URL">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-warning w-100 fw-bold">Add Movie</button>
            </div>
        </form>
    </div>

    <!-- Manage Movies Section -->
    <h4 class="text-warning mb-3">Manage Movies</h4>
    <div class="table-responsive mb-5">
        <table class="table table-dark table-striped align-middle">
            <thead>
                <tr>
                    <th>Poster</th>
                    <th>Title</th>
                    <th>Language</th>
                    <th>Duration</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($m = $movies->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($m['banner']); ?>" alt="Poster" style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;"></td>
                    <td class="fw-bold"><?php echo htmlspecialchars($m['title']); ?></td>
                    <td><?php echo htmlspecialchars($m['language']); ?></td>
                    <td><?php echo htmlspecialchars($m['duration']); ?> mins</td>
                    <td>
                        <a href="dashboard.php?action=delete_movie&id=<?php echo $m['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete movie and its bookings?')">Delete Movie</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- View All System Bookings -->
    <h4 class="text-warning mb-3">All Customer Bookings</h4>
    <div class="table-responsive mb-5">
        <table class="table table-dark table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Movie</th>
                    <th>Seats</th>
                    <th>Show Date & Time</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($bk = $all_bookings->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $bk['id']; ?></td>
                    <td><?php echo htmlspecialchars($bk['user_name']); ?></td>
                    <td class="text-warning fw-bold"><?php echo htmlspecialchars($bk['movie_title']); ?></td>
                    <td><span class="badge bg-warning text-dark"><?php echo htmlspecialchars($bk['seats']); ?></span></td>
                    <td><?php echo htmlspecialchars($bk['show_date'] . ' ' . $bk['show_time']); ?></td>
                    <td>₹<?php echo htmlspecialchars($bk['total_price']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Manage Users Section -->
    <h4 class="text-warning mb-3">User & Role Management</h4>
    <div class="table-responsive mb-5">
        <table class="table table-dark table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><span class="badge bg-<?php echo $u['role'] === 'admin' ? 'danger' : 'primary'; ?>"><?php echo $u['role']; ?></span></td>
                    <td>
                        <?php if ($u['role'] === 'user'): ?>
                            <a href="dashboard.php?action=promote&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-success">Make Admin</a>
                        <?php else: ?>
                            <a href="dashboard.php?action=demote&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-warning">Make User</a>
                        <?php endif; ?>
                        <a href="dashboard.php?action=delete_user&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete user and their bookings?')">Delete User</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
