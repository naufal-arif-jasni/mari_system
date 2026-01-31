<?php
session_start();
include "db_conn.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit_full_app'])) {
    $user_id = $_SESSION['user_id'];
    mysqli_begin_transaction($conn);
    
    try {
        // 1. Generate Application Number
        $year = date('Y');
        $seq_query = "SELECT COALESCE(MAX(CAST(SUBSTRING(application_number, -6) AS UNSIGNED)), 0) + 1 AS next_seq
                      FROM application_history WHERE application_number LIKE 'MARI-$year-%'";
        $seq_result = mysqli_query($conn, $seq_query);
        $seq_row = mysqli_fetch_assoc($seq_result);
        $application_number = 'MARI-' . $year . '-' . str_pad($seq_row['next_seq'], 6, '0', STR_PAD_LEFT);
        
        // 2. Main History Record
        $application_status = 'Pending';
        $history_sql = "INSERT INTO application_history (user_id, application_number, status, submission_date) VALUES (?, ?, ?, NOW())";
        $stmt_h = mysqli_prepare($conn, $history_sql);
        mysqli_stmt_bind_param($stmt_h, "iss", $user_id, $application_number, $application_status);
        mysqli_stmt_execute($stmt_h);
        $application_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt_h);
        
        // 3. Prepare Data Variables
        $full_name      = $_POST['full_name'];
        $mykad          = $_POST['nric_number'];
        $dob            = $_POST['date_of_birth'];
        $gender         = $_POST['gender'];
        $nationality    = $_POST['nationality'];
        $oku_card       = $_POST['oku_number'] ?? '';
        $phone          = $_POST['phone_number'];
        $email          = $_POST['email_address'] ?? '';
        $address        = $_POST['residental_address'];
        $state          = $_POST['state'];
        $zip            = $_POST['zip_code'];
        $marital        = $_POST['marital_status'];
        $education      = $_POST['education_level'];
        
        $guardian_req   = !empty($_POST['cg_full_name']) ? 1 : 0;
        $cg_name        = $_POST['cg_full_name'] ?? '';
        $cg_ic          = $_POST['cg_ic_number'] ?? '';
        $relationship   = $_POST['relationship'] ?? '';
        $cg_phone       = $_POST['cg_phone_number'] ?? '';
        $cg_email       = $_POST['cg_email'] ?? '';
        $legal_dec      = isset($_POST['declaration_authority']) ? 1 : 0;
        
        $primary_cat    = $_POST['primary_category'];
        $sub_cat        = $_POST['sub_category'] ?? '';
        $diag_date      = $_POST['diagnosis_date'];
        $severity       = $_POST['severity_assessment'];
        $diag_by        = $_POST['diagnosed_by'] ?? '';
        $hospital       = $_POST['hospital_clinic'] ?? '';
        $notes          = $_POST['disability_notes'] ?? '';
        
        $mobility       = $_POST['mobility_mode'];
        $adl            = $_POST['adl_independence'];
        $comm           = $_POST['comm_method'];
        $employment     = $_POST['employment_status'];
        $income         = floatval($_POST['monthly_income'] ?? 0);
        $special        = $_POST['special_requirements'] ?? '';
        $assistive_str  = (isset($_POST['assistive']) && is_array($_POST['assistive'])) ? implode(",", $_POST['assistive']) : "";
        
        $accuracy       = isset($_POST['accuracy_check']) ? 1 : 0;
        $consent        = isset($_POST['consent_check']) ? 1 : 0;
        $sig            = $_POST['signature'];
        $ip             = $_SERVER['REMOTE_ADDR'];
        $ua             = $_SERVER['HTTP_USER_AGENT'];
        $terms_val      = 1;

        // 4. Handle Files
        $upload_dir = "uploads/documents/";
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        $paths = ['med'=>null, 'spec'=>null, 'oku'=>null, 'ic'=>null, 'photo'=>null, 'other'=>null];
        $map = ['medical_report'=>'med', 'specialist_form'=>'spec', 'oku_card'=>'oku', 'ic_copy'=>'ic', 'photo'=>'photo', 'other_doc'=>'other'];
        foreach($map as $key => $p) {
            if(isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
                $fn = time()."_".$p."_".rand(100,999).".".$ext;
                if(move_uploaded_file($_FILES[$key]['tmp_name'], $upload_dir.$fn)) $paths[$p] = $upload_dir.$fn;
            }
        }

// ============================================
// 5. DYNAMIC INSERT
// ============================================

// Define data in a associative array: "column_name" => ["type", value]
$data_map = [
    "application_id"                => ["i", $application_id],
    "full_name"                     => ["s", $full_name],
    "mykad"                         => ["s", $mykad],
    "date_of_birth"                 => ["s", $dob],
    "gender"                        => ["s", $gender],
    "nationality"                   => ["s", $nationality],
    "oku_card_number"               => ["s", $oku_card],
    "phone_number"                  => ["s", $phone],
    "email_address"                 => ["s", $email],
    "residential_address"           => ["s", $address],
    "state"                         => ["s", $state],
    "zip_code"                      => ["s", $zip],
    "marital_status"                => ["s", $marital],
    "education_level"               => ["s", $education],
    "guardian_required"             => ["i", $guardian_req],
    "guardian_full_name"            => ["s", $cg_name],
    "guardian_ic_number"            => ["s", $cg_ic],
    "guardian_relationship"         => ["s", $relationship],
    "guardian_phone_number"         => ["s", $cg_phone],
    "guardian_email"                => ["s", $cg_email],
    "legal_authority_declaration"   => ["i", $legal_dec],
    "primary_category"              => ["s", $primary_cat],
    "sub_category"                  => ["s", $sub_cat],
    "diagnosis_date"                => ["s", $diag_date],
    "severity_level"                => ["s", $severity],
    "diagnosed_by"                  => ["s", $diag_by],
    "hospital_clinic"               => ["s", $hospital],
    "disability_additional_notes"   => ["s", $notes],
    "mobility_mode"                 => ["s", $mobility],
    "assistive_devices"             => ["s", $assistive_str],
    "adl_independence"              => ["s", $adl],
    "communication_method"          => ["s", $comm],
    "employment_status"             => ["s", $employment],
    "monthly_income"                => ["d", $income],
    "special_requirements"          => ["s", $special],
    "document_medical_report_path"  => ["s", $paths['med']],
    "document_specialist_form_path" => ["s", $paths['spec']],
    "document_oku_card_path"        => ["s", $paths['oku']],
    "document_ic_copy_path"         => ["s", $paths['ic']],
    "document_photo_path"           => ["s", $paths['photo']],
    "document_other_path"           => ["s", $paths['other']],
    "accuracy_confirmed"            => ["i", $accuracy],
    "consent_given"                 => ["i", $consent],
    "digital_signature"             => ["s", $sig],
    "signature_ip_address"          => ["s", $ip],
    "user_agent"                    => ["s", $ua],
    "terms_accepted"                => ["i", $terms_val]
];

// Automatically build the SQL strings
$columns = implode(", ", array_keys($data_map));
$placeholders = implode(", ", array_fill(0, count($data_map), "?"));
$types = "";
$values = [];

foreach ($data_map as $col => $info) {
    $types .= $info[0];
    $values[] = $info[1];
}

$details_sql = "INSERT INTO applicant_details ($columns) VALUES ($placeholders)";
$stmt = mysqli_prepare($conn, $details_sql);

// Bind the array dynamically
mysqli_stmt_bind_param($stmt, $types, ...$values);

if (!mysqli_stmt_execute($stmt)) {
    throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
}
mysqli_stmt_close($stmt);

        // 6. Log
        $log_sql = "INSERT INTO activity_log (user_id, application_id, activity_type, description, ip_address) VALUES (?, ?, 'Create', 'New application submitted', ?)";
        $stmt_l = mysqli_prepare($conn, $log_sql);
        mysqli_stmt_bind_param($stmt_l, "iis", $user_id, $application_id, $ip);
        mysqli_stmt_execute($stmt_l);
        mysqli_stmt_close($stmt_l);

        mysqli_commit($conn);
        echo "<script>alert('Application Submitted Successfully!'); window.location.href='success_landing.php?application_success=1';</script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>