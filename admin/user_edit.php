<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Kullanıcı Düzenle | ACMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="assets/images/favicon.ico">
        <link rel="stylesheet" href="assets/css/preloader.min.css" type="text/css" />
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                                    <h4 class="mb-sm-0 font-size-18" id="pageTitle">Yeni Kullanıcı Ekle</h4>
                                    <div class="page-title-right">
                                        <a href="users_list.php" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Listeye Dön</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="userForm">
                                            <input type="hidden" id="user_id">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="name" required placeholder="Ad Soyad">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">E-posta <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" required placeholder="ornek@mail.com">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Şifre</label>
                                                <input type="text" class="form-control" id="password" placeholder="Yeni şifre belirleyin">
                                                <small class="text-muted" id="passwordHint">Yeni kullanıcı eklerken zorunludur. Düzenlerken boş bırakırsanız şifre değişmez.</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">GPA Kodu</label>
                                                <input type="text" class="form-control" id="gpa_code" placeholder="Opsiyonel">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Kullanıcı Rozeti (Badge)</label>
                                                <select class="form-select" id="gamification_badge" name="gamification_badge[]" multiple="multiple">
                                                    <option value="VIP Member">VIP Member</option>
                                                    <option value="Verified Account">Verified Account</option>
                                                    <option value="Early Supporter">Early Supporter</option>
                                                    <option value="Elite Status">Elite Status</option>
                                                    <option value="Top Winner">Top Winner</option>
                                                    <option value="Veteran">Veteran</option>
                                                    <option value="High Roller">High Roller</option>
                                                    <option value="Master Tipster">Master Tipster</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">VIP Erişim Verilecek Uygulamalar</label>
                                                <select multiple class="form-select" id="app_ids" style="height: 120px;"></select>
                                                <small class="text-muted">Birden fazla uygulama seçmek için CTRL tuşuna basılı tutun.</small>
                                            </div>

                                            <hr>
                                            <h5 class="font-size-14 mb-3">Güvenlik Muafiyetleri</h5>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="exempt_force_update">
                                                <label class="form-check-label" for="exempt_force_update">Force Update (Zorunlu Güncelleme) Muafiyeti</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="exempt_security">
                                                <label class="form-check-label" for="exempt_security">
                                                    Rate Us & Hediye Muafiyeti (Test / Google Review Hesabı)
                                                    <small class="text-muted d-block">İşaretlenirse bu kullanıcıya Rate Us (5 Yıldız) modalı ve inceleme hediyeleri gösterilmez.</small>
                                                </label>
                                            </div>
                                            <div class="form-check form-switch mb-4">
                                                <input class="form-check-input" type="checkbox" id="exempt_screenshot">
                                                <label class="form-check-label" for="exempt_screenshot">
                                                    Ekran Görüntüsü & Kayıt Muafiyeti (FLAG_SECURE Muafiyeti)
                                                    <small class="text-muted d-block">İşaretlenirse uygulamada ekran koruması aktif olsa bile bu kullanıcı ekran görüntüsü ve video kaydı alabilir.</small>
                                                </label>
                                            </div>

                                            <div>
                                                <button type="submit" class="btn btn-primary w-md" id="submitBtn">Kaydet</button>
                                                <span id="globalActionBtns"></span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 d-none" id="editSidebar">
                                <div id="banAlertContainer"></div>
                                <div class="card bg-info text-white-50">
                                    <div class="card-body">
                                        <h5 class="mt-0 mb-4 text-white"><i class="mdi mdi-information-outline me-3"></i> Log Bilgileri</h5>
                                        <div class="mb-3">
                                            <label class="form-label text-white">Son Giriş IP</label>
                                            <input type="text" class="form-control bg-light text-dark" id="last_login_ip" disabled>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label text-white">Son Giriş Tarihi</label>
                                            <input type="text" class="form-control bg-light text-dark" id="last_login_date" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title mb-4">Kayıtlı Olduğu VIP Uygulamalar</h4>
                                        <div class="table-responsive">
                                            <table class="table table-nowrap align-middle mb-0">
                                                <tbody id="userAppsList">
                                                    <!-- Apps via JS -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card border border-danger">
                                    <div class="card-header bg-soft-danger border-danger">
                                        <h5 class="my-0 text-danger"><i class="mdi mdi-alert-outline me-2"></i> Benzer Üyelikler (Risk)</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush" id="similarUsersList">
                                            <!-- Similar users via JS -->
                                        </ul>
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
                    <!-- Theme Settings... -->
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
        <div class="rightbar-overlay"></div>

        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>
        <script src="assets/libs/feather-icons/feather.min.js"></script>
        <script src="assets/libs/pace-js/pace.min.js"></script>
        <script src="assets/js/app.js?v=2"></script>
        <script src="assets/js/acms-core.js?v=2"></script>
        
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            const token = localStorage.getItem('acms_admin_token');
            const urlParams = new URLSearchParams(window.location.search);
            const userId = urlParams.get('id');

            document.addEventListener("DOMContentLoaded", async function() {
                $('#gamification_badge').select2({
                    placeholder: "Rozet Seçin (Opsiyonel)",
                    allowClear: true
                });
                
                await loadAllApps();
                
                if (userId) {
                    document.getElementById('pageTitle').innerText = 'Kullanıcı Düzenle';
                    document.getElementById('editSidebar').classList.remove('d-none');
                    loadUserData();
                } else {
                    document.getElementById('password').setAttribute('required', 'true');
                }
            });

            async function loadAllApps() {
                try {
                    const res = await fetch('../api/admin/apps.php', {
                        headers: { 'Authorization': 'Bearer ' + token }
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        const select = document.getElementById('app_ids');
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

            function loadUserData() {
                fetch(`../api/admin/users.php?action=get_user&id=${userId}`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        const user = data.data.user;
                        document.getElementById('user_id').value = user.id;
                        document.getElementById('name').value = user.name;
                        document.getElementById('email').value = user.email;
                        document.getElementById('gpa_code').value = user.gpa_code || '';
                        
                        if (user.gamification_badge) {
                            const badges = user.gamification_badge.split(',');
                            $('#gamification_badge').val(badges).trigger('change');
                        }
                        
                        document.getElementById('exempt_force_update').checked = user.exempt_force_update == 1;
                        document.getElementById('exempt_security').checked = user.exempt_security == 1;
                        document.getElementById('exempt_screenshot').checked = user.exempt_screenshot == 1;
                        
                        const banAlert = document.getElementById('banAlertContainer');
                        if (user.is_banned == 1) {
                            banAlert.innerHTML = `<div class="alert alert-danger mb-4"><strong>DİKKAT:</strong> Bu kullanıcı sistemden yasaklanmıştır! <br>Sebep: ${user.ban_reason}</div>`;
                        } else {
                            banAlert.innerHTML = '';
                        }
                        
                        document.getElementById('last_login_ip').value = user.last_login_ip || 'Kayıt Yok';
                        document.getElementById('last_login_date').value = user.last_login_date || 'Kayıt Yok';

                        const appsTbody = document.getElementById('userAppsList');
                        const selectApps = document.getElementById('app_ids');
                        const userAppIds = [];
                        let appsHtml = '';
                        
                        if (data.data.apps && data.data.apps.length > 0) {
                            data.data.apps.forEach(app => {
                                userAppIds.push(app.app_id.toString());
                                
                                let badge = '';
                                if (user.is_banned == 1) {
                                    badge = '<span class="badge bg-dark">Banlı</span>';
                                } else {
                                    if (app.status === 'approved') badge = '<span class="badge bg-success">Onaylandı</span>';
                                    else if (app.status === 'pending') badge = '<span class="badge bg-warning">Bekliyor</span>';
                                    else if (app.status === 'rejected') badge = '<span class="badge bg-danger">Reddedildi</span>';
                                }
                                
                                let actionButtons = `
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="updateStatus(${user.id}, ${app.app_id}, 'approved')" title="Onayla"><i class="bx bx-check"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="updateStatus(${user.id}, ${app.app_id}, 'pending')" title="Beklemeye Al"><i class="bx bx-time"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="updateStatus(${user.id}, ${app.app_id}, 'rejected')" title="Reddet"><i class="bx bx-x"></i></button>
                                `;

                                appsHtml += `
                                    <tr>
                                        <td>
                                            <h5 class="font-size-14 mb-1">${app.app_name}</h5>
                                            <p class="text-muted mb-0">${app.created_at}</p>
                                        </td>
                                        <td class="text-end">
                                            ${badge}
                                            ${user.is_banned != 1 ? `<div class="mt-2">${actionButtons}</div>` : ''}
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            appsHtml = '<tr><td colspan="2" class="text-center text-muted">Kayıtlı uygulama yok.</td></tr>';
                        }
                        appsTbody.innerHTML = appsHtml;
                        
                        // Select the options
                        Array.from(selectApps.options).forEach(option => {
                            if (userAppIds.includes(option.value)) {
                                option.selected = true;
                            }
                        });
                        
                        // Similar Users
                        const similarList = document.getElementById('similarUsersList');
                        let similarHtml = '';
                        if (data.data.similar_users && data.data.similar_users.length > 0) {
                            data.data.similar_users.forEach(su => {
                                let badge = su.is_banned == 1 ? '<span class="badge bg-dark ms-2">Banlı</span>' : '';
                                
                                // Highlight logic
                                let emailText = su.email || '';
                                let ipText = su.last_login_ip || '-';
                                
                                if (su.match_word) {
                                    const regex = new RegExp(`(${su.match_word})`, 'gi');
                                    const highlightSpan = `<span style="background-color: #ffff00; color: #000; font-weight: bold; padding: 2px 5px; border-radius: 4px;">$1</span>`;
                                    
                                    if (su.match_reason === 'email') {
                                        emailText = emailText.replace(regex, highlightSpan);
                                    } else if (su.match_reason === 'ip') {
                                        ipText = ipText.replace(regex, highlightSpan);
                                    }
                                }

                                similarHtml += `
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">${su.name} ${badge}</h6>
                                            <p class="text-muted mb-1 font-size-13">${emailText} <span class="mx-1">|</span> <i class="bx bx-laptop"></i> ${ipText}</p>
                                            <span class="badge bg-light text-secondary border"><i class="bx bx-cube"></i> Uygulamalar: ${su.registered_apps || 'Yok'}</span>
                                        </div>
                                        <div>
                                            <a href="user_edit.php?id=${su.id}" class="btn btn-sm btn-outline-info" title="İncele"><i class="bx bx-show"></i> Detay</a>
                                        </div>
                                    </li>
                                `;
                            });
                        } else {
                            similarHtml = '<li class="list-group-item text-center text-muted py-3">Sistemde benzer bir kayıt bulunamadı.</li>';
                        }
                        similarList.innerHTML = similarHtml;
                        
                        // Global Action Buttons
                        const globalBtns = document.getElementById('globalActionBtns');
                        if (user.is_banned == 1) {
                            globalBtns.innerHTML = `<button type="button" class="btn btn-dark w-md ms-2" onclick="unbanUser(${user.id})"><i class="bx bx-check-shield"></i> Banı Kaldır</button>`;
                        } else {
                            globalBtns.innerHTML = `<button type="button" class="btn btn-danger w-md ms-2" onclick="banUser(${user.id})"><i class="bx bx-block"></i> Banla</button>`;
                        }
                        
                    } else {
                        Swal.fire('Hata', data.message, 'error').then(() => {
                            window.location.href = 'users_list.php';
                        });
                    }
                })
                .catch(err => {
                    Swal.fire('Hata', 'Kullanıcı bilgileri yüklenemedi.', 'error');
                });
            }

            document.getElementById('userForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Kaydediliyor...';

                const selectedApps = Array.from(document.getElementById('app_ids').selectedOptions).map(opt => parseInt(opt.value));

                const payload = {
                    action: 'save_user',
                    id: document.getElementById('user_id').value,
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value,
                    gpa_code: document.getElementById('gpa_code').value,
                    gamification_badge: $('#gamification_badge').val() || [],
                    exempt_force_update: document.getElementById('exempt_force_update').checked ? 1 : 0,
                    exempt_security: document.getElementById('exempt_security').checked ? 1 : 0,
                    exempt_screenshot: document.getElementById('exempt_screenshot').checked ? 1 : 0,
                    app_ids: selectedApps
                };

                fetch('../api/admin/users.php?action=save_user', {
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = 'users_list.php';
                        });
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = 'Kaydet';
                    Swal.fire('Hata!', 'İşlem sırasında sunucu hatası oluştu.', 'error');
                });
            });

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
        </script>
    </body>
</html>
