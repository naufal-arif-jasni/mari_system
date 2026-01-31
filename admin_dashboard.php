<?php
// --- 1. SESSION & AUTHENTICATION ---
session_start();

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// --- 2. DATABASE CONNECTION ---
$conn = new mysqli("localhost", "root", "", "mari_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- 3. STATS CALCULATION ---
$stats = $conn->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN application_status='Approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN application_status='Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN application_status='Rejected' THEN 1 ELSE 0 END) as rejected
    FROM applications")->fetch_assoc();

// Get recent applications
$recent_apps = $conn->query("SELECT 
    application_id,
    application_number,
    application_status,
    submission_date,
    full_name,
    primary_category
    FROM applications
    ORDER BY submission_date DESC 
    LIMIT 5");

// Get status distribution by category
$category_stats = $conn->query("SELECT 
    primary_category, 
    COUNT(*) as count 
    FROM applications
    WHERE primary_category IS NOT NULL
    GROUP BY primary_category 
    ORDER BY count DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image" href="images/logo_red.jpeg">
    <title>MARI - Admin Dashboard</title>

    <!-- Styles -->
<link rel="stylesheet" href="assets/css/admindashboard-styles.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-left">
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
            <a href="admin_dashboard.php" class="logo"><img src="images/logo.png" alt="MARI Logo"></a>
        </div>
        <div class="nav-right">Admin Panel - <span>MARI System</span></div>
    </nav>

    <aside class="sidebar" id="sidebar">
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin.php">Applications</a></li>
            <li><a href="admin_profile.php">Manage Admins</a></li>
            <li><a href="admin_dashboard.php?logout=true" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.2);">Logout</a></li>
        </ul>
    </aside>

    <main class="main-content" id="main-content">
        <h2 style="margin-bottom: 25px; color: var(--primary-maroon);">Dashboard Overview</h2>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total">📊</div>
                <div class="stat-info">
                    <div class="stat-label">Total Applications</div>
                    <div class="stat-value"><?= $stats['total'] ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon approved">✓</div>
                <div class="stat-info">
                    <div class="stat-label">Approved</div>
                    <div class="stat-value"><?= $stats['approved'] ?? 0 ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pending">⏳</div>
                <div class="stat-info">
                    <div class="stat-label">Pending Review</div>
                    <div class="stat-value"><?= $stats['pending'] ?? 0 ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon rejected">✕</div>
                <div class="stat-info">
                    <div class="stat-label">Rejected</div>
                    <div class="stat-value"><?= $stats['rejected'] ?? 0 ?></div>
                </div>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="dashboard-section">
            <div class="section-header">
                <h3 class="section-title">Recent Applications</h3>
                <a href="admin.php" class="view-all-link">View All →</a>
            </div>
            
            <?php if ($recent_apps->num_rows > 0): ?>
            <table class="recent-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Applicant Name</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($app = $recent_apps->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $app['application_id'] ?></td>
                        <td><strong><?= htmlspecialchars($app['full_name']) ?></strong></td>
                        <td><?= htmlspecialchars($app['primary_category']) ?></td>
                        <td><span class="status-badge status-<?= strtolower($app['application_status']) ?>"><?= $app['application_status'] ?></span></td>
                        <td><?= date('d M Y', strtotime($app['submission_date'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #999; padding: 20px;">No applications yet.</p>
            <?php endif; ?>
        </div>

        <!-- Category Distribution -->
        <div class="dashboard-section">
            <div class="section-header">
                <h3 class="section-title">Applications by Category</h3>
            </div>
            <div class="category-grid">
                <?php 
                if ($category_stats->num_rows > 0) {
                    while($cat = $category_stats->fetch_assoc()): 
                ?>
                <div class="category-item">
                    <div class="category-name"><?= htmlspecialchars($cat['primary_category']) ?></div>
                    <div class="category-count"><?= $cat['count'] ?></div>
                </div>
                <?php 
                    endwhile;
                } else {
                    echo '<p style="color: #999; grid-column: 1/-1; text-align: center;">No data available</p>';
                }
                ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-section">
            <div class="section-header">
                <h3 class="section-title">Quick Actions</h3>
            </div>
            <div class="quick-actions">
                <a href="admin.php" class="action-btn">📋 View All Applications</a>
                <a href="admin.php?filter_status=Pending" class="action-btn">⏳ Review Pending</a>
                <a href="admin.php?filter_status=Approved" class="action-btn">✓ View Approved</a>
                <a href="admin.php?filter_status=Rejected" class="action-btn">✕ View Rejected</a>
                <a href="admin_profile.php" class="action-btn">⚙️ Manage Admins</a>
            </div>
        </div>
    </main>

    <!-- MARI Scripts -->
    <script src="assets/js/common.js"></script>
    <script src="assets/js/admindashboard.js"></script>

</body>
</html>