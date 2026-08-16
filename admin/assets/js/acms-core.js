/* ACMS Core Javascript */

// 0. Feather Icons Initialization
document.addEventListener("DOMContentLoaded", function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});

// 1. Route Guard
if(!localStorage.getItem('acms_admin_token')) {
    window.location.href = 'login.php';
}

// 2. adminName Rendering & Custom Sidebar Styling
document.addEventListener("DOMContentLoaded", function() {
    var adminNameSpan = document.getElementById('adminName');
    if(adminNameSpan) {
        adminNameSpan.innerText = localStorage.getItem('acms_admin_name') || 'Yönetici';
    }

    // Custom CSS for prominent active sidebar menu
    var customStyle = document.createElement('style');
    customStyle.innerHTML = `
    #sidebar-menu ul li a.active {
        background-color: rgba(var(--bs-primary-rgb), 0.15) !important;
        color: var(--bs-primary) !important;
        border-right: 4px solid var(--bs-primary) !important;
        font-weight: 600;
    }
    #sidebar-menu ul li a.active i {
        color: var(--bs-primary) !important;
    }
    `;
    document.head.appendChild(customStyle);
});

// 3. Global Logout Function
function logout() {
    localStorage.removeItem('acms_admin_token');
    localStorage.removeItem('acms_admin_name');
    localStorage.removeItem('acms_admin_role');
    window.location.href = 'login.php';
}

// 4. Pending Approvals & Trash Badges
document.addEventListener("DOMContentLoaded", function() {
    if(localStorage.getItem('acms_admin_token')) {
        // Pending Approvals
        var pendingBadge = document.getElementById('pendingBadge');
        if(pendingBadge) {
            fetch('../api/admin/users.php?action=pending_count', {
                headers: { 'Authorization': 'Bearer ' + localStorage.getItem('acms_admin_token') }
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    pendingBadge.innerText = data.count;
                    pendingBadge.style.display = 'inline-block';
                }
            }).catch(err => console.error(err));
        }

        // Trash Count
        var trashBadge = document.getElementById('trashBadge');
        if(trashBadge) {
            fetch('../api/admin/trash.php?action=trash_count', {
                headers: { 'Authorization': 'Bearer ' + localStorage.getItem('acms_admin_token') }
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    trashBadge.innerText = data.count;
                    trashBadge.style.display = 'inline-block';
                }
            }).catch(err => console.error(err));
        }

        // Ticket Count
        updateGlobalTicketBadge();
    }
});

window.updateGlobalTicketBadge = function() {
    var ticketBadge = document.getElementById('ticketBadge');
    if(ticketBadge && localStorage.getItem('acms_admin_token')) {
        fetch('../api/admin/tickets.php?action=count', {
            headers: { 'Authorization': 'Bearer ' + localStorage.getItem('acms_admin_token') }
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success' && data.data > 0) {
                ticketBadge.innerText = data.data;
                ticketBadge.style.display = 'inline-block';
            } else {
                ticketBadge.style.display = 'none';
            }
        }).catch(err => console.error(err));
    }
};
