<?php
// Matches List
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Tüm Maçlar | ACMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- DataTables -->
        <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" /> 

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    </head>
<body>
    <div id="layout-wrapper">
        <?php include 'topbar.php'; ?>
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Maç Yönetimi</h4>
                                <div class="page-title-right">
                                    <button class="btn btn-danger me-2" onclick="bulkDelete()" id="btnBulkDelete" style="display:none;"><i class="bx bx-trash me-1"></i> Seçilenleri Sil</button>
                                    <a href="match_add.php" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Yeni Maç Ekle</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-bordered nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th style="width: 40px;">
                                                        <div class="form-check font-size-16">
                                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                                        </div>
                                                    </th>
                                                    <th>ID</th>
                                                    <th>Tarih</th>
                                                    <th>Lig</th>
                                                    <th>Karşılaşma</th>
                                                    <th>Tahmin</th>
                                                    <th>Oran</th>
                                                    <th>Skor</th>
                                                    <th>Statü</th>
                                                    <th>Yayınlanan</th>
                                                    <th>İşlemler</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
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

    <script src="assets/js/app.js?v=2"></script>
    <script src="assets/js/acms-core.js?v=2"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
<script>
    let dataTable = null;

    // Global icon fallback helper — onerror içinde değişken interpolasyonu çalışmaz
    function acmsImgError(el, type) {
        const icon = type === 'league'
            ? `<i class="mdi mdi-trophy text-secondary me-1" style="font-size:18px;vertical-align:middle;"></i>`
            : `<i class="mdi mdi-shield-half-full text-secondary me-1" style="font-size:18px;vertical-align:middle;"></i>`;
        el.outerHTML = icon;
    }

    function loadMatches() {
        const token = localStorage.getItem('acms_admin_token');
        
        fetch('../api/admin/matches.php?action=list', {
            headers: {
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                if (dataTable !== null) {
                    dataTable.destroy();
                }
                const tbody = document.querySelector('#datatable tbody');
                tbody.innerHTML = '';
                
                data.data.forEach(match => {
                    let botBadge = match.is_bot_added == 1 ? '<span class="badge bg-primary ms-1"><i class="mdi mdi-robot"></i> Bot</span>' : '';
                    // Status Badge
                    let statusBadge = '';
                    switch(match.status) {
                        case 'pending': statusBadge = '<span class="badge bg-warning">Bekliyor</span>'; break;
                        case 'win': statusBadge = '<span class="badge bg-success">Kazandı</span>'; break;
                        case 'lose': statusBadge = '<span class="badge bg-danger">Kaybetti</span>'; break;
                        case 'postponed': statusBadge = '<span class="badge bg-secondary">Ertelendi</span>'; break;
                        case 'half-won': statusBadge = '<span class="badge bg-info">Yarı Kazanç</span>'; break;
                        case 'half-lost': statusBadge = '<span class="badge bg-danger">Yarı Kayıp</span>'; break;
                        case 'void': statusBadge = '<span class="badge bg-dark">İade</span>'; break;
                        default: statusBadge = '<span class="badge bg-light text-dark">' + match.status + '</span>';
                    }
                    
                    const published = match.published_apps ? match.published_apps : '<span class="text-muted">Atanmadı</span>';
                    
                    let quickActions = '';
                    if (match.status === 'pending') {
                        quickActions = `
                            <button class="btn btn-sm btn-soft-success me-1" onclick="quickResolve(${match.id}, 'win')" title="Kazandı"><i class="mdi mdi-check"></i></button>
                            <button class="btn btn-sm btn-soft-danger me-1" onclick="quickResolve(${match.id}, 'lose')" title="Kaybetti"><i class="mdi mdi-close"></i></button>
                        `;
                    }

                    const tr = document.createElement('tr');
                    
                    const leagueHtml = match.league_logo
                        ? `<img src="${match.league_logo}" height="20" class="me-1" style="vertical-align:middle;" onerror="acmsImgError(this,'league')"> ${match.league_name}`
                        : `<i class="mdi mdi-trophy text-secondary me-1" style="font-size:18px;vertical-align:middle;"></i> ${match.league_name}`;
                    const htHtml = match.ht_logo
                        ? `<img src="${match.ht_logo}" height="20" class="me-1" style="vertical-align:middle;" onerror="acmsImgError(this,'team')"> ${match.ht_name || match.home_team}`
                        : `<i class="mdi mdi-shield-half-full text-secondary me-1" style="font-size:18px;vertical-align:middle;"></i> ${match.ht_name || match.home_team}`;
                    const atHtml = match.at_logo
                        ? `<img src="${match.at_logo}" height="20" class="me-1" style="vertical-align:middle;" onerror="acmsImgError(this,'team')"> ${match.at_name || match.away_team}`
                        : `<i class="mdi mdi-shield-half-full text-secondary me-1" style="font-size:18px;vertical-align:middle;"></i> ${match.at_name || match.away_team}`;
                    const matchHtml = htHtml + ' - ' + atHtml;

                    tr.innerHTML = `
                        <td>
                            <div class="form-check font-size-16">
                                <input class="form-check-input checkItem" type="checkbox" value="${match.id}">
                            </div>
                        </td>
                        <td>#${match.id}</td>
                        <td>${match.match_date}</td>
                        <td>${leagueHtml}</td>
                        <td>
                            <strong>${matchHtml}</strong>
                            ${botBadge}
                        </td>
                        <td>${match.prediction}</td>
                        <td>${match.odds}</td>
                        <td>${match.score ? '<span class="badge bg-light text-dark border">' + match.score + '</span>' : '-'}</td>
                        <td>${statusBadge}</td>
                        <td>${published}</td>
                        <td>
                            ${quickActions}
                            <a href="match_edit.php?id=${match.id}" class="btn btn-sm btn-soft-primary me-1" title="Düzenle"><i class="mdi mdi-pencil"></i></a>
                            <button class="btn btn-sm btn-soft-danger" onclick="deleteMatch(${match.id})" title="Sil"><i class="mdi mdi-trash-can"></i></button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                
                // Initialize DataTable
                dataTable = $('#datatable').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json"
                    },
                    "stateSave": true,
                    "columnDefs": [
                        { orderable: false, targets: [0, 10] }
                    ],
                    "order": [[1, 'desc']]
                });
                if(document.getElementById('checkAll')) document.getElementById('checkAll').checked = false;
                if(typeof toggleBulkDeleteButton === 'function') toggleBulkDeleteButton();
            } else {
                Swal.fire('Hata!', data.message || 'Veri yüklenemedi.', 'error');
            }
        })
        .catch(error => {
            Swal.fire('Hata!', 'Sunucu ile iletişim kurulamadı. ' + error, 'error');
        });
    }

    function quickResolve(id, status) {
        Swal.fire({
            title: 'Hızlı Sonuçlandırma',
            html: `
                <input id="swal-input-score" class="swal2-input" placeholder="Skor (Örn: 2-1)" required>
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Kay কাশী',
            cancelButtonText: 'İptal',
            preConfirm: () => {
                const score = document.getElementById('swal-input-score').value;
                if (!score && (status === 'win' || status === 'lose')) {
                    Swal.showValidationMessage('Skor girilmesi zorunludur!');
                }
                return { score: score }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const token = localStorage.getItem('acms_admin_token');
                fetch('../api/admin/matches.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({
                        action: 'set_result',
                        id: id,
                        status: status,
                        score: result.value.score
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Başarılı!', data.message, 'success');
                        loadMatches();
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Hata!', 'Sunucu ile iletişim kurulamadı.', 'error');
                });
            }
        });
    }

    function deleteMatch(id) {
        Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu maçı silmek istediğinize emin misiniz? (Soft delete uygulanır)",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet, Sil!',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                const token = localStorage.getItem('acms_admin_token');
                fetch('../api/admin/matches.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ action: 'delete', id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Silindi!', data.message, 'success');
                        loadMatches();
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Hata!', 'Sunucu ile iletişim kurulamadı.', 'error');
                });
            }
        });
    }

    function toggleBulkDeleteButton() {
        let checkedCount = document.querySelectorAll('.checkItem:checked').length;
        let btn = document.getElementById('btnBulkDelete');
        if(btn) btn.style.display = checkedCount > 0 ? 'inline-block' : 'none';
    }

    document.addEventListener('change', function(e) {
        if(e.target && e.target.id === 'checkAll') {
            let checkboxes = document.querySelectorAll('.checkItem');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
            toggleBulkDeleteButton();
        } else if (e.target && e.target.classList.contains('checkItem')) {
            toggleBulkDeleteButton();
            let allChecked = document.querySelectorAll('.checkItem:checked').length === document.querySelectorAll('.checkItem').length;
            let checkAll = document.getElementById('checkAll');
            if(checkAll) checkAll.checked = allChecked;
        }
    });

    function bulkDelete() {
        let checkboxes = document.querySelectorAll('.checkItem:checked');
        if (checkboxes.length === 0) return;

        let ids = [];
        checkboxes.forEach(cb => ids.push(cb.value));

        Swal.fire({
            title: 'Emin misiniz?',
            text: ids.length + " adet maçı silmek istediğinize emin misiniz?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet, Sil!',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                const token = localStorage.getItem('acms_admin_token');
                fetch('../api/admin/matches.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ action: 'bulk_delete', ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Silindi!', data.message, 'success');
                        loadMatches();
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Hata!', 'Sunucu ile iletişim kurulamadı.', 'error');
                });
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        loadMatches();
    });
</script>
</body>
</html>
