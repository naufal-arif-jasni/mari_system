/**
 * MARI SYSTEM - ADMIN APPLICATION MANAGEMENT (NEW DB STRUCTURE)
 * CRUD operations for normalized database structure
 */

// Global variable to store applications data
let applications = [];

/**
 * Set Applications Data
 */
function setApplicationsData(data) {
    applications = data;
    console.log('Admin.js loaded - Applications data set:', applications.length, 'applications');
}

/**
 * VIEW - View Complete Application Details
 * Fetches complete data from server via AJAX
 */
function viewApplication(id) {
    console.log('viewApplication called with ID:', id);
    
    if (!applications || applications.length === 0) {
        alert('Error: No application data loaded.');
        return;
    }
    
    // Show loading state
    document.getElementById('viewContent').innerHTML = `
        <div class="modal-header">
            <h3>Loading...</h3>
            <button class="modal-close" onclick="closeModal('viewModal')">&times;</button>
        </div>
        <div class="modal-body" style="text-align: center; padding: 50px;">
            <p>Loading application details...</p>
        </div>
    `;
    document.getElementById('viewModal').classList.add('active');
    
    // Fetch complete application data via AJAX
    fetch(`get_application_details.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                closeModal('viewModal');
                return;
            }
            
            displayApplicationDetails(data);
        })
        .catch(error => {
            console.error('Error fetching application:', error);
            alert('Error loading application details');
            closeModal('viewModal');
        });
}

/**
 * Display Complete Application Details
 */
function displayApplicationDetails(data) {
    const app = data.application;
    const applicant = data.applicant;
    const guardian = data.guardian;
    const disability = data.disability;
    const impact = data.impact;
    const declaration = data.declaration;
    const documents = data.documents;
    const history = data.history;
    
    let content = `
        <div class="modal-header">
            <h3>Application Details - ${app.application_number}</h3>
            <button class="modal-close" onclick="closeModal('viewModal')">&times;</button>
        </div>
        <div class="modal-body">
    `;
    
    // Documents Section
    if (documents && documents.length > 0) {
        content += `
            <div class="section-title">📄 Uploaded Documents</div>
            <div class="detail-grid">
        `;
        
        documents.forEach(doc => {
            content += `
                <div class="detail-item">
                    <div class="detail-label">${doc.document_type}</div>
                    <div class="file-preview">
                        ${generateFilePreview(doc)}
                    </div>
                </div>
            `;
        });
        
        content += `</div>`;
    }
    
    // Personal Information
    content += `
        <div class="section-title">👤 Personal Information</div>
        <div class="detail-grid">
            <div class="detail-item"><div class="detail-label">Full Name</div><div class="detail-value">${applicant.full_name || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">MyKad Number</div><div class="detail-value">${applicant.mykad || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">Date of Birth</div><div class="detail-value">${applicant.date_of_birth || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">Age</div><div class="detail-value">${applicant.age || 'N/A'} years</div></div>
            <div class="detail-item"><div class="detail-label">Gender</div><div class="detail-value">${applicant.gender || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">Nationality</div><div class="detail-value">${applicant.nationality || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">OKU Card Number</div><div class="detail-value">${applicant.oku_card_number || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">Phone</div><div class="detail-value">${applicant.phone_number || 'N/A'}</div></div>
            <div class="detail-item full"><div class="detail-label">Email</div><div class="detail-value">${applicant.email_address || 'N/A'}</div></div>
            <div class="detail-item full"><div class="detail-label">Address</div><div class="detail-value">${applicant.residential_address || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">State</div><div class="detail-value">${applicant.state || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">Zip Code</div><div class="detail-value">${applicant.zip_code || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">Marital Status</div><div class="detail-value">${applicant.marital_status || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">Education Level</div><div class="detail-value">${applicant.education_level || 'N/A'}</div></div>
        </div>
    `;
    
    // Guardian Information (if exists)
    if (guardian && guardian.is_required) {
        content += `
            <div class="section-title">👨‍👩‍👧 Guardian/Caregiver Information</div>
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label">Guardian Name</div><div class="detail-value">${guardian.guardian_full_name || 'N/A'}</div></div>
                <div class="detail-item"><div class="detail-label">Guardian IC</div><div class="detail-value">${guardian.guardian_ic_number || 'N/A'}</div></div>
                <div class="detail-item"><div class="detail-label">Relationship</div><div class="detail-value">${guardian.relationship || 'N/A'}</div></div>
                <div class="detail-item"><div class="detail-label">Guardian Phone</div><div class="detail-value">${guardian.guardian_phone_number || 'N/A'}</div></div>
                <div class="detail-item"><div class="detail-label">Legal Authority</div><div class="detail-value">${guardian.legal_authority_declaration ? 'Yes' : 'No'}</div></div>
            </div>
        `;
    }
    
    // Disability Information
    content += `
        <div class="section-title">🏥 Disability Classification</div>
        <div class="detail-grid">
            <div class="detail-item"><div class="detail-label">Primary Category</div><div class="detail-value">${disability.primary_category || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">Sub-Category</div><div class="detail-value">${disability.sub_category || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">Diagnosis Date</div><div class="detail-value">${disability.diagnosis_date || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">Severity Level</div><div class="detail-value">${disability.severity_level || 'N/A'}</div></div>
            ${disability.diagnosed_by ? `<div class="detail-item"><div class="detail-label">Diagnosed By</div><div class="detail-value">${disability.diagnosed_by}</div></div>` : ''}
            ${disability.additional_notes ? `<div class="detail-item full"><div class="detail-label">Additional Notes</div><div class="detail-value">${disability.additional_notes}</div></div>` : ''}
        </div>
    `;
    
    // Functional Impact
    content += `
        <div class="section-title">⚕️ Functional Impact</div>
        <div class="detail-grid">
            <div class="detail-item"><div class="detail-label">Mobility Mode</div><div class="detail-value">${impact.mobility_mode || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">Assistive Devices</div><div class="detail-value">${impact.assistive_devices || 'None'}</div></div>
            <div class="detail-item"><div class="detail-label">ADL Independence</div><div class="detail-value">${impact.adl_independence || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">Communication</div><div class="detail-value">${impact.communication_method || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">Employment Status</div><div class="detail-value">${impact.employment_status || 'N/A'}</div></div>
            ${impact.special_requirements ? `<div class="detail-item full"><div class="detail-label">Special Requirements</div><div class="detail-value">${impact.special_requirements}</div></div>` : ''}
        </div>
    `;
    
    // Declaration
    content += `
        <div class="section-title">📋 Declaration</div>
        <div class="detail-grid">
            <div class="detail-item"><div class="detail-label">Digital Signature</div><div class="detail-value">${declaration.digital_signature || 'N/A'}</div></div>
            <div class="detail-item"><div class="detail-label">Signature Date</div><div class="detail-value">${new Date(declaration.signature_date).toLocaleString()}</div></div>
            <div class="detail-item"><div class="detail-label">Accuracy Confirmed</div><div class="detail-value">${declaration.accuracy_confirmed ? 'Yes' : 'No'}</div></div>
            <div class="detail-item"><div class="detail-label">Consent Given</div><div class="detail-value">${declaration.consent_given ? 'Yes' : 'No'}</div></div>
        </div>
    `;
    
    // Application Status
    content += `
        <div class="section-title">📊 Application Status</div>
        <div class="detail-grid">
            <div class="detail-item"><div class="detail-label">Current Status</div><div class="detail-value"><span class="status-badge status-${app.status.toLowerCase().replace(' ', '-')}">${app.status}</span></div></div>
            <div class="detail-item"><div class="detail-label">Submission Date</div><div class="detail-value">${new Date(app.submission_date).toLocaleString()}</div></div>
            ${app.reviewed_by ? `<div class="detail-item"><div class="detail-label">Reviewed By</div><div class="detail-value">${app.reviewed_by}</div></div>` : ''}
            ${app.reviewed_at ? `<div class="detail-item"><div class="detail-label">Reviewed At</div><div class="detail-value">${new Date(app.reviewed_at).toLocaleString()}</div></div>` : ''}
            ${app.admin_remarks ? `<div class="detail-item full"><div class="detail-label">Admin Remarks</div><div class="detail-value">${app.admin_remarks}</div></div>` : ''}
        </div>
    `;
    
    // Status History
    if (history && history.length > 0) {
        content += `
            <div class="section-title">📜 Status History</div>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 10px; text-align: left;">Date</th>
                        <th style="padding: 10px; text-align: left;">From</th>
                        <th style="padding: 10px; text-align: left;">To</th>
                        <th style="padding: 10px; text-align: left;">Changed By</th>
                        <th style="padding: 10px; text-align: left;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        history.forEach(h => {
            content += `
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px;">${new Date(h.changed_at).toLocaleDateString()}</td>
                    <td style="padding: 10px;">${h.old_status || 'New'}</td>
                    <td style="padding: 10px;">${h.new_status}</td>
                    <td style="padding: 10px;">${h.changed_by}</td>
                    <td style="padding: 10px;">${h.remarks || '-'}</td>
                </tr>
            `;
        });
        
        content += `
                </tbody>
            </table>
        `;
    }
    
    content += `</div>`; // Close modal-body
    
    document.getElementById('viewContent').innerHTML = content;
}

/**
 * Generate File Preview
 */
function generateFilePreview(doc) {
    const ext = doc.file_extension.toLowerCase();
    const filepath = doc.file_path;
    
    if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
        return `
            <img src="${filepath}" alt="${doc.document_type}" style="max-width: 100%; max-height: 300px; border-radius: 5px;">
            <br>
            <a href="${filepath}" download class="download-btn">📥 Download ${doc.document_type}</a>
        `;
    } else if (ext === 'pdf') {
        return `
            <embed src="${filepath}" type="application/pdf" style="width: 100%; height: 400px; border-radius: 5px;">
            <br>
            <a href="${filepath}" download class="download-btn">📥 Download ${doc.document_type}</a>
        `;
    } else {
        return `
            <p>📄 ${doc.file_name}</p>
            <p style="font-size: 0.85rem; color: #666;">Size: ${(doc.file_size / 1024).toFixed(2)} KB</p>
            <a href="${filepath}" download class="download-btn">📥 Download ${doc.document_type}</a>
        `;
    }
}

/**
 * EDIT - Update Application Status
 */
function editApplication(id) {
    console.log('editApplication called with ID:', id);
    
    const app = applications.find(a => a.application_id == id);
    if (!app) {
        alert('Application not found!');
        return;
    }
    
    const content = `
        <div class="modal-header">
            <h3>Edit Application - ${app.application_number}</h3>
            <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form action="admin.php" method="POST">
                <input type="hidden" name="app_id" value="${app.application_id}">
                
                <div class="section-title">Update Application Status</div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Application Number</div>
                        <input type="text" value="${app.application_number}" readonly style="background: #f0f0f0;">
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Applicant Name</div>
                        <input type="text" value="${app.full_name}" readonly style="background: #f0f0f0;">
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Application Status *</div>
                        <select name="status" required>
                            <option value="Pending" ${app.status == 'Pending' ? 'selected' : ''}>Pending</option>
                            <option value="Under Review" ${app.status == 'Under Review' ? 'selected' : ''}>Under Review</option>
                            <option value="Approved" ${app.status == 'Approved' ? 'selected' : ''}>Approved</option>
                            <option value="Rejected" ${app.status == 'Rejected' ? 'selected' : ''}>Rejected</option>
                        </select>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Current Status</div>
                        <input type="text" value="${app.status}" readonly style="background: #f0f0f0;">
                    </div>
                    <div class="detail-item full">
                        <div class="detail-label">Admin Remarks / Notes</div>
                        <textarea name="admin_remarks" rows="4" placeholder="Add any remarks or notes about this application...">${app.admin_remarks || ''}</textarea>
                    </div>
                </div>

                <div style="text-align: center;">
                    <button type="submit" name="update_btn" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    `;

    document.getElementById('editContent').innerHTML = content;
    document.getElementById('editModal').classList.add('active');
}

/**
 * DELETE - Delete Application
 */
function deleteApplication(id) {
    console.log('deleteApplication called with ID:', id);
    
    if (confirm('Are you sure you want to delete this application?\n\nThis will permanently delete:\n- All applicant details\n- Guardian information\n- Disability records\n- Uploaded documents\n- Status history\n\nThis action CANNOT be undone!')) {
        window.location.href = `admin.php?delete_id=${id}`;
    }
}

/**
 * ========================================
 * REPORT MODAL FUNCTIONS
 * ========================================
 */

/**
 * Open Report Modal
 */
function openReportModal() {
    console.log('Opening report modal...');
    const reportModal = document.getElementById('reportModal');
    if (reportModal) {
        reportModal.classList.add('active');
    } else {
        console.error('Report modal not found!');
    }
}

/**
 * Close Report Modal
 */
function closeReportModal() {
    console.log('Closing report modal...');
    const reportModal = document.getElementById('reportModal');
    if (reportModal) {
        reportModal.classList.remove('active');
    }
}

/**
 * Download Report as PDF
 * Uses browser's print function to save as PDF
 */
function downloadReport() {
    console.log('Downloading report...');
    window.print();
}