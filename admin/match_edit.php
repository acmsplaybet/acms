<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    /**
     * Maç Ekleme Ekranı (Doküman 6.5/1 - Hızlı Ekleme Akışı)
     * - Kaydet: kaydeder, formu temizler, seçili uygulamaları korur (art arda giriş).
     * - Kaydet ve Listeye Dön: kaydeder ve maç havuzuna döner.
     */
    document.addEventListener('DOMContentLoaded', function() {
        const API_URL = '../api/admin/matches.php';
        const token = localStorage.getItem('acms_admin_token');
        const authHeaders = { 'Authorization': 'Bearer ' + token };

        const form = document.getElementById('matchForm');
        const btnSave = document.getElementById('submitBtn');
        const btnSaveReturn = document.getElementById('submitReturnBtn');
        const statusSelect = document.getElementById('match_status');
        const scoreInput = document.getElementById('score');
        const toggleAll = document.getElementById('toggleAllApps');

        // ---- Varsayılan Tarih: Bugün ----
        const matchDateInput = document.getElementById('match_date');

        const urlParams = new URLSearchParams(window.location.search);
        const editId = urlParams.get('id');
        if (!editId) {
            Swal.fire('Hata', 'ID bulunamadı!', 'error').then(() => { window.location.href = 'matches_list.php'; });
        }
        document.getElementById('match_id').value = editId;

        // ---- Lig listesi ----
        fetch(API_URL + '?action=get_leagues', { headers: authHeaders })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const select = document.getElementById('league_id');
                data.data.forEach(league => {
                    const opt = document.createElement('option');
                    opt.value = league.id;
                    opt.textContent = league.name;
                    select.appendChild(opt);
                });
            } else {
                Swal.fire('Hata!', data.message || 'Ligler yüklenemedi.', 'error');
            }
        })
        .catch(() => Swal.fire('Hata!', 'Ligler yüklenirken sunucuya ulaşılamadı.', 'error'));

        // ---- Takım listesi (Lige Göre) ----
        $('#league_id').select2({ tags: true, placeholder: "Lig Seç/Yaz" }).on('change', function() {
            const lId = $(this).val();
            if (!lId) return;
            fetch(API_URL.replace('matches.php', 'teams.php') + '?action=list&league_id=' + lId, { headers: authHeaders })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    let currentHome = $('#home_team_id').val();
                    let currentAway = $('#away_team_id').val();
                    let homeText = $('#home_team_id option:selected').text();
                    let awayText = $('#away_team_id option:selected').text();
                    
                    $('#home_team_id').empty().append(new Option('', '', false, false));
                    $('#away_team_id').empty().append(new Option('', '', false, false));
                    
                    data.data.forEach(team => {
                        // ID yerine ismi girmeliyiz ki arama çubuğunda ve veritabanı eşleştirmesinde düzgün çalışsın
                        $('#home_team_id').append(new Option(team.name, team.name, false, false));
                        $('#away_team_id').append(new Option(team.name, team.name, false, false));
                    });
                    
                    if(currentHome && !$('#home_team_id option[value="' + currentHome + '"]').length) {
                        $('#home_team_id').append(new Option(homeText || currentHome, currentHome, false, false));
                    }
                    if(currentAway && !$('#away_team_id option[value="' + currentAway + '"]').length) {
                        $('#away_team_id').append(new Option(awayText || currentAway, currentAway, false, false));
                    }
                    
                    $('#home_team_id').val(currentHome).trigger('change.select2');
                    $('#away_team_id').val(currentAway).trigger('change.select2');
                }
            });
        });
        
        $('#home_team_id').select2({ tags: true, placeholder: "Ev Sahibi Takım Seç/Yaz" });
        $('#away_team_id').select2({ tags: true, placeholder: "Deplasman Takım Seç/Yaz" });
                
        document.getElementById('quick_match_input').addEventListener('blur', function() {
            const val = this.value.trim();
            if(!val) return;
            const parts = val.split(/\s+(?:-|vs\.?|VS\.?)\s+/);
            if(parts.length >= 2) {
                const home = parts[0].trim();
                const away = parts[1].trim();
                
                let hOpt = $('#home_team_id option').filter(function() { return $(this).text().toLowerCase() === home.toLowerCase(); });
                if (hOpt.length) {
                    $('#home_team_id').val(hOpt.val()).trigger('change');
                } else {
                    $('#home_team_id').append(new Option(home, home, true, true)).trigger('change');
                }
                
                let aOpt = $('#away_team_id option').filter(function() { return $(this).text().toLowerCase() === away.toLowerCase(); });
                if (aOpt.length) {
                    $('#away_team_id').val(aOpt.val()).trigger('change');
                } else {
                    $('#away_team_id').append(new Option(away, away, true, true)).trigger('change');
                }
                
                document.getElementById('prediction').focus();
            }
        });
        
        // ---- Dağıtım için uygulama checkbox'ları ----
        fetch(API_URL + '?action=get_apps', { headers: authHeaders })
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('apps_checkbox_list');
            if (data.status !== 'success') {
                container.innerHTML = '<div class="col-12 text-danger small">Uygulamalar yüklenemedi.</div>';
                return;
            }
            if (data.data.length === 0) {
                container.innerHTML = '<div class="col-12 text-muted small">Henüz uygulama tanımlanmamış. Önce bir uygulama ekleyin.</div>';
                return;
            }
            data.data.forEach(app => {
                const col = document.createElement('div');
                col.className = 'col-md-4 mb-2';
                col.innerHTML = `
                    <div class="form-check">
                        <input class="form-check-input app-checkbox" type="checkbox" name="app_ids[]" value="${app.id}" id="app_${app.id}">
                        <label class="form-check-label" for="app_${app.id}">
                            ${app.name}
                            <span class="badge bg-secondary ms-1">${(app.app_type || '').toUpperCase()}</span>
                            ${app.brand_name ? '<span class="badge bg-light text-dark ms-1">' + app.brand_name + '</span>' : ''}
                        </label>
                    </div>
                `;
                container.appendChild(col);
            });
        })
        .then(() => {
            // Ligler ve uygulamalar yüklendikten sonra maç bilgisini çek
            fetch(API_URL + '?action=get_match&id=' + editId, { headers: authHeaders })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const m = res.data;
                    
                    if (m.league_id) {
                        if (!$('#league_id').find("option[value='" + m.league_id + "']").length) {
                            $('#league_id').append(new Option(m.league_name || m.league_id, m.league_id, true, true));
                        }
                        $('#league_id').val(m.league_id).trigger('change');
                    }
                    
                    matchDateInput.value = m.match_date_input;
                    
                    if (m.home_team_id) {
                        if (!$('#home_team_id').find("option[value='" + m.home_team_id + "']").length) {
                            $('#home_team_id').append(new Option(m.home_team_name || m.home_team, m.home_team_id, true, true));
                        }
                        $('#home_team_id').val(m.home_team_id).trigger('change');
                    } else if (m.home_team) {
                        var newOption1 = new Option(m.home_team, m.home_team, true, true);
                        $('#home_team_id').append(newOption1).trigger('change');
                    }
                    
                    if (m.away_team_id) {
                        if (!$('#away_team_id').find("option[value='" + m.away_team_id + "']").length) {
                            $('#away_team_id').append(new Option(m.away_team_name || m.away_team, m.away_team_id, true, true));
                        }
                        $('#away_team_id').val(m.away_team_id).trigger('change');
                    } else if (m.away_team) {
                        var newOption2 = new Option(m.away_team, m.away_team, true, true);
                        $('#away_team_id').append(newOption2).trigger('change');
                    }
                    document.getElementById('prediction').value = m.prediction;
                    document.getElementById('odds').value = m.odds;
                    document.getElementById('confidence_rate').value = m.confidence_rate || '';
                    statusSelect.value = m.status;
                    syncScoreField();
                    if (m.score) {
                        scoreInput.value = m.score;
                    }
                    if (m.app_ids && Array.isArray(m.app_ids)) {
                        m.app_ids.forEach(appId => {
                            const cb = document.getElementById('app_' + appId);
                            if (cb) cb.checked = true;
                        });
                    }
                } else {
                    Swal.fire('Hata', res.message, 'error');
                }
            });
        })
        .catch(() => {
            document.getElementById('apps_checkbox_list').innerHTML =
                '<div class="col-12 text-danger small">Uygulamalar yüklenirken sunucuya ulaşılamadı.</div>';
        });

        // ---- Tümünü seç ----
        toggleAll.addEventListener('change', function() {
            document.querySelectorAll('.app-checkbox').forEach(cb => { cb.checked = toggleAll.checked; });
        });

        // ---- Skor alanı sadece sonuçlanan maçlarda aktif ----
        function syncScoreField() {
            const needsScore = (statusSelect.value === 'win' || statusSelect.value === 'lose');
            scoreInput.disabled = !needsScore;
            scoreInput.required = needsScore;
            if (!needsScore) scoreInput.value = '';
        }
        statusSelect.addEventListener('change', syncScoreField);
        syncScoreField();

        // ---- Kaydetme ----
        function collectData() {
            const dataObj = { action: 'add', app_ids: [] };
            new FormData(form).forEach((value, key) => {
                if (key === 'app_ids[]') {
                    dataObj.app_ids.push(value);
                } else {
                    dataObj[key] = value;
                }
            });
            // Devre dışı input FormData'ya girmez; skoru güvenle ekliyoruz.
            if (scoreInput.disabled) dataObj.score = '';
            return dataObj;
        }

        function setLoading(btn, isLoading, originalText) {
            btn.disabled = isLoading;
            btn.innerHTML = isLoading
                ? '<i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> Kaydediliyor...'
                : originalText;
        }

        /** Hızlı ekleme: maç alanlarını sıfırlar, uygulama seçimlerini korur. */
        function resetForNextEntry() {
            $('#home_team_id').val(null).trigger('change');
            $('#away_team_id').val(null).trigger('change');
            document.getElementById('prediction').value = '';
            document.getElementById('odds').value = '';
            document.getElementById('confidence_rate').value = '';
            statusSelect.value = 'pending';
            syncScoreField();
            $('#home_team_id').select2('focus');
        }

        function submitMatch(returnToList) {
            const btn = returnToList ? btnSaveReturn : btnSave;
            const originalText = btn.innerHTML;

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            setLoading(btn, true, originalText);

            fetch(API_URL, {
                method: 'POST',
                headers: Object.assign({ 'Content-Type': 'application/json' }, authHeaders),
                body: JSON.stringify(collectData())
            })
            .then(res => res.json())
            .then(res => {
                setLoading(btn, false, originalText);

                if (res.status !== 'success') {
                    Swal.fire('Hata!', res.message || 'Maç kaydedilemedi.', 'error');
                    return;
                }

                if (returnToList) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: res.message,
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    }).then(() => { window.location.href = 'matches_list.php'; });
                } else {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: res.message,
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                }
            })
            .catch(() => {
                setLoading(btn, false, originalText);
                Swal.fire('Hata!', 'Sunucu bağlantı hatası.', 'error');
            });
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitMatch(false);
        });

        btnSaveReturn.addEventListener('click', function() {
            submitMatch(true);
        });
    });
</script>

        <!-- plugin css -->
        <link href="assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />

        <!-- preloader css -->
        <link rel="stylesheet" href="assets/css/preloader.min.css" type="text/css" />
        
        <!-- Select2 Css -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

    </head>

    <body>

    <!-- <body data-layout="horizontal"> -->

        <!-- Begin page -->
        <div id="layout-wrapper">

            
            <?php include 'topbar.php'; ?>

            <!-- ========== Left Sidebar Start ========== -->
            <?php include 'sidebar.php'; ?>
            <!-- Left Sidebar End -->

            

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Maç Bilgileri (Düzenleme)</h4>
                <p class="card-title-desc">Var olan bir maçı düzenliyorsunuz.</p>
            </div>
            <div class="card-body">
                <form id="matchForm">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" id="match_id" name="id" value="">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label text-primary">Hızlı Karşılaşma Girişi (Kopyala/Yapıştır)</label>
                            <input type="text" class="form-control form-control-lg border-primary" id="quick_match_input" placeholder="Örn: Galatasaray - Fenerbahçe veya Arsenal vs Chelsea">
                            <small class="form-text text-muted">Buraya yapıştırıp boşluğa tıkladığınızda Ev Sahibi ve Deplasman takımları otomatik ayrıştırılır.</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="league_id" class="form-label">Lig</label>
                            <select class="form-control select2" id="league_id" name="league_id" required>
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="match_date" class="form-label">Tarih ve Saat</label>
                            <input type="datetime-local" class="form-control" id="match_date" name="match_date" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="home_team_id" class="form-label">Ev Sahibi Takım</label>
                            <select class="form-control select2" id="home_team_id" name="home_team_id" required>
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="away_team_id" class="form-label">Deplasman Takım</label>
                            <select class="form-control select2" id="away_team_id" name="away_team_id" required>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="prediction" class="form-label">Tahmin</label>
                            <input type="text" class="form-control" id="prediction" name="prediction" placeholder="Örn: MS 1, 2.5 Üst" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="odds" class="form-label">Oran</label>
                            <input type="text" class="form-control" id="odds" name="odds" placeholder="Örn: 1.85">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="confidence_rate" class="form-label">Güven Oranı (%)</label>
                            <input type="text" class="form-control" id="confidence_rate" name="confidence_rate" placeholder="Örn: %85">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="match_status" class="form-label">Maç Durumu</label>
                            <select class="form-select" id="match_status" name="status">
                                <option value="pending" selected>Bekliyor (Pending)</option>
                                <option value="win">Kazandı (Win)</option>
                                <option value="lose">Kaybetti (Lose)</option>
                                <option value="postponed">Ertelendi (Postponed)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="score" class="form-label">Skor</label>
                            <input type="text" class="form-control" id="score" name="score" placeholder="Örn: 2-1" disabled>
                            <div class="form-text">Sadece Kazandı / Kaybetti durumunda zorunludur.</div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="font-size-14 mb-0">Çoklu Uygulama Dağıtımı (Multi-App)</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="toggleAllApps">
                            <label class="form-check-label small" for="toggleAllApps">Tümünü Seç</label>
                        </div>
                    </div>
                    <p class="text-muted small">Bu maçın hangi uygulamalarda yayınlanacağını seçin. (Seçilen uygulamalarda anında listelenecektir)</p>

                    <div id="apps_checkbox_list" class="row mb-4">
                        <!-- Checkboxes will be populated by JS -->
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="btn btn-primary waves-effect waves-light" id="submitBtn">Değişiklikleri Kaydet</button>
                        <button type="button" class="btn btn-success waves-effect waves-light" id="submitReturnBtn">Kaydet ve Listeye Dön</button>
                        <a href="matches_list.php" class="btn btn-secondary waves-effect waves-light">Listeye Dön</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card bg-primary text-white text-center p-3">
            <div class="card-body">
                <i class="bx bx-info-circle display-4 mb-3"></i>
                <h5 class="text-white">Otomatik Bot Dağıtımı</h5>
                <p>Eklediğiniz bu maç, seçtiğiniz uygulamalar (Real, Alex, Pep vb.) üzerinden Firebase Push Notification tetikleyerek kullanıcılara anında bildirim olarak gönderilebilir.</p>
            </div>
        </div>
    </div><!-- end row -->
                    </div>
                    <!-- container-fluid -->
                </div>
                <!-- End Page-content -->


                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <script>document.write(new Date().getFullYear())</script> © Minia.
                            </div>
                            <div class="col-sm-6">
                                <div class="text-sm-end d-none d-sm-block">
                                    Design & Develop by <a href="#!" class="text-decoration-underline">Themesbrand</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        
        <!-- Right Sidebar -->
        <div class="right-bar">
            <div data-simplebar class="h-100">
                <div class="rightbar-title d-flex align-items-center p-3">

                    <h5 class="m-0 me-2">Theme Customizer</h5>

                    <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                        <i class="mdi mdi-close noti-icon"></i>
                    </a>
                </div>

                <!-- Settings -->
                <hr class="m-0" />

                <div class="p-4">
                    <h6 class="mb-3">Layout</h6>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout"
                            id="layout-vertical" value="vertical">
                        <label class="form-check-label" for="layout-vertical">Vertical</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout"
                            id="layout-horizontal" value="horizontal">
                        <label class="form-check-label" for="layout-horizontal">Horizontal</label>
                    </div>

                    <h6 class="mt-4 mb-3 pt-2">Layout Mode</h6>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-mode"
                            id="layout-mode-light" value="light">
                        <label class="form-check-label" for="layout-mode-light">Light</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-mode"
                            id="layout-mode-dark" value="dark">
                        <label class="form-check-label" for="layout-mode-dark">Dark</label>
                    </div>

                    <h6 class="mt-4 mb-3 pt-2">Layout Width</h6>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-width"
                            id="layout-width-fuild" value="fuild" onchange="document.body.setAttribute('data-layout-size', 'fluid')">
                        <label class="form-check-label" for="layout-width-fuild">Fluid</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-width"
                            id="layout-width-boxed" value="boxed" onchange="document.body.setAttribute('data-layout-size', 'boxed')">
                        <label class="form-check-label" for="layout-width-boxed">Boxed</label>
                    </div>

                    <h6 class="mt-4 mb-3 pt-2">Layout Position</h6>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-position"
                            id="layout-position-fixed" value="fixed" onchange="document.body.setAttribute('data-layout-scrollable', 'false')">
                        <label class="form-check-label" for="layout-position-fixed">Fixed</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-position"
                            id="layout-position-scrollable" value="scrollable" onchange="document.body.setAttribute('data-layout-scrollable', 'true')">
                        <label class="form-check-label" for="layout-position-scrollable">Scrollable</label>
                    </div>

                    <h6 class="mt-4 mb-3 pt-2">Topbar Color</h6>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="topbar-color"
                            id="topbar-color-light" value="light" onchange="document.body.setAttribute('data-topbar', 'light')">
                        <label class="form-check-label" for="topbar-color-light">Light</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="topbar-color"
                            id="topbar-color-dark" value="dark" onchange="document.body.setAttribute('data-topbar', 'dark')">
                        <label class="form-check-label" for="topbar-color-dark">Dark</label>
                    </div>

                    <h6 class="mt-4 mb-3 pt-2 sidebar-setting">Sidebar Size</h6>

                    <div class="form-check sidebar-setting">
                        <input class="form-check-input" type="radio" name="sidebar-size"
                            id="sidebar-size-default" value="default" onchange="document.body.setAttribute('data-sidebar-size', 'lg')">
                        <label class="form-check-label" for="sidebar-size-default">Default</label>
                    </div>
                    <div class="form-check sidebar-setting">
                        <input class="form-check-input" type="radio" name="sidebar-size"
                            id="sidebar-size-compact" value="compact" onchange="document.body.setAttribute('data-sidebar-size', 'md')">
                        <label class="form-check-label" for="sidebar-size-compact">Compact</label>
                    </div>
                    <div class="form-check sidebar-setting">
                        <input class="form-check-input" type="radio" name="sidebar-size"
                            id="sidebar-size-small" value="small" onchange="document.body.setAttribute('data-sidebar-size', 'sm')">
                        <label class="form-check-label" for="sidebar-size-small">Small (Icon View)</label>
                    </div>

                    <h6 class="mt-4 mb-3 pt-2 sidebar-setting">Sidebar Color</h6>

                    <div class="form-check sidebar-setting">
                        <input class="form-check-input" type="radio" name="sidebar-color"
                            id="sidebar-color-light" value="light" onchange="document.body.setAttribute('data-sidebar', 'light')">
                        <label class="form-check-label" for="sidebar-color-light">Light</label>
                    </div>
                    <div class="form-check sidebar-setting">
                        <input class="form-check-input" type="radio" name="sidebar-color"
                            id="sidebar-color-dark" value="dark" onchange="document.body.setAttribute('data-sidebar', 'dark')">
                        <label class="form-check-label" for="sidebar-color-dark">Dark</label>
                    </div>
                    <div class="form-check sidebar-setting">
                        <input class="form-check-input" type="radio" name="sidebar-color"
                            id="sidebar-color-brand" value="brand" onchange="document.body.setAttribute('data-sidebar', 'brand')">
                        <label class="form-check-label" for="sidebar-color-brand">Brand</label>
                    </div>

                    <h6 class="mt-4 mb-3 pt-2">Direction</h6>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-direction"
                            id="layout-direction-ltr" value="ltr">
                        <label class="form-check-label" for="layout-direction-ltr">LTR</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-direction"
                            id="layout-direction-rtl" value="rtl">
                        <label class="form-check-label" for="layout-direction-rtl">RTL</label>
                    </div>

                </div>

            </div> <!-- end slimscroll-menu-->
        </div>
        <!-- /Right-bar -->

        <!-- Right bar overlay-->
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
        
        <!-- Select2 js -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- App js -->
        <script src="assets/js/app.js?v=2"></script>
        <!-- ACMS Core -->
        <script src="assets/js/acms-core.js?v=2"></script>

    </body>

</html>