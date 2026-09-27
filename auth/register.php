<?php
session_start();
require_once '../db.php';

$error_msg = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Check if username or email already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_res = $check_stmt->get_result();

        if ($check_res->num_rows > 0) {
            $error_msg = "Username or Email already exists!";
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $success_msg = "Account created successfully! You can now log in.";
            } else {
                $error_msg = "Registration failed. Please try again.";
            }
        }
    } else {
        $error_msg = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Uni-Biz Hub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="../index.php"><i class="fas fa-store me-2"></i>Uni-Biz Hub</a>
        <div class="ms-auto">
            <a href="login.php" class="btn btn-outline-light btn-sm"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
        </div>
    </div>
</nav>

<div class="container d-flex align-items-center justify-content-center flex-grow-1 my-5">
    <div class="card p-4 custom-card auth-container w-100" style="max-width: 420px;">
        <h3 class="text-center fw-bold mb-4" style="color: var(--brown-primary);">Create Account</h3>
        
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger text-center py-2 small fw-bold mb-3"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success text-center py-2 small fw-bold mb-3">
                <?php echo htmlspecialchars($success_msg); ?>
                <div class="mt-2"><a href="login.php" class="alert-link">Go to Login</a></div>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Enter email address" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Create password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-brown w-100 py-2 mb-3 fw-bold">Register</button>

            <div class="text-center small">
                <span class="text-muted">Already have an account?</span>
                <a href="login.php" class="fw-bold text-decoration-none ms-1" style="color: var(--brown-secondary);">Login here</a>
            </div>
        </form>
    </div>
</div>

<footer class="py-3 text-center mt-auto">
    <div class="container">
        <small>&copy; <?php echo date('Y'); ?> Uni-Biz Hub. All rights reserved.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>