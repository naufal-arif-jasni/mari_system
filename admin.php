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

// --- 3. LOGIC: DELETE ---
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // Delete all related records (CASCADE will handle this automatically)
    // But we need to manually delete physical files
    $file_query = $conn->query("SELECT file_path FROM documents WHERE application_id = $id");
    while ($file_row = $file_query->fetch_assoc()) {
        if (!empty($file_row['file_path']) && file_exists($file_row['file_path'])) {
            unlink($file_row['file_path']);
        }
    }
    
    // Delete the application (CASCADE will delete all related records)
    $conn->query("DELETE FROM applications WHERE application_id = $id");
    
    // Log the deletion
    $admin_user = $_SESSION['username'] ?? 'admin';
    $ip = $_SERVER['REMOTE_ADDR'];
    $conn->query("INSERT INTO activity_log (application_id, activity_type, description, ip_address) 
                  VALUES ($id, 'Delete', 'Application deleted by $admin_user', '$ip')");
    
    echo "<script>alert('Application deleted successfully!'); window.location.href='admin.php';</script>";
    exit();
}

// --- 4. LOGIC: UPDATE STATUS ---
if (isset($_POST['update_btn'])) {
    $id = intval($_POST['app_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $remarks = mysqli_real_escape_string($conn, $_POST['admin_remarks']);
    $admin_user = $_SESSION['username'] ?? 'admin';
    
    // Get old status for history
    $old_status_query = $conn->query("SELECT status FROM applications WHERE application_id = $id");
    $old_status_row = $old_status_query->fetch_assoc();
    $old_status = $old_status_row['status'];
    
    // Update application status
    $stmt = $conn->prepare("UPDATE applications SET status=?, admin_remarks=?, reviewed_by=?, reviewed_at=NOW() WHERE application_id=?");
    $stmt->bind_param("sssi", $status, $remarks, $admin_user, $id);
    $stmt->execute();
    
    // Insert into status history
    $history_stmt = $conn->prepare("INSERT INTO status_history (application_id, old_status, new_status, changed_by, remarks) VALUES (?, ?, ?, ?, ?)");
    $history_stmt->bind_param("issss", $id, $old_status, $status, $admin_user, $remarks);
    $history_stmt->execute();
    
    // Log activity
    $ip = $_SERVER['REMOTE_ADDR'];
    $log_stmt = $conn->prepare("INSERT INTO activity_log (application_id, activity_type, description, ip_address) VALUES (?, 'Update', ?, ?)");
    $log_desc = "Status changed from $old_status to $status by $admin_user";
    $log_stmt->bind_param("iss", $id, $log_desc, $ip);
    $log_stmt->execute();
    
    echo "<script>alert('Application status updated successfully!'); window.location.href='admin.php';</script>";
    exit();
}

// --- 5. DATA RETRIEVAL (Using JOIN to get data from multiple tables) ---
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_status = isset($_GET['filter_status']) ? mysqli_real_escape_string($conn, $_GET['filter_status']) : '';

$sql = "SELECT 
    a.application_id,
    a.application_number,
    a.status,
    a.submission_date,
    a.admin_remarks,
    ad.full_name,
    ad.mykad,
    ad.phone_number,
    ad.email_address,
    dd.primary_category,
    dd.severity_level,
    fi.employment_status,
    (SELECT GROUP_CONCAT(document_type) FROM documents WHERE application_id = a.application_id) as documents
FROM applications a
LEFT JOIN applicant_details ad ON a.application_id = ad.application_id
LEFT JOIN disability_details dd ON a.application_id = dd.application_id
LEFT JOIN functional_impact fi ON a.application_id = fi.application_id
WHERE 1=1";

if (!empty($search)) { 
    $sql .= " AND (ad.full_name LIKE '%$search%' OR a.application_number LIKE '%$search%' OR ad.mykad LIKE '%$search%')"; 
}
if (!empty($filter_status)) { 
    $sql .= " AND a.status = '$filter_status'"; 
}
$sql .= " ORDER BY a.submission_date DESC";

$result = $conn->query($sql);
$rows = [];
while($r = $result->fetch_assoc()) { $rows[] = $r; }

// --- 6. GET REPORT DATA (for report generation) ---
$report_sql = "SELECT 
    a.application_id,
    a.application_number,
    a.status,
    a.submission_date,
    ad.full_name,
    dd.primary_category
FROM applications a
LEFT JOIN applicant_details ad ON a.application_id = ad.application_id
LEFT JOIN disability_details dd ON a.application_id = dd.application_id
ORDER BY a.submission_date DESC";

$report_result = $conn->query($report_sql);
$report_data = [];
while($r = $report_result->fetch_assoc()) { $report_data[] = $r; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image" href="images/logo_red.jpeg">
    <title>MARI - Admin Dashboard</title>
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/admin-styles.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-left">
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
            <a href="admin.php" class="logo"><img src="images/logo.png" alt="MARI Logo"></a>
        </div>
        <div class="nav-right">Admin Panel - <span>MARI System</span></div>
    </nav>

    <aside class="sidebar" id="sidebar">
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin.php">Applications</a></li>
            <li><a href="admin.php?logout=true" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.2);">Logout</a></li>
        </ul>
    </aside>

    <main class="main-content" id="main-content">
        <h2 style="margin-bottom: 20px; color: var(--primary-maroon);">Application Management</h2>

        <!-- Filter Section -->
        <div class="filter-card">
            <form action="" method="GET">
                <div class="filter-grid">
                    <div>
                        <label style="font-size: 0.9rem; color: #666; margin-bottom: 5px; display: block;">Search</label>
                        <input type="text" name="search" placeholder="Search by name, ID, or IC..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div>
                        <label style="font-size: 0.9rem; color: #666; margin-bottom: 5px; display: block;">Filter Status</label>
                        <select name="filter_status">
                            <option value="">All Status</option>
                            <option value="Pending" <?= $filter_status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Approved" <?= $filter_status == 'Approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="Rejected" <?= $filter_status == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-view">Apply</button>
                    <a href="admin.php" class="btn btn-edit">Reset</a>
                    <button type="button" class="btn btn-report" onclick="openReportModal()">Generate Report</button>

                </div>
            </form>
        </div>

        <!-- Applications Grid -->
        <div class="app-grid">
            <?php foreach($rows as $row): ?>
            <div class="app-card">
                <div class="app-header">
                    <div class="app-id"><?= htmlspecialchars($row['application_number']) ?></div>
                    <div class="app-name"><?= htmlspecialchars($row['full_name']) ?></div>
                    <div class="app-category"><?= htmlspecialchars($row['primary_category']) ?></div>
                </div>
                
                <div class="app-body">
                    <div class="info-row">
                        <span class="info-label">MyKad</span>
                        <span class="info-value"><?= htmlspecialchars($row['mykad']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone</span>
                        <span class="info-value"><?= htmlspecialchars($row['phone_number']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Submitted</span>
                        <span class="info-value"><?= date('d M Y', strtotime($row['submission_date'])) ?></span>
                    </div>
                    <div class="info-row" style="border: none;">
                        <span class="info-label">Status</span>
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $row['status'])) ?>"><?= $row['status'] ?></span>
                    </div>
                </div>
                
                <div class="app-footer">
                    <button class="btn btn-view" onclick="viewApplication(<?= $row['application_id'] ?>)">View</button>
                    <button class="btn btn-edit" onclick="editApplication(<?= $row['application_id'] ?>)">Edit</button>
                    <button class="btn btn-delete" onclick="deleteApplication(<?= $row['application_id'] ?>)">Delete</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($rows)): ?>
            <div style="text-align: center; padding: 60px; background: white; border-radius: 10px; margin-top: 20px;">
                <h3 style="color: #999;">No applications found</h3>
                <p style="color: #666;">Try adjusting your search or filter criteria.</p>
            </div>
        <?php endif; ?>
    </main>

    <!-- View Modal -->
    <div class="modal-overlay" id="viewModal">
        <div class="modal-container" id="viewContent"></div>
    </div>

    <!-- Edit Modal -->
    <div class="modal-overlay" id="editModal">
        <div class="modal-container" id="editContent"></div>
    </div>

    <!-- Report Modal -->
    <div class="modal-overlay" id="reportModal">
        <div class="report-modal-container" id="reportContent">
            <button class="report-close-btn" onclick="closeReportModal()">&times;</button>
            <div class="report-container">
                <h2>Disability Aid Registration Report</h2>
                
                <div class="report-meta">
                    <strong>Generated on:</strong> <?= date('Y-m-d') ?><br>
                    <strong>Report Type:</strong> Summary of All Applications<br>
                    <strong>Total Applications:</strong> <?= count($report_data) ?>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Application ID</th>
                            <th>Applicant Name</th>
                            <th>Category</th>
                            <th>Submitted Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($report_data as $app): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['application_number']) ?></td>
                            <td><?= htmlspecialchars($app['full_name']) ?></td>
                            <td><?= htmlspecialchars($app['primary_category']) ?></td>
                            <td><?= date('d M Y', strtotime($app['submission_date'])) ?></td>
                            <td><span class="status-badge status-<?= strtolower($app['status']) ?>"><?= $app['status'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="margin-top: 30px; text-align: center;">
                    <button onclick="downloadReport()" class="btn-download">
                        📥 Download Report (PDF)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/common.js"></script>
    <script src="assets/js/admin-new.js"></script>
    
    
    <!-- Pass PHP data to JavaScript -->
    <script>
        console.log('=== DEBUG: Data from PHP ===');
        const phpData = <?= json_encode($rows) ?>;
        const reportData = <?= json_encode($report_data) ?>;
        console.log('Applications data:', phpData);
        console.log('Report data:', reportData);
        
        if (typeof setApplicationsData === 'function') {
            setApplicationsData(phpData);
            console.log('✓ setApplicationsData called successfully');
        } else {
            console.error('✗ setApplicationsData function not found!');
        }
    </script>
</body>
</html>