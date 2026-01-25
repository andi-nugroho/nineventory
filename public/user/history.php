<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

use Nineventory\Auth;
use Nineventory\Loan;

$auth = new Auth($pdo);
$auth->requireLogin();

$loan = new Loan($pdo);
$currentUser = $auth->getCurrentUser();
$isAdmin = $auth->isAdmin();

$myLoans = $loan->getByUserId($currentUser['id']);
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan History - NINEVENTORY</title>
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
        $activePage = 'history';
        $pathPrefix = '../';
        include '../includes/sidebar.php';
        ?>


        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">

            <?php
            $pageTitle = 'Loan History';
            include '../includes/header.php';
            ?>

            <main class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth hide-scrollbar">
                <div class="max-w-7xl mx-auto">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">My Loans</h3>
                            <a href="request.php" class="px-4 py-2 bg-orange-600 text-white rounded-xl text-sm font-medium hover:bg-orange-700 shadow-md shadow-orange-500/20">New Request</a>
                        </div>

                        <?php if (empty($myLoans)): ?>
                            <div class="p-12 text-center text-gray-500 dark:text-gray-400 flex flex-col items-center">
                                <i data-lucide="history" class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4"></i>
                                <p>You haven't made any loan requests yet.</p>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                                        <tr>
                                            <th class="px-6 py-4 font-semibold">Request ID</th>
                                            <th class="px-6 py-4 font-semibold">Items</th>
                                            <th class="px-6 py-4 font-semibold">Borrowed Date</th>
                                            <th class="px-6 py-4 font-semibold">Return Date</th>
                                            <th class="px-6 py-4 font-semibold">Status</th>
                                            <th class="px-6 py-4 font-semibold">Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        <?php foreach ($myLoans as $item): ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                                <?= htmlspecialchars($item['kode_peminjaman'] ?? 'LOAN-'.$item['id']) ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate" title="<?= htmlspecialchars($item['items_summary']) ?>">
                                                <?= htmlspecialchars($item['items_summary']) ?>
                                                <span class="text-xs text-gray-400 block mt-0.5"><?= $item['total_items'] ?> item(s)</span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= date('M d, Y', strtotime($item['tanggal_pinjam'])) ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= $item['tanggal_kembali'] ? date('M d, Y', strtotime($item['tanggal_kembali'])) : '-' ?></td>
                                            <td class="px-6 py-4">

                                                <?php
                                                $statusColor = match($item['status']) {
                                                    'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                    'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                                    'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                                    'returned' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                                    default => 'bg-gray-100 text-gray-700'
                                                };
                                                ?>
                                                <div class="flex items-center gap-2">
                                                    <span class="px-2 py-1 rounded-lg text-xs font-semibold <?= $statusColor ?>">
                                                        <?= ucfirst($item['status']) ?>
                                                    </span>
                                                    <?php if (in_array($item['status'], ['approved', 'returned'])): ?>
                                                        <a href="<?= $pathPrefix ?>print_loan.php?id=<?= $item['id'] ?>" target="_blank" class="p-1 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition" title="Export to PDF">
                                                            <i data-lucide="printer" class="w-4 h-4"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                <?php if ($item['status'] === 'rejected' && $item['alasan_reject']): ?>
                                                    <span class="text-red-500 dark:text-red-400">Reason: <?= htmlspecialchars($item['alasan_reject']) ?></span>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($item['keterangan'] ?? '-') ?>
                                                <?php endif; ?>
                                            </td>
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


    <script>lucide.createIcons();</script>

    <?php include '../includes/chatbot-widget.php'; ?>
    <script src="../assets/js/chatbot.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
             const urlParams = new URLSearchParams(window.location.search);
             if (urlParams.has('loan_success')) {
                 if (Alpine.store('cart')) {
                     Alpine.store('cart').clear();
                 }
                 // Remove param from URL without refresh
                 const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                 window.history.replaceState({path:newUrl},'',newUrl);
             }
        });
    </script>
</body>
</html>
