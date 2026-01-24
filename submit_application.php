<?php
session_start();
include "db_conn.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit_full_app'])) {
    $user_id = $_SESSION['user_id'];
    
    // Start transaction for data integrity
    mysqli_begin_transaction($conn);
    
    try {
        // ============================================
        // 1. GENERATE APPLICATION NUMBER
        // ============================================
        $year = date('Y');
        $seq_query = "SELECT COALESCE(MAX(CAST(SUBSTRING(application_number, -6) AS UNSIGNED)), 0) + 1 AS next_seq
                      FROM applications WHERE application_number LIKE 'MARI-$year-%'";
        $seq_result = mysqli_query($conn, $seq_query);
        $seq_row = mysqli_fetch_assoc($seq_result);
        $application_number = 'MARI-' . $year . '-' . str_pad($seq_row['next_seq'], 6, '0', STR_PAD_LEFT);
        
        // ============================================
        // 2. INSERT INTO APPLICATIONS (Master Table)
        // ============================================
        $insert_app = "INSERT INTO applications (user_id, application_number, status, submission_date) 
                       VALUES (?, ?, 'Pending', NOW())";
        $stmt = mysqli_prepare($conn, $insert_app);
        mysqli_stmt_bind_param($stmt, "is", $user_id, $application_number);
        mysqli_stmt_execute($stmt);
        $application_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        
        // ============================================
        // 3. INSERT APPLICANT DETAILS
        // ============================================
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $mykad = mysqli_real_escape_string($conn, $_POST['nric_number']);
        $dob = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);
        $oku_card = mysqli_real_escape_string($conn, $_POST['oku_number'] ?? '');
        $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
        $email = mysqli_real_escape_string($conn, $_POST['email_address'] ?? '');
        $address = mysqli_real_escape_string($conn, $_POST['residental_address']);
        $state = mysqli_real_escape_string($conn, $_POST['state']);
        $zip = mysqli_real_escape_string($conn, $_POST['zip_code']);
        $marital = mysqli_real_escape_string($conn, $_POST['marital_status']);
        $education = mysqli_real_escape_string($conn, $_POST['education_level']);
        
        $insert_applicant = "INSERT INTO applicant_details 
            (application_id, full_name, mykad, date_of_birth, gender, nationality, oku_card_number,
             phone_number, email_address, residential_address, state, zip_code, marital_status, education_level)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $insert_applicant);
        mysqli_stmt_bind_param($stmt, "isssssssssssss", 
            $application_id, $full_name, $mykad, $dob, $gender, $nationality, $oku_card,
            $phone, $email, $address, $state, $zip, $marital, $education
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // ============================================
        // 4. INSERT GUARDIAN DETAILS (if provided)
        // ============================================
        $is_guardian_required = isset($_POST['cg_full_name']) && !empty($_POST['cg_full_name']);
        
        $cg_name = mysqli_real_escape_string($conn, $_POST['cg_full_name'] ?? '');
        $cg_ic = mysqli_real_escape_string($conn, $_POST['cg_ic_number'] ?? '');
        $relationship = mysqli_real_escape_string($conn, $_POST['relationship'] ?? '');
        $cg_phone = mysqli_real_escape_string($conn, $_POST['cg_phone_number'] ?? '');
        $legal_dec = isset($_POST['declaration_authority']) ? 1 : 0;
        
        $insert_guardian = "INSERT INTO guardians 
            (application_id, is_required, guardian_full_name, guardian_ic_number, relationship, 
             guardian_phone_number, legal_authority_declaration)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $insert_guardian);
        mysqli_stmt_bind_param($stmt, "iissssi", 
            $application_id, $is_guardian_required, $cg_name, $cg_ic, $relationship, $cg_phone, $legal_dec
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // ============================================
        // 5. INSERT DISABILITY DETAILS
        // ============================================
        $primary_cat = mysqli_real_escape_string($conn, $_POST['primary_category']);
        $sub_cat = mysqli_real_escape_string($conn, $_POST['sub_category'] ?? '');
        $diagnosis_date = mysqli_real_escape_string($conn, $_POST['diagnosis_date']);
        $severity = mysqli_real_escape_string($conn, $_POST['severity_assessment']);
        
        $insert_disability = "INSERT INTO disability_details 
            (application_id, primary_category, sub_category, diagnosis_date, severity_level)
            VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $insert_disability);
        mysqli_stmt_bind_param($stmt, "issss", 
            $application_id, $primary_cat, $sub_cat, $diagnosis_date, $severity
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // ============================================
        // 6. INSERT FUNCTIONAL IMPACT
        // ============================================
        $mobility = mysqli_real_escape_string($conn, $_POST['mobility_mode']);
        $adl = mysqli_real_escape_string($conn, $_POST['adl_independence']);
        $comm = mysqli_real_escape_string($conn, $_POST['comm_method']);
        $employment = mysqli_real_escape_string($conn, $_POST['employment_status']);
        
        // Handle assistive devices (array to SET format)
        $assistive_str = "";
        if(isset($_POST['assistive']) && is_array($_POST['assistive'])){
            $assistive_arr = array_map(function($item) use ($conn) {
                return mysqli_real_escape_string($conn, $item);
            }, $_POST['assistive']);
            $assistive_str = implode(",", $assistive_arr);
        }
        
        $insert_impact = "INSERT INTO functional_impact 
            (application_id, mobility_mode, assistive_devices, adl_independence, 
             communication_method, employment_status)
            VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $insert_impact);
        mysqli_stmt_bind_param($stmt, "isssss", 
            $application_id, $mobility, $assistive_str, $adl, $comm, $employment
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // ============================================
        // 7. HANDLE FILE UPLOADS (Documents Table)
        // ============================================
        $upload_dir = "uploads/documents/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Medical Report
        if(isset($_FILES['medical_report']) && $_FILES['medical_report']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['medical_report'];
            $file_name = $file['name'];
            $file_tmp = $file['tmp_name'];
            $file_size = $file['size'];
            $file_type = $file['type'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            $new_filename = time() . "_medical_" . $application_id . "." . $file_ext;
            $file_path = $upload_dir . $new_filename;
            
            if(move_uploaded_file($file_tmp, $file_path)) {
                // Optionally read file into BLOB
                $file_data = file_get_contents($file_path);
                
                $insert_doc = "INSERT INTO documents 
                    (application_id, document_type, file_name, file_path, file_size, file_type, file_extension, file_data)
                    VALUES (?, 'Medical Report', ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $insert_doc);
                mysqli_stmt_bind_param($stmt, "ississs", 
                    $application_id, $file_name, $file_path, $file_size, $file_type, $file_ext, $file_data
                );
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        
        // Specialist Form
        if(isset($_FILES['specialist_form']) && $_FILES['specialist_form']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['specialist_form'];
            $file_name = $file['name'];
            $file_tmp = $file['tmp_name'];
            $file_size = $file['size'];
            $file_type = $file['type'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            $new_filename = time() . "_specialist_" . $application_id . "." . $file_ext;
            $file_path = $upload_dir . $new_filename;
            
            if(move_uploaded_file($file_tmp, $file_path)) {
                $file_data = file_get_contents($file_path);
                
                $insert_doc = "INSERT INTO documents 
                    (application_id, document_type, file_name, file_path, file_size, file_type, file_extension, file_data)
                    VALUES (?, 'Specialist Form', ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $insert_doc);
                mysqli_stmt_bind_param($stmt, "ississs", 
                    $application_id, $file_name, $file_path, $file_size, $file_type, $file_ext, $file_data
                );
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        
        // ============================================
        // 8. INSERT DECLARATION
        // ============================================
        $signature = mysqli_real_escape_string($conn, $_POST['signature']);
        $accuracy = isset($_POST['accuracy_check']) ? 1 : 0;
        $consent = isset($_POST['consent_check']) ? 1 : 0;
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        $insert_declaration = "INSERT INTO declarations 
            (application_id, accuracy_confirmed, consent_given, digital_signature, ip_address, user_agent, terms_accepted)
            VALUES (?, ?, ?, ?, ?, ?, 1)";
        
        $stmt = mysqli_prepare($conn, $insert_declaration);
        mysqli_stmt_bind_param($stmt, "iiisss", 
            $application_id, $accuracy, $consent, $signature, $ip_address, $user_agent
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // ============================================
        // 9. LOG ACTIVITY
        // ============================================
        $activity_log = "INSERT INTO activity_log 
            (user_id, application_id, activity_type, description, ip_address)
            VALUES (?, ?, 'Create', 'New application submitted', ?)";
        
        $stmt = mysqli_prepare($conn, $activity_log);
        mysqli_stmt_bind_param($stmt, "iis", $user_id, $application_id, $ip_address);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // ============================================
        // COMMIT TRANSACTION
        // ============================================
        mysqli_commit($conn);
        
        echo "<script>alert('Application Submitted Successfully!\\nApplication Number: $application_number'); window.location.href='success_landing.php?application_success=1';</script>";
        exit();
        
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        echo "<script>alert('Error submitting application: " . $e->getMessage() . "'); window.history.back();</script>";
        exit();
    }
    
    mysqli_close($conn);
} else {
    header("Location: success_landing.php");
    exit();
}
?>