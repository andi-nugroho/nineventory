<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

use Nineventory\Auth;
use Nineventory\Loan;

$auth = new Auth($pdo);
$auth->requireLogin();

$loan = new Loan($pdo);
$currentUser = $auth->getCurrentUser();

$loanId = $_GET['id'] ?? null;
$loanDetails = null;

if ($loanId) {
    $loanDetails = $loan->getById($loanId);
}

// Security check: ensure the user is viewing their own loan, unless they are an admin
if (!$loanDetails || ($loanDetails['user_id'] !== $currentUser['id'] && !$auth->isAdmin())) {
    header("Location: history.php?error=notfound");
    exit;
}

$pageTitle = 'Loan Detail: ' . htmlspecialchars($loanDetails['kode_peminjaman'] ?? 'LOAN-' . $loanDetails['id']);
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
          }
      }"
      x-init="$watch('darkMode', val => document.documentElement.classList.toggle('dark', val))">

    <div class="flex h-screen overflow-hidden bg-gray-50 dark:bg-gray-950">
        <div class="fixed inset-0 -z-10 bg-gray-50 dark:bg-gray-950">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-orange-500/10 blur-[100px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-red-500/10 blur-[100px]"></div>
        </div>

        <?php
        $activePage = 'history';
        $pathPrefix = '../';
        include '../includes/sidebar.php';
        ?>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            <?php include '../includes/header.php'; ?>

            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?= $pageTitle ?></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Details of your loan request.</p>
                            </div>
                            <a href="history.php" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm">
                                Back to History
                            </a>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">For Employee</span>
                                    <span class="font-bold text-lg text-gray-900 dark:text-white"><?= htmlspecialchars($loanDetails['nama_karyawan'] ?? '-') ?></span>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">Status</span>
                                    <span class="font-bold text-lg text-gray-900 dark:text-white"><?= ucfirst($loanDetails['status']) ?></span>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">Borrow Date</span>
                                    <span class="font-bold text-lg text-gray-900 dark:text-white"><?= date('M d, Y', strtotime($loanDetails['tanggal_pinjam'])) ?></span>
                                </div>
                                <?php if ($loanDetails['tanggal_kembali_rencana']): ?>
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">Due Date</span>
                                    <span class="font-bold text-lg text-gray-900 dark:text-white"><?= date('M d, Y', strtotime($loanDetails['tanggal_kembali_rencana'])) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($loanDetails['tanggal_kembali']): ?>
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">Returned On</span>
                                    <span class="font-bold text-lg text-gray-900 dark:text-white"><?= date('M d, Y', strtotime($loanDetails['tanggal_kembali'])) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($loanDetails['keterangan'])): ?>
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">Notes / Purpose</span>
                                    <p class="text-gray-800 dark:text-gray-200 mt-1 italic">"<?= htmlspecialchars($loanDetails['keterangan']) ?>"</p>
                                </div>
                            <?php endif; ?>
                             <?php if (!empty($loanDetails['alasan_reject'])): ?>
                                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                    <span class="block text-sm text-red-600 dark:text-red-400">Rejection Reason</span>
                                    <p class="text-red-800 dark:text-red-200 mt-1">"<?= htmlspecialchars($loanDetails['alasan_reject']) ?>"</p>
                                </div>
                            <?php endif; ?>

                            <div>
                                <h4 class="text-md font-bold text-gray-900 dark:text-white mb-2">Requested Items</h4>
                                <div class="border rounded-lg overflow-hidden dark:border-gray-700">
                                    <table class="w-full text-left">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                                            <tr>
                                                <th class="px-4 py-2 font-semibold">Item Name</th>
                                                <th class="px-4 py-2 font-semibold">Category</th>
                                                <th class="px-4 py-2 font-semibold text-center">Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($loanDetails['details'] as $detail): ?>
                                            <tr class="border-t dark:border-gray-700">
                                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                                    <a href="detail.php?id=<?= $detail['inventaris_id'] ?>" class="hover:text-orange-500 hover:underline">
                                                        <?= htmlspecialchars($detail['nama_barang']) ?>
                                                    </a>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300"><?= htmlspecialchars($detail['kategori']) ?></td>
                                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 text-center"><?= $detail['jumlah'] ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
