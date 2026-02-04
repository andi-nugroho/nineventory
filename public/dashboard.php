<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

use Nineventory\Auth;
use Nineventory\Inventory;
use Nineventory\Loan;

$auth = new Auth($pdo);
$auth->requireLogin();

$inventory = new Inventory($pdo);
$loan = new Loan($pdo);
$currentUser = $auth->getCurrentUser();
$isAdmin = $auth->isAdmin();

if ($isAdmin) {
    // Admin Dashboard Data
    $totalItems = $inventory->countAll();
    $availableItems = $inventory->countAvailable();
    $activeLoansGlobal = $loan->countActive();
    $pendingLoansGlobal = $loan->countPending();
    $recentLoans = $loan->getRecent(5);
    $lowStockItems = $inventory->getLowStock(5);
} else {
    // User Dashboard Data
    $userStats = $loan->getLoanStatsForUser($currentUser['id']);
    $activeUserLoans = $loan->getActiveLoansByUserId($currentUser['id']);
}

$pageTitle = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - NINEVENTORY</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['DM Sans', 'sans-serif'] },
                    colors: { primary: '#FF6626', secondary: '#1E293B' }
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
        $activePage = 'dashboard';
        $pathPrefix = './';
        include 'includes/sidebar.php';
        ?>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            <?php include 'includes/header.php'; ?>

            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                <div class="max-w-7xl mx-auto space-y-8">
                    <!-- Welcome Header -->
                    <div class="mb-8 relative overflow-hidden rounded-3xl bg-gradient-to-br from-orange-500 to-red-600 text-white shadow-xl shadow-orange-500/20 p-8">
                        <div class="relative z-10">
                            <p class="text-orange-100 font-medium mb-1">Welcome back,</p>
                            <h2 class="text-3xl font-bold mb-2"><?= htmlspecialchars($currentUser['username']) ?>!</h2>
                            <p class="text-white/80 max-w-lg">
                                <?= $isAdmin ? 'Manage your inventory, track loans, and approve requests all in one place.' : 'Here is an overview of your current loans and requests.' ?>
                            </p>
                        </div>
                    </div>

                    <?php if ($isAdmin): ?>
                        <!-- ADMIN DASHBOARD -->
                        <?php include 'includes/admin_dashboard_content.php'; ?>
                    <?php else: ?>
                        <!-- USER DASHBOARD -->
                        <?php include 'includes/user_dashboard_content.php'; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    <script>lucide.createIcons();</script>
    <?php include 'includes/chatbot-widget.php'; ?>
    <script src="assets/js/chatbot.js"></script>
</body>
</html>