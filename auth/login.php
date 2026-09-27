<?php
session_start();

// Database connection parameters
$host     = "localhost";
$db_user  = "root";
$db_pass  = "";
$db_name  = "unibizhub_db";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_input = trim($_POST['username']);
    $password_input = trim($_POST['password']);

    // Validate empty inputs
    if (empty($username_input) || empty($password_input)) {
        echo "Please fill in all fields. <a href='../login.html'>Try again</a>";
        exit;
    }

    // Fetch account details by username OR email using prepared statements
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username_input, $username_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Secure password authentication via standard Bcrypt hash verification
        if (password_verify($password_input, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];

            // Redirect user to dashboard
            header("Location: ../my-listings.php");
            exit;
        } else {
            echo "Incorrect password. <a href='../login.html'>Try again</a>";
        }
    } else {
        echo "User account not found. <a href='../login.html'>Try again</a>";
    }

    $stmt->close();
}

$conn->close();
?>