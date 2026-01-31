<?php
session_start();
include "db_conn.php";

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role_type = $_POST['role_type'] ?? 'user';

    if ($role_type === 'admin') {
        // --- HARDCODED MASTER ADMIN ---
        if ($username === 'masteradmin' && $password === 'admin123') {
            $_SESSION['user_id'] = 0;
            $_SESSION['username'] = 'MasterAdmin';
            $_SESSION['role'] = 'admin';
            header("Location: admin_dashboard.php");
            exit();
        }

        // --- DATABASE ADMIN CHECK ---
        $sql = "SELECT * FROM admins WHERE admin_username='$username'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['user_id'] = $row['admin_id'];
                $_SESSION['username'] = $row['admin_username'];
                $_SESSION['role'] = 'admin';
                header("Location: admin_dashboard.php");
                exit();
            }
        }
        header("Location: login.php?error=Invalid Admin Credentials");
        exit();

    } else {
        // --- NORMAL USER LOGIN ---
        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = 'user';
                header("Location: home.php");
                exit();
            }
        }
        header("Location: login.php?error=Incorrect username or password");
        exit();
    }
}
?>