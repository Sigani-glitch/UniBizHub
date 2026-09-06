<?php
session_start();
require_once 'includes/db.php';

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = intval($_GET['id']);

// Query product details with seller info
$stmt = $conn->prepare("SELECT products.*, users.username, users.email FROM products JOIN users ON products.seller_id = users.id WHERE products.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Product not found.";
    exit();
}

$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['title']); ?> - Uni-Biz Hub</title>
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

    <main class="details-container" style="max-width: 800px; margin: 40px auto; padding: 0 20px;">
        <a href="index.php" style="text-decoration: none; color: #007bff;">&larr; Back to Products</a>
        
        <div style="display: flex; gap: 30px; margin-top: 20px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <div style="flex: 1;">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" style="width: 100%; border-radius: 8px;">
            </div>
            <div style="flex: 1;">
                <h2><?php echo htmlspecialchars($product['title']); ?></h2>
                <p style="color: #666; font-size: 0.9rem;"><?php echo htmlspecialchars($product['category']); ?></p>
                <h3 style="color: #2e7d32; margin: 15px 0;">$<?php echo number_format($product['price'], 2); ?></h3>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                
                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                
                <h4>Seller Info</h4>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($product['username']); ?></p>
                <p><strong>Contact Email:</strong> <a href="mailto:<?php echo htmlspecialchars($product['email']); ?>"><?php echo htmlspecialchars($product['email']); ?></a></p>
            </div>
        </div>
    </main>

</body>
</html>