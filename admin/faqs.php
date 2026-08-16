<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>S.S.S Yönetimi | ACMS</title>
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
                                    <h4 class="mb-sm-0 font-size-18">Sıkça Sorulan Sorular (FAQ)</h4>
                                    <div class="page-title-right">
                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#faqModal" onclick="openAddModal()">
                                            <i class="bx bx-plus me-1"></i> Yeni SSS Ekle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <table id="faqTable" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Uygulama</th>
                                                    <th>Soru</th>
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

        <!-- FAQ Modal -->
        <div class="modal fade" id="faqModal" tabindex="-1" aria-labelledby="faqModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="faqModalLabel">Yeni SSS Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="faqForm">
                        <div class="modal-body">
                            <input type="hidden" id="faq_id">
                            
                            <div class="mb-3">
                                <label for="app_id" class="form-label">Uygulama Seçin <span class="text-danger">*</span></label>
                                <select class="form-select" id="app_id" required>
                                    <option value="">Seçiniz...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="question" class="form-label">Soru <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="question" required placeholder="Örn: Kuponumu ne zaman görebilirim?">
                            </div>

                            <div class="mb-3">
                                <label for="answer" class="form-label">Cevap <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="answer" rows="4" required placeholder="Cevabı buraya yazın..."></textarea>
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
                dataTable = $('#faqTable').DataTable({
                    ajax: {
                        url: '../api/admin/faqs.php?action=list',
                        headers: { 'Authorization': 'Bearer ' + token },
                        dataSrc: 'data'
                    },
                    columns: [
                        { data: 'id' },
                        { data: 'app_name' },
                        { 
                            data: 'question',
                            render: function(data, type, row) {
                                return `<strong>${data}</strong>`;
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
                                    <button class="btn btn-sm btn-info" onclick='editFaq(${JSON.stringify(row).replace(/'/g, "&apos;")})' title="Düzenle">
                                        <i class="bx bx-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteFaq(${row.id})" title="Sil">
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
                document.getElementById('faqForm').reset();
                document.getElementById('faq_id').value = '';
                document.getElementById('faqModalLabel').innerText = 'Yeni SSS Ekle';
            }

            function editFaq(row) {
                document.getElementById('faqForm').reset();
                document.getElementById('faq_id').value = row.id;
                document.getElementById('app_id').value = row.app_id;
                document.getElementById('question').value = row.question;
                document.getElementById('answer').value = row.answer;
                document.getElementById('status').value = row.status;
                
                document.getElementById('faqModalLabel').innerText = 'SSS Düzenle';
                var modal = new bootstrap.Modal(document.getElementById('faqModal'));
                modal.show();
            }

            document.getElementById('faqForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const payload = {
                    action: 'save',
                    id: document.getElementById('faq_id').value,
                    app_id: document.getElementById('app_id').value,
                    question: document.getElementById('question').value,
                    answer: document.getElementById('answer').value,
                    status: document.getElementById('status').value
                };

                const btn = document.getElementById('btnSave');
                btn.disabled = true;
                btn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Kaydediliyor...';

                fetch('../api/admin/faqs.php', {
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
                        var modalEl = document.getElementById('faqModal');
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

            function deleteFaq(id) {
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu soruyu silmek istediğinize emin misiniz?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Evet, Sil!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../api/admin/faqs.php', {
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
