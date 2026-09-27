<?php
session_start();
require_once 'db.php';

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$cat_filter   = isset($_GET['category']) ? intval($_GET['category']) : 0;

$sql = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
if (!empty($search_query)) {
    $sql .= " AND (p.title LIKE '%" . $conn->real_escape_string($search_query) . "%' OR p.description LIKE '%" . $conn->real_escape_string($search_query) . "%')";
}
if ($cat_filter > 0) {
    $sql .= " AND p.category_id = " . $cat_filter;
}
$sql .= " ORDER BY p.id DESC";
$products_res = $conn->query($sql);

$categories_res = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Uni-Biz Hub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-store me-2"></i>Uni-Biz Hub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#categories">Categories</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="post-product.php">Post Item</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-2" href="auth/logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="auth/login.php">My Account</a></li>
                    <li class="nav-item"><a class="btn btn-warning btn-sm text-dark font-weight-bold ms-2" href="auth/login.php">Login / Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container px-0">
    <div class="hero-section text-center">
        <h1 class="fw-bold mb-2">Welcome to Uni-Biz Hub</h1>
        <p class="mb-4">Discover, buy, and support student-owned businesses across campus!</p>
        <form action="index.php" method="GET" class="d-flex justify-content-center hero-search col-md-8 col-lg-6 mx-auto">
            <input type="text" name="search" class="form-control" placeholder="Search products, services, food..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="btn"><i class="fas fa-search me-1"></i> Search</button>
        </form>
    </div>
</div>

<div class="container my-4">
    <h3 class="fw-bold mb-3" style="color: var(--brown-primary);">Featured Products</h3>
    <div class="row g-4 mb-5">
        <?php if ($products_res && $products_res->num_rows > 0): ?>
            <?php while ($row = $products_res->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card custom-card h-100">
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" class="card-img-top product-img-top" alt="Product Image">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-light text-dark mb-2 align-self-start border"><?php echo htmlspecialchars($row['category_name']); ?></span>
                            <h5 class="card-title fw-bold fs-6 mb-1"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="price-tag mb-3">$<?php echo number_format($row['price'], 2); ?></p>
                            <a href="product-details.php?id=<?php echo $row['id']; ?>" class="btn btn-brown w-100 mt-auto">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12"><div class="alert alert-info text-center">No products found.</div></div>
        <?php endif; ?>
    </div>

    <h3 class="fw-bold mb-3" id="categories" style="color: var(--brown-primary);">Browse Categories</h3>
    <div class="row g-3 mb-5">
        <div class="col-6 col-md-2">
            <a href="index.php" class="category-card">
                <i class="fas fa-border-all"></i>
                All Items
            </a>
        </div>
        <?php 
        if ($categories_res && $categories_res->num_rows > 0):
            while ($cat = $categories_res->fetch_assoc()): 
        ?>
            <div class="col-6 col-md-2">
                <a href="index.php?category=<?php echo $cat['id']; ?>" class="category-card">
                    <i class="<?php echo htmlspecialchars($cat['icon_class']); ?>"></i>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            </div>
        <?php 
            endwhile;
        endif; 
        ?>
    </div>
</div>

<footer class="py-3 text-center">
    <div class="container">
        <small>&copy; <?php echo date('Y'); ?> Uni-Biz Hub. ICT 1209 Mini Project.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>