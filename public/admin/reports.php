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

$mostBorrowed = $loan->getMostBorrowedItems(10);
$lowStock = $inventory->getLowStock(10);

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
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-orange-500/10 blur-[100px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-red-500/10 blur-[100px]"></div>
        </div>

        <?php
        $activePage = 'reports'; // New active page
        $pathPrefix = '../';
        include '../includes/sidebar.php';
        ?>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            <?php include '../includes/header.php'; ?>

            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                <div class="max-w-7xl mx-auto space-y-8">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        
                        <!-- Most Borrowed Items Report -->
                        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Most Borrowed Items</h3>
                            </div>
                            <?php if (empty($mostBorrowed)): ?>
                                <p class="p-6 text-gray-500 dark:text-gray-400">No borrowing data available.</p>
                            <?php else: ?>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                                            <tr>
                                                <th class="px-6 py-4 font-semibold">Item Name</th>
                                                <th class="px-6 py-4 font-semibold text-center">Times Borrowed</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                            <?php foreach ($mostBorrowed as $item): ?>
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($item['nama_barang']) ?></td>
                                                <td class="px-6 py-4 text-center text-lg font-bold text-gray-900 dark:text-white"><?= $item['total_borrowed'] ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Low Stock Items Report -->
                        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Low Stock Items</h3>
                                <p class="text-sm text-gray-500">Showing items with stock less than or equal to 10.</p>
                            </div>
                            <?php if (empty($lowStock)): ?>
                                <p class="p-6 text-gray-500 dark:text-gray-400">All items are well-stocked.</p>
                            <?php else: ?>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                                            <tr>
                                                <th class="px-6 py-4 font-semibold">Item Name</th>
                                                <th class="px-6 py-4 font-semibold text-center">Available Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                            <?php foreach ($lowStock as $item): ?>
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($item['nama_barang']) ?></td>
                                                <td class="px-6 py-4 text-center text-lg font-bold text-red-500"><?= $item['stok_tersedia'] ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
