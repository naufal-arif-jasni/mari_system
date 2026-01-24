<?php
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Fetch user data from database to auto-fill form
include "db_conn.php";
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
} else {
    $user_data = [
        'full_name' => '',
        'ic_number' => '',
        'email' => '',
        'phone_number' => '',
        'address' => '',
        'state' => '',
        'zip_code' => '',
        'oku_number' => ''
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image" href="images/logo_red.jpeg">
    <title>MARI - Application Form</title>
    
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Imperial+Script&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Styles -->
<link rel="stylesheet" href="assets/css/applicationform-styles.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-left">
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
            <a href="home.php" class="logo">
                <img src="images/logo.png" alt="MARI Logo">
            </a>
        </div>
        <div class="nav-right">
            Welcome, <span><?php echo htmlspecialchars($username); ?></span>
        </div>
    </nav>

    <aside class="sidebar" id="sidebar">
        <ul class="sidebar-menu">
            <li><a href="home.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="application.php">Application</a></li>
            <li><a href="index.php" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.2);">Logout</a></li>
        </ul>
    </aside>

    <main class="main-content" id="main-content">
        <div class="container-fluid">
            <div class="text-center mb-4">
                <h2>Disability Aid Registration</h2>
                <p class="text-muted">Please complete all sections below accurately.</p>
            </div>

            <form action="submit_application.php" method="POST" enctype="multipart/form-data">

                <!-- SECTION 1: APPLICANT / PERSONAL IDENTIFICATION -->
                <div class="card shadow mb-4">
                    <div class="section-header bg-primary">Section 1: Applicant / Personal Identification (Mandatory)</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name (as in IC) <span class="required-asterisk">*</span></label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">National ID (MyKad) <span class="required-asterisk">*</span></label>
                                <input type="text" name="nric_number" class="form-control" placeholder="900101-14-1234" value="<?php echo htmlspecialchars($user_data['ic_number']); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date of Birth <span class="required-asterisk">*</span></label>
                                <input type="date" name="date_of_birth" id="dob" class="form-control" required onchange="checkAge()">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Gender <span class="required-asterisk">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="">Select...</option>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nationality <span class="required-asterisk">*</span></label>
                                <input type="text" name="nationality" class="form-control" value="Malaysian" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">OKU Card Number <span class="required-asterisk">*</span></label>
                                <input type="text" name="oku_number" class="form-control" placeholder="Enter OKU Card Number" value="<?php echo htmlspecialchars($user_data['oku_number'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number <span class="required-asterisk">*</span></label>
                                <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($user_data['phone_number']); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email_address" class="form-control" value="<?php echo htmlspecialchars($user_data['email']); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Residential Address <span class="required-asterisk">*</span></label>
                            <textarea name="residental_address" class="form-control" rows="2" required><?php echo htmlspecialchars($user_data['address'] ?? ''); ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">State <span class="required-asterisk">*</span></label>
                                <select name="state" class="form-select" required>
                                    <option value="">Select...</option>
                                    <option value="Johor" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Johor') ? 'selected' : ''; ?>>Johor</option>
                                    <option value="Kedah" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Kedah') ? 'selected' : ''; ?>>Kedah</option>
                                    <option value="Kelantan" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Kelantan') ? 'selected' : ''; ?>>Kelantan</option>
                                    <option value="Melaka" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Melaka') ? 'selected' : ''; ?>>Melaka</option>
                                    <option value="Negeri Sembilan" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Negeri Sembilan') ? 'selected' : ''; ?>>Negeri Sembilan</option>
                                    <option value="Pahang" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Pahang') ? 'selected' : ''; ?>>Pahang</option>
                                    <option value="Perak" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Perak') ? 'selected' : ''; ?>>Perak</option>
                                    <option value="Perlis" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Perlis') ? 'selected' : ''; ?>>Perlis</option>
                                    <option value="Pulau Pinang" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Pulau Pinang') ? 'selected' : ''; ?>>Pulau Pinang</option>
                                    <option value="Sabah" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Sabah') ? 'selected' : ''; ?>>Sabah</option>
                                    <option value="Sarawak" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Sarawak') ? 'selected' : ''; ?>>Sarawak</option>
                                    <option value="Selangor" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Selangor') ? 'selected' : ''; ?>>Selangor</option>
                                    <option value="Terengganu" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Terengganu') ? 'selected' : ''; ?>>Terengganu</option>
                                    <option value="Kuala Lumpur" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Kuala Lumpur') ? 'selected' : ''; ?>>Kuala Lumpur</option>
                                    <option value="Labuan" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Labuan') ? 'selected' : ''; ?>>Labuan</option>
                                    <option value="Putrajaya" <?php echo (isset($user_data['state']) && $user_data['state'] == 'Putrajaya') ? 'selected' : ''; ?>>Putrajaya</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Zip Code <span class="required-asterisk">*</span></label>
                                <input type="text" name="zip_code" class="form-control" placeholder="Enter zip code" value="<?php echo htmlspecialchars($user_data['zip_code'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Marital Status</label>
                                <select name="marital_status" class="form-select">
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Divorced">Divorced</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Education Level</label>
                                <select name="education_level" class="form-select">
                                    <option value="None">None</option>
                                    <option value="SPM">SPM</option>
                                    <option value="Diploma">Diploma</option>
                                    <option value="Degree">Degree</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="require_rep" onchange="toggleCaregiver()">
                            <label class="form-check-label" for="require_rep">
                                I require a representative/caregiver (or I am under 18)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: GUARDIAN / CAREGIVER INFO -->
                <div class="card shadow mb-4 hidden" id="section2">
                    <div class="section-header bg-warning text-dark">Section 2: Guardian / Caregiver Information</div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <small>Required because applicant is under 18 or requested representation.</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guardian Full Name</label>
                                <input type="text" name="cg_full_name" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guardian IC Number</label>
                                <input type="text" name="cg_ic_number" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Relationship to Applicant</label>
                                <select name="relationship" class="form-select">
                                    <option value="">Select...</option>
                                    <option value="Parent">Parent</option>
                                    <option value="Spouse">Spouse</option>
                                    <option value="Sibling">Sibling</option>
                                    <option value="Legal Guardian">Legal Guardian</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guardian Phone Number</label>
                                <input type="text" name="cg_phone_number" class="form-control">
                            </div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="declaration_authority" value="1">
                            <label class="form-check-label">
                                I declare I have the legal authority to act on behalf of the applicant.
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: DISABILITY CLASSIFICATION -->
                <div class="card shadow mb-4">
                    <div class="section-header bg-danger text-white">Section 3: Primary Disability Classification</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Primary Category <span class="required-asterisk">*</span></label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="primary_category" value="Physical" required> <label class="form-check-label">Physical</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="primary_category" value="Visual"> <label class="form-check-label">Visual</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="primary_category" value="Hearing"> <label class="form-check-label">Hearing</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="primary_category" value="Learning"> <label class="form-check-label">Learning</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="primary_category" value="Mental"> <label class="form-check-label">Mental</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Sub-Category (if applicable)</label>
                            <input type="text" name="sub_category" class="form-control" placeholder="e.g. Down Syndrome, Specific Learning Disorder">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Diagnosis Date <span class="required-asterisk">*</span></label>
                                <input type="date" name="diagnosis_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Severity Assessment <span class="required-asterisk">*</span></label>
                                <select name="severity_assessment" class="form-select" required>
                                    <option value="">Select...</option>
                                    <option value="Mild">Mild (Affects one area)</option>
                                    <option value="Moderate">Moderate (Requires assistance)</option>
                                    <option value="Severe">Severe (Fully dependent)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload Medical Report (PDF/JPEG) <span class="required-asterisk">*</span></label>
                            <input type="file" name="medical_report" class="form-control" accept=".pdf,.jpg,.jpeg" required>
                            <small class="text-muted">Must be dated within last 2 years.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload Specialist Confirmation Form (PDF only) <span class="required-asterisk">*</span></label>
                            <input type="file" name="specialist_form" class="form-control" accept=".pdf" required>
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: FUNCTIONAL IMPACT -->
                <div class="card shadow mb-4">
                    <div class="section-header bg-success text-white">Section 4: Functional Impact & Specific Needs</div>
                    <div class="card-body">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Primary Mode of Mobility</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mobility_mode" value="Unassisted" checked> <label>Unassisted</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mobility_mode" value="Cane/Crutch"> <label>Cane/Crutch</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mobility_mode" value="Wheelchair"> <label>Wheelchair</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mobility_mode" value="Bedridden"> <label>Fully Bedridden</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Assistive Devices Required</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="assistive[]" value="Hearing Aid"> <label>Hearing Aid</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="assistive[]" value="Prosthetic"> <label>Prosthetic</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="assistive[]" value="Oxygen Tank"> <label>Oxygen Tank</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="assistive[]" value="None"> <label>None</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Daily Living Activity Independence</label>
                            <select name="adl_independence" class="form-select" required>
                                <option value="Independent">Independent</option>
                                <option value="Needs Some Help">Needs Some Help (Dressing, Feeding)</option>
                                <option value="Fully Dependent">Fully Dependent</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Primary Communication Method</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="comm_method" value="Verbal" checked> <label>Verbal/Spoken</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="comm_method" value="Sign Language"> <label>Sign Language</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="comm_method" value="Non-Verbal"> <label>Non-Verbal/Written</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Employment Status</label>
                            <select name="employment_status" class="form-select">
                                <option value="Unemployed">Unemployed</option>
                                <option value="Employed Full-Time">Employed Full-Time</option>
                                <option value="Employed Part-Time">Employed Part-Time</option>
                                <option value="Student">Student</option>
                                <option value="Sheltered Workshop">Sheltered Workshop</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECTION 5: DECLARATION -->
                <div class="card shadow mb-4">
                    <div class="section-header bg-dark text-white">Section 5: Declaration & Consent</div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="accuracy_check" required>
                            <label class="form-check-label">
                                <strong>Accuracy:</strong> I certify that all information provided in this application is true and correct.
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="consent_check" required>
                            <label class="form-check-label">
                                <strong>Consent:</strong> I consent to the disclosure of my personal/medical info to government bodies for verification purposes.
                            </label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Digital Signature / Full Name</label>
                            <input type="text" name="signature" class="form-control imperial-script-regular" placeholder="Type full name to sign" required>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 mb-5">
                    <button type="submit" name="submit_full_app" class="btn btn-primary btn-lg">Submit Application</button>
                    <a href="application.php" class="btn btn-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </main>
    <script>

    <script src="assets/js/common.js"></script>
    <script src="assets/js/applicationform.js"></script>
</body>
</html>