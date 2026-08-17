# 2026 Dark Elite (Flagship VIP) — Modüler Tema Yol Haritası & Geliştirici Rehberi

> **Okuma & Uygulama Kuralı:** 
> Bu döküman adım adım, faz faz uygulanmak üzere hazırlanmıştır. 
> Mevcut `app.js` tek seferde değiştirilmemiş; tüm yeni bileşenler `app/themes/elite-dark-2026/` klasörü altında **bağımsız, modüler ve sıfır gecikmeli (0ms client-filtered)** olarak geliştirilmiştir.
> Her faz tamamlandıkça `[x]` ile işaretlenmiştir.

---

## 🎨 Genel Tasarım & Renk Paleti (Tokens)

* **Arka Plan:** `#0B0E14` (Derin Gece Katmanı) & `#121721` (Kart & Panel Katmanı)
* **Vurgu (Primary / Banko):** `#00E676` / `#10B981` (Canlı Zümrüt Yeşili - Bankolar, 1X2 & Kazanma Rozetleri)
* **İkincil (Accent / Aksiyon):** `#FF9100` / `#F59E0B` (Gün Batımı Turuncusu - İleri / Get Started / Seçili Filtreler)
* **Kart Kenarlıkları:** `1px solid rgba(255, 255, 255, 0.08)`
* **Tipografi:** Google Fonts `Inter`, `system-ui`
* **GPU Hızlandırma:** Tüm modal, çekmece ve sekme geçişleri `transform: translate3d()` ve `opacity` ile 60-120 FPS.

---

## 🗺️ Fazlar & Modüler Geliştirme Durumu

### 📦 FAZ 1 — Temel Dizin Yapısı & CSS Tasarım Sistemi (`theme.css`)
- [x] **1.1** `app/themes/elite-dark-2026/` dizini ve `components/` alt klasörü oluşturuldu.
- [x] **1.2** `app/themes/elite-dark-2026/theme.css` CSS tasarım sistemi oluşturuldu:
  - CSS değişkenleri (`--elite-bg-base`, `--elite-card-bg`, `--elite-emerald`, `--elite-amber`, `--elite-cyan`, `--elite-border`).
  - Cam efektleri (frosted glass), GPU animasyon sınıfları (`.elite-sheet-container`, `.tab-content-fade`).
  - Skeleton yükleyiciler ve dokunmatik geri bildirim (`.elite-tap:active { transform: scale(0.96); }`).

---

### 🌟 FAZ 2 — 5 Adımlı İnteraktif Karşılama (Onboarding) Modülü
- [x] **2.1** `app/themes/elite-dark-2026/components/OnboardingModal.js` oluşturuldu:
  - **Adım 1:** Dil Seçimi (İnteraktif bayraklı radyo kartları, 8 dil destekli).
  - **Adım 2:** Canlı Maç & Tahmin Kanıtı (Inter - Atalanta canlı gol animasyonu, 65' GOAL -> Prediction Won 🟢).
  - **Adım 3:** Bildirim Simülasyonu (Kilit ekranı push kartı: "Home win @ 1.72 - %86 Güven").
  - **Adım 4:** Uzmanlar Vitrini (Safe Steve, Balanced Ben, Risky Rick ROI ve seri kartları).
  - **Adım 5:** Sosyal Kanıt & Puanlama (4.8 Yıldız, 1M+ İndirme, %84.2 VIP Win Rate + "Get Started Now" butonu).
  - Altta interaktif noktalar (pagination dots), üstte "Skip/Geç" butonu.

---

### 🔝 FAZ 3 — Üst Bar, Sol Çekmece & Sağ Üst Modalları (Header, Drawer, Modals)
- [x] **3.1** `app/themes/elite-dark-2026/components/TopHeader.js`:
  - Sol Hamburger Butonu, Ortada Logo & Uygulama İsmi, Sağda 3 Hızlı İkon (🕒 Geçmiş, 🔔 Gelen Kutusu, ⚙️ Ayarlar).
- [x] **3.2** `app/themes/elite-dark-2026/components/LeftDrawer.js`:
  - Alttan açılan akıcı çekmece menü: VIP Members Club, 7 Günlük Bankroll Stratejisi, Nasıl Seçim Yapılır?, Puanla, Paylaş.
- [x] **3.3** `app/themes/elite-dark-2026/components/HistoricalArchive.js`:
  - Yatay tarih şeridi (`MON 11`, `TUE 12`, `WED 13`...), WON (Yeşil) / LOST (Kırmızı) istatistik barı ve oran geçmişi.
- [x] **3.4** `app/themes/elite-dark-2026/components/InboxModal.js`:
  - Bildirim geçmişi (Banko uyarıları, Kazanan Kupon bildirimleri, Sistem bültenleri).
- [x] **3.5** `app/themes/elite-dark-2026/components/SettingsModal.js`:
  - Oran Formatı Seçici (*Ondalık 1.75 / Kesirli 3/4 / Amerikan -133*), Dokunmatik Titreşim/Haptic ayarı, Ekranı Açık Tutma (Keep Awake), Bildirim anahtarı, Dil değiştirici, Sürüm bilgisi.

---

### ⚽ FAZ 4 — Günün Bankoları, Hızlı Filtreler & Lig/Maç Listesi (Home Feed)
- [x] **4.1** `app/themes/elite-dark-2026/components/DailyTickets.js`:
  - 3'lü Yatay Kayan Banko Kartları:
    * 🟢 **Safe (Güvenli):** 1.50 Oran | Geri sayım: `7h 44m` | 1-2 Maç
    * 🔵 **Medium (Orta):** 1.99 Oran | Geri sayım: `8h 27m` | 2 Maç
    * 🟣 **Risky (Sürpriz):** 3.74 Oran | Geri sayım: `7h 27m` | 3 Maç
- [x] **4.2** `app/themes/elite-dark-2026/components/MatchFilters.js`:
  - Canlı sayaç şeridi: `● 3.4k Online` | `📈 %84.6 Win Rate` | `67 Predictions`.
  - Takım / Lig Arama Kutusu.
  - Hızlı Filtre Hapları: `All Predictions`, `🛡️ SAFE PICKS`, `⚽ GOALS & BTTS`, `Upcoming`, `Today`, `Tomorrow`, `+1h`, `+3h`.
- [x] **4.3** `app/themes/elite-dark-2026/components/MatchList.js`:
  - Lig gruplama akordiyonu (Ülke bayrağı + Lig adı + Favori yıldızı).
  - Maç satır kartı: Başlama saati, Ev Sahibi - Deplasman takımları & logoları, Tahmin rozeti (`1X2 - 1X`, `HANDICAP - Home -1.5`, `BTTS - YES`, `Over 2.5`), Güven yüzdesi (%88), Oran (@ 1.29).

---

### 📊 FAZ 5 — Maç Detayı & Derin İstatistik Modalı (3 Sekmeli Analiz)
- [x] **5.1** `app/themes/elite-dark-2026/components/MatchDetailModal.js`:
  - **Sekme 1 — ÖZET (SUMMARY):**
    * Maç Sonucu (1 - X - 2) oran kutuları.
    * Çifte Şans (1X, 12, X2) + Recommended (Önerilen) rozeti.
    * Alternatif Marketler Tablosu (Over 1.5, Over 2.5, BTTS, Home Over 1.5 ve olasılık yüzdeleri).
    * Takım Güç Derecelendirmesi (*Team Strength 0-5*) çift renkli bar grafik + AI Analiz Değerlendirmesi.
  - **Sekme 2 — İSTATİSTİK (STATS):**
    * Son 10 maç karşılaştırma çubukları (Atılan Goller, Galibiyetler, Gol Yememe, Ortalama Gol) Home (Cyan) vs Away (Amber).
  - **Sekme 3 — H2H (Aralarındaki Maçlar & Form):**
    * H2H Çember istatistiği (Ev Galibiyeti 4 / Beraberlik 3 / Deplasman Galibiyeti 1).
    * Son 5 maç form kılavuzu (`[W][W][D][W][L]` renkli rozetler).

---

### 🧭 FAZ 6 — Alt Navigasyon & Ek Sayfalar (Experts, Strategy, AI Chat)
- [x] **6.1** `app/themes/elite-dark-2026/components/BottomNav.js`:
  - 5 Sekmeli Modern Tab Bar (🏠 Home, 👑 VIP Picks, 👥 Experts, 💡 Strategy, 🤖 AI Tips).
- [x] **6.2** `app/themes/elite-dark-2026/components/ExpertsView.js`:
  - Sıralı tipster kartları: Safe Steve (%88.4 Win Rate), Balanced Ben (%79.2 Win Rate), Risky Rick (%64.8 Win Rate, 92.4% ROI).
- [x] **6.3** `app/themes/elite-dark-2026/components/StrategyView.js`:
  - Fractional Kelly & Flat Staking Kasa hesaplayıcı + 7 Günlük Kasa Yönetimi Dersleri.
- [x] **6.4** `app/themes/elite-dark-2026/components/AiChatView.js`:
  - "Genius AI" maç analiz ve değer bahisleri chatbot arayüzü + Hızlı soru balonları.

---

### 🔗 FAZ 7 — Ana Entegratör, Önizleme Köprüsü & Çoklu Dil (i18n) Doğrulaması
- [x] **7.1** `app/themes/elite-dark-2026/elite_dark_2026.js` ana toplayıcı dosyasında tüm bileşenler birleştirildi.
- [x] **7.2** `app/index.html` içerisine modüler bileşen scriptleri eklendi.
- [x] **7.3** `app/app.js` içerisine `/elite-demo` önizleme rotası ve tema yönlendirmesi eklendi (eski kodlara sıfır müdahale).
- [x] **7.4** Çoklu dil anahtarları `scratch/generate_all_locales.js` içerisine eklendi ve 8 dilde derlendi.
- [x] **7.5** `scratch/validate_full_system.js` ile %100 anahtar eşleşmesi ve JS sözdizimi doğrulandı.
