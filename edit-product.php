<?php
session_start();
require_once 'includes/db.php';

// Redirect unauthenticated users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html?error=please_login");
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
    $image_url   = trim($_POST['image_url']);

    if (empty($title) || $price <= 0 || empty($category) || empty($description)) {
        $error = "Please fill in all required fields with valid values.";
    } else {
        $update_stmt = $conn->prepare("UPDATE products SET title = ?, price = ?, category = ?, description = ?, image_url = ? WHERE id = ? AND seller_id = ?");
        $update_stmt->bind_param("sdsssii", $title, $price, $category, $description, $image_url, $product_id, $user_id);

        if ($update_stmt->execute()) {
            $success = "Product updated successfully!";
            // Refresh product array values
            $product['title'] = $title;
            $product['price'] = $price;
            $product['category'] = $category;
            $product['description'] = $description;
            $product['image_url'] = $image_url;
        } else {
            $error = "Failed to update product. Please try again.";
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

    <header>
        <nav class="navbar">
            <div class="logo">Uni-Biz Hub</div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="post-product.php">Sell Item</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="my-listings.php">My Listings</a></li>
                    <li><a href="auth/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.html">Login / Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main style="max-width: 600px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px; border: 1px solid #ddd;">
        <h2>Edit Product Listing</h2>

        <?php if (!empty($error)): ?>
            <p style="color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p style="color: #155724; background: #d4edda; padding: 10px; border-radius: 4px;"><?php echo $success; ?></p>
        <?php endif; ?>

        <form action="edit-product.php?id=<?php echo $product_id; ?>" method="POST" style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
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
                    <option value="Clothing" <?php echo ($product['category'] == 'Clothing') ? 'selected' : ''; ?>>Clothing</option>
                    <option value="Electronics" <?php echo ($product['category'] == 'Electronics') ? 'selected' : ''; ?>>Electronics</option>
                    <option value="Books" <?php echo ($product['category'] == 'Books') ? 'selected' : ''; ?>>Books</option>
                    <option value="Food" <?php echo ($product['category'] == 'Food') ? 'selected' : ''; ?>>Food</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px;">Image URL</label>
                <input type="text" name="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
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
