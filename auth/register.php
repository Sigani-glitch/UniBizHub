<?php
session_start();
require_once '../includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username or Email already taken.";
        } else {
            // Hash password and save new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($insert_stmt->execute()) {
                $success = "Account created successfully! <a href='../login.html'>Login here</a>.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Uni-Biz Hub</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <main style="max-width: 400px; margin: 80px auto; padding: 20px; background: #fff; border-radius: 8px; border: 1px solid #ddd;">
        <h2>Create an Account</h2>

        <?php if (!empty($error)): ?>
            <p style="color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p style="color: #155724; background: #d4edda; padding: 10px; border-radius: 4px;"><?php echo $success; ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST" style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
            <div>
                <label style="display: block; margin-bottom: 5px;">Username</label>
                <input type="text" name="username" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px;">Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <button type="submit" style="padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Register</button>
        </form>

        <p style="margin-top: 15px; text-align: center; font-size: 0.9rem;">
            Already have an account? <a href="../login.html">Login here</a>
        </p>
    </main>

</body>
</html>