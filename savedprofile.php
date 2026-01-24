<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data from database
include "db_conn.php";
$user_id = $_SESSION['user_id'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";

$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
} else {
    // If no user found, redirect to login
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image" href="images/logo_red.jpeg">
    <title>MARI - Edit Profile</title>
    
    <!-- Bootstrap CSS (if using) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/profile-styles.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-left">
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
            <a href="home.php" class="logo"><img src="images/logo.png" alt="MARI Logo"></a>
        </div>
        <div class="nav-right">Welcome, <span><?php echo htmlspecialchars($username); ?></span></div>
    </nav>

    <aside class="sidebar" id="sidebar">
        <ul class="sidebar-menu">
            <li><a href="home.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="application.php">Application</a></li>
            <li><a href="index.php" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.2);">Logout</a></li>
        </ul>
    </aside>

    <main class="main-content" id="main-content">
        <div class="profile-card">
            <div class="profile-header">
                <div class="avatar-container">
                    <div class="avatar-placeholder">
                        <?php if (!empty($user_data['profile_picture'])): ?>
                            <img src="<?php echo htmlspecialchars($user_data['profile_picture']); ?>" alt="Profile Picture">
                        <?php else: ?>
                            <?php echo strtoupper(substr($user_data['full_name'], 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div><h2>Edit Profile</h2><p>Update your personal information and account details.</p></div>
            </div>
            
            <div class="profile-body">
                <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>Update Profile Picture</label>
                            <input type="file" name="profile_pic" accept="image/*" style="background: none; border: none; padding: 5px 0;">
                        </div>

                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>IC Number</label>
                            <input type="text" value="<?php echo htmlspecialchars($user_data['ic_number']); ?>" readonly style="background-color: #eee;">
                        </div>
                        
                        <div class="form-group">
                            <label>OKU Card Number</label>
                            <input type="text" name="oku_number" value="<?php echo htmlspecialchars($user_data['oku_number'] ?? ''); ?>" placeholder="Enter OKU Card Number">
                        </div>
                        
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user_data['phone_number']); ?>" required>
                        </div>
                        
                        <div class="form-group full-width">
                            <label>Residential Address</label>
                            <textarea name="address" rows="3"><?php echo htmlspecialchars($user_data['address'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>State</label>
                            <select name="state" class="form-select">
                                <option value="">Select State...</option>
                                <option value="Johor" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Johor') ? 'selected' : ''; ?>>Johor</option>
                                <option value="Kedah" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Kedah') ? 'selected' : ''; ?>>Kedah</option>
                                <option value="Kelantan" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Kelantan') ? 'selected' : ''; ?>>Kelantan</option>
                                <option value="Melaka" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Melaka') ? 'selected' : ''; ?>>Melaka</option>
                                <option value="Negeri Sembilan" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Negeri Sembilan') ? 'selected' : ''; ?>>Negeri Sembilan</option>
                                <option value="Pahang" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Pahang') ? 'selected' : ''; ?>>Pahang</option>
                                <option value="Perak" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Perak') ? 'selected' : ''; ?>>Perak</option>
                                <option value="Perlis" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Perlis') ? 'selected' : ''; ?>>Perlis</option>
                                <option value="Pulau Pinang" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Pulau Pinang') ? 'selected' : ''; ?>>Pulau Pinang</option>
                                <option value="Sabah" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Sabah') ? 'selected' : ''; ?>>Sabah</option>
                                <option value="Sarawak" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Sarawak') ? 'selected' : ''; ?>>Sarawak</option>
                                <option value="Selangor" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Selangor') ? 'selected' : ''; ?>>Selangor</option>
                                <option value="Terengganu" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Terengganu') ? 'selected' : ''; ?>>Terengganu</option>
                                <option value="Kuala Lumpur" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Kuala Lumpur') ? 'selected' : ''; ?>>Kuala Lumpur</option>
                                <option value="Labuan" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Labuan') ? 'selected' : ''; ?>>Labuan</option>
                                <option value="Putrajaya" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Putrajaya') ? 'selected' : ''; ?>>Putrajaya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Zip Code</label>
                            <input type="text" name="zip_code" value="<?php echo htmlspecialchars($user_data['zip_code'] ?? ''); ?>" placeholder="Enter zip code">
                        </div>
                    </div>
                    <button type="submit" class="btn-save">Update Profile</button> 
                </form>
            </div>
        </div>
    </main>

    <script src="assets/js/common.js"></script>
</body>
</html>