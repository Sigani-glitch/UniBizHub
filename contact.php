<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Uni-Biz Hub</title>
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

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-sm rounded-lg overflow-hidden">
                <div class="card-header bg-dark text-white p-4">
                    <h3 class="font-weight-bold mb-1"><i class="fas fa-envelope text-warning mr-2"></i>Contact Us</h3>
                    <p class="mb-0 text-white-50 small">Have questions or feedback? Send us a message below.</p>
                </div>
                <div class="card-body p-4">
                    <form action="contact.php" method="POST">
                        <div class="form-group mb-3">
                            <label for="name" class="font-weight-bold text-dark">Name</label>
                            <input type="text" id="name" name="name" class="form-control" required placeholder="Your Full Name">
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="font-weight-bold text-dark">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required placeholder="your.email@example.com">
                        </div>
                        <div class="form-group mb-4">
                            <label for="message" class="font-weight-bold text-dark">Message</label>
                            <textarea id="message" name="message" rows="4" class="form-control" required placeholder="Type your message here..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm">
                            <i class="fas fa-paper-plane mr-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3">
    <div class="container">
        <small>&copy; <?php echo date('Y'); ?> Uni-Biz Hub. All rights reserved.</small>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>