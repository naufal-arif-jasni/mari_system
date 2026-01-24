<?php
session_start();
include "db_conn.php";

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // --- ADMIN LOGIN CHECK ---
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['user_id'] = 'admin';
        $_SESSION['username'] = 'Admin';
        $_SESSION['role'] = 'admin';
        header("Location: admin_dashboard.php");
        exit();
    }

    // --- NORMAL USER LOGIN CHECK ---
    $username = mysqli_real_escape_string($conn, $username);
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['profile_picture'] = $row['profile_picture'];
            $_SESSION['role'] = 'user';
            header("Location: home.php");
            exit();
        }
    }
    header("Location: login.php?error=Incorrect username or password");
    exit();
}
?>