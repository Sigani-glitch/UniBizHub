<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check user credentials
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Simple plain-text match (or use password_verify if hashed)
        if ($password === $row['password']) { 
            $_SESSION['user_id'] = $row['id']; // Saves Person 1's ID (id = 1) to session
            header("Location: ../index.php");
            exit();
        }
    }
    
    header("Location: ../login.html?error=invalid_credentials");
    exit();
}
?>