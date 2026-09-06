<?php
session_start();
require_once 'includes/db.php';

// Handle search and category filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND title LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
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
                <li><a href="post-product.php">Sell Item</a></li>
                <li><a href="my-listings.php">My Listings</a></li>
                <li><a href="login.html">Account</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1>Welcome to Uni-Biz Hub</h1>
            <p>Empowering university student entrepreneurs</p>
        </section>

        <!-- Search & Filter Bar -->
        <section class="search-filter-section" style="max-width: 800px; margin: 20px auto; padding: 0 20px;">
            <form action="index.php" method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                
                <select name="category" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="">All Categories</option>
                    <option value="Clothing" <?php echo ($category == 'Clothing') ? 'selected' : ''; ?>>Clothing</option>
                    <option value="Electronics" <?php echo ($category == 'Electronics') ? 'selected' : ''; ?>>Electronics</option>
                    <option value="Books" <?php echo ($category == 'Books') ? 'selected' : ''; ?>>Books</option>
                    <option value="Food" <?php echo ($category == 'Food') ? 'selected' : ''; ?>>Food</option>
                </select>
                
                <button type="submit" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Search</button>
            </form>
        </section>

        <!-- Product Listings -->
        <section class="products-section">
            <h2>Featured Products</h2>
            
            <div class="product-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="product-card">
                            <a href="product-details.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                                <p class="category"><?php echo htmlspecialchars($row['category']); ?></p>
                                <p class="price">$<?php echo number_format($row['price'], 2); ?></p>
                                <p class="description"><?php echo htmlspecialchars($row['description']); ?></p>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align: center; width: 100%;">No products found.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

</body>
</html>