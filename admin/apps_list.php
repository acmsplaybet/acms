<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <title>Uygulama Yönetimi | ACMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <!-- DataTables -->
    <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" /> 
    <!-- Bootstrap & App Css -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
</head>
<body>
    <div id="layout-wrapper">
        <?php include 'topbar.php'; ?>
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- Sayfa Başlığı -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Uygulama Yönetimi</h4>
                                <div class="page-title-right">
                                    <a href="app_add.php" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Yeni Uygulama Ekle</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Marka Filtreleme Barı -->
                    <?php
                    require_once '../api/config/Database.php';
                    $localDb = Database::getInstance()->getConnection();
                    $brandsAll = $localDb->query("SELECT id, name FROM brands WHERE is_active = 1 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
                    $urlBrandId = isset($_GET['brand_id']) ? $_GET['brand_id'] : '';
                    ?>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                <span class="fw-semibold text-muted small">Markaya göre filtrele:</span>
                                <a href="?brand_id=" class="btn btn-sm <?= empty($urlBrandId) ? 'btn-primary' : 'btn-outline-secondary' ?>">Tümü</a>
                                <?php foreach ($brandsAll as $b): ?>
                                <a href="?brand_id=<?= $b['id'] ?>"
                                   class="btn btn-sm <?= ($urlBrandId == $b['id']) ? 'btn-primary' : 'btn-outline-secondary' ?>">
                                    <?= htmlspecialchars($b['name']) ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <!-- Tablo Kartı -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <table id="apps-datatable" class="table table-bordered dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Marka</th>
                                                <th>Uygulama Adı</th>
                                                <th>Tip</th>
                                                <th>Frontend URL</th>
                                                <th>Durum</th>
                                                <th>İşlem</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/libs/pace-js/pace.min.js"></script>
    <!-- DataTables JS -->
    <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/app.js?v=2"></script>
    <script src="assets/js/acms-core.js?v=2"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlBrandId = new URLSearchParams(window.location.search).get('brand_id');
            const apiUrl = '../api/admin/apps.php' + (urlBrandId ? '?brand_id=' + urlBrandId : '');
            
            fetch(apiUrl, {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => res.json())
            .then(response => {
                if(response.status === 'success') {
                    let tbody = '';
                    response.data.forEach(app => {
                        let statusBadge = app.is_active == 1 ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Pasif</span>';
                        tbody += `
                            <tr>
                                <td>${app.id}</td>
                                <td>${app.brand_name}</td>
                                <td>${app.app_name}</td>
                                <td>${app.app_type.toUpperCase()}</td>
                                <td><a href="https://${app.frontend_url}" target="_blank">${app.frontend_url}</a></td>
                                <td>${statusBadge}</td>
                                <td>
                                    <a href="../app/index.html?app_id=${app.id}&preview=1" target="_blank" class="btn btn-sm btn-outline-info me-1" title="Uygulamayı Görüntüle"><i class="bx bx-show"></i></a>
                                    <a href="app_edit.php?id=${app.id}" class="btn btn-sm btn-outline-primary me-1" title="Düzenle"><i class="bx bx-pencil"></i></a>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteApp(${app.id})" title="Sil"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    document.querySelector('#apps-datatable tbody').innerHTML = tbody;
                    $('#apps-datatable').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json'
                        }
                    });
                }
            })
            .catch(err => console.error(err));
        });
        function deleteApp(id) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu uygulamayı silmek istediğinize emin misiniz?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Evet, Sil!',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('../api/admin/apps.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'delete', id: id })
                    })
                    .then(res => res.json())
                    .then(response => {
                        if(response.status === 'success') {
                            Swal.fire('Silindi!', response.message, 'success')
                            .then(() => window.location.reload());
                        } else {
                            Swal.fire('Hata!', response.message, 'error');
                        }
                    })
                    .catch(err => Swal.fire('Hata!', 'Bir sorun oluştu.', 'error'));
                }
            });
        }
    </script>
</body>
</html>