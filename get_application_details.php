<?php
/**
 * MARI SYSTEM - GET APPLICATION DETAILS
 */

session_start();

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "mari_system");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Get application ID
$app_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($app_id <= 0) {
    echo json_encode(['error' => 'Invalid application ID']);
    exit();
}

// Fetch data from the applications table
$response = [];

// Main Application
$app_query = "SELECT * FROM applications WHERE application_id = $app_id";
$app_result = $conn->query($app_query);

if ($app_result && $app_result->num_rows > 0) {
    $app_data = $app_result->fetch_assoc();
    
    // Organize data into logical sections for backward compatibility with frontend
    $response['application'] = [
        'application_id' => $app_data['application_id'],
        'user_id' => $app_data['user_id'],
        'application_number' => $app_data['application_number'],
        'status' => $app_data['application_status'],
        'submission_date' => $app_data['submission_date'],
        'last_updated' => $app_data['last_updated'],
        'reviewed_by' => $app_data['reviewed_by'],
        'reviewed_at' => $app_data['reviewed_at'],
        'admin_remarks' => $app_data['admin_remarks']
    ];
    
    // Applicant Details section
    $response['applicant'] = [
        'full_name' => $app_data['full_name'],
        'mykad' => $app_data['mykad'],
        'date_of_birth' => $app_data['date_of_birth'],
        'age' => $app_data['age'],
        'gender' => $app_data['gender'],
        'nationality' => $app_data['nationality'],
        'oku_card_number' => $app_data['oku_card_number'],
        'phone_number' => $app_data['phone_number'],
        'email_address' => $app_data['email_address'],
        'residential_address' => $app_data['residential_address'],
        'state' => $app_data['state'],
        'zip_code' => $app_data['zip_code'],
        'marital_status' => $app_data['marital_status'],
        'education_level' => $app_data['education_level']
    ];
    
    // Guardian Details section
    $response['guardian'] = [
        'is_required' => $app_data['guardian_required'],
        'guardian_full_name' => $app_data['guardian_full_name'],
        'guardian_ic_number' => $app_data['guardian_ic_number'],
        'relationship' => $app_data['guardian_relationship'],
        'guardian_phone_number' => $app_data['guardian_phone_number'],
        'guardian_email' => $app_data['guardian_email'],
        'legal_authority_declaration' => $app_data['legal_authority_declaration']
    ];
    
    // Disability Details section
    $response['disability'] = [
        'primary_category' => $app_data['primary_category'],
        'sub_category' => $app_data['sub_category'],
        'diagnosis_date' => $app_data['diagnosis_date'],
        'severity_level' => $app_data['severity_level'],
        'diagnosed_by' => $app_data['diagnosed_by'],
        'hospital_clinic' => $app_data['hospital_clinic'],
        'additional_notes' => $app_data['disability_additional_notes']
    ];
    
    // Functional Impact section
    $response['impact'] = [
        'mobility_mode' => $app_data['mobility_mode'],
        'assistive_devices' => $app_data['assistive_devices'],
        'adl_independence' => $app_data['adl_independence'],
        'communication_method' => $app_data['communication_method'],
        'employment_status' => $app_data['employment_status'],
        'monthly_income' => $app_data['monthly_income'],
        'special_requirements' => $app_data['special_requirements']
    ];
    
    // Declaration section
    $response['declaration'] = [
        'accuracy_confirmed' => $app_data['accuracy_confirmed'],
        'consent_given' => $app_data['consent_given'],
        'digital_signature' => $app_data['digital_signature'],
        'signature_date' => $app_data['signature_date'],
        'ip_address' => $app_data['signature_ip_address'],
        'user_agent' => $app_data['user_agent'],
        'terms_accepted' => $app_data['terms_accepted']
    ];
    
    // Documents section - convert paths to array format
    $response['documents'] = [];
    $doc_types = [
        'Medical Report' => $app_data['document_medical_report_path'],
        'Specialist Form' => $app_data['document_specialist_form_path'],
        'OKU Card' => $app_data['document_oku_card_path'],
        'IC Copy' => $app_data['document_ic_copy_path'],
        'Photo' => $app_data['document_photo_path'],
        'Other' => $app_data['document_other_path']
    ];
    
    foreach ($doc_types as $type => $path) {
        if (!empty($path)) {
            $response['documents'][] = [
                'document_type' => $type,
                'file_path' => $path,
                'file_name' => basename($path),
                'file_extension' => pathinfo($path, PATHINFO_EXTENSION)
            ];
        }
    }
    
} else {
    echo json_encode(['error' => 'Application not found']);
    exit();
}

// Status History
$history_query = "SELECT * FROM status WHERE application_id = $app_id ORDER BY changed_at DESC";
$history_result = $conn->query($history_query);
$response['history'] = [];
if ($history_result && $history_result->num_rows > 0) {
    while ($hist = $history_result->fetch_assoc()) {
        $response['history'][] = $hist;
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>