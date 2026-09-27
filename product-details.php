<?php
session_start();
require_once 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $conn->prepare("SELECT p.*, c.name AS category_name, u.username FROM products p JOIN categories c ON p.category_id = c.id JOIN users u ON p.seller_id = u.id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $reviewer = trim($_POST['reviewer_name']);
    $comment  = trim($_POST['comment']);
    if (!empty($reviewer) && !empty($comment)) {
        $rev_stmt = $conn->prepare("INSERT INTO reviews (product_id, reviewer_name, comment) VALUES (?, ?, ?)");
        $rev_stmt->bind_param("iss", $id, $reviewer, $comment);
        $rev_stmt->execute();
        header("Location: product-details.php?id=" . $id);
        exit();
    }
}

$reviews_res = $conn->query("SELECT * FROM reviews WHERE product_id = $id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['title']); ?> - Uni-Biz Hub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-store me-2"></i>Uni-Biz Hub</a>
        <div class="ms-auto">
            <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to Home</a>
        </div>
    </div>
</nav>

<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['category_name']); ?></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['title']); ?></li>
        </ol>
    </nav>

    <div class="row g-4 card p-4 custom-card flex-row mb-4">
        <div class="col-md-5 text-center">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="img-fluid rounded border" style="max-height: 380px; width: 100%; object-fit: cover;" alt="Product">
        </div>

        <div class="col-md-7 d-flex flex-column justify-content-center">
            <span class="badge bg-light text-dark align-self-start border mb-2"><?php echo htmlspecialchars($product['category_name']); ?></span>
            <h2 class="fw-bold mb-2"><?php echo htmlspecialchars($product['title']); ?></h2>
            <h3 class="price-tag mb-3">$<?php echo number_format($product['price'], 2); ?></h3>
            
            <hr>
            
            <h5 class="fw-bold text-muted small uppercase mb-2">Description & Contact Info</h5>
            <div class="bg-light p-3 rounded border text-secondary mb-3">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>

            <div class="small text-muted">
                <i class="fas fa-bullhorn me-1"></i> Contact details are included in the description above. Reach out directly using the seller's preferred platform.
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card p-4 custom-card h-100">
                <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Advertisement Information</h5>
                <ul class="list-unstyled mb-0 text-muted">
                    <li class="mb-2"><strong>Posted By:</strong> <?php echo htmlspecialchars($product['username']); ?></li>
                    <li class="mb-2"><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name']); ?></li>
                    <li><strong>Posted On:</strong> <?php echo date('M d, Y', strtotime($product['created_at'])); ?></li>
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-4 custom-card h-100">
                <h5 class="fw-bold mb-3"><i class="fas fa-star text-warning me-2"></i>Reviews & Feedback</h5>
                
                <?php if ($reviews_res && $reviews_res->num_rows > 0): ?>
                    <div class="mb-3" style="max-height: 150px; overflow-y: auto;">
                        <?php while ($rev = $reviews_res->fetch_assoc()): ?>
                            <div class="border-bottom pb-2 mb-2">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($rev['reviewer_name']); ?></div>
                                <div class="small text-muted"><?php echo htmlspecialchars($rev['comment']); ?></div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted small mb-3">No reviews yet.</p>
                <?php endif; ?>

                <form action="product-details.php?id=<?php echo $id; ?>" method="POST" class="mt-auto">
                    <input type="text" name="reviewer_name" class="form-control form-control-sm mb-2" placeholder="Your Name" required>
                    <textarea name="comment" class="form-control form-control-sm mb-2" rows="2" placeholder="Write a review..." required></textarea>
                    <button type="submit" name="submit_review" class="btn btn-brown btn-sm w-100">Submit Review</button>
                </form>
            </div>
        </div>
    </div>
</div>

<footer class="py-3 text-center">
    <div class="container">
        <small>&copy; <?php echo date('Y'); ?> Uni-Biz Hub. All rights reserved.</small>
    </div>
</footer>

</body>
</html>