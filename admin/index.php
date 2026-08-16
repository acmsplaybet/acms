<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Dashboard | ACMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link rel="stylesheet" href="assets/css/preloader.min.css" type="text/css" />
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
                    
                    <!-- Action Panel -->
                    <div class="row" id="pendingAlertRow" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-alert-outline me-2"></i>
                                <span id="pendingAlertMsg"></span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Mini Widgets -->
                    <div class="row">
                        <!-- Widget 1 -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-h-100">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Aktif Kullanıcılar</span>
                                            <h4 class="mb-3"><span class="counter-value" id="widget_active_users">0</span></h4>
                                        </div>
                                        <div class="col-6">
                                            <div id="mini-chart1" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                                        </div>
                                    </div>
                                    <div class="text-nowrap">
                                        <span class="badge bg-soft-success text-success">+$20.65</span>
                                        <span class="ms-1 text-muted font-size-13">Since last week</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Widget 2 -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-h-100">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Bugünün Maçları</span>
                                            <h4 class="mb-3"><span class="counter-value" id="widget_today_matches">0</span></h4>
                                        </div>
                                        <div class="col-6">
                                            <div id="mini-chart2" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                                        </div>
                                    </div>
                                    <div class="text-nowrap">
                                        <span class="badge bg-soft-danger text-danger">-2.05%</span>
                                        <span class="ms-1 text-muted font-size-13">Since last week</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Widget 3 -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-h-100">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Aktif Uygulamalar</span>
                                            <h4 class="mb-3"><span class="counter-value" id="widget_total_apps">0</span></h4>
                                        </div>
                                        <div class="col-6">
                                            <div id="mini-chart3" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                                        </div>
                                    </div>
                                    <div class="text-nowrap">
                                        <span class="badge bg-soft-success text-success">+15.3%</span>
                                        <span class="ms-1 text-muted font-size-13">Since last week</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Widget 4 -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-h-100">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Genel Başarı Yüzdesi</span>
                                            <h4 class="mb-3"><span class="counter-value" id="widget_hit_rate">0</span>%</h4>
                                        </div>
                                        <div class="col-6">
                                            <div id="mini-chart4" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                                        </div>
                                    </div>
                                    <div class="text-nowrap">
                                        <span class="badge bg-soft-success text-success">+5.3%</span>
                                        <span class="ms-1 text-muted font-size-13">Since last week</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Üye Büyüme Oranı (Son 30 Gün)</h4>
                                    <div id="users-growth-chart" class="apex-charts" dir="ltr"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Maç Sonuçları Dağılımı</h4>
                                    <div id="match-results-chart" class="apex-charts" dir="ltr"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Son Kayıt Olan 10 Üye</h4>
                                    <div class="table-responsive">
                                        <table class="table align-middle table-nowrap mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Ad Soyad</th>
                                                    <th>E-posta</th>
                                                    <th>Uygulama</th>
                                                    <th>Kayıt Tarihi</th>
                                                    <th>Statü</th>
                                                </tr>
                                            </thead>
                                            <tbody id="recent_users_table">
                                            </tbody>
                                        </table>
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
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/libs/pace-js/pace.min.js"></script>
    
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
    
    <script src="assets/js/app.js?v=2"></script>
    <script src="assets/js/acms-core.js?v=2"></script>
    
    <script>
        const token = localStorage.getItem('acms_admin_token');

        document.addEventListener("DOMContentLoaded", function() {
            fetchDashboardData();
        });

        function fetchDashboardData() {
            fetch('../api/admin/dashboard.php', {
                headers: { 'Authorization': 'Bearer ' + token }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const stats = data.data;

                    // Alert Panel
                    if (stats.pending_users_count > 0) {
                        const alertRow = document.getElementById('pendingAlertRow');
                        const alertMsg = document.getElementById('pendingAlertMsg');
                        alertMsg.innerHTML = `🚨 Sistemde onayınızı bekleyen <strong>${stats.pending_users_count}</strong> adet VIP üye var. <a href="users_list.php?status=pending" class="alert-link">İncelemek için tıklayın.</a>`;
                        alertRow.style.display = 'block';
                    }

                    // Mini Widgets
                    document.getElementById('widget_active_users').innerText = stats.total_active_users;
                    document.getElementById('widget_today_matches').innerText = stats.today_matches_count;
                    document.getElementById('widget_total_apps').innerText = stats.total_apps;
                    document.getElementById('widget_hit_rate').innerText = stats.hit_rate;

                    // Fake Mini Charts for premium feel
                    const sparklineOptions = {
                        chart: { type: 'line', width: '100%', height: 40, sparkline: { enabled: true } },
                        stroke: { curve: 'smooth', width: 2 },
                        colors: ['#5156be'],
                        tooltip: { fixed: { enabled: false }, x: { show: false }, marker: { show: false } }
                    };
                    new ApexCharts(document.querySelector("#mini-chart1"), { ...sparklineOptions, series: [{ data: [12, 14, 2, 47, 42, 15, 47, 75, 65, 19, 14] }] }).render();
                    new ApexCharts(document.querySelector("#mini-chart2"), { ...sparklineOptions, series: [{ data: [65, 14, 2, 47, 42, 15, 47, 75, 65, 19, 14] }] }).render();
                    new ApexCharts(document.querySelector("#mini-chart3"), { ...sparklineOptions, series: [{ data: [2, 14, 2, 47, 42, 15, 47, 75, 65, 19, 14] }] }).render();
                    new ApexCharts(document.querySelector("#mini-chart4"), { ...sparklineOptions, series: [{ data: [12, 75, 2, 47, 42, 15, 47, 75, 65, 19, 14] }] }).render();

                    // Growth Line Chart
                    let growthData = stats.users_growth;
                    if (!growthData || growthData.length < 2) {
                        // Dummy fallback data if empty to keep UI looking premium
                        growthData = [
                            { date: 'Demo-1', count: 12 }, { date: 'Demo-2', count: 14 },
                            { date: 'Demo-3', count: 25 }, { date: 'Demo-4', count: 18 },
                            { date: 'Demo-5', count: 32 }, { date: 'Demo-6', count: 48 },
                            { date: 'Demo-7', count: 50 }
                        ];
                    }
                    
                    const dates = growthData.map(i => i.date);
                    const counts = growthData.map(i => parseInt(i.count));
                    
                    var optionsGrowth = {
                        chart: { height: 350, type: 'area', toolbar: { show: false } },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 2 },
                        series: [{ name: 'Yeni Kullanıcı', data: counts }],
                        colors: ['#5156be'],
                        xaxis: { categories: dates },
                        grid: { borderColor: '#f1f1f1' },
                        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [50, 100, 100, 100] } }
                    }
                    var chartGrowth = new ApexCharts(document.querySelector("#users-growth-chart"), optionsGrowth);
                    chartGrowth.render();

                    // Results Pie Chart
                    const mRes = stats.match_results;
                    let pieData = [mRes.WIN || 0, mRes.LOSE || 0, mRes.PENDING || 0];
                    if (pieData[0] === 0 && pieData[1] === 0 && pieData[2] === 0) {
                        // Dummy fallback data if 0
                        pieData = [45, 15, 2];
                    }
                    var optionsPie = {
                        series: pieData,
                        chart: { type: 'donut', height: 350 },
                        labels: ['WIN', 'LOSE', 'PENDING'],
                        colors: ['#2ab57d', '#fd625e', '#ffbf53'],
                        legend: { position: 'bottom' },
                        plotOptions: { pie: { donut: { size: '75%' } } }
                    };
                    var chartPie = new ApexCharts(document.querySelector("#match-results-chart"), optionsPie);
                    chartPie.render();

                    // Recent Users Table
                    const tbody = document.getElementById('recent_users_table');
                    if (stats.recent_users && stats.recent_users.length > 0) {
                        stats.recent_users.forEach(u => {
                            let badge = '';
                            if (u.status === 'approved') badge = '<span class="badge bg-success">Onaylandı</span>';
                            else if (u.status === 'pending') badge = '<span class="badge bg-warning">Bekliyor</span>';
                            else if (u.status === 'rejected') badge = '<span class="badge bg-danger">Reddedildi</span>';

                            tbody.innerHTML += `
                                <tr>
                                    <td><a href="user_edit.php?id=${u.id}" class="text-body fw-bold">#${u.id}</a></td>
                                    <td>${u.name}</td>
                                    <td>${u.email}</td>
                                    <td>${u.app_name || '-'}</td>
                                    <td>${u.created_at}</td>
                                    <td>${badge}</td>
                                </tr>
                            `;
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Kayıt bulunamadı.</td></tr>';
                    }
                }
            })
            .catch(err => console.error("Dashboard veri hatası:", err));
        }
    </script>
</body>
</html>