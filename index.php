<?php
session_start();
require_once 'includes/db.php';

$search   = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

// Build dynamic query
$query  = "SELECT products.*, users.username FROM products JOIN users ON products.seller_id = users.id WHERE 1=1";
$params = [];
$types  = "";

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR description LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types   .= "ss";
}

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types   .= "s";
}

$query .= " ORDER BY created_at DESC";
$stmt   = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$products = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Uni-Biz Hub - Marketplace</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <main style="max-width: 1100px; margin: 20px auto; padding: 0 15px;">
        <!-- Search & Filter Form -->
        <form method="GET" action="index.php" style="display: flex; gap: 10px; margin-bottom: 25px;">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            <select name="category" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <option value="">All Categories</option>
                <option value="Clothing" <?php echo ($category === 'Clothing') ? 'selected' : ''; ?>>Clothing</option>
                <option value="Electronics" <?php echo ($category === 'Electronics') ? 'selected' : ''; ?>>Electronics</option>
                <option value="Books" <?php echo ($category === 'Books') ? 'selected' : ''; ?>>Books</option>
                <option value="Food" <?php echo ($category === 'Food') ? 'selected' : ''; ?>>Food</option>
            </select>
            <button type="submit" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Search</button>
        </form>

        <!-- Product Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
            <?php if ($products->num_rows > 0): ?>
                <?php while ($row = $products->fetch_assoc()): ?>
                    <div style="border: 1px solid #ddd; border-radius: 6px; overflow: hidden; background: #fff; padding: 10px;">
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" style="width: 100%; height: 160px; object-fit: cover; border-radius: 4px;">
                        <h3 style="margin: 10px 0 5px; font-size: 1.1rem;"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p style="color: #28a745; font-weight: bold; margin: 0 0 5px;">$<?php echo number_format($row['price'], 2); ?></p>
                        <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">Seller: <?php echo htmlspecialchars($row['username']); ?></p>
                        <a href="product-details.php?id=<?php echo $row['id']; ?>" style="display: block; text-align: center; padding: 8px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">View Details</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products found.</p>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>