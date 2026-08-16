<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Promosyon Yönetimi | ACMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="assets/images/favicon.ico">

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
        <div id="layout-wrapper">
            <?php include 'topbar.php'; ?>
            <?php include 'sidebar.php'; ?>

            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Promosyon Yönetimi (Promo Engine)</h4>
                                    <div class="page-title-right">
                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#promoModal" onclick="openAddModal()">
                                            <i class="bx bx-plus me-1"></i> Yeni Promosyon Ekle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <table id="promoTable" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Uygulama</th>
                                                    <th>Kampanya Adı</th>
                                                    <th>Rozet</th>
                                                    <th>Bitiş Tarihi</th>
                                                    <th>Durum</th>
                                                    <th>İşlemler</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- JS ile doldurulacak -->
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
        </div>

        <!-- Promo Modal -->
        <div class="modal fade" id="promoModal" tabindex="-1" aria-labelledby="promoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="promoModalLabel">Yeni Promosyon Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="promoForm">
                        <div class="modal-body">
                            <input type="hidden" id="promo_id">
                            
                            <div class="mb-3">
                                <label for="app_id" class="form-label">Uygulama Seçin <span class="text-danger">*</span></label>
                                <select class="form-select" id="app_id" required>
                                    <option value="">Seçiniz...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label">Kampanya Başlığı <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" required placeholder="Örn: Hafta Sonu Fırsatı - %50 İndirim">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Açıklama (Opsiyonel)</label>
                                <textarea class="form-control" id="description" rows="3" placeholder="Kampanya detaylarını girin..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="badge_text" class="form-label">Rozet (Badge) Metni</label>
                                    <input type="text" class="form-control" id="badge_text" placeholder="Örn: %50 OFF">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label">Bitiş Tarihi <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="end_date" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Durum</label>
                                <select class="form-select" id="status">
                                    <option value="active">Aktif</option>
                                    <option value="passive">Pasif</option>
                                </select>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                            <button type="submit" class="btn btn-primary" id="btnSave">Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="right-bar">
            <div data-simplebar class="h-100">
                <div class="rightbar-title d-flex align-items-center p-3">
                    <h5 class="m-0 me-2">Theme Customizer</h5>
                    <a href="javascript:void(0);" class="right-bar-toggle ms-auto"><i class="mdi mdi-close noti-icon"></i></a>
                </div>
                <hr class="m-0" />
                <div class="p-4" style="display: none;"></div>
            </div>
        </div>
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
        
        <!-- Sweet Alerts js -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- App js -->
        <script src="assets/js/app.js?v=2"></script>
        <script src="assets/js/acms-core.js?v=2"></script>

        <script>
            const token = localStorage.getItem('acms_admin_token');
            let dataTable = null;

            document.addEventListener('DOMContentLoaded', async function () {
                await loadApps();
                initTable();
            });

            async function loadApps() {
                try {
                    const res = await fetch('../api/admin/apps.php', {
                        headers: { 'Authorization': 'Bearer ' + token }
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        const select = document.getElementById('app_id');
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

            function initTable() {
                dataTable = $('#promoTable').DataTable({
                    ajax: {
                        url: '../api/admin/promotions.php?action=list',
                        headers: { 'Authorization': 'Bearer ' + token },
                        dataSrc: 'data'
                    },
                    columns: [
                        { data: 'id' },
                        { data: 'app_name' },
                        { 
                            data: 'title',
                            render: function(data, type, row) {
                                return `<strong>${data}</strong>`;
                            }
                        },
                        {
                            data: 'badge_text',
                            render: function(data) {
                                return data ? `<span class="badge bg-primary">${data}</span>` : '-';
                            }
                        },
                        {
                            data: 'end_date',
                            render: function (data) {
                                if(!data) return '-';
                                // Tarih karşılaştırması
                                const endDate = new Date(data);
                                const now = new Date();
                                const color = endDate < now ? 'text-danger' : 'text-success';
                                return `<span class="${color}">${data}</span>`;
                            }
                        },
                        {
                            data: 'status',
                            render: function (data) {
                                return data === 'active' 
                                    ? '<span class="badge bg-success">Aktif</span>' 
                                    : '<span class="badge bg-danger">Pasif</span>';
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            render: function (data, type, row) {
                                return `
                                    <button class="btn btn-sm btn-info" onclick='editPromo(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Düzenle">
                                        <i class="bx bx-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deletePromo(${row.id})" title="Sil">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                `;
                            }
                        }
                    ],
                    language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json' }
                });
            }

            function openAddModal() {
                document.getElementById('promoForm').reset();
                document.getElementById('promo_id').value = '';
                document.getElementById('promoModalLabel').innerText = 'Yeni Promosyon Ekle';
            }

            function editPromo(row) {
                document.getElementById('promoForm').reset();
                document.getElementById('promo_id').value = row.id;
                document.getElementById('app_id').value = row.app_id;
                document.getElementById('title').value = row.title;
                document.getElementById('description').value = row.description || '';
                document.getElementById('badge_text').value = row.badge_text || '';
                
                if (row.end_date) {
                    // end_date formatı genellikle YYYY-MM-DD HH:mm:ss şeklindedir,
                    // datetime-local için ortadaki boşluğu T ile değiştirmeliyiz.
                    document.getElementById('end_date').value = row.end_date.replace(' ', 'T');
                }
                
                document.getElementById('status').value = row.status;
                
                document.getElementById('promoModalLabel').innerText = 'Promosyon Düzenle';
                var modal = new bootstrap.Modal(document.getElementById('promoModal'));
                modal.show();
            }

            document.getElementById('promoForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                let endDate = document.getElementById('end_date').value;
                if(endDate && endDate.includes('T')) {
                    endDate = endDate.replace('T', ' ') + ':00'; // MySQL formatı
                }

                const payload = {
                    action: 'save',
                    id: document.getElementById('promo_id').value,
                    app_id: document.getElementById('app_id').value,
                    title: document.getElementById('title').value,
                    description: document.getElementById('description').value,
                    badge_text: document.getElementById('badge_text').value,
                    end_date: endDate,
                    status: document.getElementById('status').value
                };

                const btn = document.getElementById('btnSave');
                btn.disabled = true;
                btn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Kaydediliyor...';

                fetch('../api/admin/promotions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = 'Kaydet';
                    if (data.status === 'success') {
                        var modalEl = document.getElementById('promoModal');
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        modal.hide();
                        dataTable.ajax.reload(null, false);
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 1500 });
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = 'Kaydet';
                    Swal.fire('Hata!', 'Sunucu hatası.', 'error');
                });
            });

            function deletePromo(id) {
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu promosyonu silmek istediğinize emin misiniz?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Evet, Sil!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../api/admin/promotions.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + token
                            },
                            body: JSON.stringify({ action: 'delete', id: id })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                dataTable.ajax.reload(null, false);
                                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 1500 });
                            } else {
                                Swal.fire('Hata!', data.message, 'error');
                            }
                        });
                    }
                });
            }
        </script>
    </body>
</html>
