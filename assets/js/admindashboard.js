/**
 * MARI SYSTEM - ADMIN DASHBOARD
 * Dashboard-specific functionality for admin_dashboard.php
 */

/**
 * Initialize Dashboard
 * Sets up any dashboard-specific interactions
 */
function initializeDashboard() {
    console.log('Admin dashboard initialized');
    // Add any dashboard-specific initialization here if needed
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeDashboard);
} else {
    initializeDashboard();
}
