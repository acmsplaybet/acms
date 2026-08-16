<!doctype html>
<html lang="tr">
    <head>
        <meta charset="utf-8" />
        <title>Bildirim Merkezi | ACMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- DataTables -->
        <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" /> 

        <!-- SweetAlert2 -->
        <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

        <!-- preloader css -->
        <link rel="stylesheet" href="assets/css/preloader.min.css" type="text/css" />

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

        <style>
            /* Live Phone Notification Mockup Styles */
            .phone-mockup-wrapper {
                background: #111827;
                border-radius: 36px;
                padding: 16px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), inset 0 0 0 2px #374151;
                max-width: 380px;
                margin: 0 auto;
            }
            .phone-notch {
                width: 140px;
                height: 18px;
                background: #1f2937;
                border-radius: 0 0 14px 14px;
                margin: 0 auto 16px;
            }
            .phone-screen {
                background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
                border-radius: 24px;
                padding: 16px 12px;
                min-height: 480px;
                color: #fff;
                position: relative;
            }
            .push-card {
                background: rgba(30, 41, 59, 0.92);
                backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.12);
                border-radius: 16px;
                padding: 14px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
                transition: all 0.3s ease;
            }
            .push-app-icon {
                width: 22px;
                height: 22px;
                border-radius: 6px;
                background: #3b82f6;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 11px;
                font-weight: bold;
                color: white;
            }
            .push-preview-image {
                width: 100%;
                height: 140px;
                object-fit: cover;
                border-radius: 10px;
                margin-top: 10px;
                border: 1px solid rgba(255, 255, 255, 0.08);
            }
            .emoji-pill {
                cursor: pointer;
                user-select: none;
                transition: transform 0.1s;
                font-size: 16px;
                padding: 4px 8px;
                border-radius: 6px;
                background: rgba(255, 255, 255, 0.05);
                display: inline-block;
                margin-right: 4px;
                margin-bottom: 4px;
            }
            .emoji-pill:hover {
                transform: scale(1.2);
                background: rgba(255, 255, 255, 0.15);
            }

            /* Card Radio Selection Enhancement */
            .card-radio-label {
                border: 2px solid #e2e8f0 !important;
                border-radius: 14px !important;
                padding: 12px 6px !important;
                cursor: pointer;
                transition: all 0.2s ease-in-out;
                background: #ffffff;
                position: relative;
                display: block;
            }
            .card-radio-label:hover {
                border-color: #cbd5e1 !important;
                background: #f8fafc;
            }
            input[type="radio"]:checked + .card-radio-label {
                border-color: #5156be !important;
                background: rgba(81, 86, 190, 0.07) !important;
                box-shadow: 0 0 0 3px rgba(81, 86, 190, 0.22) !important;
            }
            .check-active-badge {
                position: absolute;
                top: 6px;
                right: 6px;
                width: 18px;
                height: 18px;
                background: #5156be;
                color: #fff;
                border-radius: 50%;
                font-size: 11px;
                display: none;
                line-height: 18px;
                text-align: center;
            }
            input[type="radio"]:checked + .card-radio-label .check-active-badge {
                display: inline-block !important;
            }
            .variable-chip {
                cursor: pointer;
                background: #eef2ff;
                color: #4f46e5;
                border: 1px dashed #6366f1;
                padding: 3px 8px;
                border-radius: 6px;
                font-size: 11px;
                font-weight: 600;
                display: inline-block;
                margin-right: 4px;
                transition: background 0.15s;
            }
            .variable-chip:hover {
                background: #e0e7ff;
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

                        <!-- Page Title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18"><i class="bx bx-bell text-primary me-2"></i>Bildirim Merkezi (Push Notification Hub)</h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                            <li class="breadcrumb-item active">Bildirimler</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Level Tabs: Manuel Gönderim vs Otomatik Tetikleyiciler -->
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified mb-4" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active font-size-15 fw-bold py-3" data-bs-toggle="tab" href="#manualPushTab" role="tab">
                                    <i class="bx bx-paper-plane me-1 text-primary"></i> Manuel Bildirim Gönder
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-size-15 fw-bold py-3" data-bs-toggle="tab" href="#autoTriggersTab" role="tab" onclick="loadAutomatedTemplate()">
                                    <i class="bx bx-bot me-1 text-warning"></i> ⚡ Otomatik Bildirimler (VIP Onay vb.)
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            
                            <!-- TAB 1: MANUEL BİLDİRİM GÖNDER -->
                            <div class="tab-pane active" id="manualPushTab" role="tabpanel">
                                <div class="row">
                                    <!-- Left: Push Form -->
                                    <div class="col-xl-7 col-lg-7">
                                        <div class="card">
                                            <div class="card-header bg-transparent border-bottom">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <h5 class="card-title mb-0"><i class="bx bx-send me-2 text-primary"></i>Yeni Push Bildirim Gönder</h5>
                                                    <span class="badge bg-soft-info text-info"><i class="bx bx-broadcast me-1"></i>OneSignal REST v1</span>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <form id="notificationForm" onsubmit="handleSendPush(event)">
                                                    
                                                    <!-- Target App -->
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold" for="appSelect">Uygulama Seçin <span class="text-danger">*</span></label>
                                                        <select class="form-select" id="appSelect" required onchange="onAppChange()">
                                                            <option value="">Uygulama yükleniyor...</option>
                                                        </select>
                                                        <div id="appWarning" class="alert alert-warning py-2 mt-2" style="display: none;">
                                                            <i class="bx bx-error-circle me-1"></i> Bu uygulamada <strong>OneSignal App ID / API Key</strong> yapılandırılmamış. Bildirim gönderebilmek için lütfen <a id="appEditLink" href="#" class="alert-link">Uygulama Ayarları</a>'na gidip anahtarlarınızı kaydedin.
                                                        </div>
                                                    </div>

                                                    <!-- Live Subscriber & Audience Quick Stats Strip -->
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between p-2 mb-3 bg-light rounded border font-size-12">
                                                        <div class="me-3 mb-1">
                                                            <span class="text-muted"><i class="bx bx-broadcast text-info me-1"></i>OneSignal Canlı Abone:</span>
                                                            <strong id="liveOsSubscribers" class="text-dark"><span class="badge bg-soft-secondary text-secondary">Yükleniyor...</span></strong>
                                                        </div>
                                                        <div class="me-3 mb-1">
                                                            <span class="text-muted"><i class="bx bx-user-check text-success me-1"></i>Kayıtlı Uygulama Üyesi:</span>
                                                            <strong id="liveTotalUsers" class="text-dark">-</strong>
                                                        </div>
                                                        <div class="mb-1">
                                                            <span class="badge bg-soft-info text-info" id="osStatusBadge"><i class="bx bx-loader-alt bx-spin me-1"></i>Kontrol ediliyor...</span>
                                                        </div>
                                                    </div>

                                                    <!-- Segment / Audience -->
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <label class="form-label fw-bold mb-0">Hedef Kitle (Segment)</label>
                                                            <small class="text-muted">Seçili kitleye göre otomatik filtrelenir</small>
                                                        </div>
                                                        <div class="row g-2">
                                                            <div class="col-md-3 col-6">
                                                                <div class="form-check p-0 position-relative">
                                                                    <input class="form-check-input visually-hidden" type="radio" name="targetSegment" id="segAll" value="all" checked onchange="toggleUserSelect()">
                                                                    <label class="card-radio-label text-center" for="segAll">
                                                                        <span class="check-active-badge"><i class="bx bx-check"></i></span>
                                                                        <i class="bx bx-group d-block font-size-22 mb-1 text-primary"></i>
                                                                        <span class="font-size-12 fw-bold d-block text-dark">Tüm Üyeler</span>
                                                                        <div class="mt-1">
                                                                            <span id="countAllDb" class="badge bg-soft-primary text-primary font-size-11 d-block mb-1">... DB</span>
                                                                            <span id="countAllOs" class="badge bg-success text-white font-size-10 d-block"><i class="bx bx-broadcast me-1"></i>... Canlı</span>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-6">
                                                                <div class="form-check p-0 position-relative">
                                                                    <input class="form-check-input visually-hidden" type="radio" name="targetSegment" id="segVip" value="approved" onchange="toggleUserSelect()">
                                                                    <label class="card-radio-label text-center" for="segVip">
                                                                        <span class="check-active-badge"><i class="bx bx-check"></i></span>
                                                                        <i class="bx bx-crown d-block font-size-22 mb-1 text-warning"></i>
                                                                        <span class="font-size-12 fw-bold d-block text-dark">Onaylı VIP</span>
                                                                        <div class="mt-1">
                                                                            <span id="countVipDb" class="badge bg-soft-warning text-warning font-size-11 d-block mb-1">... VIP</span>
                                                                            <span id="countVipOs" class="badge bg-success text-white font-size-10 d-block"><i class="bx bx-broadcast me-1"></i>... Canlı</span>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-6">
                                                                <div class="form-check p-0 position-relative">
                                                                    <input class="form-check-input visually-hidden" type="radio" name="targetSegment" id="segPending" value="pending" onchange="toggleUserSelect()">
                                                                    <label class="card-radio-label text-center" for="segPending">
                                                                        <span class="check-active-badge"><i class="bx bx-check"></i></span>
                                                                        <i class="bx bx-time-five d-block font-size-22 mb-1 text-info"></i>
                                                                        <span class="font-size-12 fw-bold d-block text-dark">Onay Bekleyen</span>
                                                                        <div class="mt-1">
                                                                            <span id="countPendingDb" class="badge bg-soft-info text-info font-size-11 d-block mb-1">... Bekleyen</span>
                                                                            <span id="countPendingOs" class="badge bg-soft-secondary text-secondary font-size-10 d-block"><i class="bx bx-broadcast me-1"></i>... Canlı</span>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-6">
                                                                <div class="form-check p-0 position-relative">
                                                                    <input class="form-check-input visually-hidden" type="radio" name="targetSegment" id="segUser" value="user" onchange="toggleUserSelect()">
                                                                    <label class="card-radio-label text-center" for="segUser">
                                                                        <span class="check-active-badge"><i class="bx bx-check"></i></span>
                                                                        <i class="bx bx-user d-block font-size-22 mb-1 text-secondary"></i>
                                                                        <span class="font-size-12 fw-bold d-block text-dark">Özel Kullanıcı</span>
                                                                        <div class="mt-1">
                                                                            <span class="badge bg-soft-secondary text-secondary font-size-11 d-block mb-1">Tekil ACMS ID</span>
                                                                            <span class="badge bg-soft-secondary text-secondary font-size-10 d-block"><i class="bx bx-target-lock me-1"></i>Doğrudan Seçim</span>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Targeted User ID (Only if 'user' is selected) -->
                                                    <div class="mb-3" id="userTargetRow" style="display: none;">
                                                        <label class="form-label fw-bold mb-1">Hedef Kullanıcıyı Arayın & Seçin <span class="text-danger">*</span></label>
                                                        
                                                        <!-- Selected User Box (Hidden by default) -->
                                                        <div id="selectedUserCard" class="p-2 mb-2 bg-soft-primary border border-primary rounded d-flex align-items-center justify-content-between" style="display: none !important;">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-xs me-2">
                                                                    <span class="avatar-title rounded-circle bg-primary text-white font-size-12 fw-bold" id="selectedUserInitial">U</span>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0 font-size-13 text-dark fw-bold" id="selectedUserName">Kullanıcı Adı</h6>
                                                                    <span class="text-muted font-size-11" id="selectedUserEmail">kullanici@gmail.com</span>
                                                                    <span class="badge bg-primary font-size-10 ms-1" id="selectedUserBadge">VIP</span>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearSelectedUser()">
                                                                <i class="bx bx-x me-1"></i>Değiştir
                                                            </button>
                                                        </div>

                                                        <!-- Search Input & Results Dropdown -->
                                                        <div id="userSearchWrapper" class="position-relative">
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white"><i class="bx bx-search text-primary"></i></span>
                                                                <input type="text" class="form-control" id="userSearchInput" placeholder="Kullanıcı adı, e-posta, sipariş kodu veya ID yazın..." autocomplete="off" oninput="handleUserSearch(this.value)" onfocus="handleUserSearch(this.value)">
                                                                <button class="btn btn-outline-secondary" type="button" onclick="handleUserSearch(document.getElementById('userSearchInput').value)">
                                                                    <i class="bx bx-refresh"></i>
                                                                </button>
                                                            </div>
                                                            <input type="hidden" id="targetUserId" value="">
                                                            
                                                            <!-- Live Dropdown Results List -->
                                                            <div id="userSearchResults" class="list-group position-absolute w-100 shadow-lg border mt-1" style="z-index: 1050; max-height: 240px; overflow-y: auto; display: none; background: #ffffff;">
                                                                <!-- Rendered by JS -->
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">Arama kutusuna tıklayarak kullanıcı listenizden istediğiniz kişiyi tek tıkla seçebilirsiniz.</small>
                                                    </div>

                                                    <!-- Title -->
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <label class="form-label fw-bold mb-0" for="pushTitle">Bildirim Başlığı <span class="text-danger">*</span></label>
                                                            <small class="text-muted" id="titleCount">0 / 60</small>
                                                        </div>
                                                        <input type="text" class="form-control" id="pushTitle" placeholder="Örn: ⚽ Günün Banko Kuponu Eklendi!" required maxlength="100" oninput="updatePreview()">
                                                    </div>

                                                    <!-- Message Body -->
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <label class="form-label fw-bold mb-0" for="pushMessage">Bildirim Mesajı <span class="text-danger">*</span></label>
                                                            <small class="text-muted" id="messageCount">0 / 200</small>
                                                        </div>
                                                        <textarea class="form-control" id="pushMessage" rows="3" placeholder="Örn: VIP analistlerimiz tarafından hazırlanan %92 güven oranlı kupon yayında. Hemen inceleyin!" required maxlength="300" oninput="updatePreview()"></textarea>
                                                        
                                                        <!-- Emoji Helper Buttons -->
                                                        <div class="mt-2">
                                                            <span class="text-muted font-size-12 me-1">Hızlı Emoji:</span>
                                                            <span class="emoji-pill" onclick="insertEmoji('🔥')">🔥</span>
                                                            <span class="emoji-pill" onclick="insertEmoji('⚽')">⚽</span>
                                                            <span class="emoji-pill" onclick="insertEmoji('🏆')">🏆</span>
                                                            <span class="emoji-pill" onclick="insertEmoji('🎉')">🎉</span>
                                                            <span class="emoji-pill" onclick="insertEmoji('⭐')">⭐</span>
                                                            <span class="emoji-pill" onclick="insertEmoji('💰')">💰</span>
                                                            <span class="emoji-pill" onclick="insertEmoji('🚀')">🚀</span>
                                                            <span class="emoji-pill" onclick="insertEmoji('🎯')">🎯</span>
                                                            <span class="emoji-pill" onclick="insertEmoji('💎')">💎</span>
                                                        </div>
                                                    </div>

                                                    <!-- Big Picture Image URL -->
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold" for="pushImage">Büyük Görsel URL (Opsiyonel)</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="bx bx-image"></i></span>
                                                            <input type="url" class="form-control" id="pushImage" placeholder="https://example.com/banner.jpg" oninput="updatePreview()">
                                                        </div>
                                                        <small class="text-muted">Android bildirim çubuğunda açılan büyük afiş görseli.</small>
                                                    </div>

                                                    <!-- Deep Link Route -->
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold" for="deepLink">Yönlendirme Sayfası (Deep Link)</label>
                                                        <select class="form-select" id="deepLink" onchange="updatePreview()">
                                                            <option value="/app/tips">🎯 Tahminler & VIP Analizler (/app/tips)</option>
                                                            <option value="/app/home">🏠 Ana Sayfa (/app/home)</option>
                                                            <option value="/app/history">📜 Geçmiş & Biten Maçlar (/app/history)</option>
                                                            <option value="/app/vip-hub">💎 VIP Hub & Uygulamalar (/app/vip-hub)</option>
                                                            <option value="/app/support">💬 Canlı Destek & İletişim (/app/support)</option>
                                                            <option value="/app/profile">⚙️ Profil & Üyelik Bilgileri (/app/profile)</option>
                                                        </select>
                                                        <small class="text-muted">Kullanıcı bildirime tıkladığında doğrudan bu ekrana yönlendirilir.</small>
                                                    </div>

                                                    <!-- Dry Run / Test Switch & Submit -->
                                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                                        <div class="form-check form-switch form-switch-md">
                                                            <input class="form-check-input" type="checkbox" id="testModeSwitch">
                                                            <label class="form-check-label fw-semibold" for="testModeSwitch">Test Modu (Simülasyon)</label>
                                                            <small class="d-block text-muted">Açıkken OneSignal sunucularına istek gitmez, güvenle test edilir.</small>
                                                        </div>

                                                        <button type="submit" id="btnSendPush" class="btn btn-primary btn-lg px-4 waves-effect waves-light">
                                                            <i class="bx bx-send me-1"></i> Bildirimi Gönder
                                                        </button>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right: Live Mockup Preview -->
                                    <div class="col-xl-5 col-lg-5">
                                        <div class="card bg-transparent border-0 shadow-none">
                                            <div class="card-body p-0">
                                                <div class="text-center mb-3">
                                                    <h6 class="text-muted text-uppercase fw-semibold font-size-12"><i class="bx bx-mobile me-1"></i>Canlı Mobil Bildirim Önizlemesi</h6>
                                                </div>

                                                <!-- Smartphone Frame -->
                                                <div class="phone-mockup-wrapper">
                                                    <div class="phone-notch"></div>
                                                    <div class="phone-screen">
                                                        <!-- Mockup Status Bar -->
                                                        <div class="d-flex justify-content-between align-items-center text-white-50 font-size-11 px-2 mb-3">
                                                            <span>09:41</span>
                                                            <div>
                                                                <i class="bx bx-wifi me-1"></i>
                                                                <i class="bx bx-battery"></i>
                                                            </div>
                                                        </div>

                                                        <!-- Push Notification Card -->
                                                        <div class="push-card">
                                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="push-app-icon me-2" id="previewAppIcon">
                                                                        <i class="bx bx-bell font-size-13"></i>
                                                                    </div>
                                                                    <span class="fw-bold font-size-12 text-white" id="previewAppName">Real Mobile Bet</span>
                                                                </div>
                                                                <span class="text-white-50 font-size-11">şimdi</span>
                                                            </div>

                                                            <div class="push-card-body">
                                                                <h6 class="fw-bold text-white mb-1 font-size-14" id="previewTitle">⚽ Günün Banko Kuponu Eklendi!</h6>
                                                                <p class="text-white-50 font-size-12 mb-0" id="previewBody" style="line-height: 1.4;">VIP analistlerimiz tarafından hazırlanan %92 güven oranlı kupon yayında. Hemen inceleyin!</p>
                                                                <img id="previewImage" src="" class="push-preview-image" style="display: none;" alt="Preview">
                                                            </div>

                                                            <div class="mt-2 pt-2 border-top border-white-10 d-flex justify-content-between align-items-center text-white-50 font-size-11">
                                                                <span id="previewRouteTag"><i class="bx bx-link me-1"></i>/app/tips</span>
                                                                <span class="badge bg-primary font-size-10 px-2 py-1">Aç</span>
                                                            </div>
                                                        </div>

                                                        <!-- Subtitle explanation -->
                                                        <div class="text-center text-white-50 font-size-11 mt-4 px-3">
                                                            <i class="bx bx-info-circle me-1"></i> Android & iOS cihaz kilit ekranı ve bildirim tepsisinde bu şekilde görünecektir.
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bottom: Logs & History Table -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <h5 class="card-title mb-0 me-3"><i class="bx bx-history text-secondary me-2"></i>Bildirim Gönderim Geçmişi</h5>
                                                    <select class="form-select form-select-sm" id="logAppFilter" style="width: 220px;" onchange="loadLogs(1)">
                                                        <option value="">Tüm Uygulamalar</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <button class="btn btn-sm btn-outline-secondary me-2" onclick="loadLogs(1)">
                                                        <i class="bx bx-refresh me-1"></i> Yenile
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="clearLogs()">
                                                        <i class="bx bx-trash me-1"></i> Geçmişi Temizle
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover align-middle table-nowrap mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Tarih</th>
                                                                <th>Uygulama</th>
                                                                <th>Bildirim Başlığı & İçerik</th>
                                                                <th>Hedef</th>
                                                                <th>Durum</th>
                                                                <th>Teslimat</th>
                                                                <th>İşlem</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="logsTableBody">
                                                            <tr>
                                                                <td colspan="8" class="text-center py-4 text-muted">Kayıtlar yükleniyor...</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <!-- Pagination -->
                                                <div class="d-flex justify-content-between align-items-center mt-3" id="paginationWrapper" style="display: none;">
                                                    <small class="text-muted" id="paginationInfo"></small>
                                                    <ul class="pagination pagination-sm mb-0" id="paginationUl"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: OTOMATİK BİLDİRİMLER & TETİKLEYİCİLER (VIP ONAY VB.) -->
                            <div class="tab-pane" id="autoTriggersTab" role="tabpanel">
                                <div class="row">
                                    <div class="col-xl-8 col-lg-8">
                                        <div class="card border-top border-warning border-3">
                                            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title rounded-circle bg-warning text-white font-size-14"><i class="bx bx-crown"></i></span>
                                                    </div>
                                                    <div>
                                                        <h5 class="card-title mb-0">VIP Üyelik Onay Bildirimi</h5>
                                                        <small class="text-muted">Kullanıcı Listesinden bir üyenin durumu <strong>"Onaylandı"</strong> yapıldığında otomatik tetiklenir.</small>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch form-switch-md">
                                                    <input class="form-check-input" type="checkbox" id="tplIsActive" checked>
                                                    <label class="form-check-label fw-bold text-success" for="tplIsActive">Aktif</label>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                
                                                <!-- App Selection for Template -->
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold" for="tplAppSelect">Uygulama</label>
                                                    <select class="form-select" id="tplAppSelect" onchange="loadAutomatedTemplate()">
                                                        <!-- Synced with apps -->
                                                    </select>
                                                </div>

                                                <!-- Dynamic Variables Helper Chips -->
                                                <div class="mb-3 p-2 bg-light rounded border">
                                                    <span class="text-muted font-size-12 me-2"><i class="bx bx-code-alt me-1"></i>Kullanılabilir Değişkenler (Tıklayınca Ekler):</span>
                                                    <span class="variable-chip" onclick="insertTplVar('{name}')"><i class="bx bx-user me-1"></i>{name} (Kullanıcı Adı)</span>
                                                    <span class="variable-chip" onclick="insertTplVar('{app_name}')"><i class="bx bx-mobile me-1"></i>{app_name} (Uygulama Adı)</span>
                                                </div>

                                                <!-- Multi-Language Sub-Tabs -->
                                                <label class="form-label fw-bold mb-2">Çoklu Dil Şablonları (OneSignal Native i18n)</label>
                                                <ul class="nav nav-pills nav-justified mb-3" role="tablist" id="langTabs">
                                                    <li class="nav-item"><a class="nav-link active py-2 font-size-13" data-bs-toggle="pill" href="#langTr">🇹🇷 Türkçe (Varsayılan)</a></li>
                                                    <li class="nav-item"><a class="nav-link py-2 font-size-13" data-bs-toggle="pill" href="#langEn">🇬🇧 English</a></li>
                                                    <li class="nav-item"><a class="nav-link py-2 font-size-13" data-bs-toggle="pill" href="#langDe">🇩🇪 Deutsch</a></li>
                                                    <li class="nav-item"><a class="nav-link py-2 font-size-13" data-bs-toggle="pill" href="#langEs">🇪🇸 Español</a></li>
                                                    <li class="nav-item"><a class="nav-link py-2 font-size-13" data-bs-toggle="pill" href="#langPt">🇵🇹 Português</a></li>
                                                    <li class="nav-item"><a class="nav-link py-2 font-size-13" data-bs-toggle="pill" href="#langFr">🇫🇷 Français</a></li>
                                                </ul>

                                                <div class="tab-content border p-3 rounded mb-3 bg-white">
                                                    <!-- TR -->
                                                    <div class="tab-pane active" id="langTr">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">🇹🇷 Başlık (TR) <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="tplTitleTr" placeholder="🎉 VIP Üyeliğiniz Onaylandı!">
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="form-label fw-semibold">🇹🇷 Mesaj İçeriği (TR) <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" id="tplMessageTr" rows="3" placeholder="Tebrikler {name}! Sipariş kodunuz onaylandı. Artık tüm VIP tahmin ve analizlere sınırsız erişebilirsiniz."></textarea>
                                                        </div>
                                                    </div>

                                                    <!-- EN -->
                                                    <div class="tab-pane" id="langEn">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">🇬🇧 Başlık (EN)</label>
                                                            <input type="text" class="form-control" id="tplTitleEn" placeholder="🎉 VIP Membership Approved!">
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="form-label fw-semibold">🇬🇧 Mesaj İçeriği (EN)</label>
                                                            <textarea class="form-control" id="tplMessageEn" rows="3" placeholder="Congratulations {name}! Your order code has been approved. You now have full unlimited access to all VIP tips."></textarea>
                                                        </div>
                                                    </div>

                                                    <!-- DE -->
                                                    <div class="tab-pane" id="langDe">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">🇩🇪 Başlık (DE)</label>
                                                            <input type="text" class="form-control" id="tplTitleDe" placeholder="🎉 VIP-Mitgliedschaft Genehmigt!">
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="form-label fw-semibold">🇩🇪 Mesaj İçeriği (DE)</label>
                                                            <textarea class="form-control" id="tplMessageDe" rows="3" placeholder="Herzlichen Glückwunsch {name}! Ihr Bestellcode wurde bestätigt. Sie haben jetzt unbegrenzten Zugriff auf alle VIP-Tipps."></textarea>
                                                        </div>
                                                    </div>

                                                    <!-- ES -->
                                                    <div class="tab-pane" id="langEs">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">🇪🇸 Başlık (ES)</label>
                                                            <input type="text" class="form-control" id="tplTitleEs" placeholder="🎉 ¡Membresía VIP Aprobada!">
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="form-label fw-semibold">🇪🇸 Mesaj İçeriği (ES)</label>
                                                            <textarea class="form-control" id="tplMessageEs" rows="3" placeholder="¡Felicitaciones {name}! Tu código de pedido ha sido aprobado. Ahora tienes acceso ilimitado a todas las predicciones VIP."></textarea>
                                                        </div>
                                                    </div>

                                                    <!-- PT -->
                                                    <div class="tab-pane" id="langPt">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">🇵🇹 Başlık (PT)</label>
                                                            <input type="text" class="form-control" id="tplTitlePt" placeholder="🎉 Associação VIP Aprovada!">
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="form-label fw-semibold">🇵🇹 Mesaj İçeriği (PT)</label>
                                                            <textarea class="form-control" id="tplMessagePt" rows="3" placeholder="Parabéns {name}! Seu código de pedido foi aprovado. Agora você tem acesso ilimitado a todas as dicas VIP."></textarea>
                                                        </div>
                                                    </div>

                                                    <!-- FR -->
                                                    <div class="tab-pane" id="langFr">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">🇫🇷 Başlık (FR)</label>
                                                            <input type="text" class="form-control" id="tplTitleFr" placeholder="🎉 Adhésion VIP Approuvée!">
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="form-label fw-semibold">🇫🇷 Mesaj İçeriği (FR)</label>
                                                            <textarea class="form-control" id="tplMessageFr" rows="3" placeholder="Félicitations {name}! Votre code de commande a été validé. Vous avez maintenant un accès illimité à tous les pronostics VIP."></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Deep Link for Template -->
                                                <div class="mb-4">
                                                    <label class="form-label fw-bold" for="tplDeepLink">Tıklanınca Açılacak Sayfa (Deep Link)</label>
                                                    <select class="form-select" id="tplDeepLink">
                                                        <option value="/app/tips">🎯 Tahminler & VIP Analizler (/app/tips)</option>
                                                        <option value="/app/home">🏠 Ana Sayfa (/app/home)</option>
                                                        <option value="/app/history">📜 Geçmiş & Biten Maçlar (/app/history)</option>
                                                        <option value="/app/vip-hub">💎 VIP Hub & Uygulamalar (/app/vip-hub)</option>
                                                        <option value="/app/profile">⚙️ Profil & Üyelik Bilgileri (/app/profile)</option>
                                                    </select>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                    <button type="button" class="btn btn-outline-primary" onclick="testAutomatedPush()">
                                                        <i class="bx bx-paper-plane me-1"></i> Kendi Cihazımda Test Et
                                                    </button>
                                                    <button type="button" id="btnSaveTemplate" class="btn btn-warning btn-lg px-4" onclick="saveAutomatedTemplate()">
                                                        <i class="bx bx-save me-1"></i> Şablonu Kaydet
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right: Explanation & Logic Info Card -->
                                    <div class="col-xl-4 col-lg-4">
                                        <div class="card">
                                            <div class="card-header bg-transparent border-bottom">
                                                <h5 class="card-title mb-0"><i class="bx bx-info-circle text-primary me-2"></i>Nasıl Çalışır?</h5>
                                            </div>
                                            <div class="card-body font-size-13 text-muted" style="line-height: 1.6;">
                                                <p><strong>1. Otomatik Tetikleme:</strong> Panelden bir kullanıcının sipariş kodunu onaylayıp durumu <code>approved</code> yapıldığında OneSignal bu şablonu kullanır.</p>
                                                <p><strong>2. Çoklu Dil Desteği (i18n):</strong> OneSignal, kullanıcının telefonunun sistem diline (TR, EN, DE, ES, PT, FR) göre doğru dildeki mesajı otomatik olarak iletir. Dil eşleşmezse Türkçe varsayılan olarak gönderilir.</p>
                                                <p><strong>3. Değişkenler:</strong> Metin içerisindeki <code>{name}</code> kullanıcının adıyla, <code>{app_name}</code> ise uygulamanın adıyla otomatik değiştirilir.</p>
                                                <div class="alert alert-info py-2 mb-0 font-size-12">
                                                    <i class="bx bx-check-shield me-1"></i> Üyelik onaylandığı an kullanıcıya sesli ve titreşimli VIP kutlama bildirimi ulaşır.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div> <!-- container-fluid -->
                </div> <!-- page-content -->

                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <script>document.write(new Date().getFullYear())</script> © ACMS.
                            </div>
                            <div class="col-sm-6">
                                <div class="text-sm-end d-none d-sm-block">
                                    Push Notification Center
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <!-- JAVASCRIPT -->
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>
        <script src="assets/libs/feather-icons/feather.min.js"></script>
        <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
        <script src="assets/js/app.js"></script>

        <script>
            let allApps = [];
            let currentPage = 1;

            $(document).ready(function() {
                loadApps();
                loadLogs(1);
            });

            // 1. Load Applications
            async function loadApps() {
                try {
                    const res = await fetch('../api/admin/notifications.php?action=get_apps');
                    const json = await res.json();
                    if (json.status === 'success') {
                        allApps = json.data;
                        const appSelect = document.getElementById('appSelect');
                        const tplAppSelect = document.getElementById('tplAppSelect');
                        const logFilter = document.getElementById('logAppFilter');
                        
                        appSelect.innerHTML = '<option value="">-- Uygulama Seçiniz --</option>';
                        tplAppSelect.innerHTML = '';
                        logFilter.innerHTML = '<option value="">Tüm Uygulamalar</option>';

                        allApps.forEach(app => {
                            const badge = app.has_onesignal ? '' : ' (⚠️ OneSignal Eksik)';
                            appSelect.innerHTML += `<option value="${app.id}" data-has="${app.has_onesignal}" data-name="${app.name}">${app.name}${badge}</option>`;
                            tplAppSelect.innerHTML += `<option value="${app.id}">${app.name}</option>`;
                            logFilter.innerHTML += `<option value="${app.id}">${app.name}</option>`;
                        });

                        if (allApps.length > 0) {
                            appSelect.value = allApps[0].id;
                            tplAppSelect.value = allApps[0].id;
                            onAppChange();
                        }
                    }
                } catch (e) {
                    console.error('Apps load error:', e);
                }
            }

            // 2. On App Selection Change
            function onAppChange() {
                const appSelect = document.getElementById('appSelect');
                const selectedOpt = appSelect.options[appSelect.selectedIndex];
                const warningBox = document.getElementById('appWarning');
                const editLink = document.getElementById('appEditLink');
                const previewAppName = document.getElementById('previewAppName');

                if (!selectedOpt || !selectedOpt.value) return;

                const hasOneSignal = selectedOpt.getAttribute('data-has') === '1';
                const appName = selectedOpt.getAttribute('data-name') || 'Uygulama';

                previewAppName.innerText = appName;

                if (!hasOneSignal) {
                    warningBox.style.display = 'block';
                    editLink.href = `app_edit.php?id=${selectedOpt.value}#integrations`;
                } else {
                    warningBox.style.display = 'none';
                }
                updatePreview();
                loadAudienceStats(selectedOpt.value);
            }

            // 2.1. Load Audience & OneSignal Subscriber Counts
            async function loadAudienceStats(appId) {
                if (!appId) return;
                
                const countAllDb = document.getElementById('countAllDb');
                const countAllOs = document.getElementById('countAllOs');
                const countVipDb = document.getElementById('countVipDb');
                const countVipOs = document.getElementById('countVipOs');
                const countPendingDb = document.getElementById('countPendingDb');
                const countPendingOs = document.getElementById('countPendingOs');
                const liveOs = document.getElementById('liveOsSubscribers');
                const liveTotal = document.getElementById('liveTotalUsers');
                const osBadge = document.getElementById('osStatusBadge');

                countAllDb.innerHTML = '<i class="bx bx-loader-alt bx-spin font-size-10"></i>';
                countAllOs.innerHTML = '<i class="bx bx-loader-alt bx-spin font-size-10"></i>';
                countVipDb.innerHTML = '<i class="bx bx-loader-alt bx-spin font-size-10"></i>';
                countVipOs.innerHTML = '<i class="bx bx-loader-alt bx-spin font-size-10"></i>';
                countPendingDb.innerHTML = '<i class="bx bx-loader-alt bx-spin font-size-10"></i>';
                countPendingOs.innerHTML = '<i class="bx bx-loader-alt bx-spin font-size-10"></i>';
                liveOs.innerHTML = '<span class="text-muted"><i class="bx bx-loader-alt bx-spin me-1"></i>Sorgulanıyor...</span>';
                liveTotal.innerText = '...';

                try {
                    const res = await fetch(`../api/admin/notifications.php?action=get_audience_stats&app_id=${appId}&_t=${Date.now()}`);
                    const json = await res.json();
                    if (json.status === 'success') {
                        const d = json.data;

                        // 1. Database Registered Counts
                        countAllDb.innerText = `${d.db_total} DB Kayıt`;
                        countVipDb.innerText = `${d.db_vip} VIP Kayıt`;
                        countPendingDb.innerText = `${d.db_pending} Bekleyen`;
                        liveTotal.innerText = `${d.db_total} Üye`;

                        // 2. Real OneSignal Live Device Counts
                        const osTotal = (d.os_total !== null) ? d.os_total : 0;
                        const osVip = (d.os_vip !== null) ? d.os_vip : 0;
                        const osPending = (d.os_pending !== null) ? d.os_pending : 0;

                        countAllOs.innerHTML = `<i class="bx bx-broadcast me-1"></i>${osTotal} Cihaz (Canlı)`;
                        countVipOs.innerHTML = `<i class="bx bx-broadcast me-1"></i>${osVip} Cihaz (Canlı)`;
                        countPendingOs.innerHTML = `<i class="bx bx-broadcast me-1"></i>${osPending} Cihaz (Canlı)`;

                        // Style badge color depending on count
                        countAllOs.className = osTotal > 0 ? 'badge bg-success text-white font-size-10 d-block' : 'badge bg-soft-secondary text-secondary font-size-10 d-block';
                        countVipOs.className = osVip > 0 ? 'badge bg-success text-white font-size-10 d-block' : 'badge bg-soft-secondary text-secondary font-size-10 d-block';
                        countPendingOs.className = osPending > 0 ? 'badge bg-success text-white font-size-10 d-block' : 'badge bg-soft-secondary text-secondary font-size-10 d-block';

                        if (d.os_total !== null) {
                            liveOs.innerHTML = `<span class="badge bg-success font-size-12"><i class="bx bx-broadcast me-1"></i>${d.os_total} Canlı OneSignal Cihazı</span>`;
                        } else if (d.has_onesignal) {
                            liveOs.innerHTML = `<span class="badge bg-soft-info text-info font-size-12">0 Cihaz (Henüz abone yok)</span>`;
                        } else {
                            liveOs.innerHTML = `<span class="badge bg-soft-danger text-danger font-size-12">Yapılandırılmamış</span>`;
                        }

                        if (d.has_onesignal) {
                            osBadge.className = 'badge bg-soft-success text-success';
                            osBadge.innerHTML = '<i class="bx bx-check-circle me-1"></i>OneSignal Aktif';
                        } else {
                            osBadge.className = 'badge bg-soft-danger text-danger';
                            osBadge.innerHTML = '<i class="bx bx-x-circle me-1"></i>OneSignal Anahtarı Eksik';
                        }
                    }
                } catch (e) {
                    console.error('Audience stats fetch error:', e);
                }
            }

            // 3. Toggle User Select
            function toggleUserSelect() {
                const selectedSeg = document.querySelector('input[name="targetSegment"]:checked').value;
                const userRow = document.getElementById('userTargetRow');
                userRow.style.display = (selectedSeg === 'user') ? 'block' : 'none';
                if (selectedSeg === 'user') {
                    handleUserSearch('');
                }
            }

            // 3.1. Handle User Live Search
            let searchDebounce = null;
            async function handleUserSearch(q = '') {
                clearTimeout(searchDebounce);
                searchDebounce = setTimeout(async () => {
                    const appId = document.getElementById('appSelect').value;
                    const resultsBox = document.getElementById('userSearchResults');
                    
                    try {
                        let url = `../api/admin/notifications.php?action=search_users&q=${encodeURIComponent(q)}&_t=${Date.now()}`;
                        if (appId) url += `&app_id=${appId}`;
                        
                        const res = await fetch(url);
                        const json = await res.json();

                        if (json.status === 'success' && json.data.length > 0) {
                            resultsBox.innerHTML = '';
                            json.data.forEach(u => {
                                let badge = '<span class="badge bg-soft-info text-info">Bekleyen</span>';
                                if (u.status === 'approved') badge = '<span class="badge bg-soft-warning text-warning"><i class="bx bx-crown me-1"></i>VIP</span>';
                                else if (u.status === 'rejected') badge = '<span class="badge bg-soft-danger text-danger">Red</span>';

                                const item = document.createElement('a');
                                item.href = 'javascript:void(0)';
                                item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3';
                                item.innerHTML = `
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-2">
                                            <span class="avatar-title rounded-circle bg-primary text-white font-size-11 fw-bold">${(u.name || 'U').charAt(0).toUpperCase()}</span>
                                        </div>
                                        <div>
                                            <div class="fw-bold font-size-13 text-dark">${escapeHtml(u.name)} <small class="text-muted font-size-11">#${u.id}</small></div>
                                            <small class="text-muted font-size-11">${escapeHtml(u.email)}</small>
                                        </div>
                                    </div>
                                    <div>${badge}</div>
                                `;
                                item.onclick = () => selectUser(u);
                                resultsBox.appendChild(item);
                            });
                            resultsBox.style.display = 'block';
                        } else {
                            resultsBox.innerHTML = '<div class="p-3 text-center text-muted font-size-12">Kullanıcı bulunamadı.</div>';
                            resultsBox.style.display = 'block';
                        }
                    } catch (e) {
                        console.error('User search error:', e);
                    }
                }, 150);
            }

            // Close search results when clicking outside
            document.addEventListener('click', (e) => {
                const wrapper = document.getElementById('userSearchWrapper');
                if (wrapper && !wrapper.contains(e.target)) {
                    const resultsBox = document.getElementById('userSearchResults');
                    if (resultsBox) resultsBox.style.display = 'none';
                }
            });

            // 3.2. Select a User
            function selectUser(u) {
                document.getElementById('targetUserId').value = u.id;
                document.getElementById('selectedUserName').innerText = `${u.name} (#${u.id})`;
                document.getElementById('selectedUserEmail').innerText = u.email;
                document.getElementById('selectedUserInitial').innerText = (u.name || 'U').charAt(0).toUpperCase();

                const badge = document.getElementById('selectedUserBadge');
                if (u.status === 'approved') {
                    badge.className = 'badge bg-warning font-size-10 ms-1';
                    badge.innerText = 'Onaylı VIP';
                } else if (u.status === 'pending') {
                    badge.className = 'badge bg-info font-size-10 ms-1';
                    badge.innerText = 'Onay Bekleyen';
                } else {
                    badge.className = 'badge bg-secondary font-size-10 ms-1';
                    badge.innerText = u.status || 'Kullanıcı';
                }

                document.getElementById('selectedUserCard').style.setProperty('display', 'flex', 'important');
                document.getElementById('userSearchWrapper').style.display = 'none';
                document.getElementById('userSearchResults').style.display = 'none';
                updatePreview();
            }

            // 3.3. Clear Selected User
            function clearSelectedUser() {
                document.getElementById('targetUserId').value = '';
                document.getElementById('selectedUserCard').style.setProperty('display', 'none', 'important');
                document.getElementById('userSearchWrapper').style.display = 'block';
                document.getElementById('userSearchInput').value = '';
                document.getElementById('userSearchInput').focus();
                handleUserSearch('');
            }

            // 4. Insert Emoji to Message
            function insertEmoji(emoji) {
                const msg = document.getElementById('pushMessage');
                const start = msg.selectionStart;
                const end = msg.selectionEnd;
                msg.value = msg.value.substring(0, start) + emoji + msg.value.substring(end);
                msg.focus();
                msg.selectionStart = msg.selectionEnd = start + emoji.length;
                updatePreview();
            }

            // 5. Update Live Mobile Mockup Preview
            function updatePreview() {
                const title = document.getElementById('pushTitle').value || '⚽ Bildirim Başlığı';
                const body = document.getElementById('pushMessage').value || 'Bildirim mesajınız burada görüntülenecektir...';
                const img = document.getElementById('pushImage').value.trim();
                const route = document.getElementById('deepLink').value;

                document.getElementById('previewTitle').innerText = title;
                document.getElementById('previewBody').innerText = body;
                document.getElementById('titleCount').innerText = `${document.getElementById('pushTitle').value.length} / 100`;
                document.getElementById('messageCount').innerText = `${document.getElementById('pushMessage').value.length} / 300`;
                document.getElementById('previewRouteTag').innerHTML = `<i class="bx bx-link me-1"></i>${route}`;

                const prevImg = document.getElementById('previewImage');
                if (img) {
                    prevImg.src = img;
                    prevImg.style.display = 'block';
                } else {
                    prevImg.style.display = 'none';
                }
            }

            // 6. Handle Send Push Form
            async function handleSendPush(e) {
                e.preventDefault();

                const appId = document.getElementById('appSelect').value;
                const title = document.getElementById('pushTitle').value.trim();
                const message = document.getElementById('pushMessage').value.trim();
                const targetSegment = document.querySelector('input[name="targetSegment"]:checked').value;
                const targetUserId = document.getElementById('targetUserId').value.trim();
                const imageUrl = document.getElementById('pushImage').value.trim();
                const deepLink = document.getElementById('deepLink').value;
                const isTest = document.getElementById('testModeSwitch').checked;

                if (!appId) {
                    Swal.fire('Hata', 'Lütfen bir uygulama seçin.', 'warning');
                    return;
                }

                if (targetSegment === 'user' && !targetUserId) {
                    Swal.fire('Kullanıcı Seçilmedi', 'Lütfen arama kutusundan hedef kullanıcıyı seçin.', 'warning');
                    return;
                }

                const btn = document.getElementById('btnSendPush');
                const origText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Gönderiliyor...';

                try {
                    const payload = {
                        action: 'send',
                        app_id: appId,
                        title: title,
                        message: message,
                        target_segment: targetSegment,
                        target_user_id: targetUserId,
                        image_url: imageUrl,
                        deep_link: deepLink,
                        is_test: isTest
                    };

                    const res = await fetch('../api/admin/notifications.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();

                    if (data.status === 'success') {
                        let extraMsg = '';
                        if (data.dry_run) {
                            extraMsg = '<br><span class="badge bg-warning font-size-12 mt-2">🧪 Simülasyon / Test Modu Tamamlandı</span>';
                        } else {
                            extraMsg = '<br><span class="badge bg-soft-success text-success font-size-12 mt-2"><i class="bx bx-broadcast me-1"></i>OneSignal sunucuları üzerinden cihazlara anında iletildi</span>';
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            html: `${data.message}${extraMsg}`,
                            confirmButtonColor: '#5156be'
                        });

                        // Reset message only, keep title / app
                        document.getElementById('pushMessage').value = '';
                        updatePreview();
                        loadLogs(1);
                    } else {
                        Swal.fire('Gönderim Hatası', data.message || 'Bilinmeyen bir hata oluştu.', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    Swal.fire('Hata', 'Sunucu bağlantı hatası oluştu.', 'error');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = origText;
                }
            }

            // 7. Load Logs Table with Pagination
            async function loadLogs(page = 1) {
                currentPage = page;
                const appFilter = document.getElementById('logAppFilter').value;
                const tbody = document.getElementById('logsTableBody');
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-3 text-muted"><i class="bx bx-loader-alt bx-spin me-1"></i> Yükleniyor...</td></tr>';

                try {
                    let url = `../api/admin/notifications.php?action=list_logs&page=${page}&limit=15&_t=${Date.now()}`;
                    if (appFilter) url += `&app_id=${appFilter}`;

                    const res = await fetch(url);
                    const json = await res.json();

                    if (json.status === 'success' && json.data.length > 0) {
                        tbody.innerHTML = '';
                        json.data.forEach(log => {
                            let segBadge = '<span class="badge bg-primary">Tümü</span>';
                            if (log.target_segment === 'approved') segBadge = '<span class="badge bg-warning text-dark"><i class="bx bx-crown me-1"></i>VIP</span>';
                            else if (log.target_segment === 'pending') segBadge = '<span class="badge bg-info">Bekleyen</span>';
                            else if (log.target_segment === 'user') segBadge = `<span class="badge bg-secondary">Kullanıcı: #${log.target_user_id} (${log.target_user_name || ''})</span>`;

                            let statusBadge = '<span class="badge bg-success">Gönderildi</span>';
                            if (log.status === 'dry_run') statusBadge = '<span class="badge bg-soft-warning text-warning border border-warning">Test / Simüle</span>';
                            else if (log.status === 'failed') statusBadge = '<span class="badge bg-danger">Başarısız</span>';

                            tbody.innerHTML += `
                                <tr>
                                    <td class="fw-bold">#${log.id}</td>
                                    <td><small class="text-muted">${log.created_at}</small></td>
                                    <td><span class="badge bg-soft-primary text-primary font-size-12">${log.app_name || 'Silinmiş Uygulama'}</span></td>
                                    <td>
                                        <div class="fw-bold text-dark font-size-13">${escapeHtml(log.title)}</div>
                                        <div class="text-muted font-size-12 text-truncate" style="max-width: 280px;" title="${escapeHtml(log.message)}">${escapeHtml(log.message)}</div>
                                    </td>
                                    <td>${segBadge}</td>
                                    <td>${statusBadge}</td>
                                    <td><span class="badge bg-soft-success text-success font-size-12"><i class="bx bx-check-double me-1"></i>İletildi</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-soft-primary me-1" title="Tekrar Gönderim İçin Yükle" onclick="loadToForm(${escapeAttr(JSON.stringify(log))})">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                        <button class="btn btn-sm btn-soft-danger" title="Kaydı Sil" onclick="deleteLog(${log.id})">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });

                        renderPagination(json.pagination);
                    } else {
                        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Henüz gönderilmiş bir bildirim kaydı bulunamadı.</td></tr>';
                        document.getElementById('paginationWrapper').style.display = 'none';
                    }
                } catch (e) {
                    console.error(e);
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-3 text-danger">Kayıtlar yüklenirken hata oluştu.</td></tr>';
                }
            }

            // 8. Load Log Data back into Form
            function loadToForm(log) {
                document.getElementById('appSelect').value = log.app_id;
                document.getElementById('pushTitle').value = log.title;
                document.getElementById('pushMessage').value = log.message;
                document.getElementById('pushImage').value = log.image_url || '';
                document.getElementById('deepLink').value = log.deep_link || '/app/tips';

                if (log.target_segment) {
                    const r = document.querySelector(`input[name="targetSegment"][value="${log.target_segment}"]`);
                    if (r) {
                        r.checked = true;
                        toggleUserSelect();
                    }
                }
                if (log.target_user_id) {
                    document.getElementById('targetUserId').value = log.target_user_id;
                }

                onAppChange();
                updatePreview();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            // 9. Delete Log
            async function deleteLog(id) {
                const res = await Swal.fire({
                    title: 'Silinsin mi?',
                    text: 'Bu bildirim kaydını listeden silmek istediğinize emin misiniz?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f46a6a',
                    confirmButtonText: 'Evet, Sil',
                    cancelButtonText: 'İptal'
                });

                if (res.isConfirmed) {
                    try {
                        const delRes = await fetch('../api/admin/notifications.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ action: 'delete_log', id: id })
                        });
                        const data = await delRes.json();
                        if (data.status === 'success') {
                            loadLogs(currentPage);
                        }
                    } catch (e) {
                        console.error(e);
                    }
                }
            }

            // 10. Clear All Logs
            async function clearLogs() {
                const appFilter = document.getElementById('logAppFilter').value;
                const res = await Swal.fire({
                    title: 'Tüm Geçmiş Temizlensin mi?',
                    text: 'Seçili filtrenin tüm bildirim kayıtları kalıcı olarak silinecektir.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f46a6a',
                    confirmButtonText: 'Evet, Temizle',
                    cancelButtonText: 'İptal'
                });

                if (res.isConfirmed) {
                    try {
                        const clearRes = await fetch('../api/admin/notifications.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ action: 'clear_logs', app_id: appFilter || null })
                        });
                        const data = await clearRes.json();
                        if (data.status === 'success') {
                            loadLogs(1);
                        }
                    } catch (e) {
                        console.error(e);
                    }
                }
            }

            // 11. Pagination Helper
            function renderPagination(p) {
                if (!p || p.total_pages <= 1) {
                    document.getElementById('paginationWrapper').style.display = 'none';
                    return;
                }
                const wrapper = document.getElementById('paginationWrapper');
                const info = document.getElementById('paginationInfo');
                const ul = document.getElementById('paginationUl');

                wrapper.style.display = 'flex';
                info.innerText = `Toplam ${p.total} kayıt (Sayfa ${p.page} / ${p.total_pages})`;

                ul.innerHTML = '';
                if (p.page > 1) {
                    ul.innerHTML += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadLogs(${p.page - 1})">Önceki</a></li>`;
                }

                for (let i = 1; i <= p.total_pages; i++) {
                    if (i === 1 || i === p.total_pages || (i >= p.page - 2 && i <= p.page + 2)) {
                        const active = (i === p.page) ? 'active' : '';
                        ul.innerHTML += `<li class="page-item ${active}"><a class="page-link" href="javascript:void(0)" onclick="loadLogs(${i})">${i}</a></li>`;
                    } else if (i === p.page - 3 || i === p.page + 3) {
                        ul.innerHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                }

                if (p.page < p.total_pages) {
                    ul.innerHTML += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadLogs(${p.page + 1})">Sonraki</a></li>`;
                }
            }

            // 12. AUTOMATED TEMPLATES MANAGEMENT
            async function loadAutomatedTemplate() {
                const appId = document.getElementById('tplAppSelect').value || document.getElementById('appSelect').value;
                if (!appId) return;

                try {
                    const res = await fetch(`../api/admin/notifications.php?action=get_template&app_id=${appId}&event_key=vip_approval&_t=${Date.now()}`);
                    const json = await res.json();
                    if (json.status === 'success' && json.data) {
                        const d = json.data;
                        document.getElementById('tplIsActive').checked = (d.is_active == 1);
                        document.getElementById('tplDeepLink').value = d.deep_link || '/app/tips';

                        document.getElementById('tplTitleTr').value = d.title_tr || '';
                        document.getElementById('tplMessageTr').value = d.message_tr || '';

                        document.getElementById('tplTitleEn').value = d.title_en || '';
                        document.getElementById('tplMessageEn').value = d.message_en || '';

                        document.getElementById('tplTitleDe').value = d.title_de || '';
                        document.getElementById('tplMessageDe').value = d.message_de || '';

                        document.getElementById('tplTitleEs').value = d.title_es || '';
                        document.getElementById('tplMessageEs').value = d.message_es || '';

                        document.getElementById('tplTitlePt').value = d.title_pt || '';
                        document.getElementById('tplMessagePt').value = d.message_pt || '';

                        document.getElementById('tplTitleFr').value = d.title_fr || '';
                        document.getElementById('tplMessageFr').value = d.message_fr || '';
                    }
                } catch (e) {
                    console.error('Template load error:', e);
                }
            }

            async function saveAutomatedTemplate() {
                const appId = document.getElementById('tplAppSelect').value;
                const btn = document.getElementById('btnSaveTemplate');
                const origText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Kaydediliyor...';

                try {
                    const payload = {
                        action: 'save_template',
                        app_id: appId,
                        event_key: 'vip_approval',
                        name: 'VIP Üyelik Onay Bildirimi',
                        is_active: document.getElementById('tplIsActive').checked ? 1 : 0,
                        deep_link: document.getElementById('tplDeepLink').value,

                        title_tr: document.getElementById('tplTitleTr').value,
                        message_tr: document.getElementById('tplMessageTr').value,
                        title_en: document.getElementById('tplTitleEn').value,
                        message_en: document.getElementById('tplMessageEn').value,
                        title_de: document.getElementById('tplTitleDe').value,
                        message_de: document.getElementById('tplMessageDe').value,
                        title_es: document.getElementById('tplTitleEs').value,
                        message_es: document.getElementById('tplMessageEs').value,
                        title_pt: document.getElementById('tplTitlePt').value,
                        message_pt: document.getElementById('tplMessagePt').value,
                        title_fr: document.getElementById('tplTitleFr').value,
                        message_fr: document.getElementById('tplMessageFr').value
                    };

                    const res = await fetch('../api/admin/notifications.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const json = await res.json();

                    if (json.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Kaydedildi!',
                            text: 'Otomatik VIP onay bildirim şablonu tüm diller için başarıyla güncellendi.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Hata', json.message || 'Şablon kaydedilemedi.', 'error');
                    }
                } catch (e) {
                    console.error(e);
                    Swal.fire('Hata', 'Sunucu hatası oluştu.', 'error');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = origText;
                }
            }

            function insertTplVar(variable) {
                // Find currently active language tab textarea
                const activeTab = document.querySelector('#langTabs .nav-link.active');
                if (!activeTab) return;
                const targetHref = activeTab.getAttribute('href'); // e.g. #langTr
                const pane = document.querySelector(targetHref);
                if (!pane) return;
                const textarea = pane.querySelector('textarea');
                if (textarea) {
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    textarea.value = textarea.value.substring(0, start) + variable + textarea.value.substring(end);
                    textarea.focus();
                    textarea.selectionStart = textarea.selectionEnd = start + variable.length;
                }
            }

            async function testAutomatedPush() {
                const appId = document.getElementById('tplAppSelect').value;
                const titleTr = document.getElementById('tplTitleTr').value.replace('{name}', 'Alperen (Test)');
                const messageTr = document.getElementById('tplMessageTr').value.replace('{name}', 'Alperen (Test)');
                const deepLink = document.getElementById('tplDeepLink').value;

                Swal.fire({
                    title: 'Test Bildirimi Gönderilsin mi?',
                    text: 'Şu anki VIP onay bildirim şablonu telefonunuza push olarak iletilecektir.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Evet, Gönder',
                    cancelButtonText: 'İptal'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const res = await fetch('../api/admin/notifications.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    action: 'send',
                                    app_id: appId,
                                    title: titleTr,
                                    message: messageTr,
                                    target_segment: 'all',
                                    deep_link: deepLink,
                                    is_test: false
                                })
                            });
                            const data = await res.json();
                            if (data.status === 'success') {
                                Swal.fire('Gönderildi!', 'VIP onay test bildirimi OneSignal üzerinden iletildi.', 'success');
                            } else {
                                Swal.fire('Hata', data.message || 'Gönderilemedi.', 'error');
                            }
                        } catch (e) {
                            Swal.fire('Hata', 'Sunucu hatası oluştu.', 'error');
                        }
                    }
                });
            }

            function escapeHtml(text) {
                if (!text) return '';
                const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
                return String(text).replace(/[&<>"']/g, m => map[m]);
            }

            function escapeAttr(text) {
                if (!text) return '';
                return String(text).replace(/'/g, "&apos;").replace(/"/g, "&quot;");
            }
        </script>
    </body>
</html>
