<?php
session_start();
require_once 'includes/db.php';

// Redirect unauthenticated users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html?error=please_login");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle product deletion request
if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    
    // Ensure the product belongs to the logged-in user before deleting
    $del_stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
    $del_stmt->bind_param("ii", $delete_id, $user_id);
    $del_stmt->execute();
    
    header("Location: my-listings.php?success=deleted");
    exit();
}

// Fetch products posted by this seller
$stmt = $conn->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings - Uni-Biz Hub</title>
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

    <main style="max-width: 900px; margin: 40px auto; padding: 0 20px;">
        <h2>My Posted Products</h2>

        <?php if ($result->num_rows > 0): ?>
            <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 20px;">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #ddd;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px;">
                            <div>
                                <h3 style="margin: 0; font-size: 1.1rem;"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <p style="margin: 5px 0 0; color: #2e7d32; font-weight: bold;">$<?php echo number_format($row['price'], 2); ?></p>
                            </div>
                        </div>
                        <div>
                            <form action="my-listings.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this listing?');">
                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" style="background: #dc3545; color: white; border: none; padding: 8px 14px; border-radius: 4px; cursor: pointer;">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p style="margin-top: 20px; color: #666;">You haven't listed any products yet. <a href="post-product.php">Post an item now</a>.</p>
        <?php endif; ?>
    </main>

</body>
</html>