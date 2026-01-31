<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image" href="images/logo_red.jpeg">
    <title>Login - MARI System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/login-styles.css">
</head>
<body class="d-flex align-items-center justify-content-center" style="height: 100vh; background-color: #f8f9fa;">
    
    <div class="card p-4 shadow" style="width: 400px; border-radius: 15px;">
        <div class="text-center mb-4">
             <img src="images/logo_red.jpeg" alt="MARI Logo" style="height: 100px; margin-bottom: 15px; border-radius: 10px;">
            <h4 class="text-primary fw-bold">MARI System</h4>
            <small class="text-muted">Malaysia Aid Registration Initiative</small>
        </div>
        
        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger text-center" style="font-size: 0.9rem;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php } ?>

        <form action="auth.php" method="post">
            <div class="mb-3">
                <label class="form-label fw-bold">Login As:</label>
                <select name="role_type" class="form-select" required>
                    <option value="user">Public User / Applicant</option>
                    <option value="admin">System Administrator</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Login to Account</button>
        </form>

        <hr>

        <div class="text-center">
            <a href="register.php" class="d-block text-decoration-none text-secondary mb-2">Create New Account</a>
            <a href="index.php" class="d-block text-decoration-none text-secondary mb-2">Back to Homepage</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>