<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Kullanıcı Listesi | ACMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- plugin css -->
        <link href="assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />

        <!-- DataTables -->
        <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
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
                                    <h4 class="mb-sm-0 font-size-18">Kullanıcı Listesi</h4>
                                    <div class="page-title-right">
                                        <a href="user_edit.php" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Yeni Kullanıcı Ekle</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                                            <form id="filterForm" class="d-flex flex-wrap gap-2 align-items-center mb-0">
                                                <div class="col-sm-auto">
                                                    <label class="visually-hidden" for="filterStatus">Statü</label>
                                                    <select class="form-select w-auto" id="filterStatus">
                                                        <option value="">Tüm Statüler</option>
                                                        <option value="pending">Onay Bekleyenler</option>
                                                        <option value="approved">Onaylananlar</option>
                                                        <option value="rejected">Reddedilenler</option>
                                                        <option value="banned">Banlılar</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-auto">
                                                    <label class="visually-hidden" for="filterApp">Uygulama</label>
                                                    <select class="form-select w-auto" id="filterApp">
                                                        <option value="">Tüm Uygulamalar</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-auto">
                                                    <button type="submit" class="btn btn-primary"><i class="bx bx-filter-alt align-middle me-1"></i> Filtrele</button>
                                                </div>
                                            </form>

                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <select class="form-select w-auto" id="bulkActionSelect">
                                                    <option value="">Toplu İşlem Seçin...</option>
                                                    <option value="approve">✔️ Seçilenleri Onayla</option>
                                                    <option value="reject">❌ Seçilenleri Reddet</option>
                                                    <option value="pending">⏳ Seçilenleri Beklemeye Al</option>
                                                    <option value="ban">🚫 Seçilenleri Banla</option>
                                                    <option value="unban">🛡️ Seçilenlerin Banını Kaldır</option>
                                                    <option value="delete">🗑️ Onaysızları Sil</option>
                                                </select>
                                                <button class="btn btn-secondary" onclick="executeBulkAction()">Uygula</button>
                                                
                                                <div id="exportButtonsContainer"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th style="width: 20px;"><input class="form-check-input" type="checkbox" id="selectAll"></th>
                                                    <th style="width: 50px;">ID</th>
                                                    <th>Ad Soyad</th>
                                                    <th>E-posta</th>
                                                    <th>Uygulama</th>
                                                    <th>Statü</th>
                                                    <th>GPA Kodu</th>
                                                    <th>Kayıt Tarihi</th>
                                                    <th style="width:150px;">İşlemler</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- JS Fetch API ile Doldurulacak -->
                                            </tbody>
                                        </table>
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
            <!-- end main content-->
            
        </div>
        <!-- END layout-wrapper -->

        <!-- Right Sidebar -->
        <div class="right-bar">
            <div data-simplebar class="h-100">
                <div class="rightbar-title d-flex align-items-center p-3">
                    <h5 class="m-0 me-2">Theme Customizer</h5>
                    <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                        <i class="mdi mdi-close noti-icon"></i>
                    </a>
                </div>
                <hr class="m-0" />
                <div class="p-4" style="display: none;">
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="layout" id="layout-vertical" value="vertical"></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="layout" id="layout-horizontal" value="horizontal"></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="layout-mode" id="layout-mode-light" value="light"></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="layout-mode" id="layout-mode-dark" value="dark"></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="layout-width" id="layout-width-fuild" value="fuild"></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="layout-width" id="layout-width-boxed" value="boxed"></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="layout-position" id="layout-position-fixed" value="fixed"></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="layout-position" id="layout-position-scrollable" value="scrollable"></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="topbar-color" id="topbar-color-light" value="light"></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="topbar-color" id="topbar-color-dark" value="dark"></div>
                    <div class="form-check sidebar-setting"><input class="form-check-input" type="radio" name="sidebar-size" id="sidebar-size-default" value="default"></div>
                    <div class="form-check sidebar-setting"><input class="form-check-input" type="radio" name="sidebar-size" id="sidebar-size-compact" value="compact"></div>
                    <div class="form-check sidebar-setting"><input class="form-check-input" type="radio" name="sidebar-size" id="sidebar-size-small" value="small"></div>
                    <div class="form-check sidebar-setting"><input class="form-check-input" type="radio" name="sidebar-color" id="sidebar-color-light" value="light"></div>
                    <div class="form-check sidebar-setting"><input class="form-check-input" type="radio" name="sidebar-color" id="sidebar-color-dark" value="dark"></div>
                    <div class="form-check sidebar-setting"><input class="form-check-input" type="radio" name="sidebar-color" id="sidebar-color-brand" value="brand"></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="layout-direction" id="layout-direction-ltr" value="ltr"></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="layout-direction" id="layout-direction-rtl" value="rtl"></div>
                </div>
            </div>
        </div>
        <!-- /Right-bar -->

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <!-- JAVASCRIPT -->
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>
        <script src="assets/libs/feather-icons/feather.min.js"></script>
        <!-- pace js -->
        <script src="assets/libs/pace-js/pace.min.js"></script>

        <!-- Required datatable js -->
        <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
        
        <!-- Buttons examples -->
        <script src="assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
        <script src="assets/libs/jszip/jszip.min.js"></script>
        <script src="assets/libs/pdfmake/build/pdfmake.min.js"></script>
        <script src="assets/libs/pdfmake/build/vfs_fonts.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
        
        <!-- Responsive examples -->
        <script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

        <!-- App js -->
        <script src="assets/js/app.js?v=2"></script>
        <!-- ACMS Core -->
        <script src="assets/js/acms-core.js?v=2"></script>
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            const token = localStorage.getItem('acms_admin_token');
            let dataTable = null;

            document.addEventListener("DOMContentLoaded", async function() {
                await loadAllApps();
                
                const urlParams = new URLSearchParams(window.location.search);
                const status = urlParams.get('status') || '';
                const appId = urlParams.get('app_id') || '';
                
                document.getElementById('filterStatus').value = status;
                document.getElementById('filterApp').value = appId;

                loadUsers(status, appId);
                
                document.getElementById('filterForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const s = document.getElementById('filterStatus').value;
                    const a = document.getElementById('filterApp').value;
                    
                    // Update URL silently
                    const newUrl = new URL(window.location);
                    if (s) newUrl.searchParams.set('status', s); else newUrl.searchParams.delete('status');
                    if (a) newUrl.searchParams.set('app_id', a); else newUrl.searchParams.delete('app_id');
                    window.history.pushState({}, '', newUrl);

                    loadUsers(s, a);
                });
            });

            async function loadAllApps() {
                try {
                    const res = await fetch('../api/admin/apps.php', {
                        headers: { 'Authorization': 'Bearer ' + token }
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        const select = document.getElementById('filterApp');
                        data.data.forEach(app => {
                            const option = document.createElement('option');
                            option.value = app.id;
                            option.text = `${app.brand_name || 'Bilinmiyor'} - ${app.app_name}`;
                            select.appendChild(option);
                        });
                    }
                } catch(err) {
                    console.error('Uygulamalar yüklenemedi:', err);
                }
            }

            function loadUsers(status = '', appId = '') {
                let url = '../api/admin/users.php?action=list';
                if (status) url += '&status=' + encodeURIComponent(status);
                if (appId) url += '&app_id=' + encodeURIComponent(appId);

                fetch(url, {
                    headers: { 'Authorization': 'Bearer ' + token }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        if ($.fn.DataTable.isDataTable('#datatable')) {
                            $('#datatable').DataTable().destroy();
                        }
                        
                        const tbody = document.querySelector('#datatable tbody');
                        tbody.innerHTML = '';
                        
                        data.data.forEach(user => {
                            let badge = '';
                            if (user.is_banned == 1) {
                                badge = '<span class="badge bg-dark">Banlı</span>';
                            } else {
                                if (user.status === 'approved') badge = '<span class="badge bg-success">Onaylandı</span>';
                                else if (user.status === 'pending') badge = '<span class="badge bg-warning">Bekliyor</span>';
                                else if (user.status === 'rejected') badge = '<span class="badge bg-danger">Reddedildi</span>';
                            }
                                
                            const tr = document.createElement('tr');
                            
                            let gpaDisplay = user.gpa_code || '-';
                            let googleBadge = '';
                            if (user.auth_provider === 'google' || user.google_verified == 1) {
                                googleBadge = ' <span class="badge bg-soft-danger text-danger" style="font-size:10px;"><i class="bx bxl-google"></i> Google</span>';
                                if (!user.gpa_code || user.gpa_code === 'GOOGLE-PLAY-VERIFIED') {
                                    gpaDisplay = '<span class="badge bg-soft-success text-success" style="font-size:11px; font-weight:600;"><i class="bx bx-check-circle me-1"></i>Google Doğrulandı</span>';
                                }
                            }

                            let avatarDisplay = '';
                            if (user.avatar_url) {
                                avatarDisplay = `<img src="${user.avatar_url}" style="width:24px; height:24px; border-radius:50%; margin-right:6px; vertical-align:middle;">`;
                            }

                            let exemptBadges = '';
                            if (user.exempt_security == 1) {
                                exemptBadges += '<br><span class="badge bg-soft-warning text-warning" style="font-size:10px;" title="Rate Us Popup Gösterilmez"><i class="bx bx-star me-1"></i>Rate Us Muaf</span>';
                            }
                            if (user.exempt_screenshot == 1) {
                                exemptBadges += '<br><span class="badge bg-soft-info text-info" style="font-size:10px;" title="FLAG_SECURE Devre Dışı (Ekran Kaydı Alabilir)"><i class="bx bx-camera me-1"></i>Screenshot Muaf</span>';
                            }

                            let actionButtons = `
                                <a href="user_edit.php?id=${user.user_id}" class="btn btn-sm btn-outline-info" title="Düzenle"><i class="bx bx-pencil"></i></a>
                            `;
                            
                            if (user.status === 'pending') {
                                actionButtons += ` <button class="btn btn-sm btn-success" onclick="updateStatus(${user.user_id}, ${user.app_id}, 'approved')" title="Hemen Onayla"><i class="bx bx-check me-1"></i>Onayla</button>`;
                            } else {
                                actionButtons += `
                                    <button class="btn btn-sm btn-outline-success" onclick="updateStatus(${user.user_id}, ${user.app_id}, 'approved')" title="Onayla"><i class="bx bx-check"></i></button>
                                    <button class="btn btn-sm btn-outline-warning" onclick="updateStatus(${user.user_id}, ${user.app_id}, 'pending')" title="Beklemeye Al"><i class="bx bx-time"></i></button>
                                `;
                            }
                            
                            actionButtons += ` <button class="btn btn-sm btn-outline-danger" onclick="updateStatus(${user.user_id}, ${user.app_id}, 'rejected')" title="Reddet"><i class="bx bx-x"></i></button>`;
                            
                            if (user.is_banned == 1) {
                                actionButtons += ` <button class="btn btn-sm btn-outline-dark" onclick="unbanUser(${user.user_id})" title="Banı Kaldır"><i class="bx bx-check-shield"></i></button>`;
                            } else {
                                actionButtons += ` <button class="btn btn-sm btn-outline-danger" onclick="banUser(${user.user_id})" title="Banla"><i class="bx bx-block"></i></button>`;
                            }
                            
                            actionButtons += ` <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.user_id})" title="Kullanıcıyı Sil"><i class="bx bx-trash"></i></button>`;

                            tr.innerHTML = `
                                <td><input class="form-check-input user-checkbox" type="checkbox" value="${user.user_id}"></td>
                                <td>${user.user_id}</td>
                                <td>${avatarDisplay}<strong>${user.name}</strong>${googleBadge}${user.deleted_by_user == 1 ? '<br><span style="font-size:10px; color:#ef4444;"><i class="bx bx-user-x"></i> Kendi tarafından silindi</span>' : ''}</td>
                                <td>${user.email}${exemptBadges}</td>
                                <td>${user.app_name}</td>
                                <td>${badge}</td>
                                <td>${gpaDisplay}</td>
                                <td>${user.created_at}</td>
                                <td>
                                    ${actionButtons}
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                        
                        dataTable = $('#datatable').DataTable({
                            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json' },
                            dom: 'Bfrtip',
                            buttons: ['copy', 'excel', 'pdf']
                        });
                        
                        dataTable.buttons().container().appendTo('#exportButtonsContainer');
                        
                        $('#selectAll').off('change').on('change', function() {
                            if (dataTable) {
                                dataTable.$('.user-checkbox').prop('checked', this.checked);
                            }
                        });
                    }
                })
                .catch(err => {
                    Swal.fire('Hata', 'Kullanıcılar yüklenemedi.', 'error');
                });
            }

            function updateStatus(userId, appId, newStatus) {
                fetch('../api/admin/users.php?action=update_status', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ user_id: userId, app_id: appId, new_status: newStatus })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Hata', data.message, 'error');
                    }
                });
            }

            function banUser(userId) {
                Swal.fire({
                    title: 'Kullanıcıyı Banla',
                    text: "Bu kullanıcıyı sistemden tamamen yasaklamak üzeresiniz. Lütfen ban sebebini girin:",
                    input: 'text',
                    inputPlaceholder: 'Örn: Çoklu hesap',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Evet, Banla!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const banReason = result.value || 'Yönetici tarafından yasaklandı.';
                        fetch('../api/admin/users.php?action=ban', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + token
                            },
                            body: JSON.stringify({ user_id: userId, ban_reason: banReason })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if(data.status === 'success') {
                                Swal.fire('Banlandı!', data.message, 'success')
                                .then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Hata!', data.message, 'error');
                            }
                        });
                    }
                });
            }

            function unbanUser(userId) {
                Swal.fire({
                    title: 'Banı Kaldır',
                    text: "Bu kullanıcının sistemden yasaklamasını kaldırmak istediğinize emin misiniz?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Evet, Kaldır!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../api/admin/users.php?action=unban', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + token
                            },
                            body: JSON.stringify({ user_id: userId })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if(data.status === 'success') {
                                Swal.fire('Ban Kaldırıldı!', data.message, 'success')
                                .then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Hata!', data.message, 'error');
                            }
                        });
                    }
                });
            }

            function deleteUser(userId) {
                Swal.fire({
                    title: 'Kullanıcıyı silmek istediğinize emin misiniz?',
                    text: '(Kullanıcı Çöp Kutusuna taşınacaktır)',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Evet, Sil!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../api/admin/users.php?action=bulk_update', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + token
                            },
                            body: JSON.stringify({ user_ids: [userId], bulk_action: 'delete' })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Silindi',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                loadUsers(document.getElementById('filterStatus').value, document.getElementById('filterApp').value);
                            } else {
                                Swal.fire('Hata', data.message, 'error');
                            }
                        });
                    }
                });
            }

            function executeBulkAction() {
                const action = document.getElementById('bulkActionSelect').value;
                if (!action) return;
                
                if (!dataTable) return;
                const checkboxes = dataTable.$('.user-checkbox:checked');
                const userIds = Array.from(checkboxes).map(cb => cb.value);
                
                if (userIds.length === 0) {
                    Swal.fire('Uyarı', 'Lütfen işlem yapılacak kullanıcıları seçin.', 'warning');
                    return;
                }
                
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: `${userIds.length} adet kullanıcı için toplu işlem uygulanacak.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Evet, Uygula',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../api/admin/users.php?action=bulk_update', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + token
                            },
                            body: JSON.stringify({ user_ids: userIds, bulk_action: action })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire('Başarılı', data.message, 'success').then(() => {
                                    window.location.reload();
                                });
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
