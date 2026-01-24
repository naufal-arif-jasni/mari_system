<?php
/**
 * MARI SYSTEM - GET APPLICATION DETAILS
 * AJAX endpoint to fetch complete application data from normalized tables
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

// Fetch data from all tables
$response = [];

// 1. Main Application
$app_query = "SELECT * FROM applications WHERE application_id = $app_id";
$app_result = $conn->query($app_query);
if ($app_result && $app_result->num_rows > 0) {
    $response['application'] = $app_result->fetch_assoc();
} else {
    echo json_encode(['error' => 'Application not found']);
    exit();
}

// 2. Applicant Details
$applicant_query = "SELECT * FROM applicant_details WHERE application_id = $app_id";
$applicant_result = $conn->query($applicant_query);
$response['applicant'] = $applicant_result && $applicant_result->num_rows > 0 
    ? $applicant_result->fetch_assoc() 
    : null;

// 3. Guardian Details
$guardian_query = "SELECT * FROM guardians WHERE application_id = $app_id";
$guardian_result = $conn->query($guardian_query);
$response['guardian'] = $guardian_result && $guardian_result->num_rows > 0 
    ? $guardian_result->fetch_assoc() 
    : null;

// 4. Disability Details
$disability_query = "SELECT * FROM disability_details WHERE application_id = $app_id";
$disability_result = $conn->query($disability_query);
$response['disability'] = $disability_result && $disability_result->num_rows > 0 
    ? $disability_result->fetch_assoc() 
    : null;

// 5. Functional Impact
$impact_query = "SELECT * FROM functional_impact WHERE application_id = $app_id";
$impact_result = $conn->query($impact_query);
$response['impact'] = $impact_result && $impact_result->num_rows > 0 
    ? $impact_result->fetch_assoc() 
    : null;

// 6. Declaration
$declaration_query = "SELECT * FROM declarations WHERE application_id = $app_id";
$declaration_result = $conn->query($declaration_query);
$response['declaration'] = $declaration_result && $declaration_result->num_rows > 0 
    ? $declaration_result->fetch_assoc() 
    : null;

// 7. Documents (multiple records)
$documents_query = "SELECT * FROM documents WHERE application_id = $app_id ORDER BY uploaded_at DESC";
$documents_result = $conn->query($documents_query);
$response['documents'] = [];
if ($documents_result && $documents_result->num_rows > 0) {
    while ($doc = $documents_result->fetch_assoc()) {
        // Don't send BLOB data via JSON (too large)
        unset($doc['file_data']);
        $response['documents'][] = $doc;
    }
}

// 8. Status History (multiple records)
$history_query = "SELECT * FROM status_history WHERE application_id = $app_id ORDER BY changed_at DESC";
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