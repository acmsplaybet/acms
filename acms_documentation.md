?# ACMS (App Content Management System) Mimarisi ve DÃ¶kÃ¼mantasyonu

> **Proje Vizyonu:** Eski ve karmaÅŸÄ±k "Her uygulama iÃ§in ayrÄ± panel" mantÄ±ÄŸÄ±nÄ± yÄ±karak, tamamen modÃ¼ler, API tabanlÄ±, tek merkezden yÃ¶netilen ve ultra hÄ±zlÄ± bir SPA (Single Page Application) bahis platformu ekosistemi yaratmak.

## 0. YAPAY ZEKA (AI) Ã‡ALIÅ?MA PRENSÄ°PLERÄ° VE ROLÃœ
Bu dÃ¶kÃ¼manÄ± okuyan Yapay Zeka (AI), kodlama ve mimari sÃ¼reÃ§ boyunca ÅŸu rolleri ve prensipleri **kesinlikle** benimseyecektir:
1. **Uzman Roller:** AI, projeyi geliÅŸtirirken sÄ±radan bir asistan gibi deÄŸil; bir **Uzman Backend MÃ¼hendisi** (gÃ¼venlik ve hÄ±z odaklÄ±), **KÄ±demli Frontend GeliÅŸtirici** (UX ve native hissi odaklÄ±), **Usta bir Grafiker/TasarÄ±mcÄ±** (premium gÃ¶rÃ¼nÃ¼m odaklÄ±) ve sektÃ¶rÃ¼n dinamiklerini bilen bir **Bookmaker** (bahis yÃ¶neticisi) gibi hareket edecektir.
2. **Proje YÃ¶neticisi Disiplini:** AI, her yeni modÃ¼le baÅŸlamadan Ã¶nce ve bitirdikten sonra tÃ¼m sistemi kontrol edecektir. YapÄ±lan iÅŸin diÄŸer modÃ¼lleri bozup bozmadÄ±ÄŸÄ±nÄ± test edecektir.
3. **DokÃ¼mantasyon GÃ¼ncellemesi:** Bir modÃ¼l (Ã–rn: VeritabanÄ±, Login, MaÃ§ Ekleme) tamamlandÄ±ÄŸÄ±nda, AI bu dÃ¶kÃ¼manÄ±n sonuna (veya ilgili maddesine) girerek **"Neyin yapÄ±ldÄ±ÄŸÄ±nÄ±, nasÄ±l kodlandÄ±ÄŸÄ±nÄ± ve gÃ¼ncel durumunu (TamamlandÄ±/Eksik)"** detaylÄ±ca yazacaktÄ±r. DÃ¶kÃ¼man sÃ¼rekli canlÄ± bir log defteri olarak kalacaktÄ±r.

## 1. Proje Vizyonu ve Ã–zeti
Minia Bootstrap 5 Admin TemasÄ± Ã¼zerine inÅŸa edilecek, tamamen modern, dinamik ve tek merkezden (Hub) Ã§alÄ±ÅŸan bir bahis uygulamalarÄ± yÃ¶netim sistemidir. 
Sistem, eski `xyzv2` yapÄ±sÄ±ndaki gibi her uygulama iÃ§in farklÄ± veritabanÄ± veya karmaÅŸÄ±k post iÅŸlemleri barÄ±ndÄ±rmaz. TÃ¼m veriler merkezi bir API'den (JSON) daÄŸÄ±tÄ±lÄ±r. Yeni bir uygulama oluÅŸturmak, sadece panelden tema ve iÃ§erik seÃ§erek saniyeler iÃ§inde tamamlanÄ±r.

**Temel Mimari Kural (ModÃ¼ler TasarÄ±m):** Sistemin her Ã¶zelliÄŸi baÄŸÄ±msÄ±z bir **modÃ¼l** olarak tasarlanÄ±r. Bir modÃ¼le mÃ¼dahale edildiÄŸinde diÄŸer modÃ¼ller etkilenmez. Yeni modÃ¼ller sisteme takÄ±lÄ±p Ã§Ä±kartÄ±labilir (Plug & Play). Bu, bÃ¼yÃ¼k Ã¶lÃ§ekli gÃ¼ncellemelerde kodun "kafasÄ±nÄ±n karÄ±ÅŸmasÄ±nÄ±" engeller.

### 1. Temel Mimari ve Teknoloji YÄ±ÄŸÄ±nÄ± (Tech Stack)
* **Backend Motoru:** Modern OOP (Nesne YÃ¶nelimli) PHP, PDO (VeritabanÄ± baÄŸlantÄ±sÄ±) ve MySQL. %100 API-First (sadece JSON dÃ¶ner).
* **YÃ¶netim Paneli (Admin):** Minia Bootstrap 5 Admin Template (HTML/CSS) + Fetch API (AJAX).
* **Frontend (App Webview - Kesin Karar):** Vanilla JS yerine **kesinlikle Vue.js (Vue 3)** kullanÄ±lacaktÄ±r. Node.js, Vite, Webpack gibi build (derleme) araÃ§larÄ± KULLANILMAYACAK, Vue 3 doÄŸrudan CDN Ã¼zerinden saf HTML iÃ§ine Ã§ekilerek tek sayfalÄ±k dinamik SPA mimarisi kurulacaktÄ±r.
* **Multi-Domain YapÄ±sÄ±:** Ana API ve panel `acms.playbettingtips.com` adresinde Ã§alÄ±ÅŸÄ±rken, her uygulamanÄ±n Webview arayÃ¼zÃ¼ kendi domaininde (Ã–rn: `app.realmobilebet.com/vip`) barÄ±nÄ±r. Backend bu domainler iÃ§in Ã¶zel CORS kurallarÄ± iÅŸletir.

## 2. Temel Mimari (Tech Stack)
* **Backend:** PHP (PDO, OOP, GÃ¼venli API odaklÄ± mimari)
* **VeritabanÄ±:** MySQL / MariaDB
* **Admin Panel:** Minia Bootstrap 5 Admin Template
* **Frontend (App Webview SPA):** API ile beslenen, sayfa yenilenmesi yapmayan dinamik arayÃ¼z (Vanilla JS / Vue.js tabanlÄ±).
* **Dil:** Hem Admin Panel hem Webview SPA tamamen **Ä°ngilizce** olacaktÄ±r.

---

## 3. YÃ¶netim Paneli (Admin) ModÃ¼lleri

### 3.1. Dashboard (Ana GÃ¶sterge Paneli)
Minia temasÄ±ndaki grafiklerin tam kapasite kullanÄ±ldÄ±ÄŸÄ± bÃ¶lÃ¼m:
* Toplam KullanÄ±cÄ± SayÄ±sÄ± (Markalara ve uygulamalara gÃ¶re kÄ±rÄ±lÄ±mlar)
* Aktif VIP KullanÄ±cÄ± Grafikleri (AylÄ±k/HaftalÄ±k bÃ¼yÃ¼me)
* BugÃ¼n Eklenen ve SonuÃ§lanan MaÃ§larÄ±n Ä°statistikleri
* UygulamalarÄ±n hit/istek oranlarÄ±
* **Otomatik BaÅŸarÄ± OranÄ± (Hit Rate):** KazanÄ±lan/Kaybedilen maÃ§lara gÃ¶re sistemin dinamik hesapladÄ±ÄŸÄ± baÅŸarÄ± (ROI) yÃ¼zdesi. Bu veri uygulamalarda da kullanÄ±cÄ±lara gÃ¶sterilebilir.
* **Gelir ve DÃ¶nÃ¼ÅŸÃ¼m Takibi (Revenue Dashboard):** AylÄ±k onaylanan kullanÄ±cÄ± sayÄ±sÄ±, en Ã§ok satÄ±ÅŸ yapan uygulama, marka bazlÄ± bÃ¼yÃ¼me trendleri. Minia temasÄ±ndaki ApexCharts grafikleriyle gÃ¶rselleÅŸtirilir.

### 3.2. Uygulama (App) YÃ¶netimi
* **Yeni Uygulama Ekleme & DÃ¼zenleme:**
  * Uygulama AdÄ±, Slug (URL)
  * Marka SeÃ§imi (Real, Alex, Pep) â€” Her marka kendi tema ÅŸablonuna sahiptir.
  * Renk Paleti (Primary, Secondary, Accent renk seÃ§imleri)
  * Logo ve Favicon YÃ¼kleme
  * User-Agent KimliÄŸi ve iOS EriÅŸim Ä°zni
  * **Zorunlu GÃ¼ncelleme (Force Update):** Her uygulama kendi iÃ§inde minimum versiyon belirler.
  * **Hukuki Metinler (Legal Content):** Privacy Policy vb. metinler genel ayar deÄŸil, her uygulamanÄ±n kendi profilinden yÃ¶netilir.
* **Dinamik Ã–nizleme:** OluÅŸturulan uygulama iÃ§in Ã¼retilen webview linkine tÄ±klayarak anÄ±nda canlÄ± test yapabilme.

### 3.3. MaÃ§ (Tahmin) YÃ¶netimi
* **MaÃ§ Havuzu:** MaÃ§larÄ±n tek merkezde tutulduÄŸu ana liste.
* **MaÃ§ Ekleme (Manuel & Bot):** API veya manuel olarak sisteme maÃ§ eklenmesi.
* **Uygulama - MaÃ§ EÅŸleÅŸtirme:** Eklenen bir maÃ§Ä±n tek bir tÄ±kla istenilen uygulamalarda yayÄ±na alÄ±nmasÄ±.
* **Ligler ve TakÄ±mlar VeritabanÄ±:** Bot maÃ§ Ã§ekerken ligleri (Ad, Logo URL) ve takÄ±mlarÄ± (Ad, Bayrak URL) sisteme kaydeder. BÃ¶ylece sÃ¼rekli aynÄ± veri Ã§ekilmez, kendi yerleÅŸik futbol veritabanÄ±mÄ±z oluÅŸur.
* **SonuÃ§landÄ±rma:** Tek merkezden (WIN/LOSE) sonucun girilmesiyle tÃ¼m baÄŸlÄ± uygulamalarda maÃ§Ä±n sonucunun otomatik gÃ¼ncellenmesi.

### 3.4. KullanÄ±cÄ± YÃ¶netimi
* **Merkezi KullanÄ±cÄ± Listesi:** Uygulamalara kayÄ±t olan tÃ¼m Ã¼yelerin tek havuzda listelenmesi.
* **GPA (SipariÅŸ Kodu) KontrolÃ¼ ve VIP OnayÄ±:** Ãœcretli uygulamalarda kayÄ±t aÅŸamasÄ±nda Google Play SipariÅŸ Kodu zorunluluÄŸu ve admin panelden onay mekanizmasÄ±.
* **GeliÅŸmiÅŸ Banlama (Yasaklama) ModÃ¼lÃ¼:** 
  * Ban Sebebi ve BitiÅŸ Tarihi girilebilmesi.
  * *Not: GeliÅŸmiÅŸ Ã‡oklu Cihaz engellemesi ve zincirleme ban kurallarÄ± geliÅŸtirme aÅŸamasÄ±nda detaylandÄ±rÄ±lacaktÄ±r.*
  * **Ban E-postasÄ±:** KullanÄ±cÄ± banlandÄ±ÄŸÄ±nda belirlediÄŸimiz metinle kullanÄ±cÄ±ya otomatik email gÃ¶nderilir.
* **VIP SÃ¼re YÃ¶netimi ve Onay AkÄ±ÅŸÄ±:** 
  * Admin onayÄ±ndan (GPA kontrolÃ¼) sonra kullanÄ±cÄ±ya o uygulamaya Ã¶zel belirlediÄŸimiz "ÃœyeliÄŸiniz OnaylandÄ±" e-postasÄ± otomatik gider.
* KullanÄ±cÄ±lara Ã¶zel notlar ve log takibi.
* **Google Play Test HesaplarÄ± GÃ¼venliÄŸi:** MaÄŸaza onayÄ± sÃ¼recinde (demogoogle vs.) red yememek iÃ§in; test hesaplarÄ±na ban, kÄ±sÄ±tlama veya reklam gÃ¶stermeyen "White-list" (GÃ¼venilir Liste) altyapÄ±sÄ±.
* **KayÄ±t KaynaÄŸÄ± Takibi:** KayÄ±t formunda "Bizi nereden buldunuz?" seÃ§eneÄŸi (DiÄŸer uygulamamdan geldim, Google Play, Websitesi vb.). Dashboard'da kanal bazlÄ± istatistik olarak gÃ¶rÃ¼ntÃ¼lenebilir.
* **iOS TarayÄ±cÄ± EriÅŸimi:** iOS uygulamasÄ± olmadÄ±ÄŸÄ± iÃ§in bazÄ± kullanÄ±cÄ±lara Ã¶zel "iOS Access" seÃ§eneÄŸi. Bu kullanÄ±cÄ±lar User-Agent kÄ±sÄ±tlamasÄ±ndan muaf tutularak herhangi bir tarayÄ±cÄ±dan eriÅŸebilir. Admin panelden kullanÄ±cÄ± bazÄ±nda aÃ§Ä±lÄ±p kapatÄ±labilir.
* **Å?Ã¼pheli Ä°ÅŸlem AlgÄ±lama:** KullanÄ±cÄ± giriÅŸlerinde farklÄ± IP'lerden, farklÄ± tarayÄ±cÄ±lardan veya farklÄ± cihazlardan gelen oturumlarÄ± izleyen gÃ¼venlik modÃ¼lÃ¼. Å?Ã¼pheli durumda admin panelde uyarÄ± Ã¼retir ve otomatik olarak kullanÄ±cÄ±yÄ± iÅŸaretler/banlar.

### 3.5. Destek Talebi (Ticket) Sistemi
* KullanÄ±cÄ±larÄ±n uygulama iÃ§erisinden gÃ¶nderdikleri mesajlarÄ±n admin panele dÃ¼ÅŸtÃ¼ÄŸÃ¼ ve yÃ¶neticinin cevaplayabildiÄŸi dahili iletiÅŸim modÃ¼lÃ¼.

### 3.6. (Ä°ptal Edildi - Uygulama Ä°Ã§ine TaÅŸÄ±ndÄ±)
*(Hukuki Metin YÃ¶netimi artÄ±k Uygulama (App) YÃ¶netimi iÃ§inden her uygulama iÃ§in Ã¶zel yapÄ±lacaktÄ±r.)*

### 3.7. Sistem AyarlarÄ± ve MonitÃ¶r
* **CanlÄ± Terminal (Log) MonitÃ¶rÃ¼:** Sistemin (cron veya manuel) yaptÄ±ÄŸÄ± otomatik maÃ§ Ã§ekme, sonuÃ§landÄ±rma ve hata kayÄ±tlarÄ±nÄ±n canlÄ± olarak izlenebildiÄŸi terminal ekranÄ±.
* **Admin Ä°ÅŸlem GeÃ§miÅŸi (Audit Log):** Kim, ne zaman, neyi deÄŸiÅŸtirdi? TÃ¼m admin iÅŸlemlerinin (maÃ§ ekleme, kullanÄ±cÄ± banlama, ayar deÄŸiÅŸtirme) zaman damgalÄ± olarak kaydedildiÄŸi sayfa.
* **SMTP (Mail) AyarlarÄ±:** Otomatik e-posta gÃ¶nderimi iÃ§in mail sunucu yapÄ±landÄ±rmasÄ±.
* Genel API ayarlarÄ± ve SEO yapÄ±landÄ±rmalarÄ±.

### 3.8. (Ä°ptal Edildi - Uygulama Ä°Ã§ine TaÅŸÄ±ndÄ±)
*(Zorunlu GÃ¼ncelleme / Force Update artÄ±k Uygulama (App) YÃ¶netimi iÃ§inden her uygulama iÃ§in Ã¶zel yapÄ±lacaktÄ±r.)*

### 3.9. BakÄ±m Modu (Maintenance Mode)
* Panelden tek tÄ±kla tÃ¼m sistemi veya tek bir uygulamayÄ± "Under Maintenance" moduna alma. KullanÄ±cÄ±lar uygulamayÄ± aÃ§tÄ±ÄŸÄ±nda ÅŸÄ±k bir bakÄ±m ekranÄ± gÃ¶rÃ¼r. DB gÃ¼ncellemesi veya sunucu taÅŸÄ±ma anlarÄ±nda kullanÄ±cÄ±larÄ±n kÄ±rÄ±k sayfa gÃ¶rmesini engeller.

### 3.10. Ã‡oklu Veri KaynaÄŸÄ± (Data Source) YÃ¶netimi
* Panelden birden fazla veri beslenme noktasÄ± (API endpoint, scraper vb.) tanÄ±mlanabilir. MaÃ§larÄ±n hangi kaynaktan geldiÄŸi izlenebilir. Ä°leride yeni analiz motorlarÄ± veya scraper'lar eklendiÄŸinde sisteme kolayca entegre edilebilir.

### 3.11. Otomatik Yedekleme (Auto Backup)
* Sistemin veritabanÄ±nÄ± cron ile her gece otomatik yedekler. Son 7 gÃ¼nÃ¼n yedeÄŸi saklanÄ±r. Panelden "Geri YÃ¼kle" butonuyla tek tÄ±kla herhangi bir gÃ¼nÃ¼n yedeÄŸine dÃ¶nÃ¼lebilir.

### 3.12. Promosyon ve Ä°ndirim YÃ¶netimi (Promotion Engine)
* Her marka iÃ§in ayrÄ± ayrÄ± indirim kampanyalarÄ± oluÅŸturulabilir.
* Kampanya ayarlarÄ±: BaÅŸlangÄ±Ã§/BitiÅŸ Tarihi, Ä°ndirim OranÄ± (%), Ä°ndirim BaÅŸlÄ±ÄŸÄ±, AÃ§Ä±klama Metni, Banner GÃ¶rseli.
* Kampanya aktifken ilgili markanÄ±n tÃ¼m uygulamalarÄ±nda dinamik banner/slider olarak gÃ¶rÃ¼ntÃ¼lenir.
* BitiÅŸ tarihi geldiÄŸinde kampanya otomatik kapanÄ±r.

### 3.13. Bildirim Merkezi (OneSignal Entegrasyonu)
* Bildirimler ACMS Ã¼zerinden GÃ–NDERÄ°LMEZ (KullanÄ±cÄ± kendi OneSignal panelinden gÃ¶nderir).
* Ancak her uygulamanÄ±n Webview SPA'sÄ±na bir "Bildirimler (Inbox)" sayfasÄ± eklenir. API aracÄ±lÄ±ÄŸÄ±yla OneSignal'dan gÃ¶nderilmiÅŸ geÃ§miÅŸ bildirimler Ã§ekilerek uygulama iÃ§inde kullanÄ±cÄ±lara liste halinde sunulur.

### 3.14. Otomatik E-posta Dizisi (Email Sequence)
* KayÄ±t anÄ± â†’ "HoÅŸ geldiniz, Ã¼yeliÄŸiniz ekiplerimiz tarafÄ±ndan onaylanacaktÄ±r" maili.
* Onay sonrasÄ± â†’ "ÃœyeliÄŸiniz aktif edilmiÅŸtir!" maili.
* Red sonrasÄ± â†’ "ÃœyeliÄŸiniz ÅŸu sebeplerden dolayÄ± onaylanmamÄ±ÅŸtÄ±r..." maili.
* Ban sonrasÄ± â†’ Ban sebebi ve iletiÅŸim bilgisini iÃ§eren mail.
* Her uygulama iÃ§in e-posta ÅŸablonlarÄ± ve metinleri panelden Ã¶zelleÅŸtirilebilir.

### 3.15. Uygulama SaÄŸlÄ±k MonitÃ¶rÃ¼ (Health Check)
* Dashboard'da her uygulamanÄ±n durumunu gÃ¶steren panel: API yanÄ±t sÃ¼resi, son 1 saatteki istek sayÄ±sÄ±, hata oranÄ±.
* YeÅŸil/SarÄ±/KÄ±rmÄ±zÄ± renk kodlarÄ±yla tÃ¼m ekosistemin saÄŸlÄ±ÄŸÄ± bir bakÄ±ÅŸta gÃ¶rÃ¼lÃ¼r.

---

## 4. Uygulama Ä°Ã§i (Webview SPA) Mimarisi

Sistemin "GÃ¶rÃ¼ntÃ¼le" dendiÄŸinde Ã§alÄ±ÅŸacak olan Webview altyapÄ±sÄ± aÅŸaÄŸÄ±daki mantÄ±kla Ã§alÄ±ÅŸÄ±r:

### 4.1. GÃ¼venlik KatmanÄ±
* **Ã–zel User-Agent (Ajan) GÃ¼venliÄŸi:** Panelden her uygulamaya Ã¶zel bir Agent tanÄ±mlanÄ±r. Ä°stek bu Agent'tan gelmiyorsa sayfa aÃ§Ä±lmaz.
* **SÄ±kÄ± Mobil Cihaz KontrolÃ¼ (Mobile_Detect):** Eski "notmobile" sisteminin geliÅŸmiÅŸ versiyonu. Ä°stek Ã¶zel User-Agent ile gelse bile cihazÄ±n gerÃ§ekten bir mobil iÅŸletim sistemi (Android/iOS) olup olmadÄ±ÄŸÄ± sunucu tarafÄ±nda doÄŸrulanÄ±r. MasaÃ¼stÃ¼ simÃ¼latÃ¶rleri engellenir.
* **KÄ±sÄ±tlama Muafiyeti:** YÃ¶neticiler veya test iÃ§in onaylanan kullanÄ±cÄ±lar, "KullanÄ±cÄ± YÃ¶netimi" sayfasÄ±ndan Ã¶zel olarak seÃ§ilerek bu User-Agent ve Mobil cihaz zorunluluÄŸundan muaf tutulabilir (BÃ¶ylece bilgisayardan admin hesabÄ±yla giriÅŸ yapÄ±p test edebilirsiniz).
* **Oturum KalÄ±cÄ±lÄ±ÄŸÄ± (Session Persistence):** KullanÄ±cÄ± uygulamayÄ± kapatÄ±p tekrar aÃ§tÄ±ÄŸÄ±nda kullanÄ±cÄ± adÄ± ve ÅŸifre sormadan otomatik giriÅŸ yapÄ±lÄ±r. Arka planda API her aÃ§Ä±lÄ±ÅŸta ban kontrolÃ¼ yapar.

### 4.2. Tema ve Reskin Motoru
* **Marka BaÅŸÄ±na 1 Ana Tema Å?ablonu:** Her marka (Real, Alex, Pep) iÃ§in benzersiz bir dÃ¼zen (layout) ve navigasyon yapÄ±sÄ± kodlanÄ±r. AynÄ± marka altÄ±ndaki uygulamalar (VIP, Elite, Fixed vb.) bu ÅŸablon Ã¼zerinde sadece renk, logo ve iÃ§erik reskin'i ile oluÅŸturulur.
* **Dinamik Reskin (Theme Injection):** API, uygulamanÄ±n renk kodlarÄ±nÄ± ve logosunu dÃ¶ner. Motor saliseler iÃ§inde CSS deÄŸiÅŸkenlerini uygulayarak arayÃ¼zÃ¼ boyar.

### 4.3. YÃ¼kleme ve UX Deneyimi
* **Premium YÃ¼kleme Animasyonu (App Loader):** Uygulama aÃ§Ä±ldÄ±ÄŸÄ±nda tam sayfa kaplayan, uygulamanÄ±n logosunu gÃ¶steren profesyonel bir yÃ¼kleme ekranÄ± belirir. Ä°Ã§erik hazÄ±r olduÄŸunda kaybolur.
* **Skeleton Loading:** Sayfa geÃ§iÅŸlerinde ve veri yÃ¼klenirken, iÃ§erik alanlarÄ± gri tonlarÄ±nda "iskelet" (placeholder) animasyonlarÄ±yla doldurulur. KullanÄ±cÄ± boÅŸ ekrana bakmaz.

### 4.4. Ä°Ã§erik ve ModÃ¼ller
* **Dinamik Ä°Ã§erik ve GÃ¼venlik:** API, uygulamaya atanmÄ±ÅŸ maÃ§larÄ± dÃ¶ner ve gÃ¼venlik (ban) kontrolÃ¼ yapar. BanlÄ±ysa API'den dÃ¶nen Ban Sebebi ve Ä°letiÅŸim Metni gÃ¶sterilir.
* **App Ã–zel DuyurularÄ± ve Zorunlu GiriÅŸ:** Her uygulama iÃ§in panelden pop-up veya karÅŸÄ±lama mesajÄ± belirlenebilir. "Zorunlu GiriÅŸ Yap" veya "Direkt MaÃ§larÄ± GÃ¶ster" modlarÄ± API tarafÄ±ndan kontrol edilir.
* **Bekleyen Ãœye & Toast Bildirimi:** Ãœyeler onay beklerken (pending) bile uygulamada gezinebilir. Ãœyelik onaylandÄ±ÄŸÄ±nda ilk giriÅŸte ekranda ÅŸÄ±k bir "Membership Approved" toast mesajÄ± belirir.
* **Periyodik "Bizi OylayÄ±n" (Rate App) ModÃ¼lÃ¼:** MaÃ§ kazanma oranÄ±na baÄŸlÄ± deÄŸildir. Sistem, belirlenen periyotlarda (Ã¶rn: her 7. aÃ§Ä±lÄ±ÅŸta, veya belirli gÃ¼n aralÄ±klarÄ±nda) kullanÄ±cÄ±ya ÅŸÄ±k bir "Rate us 5 Stars on Google Play" modalÄ± gÃ¶sterir. Zamanlama panelden ayarlanabilir.
* **VIP Hub (VIP Uygulama Merkezi):** Her marka iÃ§in panelden yÃ¶netilebilen, o markaya ait tÃ¼m VIP uygulamalarÄ±n kartlarla listelendiÄŸi ve Google Play linklerine yÃ¶nlendirildiÄŸi Ã¶zel sayfa.

### 4.5. Sayfalar (SPA Navigasyonu)
* **Alt Navigasyon BarÄ± (Bottom Tab):** Mobil kullanÄ±cÄ±lar iÃ§in varsayÄ±lan navigasyon yÃ¶ntemidir. MenÃ¼ isimleri panelden her uygulama iÃ§in Ã¶zelleÅŸtirilebilir.
* **Ana Sayfa Kurgusu (Standart 4 Sekme):**
  * **1. Anasayfa (Home):** Uygulamaya giriÅŸ yapÄ±ldÄ±ÄŸÄ±nda kullanÄ±cÄ±yÄ± karÅŸÄ±layan dashboard (karÅŸÄ±lama yazÄ±larÄ±, gÃ¼ncel duyurular veya promosyon bannerlarÄ±).
  * **2. Tahminler (Predictions & History):** Yetki kontrollÃ¼ VIP maÃ§ alanÄ±. BugÃ¼nÃ¼n maÃ§larÄ± burada listelenir. AyrÄ±ca bu sayfadaki **Tarih SeÃ§ici** sayesinde geÃ§miÅŸ gÃ¼nlere gidilerek sonuÃ§lanmÄ±ÅŸ maÃ§lar gÃ¶rÃ¼lebilir.
  * **3. VIP Hub:** O markaya ait diÄŸer VIP uygulamalarÄ±n vitrin sayfasÄ±.
  * **4. Profil / Ä°letiÅŸim:** KullanÄ±cÄ± bilgileri, Destek Talebi (Ticket), Bildirimler (OneSignal arÅŸivi) ve Hukuki Sayfalar (Bottom Sheet modallar ile).

### 4.6. KullanÄ±cÄ± Deneyimi EkstralarÄ±
* Ã‡oklu Dil DesteÄŸi (Sisteme entegre Google Translate arayÃ¼zÃ¼)
* CanlÄ± Destek (Tawk.to) entegrasyonu

---

## 5. Uygulama SÃ¼reci ve GeliÅŸtirme AdÄ±mlarÄ±
*(Bu aÅŸamalar mutabÄ±k kalÄ±ndÄ±kÃ§a doldurulacak ve "Tik" atÄ±larak ilerlenecektir)*

1. [ ] **VeritabanÄ± Å?emasÄ±nÄ±n Ã‡izilmesi:** TablolarÄ±n (apps, users, matches, vb.) tÃ¼m kolonlarÄ±yla Markdown Ã¼zerinde planlanmasÄ±.
2. [ ] **Minia TemasÄ±nÄ±n Entegrasyonu:** SeÃ§ilen temanÄ±n lokal sunucuya entegre edilip boÅŸ sayfalarÄ±n hazÄ±rlanmasÄ±.
3. [ ] **Backend (API) AltyapÄ±sÄ±nÄ±n YazÄ±lmasÄ±:** GÃ¼venli veritabanÄ± baÄŸlantÄ±larÄ± ve JSON Ã§Ä±ktÄ± veren API'lerin kodlanmasÄ±.
4. [ ] **Admin ModÃ¼llerinin KodlanmasÄ±:** SÄ±rasÄ±yla MaÃ§, App, KullanÄ±cÄ± sayfalarÄ±nÄ±n temaya giydirilmesi.
5. [ ] **SPA (Webview) Motorunun KodlanmasÄ±:** Dinamik temanÄ±n ve uygulamalarÄ±n arayÃ¼zÃ¼nÃ¼n oluÅŸturulmasÄ±.
6. [ ] **Eski Verilerin (xyzv2) TaÅŸÄ±nmasÄ±:** TaÅŸÄ±ma scripti ile eski kullanÄ±cÄ± ve loglarÄ±n yeni ACMS'ye aktarÄ±mÄ±.

---
*Not: Bu dÃ¶kÃ¼man proje geliÅŸtikÃ§e canlÄ± olarak gÃ¼ncellenecektir.*

---

## 6. ModÃ¼l DetaylandÄ±rmalarÄ± (Module Specifications)

Bu aÅŸamada her modÃ¼lÃ¼n iÅŸ kurallarÄ±, veritabanÄ± gereksinimleri ve ekran detaylarÄ± netleÅŸtirilecektir.

### 6.1. GÃ¼venli GiriÅŸ SayfasÄ± (Admin Login)
* **Brute-Force KorumasÄ±:** 5 ardÄ±ÅŸÄ±k hatalÄ± giriÅŸte IP bazlÄ± geÃ§ici ban (30 dakika).
* **Bot KorumasÄ±:** 3 hatalÄ± giriÅŸten sonra CAPTCHA doÄŸrulama zorunluluÄŸu.
* **Oturum YÃ¶netimi:** "Beni HatÄ±rla" (Remember Me) seÃ§eneÄŸi ve uzun sÃ¼reli hareketsizlik (Ã¶rn: 2 saat) durumunda otomatik oturum sonlandÄ±rma.
* **Loglama:** TÃ¼m baÅŸarÄ±lÄ± ve baÅŸarÄ±sÄ±z giriÅŸ denemeleri IP, Tarih, TarayÄ±cÄ± bilgisiyle birlikte "Audit Log" tablosuna kaydedilir.

### 6.2. Dashboard (Ana GÃ¶sterge Paneli)
Minia temasÄ±nÄ±n sunduÄŸu "Widget" ve "ApexCharts" bileÅŸenleriyle donatÄ±lmÄ±ÅŸ, tek bakÄ±ÅŸta tÃ¼m ekosistemin yÃ¶netildiÄŸi ana merkez.

**Ã–zet KartlarÄ± (Mini Widgets):**
* **Toplam Marka:** Aktif marka sayÄ±sÄ± (Ã–rn: 3).
* **Toplam Uygulama:** Sistemdeki aktif app sayÄ±sÄ±.
* **Toplam Ãœye:** (OnaylÄ± / Bekleyen kÄ±rÄ±lÄ±mÄ± ile).
* **BugÃ¼nkÃ¼ MaÃ§lar:** (Eklenen / SonuÃ§lanan).
* **Genel Hit Rate:** TÃ¼m sistemin son 30 gÃ¼nlÃ¼k baÅŸarÄ± yÃ¼zdesi.
* **Onay Bekleyen:** Dikkat Ã§ekici kÄ±rmÄ±zÄ± badge ile bekleyen Ã¼ye sayÄ±sÄ±.

**GÃ¶rsel Grafikler (ApexCharts):**
* **Ãœye BÃ¼yÃ¼me Trendi (Line Chart):** Son 30 gÃ¼nlÃ¼k gÃ¼nlÃ¼k yeni kayÄ±t sayÄ±sÄ±nÄ±n dalgalanmasÄ±.
* **MaÃ§ SonuÃ§ DaÄŸÄ±lÄ±mÄ± (Pie Chart):** WIN / LOSE / Bekliyor dilimlerinin oransal daÄŸÄ±lÄ±mÄ±.
* **Marka BazlÄ± Performans (Bar Chart):** Hangi markanÄ±n (Real, Alex, Pep) daha Ã§ok kullanÄ±cÄ± veya hit Ã§ektiÄŸi.
* **Kategori BazlÄ± Hit Rate (Radar Chart):** Hangi tahmin tipinde (Safe Picks, Correct Score) daha baÅŸarÄ±lÄ± olunduÄŸunun analizi.
* **Uygulama SaÄŸlÄ±k Durumu:** Renk kodlu kartlarla (YeÅŸil/SarÄ±/KÄ±rmÄ±zÄ±) uygulamalarÄ±n API yanÄ±t sÃ¼releri ve hata oranlarÄ±.

**Listeler ve Tablolar:**
* **Son KayÄ±t Olan Ãœyeler:** Uygulama adÄ±, tarih, statÃ¼ (Son 10 kayÄ±t).
* **Son Gelen Ticket'lar:** Destek talepleri listesi (Son 5).
* **Onay Bekleyen Ãœyeler:** Tablo iÃ§inden anÄ±nda "Onayla/Reddet" butonu ile hÄ±zlÄ± iÅŸlem imkanÄ±.
* **BugÃ¼nÃ¼n MaÃ§larÄ±:** HÄ±zlÄ± Ã¶zet gÃ¶rÃ¼nÃ¼mÃ¼.
* **En Ã‡ok KazandÄ±ran Uygulamalar:** Hit rate oranlarÄ±na gÃ¶re Top 5 sÄ±ralama.
* **Son Admin Ä°ÅŸlemleri:** Audit log'dan son 5 kritik hareket.

**AkÄ±llÄ± UyarÄ± Sistemi (Alerts):**
* ğŸ”´ **Å?Ã¼pheli GiriÅŸ:** FarklÄ± IP'lerden veya farklÄ± cihazlardan gelen ÅŸÃ¼pheli kullanÄ±cÄ± hareketleri.
* ğŸŸ¡ **BakÄ±m Modu Aktif:** Bir uygulama bakÄ±mda kaldÄ±ÄŸÄ±nda sistem hatÄ±rlatmasÄ±.
* ğŸŸ¡ **Force Update:** Minimum versiyon gÃ¼ncellenip beklemede kalan iÅŸlemler iÃ§in hatÄ±rlatma.
* ğŸ”´ **DÃ¼ÅŸÃ¼k Hit Rate:** Herhangi bir kategorinin kazanma oranÄ± %50'nin altÄ±na dÃ¼ÅŸtÃ¼ÄŸÃ¼nde uyarÄ±.
* ğŸ”´ **API Hata OranÄ±:** Herhangi bir uygulamanÄ±n hata oranÄ± %5'i geÃ§erse kritik uyarÄ±.

*(Dashboard haricinde, her metriÄŸin Ã§ok daha detaylÄ± tarih filtrelemeleriyle incelenebildiÄŸi ayrÄ± bir **Genel Ä°statistikler (Analytics)** sayfasÄ± da bulunacaktÄ±r.)*

### 6.3. Uygulama (App) YÃ¶netimi DetaylarÄ±

**1. Temel Kimlik Bilgileri (Identity):**
* **Uygulama AdÄ±:** (Ã–rn: Real VIP Tips)
* **Uygulama Slug:** (Ã–rn: `real-vip-tips`)
* **Marka SeÃ§imi:** (SeÃ§im Kutusu: Real, Alex, Pep)
* **YayÄ±n Durumu:** (Aktif / Pasif / BakÄ±m Modu)
* **Uygulama Tipi:** (Ãœcretli "Paid" / Ãœcretsiz "Free" - Ä°lerisi iÃ§in altyapÄ±)
* **Uygulama FiyatÄ± ($):** SatÄ±ÅŸ istatistikleri ve Revenue Dashboard hesaplamalarÄ± iÃ§in girilen gÃ¼ncel maÄŸaza fiyatÄ±.

**2. TasarÄ±m ve Tema (Appearance & Reskin):**
* UygulamalarÄ±n birbirine benzememesi iÃ§in geniÅŸletilmiÅŸ renk paleti:
  * **Primary Color** (Ana Renk)
  * **Secondary Color** (Ä°kincil Renk)
  * **Accent Color** (Vurgu Rengi - Butonlar/Badge'ler iÃ§in)
  * **Background Gradient** (Arkaplan iÃ§in Ã¶zel geÃ§iÅŸ renkleri)
* **Logo ve Favicon** YÃ¼kleme.
* **App Ä°Ã§i Ä°kon SeÃ§imleri:** VIP Hub kartlarÄ±ndaki ikonlarÄ±n panelden belirlenebilmesi (Ã–rn: TaÃ§, Kalkan, Elmas).
* **Alt MenÃ¼ Ä°simlendirmesi:** Navigasyondaki 4 ana menÃ¼nÃ¼n adÄ±nÄ± (Ã–rn: Home, Predictions, VIP Hub, Profile) her uygulama iÃ§in panelden farklÄ± yapabilme.

**3. GÃ¼venlik, Teknik ve UX AyarlarÄ±:**
* **Custom User-Agent:** Sadece bu agent'a sahip Android Webview uygulamasÄ±ndan gelen istekler kabul edilir.
* **iOS EriÅŸim Ä°zni:** SeÃ§ilirse User-Agent kÄ±sÄ±tlamasÄ± esnetilir, Safari vs. tarayÄ±cÄ±lardan girilebilir.
* **Minimum SÃ¼rÃ¼m (Force Update):** Girilen sÃ¼rÃ¼mÃ¼n altÄ±ndakilere gÃ¼ncelleme uyarÄ±sÄ± basÄ±lÄ±r.
* **Ã–zel Kod AlanÄ± (Custom Scripts):** Yandex Metrica, Google Analytics veya ek CSS kodlarÄ± iÃ§in Ã¶zel alan.
* **GeÃ§miÅŸ MaÃ§ GÃ¶sterim Limiti:** KullanÄ±cÄ±larÄ±n "Tahminler" sekmesinde geriye dÃ¶nÃ¼k en fazla kaÃ§ gÃ¼nlÃ¼k geÃ§miÅŸi gÃ¶rebileceÄŸinin sÄ±nÄ±rÄ± (Ã–rn: Sadece son 10 gÃ¼n).

**4. Entegrasyonlar (Integrations):**
* OneSignal App ID
* OneSignal API Key

**5. Hukuki Metinler ve Duyurular:**
* **App Ã–zel Duyuru / Pop-up Metni**
* **Hukuki Sayfalar:** Privacy Policy, Terms of Use (Zengin metin editÃ¶rÃ¼ ile eklenebilecek alanlar).

### 6.4. KullanÄ±cÄ± YÃ¶netimi DetaylarÄ±

**1. KayÄ±t ve GiriÅŸ KurallarÄ± (Webview TarafÄ±):**
* **KayÄ±t Formu (Register):** Ad Soyad, E-posta, Å?ifre, GPA (Google Play SipariÅŸ Kodu). Telefon numarasÄ± **istenmeyecektir**.
* **GiriÅŸ Formu (Login):** Sadece E-posta ve Å?ifre ile giriÅŸ yapÄ±lÄ±r.

**2. KullanÄ±cÄ± Listesi (Admin TarafÄ± - DataTables):**
* GeliÅŸmiÅŸ filtreleme ve arama Ã¶zellikleri (Ã–rn: "Sadece BanlÄ±larÄ± GÃ¶ster", "Sadece Real markasÄ±ndakileri GÃ¶ster", "Son 7 gÃ¼nde onaylananlar").
* **Tablo KolonlarÄ±:** Ad Soyad, E-posta, Uygulama, StatÃ¼ (OnaylÄ±/Bekliyor/BanlÄ±), KayÄ±t Tarihi, HÄ±zlÄ± Aksiyon ButonlarÄ± (Onayla/Reddet/Banla).

**3. KullanÄ±cÄ± Profili (DÃ¼zenleme EkranÄ±):**
* **Temel Bilgiler:** Ad Soyad, E-posta, KayÄ±t KaynaÄŸÄ± (Bizi nereden buldunuz?).
* **Uygulama & Yetki:** Hangi uygulamaya kayÄ±tlÄ± olduÄŸu, StatÃ¼sÃ¼. TÃ¼m uygulamalar "Ã–mÃ¼r Boyu (Lifetime)" mantÄ±ÄŸÄ±yla Ã§alÄ±ÅŸtÄ±ÄŸÄ± iÃ§in sÃ¼reli VIP atamasÄ± **yapÄ±lmaz**.
* **GPA Bilgisi:** Google Play SipariÅŸ NumarasÄ±.
* **Cihaz & Log Bilgileri (Å?Ã¼pheli Ä°ÅŸlem Takibi):** KullanÄ±cÄ±nÄ±n IP adresi, cihaz modeli, son giriÅŸ tarihi, farklÄ± IP'lerden giriÅŸ yapÄ±p yapmadÄ±ÄŸÄ±nÄ± gÃ¶steren log geÃ§miÅŸi.
* **KÄ±sÄ±tlamalardan Muaf Tutma:** "Force Update'den Muaf Tut", "iOS TarayÄ±cÄ± EriÅŸimi Ver" (Adminler veya testÃ§iler iÃ§in bypass yetkileri).
* **KullanÄ±cÄ±ya Ã–zel Not:** Sadece adminlerin gÃ¶rebileceÄŸi not alanÄ±.

**4. GeliÅŸmiÅŸ Zincirleme Ban ModÃ¼lÃ¼ (Cross-App Banning):**
* **"Ãœyelik Tara" Ã–zelliÄŸi:** Ban iÅŸlemi yapÄ±lmadan Ã¶nce sistem bu kullanÄ±cÄ±nÄ±n e-posta veya cihaz IP'sine sahip baÅŸka hesaplarÄ± olup olmadÄ±ÄŸÄ±nÄ± (diÄŸer VIP uygulamalarÄ±nÄ±zda) tarar.
* EÄŸer baÅŸka uygulamalarda da Ã¼yeliÄŸi varsa, admin panelinde liste halinde gÃ¶sterilir ve *"DiÄŸer uygulamalardaki Ã¼yelikleri de banlansÄ±n mÄ±?"* seÃ§eneÄŸi sunulur. BÃ¶ylece sahtekar/kÃ¶tÃ¼ niyetli kullanÄ±cÄ±lar tÃ¼m ekosistemden tek tÄ±kla temizlenir.
* **Ban DetaylarÄ±:** Ban Sebebi (KullanÄ±cÄ±ya gÃ¶sterilecek/gÃ¶nderilecek metin), IP/Cihaz bazlÄ± engelleme seÃ§eneÄŸi.

**5. E-posta GeÃ§miÅŸi:**
* Bu kullanÄ±cÄ±ya sistem tarafÄ±ndan bugÃ¼ne kadar gÃ¶nderilmiÅŸ maillerin (HoÅŸgeldin, OnaylandÄ±, Ban, vs.) loglarÄ±.

### 6.5. MaÃ§ (Tahmin) YÃ¶netimi DetaylarÄ±

**1. MaÃ§ Ekleme EkranÄ± (Yeni Sayfa):**
* Modal (AÃ§Ä±lÄ±r pencere) yerine **Ã¶zel bir maÃ§ ekleme sayfasÄ±** kullanÄ±lacaktÄ±r. 
* **HÄ±zlÄ± Ekleme AkÄ±ÅŸÄ±:** Kaydet butonuna basÄ±ldÄ±ÄŸÄ±nda form temizlenir ve "Eklendi" uyarÄ±sÄ± verir, bÃ¶ylece art arda hÄ±zlÄ±ca maÃ§ girilebilir. AyrÄ±ca "Kaydet ve Listeye DÃ¶n" butonu bulunur.
* **Form AlanlarÄ±:**
  * **KarÅŸÄ±laÅŸma AdÄ±:** Tek bir input alanÄ± (Ã–rn: `FenerbahÃ§e - Galatasaray` veya `FenerbahÃ§e vs Galatasaray`). Sistem " - " veya " vs " iÅŸaretini baz alarak ev sahibi ve deplasmanÄ± otomatik ayÄ±rÄ±r. MaÃ§ beklerken ekranda "vs" yazar, sonuÃ§landÄ±ÄŸÄ±nda skor "vs" iÅŸaretinin yerini alÄ±r.
  * **Tarih ve Saat:** Ä°ki ayrÄ± alan veya ortak DateTime seÃ§ici.
  * **Lig:** (Ã–rn: Turkey Super League). Botun kaydettiÄŸi liglerden otomatik tamamlama ile seÃ§ilebilir.
  * **Tahmin:** (Ã–rn: Home Wins).
  * **Oran:** (Ã–rn: 1.85).
  * **Skor:** MaÃ§ sonuÃ§landÄ±ysa girilecek alan (Ã–rn: 2-1).
  * **MaÃ§ Durumu:** Bekliyor (Pending), KazandÄ± (Win), Kaybetti (Lose), Ertelendi (Postponed).
  * **Uygulama SeÃ§imi:** MaÃ§Ä±n yayÄ±nlanacaÄŸÄ± uygulamalar (Check-list).

**2. Ã‡oklu Uygulama (Multi-App) DaÄŸÄ±tÄ±mÄ±:**
* Bir maÃ§Ä± eklerken veya dÃ¼zenlerken sistemde aktif olan tÃ¼m uygulamalar (Check-list / Onay kutularÄ± ÅŸeklinde) listelenir.
* Ã–rn: "Real Elite VIP [x]", "Alex Premium [ ]". Tek bir tuÅŸla maÃ§ sadece seÃ§ili uygulamalara atanÄ±r.

**3. Otomatik Bot YÃ¶netimi ve EÅŸleÅŸtirme (Mapping):**
* MaÃ§larÄ±n dÄ±ÅŸ kaynaklÄ± bir bot tarafÄ±ndan Ã§ekilip doÄŸrudan ACMS veritabanÄ±na postalanmasÄ± iÃ§in API endpoint'i.
* **Kategori - Uygulama EÅŸleÅŸtirmesi:** Bot panelinde en kritik ayar. Bot'tan gelen kategori verisinin (Ã–rn: `over_under`), ACMS'de hangi uygulamaya (Ã–rn: `Real VIP Over/Under`) gideceÄŸinin eÅŸleÅŸtirmesi yapÄ±lÄ±r.
* **Cron YÃ¶netimi:** Botun maÃ§ Ã§ekme sÄ±klÄ±ÄŸÄ± ve sonuÃ§landÄ±rma zamanlamalarÄ± panel Ã¼zerinden tetiklenebilir.

**4. Toplu SonuÃ§landÄ±rma (Bulk Resolution):**
* "Bekleyen MaÃ§lar" listesinde maÃ§larÄ±n yanÄ±ndaki âœ”ï¸? (Kazan) veya â?Œ (Kaybet) butonlarÄ±na basarak hÄ±zlÄ± sonuÃ§landÄ±rma.
* Bir maÃ§ merkezden sonuÃ§landÄ±ÄŸÄ± an, o maÃ§Ä±n yayÄ±nlandÄ±ÄŸÄ± tÃ¼m uygulamalarda sonuÃ§ anÄ±nda ve otomatik olarak gÃ¼ncellenir (Cache mekanizmasÄ± ile birlikte).

### 6.6. Promosyon ve Ä°ndirim YÃ¶netimi (Promo Engine)
Eski sistemdeki `promo_config.php` dosyasÄ±nÄ±n gÃ¶revini tamamen Ã¼zerine alan, panelden yÃ¶netilen dinamik modÃ¼l.

**1. Kampanya Temel AyarlarÄ±:**
* **Kampanya Durumu:** AÃ§Ä±k / KapalÄ± (On/Off)
* **Zamanlama:** BaÅŸlangÄ±Ã§ Tarihi ve BitiÅŸ Tarihi (Datetime formatÄ±nda). SÃ¼re dolduÄŸunda sistem otomatik olarak indirimi yayÄ±ndan kaldÄ±rÄ±r.

**2. Kampanya Ä°Ã§erikleri (Metinler):**
* **Kampanya AdÄ±:** Ã–rn: "ğŸ?‰ 7th ANNIVERSARY PROMO"
* **BaÅŸlÄ±k & Alt BaÅŸlÄ±k:** Ã–rn: "7th Anniversary Celebration", "Celebrating 7 years of elite sports analysis..."
* **Buton (CTA) Metinleri:** "CLAIM %DISCOUNT% DISCOUNT"
* **Banner Metni:** "ğŸ?‰ 7th Anniversary Event: Up to 70% Off VIP Plans!"

**3. Uygulama BazlÄ± Ä°ndirim OranlarÄ±:**
* TÃ¼m uygulamalara standart bir "Default" indirim (Ã–rn: %50) atanabilir.
* Belirli uygulamalara panelden (eski sistemdeki package name veya slug Ã¼zerinden) Ã¶zel ekstra indirimler (Ã–rn: All in One VIP iÃ§in %70) girilebilir.

*Bu Ã¶zellik sayesinde yeni bir promosyon yapmak istediÄŸinizde dosyalarla veya kodlarla uÄŸraÅŸmanÄ±za gerek kalmaz; her ÅŸey admin panelden canlÄ± olarak yÃ¶netilir.*

### 6.7. Admin Panel Sol MenÃ¼ (Sidebar) HiyerarÅŸisi
Yeni mimari kararlarÄ±na gÃ¶re gÃ¼ncellenmiÅŸ sol menÃ¼ aÄŸacÄ±:

**ğŸ“Š DASHBOARD**
* **Genel BakÄ±ÅŸ (Overview):** Ana istatistikler ve mini saÄŸlÄ±k monitÃ¶rÃ¼.
* **Finans & DÃ¶nÃ¼ÅŸÃ¼m (Revenue):** SatÄ±ÅŸ, hit rate ve marka bÃ¼yÃ¼me metrikleri.
* **Sistem SaÄŸlÄ±ÄŸÄ± (Health Check):** Genel loglar, bot Ã§alÄ±ÅŸma durumlarÄ±, hata raporlarÄ± detay sayfasÄ±.

**ğŸ“± UYGULAMALAR (APPS)**
* **Real MarkasÄ±** (AÃ§Ä±lÄ±r MenÃ¼)
  * VIP Tips
  * Elite Tips vb.
* **Alex MarkasÄ±** (AÃ§Ä±lÄ±r MenÃ¼)
* **Pep MarkasÄ±** (AÃ§Ä±lÄ±r MenÃ¼)
* **Yeni Uygulama Ekle:** (OluÅŸtururken marka seÃ§ilir ve Hukuki Metin / Force Update gibi ayarlar buradan yapÄ±lÄ±r).

**âš½ MAÃ‡ & BOT YÃ–NETÄ°MÄ°**
* **TÃ¼m MaÃ§lar:** Manuel maÃ§ ekleme ve liste ekranÄ±.
* **HÄ±zlÄ± SonuÃ§landÄ±rma:** Bekleyen maÃ§larÄ±n operasyon ekranÄ±.
* **Bot YÃ¶netimi:** Bot Ã§alÄ±ÅŸma kurallarÄ± ve endpoint ayarlarÄ±.
* **Ligler:** Bot tarafÄ±ndan Ã§ekilen liglerin listesi (Logo & Ä°sim).
* **TakÄ±mlar:** Bot tarafÄ±ndan Ã§ekilen takÄ±mlarÄ±n listesi (Bayrak & Ä°sim).

**ğŸ‘¥ KULLANICI YÃ–NETÄ°MÄ°**
* **KullanÄ±cÄ± Listesi:** Ana tablo.
* **Onay Bekleyenler:** Badge ile sayÄ± gÃ¶sterilen hÄ±zlÄ± iÅŸlem alanÄ±.
* **GeliÅŸmiÅŸ Tarama (Ban Scanner):** Ekosistem geneli zincirleme hesap tespiti.

**ğŸš€ PAZARLAMA & Ä°LETÄ°Å?Ä°M**
* **Promosyon (Promo Engine):** Marka bazlÄ± kampanya yÃ¶netimi.
* **Destek Talepleri (Tickets):** Gelen mesaj kutusu.
* **E-posta Å?ablonlarÄ±:** Sadece Markalara Ã¶zel (Real iÃ§in, Pep iÃ§in) hoÅŸgeldin/onay mailleri tasarÄ±mÄ±.

**âš™ï¸? SÄ°STEM AYARLARI**
* **Genel & SMTP AyarlarÄ±**
* **Veri KaynaklarÄ±**
* **Yedekleme Merkezi**
* **Terminal & Ä°ÅŸlem GeÃ§miÅŸi (Audit Log)**

**ğŸ‘¤ ADMÄ°N PROFÄ°LÄ° (Ãœst MenÃ¼)**
* YÃ¶neticinin kendi ÅŸifresini, e-posta adresini ve gÃ¼venlik ayarlarÄ±nÄ± (2FA vb.) gÃ¼ncelleyebileceÄŸi hesap ayarlarÄ± sayfasÄ±.

### 6.8. Native Webview ve UX EntegrasyonlarÄ± (JS KÃ¶prÃ¼sÃ¼)
SPA Webview uygulamasÄ±nÄ±n sÄ±radan bir websitesi gibi deÄŸil, %100 Native (GerÃ§ek) mobil uygulama hissiyatÄ± vermesi iÃ§in alÄ±nacak teknik Ã¶nlemler:

* **Kopyalama/Zoom Engeli:** CSS (`user-select: none`) ve Meta tagler ile kullanÄ±cÄ±larÄ±n metin seÃ§mesi ve ekranÄ± bÃ¼yÃ¼tmesi (zoom) tamamen engellenir.
* **TitreÅŸim (Haptic Feedback) KÃ¶prÃ¼sÃ¼:** Alt menÃ¼lere veya maÃ§ kartlarÄ±na tÄ±klandÄ±ÄŸÄ±nda SPA, Android'deki `AndroidInterface.vibrate()` fonksiyonunu tetikleyerek telefona dokunsal geri bildirim (titreÅŸim) yollar.
* **Geri TuÅŸu (Back Button) YÃ¶nlendirmesi:** Android'in donanÄ±msal geri tuÅŸuna basÄ±ldÄ±ÄŸÄ±nda uygulamanÄ±n kapanmasÄ±nÄ± Ã¶nlemek iÃ§in JS History API (`pushState`) kullanÄ±lÄ±r. Geri tuÅŸu, SPA iÃ§indeki bir Ã¶nceki sekmeye kusursuzca dÃ¶ner.
* **Dinamik Ekran GÃ¶rÃ¼ntÃ¼sÃ¼ (SS) Engeli:** Android tarafÄ±ndaki `FLAG_SECURE` kodu, kullanÄ±cÄ±nÄ±n statÃ¼sÃ¼ne gÃ¶re dinamik yÃ¶netilir. KullanÄ±cÄ± uygulamayÄ± aÃ§tÄ±ÄŸÄ±nda API'den dÃ¶nen yanÄ±ta gÃ¶re JS kÃ¶prÃ¼sÃ¼ `AndroidInterface.setSecureFlag(true)` veya `false` gÃ¶nderir. YÃ¶neticiler/TestÃ§iler engelden muaf tutulabilir.
* **Resmi In-App Review (Uygulama Ä°Ã§i DeÄŸerlendirme):** "Bizi OylayÄ±n" modÃ¼lÃ¼ tetiklendiÄŸinde HTML modal yerine `AndroidInterface.showInAppReview()` JS kÃ¶prÃ¼sÃ¼ ile Google Play'in kendi resmi deÄŸerlendirme penceresi aÃ§Ä±lÄ±r.
* **Ekran AÃ§Ä±k Tutma (Keep Screen On / Wakelock):** KullanÄ±cÄ± Ã¶zellikle "Tahminler (BÃ¼lten)" sayfasÄ±ndayken ekranÄ±n zaman aÅŸÄ±mÄ±ndan kapanmamasÄ± iÃ§in JS kÃ¶prÃ¼sÃ¼ ile Android'in ekran uyanÄ±k tutma Ã¶zelliÄŸi aktif edilir. DiÄŸer sayfalarda normale dÃ¶ner.
* **Deep-Link Routing (Gelecek Vizyonu):** OneSignal bildirimlerine tÄ±klayan kullanÄ±cÄ±, uygulamanÄ±n sadece aÃ§Ä±lmasÄ±yla kalmaz; JS yÃ¶nlendirmesiyle direkt olarak bildirimle ilgili olan sekmeye (Ã–rn: GeÃ§miÅŸ maÃ§lar) otomatik yÃ¶nlendirilir.

---
## PROJE GELÄ°Å?TÄ°RME LOGLARI (AI YÃ–NETÄ°CÄ° GÃœNCELLEMELERÄ°)

**âœ… ADIM 1: VeritabanÄ± (SQL) Å?emasÄ± (TamamlandÄ± - 08.08.2026)**
* **Aksiyon:** `acms_documentation.md` dosyasÄ±ndaki 15 ana modÃ¼l ve tÃ¼m UX/GÃ¼venlik ayarlarÄ±nÄ± kapsayacak MySQL tablolarÄ± oluÅŸturuldu.
* **Kodlanan Tablolar:** `admins`, `brands`, `apps`, `users`, `user_apps`, `leagues`, `teams`, `matches`, `app_matches`, `tickets`, `promotions`, `app_promotions`, `email_templates`, `audit_logs`, `settings`.
* **Kritik Detaylar:** 
  * "Soft Delete" prensibi uygulandÄ± (`is_deleted` kolonlarÄ± eklendi).
  * CORS / Multi-domain mimarisi iÃ§in `apps` tablosuna `frontend_url` kolonu eklendi.
  * Webview JS kÃ¶prÃ¼sÃ¼ ve arayÃ¼z Ã¶zelleÅŸtirmeleri iÃ§in `nav_names_json`, `is_ios_allowed`, `exempt_security` gibi Ã¶zel ayar kolonlarÄ± yapÄ±landÄ±rÄ±ldÄ±.
* **Dosya Konumu:** `c:\xampp\htdocs\acms\database_schema.sql`
* **SÄ±radaki Bekleyen AdÄ±m:** Backend API motorunun (PDO baÄŸlantÄ±sÄ± ve dizin yapÄ±sÄ±nÄ±n) kurulmasÄ±.

**âœ… ADIM 2: Backend PDO BaÄŸlantÄ±sÄ± ve Dizin YapÄ±sÄ± (TamamlandÄ± - 08.08.2026)**
* **Aksiyon:** Vue 3 (CDN) frontend mimarisi kararÄ± belgelendi. Sistem klasÃ¶r aÄŸacÄ± (`/api`, `/admin`, `/app`) oluÅŸturuldu. Singleton deseninde gÃ¼venli PDO baÄŸlantÄ± motoru (`Database.php`) kodlandÄ±.
* **Kodlanan Dosyalar:** `api/config/Database.php`, `api/config/config.php`

**âœ… ADIM 3: Admin Paneli Kimlik DoÄŸrulama (Login) API'si (TamamlandÄ± - 08.08.2026)**
* **Aksiyon:** `/api/admin/login.php` uÃ§ noktasÄ± (endpoint) oluÅŸturuldu. POST metoduyla gelen JSON verileri iÅŸlendi. Bcrypt ile ÅŸifrelenmiÅŸ (`password_verify`) gÃ¼venli admin doÄŸrulamasÄ± yapÄ±ldÄ±. BaÅŸarÄ±lÄ± giriÅŸte PHP Session baÅŸlatÄ±lÄ±p CSRF/Auth iÅŸlemlerinde kullanÄ±labilecek benzersiz bir 'Session Token' dÃ¶ndÃ¼rÃ¼ldÃ¼.
* **Kodlanan Dosyalar:** `api/admin/login.php`

**âœ… ADIM 4: Admin Paneli ArayÃ¼zÃ¼ ve Route Guard (TamamlandÄ± - 08.08.2026)**
* **Aksiyon:** `/admin` klasÃ¶rÃ¼ oluÅŸturuldu. Minia Bootstrap 5 arayÃ¼z iskeleti kuruldu. `login.php` AJAX form tasarÄ±mÄ± yapÄ±larak API'ye baÄŸlandÄ± (BaÅŸarÄ±lÄ± giriÅŸte Token LocalStorage'a yazÄ±ldÄ±). `index.php` Dashboard anasayfasÄ±, dÃ¶kÃ¼mandaki 6.7 madde hiyerarÅŸisine uygun sol menÃ¼yle (Sidebar) tasarlandÄ±. Route Guard ile yetkisiz giriÅŸler JS katmanÄ±nda engellendi.
* **Kodlanan Dosyalar:** `admin/login.php`, `admin/index.php`
* **SÄ±radaki Bekleyen AdÄ±m:** Uygulamalar (Apps) YÃ¶netimi - Yeni Uygulama Ekleme API'si ve ArayÃ¼z Formu.

**âœ… ADIM 5: Uygulama (Apps) YÃ¶netimi API'si ve ArayÃ¼zÃ¼ (KÄ±smen TamamlandÄ± - 08.08.2026)**
* **Aksiyon:** `/api/admin/apps.php` oluÅŸturuldu ve GET/POST/UPDATE/DELETE iÅŸlemleri entegre edildi. `apps_list.php`, `app_add.php` ve `app_edit.php` sayfalarÄ± asenkron form iÅŸlemleri ve dosya yÃ¼kleme (Logo/Favicon) destekleyecek ÅŸekilde kuruldu. Mockup statik bÄ±rakÄ±ldÄ±. `preview.php` dosyasÄ± silindi.
* **Kodlanan Dosyalar:** `api/admin/apps.php`, `admin/apps_list.php`, `admin/app_add.php`, `admin/app_edit.php`

**âœ… ADIM 6: MaÃ§ (Tahmin) YÃ¶netimi API'si ve ArayÃ¼zÃ¼ (TamamlandÄ± - 09.08.2026)**
* **Aksiyon:** `/api/admin/matches.php` API ucu oluÅŸturuldu (Ekleme, DÃ¼zenleme, Silme, Toplu/Tekil SonuÃ§landÄ±rma). `matches_list.php` sayfasÄ±nda maÃ§ havuzu listelendi ve skora dayalÄ± UI rozetleri eklendi. `match_add.php` Ã¼zerinden tek maÃ§Ä±n Ã§oklu uygulamalara daÄŸÄ±tÄ±mÄ± (Multi-App) entegre edildi ve hÄ±zlÄ± maÃ§ ekleme akÄ±ÅŸÄ± tasarlandÄ±. `match_edit.php` sayfasÄ± ile var olan maÃ§larÄ±n detaylÄ± gÃ¼ncellenebilmesi saÄŸlandÄ±.
* **Kodlanan Dosyalar:** `api/admin/matches.php`, `admin/matches_list.php`, `admin/match_add.php`, `admin/match_edit.php`
* **UI/UX Eklemeleri:** MaÃ§ listesinde StateSave (filtre hafÄ±zasÄ±), Toast (saÄŸ Ã¼st bildirim) ve HÄ±zlÄ± SonuÃ§landÄ±rma (âœ”/âœ– + Skor) ile Soft Delete (Ã§Ã¶p kutusu) iÅŸlemleri AJAX ile sayfa yenilenmeden Ã§alÄ±ÅŸacak ÅŸekilde arayÃ¼ze gÃ¶mÃ¼ldÃ¼. TÃ¼m sayfalardaki sol menÃ¼ (Sidebar) tek bir bileÅŸen (`sidebar.php`) haline getirilip dinamik linkleme yapÄ±ldÄ±.
* **SÄ±radaki Bekleyen AdÄ±m:** Bot & Firebase Entegrasyonu (AdÄ±m 6.5) - Yeni maÃ§ eklendiÄŸinde seÃ§ili uygulamalara Push Notification atÄ±lmasÄ±.

**âœ… ADIM 7: Bot SÃ¶zlÃ¼ÄŸÃ¼ - Ligler ve TakÄ±mlar (TamamlandÄ± - 09.08.2026)**
* **Aksiyon:** MaÃ§ ekleme ekranÄ±nda kullanÄ±lacak ve maÃ§larÄ± gruplayacak olan "Ligler" ve "TakÄ±mlar" veritabanÄ± tablolarÄ±nÄ±n CRUD iÅŸlemleri iÃ§in API uÃ§larÄ± ve arayÃ¼zleri yazÄ±ldÄ±.
* **Kodlanan Dosyalar:** `api/admin/leagues.php`, `api/admin/teams.php`, `admin/leagues.php`, `admin/teams.php`
* **UI/UX Eklemeleri:** Lig ve TakÄ±m ekleme/dÃ¼zenleme iÅŸlemleri iÃ§in Modal yapÄ±sÄ± kullanÄ±ldÄ±. AJAX ve Toast bildirimleri ile sayfa yenilenmeden hÄ±zlÄ± veri giriÅŸi saÄŸlandÄ±. Datatables ile listeleme ve arama aktifleÅŸtirildi.
* **Ã–nemli Hata Giderme Notu (app.js BaÄŸÄ±mlÄ±lÄ±ÄŸÄ±):** Minia temasÄ±nÄ±n Ã§ekirdek JavaScript dosyasÄ± (`app.js`), sayfa yapÄ±sÄ±nda saÄŸ tarafta aÃ§Ä±lan "Theme Customizer" paneline (`.right-bar`) ve oradaki inputlara sÄ±kÄ± sÄ±kÄ±ya baÄŸlÄ±dÄ±r. Bu panel veya HTML kodlarÄ± DOM'da olmadÄ±ÄŸÄ±nda `app.js` "TypeError: Cannot set properties of null (setting 'checked')" hatasÄ± fÄ±rlatÄ±r ve betik durur. Bu da en sonda Ã§alÄ±ÅŸan `feather.replace()` fonksiyonunun tetiklenmemesine ve sayfa ikonlarÄ±nÄ±n (topbar, menÃ¼) yÃ¼klenmemesine/sayfanÄ±n bozuk gÃ¶rÃ¼nmesine neden olur. **Gelecekte oluÅŸturulacak tÃ¼m yeni sayfalarda (Ã¶rneÄŸin kullanÄ±cÄ±lar), `footer` ve gizli de olsa `.right-bar` HTML bloÄŸu `<div id="layout-wrapper">` kapandÄ±ktan sonra sayfaya BÄ°REBÄ°R eklenmek ZORUNDADIR.**

**âœ… ADIM 8.0: Mimari Temizlik ve Core JS Entegrasyonu (TamamlandÄ±)**
* **Aksiyon:** Sayfalardaki tekrar eden kodlarÄ± azaltmak ve merkezi yÃ¶netimi saÄŸlamak adÄ±na ortak JavaScript fonksiyonlarÄ± tek bir dosyada birleÅŸtirildi.
* **Detaylar:** `Route Guard` (login kontrolÃ¼), `adminName` yazdÄ±rma iÅŸlemleri ve `logout()` fonksiyonu `acms-core.js` dosyasÄ±nda toplanÄ±p sayfalara include edildi. Gereksiz satÄ±r iÃ§i (inline) betikler silindi.
* **Kritik Not:** Bundan sonra eklenecek olan tÃ¼m sayfalarda (login.php hariÃ§) en alta `<script src="assets/js/acms-core.js"></script>` import edilmek ZORUNDADIR. AyrÄ±ca bundan sonra yapÄ±lacak tÃ¼m gÃ¼ncellemeler dÃ¶kÃ¼mantasyon dosyasÄ±nÄ±n loglar bÃ¶lÃ¼mÃ¼ne anlÄ±k olarak iÅŸlenecektir.

**âœ… ADIM 8.1: KullanÄ±cÄ± YÃ¶netimi API (TamamlandÄ±)**
* **Aksiyon:** `api/admin/users.php` oluÅŸturuldu. `users`, `user_apps` ve `apps` tablolarÄ± kullanÄ±larak kullanÄ±cÄ± verilerinin yÃ¶netimi iÃ§in API uÃ§larÄ± kodlandÄ±.
* **Detaylar:**
  * **GET (list):** TÃ¼m kullanÄ±cÄ±larÄ± (`user_apps` bazlÄ±), uygulamalarÄ±yla birlikte listeleme.
  * **POST (update_status):** Belirli bir kullanÄ±cÄ±nÄ±n belirli bir uygulamadaki durumunu (approved/rejected) gÃ¼ncelleme.
  * **POST (ban):** KullanÄ±cÄ±yÄ± global olarak yasaklama (`is_banned = 1`) ve baÄŸlÄ± olduÄŸu tÃ¼m uygulamalardaki Ã¼yelik statÃ¼sÃ¼nÃ¼ `rejected` yapma (Cross-Ban/Ã‡apraz Ban).
* **SÄ±radaki Bekleyen AdÄ±m:** KullanÄ±cÄ± Listesi ArayÃ¼zÃ¼ (AdÄ±m 8.2).

**âœ… ADIM 8.2: KullanÄ±cÄ± Listesi ArayÃ¼zÃ¼ (TamamlandÄ±)**
* **Aksiyon:** `admin/users_list.php` sayfasÄ± oluÅŸturuldu ve `sidebar.php`'deki menÃ¼ linki gÃ¼ncellendi.
* **Detaylar:**
  * **Tablo (DataTables):** KullanÄ±cÄ± ID, Ad Soyad, E-posta, Uygulama, StatÃ¼ (Badge destekli), GPA Kodu ve KayÄ±t Tarihi bilgileri AJAX ile tabloya basÄ±ldÄ±.
  * **Ä°ÅŸlemler:** Her satÄ±ra "Onayla" (Approved), "Reddet" (Rejected) ve "Banla" butonlarÄ± eklendi.
  * **SweetAlert Entegrasyonu:** Banlama iÅŸleminde yÃ¶neticiye Ban Sebebi sorulan bir prompt yapÄ±sÄ± (SweetAlert2) kuruldu. Ä°ÅŸlemlerden sonra otomatik sayfa yenileme aktifleÅŸtirildi.
* **SÄ±radaki Bekleyen AdÄ±m:** KullanÄ±cÄ± Profili ve DÃ¼zenleme EkranÄ±.

**âœ… ADIM 8.3: Yeni KullanÄ±cÄ± Ekleme ve Profil DÃ¼zenleme (TamamlandÄ±)**
* **Aksiyon:** `admin/user_edit.php` oluÅŸturuldu ve `api/admin/users.php` dosyasÄ±na CRUD iÅŸlemleri eklendi. `users_list.php` sayfasÄ±na yÃ¶nlendirme butonlarÄ± yerleÅŸtirildi.
* **Detaylar:**
  * **Frontend (`user_edit.php`):** Ä°ki modlu (Ekleme ve DÃ¼zenleme) Ã§alÄ±ÅŸan ortak form sayfasÄ± tasarlandÄ±. DÃ¼zenleme modunda saÄŸ tarafa Log Bilgileri (Son GiriÅŸ IP/Tarih) ve KayÄ±tlÄ± OlduÄŸu VIP Uygulamalar listesi (salt okunur) eklendi.
  * **Backend (`users.php`):** `get_user` ve `save_user` API uÃ§larÄ± eklendi. Å?ifre iÅŸlemleri gÃ¼venli `password_hash` ile bcrypt kullanÄ±larak ÅŸifrelendi. Å?ifre alanÄ± boÅŸ bÄ±rakÄ±ldÄ±ÄŸÄ±nda ÅŸifrenin deÄŸiÅŸmemesi mantÄ±ÄŸÄ± kurgulandÄ±. Hata yÃ¶netimiyle Ã§ift e-posta kaydÄ± engellendi (`23000` SQL hatasÄ±).
* **SÄ±radaki Bekleyen AdÄ±m:** Onay Bekleyen VIP KayÄ±tlarÄ± ve Destek Talepleri (AdÄ±m 8.4).

**âœ… ADIM 8.4: KullanÄ±cÄ± Havuzu ve GeliÅŸmiÅŸ Filtreleme (TamamlandÄ±)**
* **Aksiyon:** AyrÄ± bir "Onay Bekleyenler" sayfasÄ± yaratmak yerine `users_list.php` sayfasÄ± merkezi bir havuza dÃ¶nÃ¼ÅŸtÃ¼rÃ¼ldÃ¼ ve filtre sistemi eklendi.
* **Detaylar:**
  * **Filtreleme ArayÃ¼zÃ¼:** Sayfaya StatÃ¼ (TÃ¼mÃ¼, Bekleyen, Onaylanan, Reddedilen, BanlÄ±) ve Uygulama filtrelerini barÄ±ndÄ±ran yatay bir kart eklendi. SeÃ§imlere gÃ¶re AJAX tablosu yeniden yÃ¼klenmektedir.
  * **URL Parametre DesteÄŸi:** Sayfa aÃ§Ä±lÄ±rken URL'deki `?status=` veya `?app_id=` parametrelerini algÄ±layÄ±p otomatik filtreleme yapan mantÄ±k kuruldu (Ã¶rn. `users_list.php?status=pending`).
  * **Sidebar:** `sidebar.php`'deki Onay Bekleyenler menÃ¼sÃ¼ gÃ¼ncellenerek bu parametreli linke yÃ¶nlendirildi.
  * **Backend (`users.php`):** `action=list` API'si yenilenerek gelen `status` ve `app_id` deÄŸiÅŸkenlerine gÃ¶re dinamik SQL sorgusu (`$whereClause`) Ã¼reten yapÄ± kuruldu.

**âœ… ADIM 8.5: Ã‡oklu Hesap ve Benzer Ãœyelik Tespiti (TamamlandÄ±)**
* **Aksiyon:** Anti-Fraud Ã¶nlemleri kapsamÄ±nda, sisteme aynÄ± IP'den veya benzer e-posta adreslerinden kayÄ±t olan farklÄ± kullanÄ±cÄ±larÄ± tespit eden bir uyarÄ± modÃ¼lÃ¼ eklendi.
* **Detaylar:**
  * **Backend (`users.php`):** `get_user` API ucu geniÅŸletildi. Mevcut kullanÄ±cÄ±nÄ±n IP adresi veya e-posta prefix'ine (Ã¶rn. '@' Ã¶ncesi kÄ±sÄ±ma) uyan diÄŸer kullanÄ±cÄ±lar veritabanÄ±ndan Ã§ekilerek JSON `similar_users` dizisine eklendi.
  * **Frontend (`user_edit.php`):** SaÄŸ kolona kÄ±rmÄ±zÄ± Ã§erÃ§eveli "ğŸš¨ Benzer Ãœyelikler (Risk)" kartÄ± entegre edildi. API'den dÃ¶nen benzer Ã¼yeler bu karta basÄ±lÄ±r. Ä°lgili kullanÄ±cÄ±nÄ±n adÄ±na tÄ±klanarak yeni sekmede profiline gidilebilir. BanlÄ± benzer Ã¼yelerin yanÄ±nda belirleyici siyah "BanlÄ±" rozeti gÃ¶sterilir.

**âœ… ADIM 8.6: Toplu Ä°ÅŸlemler ve Export (TamamlandÄ±)**
* **Aksiyon:** `users_list.php` sayfasÄ±na Ã§oklu seÃ§im (checkbox) ve toplu iÅŸlem (bulk action) modÃ¼lÃ¼ ile DataTables DÄ±ÅŸa AktarÄ±m butonlarÄ± eklendi.
* **Detaylar:**
  * **Frontend (`users_list.php`):** Tabloya "TÃ¼mÃ¼nÃ¼ SeÃ§" ve satÄ±r iÃ§i Checkbox Ã¶zellikleri eklendi. Tablonun hemen Ã¼stÃ¼ne "Toplu Ä°ÅŸlem SeÃ§in" Dropdown'Ä± (Onayla, Reddet, Beklemeye Al, Banla, OnaysÄ±zlarÄ± Sil) entegre edildi. SatÄ±r iÃ§i iÅŸlem butonlarÄ±na "Beklemeye Al" butonu da dahil edildi.
  * **DataTables Export:** `jszip` ve `pdfmake` kÃ¼tÃ¼phaneleri sayfaya include edilerek DataTables `dom: 'Bfrtip'` parametresi aktifleÅŸtirildi. ArtÄ±k tablo Ã¼zerindeki Excel, PDF ve Copy butonlarÄ± ile mevcut liste anÄ±nda dÄ±ÅŸa aktarÄ±labiliyor.
  * **Backend (`users.php`):** Toplu iÅŸlemleri karÅŸÄ±layabilmesi iÃ§in `action=bulk_update` adlÄ± yeni bir endpoint yazÄ±ldÄ±. `user_ids` (dizi) ve `bulk_action` parametreleriyle toplu `UPDATE` veya `DELETE` (sadece pending iÃ§in) iÅŸlemleri veritabanÄ± Transaction bÃ¼tÃ¼nlÃ¼ÄŸÃ¼ gÃ¶zetilerek (beginTransaction / commit) iÅŸleniyor.

**âœ… ADIM 8.7: Global Ã‡Ã¶p Kutusu (Trash) ModÃ¼lÃ¼ (TamamlandÄ±)**
* **Aksiyon:** Sisteme kalÄ±cÄ± veri silinmelerine karÅŸÄ± koruma saÄŸlayacak (Soft-Delete) Global Ã‡Ã¶p Kutusu arayÃ¼zÃ¼ ve API katmanÄ± geliÅŸtirildi.
* **Detaylar:**
  * **VeritabanÄ± (Migration):** `users` tablosuna `is_deleted TINYINT(1) DEFAULT 0` kolonu eklendi (diÄŸer tablolarda mevcuttu). `api/admin/users.php` iÃ§indeki `list` action'Ä± sadece aktif kullanÄ±cÄ±larÄ± (`is_deleted=0`) dÃ¶ndÃ¼recek ÅŸekilde gÃ¼ncellendi.
  * **Backend API (`api/admin/trash.php`):** Sistemin 5 ana tablosunu (apps, matches, leagues, teams, users) tarayan ve `is_deleted=1` olan kayÄ±tlarÄ± JSON objesinde toplayan `action=list` geliÅŸtirildi. AyrÄ±ca `restore` (is_deleted=0) ve `hard_delete` (DELETE FROM) iÅŸlevleri yazÄ±ldÄ±.
  * **Frontend UI (`admin/trash.php`):** Yatay sekme (Bootstrap Nav-Tabs) mantÄ±ÄŸÄ± kurularak; Uygulamalar, MaÃ§lar, Ligler, TakÄ±mlar ve KullanÄ±cÄ±lar adlÄ± 5 kategori sayfa ortasÄ±nda DataTables formunda render edildi. `restoreItem` ve `hardDeleteItem` Ã¶zellikleri eklendi. Ä°ÅŸlem tamamlandÄ±ÄŸÄ±nda SweetAlert sonrasÄ± veri sekme kaybolmadan (AJAX ile `loadTrashData()`) yeniden Ã§iziliyor.
  * **MenÃ¼:** Sidebar (`admin/sidebar.php`) "GÃ¼venlik" altÄ±na kÄ±rmÄ±zÄ± logolu `Ã‡Ã¶p Kutusu` eklendi. Ã‡Ã¶p kutusu ve sekme baÅŸlÄ±klarÄ±na anlÄ±k (dinamik) Ã¶ÄŸe sayÄ±sÄ± sayacÄ± (Badge) eklendi.

**âœ… ADIM 8.8: Sol MenÃ¼ (Sidebar) Optimizasyonu ve UX Ä°yileÅŸtirmeleri (TamamlandÄ±)**
* **Aksiyon:** Sol menÃ¼nÃ¼n (Sidebar) kullanÄ±cÄ± deneyimi iyileÅŸtirildi, daha derli toplu ve profesyonel bir gÃ¶rÃ¼nÃ¼me kavuÅŸturuldu.
* **Detaylar:**
  * **HiyerarÅŸi GÃ¼ncellemesi:** "Uygulamalar" ve "Yeni Uygulama Ekle" linkleri **"Uygulama Ä°ÅŸlemleri"** adlÄ± aÃ§Ä±lÄ±r (dropdown) menÃ¼ye; "Ligler" ve "TakÄ±mlar" sayfalarÄ± ise "TÃ¼m MaÃ§lar" ile birlikte **"MaÃ§ Ä°ÅŸlemleri"** aÃ§Ä±lÄ±r menÃ¼sÃ¼ne taÅŸÄ±narak kalabalÄ±k gÃ¶rÃ¼ntÃ¼ giderildi. Aktif kullanÄ±lan sayfalar Ã¼st tarafa, yapÄ±m aÅŸamasÄ±nda olanlar (Pasif) en alta hizalandÄ±.
  * **Kayan Animasyonun KaldÄ±rÄ±lmasÄ±:** Minia temasÄ±nda sayfa her yÃ¼klendiÄŸinde seÃ§ili menÃ¼ye doÄŸru sayfayÄ± kaydÄ±ran (scroll) rahatsÄ±z edici `animate({scrollTop:t})` animasyonu `app.js` dosyasÄ±ndan tamamen kaldÄ±rÄ±larak yerine anÄ±nda odaklanma Ã¶zelliÄŸi getirildi.
  * **Aktif MenÃ¼ Vurgusu:** Bulunulan sayfanÄ±n sidebar'da daha net belli olmasÄ± iÃ§in `acms-core.js` iÃ§erisinden Global CSS ÅŸÄ±rÄ±nga edildi (Mavi arkaplan ve sol border Ã§izgisi).
  * **Dinamik Ã‡Ã¶p Kutusu SayacÄ±:** API'ye Ã§ok hafif bir `action=trash_count` eklendi ve tÃ¼m admin sayfalarÄ±nda sol menÃ¼deki Ã§Ã¶p kutusunun yanÄ±nda, Ã§Ã¶p kutusundaki toplam silinmiÅŸ Ã¶ÄŸe sayÄ±sÄ±nÄ±n kÄ±rmÄ±zÄ± rozet (badge) ile canlÄ± olarak (F5 gerektirmeden) gÃ¶rÃ¼nmesi saÄŸlandÄ±.

* **SÄ±radaki Bekleyen AdÄ±m:** Dashboard (Ana Sayfa) Ä°statistikleri ve Grafik EntegrasyonlarÄ± (AdÄ±m 9).

**âœ… ADIM 9: Dashboard Ä°statistikleri ve Grafik EntegrasyonlarÄ± (TamamlandÄ±)**
* **Aksiyon:** Sistemin ana yÃ¶netim paneli olan (Dashboard) sayfasÄ±, tÃ¼m uygulamalardan toplanan verileri gÃ¶sterecek ÅŸekilde tamamen canlÄ± (dinamik) hale getirildi.
* **Detaylar:**
  * **Backend API (`api/admin/dashboard.php`):** Dashboard iÃ§in gerekli olan tÃ¼m istatistikleri tek bir objede dÃ¶ndÃ¼ren GET endpoint'i oluÅŸturuldu. VeritabanÄ±ndan `pending_users_count`, `total_active_users`, `total_apps`, `today_matches_count`, `hit_rate` (son 30 gÃ¼nlÃ¼k BaÅŸarÄ± YÃ¼zdesi / ROI), `users_growth` (grafik iÃ§in son 30 gÃ¼n), `match_results` ve `recent_users` (son 10 aktif Ã¼ye) asenkron Ã§aÄŸrÄ±lar iÃ§in baÅŸarÄ±yla derlendi.
  * **Frontend UI (`admin/index.php`):** Minia temasÄ±nÄ±n standart kurgusundan kurtarÄ±larak baÅŸtan inÅŸa edildi. 
    * **Acil Aksiyon UyarÄ±larÄ±:** `pending_users_count` sÄ±fÄ±rdan bÃ¼yÃ¼k ise en tepede kÄ±rmÄ±zÄ± bir alert gÃ¶sterilir ve onay iÃ§in `users_list.php?status=pending` linkine yÃ¶nlendirir.
    * **Widget'lar:** Aktif KullanÄ±cÄ±, MaÃ§ SayÄ±sÄ±, Uygulama ve BaÅŸarÄ± YÃ¼zdesi 4'lÃ¼ kart sistemiyle gÃ¶sterildi.
    * **Grafikler (ApexCharts):** Son 30 gÃ¼nlÃ¼k bÃ¼yÃ¼me eÄŸrisi "Area Chart" ile, maÃ§ sonuÃ§larÄ± (WIN/LOSE/PENDING) "Donut Chart" ile canlÄ± olarak render edildi.
    * **Son KayÄ±tlar:** Sisteme giriÅŸ yapan son 10 kullanÄ±cÄ±nÄ±n yer aldÄ±ÄŸÄ± tablo alt kÄ±sÄ±ma eklendi ve durumlarÄ±na Ã¶zel rozetler tanÄ±mlandÄ±.
    * **Mimari Entegrasyon:** Sayfa sonuna `acms-core.js` eklenerek sidebar rozetlerinin ve kullanÄ±cÄ± oturum kontrolÃ¼nÃ¼n devrede olmasÄ± saÄŸlandÄ±.
  * **Hata Giderimi (Bugfix):** KullanÄ±cÄ± onay durumlarÄ±nÄ±n (`status`) `users` tablosu yerine `user_apps` tablosunda tutulduÄŸu fark edilerek API'deki ilgili SQL sorgularÄ± `JOIN user_apps ua` mantÄ±ÄŸÄ±yla gÃ¼ncellendi (SÄ±fÄ±r dÃ¶nme hatasÄ± giderildi).
* **SÄ±radaki Bekleyen AdÄ±m:** Terminal & Ä°ÅŸlem LoglarÄ± (Audit Log) (AdÄ±m 10).

**âœ… ADIM 10: Terminal & Ä°ÅŸlem LoglarÄ± (TamamlandÄ±)**
* **Aksiyon:** Sistem yÃ¶neticilerinin (Admin) panel Ã¼zerinden yaptÄ±ÄŸÄ± tÃ¼m Ã¶nemli veri manipÃ¼lasyon iÅŸlemlerini kayÄ±t altÄ±na alan merkezi "Audit Log" (Denetim Ä°zi) altyapÄ±sÄ± kuruldu.
* **Detaylar:**
  * **Core Entegrasyonu (`audit_helper.php`):** Her iÅŸlemde Ã§aÄŸrÄ±labilmesi iÃ§in PDO kullanarak iÅŸlemleri `audit_logs` tablosuna (admin_id, action, details, ip_address, created_at) basan `log_action()` fonksiyonu oluÅŸturuldu. 
  * **API Tetikleyicileri:** 
    * `users.php`: Banlama, Ban kaldÄ±rma, Durum GÃ¼ncelleme ve Toplu Ä°ÅŸlemler loglandÄ±.
    * `apps.php`: Uygulama ekleme, silme (soft delete) ve gÃ¼ncelleme iÅŸlemleri loglandÄ±.
    * `matches.php`: MaÃ§ ekleme, dÃ¼zenleme, tekil/toplu skor sonuÃ§landÄ±rma (resolve) ve silme iÅŸlemleri loglandÄ±.
    * `trash.php`: Ã‡Ã¶p kutusundan geri getirme (restore) ve kalÄ±cÄ± silme (hard delete) iÅŸlemleri loglandÄ±.
  * **Log Ä°zleme API (`api/admin/audit_logs.php`):** Admin panelindeki arayÃ¼ze loglarÄ± en yenisi en Ã¼stte (DESC) olacak ÅŸekilde limitli JSON formatÄ±nda sunan endpoint yazÄ±ldÄ±.
  * **Frontend UI (`admin/audit_logs.php`):** DataTables destekli; dÄ±ÅŸa aktarÄ±m (PDF, Excel, Kopyalama) Ã¶zellikleri olan, log detaylarÄ±nÄ± listeleyen yeni bir sayfa kodlandÄ±. Ä°ÅŸlem tiplerine gÃ¶re gÃ¶rsel rozetlendirme (silme=kÄ±rmÄ±zÄ±, ekleme=yeÅŸil vb.) JS iÃ§erisinde yapÄ±ldÄ±.
  * **YÃ¶nlendirme:** `sidebar.php` Ã¼zerinde "Terminal & Ä°ÅŸlem LoglarÄ±" menÃ¼sÃ¼ aktif hale getirilip, bu sayfaya baÄŸlandÄ±.
* **SÄ±radaki Bekleyen AdÄ±m:** Bot YÃ¶netimi (AdÄ±m 12).

**âœ… ADIM 12: Bot YÃ¶netimi ve Ã–nizleme Merkezi (TamamlandÄ±)**
* **Aksiyon:** Sistemin dÄ±ÅŸ API kaynaÄŸÄ±ndan JSON verisini Ã§ekip, maÃ§larÄ± uygulamalara dinamik olarak daÄŸÄ±tmasÄ±nÄ± saÄŸlayan "Test/Ã–nizleme" odaklÄ± akÄ±llÄ± senkronizasyon sistemi kuruldu.
* **Detaylar:**
  * **VeritabanÄ± GÃ¼ncellemesi:** `apps` tablosuna uygulamanÄ±n hangi kuponu Ã§ekeceÄŸini belirleyen `bot_coupon_name` kolonu eklendi.
  * **Frontend UI Revizyonu (`admin/bot.php`):** Ä°ki sekmeli profesyonel bir yapÄ±ya geÃ§ildi.
    * **Tab 1 (EÅŸleÅŸtirme - Mapping):** "Kupon Ä°simlerini Getir" butonuyla (`bot_coupons.php` Ã¼zerinden) hedef sitedeki benzersiz kupon adlarÄ± anlÄ±k Ã§ekilip dropdown'lara dolduruldu.
    * **Tab 2 (Operasyon Merkezi):** Datepicker ile tarih seÃ§ip "Ã–nizleme Yap" denildiÄŸinde, hiÃ§bir veritabanÄ± kaydÄ± yapÄ±lmadan sonuÃ§larÄ±n (Lig, MaÃ§, Skor, Tahmin, Hedef Uygulamalar) DataTables Ã¼zerinde gÃ¶sterilmesi saÄŸlandÄ±. Tablonun altÄ±na nabÄ±z (pulse) animasyonlu "Sisteme Aktar (Sync)" butonu ve loglarÄ±n akacaÄŸÄ± Terminal yerleÅŸtirildi.
  * **Backend API'leri:**
    * `api/admin/bot_coupons.php`: Hedef JSON kaynaÄŸÄ±ndaki tarihleri tarayarak benzersiz (array_unique) kupon anahtarlarÄ±nÄ± arayÃ¼ze dÃ¶ndÃ¼rÃ¼r.
    * `api/admin/bot_preview.php`: SeÃ§ilen tarihteki eÅŸleÅŸmiÅŸ kuponlarÄ± bulur. AynÄ± maÃ§Ä±n (Ã–rn: Galatasaray - FenerbahÃ§e) farklÄ± kuponlarda yer almasÄ± durumunda bu maÃ§Ä± tek satÄ±rda birleÅŸtirip (aggregate) hedef uygulamalarÄ± virgÃ¼lle/badge ile yan yana ekleyerek Test Modu (JSON) Ã§Ä±ktÄ±sÄ± verir. VeritabanÄ±na yazmaz.
    * `api/admin/bot_sync.php`: Ã–nizleme onayÄ± sonrasÄ± Ã§alÄ±ÅŸÄ±r. Transaction kullanarak birleÅŸmiÅŸ maÃ§larÄ± `leagues` ve `matches` tablolarÄ±na kaydeder/gÃ¼nceller ve o maÃ§Ä±n baÄŸlÄ± olduÄŸu tÃ¼m uygulamalarÄ± `app_matches` tablosuna Ã§apraz referans (Ã§ok-a-Ã§ok) olarak ekler.
  * **GÃ¶rsel ZenginleÅŸtirme:** MaÃ§ Ã¶nizlemelerine ve senkronizasyon altyapÄ±sÄ±na Lig, Ev Sahibi ve Deplasman logolarÄ±nÄ±n (flagUrl, homeLogo, awayLogo) entegrasyonu saÄŸlandÄ±.
  * **Bot SÃ¶zlÃ¼ÄŸÃ¼:** Botun dÄ±ÅŸ kaynaktan Ã§ektiÄŸi takÄ±mlarÄ± otomatik ayrÄ±ÅŸtÄ±rarak `teams` tablosuna logolarÄ±yla birlikte (Dictionary mantÄ±ÄŸÄ±yla) eklemesi saÄŸlandÄ±.
* **SÄ±radaki Bekleyen AdÄ±m:** Sistem SaÄŸlÄ±ÄŸÄ± ve VeritabanÄ± Yedekleme (Backup) (AdÄ±m 11).

**âœ… ADIM 14: Bot Komuta Merkezi (Dashboard) ve Makro Butonlar (TamamlandÄ±)**
* **Aksiyon:** Temel test aracÄ± olarak tasarlanan Bot YÃ¶netim modÃ¼lÃ¼, kapsamlÄ± bir "Bot Komuta Merkezi"ne dÃ¶nÃ¼ÅŸtÃ¼rÃ¼ldÃ¼.
* **Detaylar:**
  * **Sekmeli Mimari Revizyonu:** `admin/bot.php` 3 ana sekmeye bÃ¶lÃ¼ndÃ¼.
    1. **Operasyon Merkezi (Ana Sekme):** Ã–nizleme, senkronizasyon ve canlÄ± loglarÄ±n aktÄ±ÄŸÄ± terminal burada toplandÄ±. AyrÄ±ca "DÃ¼nÃ¼ SonuÃ§landÄ±r", "BugÃ¼nÃ¼ Ã‡ek", "YarÄ±nÄ± Ã‡ek" gibi makro hÄ±zlÄ± butonlarla operasyon hÄ±zÄ± artÄ±rÄ±ldÄ±. Terminalin altÄ±na `bot_recent.php` den beslenen ve botun yaptÄ±ÄŸÄ± son 10 iÅŸlemi gÃ¶steren anlÄ±k durum tablosu eklendi.
    2. **Uygulama EÅŸleÅŸtirmeleri:** Kupon eÅŸleÅŸtirme listesi (mapping) ayrÄ± bir sekmeye taÅŸÄ±ndÄ±.
    3. **Bot AyarlarÄ±:** Hedef API url'si, Ã§ekim saatleri, sonuÃ§landÄ±rma sÄ±klÄ±ÄŸÄ± ve botun aÃ§Ä±k/kapalÄ± durumu gibi parametreleri yÃ¶neten yeni sekme eklendi.
  * **Settings AltyapÄ±sÄ±:** Dinamik ayarlarÄ±n tutulmasÄ± iÃ§in `settings` veritabanÄ± tablosu (`setting_key`, `setting_value`) oluÅŸturuldu ve backend API uÃ§ noktasÄ± (`settings.php`) geliÅŸtirildi.
  * **Fallback Logolar:** MaÃ§ havuzu ve bot Ã¶nizleme ekranlarÄ±nda kÄ±rÄ±k logolarÄ±n (404) sayfa tasarÄ±mÄ±nÄ± bozmasÄ±nÄ± Ã¶nlemek iÃ§in `onerror` attritbÃ¼tÃ¼ ile varsayÄ±lan (fallback) profil gÃ¶rseline dÃ¶nÃ¼lmesi saÄŸlandÄ±. TakÄ±m ve lig ekleme API'lerinde (bot_sync.php) logonun yalnÄ±zca mevcut deÄŸilse (boÅŸsa) gÃ¼ncellenmesini saÄŸlayacak koruyucu kontroller eklendi.
* **SÄ±radaki Bekleyen AdÄ±m:** Sistem SaÄŸlÄ±ÄŸÄ± ve VeritabanÄ± Yedekleme (Backup) (AdÄ±m 11).

**âœ… ADIM 15: GÃ¼venlik, UI/UX Ä°yileÅŸtirmeleri ve Ã‡Ã¶p Kutusu MakrolarÄ± (TamamlandÄ±)**
* **Aksiyon:** Sistemin kullanÄ±m kolaylÄ±ÄŸÄ± artÄ±rÄ±ldÄ± ve hatalÄ± silinmiÅŸ kayÄ±tlar iÃ§in toplu kurtarma/silme araÃ§larÄ± sisteme entegre edildi.
* **Detaylar:**
  * **Ã‡Ã¶p Kutusu (Trash) Toplu Ä°ÅŸlemler:** `admin/trash.php` iÃ§erisindeki Uygulamalar, MaÃ§lar, Ligler, TakÄ±mlar ve KullanÄ±cÄ±lar sekmelerine Checkbox ile toplu seÃ§im Ã¶zelliÄŸi getirildi. "SeÃ§ilenleri Geri YÃ¼kle" (Bulk Restore) ve "SeÃ§ilenleri KalÄ±cÄ± Sil" (Bulk Hard Delete) fonksiyonlarÄ± API'ye (`api/admin/trash.php`) ve arayÃ¼ze entegre edildi.
  * **Undelete AlgoritmasÄ±:** Bot verileri otomatik eÅŸleÅŸtirirken, daha Ã¶nce silinmiÅŸ (soft delete) olan lig veya takÄ±mlarÄ± tekrar tespit ederse, bunlarÄ± otomatik olarak "Aktif" (is_deleted=0) duruma getirecek zeka `bot_sync.php` iÃ§ine kodlandÄ±.
  * **MenÃ¼ Ã–nceliklendirmesi:** "Sistem AyarlarÄ±" altÄ±nda pasif olan `bot.php` (Bot Komuta Merkezi) ve `audit_logs.php` (Ä°ÅŸlem LoglarÄ±) sayfalarÄ±, "OTOMASYON & Ä°ZLEME" adlÄ± yeni bir aktif ana menÃ¼ baÅŸlÄ±ÄŸÄ± altÄ±nda yukarÄ± taÅŸÄ±ndÄ±.
  * **Tema DÃ¼zeltmeleri:** `bot.php` sayfasÄ±ndaki eksik saÄŸ sidebar Ã§aÄŸrÄ±sÄ± (`right_sidebar.php`) eklenerek Minia temasÄ±nÄ±n Topbar hatalarÄ± giderildi.

**âœ… ADIM 16: HiyerarÅŸik Veri YÃ¶netimi, UI Standardizasyonu ve AkÄ±llÄ± Formlar (10 AÄŸustos GÃ¼ncellemesi)**
* **Aksiyon:** Lig-TakÄ±m iliÅŸkilerinin kesin olarak veritabanÄ±na baÄŸlanmasÄ±, veri giriÅŸi sÃ¼reÃ§lerinin yapay zeka/otomasyon ile hÄ±zlandÄ±rÄ±lmasÄ± ve genel UI tasarÄ±mÄ±ndaki kÄ±rÄ±k gÃ¶rsellerin (fallback) standardize edilmesi saÄŸlandÄ±.
* **Detaylar:**
  * **Lig-TakÄ±m HiyerarÅŸisi:** `teams` tablosuna `league_id` alanÄ± eklendi. Botun maÃ§larÄ± eklerken yeni takÄ±mlarÄ± otomatik olarak ilgili lige baÄŸlamasÄ± (getOrAddTeamId) saÄŸlandÄ±. `api/admin/teams.php` Ã¼zerinde yapÄ±lan `JOIN` gÃ¼ncellemeleri sayesinde admin paneli listeleme ve filtreleme iÅŸlemlerinde takÄ±mlarÄ±n hangi lige ait olduÄŸu gÃ¶sterildi.
  * **AkÄ±llÄ± Formlar (HÄ±zlÄ± GiriÅŸ):** MaÃ§ Ekleme ve DÃ¼zenleme ekranlarÄ±nda "HÄ±zlÄ± KarÅŸÄ±laÅŸma GiriÅŸi (Kopyala/YapÄ±ÅŸtÄ±r)" metin alanÄ± eklendi. KullanÄ±cÄ± "X - Y" veya "X vs Y" yazdÄ±ÄŸÄ±nda sistemin metni otomatik bÃ¶lmesi, var olmayan takÄ±mlar iÃ§in Select2'ye anÄ±nda Option ekleyerek veritabanÄ± tutarlÄ±lÄ±ÄŸÄ±nÄ± saÄŸlamasÄ± kodlandÄ±.
  * **Null Slug OnarÄ±mÄ±:** VeritabanÄ±ndaki null `slug` deÄŸerleri otomatik olarak standart regex formatÄ±nda (kebab-case) gÃ¼ncellendi.
  * **UI Fallback Standardizasyonu:** `matches_list.php`, `teams.php` ve `leagues.php` listelemelerinde, eksik/bozuk logolar iÃ§in ekrana `<span class='fw-bold me-1'>-</span>` (tire) basan `onerror` eventleri standartlaÅŸtÄ±rÄ±larak kaÃ§Ä±ÅŸ hatalarÄ± dÃ¼zeltildi.
  * **TakÄ±m Ã–nizleme ModalÄ±:** Ligler ekranÄ±na eklenen "GÃ¶z" (mdi-eye) butonu ile, bir lige ait tÃ¼m takÄ±mlarÄ±n sayfa deÄŸiÅŸtirmeden, dinamik bir Modal tablosu (AJAX ile `strict=1` filtreleme) Ã¼zerinden gÃ¶rÃ¼ntÃ¼lenmesi saÄŸlandÄ±.
* **SÄ±radaki Bekleyen AdÄ±m:** Sistem SaÄŸlÄ±ÄŸÄ± ve VeritabanÄ± Yedekleme (Backup) (AdÄ±m 11).

**âœ… ADIM 17: Mobile SPA VeritabanÄ± ve ArayÃ¼z Senkronizasyonu (Faz 2 - AdÄ±m A & B) (10 AÄŸustos GÃ¼ncellemesi)**
* **Aksiyon:** ArayÃ¼zde (UI/UX) tasarlanan Ã¶zelliklerin (VIP Hub, Toast bildirimler, KarÅŸÄ±lama EkranÄ± vb.) arka plana (Admin ve VeritabanÄ±) entegrasyonu tamamlandÄ±.
* **Detaylar:**
  * **VeritabanÄ± GÃ¼ncellemesi (`apps`):** `apps` tablosuna uygulamanÄ±n tÃ¼m yeni tasarÄ±m ve UX Ã¶zelliklerini kapsayan 17 yeni kolon (play_store_link, vip_hub_description, contact_whatsapp, forced_login, welcome_modal_active, guide_steps vb.) baÅŸarÄ±yla eklendi.
  * **VeritabanÄ± GÃ¼ncellemesi (`matches` ve `users`):** MaÃ§ tahmin baÅŸarÄ± yÃ¼zdesi iÃ§in `matches` tablosuna `confidence_rate` (VARCHAR 10) ve kullanÄ±cÄ± profil rÃ¼tbelendirmesi iÃ§in `users` tablosuna `gamification_badge` (VARCHAR 255) eklendi. (Ã‡oklu rozet sistemi iÃ§in VARCHAR boyutu geniÅŸletildi).
  * **API Refactoring:** `api/admin/apps.php`, `api/admin/matches.php` ve `api/admin/users.php` dosyalarÄ±nda CRUD operasyonlarÄ± bu yeni kolonlarÄ± destekleyecek ÅŸekilde gÃ¼ncellendi. KullanÄ±cÄ± rozetleri virgÃ¼lle ayrÄ±lmÄ±ÅŸ bir string olarak (implode) kaydedilip arayÃ¼zde tekrar array'e Ã§evrilecek ÅŸekilde kurgulandÄ±.
  * **Admin Panel Modernizasyonu:** `app_add.php` ve `app_edit.php` sayfalarÄ± 5 farklÄ± sekme (TasarÄ±m, GÃ¼venlik, UX & Metinler vb.) haline getirilerek kompleks form basitleÅŸtirildi. Mockup (CanlÄ± Ã–nizleme) alanÄ±na Arkaplan Rengi (bg_color) ve BakÄ±m Modu katmanlarÄ± eklendi.
  * **Form GÃ¼ncellemeleri:** `match_add.php` ve `match_edit.php` dosyalarÄ±na "GÃ¼ven OranÄ± (%)" inputu eklendi. `user_edit.php` dosyasÄ±na, Select2 destekli ve Ã§oklu seÃ§im (multi-badge) yapÄ±labilen "KullanÄ±cÄ± Rozeti (Badge)" seÃ§imi eklendi. (Eklenen yeni rozetler: Top Winner, Veteran, High Roller, Master Tipster vb.)
* **SÄ±radaki Bekleyen AdÄ±m:** Mobile SPA (Vue.js) tarafÄ±nÄ±n bu yeni backend Ã¶zelliklerini okuyup arayÃ¼ze basmasÄ±nÄ±n (Veri baÄŸlama - Data Binding) saÄŸlanmasÄ±.

**âœ… ADIM 18: Promosyon ve FAQ ModÃ¼llerinin OluÅŸturulmasÄ± (AdÄ±m C)**
* **Aksiyon:** Vue arayÃ¼zÃ¼nde ihtiyaÃ§ duyulan dinamik SÄ±kÃ§a Sorulan Sorular (FAQ) ve Promosyon/Banner altyapÄ±sÄ± admin paneline ve veritabanÄ±na eklendi.
* **Detaylar:**
  * **VeritabanÄ± GÃ¼ncellemesi:** `faqs` (SÄ±kÃ§a Sorulan Sorular) ve `promotions` (Kampanyalar) tablolarÄ± PDO betiÄŸi kullanÄ±larak oluÅŸturuldu.
  * **S.S.S (FAQ) ModÃ¼lÃ¼:** `api/admin/faqs.php` (CRUD) yazÄ±ldÄ±. `admin/faqs.php` arayÃ¼zÃ¼ DataTables ve Modal form mimarisi ile oluÅŸturuldu. `admin/sidebar.php` dosyasÄ±na "S.S.S (FAQ)" menÃ¼ Ã¶ÄŸesi eklendi.
  * **Promosyon YÃ¶netimi (Promo Engine):** `api/admin/promotions.php` kodlandÄ±. `admin/promotions.php` oluÅŸturularak "Kampanya BaÅŸlÄ±ÄŸÄ±", "Rozet Metni" ve "BitiÅŸ Tarihi" dinamik yÃ¶netilebilir hale getirildi. 
* **SÄ±radaki Bekleyen AdÄ±m:** UygulamanÄ±n kalan backend baÄŸlamalarÄ± tamamlanarak Vue arayÃ¼zÃ¼nÃ¼n (SPA) gerÃ§ek API verileriyle test edilmesi.

**âœ… ADIM 19: Destek Talepleri (Tickets) Admin ModÃ¼lÃ¼**
* **Aksiyon:** KullanÄ±cÄ±larÄ±n destek taleplerini (Ticket) yÃ¶netecek olan Destek Merkezi altyapÄ±sÄ± ve arayÃ¼zÃ¼ kuruldu.
* **Detaylar:**
  * **VeritabanÄ± GÃ¼ncellemesi:** `tickets` tablosuna `category`, `subject`, `admin_reply_at`, `is_deleted` kolonlarÄ± eklendi. `status` kolonu `open`, `pending`, `closed`, `cancelled` olacak ÅŸekilde 4'lÃ¼ statÃ¼ sistemine (`ENUM`) gÃ¼ncellendi.
  * **API KatmanÄ±:** `api/admin/tickets.php` Ã¼zerinden `list`, `get`, `reply`, `update_status`, `delete` ve `count` endpointleri oluÅŸturuldu. TÃ¼m iÅŸlemler JSON input parsing (`php://input`) standartlarÄ±na gÃ¶re kodlandÄ±.
  * **ArayÃ¼z TasarÄ±mÄ±:** `admin/tickets.php` sayfasÄ± DataTables, URL statÃ¼ filtrelemesi (`?status=open`) ve sol-saÄŸ kolonlu tek Modal konsepti ile kodlandÄ±. YÃ¶neticiler bu modal Ã¼zerinden kullanÄ±cÄ± mesajÄ±nÄ± okuyup, yanÄ±t yazÄ±p ticket statÃ¼sÃ¼nÃ¼ anÄ±nda yÃ¶netebilir hale getirildi.
  * **Core Entegrasyon:** `admin/sidebar.php` menÃ¼sÃ¼ gÃ¼ncellendi ve aÃ§Ä±k ticket sayÄ±sÄ±nÄ± anlÄ±k bildiren bildirim sayacÄ± (badge) `acms-core.js` iÃ§erisine entegre edildi.
* **SÄ±radaki Bekleyen AdÄ±m:** Mobile SPA (Vue.js) uygulamasÄ±nÄ±n geliÅŸtirilmesi.

**âœ… ADIM 20: GÃ¼venlik AltyapÄ±sÄ± ve SMTP HazÄ±rlÄ±ÄŸÄ± (Faz 3 Ã–ncesi)**
* **Aksiyon:** Vue SPA (CanlÄ± API) testlerine geÃ§meden Ã¶nce kullanÄ±cÄ± gÃ¼venlik duvarlarÄ±, ÅŸifre sÄ±fÄ±rlama tablolarÄ± ve mail (SMTP) konfigÃ¼rasyonu tamamlandÄ±.
* **Detaylar:**
  * **VeritabanÄ± GÃ¼ncellemesi:** `users` tablosuna `session_token`, `failed_login_attempts`, `lockout_time`, `last_login_ip`, `last_login_date`, `approval_date` eklendi. `password_resets` ve `settings` tablolarÄ± oluÅŸturuldu. `apps` tablosuna `font_family` eklendi.
  * **SMTP AyarlarÄ±:** `admin/smtp_settings.php` oluÅŸturularak mail sunucusu konfigÃ¼rasyon arayÃ¼zÃ¼ yazÄ±ldÄ±. Veriler `api/admin/settings.php` Ã¼zerinden (`save_smtp`, `get_smtp`) yÃ¶netilmeye baÅŸlandÄ±. `admin/sidebar.php`'deki menÃ¼ linki aktifleÅŸtirildi.
  * **Font YÃ¶netimi:** `admin/app_add.php` ve `admin/app_edit.php` (TasarÄ±m Sekmesi) iÃ§erisine "Font Ailesi" dropdown'u eklendi. `api/admin/apps.php` dosyasÄ± INSERT ve UPDATE iÅŸlemlerinde font bilgisini veritabanÄ±na iÅŸleyecek ÅŸekilde gÃ¼ncellendi.
* **SÄ±radaki Bekleyen AdÄ±m:** Mobile SPA (Vue.js) ile CanlÄ± API entegrasyonu (Faz 3).

**âœ… ADIM 21: Faz 3 BaÅŸlangÄ±cÄ± â€” init.php CanlÄ± Veri BaÄŸlantÄ±sÄ±**
* **Aksiyon:** Vue SPA'nÄ±n baÅŸlarken ilk Ã§aÄŸÄ±rdÄ±ÄŸÄ± (tÃ¼m ayarlarÄ± ve temayÄ± aldÄ±ÄŸÄ±) `init.php` dosyasÄ±, sahte (mock) JSON verilerinden arÄ±ndÄ±rÄ±lÄ±p gerÃ§ek PDO veritabanÄ± baÄŸlantÄ±sÄ±na taÅŸÄ±ndÄ±.
* **Detaylar:**
  * `api/app/init.php` yeniden kodlandÄ±. Ä°stekler `?app_id=` veya `?slug=` parametreleri ile alÄ±narak dinamik hale getirildi.
  * **CORS ve GÃ¼venlik:** Ä°lgili origin, JSON content-type ve options method header'larÄ± eklendi.
  * **Veri Toplama:** 
    - `apps` tablosundan uygulama renkleri, versiyon sÄ±nÄ±rlarÄ± ve font bilgileri,
    - `brands` tablosundan `slug` (`theme`) verisi,
    - `promotions` tablosundan o uygulamaya ait aktif promosyon kampanyasÄ±,
    - VIP Hub iÃ§in; aynÄ± `brand_id` altÄ±ndaki diÄŸer aktif uygulamalar baÅŸarÄ±yla dizi (array) halinde derlendi.
  * **JSON Ã‡Ä±ktÄ±sÄ±:** Frontend mimarisinin beklediÄŸi iÃ§ iÃ§e geÃ§miÅŸ JSON objeleri (`social`, `contact`, `announcement_modal`, vb.) sorunsuz ve Null Warning vermeyecek ÅŸekilde dÃ¼zenlendi.
* **SÄ±radaki Bekleyen AdÄ±m:** SPA'nÄ±n (Vue) oluÅŸturulan bu canlÄ± `init.php` API'sini baÅŸarÄ±yla tÃ¼ketip render etmesi.

**âœ… ADIM 22: Faz 3 â€” Auth API UÃ§larÄ± (register, login, verify, logout)**
* **Aksiyon:** Vue SPA'nÄ±n kullanÄ±cÄ± giriÅŸ, kayÄ±t ve oturum yenileme ekranlarÄ± iÃ§in API servisleri yazÄ±ldÄ±.
* **Detaylar:**
  * BÃ¼tÃ¼n dosyalar `api/app/auth/` klasÃ¶rÃ¼ altÄ±nda oluÅŸturuldu. CORS ve OPTIONS preflight standartlarÄ± eklendi.
  * **register.php:** Yeni kullanÄ±cÄ±larÄ± bcrypt ile kaydeder. Email ve GPA code (varsa) benzersizlik (unique) denetiminden geÃ§er. Hem `users` hem de `user_apps` tablolarÄ±na veri yazarak Ã§oklu uygulama altyapÄ±sÄ±nÄ± destekler.
  * **login.php:** GiriÅŸ iÅŸlemini saÄŸlar. YanlÄ±ÅŸ ÅŸifre denemelerinde (5 ve Ã¼zeri) hesabÄ± 30 dakika kitler (`lockout_time`). Token yenilemesi yapar ve `last_login_ip`, `last_login_date` gÃ¼nceller.
  * **verify.php:** SPA'nÄ±n session (oturum) kurtarmasÄ±nÄ± saÄŸlar. Token Authorization header veya POST body'den okunur. KullanÄ±cÄ± `is_banned` ise otomatik 403 atar, geÃ§erliyse "sliding expiry" mantÄ±ÄŸÄ±yla yeni bir token Ã¼retip dÃ¶ner.
  * **logout.php:** Token'Ä± veritabanÄ±ndan geÃ§ersiz (`NULL`) kÄ±lar.
* **SÄ±radaki Bekleyen AdÄ±m:** Mobil uygulama arayÃ¼zÃ¼nde (Vue.js) Login/Register formlarÄ±nÄ±n oluÅŸturulmasÄ± ve bu API'lere baÄŸlanmasÄ±.

**âœ… ADIM 23: Faz 3 â€” Matches (Tips) API**
* **Aksiyon:** Vue SPA'nÄ±n "Tahminler" listesini Ã§ekeceÄŸi ana API dosyasÄ± (`api/app/matches.php`) oluÅŸturuldu.
* **Detaylar:**
  * Endpoint, `app_id` (Zorunlu) ve `date` (Opsiyonel, varsayÄ±lan: BugÃ¼n) parametreleriyle Ã§alÄ±ÅŸÄ±r.
  * **GÃ¼venlik KalkanÄ±:** Authorization header Ã¼zerinden token kontrolÃ¼ saÄŸlayan bir helper fonksiyon eklendi.
  * **Veri Gizleme:** Sisteme giriÅŸ yapmamÄ±ÅŸ "Misafirler" (Guest) ve durumu onay bekleyen ("pending") kullanÄ±cÄ±lar maÃ§ listesini gÃ¶rebilir ancak `prediction` (tahmin) ve `odds` (oran) deÄŸerleri sunucu tarafÄ±ndan `NULL` olarak temizlenip gÃ¶nderilir. YalnÄ±zca `approved` kullanÄ±cÄ±lar asÄ±l tahminleri gÃ¶rebilir.
  * **GeÃ§miÅŸe KÄ±sÄ±tlama:** UygulamanÄ±n `guest_tips_limit` deÄŸerine gÃ¶re, giriÅŸ yapmamÄ±ÅŸ (veya Ã¼ye olmayan) misafirler geÃ§miÅŸ gÃ¼nlerdeki maÃ§ verilerini gÃ¶remez, istek atÄ±ldÄ±ÄŸÄ±nda sistem boÅŸ liste `[]` dÃ¶ner.
  * **BaÄŸlantÄ±lar (JOIN):** MaÃ§ objesinde yalnÄ±zca ID deÄŸerleri deÄŸil, Frontend'in direkt render edebilmesi iÃ§in takÄ±mlarÄ±n ve ligin isimleri, logolarÄ± da tam BaseURL linkiyle birlikte verilir.
* **SÄ±radaki Bekleyen AdÄ±m:** Vue 3 projesi baÅŸlatÄ±lÄ±p oluÅŸturduÄŸumuz Matches ve Auth API'lerinin gÃ¶rsel entegrasyonunun yapÄ±lmasÄ±.

**âœ… ADIM 24: Faz 3 â€” Profile, Support ve Promotions API UÃ§larÄ±**
* **Aksiyon:** Vue SPA'nÄ±n kullanÄ±cÄ± profili yÃ¶netimi, SSS, bilet (ticket) iÅŸlemleri ve promosyon bilgileri iÃ§in gereken `profile.php`, `support.php` ve `promotions.php` API uÃ§larÄ± oluÅŸturuldu.
* **Detaylar:**
  * **profile.php:**
    - GET: Token Ã¼zerinden kullanÄ±cÄ± bilgilerini alÄ±r (status ve array yapÄ±sÄ±ndaki rozetler dahil).
    - POST (change_password): Eski ÅŸifreyi doÄŸrular, yeni ÅŸifreyi bcrypt ile gÃ¼nceller (min 8 karakter sÄ±nÄ±rÄ±).
    - POST (update_gpa): SatÄ±n alÄ±m kodunu gÃ¼nceller ve unique kÄ±sÄ±tlamasÄ±nÄ± kontrol eder. Durumu `pending`e Ã§eker.
    - DELETE (delete_account): `exempt_security` (silinmezlik) kontrolÃ¼ yapar, ÅŸifreyi doÄŸrular ve soft delete iÅŸler.
  * **support.php:**
    - GET (faqs): Uygulamaya ait aktif SSS kayÄ±tlarÄ±nÄ± dÃ¶ndÃ¼rÃ¼r.
    - GET (tickets): KullanÄ±cÄ±nÄ±n geÃ§miÅŸ ve aktif destek taleplerini tarih sÄ±rasÄ±na gÃ¶re listeler.
    - POST (create_ticket): Yeni destek talebi aÃ§ar. AynÄ± anda maks 2 aÃ§Ä±k bilet (open/pending) kÄ±sÄ±tlamasÄ±na tabidir.
  * **promotions.php:**
    - GET: Uygulamaya ait son eklenen ve sÃ¼resi geÃ§memiÅŸ gÃ¼ncel kampanyayÄ± (badge text vb. bilgilerle) dÃ¶ndÃ¼rÃ¼r.
* **SÄ±radaki Bekleyen AdÄ±m:** Vue 3 tabanlÄ± mobil arayÃ¼z projesinin (SPA) tÃ¼m bu backend uÃ§larÄ±na baÄŸlanarak render edilmesi.

**âœ… ADIM 25: Login BileÅŸeni API Entegrasyonu**
* **Aksiyon:** Login bileÅŸeni canlÄ± auth/login.php API'sine baÄŸlandÄ±. Token localStorage'a yazÄ±lÄ±yor. Register bileÅŸeni canlÄ± auth/register.php API'sine baÄŸlandÄ±. Session Resume (verify.php) ve Logout baÄŸlandÄ±. Router guard aktif.
* **SÄ±radaki Bekleyen AdÄ±m:** DiÄŸer Vue 3 bileÅŸenlerinin (SPA) backend uÃ§larÄ±na baÄŸlanmasÄ±.

**âœ… ADIM 26: Faz 3 â€” Tips SayfasÄ± CanlÄ±ya BaÄŸlandÄ±**
* **Aksiyon:** DateSlider, API cache, skeleton loader ve maÃ§ durumu kartlarÄ± entegre edildi.
* **SÄ±radaki Bekleyen AdÄ±m:** Profile ve diÄŸer bileÅŸenlerin backend uÃ§larÄ±na baÄŸlanmasÄ±.

**âœ… ADIM 27: Faz 3 â€” Profile SayfasÄ± CanlÄ±ya BaÄŸlandÄ±**
* **Aksiyon:** KullanÄ±cÄ± adÄ±, email, durum rozeti ve GPA gÃ¼ncelleme API'ye baÄŸlandÄ±.
* **SÄ±radaki Bekleyen AdÄ±m:** DiÄŸer bileÅŸenlerin backend uÃ§larÄ±na baÄŸlanmasÄ±.

**âœ… ADIM 28: Faz 3 â€” Support Center CanlÄ±ya BaÄŸlandÄ±**
* **Aksiyon:** FAQ listesi, ticket geÃ§miÅŸi ve yeni ticket oluÅŸturma API'ye baÄŸlandÄ±. EmptyState UI eklendi. Admin panel app_edit.php ve app_add.php formlarÄ±na vip_hub_description alanÄ± eklendi.
* **SÄ±radaki Bekleyen AdÄ±m:** DiÄŸer bileÅŸenlerin backend uÃ§larÄ±na baÄŸlanmasÄ±.

**âœ… ADIM 29: Faz 3 â€” VIP Hub ve appConfig Inject DÃ¼zeltildi**
* **Aksiyon:** vip_hub_apps artÄ±k init.php'den dinamik geliyor, tÃ¼m componentler appConfig'e eriÅŸebiliyor. VIP Hub kartlarÄ± dinamikleÅŸtirildi. VIP Hub UI iyileÅŸtirmeleri: logo fallback, buton metni gÃ¼ncellendi, Play Store uyumluluk notu ve bottom nav sekmesi eklendi.
* **SÄ±radaki Bekleyen AdÄ±m:** DiÄŸer bileÅŸenlerin backend uÃ§larÄ±na baÄŸlanmasÄ±.

**âœ… ADIM 30: Faz 3 â€” Home SayfasÄ± CanlÄ±ya BaÄŸlandÄ±**
* **Aksiyon:** Duyurular, geri sayÄ±m, teaser ve son kazananlar matches.php ve appConfig Ã¼zerinden dinamik hale getirildi.
* **SÄ±radaki Bekleyen AdÄ±m:** DiÄŸer bileÅŸenlerin backend uÃ§larÄ±na baÄŸlanmasÄ±.

**âœ… ADIM 31: Faz 3 â€” Promotions Banner CanlÄ±ya BaÄŸlandÄ±**
* **Aksiyon:** Admin panelinden oluÅŸturulan aktif kampanya AppLayout banner'Ä±nda dinamik gÃ¶steriliyor. 48 saat kapatma kuralÄ± eklendi.
* **SÄ±radaki Bekleyen AdÄ±m:** DiÄŸer bileÅŸenlerin backend uÃ§larÄ±na baÄŸlanmasÄ±.

**âœ… ADIM 32: Faz 3 â€” Heartbeat GÃ¼venlik DÃ¶ngÃ¼sÃ¼ Kuruldu**
* **Aksiyon:** KullanÄ±cÄ± uygulamayÄ± aÃ§Ä±k tutarken her 5 dakikada (test iÃ§in geÃ§ici 10 sn) bir verify.php Ã§aÄŸrÄ±larak ban ve maintenance durumu kontrol ediliyor. BanlanmÄ±ÅŸ kullanÄ±cÄ± anÄ±nda logout yapÄ±lÄ±p atÄ±lÄ±yor.
* **SÄ±radaki Bekleyen AdÄ±m:** Faz sonu kontrolleri ve testler.

**âœ… ADIM 33: Faz 3 â€” Forgot Password / OTP Å?ifre SÄ±fÄ±rlama tamamlandÄ±.**
* **Aksiyon:** `forgot_password.php` ve `reset_password.php` API'leri eklendi. `app.js` iÃ§erisinde 2 adÄ±mlÄ± ÅŸifre sÄ±fÄ±rlama formu entegre edildi. VeritabanÄ±na OTP destekleyen kolonlar dinamik olarak eklendi.

**âœ… ADIM 34: Faz 3 â€” Change Password & Delete Account modÃ¼lleri canlÄ±ya baÄŸlandÄ±.**
* **Aksiyon:** Profil sayfasÄ± iÃ§indeki Account Actions menÃ¼sÃ¼ tamamlandÄ±. `change_password` ve `delete_account` fonksiyonlarÄ± Vue iÃ§erisinde kodlandÄ±. `exempt_security` parametresi kullanÄ±larak test hesaplarÄ±nÄ±n bu modÃ¼lleri gÃ¶rmesi engellendi.
'  A D I M   3 3 :   F a z   3      F o r g o t   P a s s w o r d   /   O T P   ^i f r e   S 1f 1r l a m a   t a m a m l a n d 1. 
 

**âœ… ADIM 35: Faz 3 â€” Contact Us, Privacy Policy & Terms canlÄ±ya baÄŸlandÄ±.**
* **Aksiyon:** Profil sayfasÄ±ndaki Support & Legal bÃ¶lÃ¼mÃ¼ aktif edildi. Contact Us butonu `appConfig.contact` ve `appConfig.social` verisine baÄŸlandÄ± (Telegram > WhatsApp > Email Ã¶nceliÄŸi). Privacy Policy ve Terms of Use butonlarÄ± yasal metinleri Modal iÃ§inde aÃ§acak ÅŸekilde entegre edildi. `index.html` versiyonu v20'ye Ã§ekildi.

## FAZ 3 TAMAMLANDI â€” Son GÃ¼ncelleme: 13.08.2026

### AdÄ±m 11â€“17 Ã–zeti
- âœ… Banned kullanÄ±cÄ± akÄ±ÅŸÄ± (BannedState UI, heartbeat kontrolÃ¼)
- âœ… Åifre sÄ±fÄ±rlama (Forgot Password, OTP, 15dk countdown)
- âœ… Åifre deÄŸiÅŸtirme (Change Password, exempt_security korumasÄ±)
- âœ… Hesap silme (Delete Account, onay checkbox, silme sebebi, soft-delete, admin Ã§Ã¶p kutusu)
- âœ… Contact Us (Support sayfasÄ±na yÃ¶nlendirme, iletiÅŸim kanallarÄ±, yanÄ±t sÃ¼releri, dÄ±ÅŸ link modalÄ±)
- âœ… Privacy Policy & Terms of Use (DB'den dinamik, admin yÃ¶netimli)
- âœ… Rate Us Modal (2 aÅŸamalÄ±, admin yÃ¶netimli metinler, muafiyet sistemi, snooze mantÄ±ÄŸÄ±)

### Ãœretim HazÄ±rlÄ±ÄŸÄ± NotlarÄ±
- app.js gÃ¼ncel versiyon: v=29
- Heartbeat: 5 dakika
- exempt_security: Test hesaplarÄ± iÃ§in hesap iÅŸlemleri + Rate Us korumasÄ± aktif
- TÃ¼m dÄ±ÅŸ linkler "Leaving the App" onay modalÄ± ile aÃ§Ä±lÄ±yor
- Rate Us sadece /app/tips rotasÄ±nda tetikleniyor (router.afterEach)

### Kalan GÃ¶revler (Opsiyonel)
- Google Play Console submission
- Privacy Policy ve Terms iÃ§eriklerinin hukuki gÃ¶zden geÃ§irilmesi
- FCM Push Notification entegrasyonu (Faz 4)

---
**✅ ADIM 36: Dinamik Onboarding ve Announcement Konfigürasyonu (Faz 3 Sonu)**
* **Aksiyon:** Eskiden `app.js` içerisinde sabit (hardcoded) olan Onboarding (Step 1-2-3) metinleri, Guest Landing Guide Card metinleri ve Anasayfa Latest News (Duyuru) alanı, tamamen Admin Panel'e bağlandı.
* **Detaylar:**
  * **Veritabanı (Migration):** `apps` tablosuna `onboarding_step1_title/desc` (1-3 arası) ve `home_announcement_text` kolonları eklendi.
  * **API Entegrasyonu:** `api/admin/apps.php` ve `api/app/init.php` uç noktaları güncellendi.
  * **Admin Panel:** `admin/app_add.php` ve `admin/app_edit.php` dosyalarında "UX & Metinler" sekmesine yeni form elemanları eklendi.
  * **Vue.js Entegrasyonu:** `Onboarding`, `GuestLanding`, ve `Home` componentleri `appConfig` verisini kullanacak şekilde refactor edildi. `index.html` versiyon v=53 olarak güncellendi.

---
**✅ ADIM 37: app_edit.php ve app_add.php Tam Yeniden Yazımı — 8 Sekmeli Yapı + Live Mockup Entegrasyonu**
* **Aksiyon:** `app_edit.php` ve `app_add.php` dosyaları iç içe geçmiş tag hatalarından (div uyuşmazlığı) kurtarılarak temiz bir 8 sekmeli (tab) mimariye taşındı.
* **Detaylar:**
  * Tasarım, İletişim, UX & Metinler, Güvenlik, Entegrasyonlar ve Yasal Metinler birbirinden izole edildi.
  * Form gönderim işlemi onarıldı, özellikle `legal_texts_json` derlemesi sağlandı.
  * Canlı mockup yapısı iyileştirildi; renkler, uyarılar, UX içerikleri anlık olarak mockup üzerinde görünür kılındı.

---
**✅ ADIM 38: Mockup Yeniden Tasarımı ve JS Mantığı**
* **Aksiyon:** `app_edit.php` içerisindeki Canlı Önizleme (Mockup) HTML yapısı ve JavaScript (`updateMockup`) fonksiyonu tamamen yenilendi.
* **Detaylar:**
  * Mockup ekranları (Home, Onboarding, Login, Rate Us) arasında geçiş yapabilen `mockup-screen-switcher` butonları eklendi.
  * Telefon çerçevesi (border, notch, shadow) çok daha premium ve gerçeğe yakın (native) bir iPhone/Android görünümüne kavuşturuldu.
  * CSS ve stil kodları `sticky-top` ile form doldurulurken aşağı inmesi sağlandı.
  * JS tarafında form sekmelerine tıklandığında (örn: UX & Metinler sekmesine geçince) otomatik olarak mockup'ın Onboarding veya Rate Us ekranına geçmesi mantığı eklendi.
  * Mockup içeriklerinin anlık güncellenmesi için `updateMockup` fonksiyonu, yeni ekranları kapsayacak şekilde genişletildi.

---
**✅ ADIM 39: Gerçek Iframe ve postMessage Canlı Önizleme Köprüsü (Live Customizer)**
* **Aksiyon:** Sahte HTML mockup yapısı tamamen kaldırılarak yerine gerçek Vue 3 SPA'yı yükleyen `<iframe>` ve 0 ms gecikmeli `postMessage` reaktif köprüsü kuruldu.
* **Detaylar:**
  * **SPA Katmanı (`app/app.js`):** `ACMS_PREVIEW_UPDATE` ve `ACMS_PREVIEW_NAVIGATE` mesaj dinleyicileri eklendi. `appConfigRef` reaktif state'i, CSS değişkenleri (`--color-primary` vb.) ve `#theme-style` stylesheet linki sayfa yenilenmeden anlık güncellenebilir kılındı.
  * **Router ve Loader İyileştirmesi:** `preview=true` parametresi algılandığında 1.2 sn açılış splash animasyonu ve oturum kilidi bypass edilerek admin panelinde anında Home ekranı render edildi.
  * **Admin Panel (`app_edit.php` & `app_add.php`):** Sağ sütuna 375x750 çözünürlüğünde mobil render yapıp `scale(0.746)` ile telefon kasasına oturan responsive `<iframe>` yerleştirildi. Formdaki tüm `input/change` olayları ile sekme geçişleri anlık olarak iframe'e yönlendirildi. Yenile butonu entegre edildi.
