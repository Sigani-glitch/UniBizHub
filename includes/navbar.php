<header>
    <nav class="navbar">
        <div class="logo"><a href="index.php" style="color: white; text-decoration: none; font-weight: bold;">Uni-Biz Hub</a></div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="post-product.php">Sell Item</a></li>
                <li><a href="my-listings.php">My Listings</a></li>
                <li><a href="auth/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.html">Login / Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>