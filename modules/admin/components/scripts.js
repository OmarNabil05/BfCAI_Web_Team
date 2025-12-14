// Toggle sidebar function for mobile
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const backdrop = document.getElementById('sidebarBackdrop');
    
    sidebar.classList.toggle('show');
    if (backdrop) {
        backdrop.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
    }
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    
    if (window.innerWidth <= 992 && 
        sidebar &&
        !sidebar.contains(event.target) && 
        !event.target.closest('.menu-toggle')) {
        sidebar.classList.remove('show');
        if (backdrop) {
            backdrop.style.display = 'none';
        }
    }
});

// Close sidebar on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        
        if (sidebar) {
            sidebar.classList.remove('show');
        }
        if (backdrop) {
            backdrop.style.display = 'none';
        }
    }
});

// Auto-close sidebar on window resize if it's open on mobile
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    
    if (window.innerWidth > 992 && sidebar && sidebar.classList.contains('show')) {
        sidebar.classList.remove('show');
        if (backdrop) {
            backdrop.style.display = 'none';
        }
    }
});
