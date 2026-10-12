<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - LCU Cinemas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light d-flex align-items-center justify-content-center vh-100">
    <div class="card bg-secondary text-white p-4 shadow" style="width: 380px;">
        <h3 class="text-center text-warning mb-3">Admin Portal</h3>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger py-2">Invalid admin credentials.</div>
        <?php endif; ?>
        <form action="../../../Controller/admin_auth.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Admin Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-warning w-100 fw-bold">Admin Login</button>
        </form>
    </div>
</body>
</html>
