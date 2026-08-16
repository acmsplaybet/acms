            <!-- ========== Left Sidebar Start ========== -->
            <div class="vertical-menu">

                <div data-simplebar class="h-100">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <!-- Left Menu Start -->
                        <ul class="metismenu list-unstyled" id="side-menu">
                            
                            <!-- AKTIF / CALISAN MENULER -->
                            <li class="menu-title" data-key="t-menu">DASHBOARD</li>
                            <li>
                                <a href="index.php">
                                    <i data-feather="home"></i>
                                    <span data-key="t-dashboard">Genel Bakış</span>
                                </a>
                            </li>

                            <li class="menu-title mt-2" data-key="t-apps">UYGULAMALAR</li>
                            <li>
                                <a href="brands.php" class="<?= basename($_SERVER['PHP_SELF']) === 'brands.php' ? 'active' : '' ?>">
                                    <i data-feather="layers"></i>
                                    <span data-key="t-brands">Markalar</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript: void(0);" class="has-arrow">
                                    <i data-feather="smartphone"></i>
                                    <span data-key="t-apps-menu">Uygulama İşlemleri</span>
                                </a>
                                <ul class="sub-menu" aria-expanded="false">
                                    <li><a href="apps_list.php" data-key="t-all-apps">Tüm Uygulamalar</a></li>
                                    <li><a href="app_add.php" data-key="t-new-app">Yeni Uygulama Ekle</a></li>
                                </ul>
                            </li>

                            <li class="menu-title mt-2" data-key="t-matches">MAÇ İŞLEMLERİ</li>
                            <li>
                                <a href="javascript: void(0);" class="has-arrow">
                                    <i data-feather="dribbble"></i>
                                    <span data-key="t-match-menu">Maç İşlemleri</span>
                                </a>
                                <ul class="sub-menu" aria-expanded="false">
                                    <li><a href="matches_list.php" data-key="t-all-matches">Tüm Maçlar</a></li>
                                    <li><a href="match_add.php" data-key="t-new-match">Yeni Maç Ekle</a></li>
                                    <li><a href="#" data-key="t-results">Toplu Sonuçlandırma</a></li>
                                    <li><a href="leagues.php" data-key="t-leagues">Ligler</a></li>
                                    <li><a href="teams.php" data-key="t-teams">Takımlar</a></li>
                                </ul>
                            </li>
                            <li class="menu-title mt-2" data-key="t-automation">OTOMASYON & İZLEME</li>
                            <li>
                                <a href="bot.php">
                                    <i data-feather="cpu"></i>
                                    <span data-key="t-bot">Bot Operasyon Merkezi</span>
                                </a>
                            </li>
                            <li>
                                <a href="audit_logs.php">
                                    <i data-feather="terminal"></i>
                                    <span data-key="t-audit">Terminal & İşlem Logları</span>
                                </a>
                            </li>


                            <li class="menu-title mt-2" data-key="t-users">KULLANICI YÖNETİMİ</li>
                            <li>
                                <a href="users_list.php">
                                    <i data-feather="users"></i>
                                    <span data-key="t-user-list">Kullanıcı Listesi</span>
                                </a>
                            </li>
                            <li>
                                <a href="users_list.php?status=pending">
                                    <i data-feather="clock"></i>
                                    <span id="pendingBadge" class="badge rounded-pill badge-soft-danger text-danger float-end"></span>
                                    <span data-key="t-pending">Onay Bekleyenler</span>
                                </a>
                            </li>

                            <li class="menu-title mt-2" data-key="t-trash-menu">GÜVENLİK</li>
                            <li>
                                <a href="trash.php" class="text-danger">
                                    <i data-feather="trash-2"></i>
                                    <span id="trashBadge" class="badge rounded-pill bg-danger float-end" style="display: none;"></span>
                                    <span data-key="t-trash">Çöp Kutusu</span>
                                </a>
                            </li>
                            
                            <!-- AKTIF MENULER -->
                            <li class="menu-title mt-4" data-key="t-marketing">PAZARLAMA & İLETİŞİM</li>
                            <li>
                                <a href="notifications.php" class="<?= basename($_SERVER['PHP_SELF']) === 'notifications.php' ? 'active' : '' ?>">
                                    <i data-feather="bell"></i>
                                    <span data-key="t-notifications">Bildirim Merkezi</span>
                                </a>
                            </li>
                            <li>
                                <a href="promotions.php" class="<?= basename($_SERVER['PHP_SELF']) === 'promotions.php' ? 'active' : '' ?>">
                                    <i data-feather="tag"></i>
                                    <span data-key="t-promo">Promosyon Yönetimi</span>
                                </a>
                            </li>
                            <li>
                                <a href="faqs.php">
                                    <i data-feather="help-circle"></i>
                                    <span data-key="t-faqs">S.S.S (FAQ)</span>
                                </a>
                            </li>
                            <li>
                                <a href="tickets.php">
                                    <i data-feather="message-circle"></i>
                                    <span id="ticketBadge" class="badge rounded-pill bg-primary float-end" style="display: none;"></span>
                                    <span data-key="t-tickets">Destek Talepleri</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i data-feather="mail"></i>
                                    <span data-key="t-templates">E-posta Şablonları</span>
                                </a>
                            </li>

                            <li class="menu-title mt-2" data-key="t-settings">SİSTEM AYARLARI</li>

                            <li>
                                <a href="javascript: void(0);" class="has-arrow">
                                    <i data-feather="settings"></i>
                                    <span data-key="t-general">Genel & Çekirdek Ayarlar</span>
                                </a>
                                <ul class="sub-menu" aria-expanded="false">
                                    <li><a href="smtp_settings.php" data-key="t-smtp">Genel & SMTP Ayarları</a></li>
                                    <li><a href="#" data-key="t-data">Veri Kaynakları</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">
                                    <i data-feather="server"></i>
                                    <span data-key="t-backup">Yedekleme Merkezi</span>
                                </a>
                            </li>

                            
                        </ul>
                    </div>
                    <!-- Sidebar -->
                </div>
            </div>
            <!-- Left Sidebar End -->
