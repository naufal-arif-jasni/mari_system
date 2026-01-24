<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image" href="images/logo_red.jpeg">
    <title>Login - MARI System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Styles -->
<link rel="stylesheet" href="assets/css/login-styles.css">

</head>
<body class="d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card p-4 shadow" style="width: 400px;">
        <div class="text-center mb-4">
             <!-- LOGO HERE -->
            <img src="images/logo_red.jpeg" alt="MARI Logo red" style="height: 100px; margin-bottom: 15px; border-radius: 10px;">
            <h4 class="text-primary fw-bold">MARI System</h4>
            <small class="text-muted">Malaysia Aid Registration Initiative</small>
</div>
        
        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger"><?=$_GET['error']?></div>
        <?php } ?>

        <form action="auth.php" method="post">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <a href="register.php" class="d-block text-center mt-3 text-secondary">Register New Account</a>
        <a href="index.php" class="d-block text-center mt-2 text-muted small">Back to Home</a>
    </div>
</body>
</html>