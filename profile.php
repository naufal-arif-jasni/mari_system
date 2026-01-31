<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data from database
include "db_conn.php";
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $saved_data = mysqli_fetch_assoc($result);
} else {
    header("Location: login.php");
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";

// Fetch application history
$app_sql = "SELECT 
                application_id,
                application_number,
                application_status,
                submission_date,
                primary_category
            FROM applications
            WHERE user_id = '$user_id' 
            ORDER BY submission_date DESC";
$app_result = mysqli_query($conn, $app_sql);

$application_history = [];
while ($row = mysqli_fetch_assoc($app_result)) {
    $application_history[] = [
        'id' => $row['application_id'],
        'type' => $row['primary_category'] . ' Assistance',
        'date' => date('Y-m-d', strtotime($row['submission_date'])),
        'status' => $row['application_status']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image" href="images/logo_red.jpeg">
    <title>MARI - Profile Overview</title>
    
    <!-- Styles -->
<link rel="stylesheet" href="assets/css/savedprofile-styles.css">
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
        <div class="container">
            
            <?php if (isset($_GET['update_success'])): ?>
                <div class="alert-success">
                    <strong>Success!</strong> Your profile has been updated successfully.
                </div>
            <?php endif; ?>

            <div class="section-card">
                <div class="section-header">
                    <h2>Profile Overview</h2>
                    <a href="savedprofile.php" class="btn-edit">✎ Edit Profile</a>
                </div>

                <div class="profile-display-header">
                    <div class="avatar-circle">
                        <?php if (!empty($saved_data['profile_picture'])): ?>
                            <img src="<?php echo htmlspecialchars($saved_data['profile_picture']); ?>" alt="Profile Picture">
                        <?php else: ?>
                            <?php echo strtoupper(substr($saved_data['full_name'], 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h3><?php echo htmlspecialchars($saved_data['full_name']); ?></h3>
                        <p style="color: #777;">Member Profile</p>
                    </div>
                </div>
                
                <div class="detail-row"><div class="detail-label">Full Name</div><div class="detail-value"><?php echo htmlspecialchars($saved_data['full_name']); ?></div></div>
                <div class="detail-row"><div class="detail-label">IC Number</div><div class="detail-value"><?php echo htmlspecialchars($saved_data['ic_number']); ?></div></div>
                <div class="detail-row"><div class="detail-label">OKU Card No.</div><div class="detail-value"><?php echo !empty($saved_data['oku_number']) ? htmlspecialchars($saved_data['oku_number']) : '<em style="color: #999;">Not provided yet</em>'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Email Address</div><div class="detail-value"><?php echo htmlspecialchars($saved_data['email']); ?></div></div>
                <div class="detail-row"><div class="detail-label">Phone Number</div><div class="detail-value"><?php echo htmlspecialchars($saved_data['phone_number']); ?></div></div>
                <div class="detail-row"><div class="detail-label">Mailing Address</div><div class="detail-value"><?php echo !empty($saved_data['address']) ? htmlspecialchars($saved_data['address']) : '<em style="color: #999;">Not provided yet</em>'; ?></div></div>
                <div class="detail-row"><div class="detail-label">State</div><div class="detail-value"><?php echo !empty($saved_data['state']) ? htmlspecialchars($saved_data['state']) : '<em style="color: #999;">Not provided yet</em>'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Zip Code</div><div class="detail-value"><?php echo !empty($saved_data['zip_code']) ? htmlspecialchars($saved_data['zip_code']) : '<em style="color: #999;">Not provided yet</em>'; ?></div></div>
            </div>
        </div>
    </main>

    <script src="assets/js/common.js"></script>
</body>

</html>