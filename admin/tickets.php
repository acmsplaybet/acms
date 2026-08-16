<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Destek Talepleri (Tickets) | ACMS</title>
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
                                    <h4 class="mb-sm-0 font-size-18">Destek Talepleri (Tickets)</h4>
                                    <div class="page-title-right">
                                        <div class="btn-group" role="group">
                                            <a href="tickets.php" class="btn btn-outline-secondary" id="filter-all">Tümü</a>
                                            <a href="tickets.php?status=open" class="btn btn-outline-primary" id="filter-open">Açık</a>
                                            <a href="tickets.php?status=pending" class="btn btn-outline-warning" id="filter-pending">Bekleyen</a>
                                            <a href="tickets.php?status=closed" class="btn btn-outline-success" id="filter-closed">Çözüldü</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <table id="ticketTable" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Kullanıcı</th>
                                                    <th>Uygulama</th>
                                                    <th>Konu</th>
                                                    <th>Kategori</th>
                                                    <th>Statü</th>
                                                    <th>Tarih</th>
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

        <!-- Ticket Modal -->
        <div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ticketModalLabel">Talep Detayı (#<span id="ticketIdLabel"></span>)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Sol Taraf: Kullanıcı Mesajı -->
                            <div class="col-md-6 border-end">
                                <h6 class="text-primary mb-3">Kullanıcı Bildirimi</h6>
                                <p><strong>Kullanıcı:</strong> <span id="v_user"></span></p>
                                <p><strong>Uygulama:</strong> <span id="v_app"></span></p>
                                <p><strong>Kategori:</strong> <span id="v_category"></span></p>
                                <p><strong>Konu:</strong> <span id="v_subject"></span></p>
                                <p><strong>Tarih:</strong> <span id="v_date"></span></p>
                                <hr>
                                <div class="p-3 bg-light rounded" style="min-height: 150px; white-space: pre-wrap;" id="v_message"></div>
                            </div>

                            <!-- Sağ Taraf: Admin İşlemleri -->
                            <div class="col-md-6">
                                <h6 class="text-success mb-3">Yönetici İşlemleri</h6>
                                
                                <form id="replyForm">
                                    <input type="hidden" id="ticket_id">
                                    
                                    <div class="mb-3">
                                        <label for="admin_reply" class="form-label">Yanıtınız</label>
                                        <textarea class="form-control" id="admin_reply" rows="6" placeholder="Kullanıcıya iletilecek yanıtı yazın..."></textarea>
                                        <small class="text-muted">Yanıt gönderildiğinde statü otomatik olarak 'Çözüldü' olacaktır.</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 mb-4" id="btnReply">Yanıtı Gönder ve Kapat</button>
                                </form>

                                <hr>

                                <form id="statusForm">
                                    <div class="mb-3">
                                        <label for="ticket_status" class="form-label">Statü Değiştir (Manuel)</label>
                                        <select class="form-select" id="ticket_status">
                                            <option value="open">Açık (Open)</option>
                                            <option value="pending">Beklemede (Pending)</option>
                                            <option value="closed">Çözüldü (Closed)</option>
                                            <option value="cancelled">İptal Edildi (Cancelled)</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-outline-secondary w-100" id="btnStatus">Sadece Statüyü Güncelle</button>
                                </form>

                            </div>
                        </div>
                    </div>
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
            const urlParams = new URLSearchParams(window.location.search);
            const statusFilter = urlParams.get('status') || '';
            let dataTable = null;

            document.addEventListener('DOMContentLoaded', function () {
                // Filtre butonunu aktif yap
                if (statusFilter === 'open') {
                    document.getElementById('filter-open').classList.replace('btn-outline-primary', 'btn-primary');
                } else if (statusFilter === 'pending') {
                    document.getElementById('filter-pending').classList.replace('btn-outline-warning', 'btn-warning');
                } else if (statusFilter === 'closed') {
                    document.getElementById('filter-closed').classList.replace('btn-outline-success', 'btn-success');
                } else {
                    document.getElementById('filter-all').classList.replace('btn-outline-secondary', 'btn-secondary');
                }

                initTable();
            });

            function initTable() {
                dataTable = $('#ticketTable').DataTable({
                    ajax: {
                        url: `../api/admin/tickets.php?action=list&status=${statusFilter}`,
                        headers: { 'Authorization': 'Bearer ' + token },
                        dataSrc: 'data'
                    },
                    columns: [
                        { data: 'id' },
                        { data: 'user_name' },
                        { data: 'app_name' },
                        { 
                            data: 'subject',
                            render: function(data) {
                                return `<strong>${data || '-'}</strong>`;
                            }
                        },
                        { data: 'category' },
                        {
                            data: 'status',
                            render: function (data) {
                                let badge = '';
                                if(data === 'open') badge = '<span class="badge bg-primary">Açık</span>';
                                else if(data === 'pending') badge = '<span class="badge bg-warning">Beklemede</span>';
                                else if(data === 'closed') badge = '<span class="badge bg-success">Çözüldü</span>';
                                else if(data === 'cancelled') badge = '<span class="badge bg-secondary">İptal</span>';
                                else badge = `<span class="badge bg-dark">${data}</span>`;
                                return badge;
                            }
                        },
                        { data: 'created_at' },
                        {
                            data: null,
                            orderable: false,
                            render: function (data, type, row) {
                                return `
                                    <button class="btn btn-sm btn-info" onclick="openTicket(${row.id})" title="Detay / Yanıtla">
                                        <i class="bx bx-message-square-detail"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteTicket(${row.id})" title="Sil">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                `;
                            }
                        }
                    ],
                    order: [[ 0, "desc" ]],
                    language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json' }
                });
            }

            function openTicket(id) {
                document.getElementById('ticket_id').value = id;
                document.getElementById('ticketIdLabel').innerText = id;
                
                // Clear old data
                document.getElementById('v_user').innerText = 'Yükleniyor...';
                document.getElementById('v_app').innerText = 'Yükleniyor...';
                document.getElementById('v_category').innerText = '';
                document.getElementById('v_subject').innerText = '';
                document.getElementById('v_date').innerText = '';
                document.getElementById('v_message').innerText = '';
                document.getElementById('admin_reply').value = '';
                
                fetch(`../api/admin/tickets.php?action=get&id=${id}`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const t = data.data;
                        document.getElementById('v_user').innerText = t.user_name || '-';
                        document.getElementById('v_app').innerText = t.app_name || '-';
                        document.getElementById('v_category').innerText = t.category || '-';
                        document.getElementById('v_subject').innerText = t.subject || '-';
                        document.getElementById('v_date').innerText = t.created_at;
                        document.getElementById('v_message').innerText = t.message || '-';
                        document.getElementById('admin_reply').value = t.admin_reply || '';
                        document.getElementById('ticket_status').value = t.status || 'open';
                        
                        var modal = new bootstrap.Modal(document.getElementById('ticketModal'));
                        modal.show();
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Hata!', 'Talep detayları alınamadı.', 'error');
                });
            }

            document.getElementById('replyForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const payload = {
                    action: 'reply',
                    id: document.getElementById('ticket_id').value,
                    admin_reply: document.getElementById('admin_reply').value
                };

                const btn = document.getElementById('btnReply');
                btn.disabled = true;
                btn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Gönderiliyor...';

                fetch('../api/admin/tickets.php', {
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
                    btn.innerHTML = 'Yanıtı Gönder ve Kapat';
                    if (data.status === 'success') {
                        var modalEl = document.getElementById('ticketModal');
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        modal.hide();
                        dataTable.ajax.reload(null, false);
                        updateTicketBadge(); // Update global badge if logic added
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 1500 });
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = 'Yanıtı Gönder ve Kapat';
                    Swal.fire('Hata!', 'Sunucu hatası.', 'error');
                });
            });

            document.getElementById('statusForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const payload = {
                    action: 'update_status',
                    id: document.getElementById('ticket_id').value,
                    status: document.getElementById('ticket_status').value
                };

                const btn = document.getElementById('btnStatus');
                btn.disabled = true;
                btn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Güncelleniyor...';

                fetch('../api/admin/tickets.php', {
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
                    btn.innerHTML = 'Sadece Statüyü Güncelle';
                    if (data.status === 'success') {
                        var modalEl = document.getElementById('ticketModal');
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        modal.hide();
                        dataTable.ajax.reload(null, false);
                        updateTicketBadge();
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 1500 });
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = 'Sadece Statüyü Güncelle';
                    Swal.fire('Hata!', 'Sunucu hatası.', 'error');
                });
            });

            function deleteTicket(id) {
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu destek talebini silmek istediğinize emin misiniz?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Evet, Sil!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../api/admin/tickets.php', {
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
                                updateTicketBadge();
                                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 1500 });
                            } else {
                                Swal.fire('Hata!', data.message, 'error');
                            }
                        });
                    }
                });
            }

            // Optional: Helper function to call global update method if it exists in acms-core.js
            function updateTicketBadge() {
                if(typeof updateGlobalTicketBadge === 'function') {
                    updateGlobalTicketBadge();
                }
            }
        </script>
    </body>
</html>
