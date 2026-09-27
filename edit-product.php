<?php
session_start();
require_once 'includes/db.php';

// Redirect unauthenticated users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=please_login");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: my-listings.php");
    exit();
}

$product_id = intval($_GET['id']);

// Fetch product details and verify ownership
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Product not found or access denied.";
    exit();
}

$product = $result->fetch_assoc();

// Handle form update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $price       = floatval($_POST['price']);
    $category    = trim($_POST['category']);
    $description = trim($_POST['description']);
    $image_path  = $product['image_url']; // Default to existing image

    // Handle optional new image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp   = $_FILES['image']['tmp_name'];
        $file_name  = $_FILES['image']['name'];
        $file_ext   = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed    = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed)) {
            $new_file_name = time() . '_' . uniqid() . '.' . $file_ext;
            $target_path   = 'uploads/' . $new_file_name;

            if (move_uploaded_file($file_tmp, $target_path)) {
                // Delete old image file if it exists in uploads/ and isn't a default
                $old_file = $product['image_url'];
                if (!empty($old_file) && $old_file !== 'uploads/default.jpg' && file_exists($old_file)) {
                    @unlink($old_file);
                }
                $image_path = $target_path;
            } else {
                $error = "Failed to move uploaded image.";
            }
        } else {
            $error = "Invalid file type. Only JPG, JPEG, PNG, and WEBP are allowed.";
        }
    }

    if (empty($error)) {
        if (empty($title) || $price <= 0 || empty($category) || empty($description)) {
            $error = "Please fill in all required fields with valid values.";
        } else {
            $update_stmt = $conn->prepare("UPDATE products SET title = ?, price = ?, category = ?, description = ?, image_url = ? WHERE id = ? AND seller_id = ?");
            $update_stmt->bind_param("sdsssii", $title, $price, $category, $description, $image_path, $product_id, $user_id);

            if ($update_stmt->execute()) {
                $success = "Product updated successfully!";
                // Refresh product array values for display
                $product['title']       = $title;
                $product['price']       = $price;
                $product['category']    = $category;
                $product['description'] = $description;
                $product['image_url']   = $image_path;
            } else {
                $error = "Failed to update product. Please try again.";
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
    <title>Edit Listing - Uni-Biz Hub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <main style="max-width: 600px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px; border: 1px solid #ddd;">
        <h2>Edit Product Listing</h2>

        <?php if (!empty($error)): ?>
            <p style="color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p style="color: #155724; background: #d4edda; padding: 10px; border-radius: 4px;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form action="edit-product.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
            <div>
                <label style="display: block; margin-bottom: 5px;">Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px;">Price ($)</label>
                <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px;">Category</label>
                <select name="category" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="Clothing" <?php echo ($product['category'] === 'Clothing') ? 'selected' : ''; ?>>Clothing</option>
                    <option value="Electronics" <?php echo ($product['category'] === 'Electronics') ? 'selected' : ''; ?>>Electronics</option>
                    <option value="Books" <?php echo ($product['category'] === 'Books') ? 'selected' : ''; ?>>Books</option>
                    <option value="Food" <?php echo ($product['category'] === 'Food') ? 'selected' : ''; ?>>Food</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px;">Current Image</label>
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current Product Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px; display: block; margin-bottom: 8px;">
                
                <label style="display: block; margin-bottom: 5px;">Replace Image (Optional)</label>
                <input type="file" name="image" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px;">Description</label>
                <textarea name="description" rows="4" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Save Changes</button>
                <a href="my-listings.php" style="padding: 10px 20px; background: #6c757d; color: white; border-radius: 4px; text-decoration: none; text-align: center;">Cancel</a>
            </div>
        </form>
    </main>

</body>
</html>