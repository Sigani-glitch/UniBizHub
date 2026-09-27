<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand font-weight-bold" href="/UniBizHub/index.php">Uni-Biz Hub</a>
    
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="/UniBizHub/index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/UniBizHub/contact.php">Contact Us</a>
        </li>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="/UniBizHub/post-product.php">Post Product</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/UniBizHub/my-listings.php">My Listings</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger" href="/UniBizHub/auth/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>)</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="/UniBizHub/auth/login.php">Login / Register</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>