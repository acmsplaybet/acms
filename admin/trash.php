<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Çöp Kutusu | ACMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- plugin css -->
        <link href="assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />

        <!-- DataTables -->
        <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" /> 

        <!-- preloader css -->
        <link rel="stylesheet" href="assets/css/preloader.min.css" type="text/css" />

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <!-- Begin page -->
        <div id="layout-wrapper">
            
            <?php include 'topbar.php'; ?>
            <!-- ========== Left Sidebar Start ========== -->
            <?php include 'sidebar.php'; ?>
            <!-- Left Sidebar End -->

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18 text-danger"><i class="bx bx-trash"></i> Çöp Kutusu</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#users" role="tab">
                                                    <span class="d-none d-sm-block"><i class="bx bx-user"></i> Kullanıcılar <span id="count-user" class="badge rounded-pill bg-danger ms-1">0</span></span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#apps" role="tab">
                                                    <span class="d-none d-sm-block"><i class="bx bx-cube"></i> Uygulamalar <span id="count-app" class="badge rounded-pill bg-danger ms-1">0</span></span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#matches" role="tab">
                                                    <span class="d-none d-sm-block"><i class="bx bx-football"></i> Maçlar <span id="count-match" class="badge rounded-pill bg-danger ms-1">0</span></span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#leagues" role="tab">
                                                    <span class="d-none d-sm-block"><i class="bx bx-trophy"></i> Ligler <span id="count-league" class="badge rounded-pill bg-danger ms-1">0</span></span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#teams" role="tab">
                                                    <span class="d-none d-sm-block"><i class="bx bx-shield"></i> Takımlar <span id="count-team" class="badge rounded-pill bg-danger ms-1">0</span></span>
                                                </a>
                                            </li>
                                        </ul>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="card-title mb-0">Silinmiş Öğeler</h5>
                                            <div>
                                                <button class="btn btn-success btn-sm me-1" onclick="bulkRestore()"><i class="bx bx-repost"></i> Seçilenleri Geri Yükle</button>
                                                <button class="btn btn-danger btn-sm" onclick="bulkHardDelete()"><i class="bx bx-trash"></i> Seçilenleri Kalıcı Sil</button>
                                            </div>
                                        </div>
                                        
                                        <!-- Tab panes -->
                                        <div class="tab-content p-3 text-muted">
                                            <!-- USERS -->
                                            <div class="tab-pane active" id="users" role="tabpanel">
                                                <table id="dt-users" class="table table-bordered dt-responsive nowrap w-100">
                                                    <thead><tr><th style="width:20px;"><input type="checkbox" class="form-check-input select-all" data-type="user"></th><th>ID</th><th>Kullanıcı Adı</th><th>E-posta</th><th>Tarih</th><th style="width:180px;">İşlemler</th></tr></thead>
                                                    <tbody id="tbody-user"></tbody>
                                                </table>
                                            </div>
                                            <!-- APPS -->
                                            <div class="tab-pane" id="apps" role="tabpanel">
                                                <table id="dt-apps" class="table table-bordered dt-responsive nowrap w-100">
                                                    <thead><tr><th style="width:20px;"><input type="checkbox" class="form-check-input select-all" data-type="app"></th><th>ID</th><th>Başlık</th><th>Marka</th><th>Tarih</th><th style="width:180px;">İşlemler</th></tr></thead>
                                                    <tbody id="tbody-app"></tbody>
                                                </table>
                                            </div>
                                            <!-- MATCHES -->
                                            <div class="tab-pane" id="matches" role="tabpanel">
                                                <table id="dt-matches" class="table table-bordered dt-responsive nowrap w-100">
                                                    <thead><tr><th style="width:20px;"><input type="checkbox" class="form-check-input select-all" data-type="match"></th><th>ID</th><th>Karşılaşma</th><th>Tarih</th><th style="width:180px;">İşlemler</th></tr></thead>
                                                    <tbody id="tbody-match"></tbody>
                                                </table>
                                            </div>
                                            <!-- LEAGUES -->
                                            <div class="tab-pane" id="leagues" role="tabpanel">
                                                <table id="dt-leagues" class="table table-bordered dt-responsive nowrap w-100">
                                                    <thead><tr><th style="width:20px;"><input type="checkbox" class="form-check-input select-all" data-type="league"></th><th>ID</th><th>Lig Adı</th><th>Ülke</th><th>Tarih</th><th style="width:180px;">İşlemler</th></tr></thead>
                                                    <tbody id="tbody-league"></tbody>
                                                </table>
                                            </div>
                                            <!-- TEAMS -->
                                            <div class="tab-pane" id="teams" role="tabpanel">
                                                <table id="dt-teams" class="table table-bordered dt-responsive nowrap w-100">
                                                    <thead><tr><th style="width:20px;"><input type="checkbox" class="form-check-input select-all" data-type="team"></th><th>ID</th><th>Takım Adı</th><th>Ülke</th><th>Tarih</th><th style="width:180px;">İşlemler</th></tr></thead>
                                                    <tbody id="tbody-team"></tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <script>document.write(new Date().getFullYear())</script> © ACMS.
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        
        <?php include 'right_sidebar.php'; ?>
    <div class="rightbar-overlay"></div>

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <!-- JAVASCRIPT -->
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>
        <script src="assets/libs/feather-icons/feather.min.js"></script>
        <script src="assets/libs/pace-js/pace.min.js"></script>

        <!-- Required datatable js -->
        <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

        <!-- App js -->
        <script src="assets/js/app.js?v=2"></script>
        <script src="assets/js/acms-core.js?v=2"></script>
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            const token = localStorage.getItem('acms_admin_token');
            const dataTables = {};

            document.addEventListener("DOMContentLoaded", function() {
                loadTrashData();
            });

            function loadTrashData() {
                fetch('../api/admin/trash.php?action=list', {
                    headers: { 'Authorization': 'Bearer ' + token }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderTable('app', data.data.apps, '#dt-apps', '#tbody-app');
                        renderTable('match', data.data.matches, '#dt-matches', '#tbody-match');
                        renderTable('league', data.data.leagues, '#dt-leagues', '#tbody-league');
                        renderTable('team', data.data.teams, '#dt-teams', '#tbody-team');
                        renderTable('user', data.data.users, '#dt-users', '#tbody-user');

                        // Update counts
                        document.getElementById('count-app').innerText = data.data.apps.length;
                        document.getElementById('count-match').innerText = data.data.matches.length;
                        document.getElementById('count-league').innerText = data.data.leagues.length;
                        document.getElementById('count-team').innerText = data.data.teams.length;
                        document.getElementById('count-user').innerText = data.data.users.length;
                        
                        // Update global sidebar badge
                        let totalTrash = data.data.apps.length + data.data.matches.length + data.data.leagues.length + data.data.teams.length + data.data.users.length;
                        let sidebarBadge = document.getElementById('trashBadge');
                        if (sidebarBadge) {
                            sidebarBadge.innerText = totalTrash;
                            sidebarBadge.style.display = 'inline-block';
                        }
                    } else {
                        Swal.fire('Hata', data.message, 'error');
                    }
                })
                .catch(err => console.error(err));
            }

            function renderTable(type, items, tableId, tbodyId) {
                if (dataTables[type]) {
                    dataTables[type].destroy();
                }

                const tbody = document.querySelector(tbodyId);
                tbody.innerHTML = '';
                
                items.forEach(item => {
                    const tr = document.createElement('tr');
                    
                    const actionBtns = `
                        <button class="btn btn-sm btn-outline-success" onclick="restoreItem('${type}', ${item.id})" title="Geri Yükle"><i class="bx bx-repost"></i> Geri Yükle</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="hardDeleteItem('${type}', ${item.id})" title="Kalıcı Sil"><i class="bx bx-trash"></i> Kalıcı Sil</button>
                    `;
                    
                    const checkbox = `<td><input type="checkbox" class="form-check-input item-check" value="${item.id}" data-type="${type}"></td>`;
                    
                    if (type === 'user') {
                        const selfDeletedBadge = item.deleted_by_user == 1
                            ? '<br><span style="font-size:10px; color:#ef4444;"><i class="bx bx-user-x"></i> Kendi tarafından silindi</span>'
                            : '';
                        tr.innerHTML = `
                            ${checkbox}
                            <td>${item.id}</td>
                            <td><strong>${item.title}</strong>${selfDeletedBadge}</td>
                            <td>${item.brand_name || '-'}</td>
                            <td>${item.created_at || '-'}</td>
                            <td>${actionBtns}</td>
                        `;
                    } else if (type === 'match') {
                        tr.innerHTML = `
                            ${checkbox}
                            <td>${item.id}</td>
                            <td><strong>${item.title}</strong></td>
                            <td>${item.created_at || '-'}</td>
                            <td>${actionBtns}</td>
                        `;
                    } else {
                        tr.innerHTML = `
                            ${checkbox}
                            <td>${item.id}</td>
                            <td><strong>${item.title}</strong></td>
                            <td>${item.brand_name || '-'}</td>
                            <td>${item.created_at || '-'}</td>
                            <td>${actionBtns}</td>
                        `;
                    }
                    tbody.appendChild(tr);
                });

                dataTables[type] = $(tableId).DataTable({
                    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json' }
                });
            }

            function restoreItem(type, id) {
                fetch('../api/admin/trash.php?action=restore', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ type: type, id: id })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 1500
                        });
                        loadTrashData();
                    } else {
                        Swal.fire('Hata', data.message, 'error');
                    }
                });
            }

            function hardDeleteItem(type, id) {
                Swal.fire({
                    title: 'Kalıcı Olarak Sil?',
                    text: "Bu işlem geri alınamaz! Veritabanından tamamen silinecektir.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Evet, Kalıcı Olarak Sil!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../api/admin/trash.php?action=hard_delete', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + token
                            },
                            body: JSON.stringify({ type: type, id: id })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 1500
                                });
                                loadTrashData();
                            } else {
                                Swal.fire('Hata', data.message, 'error');
                            }
                        });
                    }
                });
            }
            
            // Handle select all logic
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('select-all')) {
                    const type = e.target.getAttribute('data-type');
                    const isChecked = e.target.checked;
                    document.querySelectorAll(`.item-check[data-type="${type}"]`).forEach(cb => {
                        cb.checked = isChecked;
                    });
                }
            });

            function getSelectedItems() {
                // Sadece aktif sekmedeki seçili olanları al
                const activeTab = document.querySelector('.tab-pane.active');
                if (!activeTab) return { type: null, ids: [] };
                
                const tableId = activeTab.querySelector('table').id;
                const type = tableId.replace('dt-', ''); // apps -> app, matches -> match etc.
                // Plural to singular hack since our IDs are plurals
                const typeMap = {'users': 'user', 'apps': 'app', 'matches': 'match', 'leagues': 'league', 'teams': 'team'};
                const singleType = typeMap[type];

                const selected = activeTab.querySelectorAll('.item-check:checked');
                const ids = Array.from(selected).map(cb => parseInt(cb.value));
                
                return { type: singleType, ids: ids };
            }

            function bulkRestore() {
                const { type, ids } = getSelectedItems();
                if (ids.length === 0) {
                    Swal.fire('Uyarı', 'Geri yüklenecek öğe seçmediniz.', 'warning');
                    return;
                }
                
                fetch('../api/admin/trash.php?action=bulk_restore', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify({ type: type, ids: ids })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 1500 });
                        loadTrashData();
                        document.querySelectorAll('.select-all').forEach(cb => cb.checked = false);
                    } else {
                        Swal.fire('Hata', data.message, 'error');
                    }
                });
            }

            function bulkHardDelete() {
                const { type, ids } = getSelectedItems();
                if (ids.length === 0) {
                    Swal.fire('Uyarı', 'Kalıcı silinecek öğe seçmediniz.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Seçilenleri Kalıcı Olarak Sil?',
                    text: "Bu işlem geri alınamaz! " + ids.length + " adet kayıt tamamen silinecektir.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Evet, Kalıcı Sil!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../api/admin/trash.php?action=bulk_hard_delete', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                            body: JSON.stringify({ type: type, ids: ids })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 1500 });
                                loadTrashData();
                                document.querySelectorAll('.select-all').forEach(cb => cb.checked = false);
                            } else {
                                Swal.fire('Hata', data.message, 'error');
                            }
                        });
                    }
                });
            }
        </script>
    </body>
</html>
