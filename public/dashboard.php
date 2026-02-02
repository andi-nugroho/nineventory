<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

use Nineventory\Auth;
use Nineventory\Inventory;
use Nineventory\Loan;

$auth = new Auth($pdo);
$auth->requireLogin();

$inventory = new Inventory($pdo);
$loan = new Loan($pdo);
$currentUser = $auth->getCurrentUser();
$isAdmin = $auth->isAdmin();


$totalItems = $inventory->countAll();
$availableItems = $inventory->countAvailable();
$activeLoans = $loan->countActive();
$pendingLoans = $loan->countPending();


$recentLoans = $loan->getRecent(5);
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NINEVENTORY</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
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
    <style>
        [x-cloak] { display: none !important; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans text-slate-800 dark:text-white transition-colors duration-300"
      x-data="{
          darkMode: localStorage.getItem('theme') === 'dark',
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          }
      }"
      x-init="$watch('darkMode', val => val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')); if(darkMode) document.documentElement.classList.add('dark');">

    <div class="flex h-screen overflow-hidden bg-gray-50 dark:bg-gray-950">


        <div class="fixed inset-0 -z-10 bg-gray-50 dark:bg-gray-950">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-orange-500/10 blur-[100px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-red-500/10 blur-[100px] animate-pulse"></div>
        </div>

        <?php
        $activePage = 'dashboard';
        $pathPrefix = './';
        include 'includes/sidebar.php';
        ?>


        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">

            <?php
            $pageTitle = 'Overview';
            include 'includes/header.php';
            ?>

            <main class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth hide-scrollbar">


                <div class="mb-8 relative overflow-hidden rounded-3xl bg-gradient-to-br from-orange-500 to-red-600 text-white shadow-xl shadow-orange-500/20 p-8">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-orange-100 font-medium mb-1">Welcome back,</p>
                                <h2 class="text-3xl font-bold mb-4"><?= htmlspecialchars($currentUser['username']) ?>!</h2>
                                <p class="text-white/80 max-w-lg">Manage you inventory, track loans, and approve requests all in one place.</p>
                            </div>
                            <div class="hidden md:block p-3 bg-white/10 rounded-2xl backdrop-blur-sm">
                                <i data-lucide="layout-grid" class="w-8 h-8 text-white"></i>
                            </div>
                        </div>
                    </div>


                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-2xl group-hover:scale-110 transition-transform">
                                <i data-lucide="box" class="w-6 h-6"></i>
                            </div>
                            <span class="text-xs font-bold text-green-500 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-lg">+2.5%</span>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1"><?= $totalItems ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Items</p>
                    </div>

                    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-2xl group-hover:scale-110 transition-transform">
                                <i data-lucide="check-circle" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1"><?= $availableItems ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Available Stock</p>
                    </div>

                    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 rounded-2xl group-hover:scale-110 transition-transform">
                                <i data-lucide="clock" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1"><?= $activeLoans ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Active Loans</p>
                    </div>

                    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-2xl group-hover:scale-110 transition-transform">
                                <i data-lucide="alert-circle" class="w-6 h-6"></i>
                            </div>
                            <?php if($pendingLoans > 0): ?>
                                <span class="flex h-3 w-3 relative">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                </span>
                            <?php endif; ?>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1"><?= $pendingLoans ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pending Requests</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">


                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="font-bold text-gray-900 dark:text-white">Recent Activity</h3>
                            <a href="user/history.php" class="text-sm text-orange-600 font-medium hover:text-orange-700">View All</a>
                        </div>
                        <div class="p-0">
                            <?php if (empty($recentLoans)): ?>
                                <div class="p-8 text-center text-gray-500">No recent activity.</div>
                            <?php else: ?>
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 dark:bg-gray-750 text-xs uppercase text-gray-500 dark:text-gray-400">
                                        <tr>
                                            <th class="px-6 py-4 font-medium">User</th>
                                            <th class="px-6 py-4 font-medium">Item</th>
                                            <th class="px-6 py-4 font-medium">Status</th>
                                            <th class="px-6 py-4 font-medium">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        <?php foreach ($recentLoans as $loan): ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold">
                                                        <?= strtoupper(substr($loan['username'], 0, 1)) ?>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($loan['username']) ?></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300"><?= htmlspecialchars($loan['nama_barang']) ?></td>
                                            <td class="px-6 py-4">
                                                <?php
                                                $statusColor = match($loan['status']) {
                                                    'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                    'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                                    'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                                    'returned' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                                    default => 'bg-gray-100 text-gray-700'
                                                };
                                                ?>
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $statusColor ?>">
                                                    <?= ucfirst($loan['status']) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500"><?= date('M d', strtotime($loan['tanggal_pinjam'])) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>


                    <div class="space-y-6">
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl p-6 text-white shadow-lg shadow-indigo-500/20">
                            <h3 class="text-xl font-bold mb-2">Needs Help?</h3>
                            <p class="text-white/80 text-sm mb-4">Chat with our AI assistant to check stock or policy.</p>
                            <button class="px-4 py-2 bg-white text-indigo-600 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition-shadow">
                                Open Chat
                            </button>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-6">
                            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Quick Links</h3>
                            <div class="space-y-2">
                                <a href="user/browse.php" class="flex items-center gap-3 p-3 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors group">
                                    <div class="p-2 bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 rounded-xl group-hover:scale-110 transition-transform">
                                        <i data-lucide="search" class="w-4 h-4"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Browse Catalog</span>
                                    <i data-lucide="chevron-right" class="w-4 h-4 ml-auto text-gray-400"></i>
                                </a>
                                <a href="user/request.php" class="flex items-center gap-3 p-3 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors group">
                                    <div class="p-2 bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 rounded-xl group-hover:scale-110 transition-transform">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">New Loan Request</span>
                                    <i data-lucide="chevron-right" class="w-4 h-4 ml-auto text-gray-400"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    
    <script>lucide.createIcons();</script>

    <?php include 'includes/chatbot-widget.php'; ?>
    <script src="assets/js/chatbot.js"></script>
</body>
</html>
