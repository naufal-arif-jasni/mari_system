<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image" href="images/logo_red.jpeg">
    <title>Register - MARI System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/register-styles.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    
    <div class="card p-4 shadow-lg" style="width: 100%; max-width: 600px;">
        <div class="text-center mb-4">
            <img src="images/logo_red.jpeg" alt="MARI Logo red" style="height: 80px; margin-bottom: 10px; border-radius: 8px;">
            <h3 class="fw-bold text-primary">Create Account</h3>
            <p class="text-muted">Join the Malaysia Aid Registration Initiative</p>
        </div>

        <form action="register.php" method="post">
            
            <!-- 1. Full Name -->
            <div class="mb-3">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" placeholder="Enter your full name as in IC" required>
            </div>

            <!-- 2. IC Number (NRIC) -->
            <div class="mb-3">
                <label class="form-label">IC Number (MyKad) <span class="text-danger">*</span></label>
                <input type="text" name="ic_number" class="form-control" placeholder="XXXXXXXXXXXX" required>
            </div>

            <div class="row">
                <!-- 3. Email -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                </div>
                <!-- 4. Phone Number -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="phone_number" class="form-control" placeholder="0123456789" required>
                </div>
            </div>

            <!-- 5. Username -->
            <div class="mb-3">
                <label class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" placeholder="Choose a username" required>
            </div>

            <!-- 6. Password -->
            <div class="mb-3">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" placeholder="Create a password" required>
            </div>

            <button type="submit" name="signup" class="btn btn-primary w-100 btn-lg">Sign Up</button>
            
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">Already have an account? Login</a>
            </div>
            <div class="text-center mt-2">
                <a href="index.php" class="text-secondary small">Back to Home</a>
            </div>
        </form>

        <?php
        if (isset($_POST['signup'])) {
            include "db_conn.php";

            // Receive all inputs
            $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
            $ic_number = mysqli_real_escape_string($conn, $_POST['ic_number']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // Check if username already exists
            $check_sql = "SELECT * FROM users WHERE username='$username'";
            $check_result = mysqli_query($conn, $check_sql);

            if (mysqli_num_rows($check_result) > 0) {
                echo "<div class='alert alert-warning mt-3'>Username already taken. Please choose another.</div>";
            } else {
                $sql = "INSERT INTO users (full_name, ic_number, email, phone_number, username, password) 
                        VALUES ('$full_name', '$ic_number', '$email', '$phone', '$username', '$password')";
                
                if (mysqli_query($conn, $sql)) {
                    echo "<div class='alert alert-success mt-3'>Account created successfully! <a href='login.php'>Login here</a>.</div>";
                } else {
                    echo "<div class='alert alert-danger mt-3'>Error: " . mysqli_error($conn) . "</div>";
                }
            }
        }
        ?>
    </div>

</body>
</html>