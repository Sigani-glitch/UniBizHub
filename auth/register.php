<?php
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Securely hash the user password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "Registration successful! <a href='../login.html'>Login here</a>";
        } else {
            echo "Error: Username or Email already exists.";
        }
        $stmt->close();
    } else {
        echo "Please fill in all fields.";
    }
}
?>