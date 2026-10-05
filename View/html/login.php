<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - LCU Cinemas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="bg-dark text-light d-flex align-items-center justify-content-center vh-100">
    <div class="card bg-secondary text-white p-4 shadow" style="width: 350px;">
        <h3 class="text-center text-warning mb-3">Login</h3>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger py-2" role="alert">
                <?php 
                if ($_GET['error'] === 'invalid_password') echo "Invalid password.";
                elseif ($_GET['error'] === 'user_not_found') echo "User does not exist.";
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'registered'): ?>
            <div class="alert alert-success py-2" role="alert">
                Registration successful! Please login.
            </div>
        <?php endif; ?>

        <form action="../../Controller/auth.php" method="POST">
            <input type="hidden" name="action" value="login">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-warning w-100 fw-bold">Login</button>
        </form>
        <div class="text-center mt-3">
            <small>Don't have an account? <a href="register.php" class="text-warning">Register</a></small>
        </div>
    </div>
</body>
</html>
