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
    $stmt = $conn->prepare("INSERT INTO movies (title, duration, language) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $title, $duration, $language);
    $stmt->execute();
}

// Handle Role Change / Delete User
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($_GET['action'] === 'promote') {
        $conn->query("UPDATE users SET role = 'admin' WHERE id = $id");
    } elseif ($_GET['action'] === 'demote') {
        $conn->query("UPDATE users SET role = 'user' WHERE id = $id");
    } elseif ($_GET['action'] === 'delete_user') {
        $conn->query("DELETE FROM users WHERE id = $id");
    }
    header("Location: dashboard.php");
    exit();
}

$users = $conn->query("SELECT * FROM users");
$movies = $conn->query("SELECT * FROM movies");
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
        <h4>Add New Movie</h4>
        <form method="POST" class="row g-3 mt-1">
            <input type="hidden" name="add_movie" value="1">
            <div class="col-md-4">
                <input type="text" name="title" class="form-control" placeholder="Movie Title" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="duration" class="form-control" placeholder="Duration (mins)" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="language" class="form-control" placeholder="Language" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-warning w-100 fw-bold">Add Movie</button>
            </div>
        </form>
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
                        <a href="dashboard.php?action=delete_user&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete user?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
