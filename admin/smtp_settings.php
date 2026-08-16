<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>SMTP Ayarları | ACMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="assets/images/favicon.ico">

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
                                    <h4 class="mb-sm-0 font-size-18">Genel & SMTP Ayarları</h4>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-8">
                                <div class="card">
                                    <div class="card-header bg-transparent border-bottom">
                                        <h5 class="card-title mb-0"><i class="bx bx-mail-send me-2 text-primary"></i> E-Posta Gönderim (SMTP) Konfigürasyonu</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="smtpForm">
                                            <div class="row">
                                                <div class="col-md-8 mb-3">
                                                    <label for="smtp_host" class="form-label">SMTP Sunucusu (Host)</label>
                                                    <input type="text" class="form-control" id="smtp_host" placeholder="Örn: smtp.gmail.com">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="smtp_port" class="form-label">Port</label>
                                                    <input type="number" class="form-control" id="smtp_port" placeholder="Örn: 465">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="smtp_username" class="form-label">Kullanıcı Adı (Username)</label>
                                                    <input type="text" class="form-control" id="smtp_username" placeholder="Örn: no-reply@example.com">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="smtp_password" class="form-label">Şifre (Password)</label>
                                                    <input type="password" class="form-control" id="smtp_password" placeholder="Değiştirmek istemiyorsanız boş bırakın">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="smtp_from_name" class="form-label">Gönderen Adı (From Name)</label>
                                                    <input type="text" class="form-control" id="smtp_from_name" placeholder="Örn: ACMS Support">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="smtp_from_email" class="form-label">Gönderen E-posta (From Email)</label>
                                                    <input type="email" class="form-control" id="smtp_from_email" placeholder="Örn: support@example.com">
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label for="smtp_encryption" class="form-label">Şifreleme (Encryption)</label>
                                                <select class="form-select" id="smtp_encryption">
                                                    <option value="">Yok (None)</option>
                                                    <option value="tls">TLS</option>
                                                    <option value="ssl">SSL</option>
                                                </select>
                                            </div>

                                            <div>
                                                <button type="submit" class="btn btn-primary" id="btnSave">
                                                    <i class="bx bx-save me-1"></i> Ayarları Kaydet
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="card bg-primary-subtle border-primary">
                                    <div class="card-body">
                                        <h5 class="text-primary"><i class="bx bx-info-circle"></i> Bilgi</h5>
                                        <p class="mt-3 text-muted">
                                            SMTP ayarları, uygulamanın şifre sıfırlama, hoşgeldin e-postaları veya sistem bildirimlerini gönderebilmesi için gereklidir. Lütfen mail servis sağlayıcınızın verdiği bilgileri tam ve eksiksiz girin.
                                        </p>
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
        
        <!-- Sweet Alerts js -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- App js -->
        <script src="assets/js/app.js?v=2"></script>
        <script src="assets/js/acms-core.js?v=2"></script>

        <script>
            const token = localStorage.getItem('acms_admin_token');

            document.addEventListener('DOMContentLoaded', function () {
                loadSettings();
            });

            function loadSettings() {
                fetch('../api/admin/settings.php?action=get_smtp', {
                    headers: { 'Authorization': 'Bearer ' + token }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const d = data.data;
                        document.getElementById('smtp_host').value = d.smtp_host || '';
                        document.getElementById('smtp_port').value = d.smtp_port || '';
                        document.getElementById('smtp_username').value = d.smtp_username || '';
                        document.getElementById('smtp_from_name').value = d.smtp_from_name || '';
                        document.getElementById('smtp_from_email').value = d.smtp_from_email || '';
                        document.getElementById('smtp_encryption').value = d.smtp_encryption || '';
                        // Password is left blank purposefully
                    }
                })
                .catch(err => console.error('Ayar yükleme hatası', err));
            }

            document.getElementById('smtpForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const payload = {
                    action: 'save_smtp',
                    smtp_host: document.getElementById('smtp_host').value,
                    smtp_port: document.getElementById('smtp_port').value,
                    smtp_username: document.getElementById('smtp_username').value,
                    smtp_password: document.getElementById('smtp_password').value,
                    smtp_from_name: document.getElementById('smtp_from_name').value,
                    smtp_from_email: document.getElementById('smtp_from_email').value,
                    smtp_encryption: document.getElementById('smtp_encryption').value
                };

                const btn = document.getElementById('btnSave');
                btn.disabled = true;
                btn.innerHTML = '<i class="bx bx-loader bx-spin me-1"></i> Kaydediliyor...';

                fetch('../api/admin/settings.php', {
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
                    btn.innerHTML = '<i class="bx bx-save me-1"></i> Ayarları Kaydet';
                    
                    if (data.status === 'success') {
                        document.getElementById('smtp_password').value = ''; // Reset password field
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bx bx-save me-1"></i> Ayarları Kaydet';
                    Swal.fire('Hata!', 'Sunucu hatası.', 'error');
                });
            });
        </script>
    </body>
</html>
