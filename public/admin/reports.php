<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

use Nineventory\Auth;
use Nineventory\Inventory;
use Nineventory\Loan;

$auth = new Auth($pdo);
$auth->requireAdmin();

$inventory = new Inventory($pdo);
$loan = new Loan($pdo);
$currentUser = $auth->getCurrentUser();
$isAdmin = $auth->isAdmin();

// Fetch data for the charts and reports
$mostBorrowed = $loan->getMostBorrowedItems(5);
$lowStock = $inventory->getLowStock(5);
$overdueLoans = $loan->getOverdueLoans();
$topBorrowers = $loan->getTopBorrowers(5);
$categoryBreakdown = $loan->getCategoryBreakdown();


// Prepare data for ApexCharts
$mostBorrowedData = [
    'labels' => array_column($mostBorrowed, 'nama_barang'),
    'series' => array_column($mostBorrowed, 'total_borrowed'),
];

$lowStockData = [
    'labels' => array_column($lowStock, 'nama_barang'),
    'series' => array_column($lowStock, 'stok_tersedia'),
];

$categoryBreakdownData = [
    'labels' => array_column($categoryBreakdown, 'kategori'),
    'series' => array_column($categoryBreakdown, 'total_loans'),
];


$pageTitle = 'Reports & Analytics';
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - NINEVENTORY</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['DM Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: '#FF6626', // Orange
                        secondary: '#1E293B', // Slate 800
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans text-slate-800 dark:text-white transition-colors duration-300"
      x-data="{
          darkMode: localStorage.getItem('theme') === 'dark',
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
              document.documentElement.classList.toggle('dark', this.darkMode);
          }
      }"
      x-init="$watch('darkMode', val => document.documentElement.classList.toggle('dark', val)); document.documentElement.classList.toggle('dark', darkMode)">

    <div class="flex h-screen overflow-hidden bg-gray-50 dark:bg-gray-950">
        <div class="fixed inset-0 -z-10 bg-gray-50 dark:bg-gray-950">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-orange-500/10 blur-[100px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-red-500/10 blur-[100px]"></div>
        </div>

        <?php
        $activePage = 'reports';
        $pathPrefix = '../';
        include '../includes/sidebar.php';
        ?>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            <?php include '../includes/header.php'; ?>

            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                <div class="max-w-7xl mx-auto space-y-8">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        
                        <!-- Most Borrowed Items Chart -->
                        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Most Borrowed Items</h3>
                            <div id="most-borrowed-chart"></div>
                        </div>

                        <!-- Low Stock Items Chart -->
                        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                             <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Low Stock Items</h3>
                             <div id="low-stock-chart"></div>
                        </div>
                        
                        <!-- Category Breakdown Chart -->
                        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                             <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Loan by Category</h3>
                             <div id="category-chart"></div>
                        </div>

                        <!-- Top Borrowers -->
                        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Top Borrowers</h3>
                            </div>
                            <?php if (empty($topBorrowers)): ?>
                                <p class="p-6 text-gray-500 dark:text-gray-400">No borrowing data available.</p>
                            <?php else: ?>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                                            <tr>
                                                <th class="px-6 py-4 font-semibold">Username</th>
                                                <th class="px-6 py-4 font-semibold text-center">Total Loans</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                            <?php foreach ($topBorrowers as $borrower): ?>
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($borrower['username']) ?></td>
                                                <td class="px-6 py-4 text-center text-lg font-bold text-gray-900 dark:text-white"><?= $borrower['total_loans'] ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Overdue Borrowings -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Overdue Loans</h3>
                        </div>
                        <?php if (empty($overdueLoans)): ?>
                            <p class="p-6 text-gray-500 dark:text-gray-400">No overdue loans.</p>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                                        <tr>
                                            <th class="px-6 py-4 font-semibold">User</th>
                                            <th class="px-6 py-4 font-semibold">Items</th>
                                            <th class="px-6 py-4 font-semibold">Due Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        <?php foreach ($overdueLoans as $loan): ?>
                                        <tr class="hover:bg-red-50 dark:hover:bg-red-900/10">
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($loan['username']) ?></td>
                                            <td class="px-6 py-4 text-sm">
                                                <ul class="list-disc pl-4">
                                                <?php foreach($loan['details'] as $detail): ?>
                                                    <li><?= htmlspecialchars($detail['nama_barang']) ?> (x<?= $detail['jumlah'] ?>)</li>
                                                <?php endforeach; ?>
                                                </ul>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium text-red-600 dark:text-red-400"><?= date('M d, Y', strtotime($loan['tanggal_kembali_rencana'])) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
    lucide.createIcons();

    document.addEventListener('DOMContentLoaded', () => {
        const isDarkMode = localStorage.getItem('theme') === 'dark';

        const chartOptions = {
            chart: { toolbar: { show: false }, background: 'transparent' },
            grid: { borderColor: isDarkMode ? '#374151' : '#e5e7eb', strokeDashArray: 4 },
            dataLabels: { enabled: true },
            tooltip: { theme: isDarkMode ? 'dark' : 'light' }
        };

        // Most Borrowed Chart
        const mostBorrowedData = <?= json_encode($mostBorrowedData) ?>;
        if (mostBorrowedData.series.length > 0) {
            const mostBorrowedOptions = {
                ...chartOptions,
                series: [{ name: 'Times Borrowed', data: mostBorrowedData.series }],
                chart: { ...chartOptions.chart, type: 'bar', height: 350 },
                plotOptions: { bar: { borderRadius: 4, horizontal: true } },
                colors: ['#FF6626'],
                xaxis: { ...chartOptions.xaxis, categories: mostBorrowedData.labels, labels: { style: { colors: isDarkMode ? '#9ca3af' : '#6b7280' } } },
                yaxis: { labels: { style: { colors: isDarkMode ? '#9ca3af' : '#6b7280' } } }
            };
            new ApexCharts(document.querySelector("#most-borrowed-chart"), mostBorrowedOptions).render();
        } else {
            document.querySelector("#most-borrowed-chart").innerHTML = '<p class="text-center text-gray-500 py-8">No data to display.</p>';
        }

        // Low Stock Chart
        const lowStockData = <?= json_encode($lowStockData) ?>;
        if(lowStockData.series.length > 0) {
            const lowStockOptions = {
                ...chartOptions,
                series: [{ name: 'Available Stock', data: lowStockData.series }],
                chart: { ...chartOptions.chart, type: 'bar', height: 350 },
                plotOptions: { bar: { borderRadius: 4, horizontal: true } },
                colors: ['#EF4444'],
                xaxis: { ...chartOptions.xaxis, categories: lowStockData.labels, labels: { style: { colors: isDarkMode ? '#9ca3af' : '#6b7280' } } },
                yaxis: { labels: { style: { colors: isDarkMode ? '#9ca3af' : '#6b7280' } } }
            };
            new ApexCharts(document.querySelector("#low-stock-chart"), lowStockOptions).render();
        } else {
             document.querySelector("#low-stock-chart").innerHTML = '<p class="text-center text-gray-500 py-8">No low stock items.</p>';
        }

        // Category Breakdown Chart
        const categoryData = <?= json_encode($categoryBreakdownData) ?>;
        if (categoryData.series.length > 0) {
            const categoryOptions = {
                ...chartOptions,
                series: categoryData.series.map(s => parseInt(s)),
                chart: { ...chartOptions.chart, type: 'donut', height: 350 },
                labels: categoryData.labels,
                legend: { position: 'bottom', labels: { colors: isDarkMode ? '#9ca3af' : '#6b7280' } },
                responsive: [{ breakpoint: 480, options: { chart: { width: 200 }, legend: { position: 'bottom' } } }]
            };
            new ApexCharts(document.querySelector("#category-chart"), categoryOptions).render();
        } else {
            document.querySelector("#category-chart").innerHTML = '<p class="text-center text-gray-500 py-8">No category data.</p>';
        }
    });
    </script>
</body>
</html>