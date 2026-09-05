<?php
session_start();
require_once '../includes/db.php';

// Block unauthenticated access
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html?error=please_login");
    exit();
}

$seller_id = $_SESSION['user_id']; // Dynamically pulls logged-in user ID

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);

    // File Upload
    $image = $_FILES['image'];
    $image_name = time() . '_' . basename($image['name']);
    $target_dir = "../uploads/";
    $target_file = $target_dir . $image_name;
    $db_image_path = "uploads/" . $image_name;

    if (move_uploaded_file($image['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO products (seller_id, title, category, price, description, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdss", $seller_id, $title, $category, $price, $description, $db_image_path);

        if ($stmt->execute()) {
            header("Location: ../index.php?success=product_posted");
            exit();
        } else {
            echo "Database error: " . $stmt->error;
        }
    } else {
        echo "Failed to upload image.";
    }
}
?>