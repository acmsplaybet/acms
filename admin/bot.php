<?php
// Ensure this matches your general admin logic
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Bot Yönetimi & Önizleme | ACMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <!-- DataTables -->
    <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- Sweet Alert-->
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <style>
        .terminal-box {
            background-color: #1c1e21;
            color: #4CAF50;
            font-family: monospace;
            padding: 15px;
            border-radius: 5px;
            height: 250px;
            overflow-y: auto;
            margin-top: 20px;
        }
        .terminal-box p {
            margin: 0;
            padding: 2px 0;
            border-bottom: 1px solid #333;
        }
        .btn-sync-huge {
            font-size: 1.2rem;
            padding: 15px 40px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(239, 71, 111, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(239, 71, 111, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 71, 111, 0); }
        }
    </style>
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
                                <h4 class="mb-sm-0 font-size-18">Bot Yönetimi & Önizleme Merkezi</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#sync" role="tab">
                                                <span class="d-block d-sm-none"><i class="fas fa-cogs"></i></span>
                                                <span class="d-none d-sm-block"><i class="bx bx-cog me-1"></i> Operasyon Merkezi</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#mapping" role="tab">
                                                <span class="d-block d-sm-none"><i class="fas fa-link"></i></span>
                                                <span class="d-none d-sm-block"><i class="bx bx-link me-1"></i> Uygulama Eşleştirmeleri</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#settings" role="tab">
                                                <span class="d-block d-sm-none"><i class="fas fa-sliders-h"></i></span>
                                                <span class="d-none d-sm-block"><i class="bx bx-slider-alt me-1"></i> Bot Ayarları</span>
                                            </a>
                                        </li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content p-3 text-muted">
                                        
                                        <!-- TAB 2: MAPPING -->
                                        <div class="tab-pane" id="mapping" role="tabpanel">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <p class="mb-0">Uygulamalarınızın dış kaynaktan hangi kuponu çekeceğini eşleştirin.</p>
                                                <button class="btn btn-info btn-sm" onclick="fetchCoupons()"><i class="bx bx-cloud-download"></i> Kupon İsimlerini Getir</button>
                                            </div>
                                            
                                            <div class="table-responsive">
                                                <table class="table align-middle table-nowrap mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="align-middle">Uygulama Adı</th>
                                                            <th class="align-middle">Mevcut Eşleşme</th>
                                                            <th class="align-middle">Kupon Seçimi (Dış Kaynak)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="appMappingList">
                                                        <tr><td colspan="3" class="text-center">Önce "Kupon İsimlerini Getir" butonuna basınız veya direkt uygulamaları yükleyiniz.</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <div class="mt-4 text-end">
                                                <button class="btn btn-primary" onclick="saveMappings()"><i class="bx bx-save"></i> Eşleştirmeleri Kaydet</button>
                                            </div>
                                        </div>

                                        <!-- TAB 1: SYNC / OPERASYON -->
                                        <div class="tab-pane active" id="sync" role="tabpanel">
                                            <div class="row align-items-end mb-4">
                                                <div class="col-md-3">
                                                    <label for="syncDate">Tarih Seçin</label>
                                                    <input type="date" class="form-control" id="syncDate" value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                                <div class="col-md-9">
                                                    <button class="btn btn-warning me-2" onclick="runPreview()"><i class="bx bx-search-alt"></i> ÖNİZLEME YAP (TEST)</button>
                                                    <button class="btn btn-soft-secondary me-1" onclick="quickDate(-1)">Dünü Sonuçlandır</button>
                                                    <button class="btn btn-soft-primary me-1" onclick="quickDate(0)">Bugünü Çek</button>
                                                    <button class="btn btn-soft-info" onclick="quickDate(1)">Yarını Çek</button>
                                                </div>
                                            </div>
                                            
                                            <div id="previewContainer" style="display: none;">
                                                <h5 class="font-size-14 mb-3">Önizleme Sonuçları (Bu veriler henüz veritabanına yazılmadı!)</h5>
                                                
                                                <table id="previewTable" class="table table-bordered dt-responsive nowrap w-100 align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Tarih/Saat</th>
                                                            <th>Lig</th>
                                                            <th>Karşılaşma</th>
                                                            <th>Tahmin / Oran</th>
                                                            <th>Skor / Sonuç</th>
                                                            <th>Hedef Uygulamalar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                                
                                                <div class="text-center mt-5 mb-4" id="syncActionBox" style="display: none;">
                                                    <button class="btn btn-danger btn-sync-huge" onclick="runSync()"><i class="bx bx-cloud-upload"></i> VERİLERİ SİSTEME AKTAR (SYNC)</button>
                                                    <p class="text-muted mt-2">Önizlemedeki veriler veritabanına kalıcı olarak işlenecektir.</p>
                                                </div>
                                            </div>

                                            <div class="terminal-box" id="syncTerminal" style="display:block;">
                                                <p>Sistem Hazır. Senkronizasyon bekleniyor...</p>
                                            </div>
                                            
                                            <div class="mt-4">
                                                <h5 class="font-size-14 mb-3">Son 10 Bot İşlemi</h5>
                                                <table id="recentBotTable" class="table table-sm table-bordered dt-responsive nowrap w-100 align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Tarih</th>
                                                            <th>Karşılaşma</th>
                                                            <th>Tahmin</th>
                                                            <th>Statü</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="recentBotTableBody">
                                                        <tr><td colspan="5" class="text-center">Yükleniyor...</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- TAB 3: SETTINGS -->
                                        <div class="tab-pane" id="settings" role="tabpanel">
                                            <form id="botSettingsForm">
                                                <div class="mb-3">
                                                    <label class="form-label">Hedef API URL</label>
                                                    <input type="text" class="form-control" id="bot_api_url" name="bot_api_url">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Otomatik Çekim Saatleri (Virgülle Ayırın)</label>
                                                    <input type="text" class="form-control" id="bot_cron_fetch" name="bot_cron_fetch">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Sonuçlandırma Sıklığı (Dakika)</label>
                                                    <input type="number" class="form-control" id="bot_cron_result" name="bot_cron_result">
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                        <input class="form-check-input" type="checkbox" id="bot_status" checked>
                                                        <label class="form-check-label" for="bot_status">Bot Durumu (Açık/Kapalı)</label>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-primary" onclick="saveSettings()">Ayarları Kaydet</button>
                                            </form>
                                        </div>
                                        
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

    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/libs/pace-js/pace.min.js"></script>
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    
    <!-- Required datatable js -->
    <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    
    <script src="assets/js/app.js?v=2"></script>
    <script src="assets/js/acms-core.js?v=2"></script>
    
    <script>
        const token = localStorage.getItem('acms_admin_token');
        let availableCoupons = [];
        let loadedApps = [];
        let previewDataTable = null;

        $(document).ready(function() {
            // İlk açılışta direkt uygulamaları yükle (Eğer API'den kupon çekilmediyse select'ler boş gelebilir)
            loadApps();
            loadSettings();
            loadRecentBot();
        });

        function fetchCoupons() {
            Swal.fire({ title: 'Kuponlar Aranıyor...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            
            $.ajax({
                url: '../api/admin/bot_coupons.php',
                type: 'GET',
                success: function(res) {
                    Swal.close();
                    if (res.status === 'success') {
                        availableCoupons = res.data;
                        Swal.fire('Başarılı', availableCoupons.length + ' adet benzersiz kupon bulundu.', 'success');
                        renderAppsTable(); // Mevcut uygulamaları yeni kupon listesiyle tekrar çiz
                    } else {
                        Swal.fire('Hata!', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Hata!', 'Sunucuya bağlanılamadı.', 'error');
                }
            });
        }

        function loadApps() {
            $.ajax({
                url: '../api/admin/bot_sync.php?action=get_apps',
                type: 'GET',
                headers: { 'Authorization': 'Bearer ' + token },
                success: function(res) {
                    if (res.status === 'success') {
                        loadedApps = res.data;
                        renderAppsTable();
                    } else {
                        $('#appMappingList').html(`<tr><td colspan="3" class="text-danger">${res.message}</td></tr>`);
                    }
                }
            });
        }

        function renderAppsTable() {
            let html = '';
            loadedApps.forEach(app => {
                let current = app.bot_coupon_name || '';
                let optionsHtml = '<option value="">-- Eşleştirilmedi --</option>';
                
                if(current !== '' && !availableCoupons.includes(current)) {
                    availableCoupons.push(current);
                }
                
                availableCoupons.forEach(coupon => {
                    let selected = (coupon === current) ? 'selected' : '';
                    optionsHtml += `<option value="${coupon}" ${selected}>${coupon}</option>`;
                });
                
                html += `
                    <tr data-app-id="${app.id}">
                        <td><strong>${app.name}</strong></td>
                        <td>${current ? `<span class="badge bg-info">${current}</span>` : '<span class="badge bg-secondary">Yok</span>'}</td>
                        <td><select class="form-select form-select-sm coupon-select">${optionsHtml}</select></td>
                    </tr>
                `;
            });
            $('#appMappingList').html(html);
        }

        function saveMappings() {
            let mappings = [];
            $('#appMappingList tr').each(function() {
                let appId = $(this).data('app-id');
                let couponName = $(this).find('.coupon-select').val();
                mappings.push({ app_id: appId, coupon_name: couponName });
            });

            $.ajax({
                url: '../api/admin/bot_sync.php',
                type: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                contentType: 'application/json',
                data: JSON.stringify({ action: 'save_mapping', mappings: mappings }),
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire('Başarılı!', res.message, 'success');
                        loadApps(); 
                    } else {
                        Swal.fire('Hata!', res.message, 'error');
                    }
                }
            });
        }

        function getResultBadge(result) {
            let res = (result || '').toUpperCase();
            if (res === 'WIN' || res === 'WON') return '<span class="badge bg-success">WIN</span>';
            if (res === 'LOSE' || res === 'LOST') return '<span class="badge bg-danger">LOSE</span>';
            if (res === 'REFUND' || res === 'VOID') return '<span class="badge bg-warning text-dark">REFUND</span>';
            return '<span class="badge bg-secondary">PENDING</span>';
        }

        function runPreview() {
            let date = $('#syncDate').val();
            if(!date) return;
            
            Swal.fire({ title: 'Önizleme Hesaplanıyor...', text: 'Lütfen bekleyin', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            
            $.ajax({
                url: '../api/admin/bot_preview.php',
                type: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                contentType: 'application/json',
                data: JSON.stringify({ date: date }),
                success: function(res) {
                    Swal.close();
                    if (res.status === 'success') {
                        if (res.data.length === 0) {
                            Swal.fire('Bilgi', 'Bu tarihte eşleşen hiçbir maç bulunamadı.', 'info');
                            $('#previewContainer').hide();
                            return;
                        }
                        
                        $('#previewContainer').show();
                        $('#syncActionBox').show();
                        $('#syncTerminal').hide().html('<p>Sistem Hazır. Senkronizasyon bekleniyor...</p>');
                        
                        if (previewDataTable) {
                            previewDataTable.destroy();
                        }
                        
                        let tbody = '';
                        res.data.forEach(m => {
                            let appsHtml = m.apps.map(a => `<span class="badge bg-primary me-1">${a}</span>`).join('');
                            let matchParts = m.match.split(' - ');
                            let homeName = matchParts[0] || '';
                            let awayName = matchParts[1] || '';
                            
                            let leagueHtml = m.league_logo ? `<img src="${m.league_logo}" width="20" class="me-2" onerror="this.src='assets/images/blank-league.svg'"> ${m.league}` : m.league;
                            let matchHtml = `<div class="d-flex align-items-center">`;
                            if(m.home_logo) matchHtml += `<img src="${m.home_logo}" width="20" class="me-2" onerror="this.src='https://www.forebet.com/images/icons/blank-logo.png'">`;
                            matchHtml += `<span>${homeName}</span> <span class="mx-2 text-muted">-</span>`;
                            if(m.away_logo) matchHtml += `<img src="${m.away_logo}" width="20" class="me-2" onerror="this.src='https://www.forebet.com/images/icons/blank-logo.png'">`;
                            matchHtml += `<span>${awayName}</span></div>`;
                            
                            tbody += `
                                <tr>
                                    <td><span class="text-muted">${date}</span> <br> <strong>${m.time}</strong></td>
                                    <td>${leagueHtml}</td>
                                    <td>${matchHtml}</td>
                                    <td>${m.tip} <br> <span class="text-primary fw-bold">${m.odds}</span></td>
                                    <td>${m.score} <br> ${getResultBadge(m.result)}</td>
                                    <td class="text-wrap">${appsHtml}</td>
                                </tr>
                            `;
                        });
                        
                        $('#previewTable tbody').html(tbody);
                        previewDataTable = $('#previewTable').DataTable({
                            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json' },
                            pageLength: 25,
                            ordering: false
                        });
                        
                        document.getElementById('previewContainer').scrollIntoView({behavior: 'smooth'});
                        
                    } else {
                        Swal.fire('Hata!', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Hata!', 'Sunucuya bağlanılamadı.', 'error');
                }
            });
        }

        function addLog(msg) {
            let now = new Date().toLocaleTimeString('tr-TR');
            $('#syncTerminal').append(`<p>[${now}] ${msg}</p>`);
            let box = document.getElementById('syncTerminal');
            box.scrollTop = box.scrollHeight;
        }

        function runSync() {
            let date = $('#syncDate').val();
            if(!date) return;
            
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Önizlemedeki tüm veriler kalıcı olarak sisteme aktarılacaktır!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef476f',
                cancelButtonColor: '#74788d',
                confirmButtonText: 'Evet, Aktar!',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#syncTerminal').show();
                    addLog(`>>> Senkronizasyon başlatılıyor (${date})...`);
                    $('#syncActionBox').hide(); // Butonu gizle ki çift basılmasın
                    
                    $.ajax({
                        url: '../api/admin/bot_sync.php',
                        type: 'POST',
                        headers: { 'Authorization': 'Bearer ' + token },
                        contentType: 'application/json',
                        data: JSON.stringify({ action: 'sync_bot', date: date }),
                        success: function(res) {
                            if (res.status === 'success') {
                                res.logs.forEach(log => addLog(log));
                                Swal.fire('Tamamlandı', res.message, 'success');
                                document.getElementById('syncTerminal').scrollIntoView({behavior: 'smooth'});
                            } else {
                                addLog(`<span class="text-danger">Hata: ${res.message}</span>`);
                                Swal.fire('Hata!', res.message, 'error');
                            }
                        },
                        error: function() {
                            addLog(`<span class="text-danger">Sunucu bağlantı hatası oluştu.</span>`);
                        }
                    });
                }
            });
        }

        function loadSettings() {
            $.ajax({
                url: '../api/admin/settings.php',
                type: 'GET',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#bot_api_url').val(res.data.bot_api_url);
                        $('#bot_cron_fetch').val(res.data.bot_cron_fetch);
                        $('#bot_cron_result').val(res.data.bot_cron_result);
                        $('#bot_status').prop('checked', res.data.bot_status === '1');
                    }
                }
            });
        }

        function saveSettings() {
            let data = {
                bot_api_url: $('#bot_api_url').val(),
                bot_cron_fetch: $('#bot_cron_fetch').val(),
                bot_cron_result: $('#bot_cron_result').val(),
                bot_status: $('#bot_status').is(':checked') ? '1' : '0'
            };
            $.ajax({
                url: '../api/admin/settings.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(res) {
                    if (res.status === 'success') Swal.fire('Başarılı!', res.message, 'success');
                    else Swal.fire('Hata!', res.message, 'error');
                }
            });
        }

        function loadRecentBot() {
            $.ajax({
                url: '../api/admin/bot_recent.php',
                type: 'GET',
                success: function(res) {
                    if (res.status === 'success') {
                        let html = '';
                        if (res.data.length === 0) {
                            html = '<tr><td colspan="5" class="text-center">Henüz bot ile işlenmiş maç yok.</td></tr>';
                        } else {
                            res.data.forEach(m => {
                                let badge = getResultBadge(m.status);
                                html += `<tr>
                                    <td>#${m.id}</td>
                                    <td>${m.updated_at}</td>
                                    <td>${m.match_title}</td>
                                    <td>${m.prediction}</td>
                                    <td>${badge}</td>
                                </tr>`;
                            });
                        }
                        $('#recentBotTableBody').html(html);
                    }
                }
            });
        }

        function quickDate(offset) {
            let d = new Date();
            d.setDate(d.getDate() + offset);
            let yyyy = d.getFullYear();
            let mm = String(d.getMonth() + 1).padStart(2, '0');
            let dd = String(d.getDate()).padStart(2, '0');
            $('#syncDate').val(`${yyyy}-${mm}-${dd}`);
            runPreview();
        }
    </script>
</body>
</html>
