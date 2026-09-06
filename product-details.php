<?php
session_start();
require_once 'includes/db.php';

// URL-இல் product ID உள்ளதா எனச் சரிபார்த்தல்
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = intval($_GET['id']);

// பொருளின் விவரங்கள் மற்றும் விற்பனையாளரின் விவரங்களை எடுத்தல்
$stmt = $conn->prepare("
    SELECT products.*, users.username, users.email 
    FROM products 
    JOIN users ON products.seller_id = users.id 
    WHERE products.id = ?
");
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
                <li><a href="login.html">Account</a></li>
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
                <p style="color: #666; font-size: 0.9rem; margin-bottom: 10px;"><?php echo htmlspecialchars($product['category']); ?></p>
                <h3 style="color: #2e7d32; font-size: 1.5rem; margin-bottom: 15px;">$<?php echo number_format($product['price'], 2); ?></h3>
                
                <p><strong>Description:</strong></p>
                <p style="color: #444; margin-bottom: 20px;"><?php echo htmlspecialchars($product['description']); ?></p>
                
                <div style="background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #eee;">
                    <h4>Seller Information</h4>
                    <p><strong>Seller:</strong> <?php echo htmlspecialchars($product['username']); ?></p>
                    <p><strong>Contact:</strong> <a href="mailto:<?php echo htmlspecialchars($product['email']); ?>"><?php echo htmlspecialchars($product['email']); ?></a></p>
                </div>
            </div>
        </div>
    </main>

</body>
</html>