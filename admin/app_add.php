<?php
require_once '../api/config/Database.php';
$db = Database::getInstance()->getConnection();
$brandsStmt = $db->query("SELECT id, name, slug, default_theme FROM brands WHERE is_active = 1 ORDER BY name ASC");
$brands = $brandsStmt->fetchAll(PDO::FETCH_ASSOC);
$app = []; // Boş dizi, edit yapısı ile benzerliği korumak için, ancak doldurulmayacak
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Yeni Uygulama Ekle | ACMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
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
                            <h4 class="mb-sm-0 font-size-18">Yeni Uygulama Ekle</h4>
                            <div class="page-title-right">
                                <a href="apps_list.php" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Listeye Dön</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- SOL: FORM -->
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-body">
                                <form id="appAddForm">
                                    <input type="hidden" name="action" value="create">

                                    <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-basic"><i class="bx bx-info-circle me-1"></i> Temel Bilgiler</a></li>
                                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-design"><i class="bx bx-palette me-1"></i> Tema & Renkler</a></li>
                                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-contact"><i class="bx bx-message-rounded-dots me-1"></i> İletişim & Sosyal Medya</a></li>
                                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-ux"><i class="bx bx-mobile-alt me-1"></i> UX & Karşılama</a></li>
                                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-native"><i class="bx bx-mobile-vibration me-1"></i> Cihaz & Titreşim</a></li>
                                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-rateus"><i class="bx bx-star me-1"></i> Değerlendirme & Hediye</a></li>
                                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-security"><i class="bx bx-shield me-1"></i> Güvenlik</a></li>
                                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-integrations"><i class="bx bx-plug me-1"></i> Entegrasyonlar</a></li>
                                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-legal"><i class="bx bx-shield-quarter me-1"></i> Yasal & Gizlilik</a></li>
                                    </ul>

                                    <div class="tab-content p-3 text-muted">
                                        <!-- TAB 1: Temel Bilgiler -->
                                        <div class="tab-pane active" id="tab-basic" role="tabpanel">
                                            <p class="text-muted small mb-4">Uygulamanın temel kimlik bilgileri. Slug, URL'de görünen benzersiz kısa addır.</p>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Marka Seçimi</label>
                                                    <select class="form-select" name="brand_id" required>
                                                        <option value="">Seçiniz...</option>
                                                        <?php foreach($brands as $brand): ?>
                                                        <option value="<?= $brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="name">App Adı</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="slug">Slug</label>
                                                    <input type="text" class="form-control" id="slug" name="slug" value="" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="frontend_url">Frontend URL</label>
                                                    <input type="text" class="form-control" id="frontend_url" name="frontend_url" value="">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="app_type">App Tipi</label>
                                                    <select class="form-select" id="app_type" name="app_type">
                                                        <option value="free">Free</option>
                                                        <option value="paid" selected>Paid</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="price">Fiyat ($)</label>
                                                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="0.00">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- TAB 2: Tasarım -->
                                        <div class="tab-pane" id="tab-design" role="tabpanel">
                                            <p class="text-muted small mb-4">Uygulamanın görsel kimliği. Renk değişiklikleri sağdaki önizlemede anlık yansır.</p>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="theme">Tema Seçimi</label>
                                                    <small class="d-block text-muted mb-1">Mobil uygulamanın genel renk ve stil teması.</small>
                                                    <select class="form-select" id="theme" name="theme">
                                                        <?php $themes = ['real' => 'Real Theme (Dark)', 'pep' => 'Pep Theme', 'alex' => 'Alex Theme', 'real_v2' => 'Real V2 (2026 Premium)']; foreach($themes as $tv => $tl): ?>
                                                        <option value="<?= $tv ?>"><?= $tl ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label" for="primary_color">Primary Color</label>
                                                    <input type="color" class="form-control form-control-color w-100" id="primary_color" name="primary_color" value="#5156be">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label" for="secondary_color">Secondary Color</label>
                                                    <input type="color" class="form-control form-control-color w-100" id="secondary_color" name="secondary_color" value="#333333">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label" for="accent_color">Accent Color</label>
                                                    <input type="color" class="form-control form-control-color w-100" id="accent_color" name="accent_color" value="#ffcc00">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label" for="bg_color">BG Color</label>
                                                    <input type="color" class="form-control form-control-color w-100" id="bg_color" name="bg_color" value="#ffffff">
                                                </div>
                                                <div class="col-12 mt-1 mb-3">
                                                    <label class="form-label d-flex justify-content-between">
                                                        <span><i class="bx bx-color-fill me-1"></i> 7 VIP Hazır Renk Paleti</span>
                                                        <small class="text-muted">Tek tıkla canlı önizlemeye uygula</small>
                                                    </label>
                                                    <div class="d-flex flex-wrap gap-2" id="color-presets">
                                                        <button type="button" class="btn btn-sm btn-outline-dark d-flex align-items-center gap-1 preset-btn" 
                                                            data-p="#00D4FF" data-s="#0066CC" data-a="#FFCC00" data-bg="#060D1A" style="border-radius: 8px;">
                                                            <span style="width:12px;height:12px;border-radius:50%;background:#00D4FF;"></span>
                                                            <span style="width:12px;height:12px;border-radius:50%;background:#0066CC;"></span>
                                                            <span>💎 Real VIP</span>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-dark d-flex align-items-center gap-1 preset-btn" 
                                                            data-p="#A855F7" data-s="#6366F1" data-a="#F59E0B" data-bg="#0B0814" style="border-radius: 8px;">
                                                            <span style="width:12px;height:12px;border-radius:50%;background:#A855F7;"></span>
                                                            <span style="width:12px;height:12px;border-radius:50%;background:#6366F1;"></span>
                                                            <span>👑 Elite VIP</span>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-dark d-flex align-items-center gap-1 preset-btn" 
                                                            data-p="#FF3366" data-s="#990033" data-a="#FFB800" data-bg="#0F0508" style="border-radius: 8px;">
                                                            <span style="width:12px;height:12px;border-radius:50%;background:#FF3366;"></span>
                                                            <span style="width:12px;height:12px;border-radius:50%;background:#990033;"></span>
                                                            <span>🔥 Fixed VIP</span>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-dark d-flex align-items-center gap-1 preset-btn" 
                                                            data-p="#10B981" data-s="#047857" data-a="#FACC15" data-bg="#040D08" style="border-radius: 8px;">
                                                            <span style="width:12px;height:12px;border-radius:50%;background:#10B981;"></span>
                                                            <span style="width:12px;height:12px;border-radius:50%;background:#047857;"></span>
                                                            <span>⚡ Special VIP</span>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-dark d-flex align-items-center gap-1 preset-btn" 
                                                            data-p="#F97316" data-s="#C2410C" data-a="#38BDF8" data-bg="#120904" style="border-radius: 8px;">
                                                            <span style="width:12px;height:12px;border-radius:50%;background:#F97316;"></span>
                                                            <span style="width:12px;height:12px;border-radius:50%;background:#C2410C;"></span>
                                                            <span>🚀 Over/Under VIP</span>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-dark d-flex align-items-center gap-1 preset-btn" 
                                                            data-p="#3B82F6" data-s="#1D4ED8" data-a="#34D399" data-bg="#050B14" style="border-radius: 8px;">
                                                            <span style="width:12px;height:12px;border-radius:50%;background:#3B82F6;"></span>
                                                            <span style="width:12px;height:12px;border-radius:50%;background:#1D4ED8;"></span>
                                                            <span>🌌 Daily Safe VIP</span>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-dark d-flex align-items-center gap-1 preset-btn" 
                                                            data-p="#F43F5E" data-s="#881337" data-a="#E2E8F0" data-bg="#0C0A0E" style="border-radius: 8px;">
                                                            <span style="width:12px;height:12px;border-radius:50%;background:#F43F5E;"></span>
                                                            <span style="width:12px;height:12px;border-radius:50%;background:#881337;"></span>
                                                            <span>🏆 Master VIP</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="font_family">Font Ailesi</label>
                                                    <select class="form-select" id="font_family" name="font_family">
                                                        <?php $fonts = ['Inter','Outfit','Syne','Nunito','Rajdhani','Barlow','DM Sans','Poppins','Space Grotesk','Oswald']; foreach($fonts as $f): ?>
                                                        <option value="<?= $f ?>"><?= $f ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="logo_file">Logo Yükle</label>
                                                    <input type="file" class="form-control" id="logo_file" name="logo_file">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="favicon_file">Favicon Yükle</label>
                                                    <input type="file" class="form-control" id="favicon_file" name="favicon_file">
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="play_store_link">Play Store Linki</label>
                                                    <input type="text" class="form-control" id="play_store_link" name="play_store_link" value="">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- TAB 3: İletişim -->
                                        <div class="tab-pane" id="tab-contact" role="tabpanel">
                                            <p class="text-muted small mb-4">Kullanıcıların 'İletişim' ekranında göreceği kanallar ve tahmini yanıt süreleri.</p>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="contact_telegram">Telegram (URL)</label>
                                                    <input type="url" class="form-control mb-1" id="contact_telegram" name="contact_telegram" value="">
                                                    <input type="text" class="form-control" id="contact_telegram_response" name="contact_telegram_response" placeholder="Yanıt Süresi Örn: 1 Saat" value="">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="contact_whatsapp">WhatsApp (Tel)</label>
                                                    <input type="text" class="form-control mb-1" id="contact_whatsapp" name="contact_whatsapp" value="">
                                                    <input type="text" class="form-control" id="contact_whatsapp_response" name="contact_whatsapp_response" placeholder="Yanıt Süresi" value="">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="contact_instagram">Instagram (URL)</label>
                                                    <input type="url" class="form-control mb-1" id="contact_instagram" name="contact_instagram" value="">
                                                    <input type="text" class="form-control" id="contact_instagram_response" name="contact_instagram_response" placeholder="Yanıt Süresi" value="">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="contact_email">Email</label>
                                                    <input type="email" class="form-control mb-1" id="contact_email" name="contact_email" value="">
                                                    <input type="text" class="form-control" id="contact_email_response" name="contact_email_response" placeholder="Yanıt Süresi" value="">
                                                </div>
                                                           <!-- TAB 4: UX & Karşılama -->
                                        <div class="tab-pane" id="tab-ux" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-check form-switch mt-4">
                                                        <input class="form-check-input" type="checkbox" id="welcome_modal_active" name="welcome_modal_active" value="1">
                                                        <label class="form-check-label" for="welcome_modal_active">Welcome Modal Aktif</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label" for="welcome_modal_title">Duyuru Başlığı</label>
                                                    <input type="text" class="form-control" name="welcome_modal_title" id="welcome_modal_title" 
                                                           value="Important Notice" placeholder="Örn: Güncelleme & Önemli Duyuru">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label" for="welcome_modal_frequency">Gösterim Sıklığı</label>
                                                    <select class="form-select" name="welcome_modal_frequency" id="welcome_modal_frequency">
                                                        <option value="daily" selected>Günde 1 Kez</option>
                                                        <option value="always">Her Açılışta</option>
                                                        <option value="once">Sadece İlk Açılışta (1 Kez)</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="welcome_modal_text">Welcome Modal Metni</label>
                                                    <textarea class="form-control" id="welcome_modal_text" name="welcome_modal_text" rows="2"></textarea>
                                                    <small class="text-muted">Kullanıcıya çıkan açılış/karşılama pop-up duyurusu.</small>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="home_announcement_text">Ana Sayfa Duyuru Metni (Latest News)</label>
                                                    <textarea class="form-control" id="home_announcement_text" name="home_announcement_text" rows="2"></textarea>
                                                    <small class="text-muted">Ana sayfadaki 'Latest News' bandında görünen metin.</small>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="post_register_text">Kayıt Sonrası Metni</label>
                                                    <textarea class="form-control" id="post_register_text" name="post_register_text" rows="2"></textarea>
                                                    <small class="text-muted">Kullanıcı kayıt olduktan sonra göreceği 'Onay bekleniyor' mesajı.</small>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="empty_state_text">Boş Durum (Empty) Metni</label>
                                                    <textarea class="form-control" id="empty_state_text" name="empty_state_text" rows="2"></textarea>
                                                    <small class="text-muted">Maç verisi olmadığında gösterilecek boş durum mesajı.</small>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="vip_hub_description">VIP Hub Açıklaması</label>
                                                    <textarea class="form-control" id="vip_hub_description" name="vip_hub_description" rows="2"></textarea>
                                                    <small class="text-muted">VIP Hub sayfasında bu uygulamanın diğer uygulamalarda gösterilen tanıtım açıklaması.</small>
                                                </div>
                                            </div>
                                            <hr>
                                            <h6 class="mb-3">Onboarding Ekranları</h6>
                                            <p class="text-muted small">Uygulamayı ilk açan kullanıcının gördüğü 3 adımlı karşılama slaytları.</p>
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <input type="text" class="form-control mb-2" id="onboarding_step1_title" name="onboarding_step1_title" placeholder="Step 1 Title" value="">
                                                    <input type="text" class="form-control" id="onboarding_step1_desc" name="onboarding_step1_desc" placeholder="Step 1 Desc" value="">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <input type="text" class="form-control mb-2" id="onboarding_step2_title" name="onboarding_step2_title" placeholder="Step 2 Title" value="">
                                                    <input type="text" class="form-control" id="onboarding_step2_desc" name="onboarding_step2_desc" placeholder="Step 2 Desc" value="">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <input type="text" class="form-control mb-2" id="onboarding_step3_title" name="onboarding_step3_title" placeholder="Step 3 Title" value="">
                                                    <input type="text" class="form-control" id="onboarding_step3_desc" name="onboarding_step3_desc" placeholder="Step 3 Desc" value="">
                                                </div>
                                            </div>
                                            <hr>
                                            <h6 class="mb-3">Kılavuz Adımları (Guest Landing)</h6>
                                            <p class="text-muted small">Giriş/Kayıt ekranında 'Nasıl Çalışır' bölümündeki 3 adım metni.</p>
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <input type="text" class="form-control" id="guide_step_1" name="guide_step_1" placeholder="Step 1" value="">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <input type="text" class="form-control" id="guide_step_2" name="guide_step_2" placeholder="Step 2" value="">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <input type="text" class="form-control" id="guide_step_3" name="guide_step_3" placeholder="Step 3" value="">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- TAB: Cihaz & Titreşim (Haptic & Keep Awake) -->
                                        <div class="tab-pane" id="tab-native" role="tabpanel">
                                            <p class="text-muted small mb-4">Mobil cihaz donanım etkileşimleri (Titreşim motoru ve ekran açık tutma) ayarları.</p>
                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <div class="card border border-primary bg-soft-primary mb-0">
                                                        <div class="card-body">
                                                            <h5 class="card-title text-primary mb-2"><i class="bx bx-mobile-vibration me-2 font-size-18"></i> Titreşimli Dokunma (Haptic Feedback)</h5>
                                                            <p class="text-muted small mb-3">Alt menü geçişlerinde, tarih seçiminde ve buton etkileşimlerinde kullanıcının cihazında titreşim hissi oluşturur.</p>
                                                            
                                                            <div class="form-check form-switch mb-3">
                                                                <input class="form-check-input" type="checkbox" id="enable_haptic" name="enable_haptic" value="1" checked>
                                                                <label class="form-check-label fw-bold" for="enable_haptic">Titreşimli Dokunma Aktif</label>
                                                            </div>

                                                            <div class="mb-2">
                                                                <label class="form-label" for="haptic_intensity">Titreşim Şiddeti (Yoğunluk)</label>
                                                                <select class="form-select" id="haptic_intensity" name="haptic_intensity">
                                                                    <option value="light" selected>Hafif (Light - 15ms) • Tavsiye Edilen</option>
                                                                    <option value="medium">Orta (Medium - 30ms)</option>
                                                                    <option value="heavy">Güçlü (Heavy - 60ms)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-4">
                                                    <div class="card border border-info bg-soft-info mb-0">
                                                        <div class="card-body">
                                                            <h5 class="card-title text-info mb-2"><i class="bx bx-sun me-2 font-size-18"></i> Ekranı Sürekli Açık Tutma (Keep Awake)</h5>
                                                            <p class="text-muted small mb-3">Kullanıcı tahminler sayfasında veya uygulama içindeyken ekranın kararmasını ve kilitlenmesini engeller (FLAG_KEEP_SCREEN_ON).</p>
                                                            
                                                            <div class="form-check form-switch mb-3">
                                                                <input class="form-check-input" type="checkbox" id="keep_screen_awake" name="keep_screen_awake" value="1" checked>
                                                                <label class="form-check-label fw-bold" for="keep_screen_awake">Ekranı Sürekli Açık Tut</label>
                                                            </div>
                                                            <small class="text-muted d-block">Özellikle maç ve tahmin incelerken ekranın kapanmasını önleyerek kullanıcı deneyimini artırır.</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- TAB 5: Rate Us -->
                                        <div class="tab-pane" id="tab-rateus" role="tabpanel">
                                            <p class="text-muted small mb-4">Kullanıcıdan uygulama değerlendirmesi isteyen pop-up akışı ayarları.</p>
                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <div class="form-check form-switch mt-4">
                                                        <input class="form-check-input" type="checkbox" id="rate_us_active" name="rate_us_active" value="1">
                                                        <label class="form-check-label" for="rate_us_active">Rate Us Aktif</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label" for="rate_us_snooze_days">Snooze (Gün)</label>
                                                    <input type="number" class="form-control" id="rate_us_snooze_days" name="rate_us_snooze_days" value="3">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="rate_us_reward">Ödül</label>
                                                    <input type="text" class="form-control" id="rate_us_reward" name="rate_us_reward" value="">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="rate_us_title">Başlık</label>
                                                    <input type="text" class="form-control" id="rate_us_title" name="rate_us_title" value="">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="rate_us_text">Açıklama</label>
                                                    <input type="text" class="form-control" id="rate_us_text" name="rate_us_text" value="">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="rate_us_rate_btn_text">Puanla Butonu</label>
                                                    <input type="text" class="form-control" id="rate_us_rate_btn_text" name="rate_us_rate_btn_text" value="">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="rate_us_later_btn_text">Daha Sonra Butonu</label>
                                                    <input type="text" class="form-control" id="rate_us_later_btn_text" name="rate_us_later_btn_text" value="">
                                                </div>
                                            </div>
                                            <hr>
                                            <h6 class="mb-3">Onay / Kanıt Adımı (Step 2)</h6>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="rate_us_step2_title">Step 2 Title</label>
                                                    <input type="text" class="form-control" id="rate_us_step2_title" name="rate_us_step2_title" value="">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="rate_us_step2_text">Step 2 Text</label>
                                                    <input type="text" class="form-control" id="rate_us_step2_text" name="rate_us_step2_text" value="">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label" for="rate_us_step2_email_btn">Email Btn</label>
                                                    <input type="text" class="form-control" id="rate_us_step2_email_btn" name="rate_us_step2_email_btn" value="">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label" for="rate_us_step2_telegram_btn">Telegram Btn</label>
                                                    <input type="text" class="form-control" id="rate_us_step2_telegram_btn" name="rate_us_step2_telegram_btn" value="">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label" for="rate_us_step2_done_btn">Done Btn</label>
                                                    <input type="text" class="form-control" id="rate_us_step2_done_btn" name="rate_us_step2_done_btn" value="">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- TAB 6: Güvenlik -->
                                        <div class="tab-pane" id="tab-security" role="tabpanel">
                                            <p class="text-muted small mb-4">Erişim kısıtlamaları ve teknik güvenlik ayarları. Bakım modu açılırsa tüm kullanıcılar uygulamayı göremez.</p>
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-check form-switch mt-4">
                                                        <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1">
                                                        <label class="form-check-label" for="maintenance_mode">Bakım Modu</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-check form-switch mt-4">
                                                        <input class="form-check-input" type="checkbox" id="is_ios_allowed" name="is_ios_allowed" value="1">
                                                        <label class="form-check-label" for="is_ios_allowed">iOS İzni Ver</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-check form-switch mt-4">
                                                        <input class="form-check-input" type="checkbox" id="forced_login" name="forced_login" value="1">
                                                        <label class="form-check-label" for="forced_login">Zorunlu Giriş</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-check form-switch mt-4">
                                                        <input class="form-check-input" type="checkbox" id="prevent_screenshot" name="prevent_screenshot" value="1" checked>
                                                        <label class="form-check-label text-danger fw-bold" for="prevent_screenshot">🛡️ Ekran Kaydı & Görüntüsü Engelle (FLAG_SECURE)</label>
                                                    </div>
                                                    <small class="text-muted d-block mt-1">Tahmin sayfalarında ekran görüntüsü ve video kaydını engeller. Sadece <b>Güvenlik Muafiyeti</b> olan test kullanıcıları görüntü alabilir.</small>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label" for="app_version">App Versiyonu</label>
                                                    <input type="text" class="form-control" id="app_version" name="app_version" value="1.0.0">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label" for="min_required_version">Min. Versiyon</label>
                                                    <input type="text" class="form-control" id="min_required_version" name="min_required_version" value="1.0.0">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-check form-switch mt-4">
                                                        <input class="form-check-input" type="checkbox" id="is_force_update" name="is_force_update" value="1">
                                                        <label class="form-check-label" for="is_force_update">Zorunlu Güncelleme</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="user_agent">Zorunlu User-Agent</label>
                                                    <input type="text" class="form-control" id="user_agent" name="user_agent" value="">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="guest_tips_limit">Guest Maç Limiti</label>
                                                    <input type="number" class="form-control" id="guest_tips_limit" name="guest_tips_limit" value="3">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="history_limit_days">Geçmiş Gün Gösterim Limiti</label>
                                                    <input type="number" class="form-control" id="history_limit_days" name="history_limit_days" value="10">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- TAB 7: Entegrasyonlar -->
                                        <div class="tab-pane" id="tab-integrations" role="tabpanel">
                                            <p class="text-muted small mb-4">Üçüncü parti servis bağlantıları. OneSignal push bildirim, Tawk.to canlı destek.</p>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="onesignal_app_id">OneSignal App ID</label>
                                                    <input type="text" class="form-control" id="onesignal_app_id" name="onesignal_app_id" value="">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="onesignal_api_key">OneSignal API Key</label>
                                                    <input type="text" class="form-control" id="onesignal_api_key" name="onesignal_api_key" value="">
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="tawk_to_id">Tawk.to Property ID</label>
                                                    <input type="text" class="form-control" id="tawk_to_id" name="tawk_to_id" value="">
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="appmetrica_key">
                                                        <i class="bx bx-bar-chart-alt-2 me-1 text-primary"></i> Yandex AppMetrica API / App Key
                                                    </label>
                                                    <input type="text" class="form-control font-monospace" id="appmetrica_key" name="appmetrica_key" value="" placeholder="Örn: 7d7fd12a-02e2-4d54-98c5-09de6d1954e0">
                                                    <div class="form-text text-muted small">
                                                        <i class="bx bx-info-circle me-1"></i> Google Play politika incelemelerinden bağımsız, güvenli mobil analitik ve kullanıcı oturum takibi için AppMetrica anahtarı.
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="custom_scripts">Özel Script (JS)</label>
                                                    <textarea class="form-control" id="custom_scripts" name="custom_scripts" rows="4"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- TAB 8: Yasal -->
                                        <div class="tab-pane" id="tab-legal" role="tabpanel">
                                            <p class="text-muted small mb-4">Kullanıcının profil ekranından erişeceği yasal metinler.</p>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="privacy_policy">Privacy Policy</label>
                                                    <textarea class="form-control" id="privacy_policy" rows="5"></textarea>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="terms_of_use">Terms of Use</label>
                                                    <textarea class="form-control" id="terms_of_use" rows="5"></textarea>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label" for="about_us">About Us</label>
                                                    <textarea class="form-control" id="about_us" rows="4"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    
                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-primary" id="submitBtn">Kaydet</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SAĞ: CANLI ÖNİZLEME (IFRAME MOCKUP) -->
                    <div class="col-xl-4">
                        <div class="card sticky-top" style="top: 80px;">
                            <div class="card-body text-center p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="card-title text-uppercase text-muted m-0" style="font-size:11px; letter-spacing:1px;">
                                        <i class="bx bx-mobile-alt me-1 text-primary"></i> Canlı Önizleme
                                    </h6>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" id="btn-reload-preview" style="font-size:10px;" title="Yeniden Yükle">
                                        <i class="bx bx-refresh"></i> Yenile
                                    </button>
                                </div>
                                
                                <!-- Telefon Çerçevesi (Dış: 340x680px, İç: 320x660px) -->
                                <div id="phone-wrapper" style="
                                    width: 340px;
                                    height: 680px;
                                    margin: 0 auto;
                                    background: #060d1a;
                                    border: 10px solid #1a1a2e;
                                    border-radius: 46px;
                                    position: relative;
                                    overflow: hidden;
                                    box-shadow: 0 25px 60px rgba(0,0,0,0.5), inset 0 0 0 2px rgba(255,255,255,0.08);
                                ">
                                    <!-- Dynamic Island -->
                                    <div style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 90px; height: 18px; background: #1a1a2e; border-radius: 0 0 12px 12px; z-index: 20;"></div>
                                    
                                    <!-- Gerçek SPA Iframe (375x773px -> scale(0.8533) -> tam 320x660px) -->
                                    <iframe id="spa-preview-iframe" 
                                        src="../app/index.html?app_id=1&preview=true" 
                                        style="
                                            width: 375px;
                                            height: 773px;
                                            border: none;
                                            transform: scale(0.8533);
                                            transform-origin: top left;
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            background: #060d1a;
                                            overflow: hidden;
                                        ">
                                    </iframe>
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
                    <div class="col-sm-6"><script>document.write(new Date().getFullYear())</script> &copy; ACMS.</div>
                    <div class="col-sm-6"><div class="text-sm-end d-none d-sm-block">ACMS Admin Panel</div></div>
                </div>
            </div>
        </footer>
    </div> <!-- main-content -->
</div> <!-- layout-wrapper -->

<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/libs/metismenu/metisMenu.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script>
<script src="assets/libs/node-waves/waves.min.js"></script>
<script src="assets/libs/feather-icons/feather.min.js"></script>
<script src="assets/libs/pace-js/pace.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/acms-core.js?v=2"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    const previewFrame = document.getElementById('spa-preview-iframe');

    // Formdaki tüm güncel verileri SPA'ya gönderen fonksiyon
    function sendConfigToPreview() {
        if (!previewFrame || !previewFrame.contentWindow) return;

        const previewData = {
            name: document.getElementById('name')?.value || '',
            theme: document.getElementById('theme')?.value || 'real_v2',
            font_family: document.getElementById('font_family')?.value || 'Inter',
            primary_color: document.getElementById('primary_color')?.value || '#00D4FF',
            secondary_color: document.getElementById('secondary_color')?.value || '#0066CC',
            accent_color: document.getElementById('accent_color')?.value || '#FFCC00',
            bg_color: document.getElementById('bg_color')?.value || '#060D1A',
            home_announcement_text: document.getElementById('home_announcement_text')?.value || '',
            welcome_modal_active: document.getElementById('welcome_modal_active')?.checked ? 1 : 0,
            welcome_modal_title: document.getElementById('welcome_modal_title')?.value || 'Important Notice',
            welcome_modal_text: document.getElementById('welcome_modal_text')?.value || '',
            welcome_modal_frequency: document.getElementById('welcome_modal_frequency')?.value || 'daily',
            contact: {
                telegram: document.getElementById('contact_telegram')?.value || '',
                telegram_response: document.getElementById('contact_telegram_response')?.value || '',
                whatsapp: document.getElementById('contact_whatsapp')?.value || '',
                whatsapp_response: document.getElementById('contact_whatsapp_response')?.value || '',
                instagram: document.getElementById('contact_instagram')?.value || '',
                instagram_response: document.getElementById('contact_instagram_response')?.value || '',
                email: document.getElementById('contact_email')?.value || '',
                email_response: document.getElementById('contact_email_response')?.value || ''
            },
            privacy_policy: document.getElementById('privacy_policy')?.value || '',
            terms_of_use: document.getElementById('terms_of_use')?.value || '',
            about_us: document.getElementById('about_us')?.value || '',
            onboarding_steps: [
                {
                    title: document.getElementById('onboarding_step1_title')?.value || 'High Win Rate',
                    desc: document.getElementById('onboarding_step1_desc')?.value || 'Get access to premium betting tips with a highly proven success record.'
                },
                {
                    title: document.getElementById('onboarding_step2_title')?.value || 'Daily Safe Picks',
                    desc: document.getElementById('onboarding_step2_desc')?.value || 'Our experts analyze hundreds of matches to bring you the safest picks daily.'
                },
                {
                    title: document.getElementById('onboarding_step3_title')?.value || 'Join the VIP Family',
                    desc: document.getElementById('onboarding_step3_desc')?.value || 'Become a VIP member today and start winning consistently.'
                }
            ],
            guide_steps: [
                document.getElementById('guide_step_1')?.value || 'Step 1: Purchase on Google Play',
                document.getElementById('guide_step_2')?.value || 'Step 2: Register with Order Code',
                document.getElementById('guide_step_3')?.value || 'Step 3: Get Approved & Access Tips'
            ],
            rate_us: {
                active: document.getElementById('rate_us_active')?.checked ? 1 : 0,
                title: document.getElementById('rate_us_title')?.value || 'Enjoying the App?',
                text: document.getElementById('rate_us_text')?.value || '',
                reward: document.getElementById('rate_us_reward')?.value || '',
                rate_btn_text: document.getElementById('rate_us_rate_btn_text')?.value || '⭐ Rate on Google Play',
                later_btn_text: document.getElementById('rate_us_later_btn_text')?.value || 'Remind me later',
                step2_title: document.getElementById('rate_us_step2_title')?.value || 'Thanks for your support! 🙌',
                step2_text: document.getElementById('rate_us_step2_text')?.value || '',
                step2_email_btn: document.getElementById('rate_us_step2_email_btn')?.value || '📩 Send via Email',
                step2_telegram_btn: document.getElementById('rate_us_step2_telegram_btn')?.value || '💬 Send via Telegram',
                step2_done_btn: document.getElementById('rate_us_step2_done_btn')?.value || 'Done ✓'
            }
        };

        previewFrame.contentWindow.postMessage({
            type: 'ACMS_PREVIEW_UPDATE',
            data: previewData
        }, '*');
    }

    // Input ve Select dinleyicileri (Anlık Canlı Önizleme)
    const formInputs = document.querySelectorAll('#appAddForm input, #appAddForm select, #appAddForm textarea');
    formInputs.forEach(input => {
        input.addEventListener('input', sendConfigToPreview);
        input.addEventListener('change', sendConfigToPreview);
    });

    // Welcome modal aktif checkbox değiştiğinde canlı önizlemeye tetikleme gönder
    const welcomeModalSwitch = document.getElementById('welcome_modal_active');
    if (welcomeModalSwitch) {
        welcomeModalSwitch.addEventListener('change', function() {
            if (this.checked && previewFrame && previewFrame.contentWindow) {
                previewFrame.contentWindow.postMessage({ type: 'ACMS_TRIGGER_WELCOME_MODAL' }, '*');
            }
        });
    }

    // Hazır Renk Paletleri Dinleyicisi
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const p = this.dataset.p;
            const s = this.dataset.s;
            const a = this.dataset.a;
            const bg = this.dataset.bg;
            
            if (document.getElementById('primary_color')) document.getElementById('primary_color').value = p;
            if (document.getElementById('secondary_color')) document.getElementById('secondary_color').value = s;
            if (document.getElementById('accent_color')) document.getElementById('accent_color').value = a;
            if (document.getElementById('bg_color')) document.getElementById('bg_color').value = bg;
            
            // Canlı Önizleme iframe'ine anında gönder
            if (typeof sendConfigToPreview === 'function') {
                sendConfigToPreview();
            }
        });
    });

    // Sekme Tıklamalarına Göre Akıllı Rota Yönlendirme
    document.querySelectorAll('.nav-tabs .nav-link').forEach(link => {
        link.addEventListener('click', function() {
            const target = this.getAttribute('href');
            let route = '/app/home';
            if (target === '#tab-ux') {
                route = '/onboarding';
            } else if (target === '#tab-contact') {
                route = '/app/support';
            } else if (target === '#tab-legal') {
                route = '/app/profile';
            } else if (target === '#tab-rateus') {
                route = '/app/home';
                if (previewFrame && previewFrame.contentWindow) {
                    previewFrame.contentWindow.postMessage({ type: 'ACMS_TRIGGER_RATE_US' }, '*');
                }
            }
            if (previewFrame && previewFrame.contentWindow) {
                previewFrame.contentWindow.postMessage({
                    type: 'ACMS_PREVIEW_NAVIGATE',
                    route: route
                }, '*');
            }
        });
    });

    // Iframe ilk yüklendiğinde mevcut form verisini SPA'ya aktar
    if (previewFrame) {
        previewFrame.addEventListener('load', function() {
            setTimeout(sendConfigToPreview, 250);
        });
    }

    // Yenileme butonu
    const reloadBtn = document.getElementById('btn-reload-preview');
    if (reloadBtn && previewFrame) {
        reloadBtn.addEventListener('click', function() {
            previewFrame.src = previewFrame.src;
        });
    }
    
    // Form Submit
    document.getElementById('appAddForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('legal_texts_json', JSON.stringify({
            privacy: document.getElementById('privacy_policy')?.value || '',
            terms:   document.getElementById('terms_of_use')?.value || '',
            about:   document.getElementById('about_us')?.value || ''
        }));
        const btn = document.getElementById('submitBtn');
        btn.disabled = true; btn.innerHTML = 'Kaydediliyor...';
        fetch('../api/admin/apps.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({icon:'success',title:'Başarılı!',text:res.message,timer:2000,showConfirmButton:false})
                        .then(() => window.location.href = 'apps_list.php');
                } else {
                    Swal.fire('Hata!', res.message || 'Bir sorun oluştu.', 'error');
                    btn.disabled = false; btn.innerHTML = 'Kaydet';
                }
            }).catch(() => { Swal.fire('Hata!','Sunucu hatası.','error'); btn.disabled=false; btn.innerHTML='Kaydet'; });
    });
});
</script>
</body>
</html>