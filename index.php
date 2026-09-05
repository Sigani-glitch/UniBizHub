<?php
require_once 'includes/db.php';

// Fetch all products sorted by newest first
$query = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uni-Biz Hub - Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <nav class="navbar">
            <div class="logo">Uni-Biz Hub</div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="post-product.html">Sell Item</a></li>
                <li><a href="login.html">Account</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1>Welcome to Uni-Biz Hub</h1>
            <p>Empowering university student entrepreneurs & campus commerce.</p>
        </section>

        <section class="products-section">
            <h2>Featured Products</h2>
            
            <div class="product-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="product-card">
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" style="max-width: 100%; height: auto;">
                            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                            <p class="category"><?php echo htmlspecialchars($row['category']); ?></p>
                            <p class="price">$<?php echo number_format($row['price'], 2); ?></p>
                            <p class="description"><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No products posted yet.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

</body>
</html>