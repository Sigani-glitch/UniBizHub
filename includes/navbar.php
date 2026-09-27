<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="/UniBizHub/index.php">
            <i class="fas fa-store mr-2"></i>Uni-Biz Hub
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-collapse nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/UniBizHub/index.php"><i class="fas fa-home mr-1"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/UniBizHub/contact.php"><i class="fas fa-envelope mr-1"></i> Contact Us</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/UniBizHub/post-product.php"><i class="fas fa-plus-circle mr-1"></i> Post Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/UniBizHub/my-listings.php"><i class="fas fa-list mr-1"></i> My Listings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="/UniBizHub/auth/logout.php"><i class="fas fa-sign-out-alt mr-1"></i> Logout (<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>)</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/UniBizHub/auth/login.php"><i class="fas fa-sign-in-alt mr-1"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/UniBizHub/auth/register.php"><i class="fas fa-user-plus mr-1"></i> Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>