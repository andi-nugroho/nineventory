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

$itemId = $_GET['id'] ?? null;

if (!$itemId) {
    // Redirect or show error if no ID is provided
    header("Location: browse.php");
    exit;
}

$item = $inventory->getById($itemId);

// If item not found, show a 404-like page
if (!$item) {
    http_response_code(404);
    $pageTitle = 'Not Found';
    // We can create a simple not found view or redirect
    // For now, let's just show a message.
    $notFound = true;
} else {
    $pageTitle = htmlspecialchars($item['nama_barang']);
    $notFound = false;
}

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
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans text-slate-800 dark:text-white transition-colors duration-300"
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
      x-init="$watch('darkMode', val => { localStorage.setItem('theme', val ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', val) }); if (darkMode) document.documentElement.classList.add('dark')">

    <div class="flex h-screen overflow-hidden bg-gray-50 dark:bg-gray-950">
        <div class="fixed inset-0 -z-10 bg-gray-50 dark:bg-gray-950">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-orange-500/10 blur-[100px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-red-500/10 blur-[100px] animate-pulse"></div>
        </div>

        <?php
        $activePage = 'browse';
        $pathPrefix = '../';
        include '../includes/sidebar.php';
        ?>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            <?php include '../includes/header.php'; ?>

            <main class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth hide-scrollbar">
                <div class="max-w-4xl mx-auto">
                    <?php if ($notFound): ?>
                        <div class="text-center py-20">
                            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="alert-triangle" class="w-10 h-10 text-gray-400"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Item Not Found</h3>
                            <p class="text-gray-500 dark:text-gray-400">The item you are looking for does not exist or has been removed.</p>
                            <a href="browse.php" class="inline-block mt-6 px-6 py-2 bg-orange-600 text-white rounded-xl font-bold hover:bg-orange-700 transition-colors">
                                Back to Browse
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-lg shadow-orange-500/5">
                            <div class="p-6 md:p-8">
                                <div class="flex flex-col md:flex-row md:items-start gap-8">
                                    <div class="w-full md:w-1/3 h-64 bg-gray-100 dark:bg-gray-750 rounded-2xl flex items-center justify-center overflow-hidden">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="../assets/product/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['nama_barang']) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                             <i data-lucide="box" class="w-24 h-24 text-gray-300 dark:text-gray-600"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm font-bold text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20 px-3 py-1 rounded-md uppercase tracking-wider">
                                                <?= htmlspecialchars($item['kategori']) ?>
                                            </span>
                                            <?php if ($item['stok_tersedia'] > 0): ?>
                                                <span class="bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-sm font-bold px-3 py-1 rounded-lg">Available</span>
                                            <?php else: ?>
                                                <span class="bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 text-sm font-bold px-3 py-1 rounded-lg">Out of Stock</span>
                                            <?php endif; ?>
                                        </div>
                                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4"><?= htmlspecialchars($item['nama_barang']) ?></h1>
                                        <p class="text-gray-600 dark:text-gray-300 mb-6">
                                            <?= nl2br(htmlspecialchars($item['deskripsi'] ?: 'No description available for this item.')) ?>
                                        </p>

                                        <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                                <span class="block text-gray-500 dark:text-gray-400">Total Stock</span>
                                                <span class="font-bold text-lg text-gray-900 dark:text-white"><?= $item['stok_total'] ?></span>
                                            </div>
                                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                                <span class="block text-gray-500 dark:text-gray-400">Available Stock</span>
                                                <span class="font-bold text-lg text-gray-900 dark:text-white"><?= $item['stok_tersedia'] ?></span>
                                            </div>
                                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                                <span class="block text-gray-500 dark:text-gray-400">Condition</span>
                                                <span class="font-bold text-lg text-gray-900 dark:text-white"><?= htmlspecialchars(ucfirst($item['kondisi'])) ?></span>
                                            </div>
                                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                                <span class="block text-gray-500 dark:text-gray-400">Location</span>
                                                <span class="font-bold text-lg text-gray-900 dark:text-white"><?= htmlspecialchars($item['lokasi']) ?></span>
                                            </div>
                                        </div>

                                        <div class="mt-8 flex items-center gap-4">
                                            <?php if ($item['stok_tersedia'] > 0): ?>
                                                <a href="request.php?item_id=<?= $item['id'] ?>" class="flex-1 text-center px-6 py-3 bg-orange-600 text-white rounded-xl text-base font-bold hover:bg-orange-700 transition-colors">
                                                    Borrow Now
                                                </a>
                                            <?php else: ?>
                                                <button disabled class="flex-1 text-center px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-400 rounded-xl text-base font-bold cursor-not-allowed">
                                                    Currently Unavailable
                                                </button>
                                            <?php endif; ?>
                                            <a href="browse.php" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl text-base font-bold hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                                Back
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script>lucide.createIcons();</script>
    <?php include '../includes/chatbot-widget.php'; ?>
    <script src="../assets/js/chatbot.js"></script>
</body>
</html>
