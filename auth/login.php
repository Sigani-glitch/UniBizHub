<?php
session_start();
require_once '../db.php';

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_input = trim($_POST['email']);
    $password    = trim($_POST['password']);
    $role        = isset($_POST['role']) ? $_POST['role'] : 'buyer';

    if (!empty($login_input) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $login_input, $login_input);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows == 1) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $role;
                header("Location: ../index.php");
                exit();
            } else {
                $error_msg = "Invalid password.";
            }
        } else {
            $error_msg = "Account not found.";
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
    <title>Login - Uni-Biz Hub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="../index.php"><i class="fas fa-store me-2"></i>Uni-Biz Hub</a>
    </div>
</nav>

<div class="container d-flex align-items-center flex-grow-1">
    <div class="card p-4 custom-card auth-container w-100">
        <h3 class="text-center fw-bold mb-4" style="color: var(--brown-primary);">Login</h3>
        
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger text-center py-2 small fw-bold mb-3"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="d-flex justify-content-center mb-4 gap-2">
                <input type="radio" class="btn-check" name="role" id="buyerRole" value="buyer" checked>
                <label class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold" for="buyerRole">Buyer Login</label>

                <input type="radio" class="btn-check" name="role" id="sellerRole" value="seller">
                <label class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold" for="sellerRole">Seller Login</label>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email or Username</label>
                <input type="text" name="email" class="form-control" placeholder="Enter email or username" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn btn-brown w-100 py-2 mb-3">Login</button>

            <div class="d-flex justify-content-between small">
                <a href="#" class="text-muted text-decoration-none">Forgot Password?</a>
                <a href="register.php" class="fw-bold text-decoration-none" style="color: var(--brown-secondary);">Create an Account</a>
            </div>
        </form>
    </div>
</div>

<footer class="py-3 text-center">
    <div class="container">
        <small>&copy; <?php echo date('Y'); ?> Uni-Biz Hub. All rights reserved.</small>
    </div>
</footer>

</body>
</html>