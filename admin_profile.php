<?php
session_start();
include "db_conn.php";

// Protect the page
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

// Logic to ADD a new admin
if (isset($_POST['add_admin'])) {
    $user = mysqli_real_escape_string($conn, $_POST['admin_username']);
    $pass = password_hash($_POST['password_hash'], PASSWORD_DEFAULT);
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $sql = "INSERT INTO admins (admin_username, password_hash, full_name,email) VALUES ('$user', '$pass', '$name','$email')";
    mysqli_query($conn, $sql);
    header("Location: admin_profile.php?success=1");
    exit();
}

// Logic to DELETE an admin
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM admins WHERE admin_id = $id");
    header("Location: admin_profile.php");
    exit();
}

$admins = mysqli_query($conn, "SELECT * FROM admins");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins - MARI System</title>
    <link rel="stylesheet" href="assets/css/admin-styles.css">
    <link rel="stylesheet" href="assets/css/adminprofile-styles.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-left">
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
            <a href="admin_dashboard.php" class="logo"><img src="images/logo_red.jpeg" alt="MARI Logo" style="height:40px;"></a>
        </div>
        <div class="nav-right">Admin Panel - <span>MARI System</span></div>
    </nav>

    <aside class="sidebar" id="sidebar">
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin.php">Applications</a></li>
            <li><a href="admin_profile.php" class="active">Manage Admins</a></li>
            <li><a href="?logout=true" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.2);">Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="admin-container">
            <h2 style="color: #800000; margin-bottom: 20px;">Manage System Administrators</h2>
            
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success" style="padding:10px; background:#d4edda; color:#155724; border-radius:5px; margin-bottom:20px;">
                    ✓ New Admin added successfully!
                </div>
            <?php endif; ?>

            <div class="add-admin-section" style="background:#f9f9f9; padding:20px; border-radius:8px; margin-bottom:30px; border:1px solid #eee;">
                <h4 style="margin-bottom:15px; color:#800000;">Register New Administrator</h4>
                <form method="POST" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <input type="text" name="admin_username" class="form-control" placeholder="Username" required style="flex:1; padding:10px;">
                    <input type="password" name="password_hash" class="form-control" placeholder="Password" required style="flex:1; padding:10px;">
                    <input type="text" name="full_name" class="form-control" placeholder="Full Name" required style="flex:2; padding:10px;">
                    <input type="email" name="email" class="form-control" placeholder="Email Address" required style="flex:2; padding:10px;">
                    <button type="submit" name="add_admin" class="btn-mari" style="padding:10px 20px; background:#800000; color:white; border:none; border-radius:4px; cursor:pointer;">Add Account</button>
                </form>
            </div>

            <div class="table-responsive" style="background:white; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
                <table style="width:100%; border-collapse:collapse;">
                    <thead style="background:#800000; color:white;">
                        <tr>
                            <th style="padding:15px; text-align:left;">Username</th>
                            <th style="padding:15px; text-align:left;">Full Name</th>
                            <th style="padding:15px; text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="background:#fff5f5; border-bottom:1px solid #eee;">
                            <td style="padding:15px;"><strong>masteradmin</strong></td>
                            <td style="padding:15px;">System Master Account</td>
                            <td style="padding:15px; text-align:center; color:#999; font-style:italic;">Protected</td>
                        </tr>
                        <?php while($row = mysqli_fetch_assoc($admins)): ?>
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:15px;"><?= htmlspecialchars($row['admin_username']) ?></td>
                            <td style="padding:15px;"><?= htmlspecialchars($row['full_name']) ?></td>

                            <td style="padding:15px; text-align:center;">
                                <a href="?delete=<?= $row['admin_id'] ?>" class="btn-delete" style="color:font-weight:bold;" onclick="return confirm('Delete this admin?')">Remove</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('active');
        }
    </script>
</body>
</html>