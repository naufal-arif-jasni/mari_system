<?php
$sname = "localhost";
$uname = "root";
$password = ""; // Default XAMPP/Laragon password is usually empty
$db_name = "mari_system"; // Changed to mari_system

$conn = mysqli_connect($sname, $uname, $password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>