<?php
include '../config.php';
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}
include 'header.php';
include 'sidebar.php';

// Get next patient ID
$result = mysqli_query($conn, "SHOW TABLE STATUS LIKE 'patients'");
$row = mysqli_fetch_assoc($result);
$nextId = $row['Auto_increment'];
$patientCode = 'LAB-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

// Total Patients
$totalPatients = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM patients"));

// Total Income
$totalIncome = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(grand_total) as total FROM patients
"));

// Today's Patients
$todayPatients = mysqli_num_rows(mysqli_query($conn, "
    SELECT * FROM patients WHERE DATE(created_at) = CURDATE()
"));

// Today's Revenue
$dailyRevenue = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(grand_total) as total FROM patients WHERE DATE(created_at) = CURDATE()
"));

// This Month Income
$monthlyIncome = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(grand_total) as total FROM patients
    WHERE MONTH(created_at) = MONTH(CURDATE())
    AND YEAR(created_at) = YEAR(CURDATE())
"));

// Last Month Income
$lastMonthRevenue = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(grand_total) as total FROM patients
    WHERE MONTH(created_at) = MONTH(CURDATE() - INTERVAL 1 MONTH)
    AND YEAR(created_at) = YEAR(CURDATE() - INTERVAL 1 MONTH)
"));

// --- Date Range Filter ---
$dateFrom = isset($_GET['date_from']) && $_GET['date_from'] !== '' ? $_GET['date_from'] : null;
$dateTo   = isset($_GET['date_to'])   && $_GET['date_to']   !== '' ? $_GET['date_to']   : null;

// Filtered stats (only when date range is set)
if ($dateFrom && $dateTo) {
    $filteredStats = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT COUNT(*) as total_patients, SUM(grand_total) as total_revenue
        FROM patients
        WHERE DATE(created_at) BETWEEN '" . mysqli_real_escape_string($conn, $dateFrom) . "'
                                    AND '" . mysqli_real_escape_string($conn, $dateTo) . "'
    "));
} else {
    $filteredStats = null;
}

// --- Chart Data ---

// Monthly (all 12 months, current year)
$monthlyRaw   = [];
$monthlyQuery = mysqli_query($conn, "
    SELECT MONTH(created_at) as month_num, SUM(grand_total) as total
    FROM patients
    WHERE YEAR(created_at) = YEAR(CURDATE())
    GROUP BY MONTH(created_at)
    ORDER BY MONTH(created_at) ASC
");
while ($m = mysqli_fetch_assoc($monthlyQuery)) {
    $monthlyRaw[(int)$m['month_num']] = (float)$m['total'];
}
$monthNames    = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$monthlyLabels = $monthNames;
$monthlyTotals = [];
for ($i = 1; $i <= 12; $i++) {
    $monthlyTotals[] = $monthlyRaw[$i] ?? 0;
}

// Weekly (last 7 days)
$weeklyLabels = [];
$weeklyTotals = [];
$weeklyQuery  = mysqli_query($conn, "
    SELECT DATE_FORMAT(created_at, '%a %d') as label, SUM(grand_total) as total
    FROM patients
    WHERE created_at >= CURDATE() - INTERVAL 6 DAY
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at) ASC
");
while ($w = mysqli_fetch_assoc($weeklyQuery)) {
    $weeklyLabels[] = $w['label'];
    $weeklyTotals[] = (float)$w['total'];
}

// Today (by hour)
$todayLabels = [];
$todayTotals = [];
$todayQuery  = mysqli_query($conn, "
    SELECT DATE_FORMAT(created_at, '%H:00') as label, SUM(grand_total) as total
    FROM patients
    WHERE DATE(created_at) = CURDATE()
    GROUP BY HOUR(created_at)
    ORDER BY HOUR(created_at) ASC
");
while ($t = mysqli_fetch_assoc($todayQuery)) {
    $todayLabels[] = $t['label'];
    $todayTotals[] = (float)$t['total'];
}

// Custom date range chart data
$customLabels = [];
$customTotals = [];
if ($dateFrom && $dateTo) {
    $customQuery = mysqli_query($conn, "
        SELECT DATE_FORMAT(created_at, '%d %b') as label, SUM(grand_total) as total
        FROM patients
        WHERE DATE(created_at) BETWEEN '" . mysqli_real_escape_string($conn, $dateFrom) . "'
                                    AND '" . mysqli_real_escape_string($conn, $dateTo) . "'
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at) ASC
    ");
    while ($c = mysqli_fetch_assoc($customQuery)) {
        $customLabels[] = $c['label'];
        $customTotals[] = (float)$c['total'];
    }
}
?>

<!-- ========== MAIN CONTENT AREA (Scrollable) ========== -->
<main class="flex-1 bg-slate-50 p-6">

    <!-- Welcome Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-3xl p-8 text-white shadow-xl">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">Laboratory Dashboard</h1>
                    <p class="text-blue-100 text-sm md:text-base">
                        Manage patients, reports, tests and revenue from one place.
                    </p>
                    <div class="mt-4 flex items-center gap-3">
                        <span class="bg-white/20 px-4 py-2 rounded-xl text-sm backdrop-blur">
                            <i class="fas fa-calendar-alt mr-2"></i><?php echo date('d M Y'); ?>
                        </span>
                        <span class="bg-white/20 px-4 py-2 rounded-xl text-sm backdrop-blur">
                            <i class="fas fa-id-card mr-2"></i><?php echo $patientCode; ?>
                        </span>
                    </div>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-hospital-user text-8xl text-white/20"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtered Result Banner -->
    <?php if ($filteredStats): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="flex items-center gap-4 bg-indigo-50 rounded-2xl p-4 border border-indigo-100">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-users text-indigo-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Patients in range</p>
                    <p class="text-2xl font-bold text-slate-800"><?php echo $filteredStats['total_patients']; ?></p>
                    <p class="text-xs text-indigo-600 mt-0.5">
                        <?php echo date('d M Y', strtotime($dateFrom)); ?> &rarr;
                        <?php echo date('d M Y', strtotime($dateTo)); ?>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4 bg-emerald-50 rounded-2xl p-4 border border-emerald-100">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-coins text-emerald-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Revenue in range</p>
                    <p class="text-2xl font-bold text-slate-800">
                        Rs.<?php echo number_format($filteredStats['total_revenue'] ?? 0); ?>
                    </p>
                    <p class="text-xs text-emerald-600 mt-0.5">
                        <?php echo date('d M Y', strtotime($dateFrom)); ?> &rarr;
                        <?php echo date('d M Y', strtotime($dateTo)); ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

        <div class="group bg-white rounded-3xl p-6 shadow-sm border border-slate-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-sm font-medium">Today's Patients</p>
                    <h2 class="text-4xl font-bold text-slate-800 mt-3"><?php echo $todayPatients; ?></h2>
                    <div class="mt-4 text-purple-600 text-sm font-medium">Registered Today</div>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-user-plus text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <a href="patients.php">
            <div class="group bg-white rounded-3xl p-6 shadow-sm border border-slate-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-500 text-sm font-medium">Total Patients</p>
                        <h2 class="text-4xl font-bold text-slate-800 mt-3"><?php echo $totalPatients; ?></h2>
                        <div class="mt-4 text-blue-600 text-sm font-medium">View Patients →</div>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </a>

        <div class="group bg-white rounded-3xl p-6 shadow-sm border border-slate-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-sm font-medium">Today's Revenue</p>
                    <h2 class="text-4xl font-bold text-slate-800 mt-3">
                        Rs.<?php echo number_format($dailyRevenue['total'] ?? 0); ?>
                    </h2>
                    <div class="mt-4 text-orange-600 text-sm font-medium"><?php echo date('d M Y'); ?></div>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-coins text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="group bg-white rounded-3xl p-6 shadow-sm border border-slate-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-500 text-sm font-medium">Total Income</p>
                    <h2 class="text-4xl font-bold text-slate-800 mt-3">
                        Rs.<?php echo number_format($totalIncome['total'] ?? 0); ?>
                    </h2>
                    <div class="mt-4 text-emerald-600 text-sm font-medium">Lifetime Revenue</div>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-emerald-600 text-2xl"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart + Summary -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">

        <!-- Chart -->
        <div class="xl:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">Revenue Overview</h3>
                <select id="chartFilter" class="border rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="monthly">Monthly</option>
                    <option value="weekly">Weekly</option>
                    <option value="today">Today</option>
                    <?php if ($dateFrom && $dateTo): ?>
                        <option value="custom" selected>
                            <?php echo date('d M', strtotime($dateFrom)); ?> – <?php echo date('d M', strtotime($dateTo)); ?>
                        </option>
                    <?php endif; ?>
                </select>
            </div>
            <canvas id="revenueChart" height="120"></canvas>
        </div>

        <!-- Revenue Summary -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold mb-5">Revenue Summary</h3>

            <!-- Date search -->
            <form method="GET" action="" class="mb-5 space-y-2">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">
                        <i class="fas fa-calendar text-indigo-400 mr-1"></i> From
                    </label>
                    <input type="date" name="date_from"
                        value="<?php echo htmlspecialchars($dateFrom ?? ''); ?>"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">
                        <i class="fas fa-calendar text-indigo-400 mr-1"></i> To
                    </label>
                    <input type="date" name="date_to"
                        value="<?php echo htmlspecialchars($dateTo ?? ''); ?>"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                <div class="flex gap-2 pt-1">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-xl text-sm font-medium transition">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                    <?php if ($dateFrom || $dateTo): ?>
                        <a href="dashboard.php"
                            class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 py-2 rounded-xl text-sm font-medium transition">
                            <i class="fas fa-times mr-1"></i> Clear
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="space-y-4">
                <?php if ($filteredStats): ?>
                    <div class="flex justify-between bg-indigo-50 rounded-xl px-3 py-2">
                        <span class="text-indigo-700 text-sm font-medium">
                            <?php echo date('d M', strtotime($dateFrom)); ?> &rarr; <?php echo date('d M Y', strtotime($dateTo)); ?>
                        </span>
                        <strong class="text-indigo-700">
                            Rs.<?php echo number_format($filteredStats['total_revenue'] ?? 0); ?>
                        </strong>
                    </div>
                    <div class="border-t pt-3"></div>
                <?php endif; ?>
                <div class="flex justify-between">
                    <span>Today's Revenue</span>
                    <strong>Rs.<?php echo number_format($dailyRevenue['total'] ?? 0); ?></strong>
                </div>
                <div class="flex justify-between">
                    <span>This Month</span>
                    <strong>Rs.<?php echo number_format($monthlyIncome['total'] ?? 0); ?></strong>
                </div>
                <div class="flex justify-between">
                    <span>Last Month</span>
                    <strong>Rs.<?php echo number_format($lastMonthRevenue['total'] ?? 0); ?></strong>
                </div>
                <div class="border-t pt-4 flex justify-between text-green-600">
                    <span>Total Revenue</span>
                    <strong>Rs.<?php echo number_format($totalIncome['total'] ?? 0); ?></strong>
                </div>
            </div>
        </div>

    </div>

</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartDatasets = {
        monthly: {
            labels: <?php echo json_encode($monthlyLabels); ?>,
            data:   <?php echo json_encode($monthlyTotals); ?>
        },
        weekly: {
            labels: <?php echo json_encode($weeklyLabels); ?>,
            data:   <?php echo json_encode($weeklyTotals); ?>
        },
        today: {
            labels: <?php echo json_encode($todayLabels); ?>,
            data:   <?php echo json_encode($todayTotals); ?>
        },
        custom: {
            labels: <?php echo json_encode($customLabels); ?>,
            data:   <?php echo json_encode($customTotals); ?>
        }
    };

    const defaultView = <?php echo ($dateFrom && $dateTo) ? '"custom"' : '"monthly"'; ?>;
    const initial = chartDatasets[defaultView];

    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: initial.labels.length ? initial.labels : ['No Data'],
            datasets: [{
                label: 'Revenue (Rs.)',
                data: initial.data.length ? initial.data : [0],
                fill: true,
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                pointRadius: 5,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rs. ' + ctx.parsed.y.toLocaleString()
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: value => 'Rs. ' + value.toLocaleString() },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    document.getElementById('chartFilter').addEventListener('change', function () {
        const selected = chartDatasets[this.value];
        chart.data.labels           = selected.labels.length ? selected.labels : ['No Data'];
        chart.data.datasets[0].data = selected.data.length   ? selected.data   : [0];
        chart.update();
    });
</script>
<?php include 'footer.php'; ?>