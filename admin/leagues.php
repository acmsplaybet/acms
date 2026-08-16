<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Ligler | ACMS</title>
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
                                    <h4 class="mb-sm-0 font-size-18">Ligler</h4>
                                    <div class="page-title-right">
                                        <button class="btn btn-danger me-2" onclick="bulkDelete()" id="btnBulkDelete" style="display:none;"><i class="bx bx-trash me-1"></i> Seçilenleri Sil</button>
                                        <button class="btn btn-primary" onclick="openLeagueModal()"><i class="bx bx-plus me-1"></i> Yeni Lig Ekle</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th style="width: 40px;">
                                                        <div class="form-check font-size-16">
                                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                                        </div>
                                                    </th>
                                                    <th style="width: 50px;">ID</th>
                                                    <th>Logo</th>
                                                    <th>Lig Adı</th>
                                                    <th>Slug</th>
                                                    <th style="width:100px;">İşlem</th>
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
            
            <!-- Modal -->
            <div class="modal fade" id="leagueModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitle">Yeni Lig Ekle</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="leagueForm">
                                <input type="hidden" id="league_id">
                                <div class="mb-3">
                                    <label class="form-label">Lig Adı</label>
                                    <input type="text" class="form-control" id="league_name" required placeholder="Örn: La Liga">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Logo URL (Opsiyonel)</label>
                                    <input type="url" class="form-control" id="league_logo" placeholder="https://.../logo.png">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" onclick="saveLeague()">Kaydet</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal: View League Teams -->
            <div class="modal fade" id="leagueTeamsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="teamsModalTitle">Lig Takımları</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">ID</th>
                                        <th style="width: 50px;">Logo</th>
                                        <th>Takım Adı</th>
                                    </tr>
                                </thead>
                                <tbody id="leagueTeamsTableBody">
                                    <!-- JS will populate -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

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
            let myModal = null;
            let teamsModal = null;

            // Global icon fallback helper
            function acmsImgError(el, type) {
                el.outerHTML = type === 'league'
                    ? '<i class="mdi mdi-trophy text-secondary me-1" style="font-size:20px;vertical-align:middle;"></i>'
                    : '<i class="mdi mdi-shield-half-full text-secondary me-1" style="font-size:20px;vertical-align:middle;"></i>';
            }

            document.addEventListener("DOMContentLoaded", function() {
                

                myModal = new bootstrap.Modal(document.getElementById('leagueModal'));
                teamsModal = new bootstrap.Modal(document.getElementById('leagueTeamsModal'));
                loadLeagues();
            });

            document.addEventListener('change', function(e) {
                if(e.target && e.target.id === 'checkAll') {
                    let checkboxes = document.querySelectorAll('.checkItem');
                    checkboxes.forEach(cb => cb.checked = e.target.checked);
                    toggleBulkDeleteButton();
                } else if (e.target && e.target.classList.contains('checkItem')) {
                    toggleBulkDeleteButton();
                    let allChecked = document.querySelectorAll('.checkItem:checked').length === document.querySelectorAll('.checkItem').length;
                    document.getElementById('checkAll').checked = allChecked;
                }
            });

            function toggleBulkDeleteButton() {
                let checkedCount = document.querySelectorAll('.checkItem:checked').length;
                document.getElementById('btnBulkDelete').style.display = checkedCount > 0 ? 'inline-block' : 'none';
            }

            

            function loadLeagues() {
                fetch('../api/admin/leagues.php?action=list', {
                    headers: { 'Authorization': 'Bearer ' + token }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (dataTable !== null) {
                            dataTable.destroy();
                        }
                        
                        const tbody = document.querySelector('#datatable tbody');
                        tbody.innerHTML = '';
                        
                        data.data.forEach(league => {
                            const logoHtml = league.logo_url 
                                ? `<img src="${league.logo_url}" alt="logo" height="24" style="vertical-align:middle;" onerror="acmsImgError(this,'league')">` 
                                : `<i class="mdi mdi-trophy text-secondary me-1" style="font-size:20px;vertical-align:middle;"></i>`;
                                
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>
                                    <div class="form-check font-size-16">
                                        <input class="form-check-input checkItem" type="checkbox" value="${league.id}">
                                    </div>
                                </td>
                                <td>${league.id}</td>
                                <td>${logoHtml}</td>
                                <td><strong>${league.name}</strong></td>
                                <td><span class="text-muted">${league.slug}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-soft-info me-1" onclick="viewLeagueTeams(${league.id}, '${league.name.replace(/'/g, "\\'")}')" title="Takımları Görüntüle"><i class="mdi mdi-eye"></i></button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editLeague(${league.id}, '${league.name.replace(/'/g, "\\'")}', '${league.logo_url || ''}')" title="Düzenle"><i class="bx bx-pencil"></i></button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteLeague(${league.id})" title="Sil"><i class="bx bx-trash"></i></button>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                        
                        dataTable = $('#datatable').DataTable({
                            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json' },
                            columnDefs: [{ orderable: false, targets: [0, 5] }],
                            order: [[1, 'desc']]
                        });
                        document.getElementById('checkAll').checked = false;
                        toggleBulkDeleteButton();
                    }
                });
            }

            function viewLeagueTeams(leagueId, leagueName) {
                document.getElementById('teamsModalTitle').innerText = leagueName + ' Takımları';
                const tbody = document.getElementById('leagueTeamsTableBody');
                tbody.innerHTML = '<tr><td colspan="3" class="text-center">Yükleniyor...</td></tr>';
                teamsModal.show();
                
                fetch('../api/admin/teams.php?action=list&league_id=' + leagueId + '&strict=1', {
                    headers: { 'Authorization': 'Bearer ' + token }
                })
                .then(res => res.json())
                .then(data => {
                    tbody.innerHTML = '';
                    if (data.status === 'success' && data.data.length > 0) {
                        data.data.forEach(team => {
                            const logoHtml = team.logo_url 
                                ? `<img src="${team.logo_url}" alt="logo" height="24" style="vertical-align:middle;" onerror="acmsImgError(this,'team')">` 
                                : `<i class="mdi mdi-shield-half-full text-secondary me-1" style="font-size:20px;vertical-align:middle;"></i>`;
                            tbody.innerHTML += `
                                <tr>
                                    <td>${team.id}</td>
                                    <td>${logoHtml}</td>
                                    <td><strong>${team.name}</strong></td>
                                </tr>
                            `;
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="3" class="text-center">Bu lige ait takım bulunamadı.</td></tr>';
                    }
                })
                .catch(err => {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Takımlar yüklenirken hata oluştu.</td></tr>';
                });
            }

            function openLeagueModal() {
                document.getElementById('leagueForm').reset();
                document.getElementById('league_id').value = '';
                document.getElementById('modalTitle').innerText = 'Yeni Lig Ekle';
                myModal.show();
            }

            function editLeague(id, name, logo) {
                document.getElementById('league_id').value = id;
                document.getElementById('league_name').value = name;
                document.getElementById('league_logo').value = logo;
                document.getElementById('modalTitle').innerText = 'Ligi Düzenle';
                myModal.show();
            }

            function saveLeague() {
                const id = document.getElementById('league_id').value;
                const name = document.getElementById('league_name').value;
                const logo = document.getElementById('league_logo').value;

                if(!name) {
                    Swal.fire('Uyarı', 'Lig adı zorunludur', 'warning');
                    return;
                }

                fetch('../api/admin/leagues.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ action: 'save', id: id, name: name, logo_url: logo })
                })
                .then(res => res.json())
                .then(res => {
                    if(res.status === 'success') {
                        myModal.hide();
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 1500 });
                        loadLeagues();
                    } else {
                        Swal.fire('Hata', res.message, 'error');
                    }
                });
            }

            function deleteLeague(id) {
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Lig silinecek (Soft Delete).",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Evet, Sil!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../api/admin/leagues.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + token
                            },
                            body: JSON.stringify({ action: 'delete', id: id })
                        })
                        .then(res => res.json())
                        .then(res => {
                            if(res.status === 'success') {
                                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 1500 });
                                loadLeagues();
                            }
                        });
                    }
                });
            }

            function bulkDelete() {
                let selectedIds = [];
                document.querySelectorAll('.checkItem:checked').forEach(cb => selectedIds.push(cb.value));

                if (selectedIds.length === 0) return;

                Swal.fire({
                    title: 'Emin misiniz?',
                    text: selectedIds.length + " adet lig silinecek (Soft Delete).",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Evet, Sil!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../api/admin/leagues.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + token
                            },
                            body: JSON.stringify({ action: 'bulk_delete', ids: selectedIds })
                        })
                        .then(res => res.json())
                        .then(res => {
                            if(res.status === 'success') {
                                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 1500 });
                                loadLeagues();
                            }
                        });
                    }
                });
            }
        </script>
    </body>
</html>