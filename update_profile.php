<?php
session_start();
include "db_conn.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    
    // Get form data
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $oku_number = mysqli_real_escape_string($conn, $_POST['oku_number']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $zip_code = mysqli_real_escape_string($conn, $_POST['zip_code']);
    
    // Handle Profile Picture Upload
    $profile_pic_path = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/profiles/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = time() . '_' . basename($_FILES['profile_pic']['name']);
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
            $profile_pic_path = $target_path;
        }
    }
    
    // Update query
    if ($profile_pic_path) {
        $sql = "UPDATE users SET 
                full_name = '$full_name',
                email = '$email',
                phone_number = '$phone_number',
                address = '$address',
                oku_number = '$oku_number',
                state = '$state',
                zip_code = '$zip_code',
                profile_picture = '$profile_pic_path'
                WHERE user_id = '$user_id'";
    } else {
        $sql = "UPDATE users SET 
                full_name = '$full_name',
                email = '$email',
                phone_number = '$phone_number',
                address = '$address',
                oku_number = '$oku_number',
                state = '$state',
                zip_code = '$zip_code'
                WHERE user_id = '$user_id'";
    }
    
    if (mysqli_query($conn, $sql)) {
        // Update session variables
        $_SESSION['full_name'] = $full_name;
        header("Location: profile.php?update_success=1");
        exit();
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
}
?>