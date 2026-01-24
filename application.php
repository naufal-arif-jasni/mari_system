<?php
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Fetch application history
include "db_conn.php";
$user_id = $_SESSION['user_id'];
// NEW query 
$app_sql = "SELECT 
                a.application_id,
                a.application_number,
                a.status,
                a.submission_date,
                dd.primary_category,
                a.admin_remarks
            FROM applications a
            LEFT JOIN disability_details dd ON a.application_id = dd.application_id
            WHERE a.user_id = '$user_id' 
            ORDER BY a.submission_date DESC 
            LIMIT 5";
$app_result = mysqli_query($conn, $app_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image" href="images/logo_red.jpeg">
    <title>MARI - New Application</title>
    
    <!-- Bootstrap CSS (if using) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Styles -->
<link rel="stylesheet" href="assets/css/application-styles.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-left">
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
            <a href="home.php" class="logo">
                <img src="images/logo.png" alt="MARI Logo">
            </a>
        </div>
        <div class="nav-right">
            Welcome, <span><?php echo htmlspecialchars($username); ?></span>
        </div>
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
            
            <!-- Hero Section -->
            <div class="hero-card">
                <h1>📋 New Disability Aid Application</h1>
                <p>Please ensure you have your medical documents ready before starting.</p>
                <a href="application_form.php" class="btn-start">Start Application Form →</a>
            </div>

            <!-- Requirements Section -->
            <div class="info-section">
                <h2>Before You Begin</h2>
                <div class="alert-info">
                    <strong>Important:</strong> Please prepare the following documents before starting your application to ensure a smooth process.
                </div>
                
                <div class="requirements-grid">
                    <div class="requirement-card">
                        <span class="icon">🆔</span>
                        <h3>Identity Documents</h3>
                        <p>MyKad (IC) number and OKU card registration details</p>
                    </div>
                    
                    <div class="requirement-card">
                        <span class="icon">🏥</span>
                        <h3>Medical Reports</h3>
                        <p>Medical report dated within the last 2 years (PDF or JPEG)</p>
                    </div>
                    
                    <div class="requirement-card">
                        <span class="icon">📝</span>
                        <h3>Specialist Form</h3>
                        <p>Specialist confirmation form signed by your doctor (PDF only)</p>
                    </div>
                    
                    <div class="requirement-card">
                        <span class="icon">👨‍👩‍👧</span>
                        <h3>Guardian Info</h3>
                        <p>Guardian/caregiver details (if applicable or under 18)</p>
                    </div>
                </div>
            </div>

<div class="history-section">
    <h2>Your Application History</h2>
    
    <?php if (mysqli_num_rows($app_result) > 0): ?>
        <table class="history-table">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Category</th>
                    <th>Submitted Date</th>
                    <th>Status</th>
                    <th>Admin Remarks</th> </tr>
            </thead>
            <tbody>
                <?php while($app = mysqli_fetch_assoc($app_result)): ?>
                <tr>
                    <td><strong>#<?= $app['application_id'] ?></strong></td>
                    <td><?= htmlspecialchars($app['primary_category']) ?> Assistance</td>
                    <td><?= date('d M Y', strtotime($app['submission_date'])) ?></td>
                    <td>
                        <span class="status-badge status-<?= strtolower($app['status']) ?>">
                            <?= $app['status'] ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($app['admin_remarks'])): ?>
                            <span class="remark-text"><?= htmlspecialchars($app['admin_remarks']) ?></span>
                        <?php else: ?>
                            <small style="color: #999; font-style: italic;">No remarks</small>
                        <?php endif; ?>
                    </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-history">
            <p>You haven't submitted any applications yet. Click the button above to start your first application.</p>
        </div>
    <?php endif; ?>
</div>

        </div>
        <?php include 'footer.php'; ?>
    </main>
    
    <script src="assets/js/common.js"></script>
</body>
</html>