<?php
// Starting the session to catch the username from the login page
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";
$user_id = $_SESSION['user_id'];

// Fetch application history
include "db_conn.php";
// NEW query must JOIN two tables
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
            ORDER BY a.submission_date DESC";
$app_result = mysqli_query($conn, $app_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image" href="images/logo_red.jpeg">
    <title>MARI - Page Title</title>
        
    <!-- Bootstrap CSS (if using) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Styles -->
<link rel="stylesheet" href="assets/css/home-styles.css">
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
        <div class="content-container">
            
            <!-- Application History Section -->
            <section id="my-applications">
                <h2>My Applications</h2>
                <?php if (mysqli_num_rows($app_result) > 0): ?>
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Application ID</th>
                                <th>Category</th>
                                <th>Submitted Date</th>
                                <th>Status</th>
                                <th>Admin Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($app = mysqli_fetch_assoc($app_result)): ?>
                            <tr>
                                <td><strong>#<?= $app['application_id'] ?></strong></td>
                                <td><?= htmlspecialchars($app['primary_category']) ?> Assistance</td>
                                <td><?= date('d M Y, g:i A', strtotime($app['submission_date'])) ?></td>
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
                    <div class="no-applications">
                        <p>You haven't submitted any applications yet.</p>
                        <a href="application.php" class="apply-btn">Submit Your First Application</a>
                    </div>
                <?php endif; ?>
            </section>

            <section id="about">
                <h2>About Us</h2>
                <p><strong>Malaysia Aid Registration Initiative (MARI)</strong> is a community-driven platform created to make financial and social assistance more accessible for Orang Kurang Upaya (OKU) across Malaysia.</p>
                <p>The word "Mari" means "come" in Bahasa Malaysia, reflecting our welcoming spirit and our mission to invite everyone to come together in supporting the OKU community. MARI acts as a bridge between OKU individuals and available assistance from government agencies, non-governmental organisations (NGOs), and charitable partners.</p>
                <p>Many OKU individuals face challenges in understanding eligibility requirements, completing applications, or even knowing what types of assistance are available. MARI simplifies this process by providing guided registration, clear information, and application support, ensuring that no one is left behind due to lack of access or knowledge.</p>
                <p>Through strategic collaborations with government bodies and NGOs, MARI aims to streamline aid distribution, improve transparency, and ensure that assistance reaches those who truly need it. Our long-term vision is to build an inclusive digital ecosystem where support is easy to access, fair, and empowering.</p>
                <p>At MARI, we believe that support should be simple, dignified, and inclusive, because everyone deserves the opportunity to live with independence and hope.</p>
            </section>

            <section id="rules">
                <h2>Rules and Requirements</h2>
                <ul class="rules-list">
                    <li>
                        <strong>1. Registered OKU Status</strong>
                        Applicants must be officially registered as Orang Kurang Upaya (OKU) with the relevant Malaysian authority.
                    </li>
                    <li>
                        <strong>2. Malaysian Citizen</strong>
                        Only Malaysian citizens are eligible to apply for assistance through MARI.
                    </li>
                    <li>
                        <strong>3. Accurate Personal Information</strong>
                        All personal details provided must be true, complete, and up to date to ensure proper verification and processing.
                    </li>
                    <li>
                        <strong>4. Supporting Documents Required</strong>
                        Applicants must submit relevant supporting documents, such as OKU registration proof or income-related documents, when requested.
                    </li>
                    <li>
                        <strong>5. One Active Application at a Time</strong>
                        To ensure fair distribution of assistance, applicants may only have one active application under MARI at any given time.
                    </li>
                </ul>
                <p class="warning-text">Failure to comply with the above requirements may result in delays or rejection of the application.</p>
            </section>



        </div>
        <?php include 'footer.php'; ?>    
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/common.js"></script>
</body>
</html>