<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

use Nineventory\Auth;
use Nineventory\Inventory;

$auth = new Auth($pdo);
$auth->requireLogin();

$inventory = new Inventory($pdo);
$currentUser = $auth->getCurrentUser();
$isAdmin = $auth->isAdmin();

// Get items
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$items = $inventory->getAll($search, $category);
$categories = $inventory->getCategories();
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Inventory - NINEVENTORY</title>
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
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Lucide Icons -->
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
        
        <!-- Animated Background Gradient -->
        <div class="fixed inset-0 -z-10 bg-gray-50 dark:bg-gray-950">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-orange-500/10 blur-[100px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-red-500/10 blur-[100px] animate-pulse"></div>
        </div>

        <?php 
        $activePage = 'browse';
        $pathPrefix = '../';
        include '../includes/sidebar.php'; 
        ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            
            <?php 
            $pageTitle = 'Browse Inventory';
            include '../includes/header.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth hide-scrollbar">
                
                <!-- Search & Filter -->
                <div class="mb-8">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <form method="GET" class="flex flex-col md:flex-row gap-4">
                            <div class="relative flex-1">
                                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                                <input type="text" name="search" placeholder="Search items..." value="<?= htmlspecialchars($search) ?>" 
                                       class="w-full pl-12 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-900 border border-transparent focus:bg-white dark:focus:bg-gray-800 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition-all placeholder-gray-400 dark:placeholder-gray-500">
                            </div>
                            <div class="w-full md:w-48">
                                <select name="category" onchange="this.form.submit()" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-900 border border-transparent focus:bg-white dark:focus:bg-gray-800 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition-all text-gray-700 dark:text-gray-200">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Grid -->
                <?php if (empty($items)): ?>
                    <div class="text-center py-20">
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="package-open" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">No items found</h3>
                        <p class="text-gray-500 dark:text-gray-400">Try adjusting your search or category filter.</p>
                        <a href="browse.php" class="inline-block mt-4 px-6 py-2 bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 rounded-xl font-bold hover:bg-orange-200 dark:hover:bg-orange-900/50 transition-colors">Clear Filters</a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <?php foreach ($items as $item): ?>
                            <div class="group bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-xl hover:shadow-orange-500/10 transition-all duration-300 flex flex-col h-full">
                                <div class="h-48 bg-gray-100 dark:bg-gray-750 relative overflow-hidden flex items-center justify-center">
                                    <?php if ($item['stok_tersedia'] > 0): ?>
                                        <span class="absolute top-4 right-4 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-lg shadow-sm z-10">Available</span>
                                    <?php else: ?>
                                        <span class="absolute top-4 right-4 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-lg shadow-sm z-10">Out of Stock</span>
                                    <?php endif; ?>
                                    
                                    <!-- Placeholder Icon -->
                                    <i data-lucide="box" class="w-16 h-16 text-gray-300 dark:text-gray-600 group-hover:scale-110 transition-transform duration-500"></i>
                                </div>
                                
                                <div class="p-6 flex-1 flex flex-col">
                                    <div class="flex items-start justify-between mb-2">
                                        <span class="text-xs font-bold text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20 px-2 py-1 rounded-md uppercase tracking-wider">
                                            <?= htmlspecialchars($item['kategori']) ?>
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-1 group-hover:text-orange-500 transition-colors">
                                        <?= htmlspecialchars($item['nama_barang']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-4 flex-1">
                                        <?= htmlspecialchars($item['deskripsi'] ?: 'No description available for this item.') ?>
                                    </p>
                                    
                                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
                                        <div class="text-sm">
                                            <span class="text-gray-500 dark:text-gray-400">Stock:</span>
                                            <span class="font-bold text-gray-900 dark:text-white"><?= $item['stok_tersedia'] ?></span>
                                            <span class="text-gray-400 text-xs">/ <?= $item['stok_total'] ?></span>
                                        </div>
                                        
                                        <?php if ($item['stok_tersedia'] > 0): ?>
                                            <a href="request.php?item_id=<?= $item['id'] ?>" class="flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-sm font-bold hover:bg-orange-600 dark:hover:bg-gray-200 transition-colors">
                                                Borrow <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                            </a>
                                        <?php else: ?>
                                            <button disabled class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-xl text-sm font-bold cursor-not-allowed">
                                                Unavailable
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </main>
        </div>
    </div>
    
    <!-- Initialize Lucide -->
    <script>lucide.createIcons();</script>
    
    <?php include '../includes/chatbot-widget.php'; ?>
    <script src="../assets/js/chatbot.js"></script>
</body>
</html>
