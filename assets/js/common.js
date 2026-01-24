/**
 * MARI SYSTEM - COMMON UTILITIES
 * Shared functions used across multiple pages
 */

/**
 * Toggle Sidebar Navigation
 * Used in all authenticated pages
 */
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    
    if (sidebar) {
        sidebar.classList.toggle('hidden');
    }
    
    if (mainContent) {
        mainContent.classList.toggle('expanded');
    }
}

/**
 * Close Modal by ID
 * Generic function for closing any modal
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

/**
 * Close modal when clicking outside
 */
window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.classList.remove('active');
    }
}

