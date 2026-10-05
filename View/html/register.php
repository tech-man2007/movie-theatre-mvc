<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - LCU Cinemas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="bg-dark text-light d-flex align-items-center justify-content-center vh-100">
    <div class="card bg-secondary text-white p-4 shadow" style="width: 350px;">
        <h3 class="text-center text-warning mb-3">Register</h3>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'email_exists'): ?>
            <div class="alert alert-danger py-2" role="alert">
                This email is already registered.
            </div>
        <?php endif; ?>

        <form action="../../Controller/auth.php" method="POST">
            <input type="hidden" name="action" value="register">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-warning w-100 fw-bold">Register</button>
        </form>
        <div class="text-center mt-3">
            <small>Already have an account? <a href="login.php" class="text-warning">Login</a></small>
        </div>
    </div>
</body>
</html>
