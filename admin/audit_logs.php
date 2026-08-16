<?php
// Ensure this matches your general admin logic
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Terminal & İşlem Geçmişi | ACMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- DataTables -->
    <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <!-- Responsive datatable examples -->
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

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Terminal & İşlem Geçmişi</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <table id="auditTable" class="table table-bordered dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Admin</th>
                                                <th>İşlem (Action)</th>
                                                <th>Detay</th>
                                                <th>IP Adresi</th>
                                                <th>Tarih</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data injected by JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> <!-- end col -->
                    </div> <!-- end row -->

                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

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

    <!-- Buttons examples -->
    <script src="assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
    <script src="assets/libs/jszip/jszip.min.js"></script>
    <script src="assets/libs/pdfmake/build/pdfmake.min.js"></script>
    <script src="assets/libs/pdfmake/build/vfs_fonts.js"></script>
    <script src="assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>

    <script src="assets/js/app.js?v=2"></script>
    <script src="assets/js/acms-core.js?v=2"></script>
    
    <script>
        const token = localStorage.getItem('acms_admin_token');

        $(document).ready(function() {
            var table = $('#auditTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json"
                },
                "order": [[5, "desc"]], // Tarihe göre azalan
                "dom": 'Bfrtip',
                "buttons": ['copy', 'excel', 'pdf'],
                "ajax": {
                    "url": "../api/admin/audit_logs.php",
                    "type": "GET",
                    "headers": {
                        "Authorization": "Bearer " + token
                    },
                    "dataSrc": function (json) {
                        if (json.status === 'success') {
                            return json.data;
                        } else {
                            console.error(json.message);
                            return [];
                        }
                    }
                },
                "columns": [
                    { "data": "id" },
                    { "data": "admin_name" },
                    { 
                        "data": "action",
                        "render": function (data, type, row) {
                            let badgeClass = 'bg-secondary';
                            let displayAction = data;
                            
                            // Dictionary mechanism for translations
                            const actionDict = {
                                "ban_user": "Kullanıcı Banlandı",
                                "unban_user": "Ban Kaldırıldı",
                                "update_user_status": "Kullanıcı Statü Değişimi",
                                "restore_from_trash": "Çöpten Kurtarıldı",
                                "hard_delete": "Kalıcı Silindi",
                                "delete_app": "Uygulama Silindi",
                                "update_app": "Uygulama Güncellendi",
                                "add_app": "Uygulama Eklendi",
                                "delete_match": "Maç Silindi",
                                "set_match_result": "Maç Sonuçlandırıldı",
                                "bulk_resolve_matches": "Toplu Maç Sonuçlandırma",
                                "add_match": "Maç Eklendi",
                                "update_match": "Maç Güncellendi",
                                "bulk_update_users": "Toplu Kullanıcı İşlemi"
                            };

                            if (actionDict[data]) {
                                displayAction = actionDict[data];
                            } else {
                                // Fallback format (örn: bilmedigimiz_bir_islem -> Bilmedigimiz Bir Islem)
                                displayAction = data.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            }
                            
                            // Renk kodlaması
                            if (data.includes('delete') || data === 'ban_user') badgeClass = 'bg-danger';
                            else if (data.includes('add') || data.includes('restore') || data === 'unban_user') badgeClass = 'bg-success';
                            else if (data.includes('update') || data.includes('set_match')) badgeClass = 'bg-info';
                            else if (data.includes('bulk')) badgeClass = 'bg-warning';
                            
                            if (data === "restore_from_trash") badgeClass = 'bg-warning text-dark';
                            if (data === "hard_delete") badgeClass = 'bg-dark';
                            
                            return `<span class="badge ${badgeClass}">${displayAction}</span>`;
                        }
                    },
                    { 
                        "data": "details",
                        "render": function (data, type, row) {
                            if(!data) return '';
                            let formatted = data;
                            formatted = formatted.replace(/User ID/g, "Kullanıcı ID");
                            formatted = formatted.replace(/App ID/g, "Uygulama ID");
                            formatted = formatted.replace(/Match ID/g, "Maç ID");
                            formatted = formatted.replace(/banned\. Reason/g, "banlandı. Sebep");
                            formatted = formatted.replace(/unbanned/g, "banı kaldırıldı");
                            formatted = formatted.replace(/status updated to/g, "statüsü şuna güncellendi:");
                            formatted = formatted.replace(/restored/g, "geri yüklendi");
                            formatted = formatted.replace(/permanently deleted/g, "kalıcı olarak silindi");
                            formatted = formatted.replace(/deleted/g, "silindi");
                            formatted = formatted.replace(/updated/g, "güncellendi");
                            formatted = formatted.replace(/added/g, "eklendi");
                            formatted = formatted.replace(/result set to/g, "sonucu olarak atandı:");
                            formatted = formatted.replace(/Score:/g, "Skor:");
                            formatted = formatted.replace(/Bulk resolve:/g, "Toplu sonuçlandırma:");
                            formatted = formatted.replace(/matches updated/g, "maç güncellendi");
                            formatted = formatted.replace(/skipped/g, "atlandı");
                            formatted = formatted.replace(/Bulk action:/g, "Toplu işlem:");
                            formatted = formatted.replace(/on/g, "uygulanan kayıt sayısı:");
                            formatted = formatted.replace(/users/g, "kullanıcılar");
                            return formatted;
                        }
                    },
                    { "data": "ip_address" },
                    { "data": "created_at" }
                ]
            });
        });
    </script>
</body>
</html>
