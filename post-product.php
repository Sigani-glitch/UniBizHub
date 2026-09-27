<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$error_msg = "";
$success_msg = "";

// Fetch categories for dropdown
$categories_res = $conn->query("SELECT * FROM categories ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $category_id = intval($_POST['category_id']);
    $price       = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $seller_id   = $_SESSION['user_id'];
    
    // File upload logic
    $image_url = "uploads/default.jpg"; // Default fallback
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['product_image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['product_image']['name']);
        $target_dir = "uploads/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($file_tmp, $target_file)) {
            $image_url = $target_file;
        }
    }

    if (!empty($title) && $category_id > 0 && $price >= 0 && !empty($description)) {
        $stmt = $conn->prepare("INSERT INTO products (title, category_id, price, description, image_url, seller_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siddsi", $title, $category_id, $price, $description, $image_url, $seller_id);
        
        if ($stmt->execute()) {
            $success_msg = "Product published successfully!";
        } else {
            $error_msg = "Error publishing product. Please try again.";
        }
    } else {
        $error_msg = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post an Item - Uni-Biz Hub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-store me-2"></i>Uni-Biz Hub</a>
        <div class="ms-auto">
            <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to Home</a>
        </div>
    </div>
</nav>

<div class="container my-5 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom-card overflow-hidden">
                <!-- Theme Header -->
                <div class="p-4 text-center text-white" style="background-color: var(--brown-primary);">
                    <h3 class="fw-bold mb-1"><i class="fas fa-plus-circle me-2"></i>Post an Item for Sale</h3>
                    <p class="mb-0 small text-white-50">Fill out the details below to publish your advertisement on the campus marketplace.</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if (!empty($error_msg)): ?>
                        <div class="alert alert-danger py-2 small fw-bold mb-4"><?php echo htmlspecialchars($error_msg); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success_msg)): ?>
                        <div class="alert alert-success py-2 small fw-bold mb-4">
                            <?php echo htmlspecialchars($success_msg); ?>
                            <a href="index.php" class="alert-link ms-2">View Marketplace</a>
                        </div>
                    <?php endif; ?>

                    <form action="post-product.php" method="POST" enctype="multipart/form-data">
                        <!-- Product Title -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Product Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Engineering Textbook, Mechanical Keyboard, Homemade Brownies" required>
                        </div>

                        <!-- Category & Price -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="" selected disabled>Select Category</option>
                                    <?php 
                                    if ($categories_res && $categories_res->num_rows > 0):
                                        while ($cat = $categories_res->fetch_assoc()): 
                                    ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                    <?php 
                                        endwhile;
                                    endif; 
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Price ($) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" name="price" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>

                        <!-- Description & Contact Details -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description & Contact Info <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Describe your item condition, specifications, pickup location, AND include your contact info (WhatsApp, Instagram, Phone #, or Email)..." required></textarea>
                            <div class="form-text mt-1 text-muted small">
                                <i class="fas fa-info-circle me-1"></i> Be sure to include your preferred contact method (e.g., Phone, WhatsApp, Email) inside the description so buyers can reach you.
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Product Image</label>
                            <input type="file" name="product_image" class="form-control" accept="image/jpeg, image/png, image/webp">
                            <div class="form-text text-muted small">Supported formats: JPG, PNG, WEBP.</div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-brown w-100 py-2 fw-bold fs-6">
                            <i class="fas fa-paper-plane me-2"></i>Publish Product
                        </button>
                    </form>
                </div>
            </div>
        </div>
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