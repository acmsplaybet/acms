<?php
require_once '../api/config/Database.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Marka Yönetimi | ACMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
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
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Marka Yönetimi</h4>
                                <div class="page-title-right">
                                    <button class="btn btn-primary" onclick="openModal()"><i class="bx bx-plus me-1"></i> Yeni Marka Ekle</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-hover align-middle table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Marka Adı</th>
                                                <th>Slug</th>
                                                <th>Varsayılan Tema</th>
                                                <th>Uygulama Sayısı</th>
                                                <th>Durum</th>
                                                <th>İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody id="brandTableBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="brandModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="brandForm" onsubmit="saveBrand(event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="brandModalTitle">Yeni Marka Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="brandId" name="id">
                        <input type="hidden" id="brandAction" name="action" value="create">
                        
                        <div class="mb-3">
                            <label class="form-label">Marka Adı</label>
                            <input type="text" class="form-control" id="brandName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control" id="brandSlug" name="slug" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Varsayılan Tema</label>
                            <select class="form-select" id="brandTheme" name="default_theme" required>
                                <option value="real">Real Theme (Dark)</option>
                                <option value="pep">Pep Theme</option>
                                <option value="alex">Alex Theme</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Açıklama</label>
                            <input type="text" class="form-control" id="brandDescription" name="description">
                        </div>
                        <div class="mb-3 form-check" id="activeContainer" style="display:none;">
                            <input type="checkbox" class="form-check-input" id="brandActive" name="is_active" value="1" checked>
                            <label class="form-check-label">Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/app.js?v=2"></script>
    <script src="assets/js/acms-core.js?v=2"></script>
    <script>
        let modal;
        document.addEventListener("DOMContentLoaded", function() {
            modal = new bootstrap.Modal(document.getElementById('brandModal'));
            loadBrands();
        });
        
        document.getElementById('brandName').addEventListener('input', function() {
            if (document.getElementById('brandAction').value === 'create') {
                document.getElementById('brandSlug').value = this.value.toLowerCase().replace(/\\s+/g, '-').replace(/[^a-z0-9-]/g, '');
            }
        });

        function loadBrands() {
            fetch('../api/admin/brands.php?action=list')
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    let html = '';
                    res.data.forEach(b => {
                        let status = b.is_active == 1 ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Pasif</span>';
                        html += `<tr>
                            <td>${b.id}</td>
                            <td>${b.name}</td>
                            <td>${b.slug}</td>
                            <td>${b.default_theme}</td>
                            <td>${b.app_count}</td>
                            <td>${status}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick='editBrand(${JSON.stringify(b).replace(/'/g, "&#39;")})'><i class="bx bx-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteBrand(${b.id})"><i class="bx bx-trash"></i></button>
                            </td>
                        </tr>`;
                    });
                    document.getElementById('brandTableBody').innerHTML = html;
                }
            });
        }

        function openModal() {
            document.getElementById('brandForm').reset();
            document.getElementById('brandAction').value = 'create';
            document.getElementById('brandId').value = '';
            document.getElementById('activeContainer').style.display = 'none';
            document.getElementById('brandModalTitle').innerText = 'Yeni Marka Ekle';
            modal.show();
        }

        function editBrand(b) {
            document.getElementById('brandAction').value = 'update';
            document.getElementById('brandId').value = b.id;
            document.getElementById('brandName').value = b.name;
            document.getElementById('brandSlug').value = b.slug;
            document.getElementById('brandTheme').value = b.default_theme;
            document.getElementById('brandDescription').value = b.description || '';
            document.getElementById('brandActive').checked = (b.is_active == 1);
            document.getElementById('activeContainer').style.display = 'block';
            document.getElementById('brandModalTitle').innerText = 'Marka Düzenle';
            modal.show();
        }

        function saveBrand(e) {
            e.preventDefault();
            const formData = new FormData(document.getElementById('brandForm'));
            fetch('../api/admin/brands.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    modal.hide();
                    loadBrands();
                    Swal.fire('Başarılı', 'Marka kaydedildi.', 'success');
                } else {
                    Swal.fire('Hata', res.message, 'error');
                }
            });
        }

        function deleteBrand(id) {
            Swal.fire({
                title: 'Emin misiniz?', text: "Markayı silmek üzeresiniz.", icon: 'warning',
                showCancelButton: true, confirmButtonText: 'Evet, Sil!', cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const fd = new FormData();
                    fd.append('action', 'delete');
                    fd.append('id', id);
                    fetch('../api/admin/brands.php', { method: 'POST', body: fd })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            loadBrands();
                            Swal.fire('Silindi!', '', 'success');
                        } else {
                            Swal.fire('Hata', res.message, 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
