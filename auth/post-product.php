<?php
session_start();
require_once '../includes/db.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seller_id   = $_SESSION['user_id'];
    $title       = trim($_POST['title']);
    $price       = trim($_POST['price']);
    $category    = trim($_POST['category']);
    $description = trim($_POST['description']);

    // Image Upload Handling
    $image_name   = $_FILES['image']['name'];
    $image_tmp    = $_FILES['image']['tmp_name'];
    $target_dir   = "../uploads/";
    
    $file_name    = time() . "_" . basename($image_name);
    $target_file  = $target_dir . $file_name;
    $db_image_url = "uploads/" . $file_name;

    if (move_uploaded_file($image_tmp, $target_file)) {
        $stmt = $conn->prepare("INSERT INTO products (seller_id, title, description, price, category, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdss", $seller_id, $title, $description, $price, $category, $db_image_url);

        if ($stmt->execute()) {
            header("Location: ../index.html?success=product_posted");
            exit();
        } else {
            echo "Error saving product to database.";
        }
        $stmt->close();
    } else {
        echo "Failed to upload image.";
    }
}
?>