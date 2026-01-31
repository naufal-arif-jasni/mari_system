/**
 * MARI SYSTEM - APPLICATION FORM
 * Form validation and dynamic sections for application_form.php
 */

/**
 * Check Age for Guardian Requirement
 * Auto-shows guardian section if applicant is under 18
 */
function checkAge() {
    const dobInput = document.getElementById('dob');
    if (!dobInput) return;
    
    const dobValue = dobInput.value;
    if (dobValue) {
        const dob = new Date(dobValue);
        const diff_ms = Date.now() - dob.getTime();
        const age_dt = new Date(diff_ms);
        const age = Math.abs(age_dt.getUTCFullYear() - 1970);
        
        const section2 = document.getElementById('section2');
        const checkBox = document.getElementById('require_rep');
        
        if (!section2 || !checkBox) return;
        
        if (age < 18) {
            section2.classList.remove('hidden');
            checkBox.checked = true;
        } else {
            if (!checkBox.checked) {
                section2.classList.add('hidden');
            }
        }
    }
}

/**
 * Toggle Caregiver Section Manually
 * When user checks "I require representative" checkbox
 */
function toggleCaregiver() {
    const checkBox = document.getElementById('require_rep');
    const section2 = document.getElementById('section2');
    
    if (!checkBox || !section2) return;
    
    if (checkBox.checked) {
        section2.classList.remove('hidden');
    } else {
        section2.classList.add('hidden');
        checkAge();
    }
}

/**
 * Initialize Application Form
 */
function initializeApplicationForm() {
    const dobInput = document.getElementById('dob');
    if (dobInput) {
        dobInput.addEventListener('change', checkAge);
    }
    
    // Add representative checkbox listener
    const repCheckbox = document.getElementById('require_rep');
    if (repCheckbox) {
        repCheckbox.addEventListener('change', toggleCaregiver);
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApplicationForm);
} else {
    initializeApplicationForm();
}
