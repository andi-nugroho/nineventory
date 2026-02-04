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

$message = '';
$error = '';

// Handle the return action
if (isset($_GET['action']) && $_GET['action'] === 'mark_returned' && isset($_GET['id'])) {
    $returnResult = $loan->markReturned($_GET['id']);
    if ($returnResult['success']) {
        // Redirect to clean the URL and show a success message
        header("Location: history.php?success=returned");
        exit;
    } else {
        $error = $returnResult['message'];
    }
}

// Check for success messages from redirect
if(isset($_GET['success']) && $_GET['success'] === 'returned') {
    $message = 'Item successfully marked as returned.';
}
if(isset($_GET['loan_success'])) {
    $message = 'Loan request submitted successfully!';
}


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

                    <?php if ($message): ?>
                        <div class="mb-4 p-4 rounded-2xl bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800 flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5"></i> <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>
                     <?php if ($error): ?>
                        <div class="mb-4 p-4 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

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
                                            <th class="px-6 py-4 font-semibold">For Employee</th>
                                            <th class="px-6 py-4 font-semibold">Items</th>
                                            <th class="px-6 py-4 font-semibold">Status</th>
                                            <th class="px-6 py-4 font-semibold text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        <?php foreach ($myLoans as $item): ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                                <a href="loan_detail.php?id=<?= $item['id'] ?>" class="hover:text-orange-500 hover:underline">
                                                    <?= htmlspecialchars($item['kode_peminjaman'] ?? 'LOAN-'.$item['id']) ?>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                                <?= htmlspecialchars($item['nama_karyawan'] ?? '-') ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                <ul class="list-disc pl-5 space-y-1">
                                                    <?php foreach ($item['details'] as $detail): ?>
                                                        <li>
                                                            <a href="detail.php?id=<?= $detail['inventaris_id'] ?>" class="hover:text-orange-500 hover:underline">
                                                                <?= htmlspecialchars($detail['nama_barang']) ?>
                                                            </a>
                                                            <span class="text-xs">(x<?= $detail['jumlah'] ?>)</span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </td>
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
                                                <span class="px-2 py-1 rounded-lg text-xs font-semibold <?= $statusColor ?>">
                                                    <?= ucfirst($item['status']) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="loan_detail.php?id=<?= $item['id'] ?>" class="inline-block px-3 py-1 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-xs font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="View Details">
                                                    View Details
                                                </a>
                                                <?php if ($item['status'] === 'approved'): ?>
                                                    <a href="?action=mark_returned&id=<?= $item['id'] ?>" 
                                                       onclick="return confirm('Are you sure you want to mark this loan as returned? This action cannot be undone.')" 
                                                       class="px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-lg text-xs font-semibold hover:bg-blue-200 transition">
                                                        Mark as Returned
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= $pathPrefix ?>print_loan.php?id=<?= $item['id'] ?>" target="_blank" class="inline-block p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition" title="Export to PDF">
                                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                                </a>
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
    <script>
        document.addEventListener('alpine:init', () => {
             const urlParams = new URLSearchParams(window.location.search);
             if (urlParams.has('loan_success') || urlParams.has('success')) {
                 const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                 window.history.replaceState({path:newUrl},'',newUrl);
             }
        });
    </script>
</body>
</html>