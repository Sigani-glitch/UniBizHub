<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Database Connection
$host     = "localhost";
$db_user  = "root";
$db_pass  = "";
$db_name  = "unibizhub_db";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch user's listings
$stmt = $conn->prepare("SELECT * FROM products WHERE user_id = ? ORDER BY created_at DESC");
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
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/UniBizHub/style.css">
</head>
<body>

<?php include_once 'includes/navbar.php'; ?>

<div class="bg-white border-bottom py-4 mb-4 shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h2 class="font-weight-bold text-dark mb-1"><i class="fas fa-list-alt text-primary mr-2"></i>My Listed Products</h2>
            <p class="text-muted mb-0">Manage, edit, or remove the items you are selling on campus.</p>
        </div>
        <a href="post-product.php" class="btn btn-success font-weight-bold shadow-sm">
            <i class="fas fa-plus-circle mr-1"></i> Post New Item
        </a>
    </div>
</div>

<div class="container mb-5">
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="row">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm rounded-lg overflow-hidden">
                        <?php 
                            $img = !empty($row['image']) ? $row['image'] : '';
                            if (!empty($img) && strpos($img, 'uploads/') !== 0) {
                                $img = 'uploads/' . $img;
                            }
                            if (empty($img) || !file_exists($img)) {
                                $img = 'https://via.placeholder.com/400x250?text=No+Image+Available';
                            }
                        ?>
                        <div class="position-relative">
                            <img src="<?php echo htmlspecialchars($img); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['title']); ?>" style="height: 200px; object-fit: cover;">
                            <span class="badge badge-primary position-absolute" style="top: 10px; right: 10px; padding: 6px 12px; font-size: 0.8rem;">Active</span>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title font-weight-bold text-dark mb-2"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="text-success font-weight-bold h5 mb-3">$<?php echo number_format($row['price'], 2); ?></p>
                            
                            <div class="mt-auto pt-3 border-top d-flex justify-content-between">
                                <a href="product-details.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm font-weight-bold">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                                <a href="edit-product.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-secondary btn-sm font-weight-bold">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <a href="delete-product.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-danger btn-sm font-weight-bold" onclick="return confirm('Are you sure you want to delete this listing?');">
                                    <i class="fas fa-trash-alt mr-1"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="row justify-content-center py-5">
            <div class="col-md-6 text-center">
                <div class="card border-0 shadow-sm p-5 rounded-lg">
                    <div class="mb-3 text-muted">
                        <i class="fas fa-box-open fa-4x text-secondary"></i>
                    </div>
                    <h4 class="font-weight-bold text-dark">No Products Listed Yet</h4>
                    <p class="text-muted mb-4">You haven't posted any items for sale. Start selling to your campus community today!</p>
                    <div>
                        <a href="post-product.php" class="btn btn-primary btn-lg font-weight-bold px-4 shadow-sm">
                            <i class="fas fa-plus-circle mr-2"></i>Post An Item Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<footer class="bg-dark text-white text-center py-3 mt-auto">
    <div class="container">
        <small>&copy; <?php echo date('Y'); ?> Uni-Biz Hub. All rights reserved.</small>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>