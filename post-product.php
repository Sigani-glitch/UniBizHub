<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html?error=please_login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Product - UniBizHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <nav class="navbar">
            <div class="logo">Uni-Biz Hub</div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="post-product.html">Sell Item</a></li>
                <li><a href="login.html">Account</a></li>
            </ul>
        </nav>
    </header>

    <main class="form-container">
        <h2>Post an Item for Sale</h2>  
        
        <form action="auth/post-product.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="title">Product Title</label>
                <input type="text" id="title" name="title" placeholder="e.g. Engineering Textbook" required>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="Food & Bakery">Food & Bakery</option>
                    <option value="Clothing">Clothing</option>
                    <option value="Electronics">Electronics</option>
                    <option value="Services">Services</option>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price ($)</label>
                <input type="number" id="price" name="price" step="0.01" placeholder="0.00" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Describe your product condition, location, etc." required></textarea>
            </div>

            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>

            <button type="submit" class="submit-btn">Publish Product</button>
        </form>
    </main>

</body>
</html>