# ACMS Webview SPA — Geliştirici Rehberi & Mimari Dökümanı

> **Okuma Talimatı:** Bu döküman **öncelik sırasına** göre yazılmıştır.
> Her faz tamamlandıkça `[x]` ile işaretlenecektir.
> **Faz 1 bitmeden Faz 2'ye geçilmez.**

---

## ⚙️ BÖLÜM A — MİMARİ TEMEL KANUNLAR
*(Geliştirmeye başlamadan önce bilinmesi zorunlu kurallar)*

### A.1 Teknoloji Yığını
- **Frontend:** Saf HTML + **Vue.js 3 (CDN)** + Vue Router. Node.js, Webpack, derleyici YOKTUR.
- **Styling:** Vanilla CSS + CSS Variables (`:root`). Bootstrap yardımcı olarak kullanılabilir.
- **Backend:** PHP (PDO) — sadece JSON dönen API uçları.
- **Uygulama Dili:** TÜM arayüz metinleri **İngilizce (English).**

### A.2 Klasör Yapısı
```
app/
├── index.html           ← Tüm markalar için TEK giriş noktası
├── app.js               ← Vue instance, Router, State
├── core.css             ← Değişmez temel stiller (reset, utility)
├── themes/
│   ├── real.css         ← Real markası layout + CSS değişkenleri
│   ├── alex.css         ← Alex markası layout + CSS değişkenleri
│   └── pep.css          ← Pep markası layout + CSS değişkenleri
├── components/          ← Vue component dosyaları
└── assets/              ← Yerel ikonlar, logo placeholder'lar
```

### A.3 Reskin (Tema) Motoru — Nasıl Çalışır?
```
init.php → { theme: "real", primary: "#00c4ff", accent: "#ffd700", logo: "..." }
     ↓
Vue: <link href="themes/real.css"> eklenir   ← Real'in YAPISINI yükler
Vue: :root { --primary: #00c4ff; --accent: #ffd700; } enjekte edilir  ← Renkleri override eder
     ↓
Sonuç: Real VIP ve Real Elite aynı YAPIDA ama FARKLI renklerde görünür.
       Alex tamamen farklı bir YAPIDA (flat nav, keskin köşeler) açılır.
```

**Kural:** Her marka (Real, Alex, Pep) = 1 adet `theme-X.css` dosyası.
O marka altındaki tüm uygulamalar (VIP, Elite, Fixed...) aynı tema dosyasını kullanır, sadece CSS değişkenleri (`--primary`, `--accent`, `--bg`) API'den gelen renklerle değişir.

### A.4 Performans Kuralları (Kasmasız Uygulama)
- Tüm CSS animasyonları YALNIZCA `transform` ve `opacity` üzerinden çalışır. `top/left/margin` animasyonu YASAKTIR.
- Sabit elementlerde (`position: fixed`) `will-change: transform` kullanılır.
- Sayfa geçişleri `translate3d()` ile (GPU hızlandırmalı).
- Görseller `loading="lazy"` ile yüklenir.
- API verisi beklenirken **Skeleton Loader** kullanılır — spinner kullanılmaz.

### A.5 İkon Standartı
- Emoji ve yapay zeka görseli YASAKTIR.
- Sadece **MDI (Material Design Icons)** veya **FontAwesome** kullanılır.

### A.6 Multi-Brand Algısı
- Her markanın `index.html`'i `data-theme="real"` gibi bir etiket taşır.
- `init.php` bu değeri okuyarak o markaya ait tema, renk ve ayarları döndürür.

### A.7 Web Dosyaları Cache Busting
- `init.php` yanıtında `web_version` alanı bulunur.

### A.8 - Cache Busting Stratejisi
`init.php` içinde `web_version` döner (örn: `1.2.0`). Vue her açılışta bu versiyonu `localStorage`'daki değerle karşılaştırır. Farklıysa `location.reload(true)` ile cache'i patlatıp yeni dosyaları çeker. Hata durumunda (sunucu çökmesi), Vue `localStorage`'daki son geçerli temayla (offline mode) açılmaya devam eder.

### A.9 - Kesin Mobil Uyumluluk (Mobile Only)
Bu proje bir Webview SPA'dır.
- `index.html` içine `<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">` kesin eklenecektir.
- Sabit piksel genişlikleri (örn: `width: 350px`) KULLANILMAYACAKTIR.
- Eski (iPhone SE vb.) ve yeni (S24 Ultra) tüm telefonlarda esneyebilen (fluid) bir yapı kullanılmalıdır (`flex`, `grid`, `width: 100%`). Yana kayma (overflow-x) asla olmamalıdır.

### A.8 init.php Hata Durumu
- Sunucu yanıt vermezse Vue, `localStorage`'daki son geçerli tema verisini kullanır ve uygulamayı açar. Arka planda yeniden dener.

---

## 🚀 FAZ 1 — MVP (Uygulamayı Yayına Almak İçin Zorunlu)

> Bu faz bitmeden kullanıcıya uygulama sunulamaz.

### [ ] F1.1 — Backend: init.php API
API yanıtı şu verileri içermeli:
- `theme` (real / alex / pep)
- `app_name`, `logo_url`, `favicon_url`
- `primary_color`, `accent_color`, `bg_color`
- `maintenance: true/false`
- `min_required_version`
- `web_version`
- `social` → `{ telegram, whatsapp, instagram }` (boşsa null)
- `forced_login: true/false`
- `announcement_modal` → `{ text, show_on_login }` (yoksa null)
- `guest_tips_limit` (geçmiş gösterim limiti, gün olarak)
- `custom_scripts` (head'e enjekte edilecek analytics kodu)

### [ ] F1.2 — App Loader (Açılış Ekranı)
Uygulama açılışının ilk görüntüsü. init.php sonucu gelene kadar kullanıcıyı bekletir.

**Animasyon Akışı:**
1. Tam siyah ekran (`#000`)
2. Ortada uygulama logosu → `fade-in` (0.4s)
3. Logonun altında marka rengiyle (`--color-primary`) animasyonlu gösterge → **Tema CSS dosyasında tanımlanır** (real.css → pulse bar, alex.css → progress bar, pep.css → spinning ring vs.)
4. `init.php` yanıtı gelince → tüm ekran yukarı kayar (`slide-up` 0.3s) → uygulama açılır

**Edge Case'ler:**
- `init.php` geç yanıt verirse: Loader devam eder, kullanıcı bekler.
- `init.php` tamamen başarısız olursa: `localStorage`'dan son geçerli tema kullanılır, loader kaybolur.
- Hiç tema yoksa (ilk yükleme + hata): *"Connection failed. Please check your internet."* ekranı gösterilir.

### [ ] F1.3 — Guest Landing Page (Misafir Karşılama Sayfası)
Uygulamanın vitrini. Kullanıcıyı "Sign Up" yapmaya ikna eden sayfa.

**İçerik Düzeni (yukarıdan aşağıya):**
1. Logo + App Adı (ortada, şık)
2. **Registration Guide** — 3 adım kartı (Admin'den özelleştirilebilir metin):
   - *"Step 1 — Purchase on Google Play"*
   - *"Step 2 — Register with your Order Code"*
   - *"Step 3 — Get Approved & Access Tips"*
3. Sosyal medya ikonları → `init.php`'den gelen `social` alanında URL varsa gösterilir, yoksa hiç çıkmaz.
4. **"Sign Up"** butonu → gradient, büyük, tam genişlik.
5. **"Sign In"** butonu → outline/transparent, daha küçük.

**Davranış Kuralları:**
- `forced_login: true` → misafir hiçbir şey göremez, bu sayfa tam ekran çıkar.
- `forced_login: false` → anasayfa görünür, Sign Up/Sign In butonları altta sticky bar olarak durur.
- **Welcome Modal:** `announcement_modal` dizisinde `show_on_login: true` olan ve `localStorage`'da `seen_modal_{id}` kaydı olmayan duyurular sayfa yüklenince sırayla gösterilir. Kullanıcı kapatınca `seen_modal_{id}: true` kaydedilir, bir daha çıkmaz.

### [ ] F1.4 — Kayıt (Sign Up) Formu

**Form Alanları:**

| Alan | Zorunlu | Kural | Hata Mesajı |
|---|---|---|---|
| Full Name | ✅ | Min 2 karakter | *"Please enter your full name."* |
| Email | ✅ | Format + onBlur duplicate check | *"This email is already registered. [Sign in instead?]"* |
| Password | ✅ | Min 8 karakter + göster/gizle toggle | *"Password must be at least 8 characters."* |
| GPA Code | ✅ | onBlur duplicate check | *"An account with this order already exists. [Reset your password?]"* |
| How did you hear? | ❌ İsteğe bağlı | Telegram / Google Play / Other Apps / Friend / Other | — |

**GPA Alanı Detayları:**
- Alan altında ⚠️ *"Entering an incorrect GPA code may delay your approval."* uyarısı.
- Hemen altında `[Where to find your order code?]` → tıklayınca accordion açılır, adım adım Google Play sipariş kodu bulma rehberi gösterilir.

**Başarılı Kayıt Akışı:** Token alınır → otomatik login → F1.5 Welcome ekranına geçilir.

### [ ] F1.5 — Post-Registration Welcome Ekranı
Kayıt sonrası kullanıcıya ne yapacağını söyleyen geçiş ekranı.

**İçerik Düzeni:**
1. Büyük ✅ ikonu veya uygulama logosu
2. *"Registration Successful!"* başlığı
3. *"Your account is under review. Our team will approve your membership shortly."* (Admin'den özelleştirilebilir)
4. **İletişim Kanalları** (`init.php` → `contact` alanından gelir):
   - Telegram admin linki
   - E-posta linki
   - WhatsApp (tanımlıysa)
   - Alt metin: *"Reach out for faster approval!"*
5. **"Continue"** butonu → Anasayfaya geçer

**Davranış Kuralları:**
- Geri tuşuyla bu ekrana dönülemez (`history.pushState` ile kayıt formu geçmişten temizlenir).
- İletişim kanalları `contact` alanında `null` değilse görünür, `null` olanlar hiç çıkmaz.

### [ ] F1.6 — Giriş (Sign In) Formu

**Form Alanları:** Email + Password (göster/gizle toggle)

**Ekran Düzeni (yukarıdan aşağıya):**
1. Email alanı
2. Password alanı
3. **"Sign In"** butonu → gradient
4. *"Forgot your password?"* → link
5. *"Don't have an account? Sign Up"* → yönlendirme

**Brute-Force Kuralı:**
- 5 ardışık hatalı giriş → geçici kilit
- Ekranda: *"Too many failed attempts. Please wait X minutes."*
- **"Forgot Password?"** linki belirginleşir
- Süre dolunca form otomatik tekrar aktif olur

**Başarılı Giriş Sonrası Yönlendirme:**

| Kullanıcı Durumu | Sonuç |
|---|---|
| `approved` | Anasayfaya geçer |
| `pending` | Anasayfaya geçer (içerik kilitli, Heartbeat başlar) |
| `rejected` | Anasayfaya geçer (Rejected State ekranı) |
| `banned` | Anasayfaya geçer (BannedState ekranı) |

> Banlı/Rejected kullanıcı login'de engellenmez. Giriş yapar, durumunu görür ve destek kanallarına ulaşabilir.

### [x] F1.7 — Şifremi Unuttum (Forgot Password)
4 adımlı akış, kullanıcı uygulamadan hiç çıkmaz.

**Adım 1 — Email Gir:**
- Email alanı + *"Send Code"* butonu
- Backend 6 haneli kod maili atar (Rate limit: maks. 3 istek/saat)
- Email kayıtlı değilse bile → *"If this email exists, a code will be sent."* (Güvenlik: kayıtlı olup olmadığı belli edilmez)

**Adım 2 — Kodu Gir:**
- *"We sent a 6-digit code to [e\*\*\*@mail.com]"*
- 6 haneli kod inputu (otomatik odak, rakam klavyesi)
- Süre göstergesi: *"Code expires in 4:32"*
- *"Didn't receive it? Resend"* → sadece süre bittikten sonra aktif olur
- 3 yanlış deneme → kod iptal → *"Code invalidated. Request a new one."*

**Adım 3 — Yeni Şifre Belirle:**
- New Password (min 8 karakter) + Confirm Password (eşleşme kontrolü)
- *"Reset Password"* butonu

**Adım 4 — Tamamlandı:**
- ✅ *"Password updated successfully!"*
- Otomatik login → Anasayfaya yönlendirilir.

### [x] F1.12 — Session Resume (Token Verify) & Heartbeat
Kullanıcıya görünmez. Her uygulama açılışında arka planda çalışır.

**Akış:**
```
localStorage'da token var mı?
  ├── HAYIR → Guest Landing'e git
  └── EVET  → auth/verify isteği at (init.php ile paralel)
                  ├── 200 OK           → direkt Anasayfaya
                  ├── 401 (süresi dolmuş) → e-posta otomatik dolu, şifre iste
                  └── 503 (sunucu hatası) → cached tema ile aç, "Connecting..." toast
```

**Sliding Expiry:** Her başarılı `auth/verify`'da token yenilenir. 10 gün açılmamışsa expire olur; e-posta alanı otomatik dolu gelir, sadece şifre girilir.

### [x] F1.9 — Anasayfa (Home) — MVP İçeriği
Kullanıcı durumuna göre 3 farklı hal alır.

**Modül Sırası (yukarıdan aşağıya):**

| Modül | Approved | Pending/Rejected | Banned |
|---|---|---|---|
| **Announcements** | ✅ Açık | ✅ Açık | ✅ Açık |
| **Countdown** | ✅ Açık | ✅ Açık | ✅ Açık |
| **Teaser** | ✅ Açık | 🔒 LockedState | 🚫 BannedState |
| **Top 5 Win** | ✅ Açık | 🔒 LockedState | 🚫 BannedState |

**Modül Detayları:**

- **Announcements:** Son **5** duyuru kart olarak listelenir. Duyuru yoksa *"No new announcements."*
- **Countdown:** *"Next Match In: 02:34:17"* — tüm maçlar başladıysa *"Today's matches are in progress."* Bir sonraki bekleyen maç varsa ona geçer.
- **Teaser (Günün Bankosu):** O gün yayınlanan **başarı oranı en yüksek** maç otomatik seçilir. Takım adları ve saat görünür. **Tahmin ve oran blur'lanır.** *"Expert pick — Tap to see prediction"* etiketi.
- **Top 5 Win:** Son kazanan 5 maç mini kart olarak listelenir.

**LockedState (Pending/Rejected):** Sarı/amber kilit ikonu + *"Your membership is pending approval."* + *"Need faster approval? Contact us →"* (contact kanalları)

**BannedState (Banned):** Kırmızı kalkan ikonu + admin'in yazdığı ban sebebi + *"Contact Support"* butonu.

### [x] F1.10 — Tahminler (Tips) Sayfası — MVP İçeriği
Uygulamanın ana ekranı, maç listesi.

**Bileşenler ve Kurallar:**
- **DateSlider:** Yatay kaydırılabilir tarih hapları.
  - Sadece **geçmiş günler ve Bugün** gösterilir. Gelecek tarihler **gösterilmez**.
  - `past_days_limit` ayarına göre geriye gidilebilir.
  - Gün dönümü hesaplamaları tamamen kullanıcının **yerel saatine** göre yapılır.
- **MatchCard (Compact & Grid UI):** Lig ikonu/adı, takım logoları/isimleri, yerel saat, tahmin (Prediction), oran (Odds).
  - Yüksek yoğunluklu (High-Density) veri gösterimi için "Compact" tasarıma geçildi. Bir ekranda 7-8 maç görüntülenebilir.
  - **Kırık Görsel Önlemi:** Logosu eksik veya linki kırık takımlar/ligler için `@error` event'i üzerinden şeffaf, yer tutucu (placeholder) SVG/GIF fallback mekanizması kuruludur.
  - **Kusursuz Ortalama (CSS Grid):** Alt kısımdaki "Başarı %, Tahmin Çipi ve Oran" (stats) barı, sol ve sağdaki değerlerin asimetrik genişliklerinden etkilenmemesi için **CSS Grid (`1fr minmax(0, auto) 1fr`)** hilesiyle dizayn edilmiştir. Bu sayede tahmin çipi ekranın her zaman *kusursuz geometrik merkezinde* kalır.
  - **Başarı Oranı:** Bot tarafından çekilen, maça özel dinamik başarı oranı (Örn: *85%*). Yoksa `-` gösterilir.
  - **4 Görsel Durum:**
    - `Pending` (Nötr): Henüz sonuçlanmadı.
    - `Won` (Yeşil/Neon): Kazandı. Etrafında hafif `--color-glow` efekti.
    - `Lost` (Kırmızı/Mat): Kaybetti. Daha soluk renkler.
    - `Postponed/Canceled` (Gri): Ertelendi/İptal. Tahmin ve Oran üzeri çizik.
- **EmptyState:** O güne ait maç yoksa gösterilir. Merkezi ikon + Admin'den özelleştirilebilir metin.
- **Locked/Banned State:** Onaysız ve yasaklı kullanıcılar için tam ekran kilit/kalkan çıkar, maçları göremezler (Bkz F1.9).
- **WakeLock:** Sayfaya girildiğinde ekran kararmasını engellemek için JS Bridge üzerinden `Android.setWakeLock(true)` çağrılır. Çıkınca `false` yapılır.
- **API Cache:** Seçilen günün verisi bellekte 5-10 dakika tutulur, sekmeler arası geçişte tekrar yüklenmez (Pull-to-refresh yoktur).

### [x] F1.11 — Profil (Profile) Sayfası — MVP İçeriği
Kullanıcının hesabı yönettiği, iletişim kurduğu ve çıkış yaptığı sayfa. (Sadece Dark Mode)

**İçerik Sırası ve Kurallar:**
1. **UserProfileHeader (Profil Başlığı):**
   - İsmin baş harflerinden oluşan marka renginde avatar, Tam Ad ve E-posta.
   - **Durum Rozeti:** Approved (Yeşil) / Pending (Sarı) / Banned (Kırmızı).
   - **Gamification Rozetleri (Badges):** Kullanıcıyı motive edecek premium etiketler. Duruma veya admin'e göre atanabilir. Örnek rozetler:
     - 👑 *VIP Member* (Tüm onaylılarda standart)
     - 🛡️ *Verified Account* 
     - 🔥 *Early Supporter*
     - 💎 *Elite Status*

2. **Hesap İşlemleri (Account Actions):**
   - **Change Password:** Eski şifre + yeni şifre ile güncellenir. [x]
   - **Delete Account:** Google Play zorunluluğu. Tıklanınca emin misin uyarısı çıkar, onaylanırsa soft-delete yapılır, oturum kapatılır (30 gün sonra kalıcı silme). [x]
   - *KRİTİK GÜVENLİK:* Uygulamanın incelenmesi için verilen `exempt_security` test hesaplarında bu bölüm **TAMAMEN GİZLENİR.** (Google Play yetkilisi şifreyi/hesabı silemesin diye). [x]

3. **Destek ve Yasal (Support & Legal):**
   - [x] **Contact Us:** `init.php` içindeki `contact` objesinden gelen iletişim kanalları (Email, Telegram Admin).
   - [x] **Yasal Metinler (Privacy & Terms):** Harici siteye yönlendirmez. Tıklandığında sayfa içinde yukarı doğru kayarak açılan bir **Modal (Popup) / Bottom Sheet** içinde okunur.

4. **Çıkış ve Versiyon:**
   - **Sign Out:** `localStorage` token'ını siler ve Guest Landing ekranına yönlendirir.
   - **App Version:** En altta silik renkte (örn: `v1.0.4`).

### [x] F1.12 — Güvenlik Katmanı
- `user-select: none` + zoom engeli + sağ tık engeli (CSS).
- `Android.setSecureFlag(true)` → normal kullanıcı. `false` → `exempt_security` yetkili hesap.
- **Multi-Device Kick:** Her login'de `session_token` yenilenir. Eski token bir sonraki API isteğinde `401` alır → otomatik logout.
- **Aktif Ban Tespiti:** Her API isteğinde backend `is_banned` kontrolü yapar. Ban varsa `403` döner → frontend logout + BannedState.

### [ ] F1.13 — ForceUpdate & Maintenance Ekranları
- `min_required_version` cihaz versiyonundan büyükse → kilitli Play Store ekranı.
- `maintenance: true` ise → alt menü kaybolur, *"We're upgrading our servers."* ekranı çıkar.

---

## 🔧 FAZ 2 — POST-MVP (Yayın Sonrası Eklenecekler)

> Uygulama yayında, kullanıcılar var. Bu özellikler eklendikçe işaretlenir.

### [x] F2.1 — VIP Hub (Çapraz Promosyon) Sayfası (UI iyileştirmeleri tamamlandı)
Kullanıcılara diğer uygulamalarımızı çapraz tanıtım (cross-promotion) ile sunduğumuz sayfa.

- **CrossPromoCard:** Diğer VIP uygulamaların logosu, adı ve "Download on Google Play" butonu. (Fiyat gösterilmez, Play Store'a bırakılır).
- **Filtreleme (Backend):** Kullanıcının bulunduğu mevcut uygulama API tarafından filtrelenir ve listede gösterilmez. 
- Butona tıklanınca JS Bridge ile `Android.openUrl(play_store_link)` tetiklenir.

### [ ] F2.2 — Profil Ek Modüller
- **GPA Güncelleme (Update Order Code):**
  - Profil sayfasında sadece `Pending` veya `Rejected` statüsündeki kullanıcılara *"Update Order Code"* butonu görünür.
  - Tıklanınca açılan Modal'dan yeni GPA kodu girilir.
  - Kod gönderilince kullanıcının statüsü otomatik olarak **`Pending`** (Bekleme) moduna geçer.
  - ACMS Admin paneline "Kullanıcı GPA güncelledi" bildirimi düşer.

### [ ] F2.3 — Bildirim Modülleri
- **Notification Inbox (Zil Modülü):**
  - AppHeader'daki zile tıklandığında, zilin hemen altından (sağ üst köşeden) açılan şık bir panel (Dropdown/Popup) olarak tasarlanır.
  - Okunmamış bildirim varsa zilin üzerinde kırmızı nokta (badge) görünür.
  - Backend/OneSignal üzerinden geçmiş duyurular listelenir. Bildirim yoksa *"No notifications yet."* EmptyState gösterilir.
- **Toast Notification:**
  - Başarı, hata veya bilgi mesajları için (Örn: "Şifre güncellendi") ekranın uygun yerinden zarifçe kayarak gelip 3 saniye sonra kaybolan baloncuklar.

### [x] F2.4 — Support & Legal (Modal veya Sayfa)sı
Profil sayfasından erişilen ana destek merkezi.
- **Sıkça Sorulan Sorular (FAQ):** Admin panelinden eklenen sorular accordion (tıklanabilir liste) şeklinde görünür.
- **Canlı Destek (Tawk.to):** Tawk.to widget'ı uygulamanın her yerinde çıkmaz; sadece bu sayfada belirgin bir *"Chat with us live"* butonu olarak yer alır.
- **Dahili Ticket Sistemi:**
  - Kullanıcı kategori (Örn: Ödeme, Teknik) seçerek yeni destek talebi (Ticket) oluşturabilir. ACMS admin paneline düşer.
  - **Kritik Limit:** Aynı anda maksimum **2 adet Açık/Bekleyen (Pending)** destek talebi olabilir. Kullanıcı 2'den fazla talep açmak isterse *Toast Notification* ile hata alır.
  - **Kritik:** Kullanıcı **geçmiş ve bekleyen destek taleplerini** (açık/kapalı statüleriyle) bu ekranda listeleyebilir.

### [x] F2.5 — Promosyon Modülü (Cross-Sale)
ACMS üzerinden aktif edilen kampanyaların gösterimi.
- **PromoBanner:** Anasayfada (alt menünün üstünde) yüzen çubuk (floating banner) olarak çıkar. Tıklanınca harici URL'e değil, direkt **VIP Hub** sayfasına yönlendirir.
- **Kapatma Kuralı:** Kullanıcı "X" tuşu ile kapatırsa, `localStorage`'a kaydedilir ve kampanya devam ediyorsa **2 gün sonra tekrar** görünür.
- **VIP Hub Buton Senkronizasyonu:** Promosyon aktifse, VIP Hub sayfasındaki tüm uygulamaların altındaki "Download" butonlarının metni, **aktif promosyon metniyle (Örn: "%50 OFF")** otomatik olarak değişir.

### [x] F2.6 — RateUs Modal (Ödüllü Oylama)
Kullanıcıdan mağazada 5 yıldız vermesini teşvik eden ve ödül vaat eden modal.
- **Tetikleyici:** Kullanıcının **onay tarihinden (approval_date)** itibaren **5 gün** geçmişse çıkar.
- **İçerik:** İçerik ve vaat metinleri tamamen Admin panelindeki "Uygulama Ayarları"ndan özelleştirilebilir.
- **Aksiyon:** Butona basıldığında `Android.showInAppReview()` tetiklenir (Play Store Native Oylama).
- **Hatırlatma (Loop):** Kullanıcı pencereyi kapatırsa, **her 5 günde bir** tekrar karşısına çıkar.
- **KRİTİK KURAL:** Google Play politikası gereği, `exempt_security` (Google inceleme vs.) hesaplarına **ASLA GÖSTERİLMEZ.**

### [ ] F2.7 — Onboarding Ekranları
Uygulamayı ilk kez indiren birine değer önerisini sunan tanıtım ekranları.
- **Sadece İlk Giriş:** `localStorage`'da `seen_onboarding` yoksa (Guest Landing'den bile önce) 1 kez çıkar.
- **Tasarım:** Tema dosyalarına uygun statik vektörler kullanılır. Bu SVG/animasyonlar CSS değişkenlerini okuyarak `var(--color-primary)` uygulamanın kendi rengine **otomatik boyanır.**
- **Akış:** Sağa/sola kaydırmalı 3 ekran. Son ekrandaki *"Get Started"* butonuna basılınca `seen_onboarding` kaydedilir ve F1.3 (Guest Landing) sayfasına yönlendirilir.

---

## 📧 E-POSTA TETİKLEYİCİLERİ (Email Triggers)

*(Tüm şablonlar Admin panelden uygulamaya özel düzenleerbilir)*

| # | Tetikleyici | Zaman | İçerik |
|---|---|---|---|
| 1 | Kayıt Onayı | Kayıt sonrası | "Welcome! Your account is under review." |
| 2 | Üyelik Onaylandı | Admin onaylayınca | "Your VIP membership is now active!" |
| 3 | Üyelik Reddedildi | Admin reddedince | "Not approved." + admin'in red sebebi |
| 4 | Hesap Yasaklandı | Admin banlayınca | Ban sebebi + destek iletişim bilgisi |
| 5 | Yasak Kaldırıldı | Admin banı kaldırınca | "Your account has been reinstated." |
| 6 | Şifre Sıfırlama | Kullanıcı talep edince | 6 haneli kod (5 dakika geçerli) |

---

## 🔌 FAZ 3 — BACKEND API ENTEGRASYONU (Vue SPA → Canlı Veri)

> Bu faz başlamadan önce Faz 1 ve Faz 2'nin UI katmanı tamamlanmış olmalıdır.
> Her API ucu bağlandıkça `[x]` ile işaretlenir.

### [ ] F3.1 — init.php (Canlı Veri Bağlantısı)
**Dosya:** `api/app/init.php`

Şu an sahte (mock) JSON döndürüyor. Gerçek veritabanına bağlanacak.

**Yapılacaklar:**
- `apps` tablosundan `slug` veya `app_id` parametresiyle ilgili uygulama kaydı çekilecek.
- Dönen JSON yapısı aşağıdaki alanları içermeli:
```json
{
  "status": "success",
  "data": {
    "app_name": "...",
    "theme": "real",
    "logo_url": "...",
    "primary_color": "#...",
    "secondary_color": "#...",
    "accent_color": "#...",
    "bg_color": "#...",
    "maintenance": false,
    "maintenance_message": "...",
    "min_required_version": "1.0.0",
    "web_version": "1.2.0",
    "forced_login": false,
    "guest_tips_limit": 3,
    "play_store_link": "...",
    "social": { "telegram": "...", "whatsapp": "...", "instagram": "..." },
    "contact": { "email": "...", "telegram": "..." },
    "tawk_to_id": "...",
    "announcement_modal": { "active": false, "text": "..." },
    "guide_steps": ["...", "...", "..."],
    "post_register_text": "...",
    "empty_state_text": "...",
    "rate_us_text": "...",
    "rate_us_reward": "...",
    "vip_hub_apps": [...],
    "font_family": "Inter",
    "custom_scripts": "..."
  }
}
```
**Kural:** `maintenance: true` ise diğer tüm alanlar yine dönmeli — Vue kararı verecek.

---

### [x] F3.8 — Auth API (Kayıt, Giriş, Doğrulama) Tamamlandı
**Dosya:** `api/app/auth/`

#### F3.2a — register.php
- POST: `{ name, email, password, gpa_code, app_id }`
- `email` unique kontrolü → hata döner.
- Şifre `password_hash()` ile bcrypt'e çevrilerek kaydedilir.
- Kayıt sonrası `status: 'pending'` atanır.
- Başarı → 201, `{ status: "success", message: "Account created." }`

#### F3.2b — login.php
- POST: `{ email, password, app_id }`
- `password_verify()` ile doğrulama.
- Başarılı giriş → yeni `session_token` (UUID) üretilip `users` tablosuna yazılır.
- `is_banned` kontrolü → `403 { status: "banned", ban_reason: "..." }`
- Başarı → `{ status: "success", token: "...", user: { id, name, email, status, badges: [...] } }`
- **Brute-Force Koruması:** 5 yanlış denemede 30 dk. kilitlenme (`failed_login_attempts`, `lockout_time`).

#### F3.2c — verify.php (Session Resume)
- POST veya GET: `{ token, app_id }`
- Token geçerliyse kullanıcı verisini döner + token'ı yeniler (Sliding Expiry).
- Token 10 gün boyunca kullanılmadıysa `401` döner.
- Her başarılı verify'de `failed_login_attempts` sıfırlanır.

#### F3.2d — logout.php
- POST: `{ token }`
- `session_token` alanını NULL yapar.

#### [x] F3.2e — forgot_password.php / reset_password.php
- 6 haneli OTP kodu ile 2 aşamalı şifre sıfırlama.
- OTP `password_resets` tablosunda 5 dakika geçerli olarak tutulur.
- 3 yanlış denemede kod iptal edilir.
- **⚠️ Veritabanı Notu:** `password_resets` tablosu şu an sistemde **yoktur**, API bağlama aşamasında oluşturulmalıdır. Kolonlar: `id`, `email` (VARCHAR 255), `code` (VARCHAR 10), `expires_at` (DATETIME), `attempts` (INT, def: 0).

---

### [x] F3.3 — Matches (Tips) API
**Dosya:** `api/app/matches.php`

- GET: `{ token, app_id, date (YYYY-MM-DD) }`
- Sadece o uygulamaya (`app_id`) ait ve seçilen güne ait maçları döner.
- Her maç kaydı: `{ id, home_team, away_team, home_logo, away_logo, league, match_time, prediction, odds, confidence_rate, status, score }`
- **Güvenlik:** `status: pending` kullanıcılar için `prediction` ve `odds` alanları null döner (backend blur eder, frontend değil!).
- **Misafir Limiti:** Token yoksa `guest_tips_limit` günden öncesine ait veriler döndürülmez.
- **5-10 Dk. Cache:** Vue tarafında seçili günün verisi `vuex state` veya `reactive()` ile bellekte tutulur; sekme değişiminde tekrar `fetch` yapılmaz.

---

### [x] F3.4 — Profile & Account API
**Dosya:** `api/app/profile.php`

- **GET:** Token ile kullanıcının kendi verisini çeker. `{ name, email, status, gamification_badges: ["VIP Member", "Veteran"], approval_date, ... }`
- **POST (Change Password):** `{ token, old_password, new_password }` → bcrypt verify + update.
- **POST (Update GPA Code):** `{ token, gpa_code }` → `status` → `pending`'e çeker.
- **DELETE (Delete Account):** `{ token, password }` → şifre doğrula → `is_deleted: 1`, 30 gün sonra kalıcı silme. `exempt_security` hesaplarda bu endpoint `403` döner.

---

### [x] F3.5 — Support & Tickets API
**Dosya:** `api/app/support.php`

- **GET FAQs:** `{ app_id }` → `faqs` tablosundan `status: active` olanları döner.
- **GET Tickets:** `{ token }` → Kullanıcının kendi destek taleplerini döner (tüm statüler).
- **POST (Create Ticket):** `{ token, category, subject, message }`
  - Kullanıcının mevcut `pending` ticket sayısı kontrol edilir. **2 veya daha fazlaysa** `{ status: "error", message: "Max 2 open tickets allowed." }` döner.
  - Başarıda ticket `tickets` tablosuna eklenir.

---

### [x] F3.6 — Promotions API
**Dosya:** `api/app/promotions.php`

- **GET:** `{ app_id }` → `promotions` tablosundan `status: active` ve `end_date >= NOW()` olan aktif promosyonu döner.
- Vue bu yanıta göre `PromoBanner`'ı ve VIP Hub buton metinlerini dinamik günceller.

---

### [x] F3.7 — VIP Hub API
**Dosya:** `api/app/vip_hub.php`

- **GET:** `{ app_id }` → Mevcut uygulamanın markasına ait diğer tüm aktif uygulamaları (`apps` tablosu) döner. Kendi uygulaması listede yer almaz.
- Her kayıt: `{ name, logo_url, play_store_link, vip_hub_description }`

---

### [x] F3.8 — Heartbeat (Aktif Ban Kontrolü)
**Dosya:** `api/app/verify.php` (F3.2c ile aynı)

- Vue, kullanıcı uygulamayı açık tuttuğu sürece her **5 dakikada bir** (test için 10 sn) `verify.php`'yi çağırır.
- `status: 'banned'` döndüğünde Vue, Heartbeat döngüsünü sonlandırır ancak **oturum kapatılmaz (logout yapılmaz)**. Kullanıcı bulunduğu ekranda `BannedState`'i görmeye devam eder.
- `maintenance: true` döndüğünde Vue bakım ekranına geçer.

---

### F3 — Frontend Bağlama Kuralları (Vue app.js)

1. [x] **`fetch()` Wrapper:** Tüm API çağrıları merkezi bir `apiCall(endpoint, options)` fonksiyonu üzerinden yapılır. Bu fonksiyon `token`'ı otomatik header'a ekler ve `401` alınca logout tetikler.
2. **Loading State:** Her `apiCall` öncesi ilgili bileşende `isLoading: true` → Skeleton Loader gösterilir.
3. **Error Handling:** Her `apiCall` sonrasında hata varsa global `showToast()` ile kullanıcıya bildirilir. `console.error()` asla tek başına kullanılmaz.
4. **Token Saklama:** `localStorage` içinde `acms_token_{app_id}` anahtarıyla saklanır. Farklı uygulamalar birbirinin token'ını ezmez.
5. **`init.php` Önceliği:** Vue mount edildiğinde ilk iş `init.php` çağrısıdır. Bu yanıt gelmeden hiçbir sayfa render edilmez.

---

### F3 — Test Protokolü

Her API ucu bağlandığında şu adımlar uygulanır:

| Adım | Test | Beklenen Sonuç |
|---|---|---|
| 1 | Geçersiz token ile istek | `401` + Vue logout |
| 2 | Banlı kullanıcı ile login | `403` + ban mesajı |
| 3 | 5 yanlış şifre denemesi | `429` + 30 dk. kilit |
| 4 | Pending kullanıcı tips sayfası | `prediction: null` |
| 5 | 3. ticket açma denemesi | `error: max 2 tickets` |
| 6 | Maintenance true → tüm sayfalar | Bakım ekranı |
| 7 | exempt_security hesap | Hesap silme butonu gizli |

---

## 🎨 BÖLÜM B — TASARIM SİSTEMİ (Design System)

### B.1 Renk Token Sistemi (Color Tokens)
Admin panelde **4 renk** girer. Vue bu değerlerden tüm token'ları otomatik türetir:

| Admin Inputu | Token | Kullanım Yeri |
|---|---|---|
| Primary Color | `--color-primary` | Aktif nav, buton başlangıcı, link |
| Secondary Color | `--color-secondary` | Gradient bitiş, hover durumu |
| Accent Color | `--color-accent` | Rozetler, özel vurgular |
| Background Color | `--bg-base` | Sayfa zemini (her zaman koyu) |

**Otomatik türetilen token'lar** (admin dokunmaz, sistem hesaplar):
```css
--gradient-brand:  linear-gradient(135deg, primary, secondary)   /* Butonlar, aktif nav */
--gradient-card:   linear-gradient(135deg, primary@8%, secondary@3%)  /* Kart overlay */
--color-glow:      rgba(primary, 0.25)                           /* Box-shadow glow */
--bg-surface:      bg-base + %10 açık                           /* Kart zemini */
--bg-nav:          bg-base + %5 açık                            /* Alt menü zemini */
```

### B.2 Gradient Stratejisi — "Stratejik Gradient" Kuralı
Her yere gradient = ucuz görünüm. Doğru denge:

| Eleman | Stil | Açıklama |
|---|---|---|
| **Butonlar (CTA)** | ✅ Gradient | `--gradient-brand` |
| **Aktif Nav Göstergesi** | ✅ Gradient | Pill veya bar |
| **Won MatchCard Glow** | ✅ Gradient glow | `--color-glow` box-shadow |
| **Rozetler / Badges** | ✅ Gradient | Premium his |
| **Kart arka planı** | ⚡ Çok hafif overlay | `--gradient-card` (neredeyse görünmez) |
| **Sayfa arka planı** | ❌ DÜZ koyu | Gradient bg = eski his |
| **Metinler** | ❌ Düz | Okunabilirlik |
| **Input alanları** | ❌ Düz surface | Sadelik |

### B.3 Typography (Yazı Tipi)
Font seçimi tema kurulurken **agent tarafından sorulur** ve admin panelindeki tema ayarlarından değiştirilebilir. `init.php` yanıtında `font_family` alanı döner, Vue Google Fonts CDN'ini dinamik yükler.

**Seçilebilecek 10 Profesyonel Font:**
| # | Font | Karakter |
|---|---|---|
| 1 | **Inter** | Temiz, modern, okunabilir — en yaygın |
| 2 | **Outfit** | Geometrik, güçlü, genç |
| 3 | **Syne** | Cesur, farklı, dikkat çekici |
| 4 | **Nunito** | Yumuşak, arkadaşça |
| 5 | **Rajdhani** | Teknolojik, spor hissi |
| 6 | **Barlow** | Kompakt, profesyonel |
| 7 | **DM Sans** | Minimal, şık |
| 8 | **Poppins** | Yuvarlak, modern |
| 9 | **Space Grotesk** | Teknik, premium |
| 10 | **Oswald** | Güçlü, dikkat çekici başlıklar |

### B.4 Border Radius — Temaları Ayıran Gizli Silah
Köşe yuvarlaklığı renk kadar etkili diferansiyatör. Tema CSS dosyasında sabit tanımlanır, admin değiştiremez:
```css
/* real.css örneği — Yumuşak, premium */
--radius-card:           16px;
--radius-button:         12px;
--radius-nav-indicator:  20px;  /* Pill şekli */
--radius-input:          10px;

/* alex.css örneği — Keskin, agresif */
--radius-card:           6px;
--radius-button:         4px;
--radius-nav-indicator:  4px;   /* Köşeli bar */
--radius-input:          4px;
```

### B.5 App Loader Animasyonu
Uygulama açılışının ilk görüntüsü:
1. Tam siyah ekran
2. Ortada uygulama logosu (fade-in ile belirir)
3. Logonun altında marka rengiyle (`--color-primary`) nabız atan ince çizgi (pulse bar)
4. `init.php` yanıtı gelince tüm ekran yukarı kayarak açılır (`slide-up` transition)

### B.6 7 VIP Hazır Palette Presetleri
Admin tema kurarken renk teorisi bilmeden seçim yapabilsin diye Admin Paneline (`app_edit.php` & `app_add.php`) tek tıkla canlı önizlemeyi güncelleyen 7 VIP hazır preset butonu entegre edilmiştir:
| VIP Uygulama | Primary | Secondary | Accent | BG | Karakter & Atmosfer |
|---|---|---|---|---|---|
| **💎 Real VIP** | `#00D4FF` | `#0066CC` | `#FFCC00` | `#060D1A` | Modern teknolojik cyan & altın |
| **👑 Elite VIP** | `#A855F7` | `#6366F1` | `#F59E0B` | `#0B0814` | Lüks krallık moru & amber |
| **🔥 Fixed VIP** | `#FF3366` | `#990033` | `#FFB800` | `#0F0508` | Agresif neon kırmızı & alev |
| **⚡ Special VIP** | `#10B981` | `#047857` | `#FACC15` | `#040D08` | Kazanma odaklı zümrüt & neon sarı |
| **🚀 Over/Under VIP** | `#F97316` | `#C2410C` | `#38BDF8` | `#120904` | Enerjik siber turuncu & buz mavisi |
| **🌌 Daily Safe VIP** | `#3B82F6` | `#1D4ED8` | `#34D399` | `#050B14` | Güven veren safir mavisi & nane |
| **🏆 Master VIP** | `#F43F5E` | `#881337` | `#E2E8F0` | `#0C0A0E` | Prestijli yakut kırmızısı & platin |

### B.7 Durum Renkleri (Status Colors — Sabit, Override Edilemez)
| Durum | Renk | Hex |
|---|---|---|
| Pending / Locked | Sarı / Amber | `#f59e0b` |
| Approved / Won | Yeşil / Neon | `#22c55e` |
| Banned / Lost | Kırmızı | `#ef4444` |
| Postponed | Gri | `#6b7280` |
| Rejected | Turuncu | `#f97316` |

### B.8 Skeleton Loader Standartı
- API çağrısı olan HER modülde **Skeleton Loader** kullanılır. Spinner kullanılmaz.
- Shimmer animasyonu: `background: linear-gradient(90deg, #1a1a1a 25%, #2a2a2a 50%, #1a1a1a 75%)` + `animation: shimmer 1.5s infinite`.

### B.9 Native Animasyon Kuralları
- **Sayfa Geçişleri (Router Transitions):** Tüm sayfa geçişleri `<transition name="page" mode="out-in">` ile sarılarak GPU hızlandırmalı yumuşak geçişe (Fade-Slide) bağlanmıştır.
- **Çapraz Geçiş (Cross-fade):** Skeleton Loader'dan gerçek veriye geçişlerde anlık patlamaları önlemek için mutlaka `<transition name="fade" mode="out-in">` kullanılacaktır.
- **Haptic (Dokunmatik) Geri Bildirim:** Kartlar (.mc), butonlar ve slider elemanlarına basıldığında native uygulama hissi yaratmak için CSS :active durumunda `transform: scale(0.97)` uygulanmıştır.

### B.10 Logo Renk Çıkarma (Backlog — MVP Sonrası)
Admin logoyu yüklediğinde `color-thief.js` ile dominant renk analizi yapılır. Sistem 3 renk önerir (Dominant, Vibrant, Muted). Admin beğendiğini seçer. Uygulanmadıysa preset'e dönülür.

### B.11 Canlı Önizleme (Live Customizer), Tok Cam (Solid Glass) ve Dinamik Tipografi Mimarisi
- **Gerçek Zamanlı Köprü (postMessage):** Admin panel (`app_edit.php` / `app_add.php`) ile Vue 3 SPA arasındaki veri akışı `window.postMessage` API ile 0 ms gecikmeyle sağlanır.
- **Dinamik Font Yükleyici:** Formdan seçilen font (`font_family`) anında Google Fonts CDN linki oluşturularak DOM'a enjekte edilir ve `--font-family` CSS değişkeniyle tüm uygulamaya anında uygulanır.
- **Tok Cam (Solid Glassmorphism) Yüzeyler:**
  - Kartlar (`.glass-module`, `.announcement-card`, `.countdown-card`, `.locked-card`): `rgba(13, 20, 36, 0.82)` + `backdrop-filter: blur(25px)` ile aşırı saydamlık önlenmiş, kristal netliğinde kontrast sağlanmıştır.
  - Maç Kutuları (`.mc`): `rgba(18, 26, 44, 0.88)` + `backdrop-filter: blur(15px)` + metin gölgeleri (`text-shadow: 0 1px 2px rgba(0,0,0,0.5)`) ile takım isimleri (`.mc-tname`) ve oranlar kusursuz okunabilirliğe kavuşturulmuştur.
- **CSS Değişken Enjeksiyonu ve Bileşen Eşleşmeleri:**
  - `--color-primary`: Ana marka rengi, navbar aktif dot, form focus çerçeveleri ve buton başlangıç gradienti.
  - `--color-secondary`: İkincil renk; butonların ve aktif pillerin gradient bitişi (`--gradient-brand`), maç kartlarının bekleyen durum çizgisi (`.mc.match-pending`), cam kartların (`.glass-module`, `.announcement-card`) üst sınır parıltısı (`border-top: 1px solid var(--color-secondary)`).
  - `--color-accent`: Vurgu ve aksiyon rengi; Next Match geri sayım sayacı (`.countdown-time` + text-shadow neon ışıma), VIP rozetleri (`.vip-badge`, `.badge-accent`), kazanma yüzdeleri (`.mc-pct`) ve duyuru başlıkları (`.announcement-title`).
  - `--bg-base`: Ana arka plan ve derinlik; `body` üzerinde `radial-gradient(circle at 15% 0%, var(--color-primary) 0%, transparent 40%)` ve `radial-gradient(circle at 85% 100%, var(--color-secondary) 0%, transparent 45%)` ile dinamik neon derinlik atmosferi.
- **Mockup Çözünürlük ve Ölçek Standartı:**
  - Dış Kasa (`#phone-wrapper`): `340px × 680px`, 10px çerçeve, iç alan `320px × 660px`.
  - SPA Iframe (`#spa-preview-iframe`): `375px × 773px` gerçek mobil viewport render'ı.
  - Scale Oranı: `transform: scale(0.8533)` ile piksel taşması ve bulanıklık olmadan tam `320px × 660px` içerisine oturtulur.
- **Canlı Modüller ve Olay Köprüleri:**
  - **Rate Us:** Admin `#tab-rateus` sekmesine geçildiğinde `ACMS_TRIGGER_RATE_US` olayı fırlatılarak canlı önizlemede değerlendirme modalı anında tetiklenir.
  - **İletişim & Yasal:** Admin sekmeleri değiştikçe rota otomatik olarak `/app/support` veya `/app/profile`'a yönlendirilir; `contact`, `privacy_policy`, `terms_of_use` ve `about_us` alanları anında DOM'a yansıtılır.
  - **Bildirimler (Header Inbox):** `%97` opak cam yüzey (`rgba(13, 20, 36, 0.97)`) ile arkadaki öğelerin sızması engellenmiştir.

---

## 🔌 JS BRIDGE — NATIVE ENTEGRASYONLAR

| Köprü Metodu | Ne Zaman | Açıklama |
|---|---|---|
| `Android.setSecureFlag(bool)` | Giriş anı | SS koruması aç/kapat |
| `Android.vibrate()` | Her tıklama | Haptic feedback |
| `Android.showInAppReview()` | RateUs modalı | Google Play resmi oylama |
| `Android.openUrl(url)` | VIP Hub, Play Store | Harici URL |
| `history.pushState()` | Tüm geçişler | Geri tuşuna basılınca uygulama kapanmaz, önceki sekmeye döner |
| `Android.setWakeLock(bool)` | Tips sayfasına girilince | Ekran kararmasını engeller |

---

## 📦 BACKLOG (Sonraya Bırakılan)

- Onboarding intro ekranları (Faz 2.8'de ele alınacak).
- Free uygulama: GPA alanı gizleme dinamiği.
- Çok Dilli Destek (Google Translate entegrasyonu).
- GPA Auto-Verify: Google Play Developer API (~2-3 saatlik kurulum).


