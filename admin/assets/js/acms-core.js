/* ACMS Core Javascript - Reliable Lifecycle & UI Engine */

// 0. Robust Feather Icons Initializer
function initFeatherIcons() {
    if (typeof feather !== 'undefined' && typeof feather.replace === 'function') {
        try {
            feather.replace();
        } catch (e) {
            console.warn("ACMS: Feather icons replacement notice:", e);
        }
    }
}

// Execute feather replacement at multiple lifecycle points
initFeatherIcons();
if (document.readyState === 'loading') {
    document.addEventListener("DOMContentLoaded", initFeatherIcons);
}
window.addEventListener("load", initFeatherIcons);
setTimeout(initFeatherIcons, 50);
setTimeout(initFeatherIcons, 300);

// 1. Route Guard
if (!localStorage.getItem('acms_admin_token')) {
    window.location.href = 'login.php';
}

// 2. adminName Rendering & Custom Sidebar Styling
function initAdminHeaderAndSidebar() {
    var adminNameSpan = document.getElementById('adminName');
    if (adminNameSpan) {
        adminNameSpan.innerText = localStorage.getItem('acms_admin_name') || 'Yönetici';
    }

    // Custom CSS for prominent active sidebar menu
    if (!document.getElementById('acms-custom-sidebar-style')) {
        var customStyle = document.createElement('style');
        customStyle.id = 'acms-custom-sidebar-style';
        customStyle.innerHTML = `
        #sidebar-menu ul li a.active {
            background-color: rgba(var(--bs-primary-rgb), 0.15) !important;
            color: var(--bs-primary) !important;
            border-right: 4px solid var(--bs-primary) !important;
            font-weight: 600;
        }
        #sidebar-menu ul li a.active i,
        #sidebar-menu ul li a.active svg {
            color: var(--bs-primary) !important;
        }
        `;
        document.head.appendChild(customStyle);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener("DOMContentLoaded", initAdminHeaderAndSidebar);
} else {
    initAdminHeaderAndSidebar();
}

// 3. Global Logout Function
function logout() {
    localStorage.removeItem('acms_admin_token');
    localStorage.removeItem('acms_admin_name');
    localStorage.removeItem('acms_admin_role');
    window.location.href = 'login.php';
}

// 4. Pending Approvals, Trash & Ticket Badges
function updateBadges() {
    var token = localStorage.getItem('acms_admin_token');
    if (!token) return;

    // Pending Approvals
    var pendingBadge = document.getElementById('pendingBadge');
    if (pendingBadge) {
        fetch('../api/admin/users.php?action=pending_count', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.status === 'success') {
                pendingBadge.innerText = data.count;
                pendingBadge.style.display = 'inline-block';
            }
        })
        .catch(err => console.warn('Pending badge fetch:', err));
    }

    // Trash Count
    var trashBadge = document.getElementById('trashBadge');
    if (trashBadge) {
        fetch('../api/admin/trash.php?action=trash_count', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.status === 'success') {
                trashBadge.innerText = data.count;
                trashBadge.style.display = 'inline-block';
            }
        })
        .catch(err => console.warn('Trash badge fetch:', err));
    }

    // Ticket Count
    updateGlobalTicketBadge();
}

if (document.readyState === 'loading') {
    document.addEventListener("DOMContentLoaded", updateBadges);
} else {
    updateBadges();
}

window.updateGlobalTicketBadge = function() {
    var ticketBadge = document.getElementById('ticketBadge');
    var token = localStorage.getItem('acms_admin_token');
    if (ticketBadge && token) {
        fetch('../api/admin/tickets.php?action=count', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.status === 'success' && data.data > 0) {
                ticketBadge.innerText = data.data;
                ticketBadge.style.display = 'inline-block';
            } else if (ticketBadge) {
                ticketBadge.style.display = 'none';
            }
        })
        .catch(err => console.warn('Ticket badge fetch:', err));
    }
};
