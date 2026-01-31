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
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Get document paths from applicant_details before deletion
        $file_query = $conn->query("SELECT document_medical_report_path, document_specialist_form_path, 
                                    document_oku_card_path, document_ic_copy_path, document_photo_path, document_other_path 
                                    FROM applicant_details WHERE application_id = $id");
        
        if ($file_row = $file_query->fetch_assoc()) {
            $doc_fields = ['document_medical_report_path', 'document_specialist_form_path', 
                          'document_oku_card_path', 'document_ic_copy_path', 'document_photo_path', 'document_other_path'];
            
            foreach ($doc_fields as $field) {
                if (!empty($file_row[$field]) && file_exists($file_row[$field])) {
                    unlink($file_row[$field]);
                }
            }
        }
        
        // Delete from application_history (CASCADE will delete applicant_details and status records)
        $conn->query("DELETE FROM application_history WHERE application_id = $id");
        
        // Log the deletion
        $admin_user = $_SESSION['username'] ?? 'admin';
        $ip = $_SERVER['REMOTE_ADDR'];
        $conn->query("INSERT INTO activity_log (application_id, activity_type, description, ip_address) 
                      VALUES ($id, 'Delete', 'Application deleted by $admin_user', '$ip')");
        
        mysqli_commit($conn);
        
        echo "<script>alert('Application deleted successfully!'); window.location.href='admin.php';</script>";
        exit();
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Error deleting application'); window.location.href='admin.php';</script>";
        exit();
    }
}

// --- 4. LOGIC: UPDATE STATUS ---
if (isset($_POST['update_btn'])) {
    $id = intval($_POST['app_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $remarks = mysqli_real_escape_string($conn, $_POST['admin_remarks']);
    $admin_user = $_SESSION['username'] ?? 'admin';
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Get old status from application_history
        $old_status_query = $conn->query("SELECT status FROM application_history WHERE application_id = $id");
        $old_status_row = $old_status_query->fetch_assoc();
        $old_status = $old_status_row['status'];
        
        // Update application_history table
        $stmt = $conn->prepare("UPDATE application_history 
                                SET status=?, admin_remarks=?, reviewed_by=?, reviewed_at=NOW() 
                                WHERE application_id=?");
        $stmt->bind_param("sssi", $new_status, $remarks, $admin_user, $id);
        $stmt->execute();
        $stmt->close();
        
        // Insert into status table for history tracking
        $admin_id = $_SESSION['user_id'] ?? null;
        $history_stmt = $conn->prepare("INSERT INTO status (application_id, admin_id, old_status, new_status, changed_by, remarks) 
                                        VALUES (?, ?, ?, ?, ?, ?)");
        $history_stmt->bind_param("iissss", $id, $admin_id, $old_status, $new_status, $admin_user, $remarks);
        $history_stmt->execute();
        $history_stmt->close();
        
        // Log activity
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt = $conn->prepare("INSERT INTO activity_log (admin_id, application_id, activity_type, description, ip_address) 
                                     VALUES (?, ?, 'Update', ?, ?)");
        $log_desc = "Status changed from $old_status to $new_status by $admin_user";
        $log_stmt->bind_param("iiss", $admin_id, $id, $log_desc, $ip);
        $log_stmt->execute();
        $log_stmt->close();
        
        mysqli_commit($conn);
        
        echo "<script>alert('Application status updated successfully!'); window.location.href='admin.php';</script>";
        exit();
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Error updating status'); window.location.href='admin.php';</script>";
        exit();
    }
}

// --- 5. DATA RETRIEVAL (Using VIEW for backward compatibility) ---
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_status = isset($_GET['filter_status']) ? mysqli_real_escape_string($conn, $_GET['filter_status']) : '';

$sql = "SELECT 
    application_id,
    application_number,
    application_status,
    submission_date,
    admin_remarks,
    full_name,
    mykad,
    phone_number,
    email_address,
    primary_category,
    severity_level,
    employment_status
FROM applications
WHERE 1=1";

if (!empty($search)) { 
    $sql .= " AND (full_name LIKE '%$search%' OR application_number LIKE '%$search%' OR mykad LIKE '%$search%')"; 
}
if (!empty($filter_status)) { 
    $sql .= " AND application_status = '$filter_status'"; 
}
$sql .= " ORDER BY submission_date DESC";

$result = $conn->query($sql);
$rows = [];
while($r = $result->fetch_assoc()) { $rows[] = $r; }

// --- 6. GET REPORT DATA (for report generation) ---
$report_sql = "SELECT 
    application_id,
    application_number,
    application_status,
    submission_date,
    full_name,
    primary_category
FROM applications
ORDER BY submission_date DESC";

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
                            <option value="Under Review" <?= $filter_status == 'Under Review' ? 'selected' : '' ?>>Under Review</option>
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
                    <div class="app-category"><?= htmlspecialchars($row['primary_category'] ?? 'N/A') ?></div>
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
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $row['application_status'])) ?>"><?= $row['application_status'] ?></span>
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
                            <td><?= htmlspecialchars($app['primary_category'] ?? 'N/A') ?></td>
                            <td><?= date('d M Y', strtotime($app['submission_date'])) ?></td>
                            <td><span class="status-badge status-<?= strtolower($app['application_status']) ?>"><?= $app['application_status'] ?></span></td>
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