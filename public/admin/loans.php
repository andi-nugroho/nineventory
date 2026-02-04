<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

use Nineventory\Auth;
use Nineventory\Loan;

$auth = new Auth($pdo);
$auth->requireAdmin();

$loan = new Loan($pdo);
$currentUser = $auth->getCurrentUser();
$isAdmin = $auth->isAdmin();

$message = '';
$error = '';


if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    if ($action === 'reject') {
        $result = $loan->reject($id, 'Ditolak oleh admin');
        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['message'];
        }
    } elseif ($action === 'return') {
        $result = $loan->markReturned($id);
        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_loan'])) {
    $loan_id = intval($_POST['loan_id'] ?? 0);
    $tanggal_kembali = $_POST['tanggal_kembali_rencana'] ?? null;

    if ($loan_id && $tanggal_kembali) {
        $result = $loan->approve($loan_id, $tanggal_kembali);
        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['message'];
        }
    } else {
        $error = 'Tanggal pengembalian harus diisi';
    }
}


$allLoans = $loan->getAll();
$pendingLoans = $loan->getPending();
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Approvals - NINEVENTORY</title>
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
          modalOpen: false,
          modalData: {
              id: null,
              items: [],
              user: '',
              employee: '',
              date: '',
              minReturnDate: ''
          },
          openApprovalModal(loan) {
              this.modalData.id = loan.id;
              this.modalData.items = loan.details;
              this.modalData.user = loan.username;
              this.modalData.employee = loan.nama_karyawan;
              this.modalData.date = loan.tanggal_pinjam;

              // Set default return date to 7 days from now
              let date = new Date(loan.tanggal_pinjam);
              date.setDate(date.getDate() + 7);
              this.modalData.defaultReturn = date.toISOString().split('T')[0];

              // Min date is tomorrow
              let minDate = new Date(loan.tanggal_pinjam);
              minDate.setDate(minDate.getDate() + 1);
              this.modalData.minReturnDate = minDate.toISOString().split('T')[0];

              this.modalOpen = true;
          },
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
        $activePage = 'loans';
        $pathPrefix = '../';
        include '../includes/sidebar.php';
        ?>


        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">

            <?php
            $pageTitle = 'Approvals';
            include '../includes/header.php';
            ?>

            <main class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth hide-scrollbar">
                <div class="max-w-7xl mx-auto space-y-8">

                    <?php if ($message): ?>
                        <div class="p-4 rounded-2xl bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800 flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5"></i> <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>


                    <div class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                             Pending Requests <span class="bg-orange-100 text-orange-600 dark:bg-orange-900/40 dark:text-orange-400 px-2 py-0.5 rounded-lg text-xs"><?= count($pendingLoans) ?></span>
                        </h3>

                        <?php if (empty($pendingLoans)): ?>
                            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 border border-gray-100 dark:border-gray-700 text-center text-gray-500">
                                <i data-lucide="check-circle" class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600"></i>
                                <p>No pending requests.</p>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php foreach ($pendingLoans as $item): ?>
                                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center font-bold text-gray-600 dark:text-gray-300">
                                                <?= strtoupper(substr($item['username'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($item['username']) ?></div>
                                                <?php if (!empty($item['nama_karyawan'])): ?>
                                                    <div class="text-xs text-gray-500" title="Nama Karyawan">for <?= htmlspecialchars($item['nama_karyawan']) ?></div>
                                                <?php endif; ?>
                                                <div class="text-xs text-gray-500">ID: <?= $item['kode_peminjaman'] ?? 'LOAN-' . $item['id'] ?></div>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 text-xs font-semibold rounded-lg">Pending</span>
                                    </div>

                                    <div class="space-y-2 mb-6 flex-1">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500 dark:text-gray-400">Date:</span>
                                            <span class="font-medium text-gray-900 dark:text-white"><?= date('M d, Y', strtotime($item['tanggal_pinjam'])) ?></span>
                                        </div>
                                        <div class="block text-sm">
                                            <span class="text-gray-500 dark:text-gray-400 block mb-1">Items (<?= count($item['details']) ?>):</span>
                                            <ul class="font-medium text-gray-900 dark:text-white text-sm list-disc pl-5 space-y-1">
                                                <?php foreach ($item['details'] as $detail): ?>
                                                    <li><?= htmlspecialchars($detail['nama_barang']) ?> <span class="text-gray-500">(x<?= $detail['jumlah'] ?>)</span></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <?php if($item['keterangan']): ?>
                                        <div class="bg-gray-50 dark:bg-gray-750 p-3 rounded-xl text-sm text-gray-600 dark:text-gray-300 mt-2 italic">
                                            "<?= htmlspecialchars($item['keterangan']) ?>"
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex gap-2 mt-auto">
                                        <button @click='openApprovalModal(<?= json_encode($item) ?>)' class="flex-1 py-2 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition">Approve</button>
                                        <a href="?action=reject&id=<?= $item['id'] ?>" onclick="return confirm('Reject this request?')" class="px-4 py-2 bg-red-100 text-red-600 dark:bg-red-900/20 dark:text-red-400 rounded-xl font-medium hover:bg-red-200 dark:hover:bg-red-900/40 transition">Reject</a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>


                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">All Loans History</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                                    <tr>
                                        <th class="px-6 py-4 font-semibold">User</th>
                                        <th class="px-6 py-4 font-semibold">Employee</th>
                                        <th class="px-6 py-4 font-semibold">Items</th>
                                        <th class="px-6 py-4 font-semibold">Borrowed</th>
                                        <th class="px-6 py-4 font-semibold">Due Date</th>
                                        <th class="px-6 py-4 font-semibold">Returned</th>
                                        <th class="px-6 py-4 font-semibold">Status</th>
                                        <th class="px-6 py-4 font-semibold text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <?php foreach ($allLoans as $item): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($item['username']) ?></td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($item['nama_karyawan'] ?? '-') ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <?= count($item['details']) ?> item(s)
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= date('M d, Y', strtotime($item['tanggal_pinjam'])) ?></td>
                                        <td class="px-6 py-4">
                                            <?php if ($item['tanggal_kembali_rencana']): ?>
                                                <span class="px-2 py-1 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg text-xs font-medium">
                                                    <?= date('M d', strtotime($item['tanggal_kembali_rencana'])) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-gray-300">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <?= $item['tanggal_kembali'] ? date('M d, Y', strtotime($item['tanggal_kembali'])) : '-' ?>
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
                                            <?php if ($item['status'] === 'approved'): ?>
                                                <a href="?action=return&id=<?= $item['id'] ?>" onclick="return confirm('Mark as returned?')" class="px-3 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-lg text-xs font-medium hover:opacity-90 transition">Return</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>


            <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="modalOpen = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="modalOpen" x-transition.scale class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                        <form method="POST">
                            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4" id="modal-title">Approve Loan</h3>

                                <input type="hidden" name="loan_id" x-model="modalData.id">
                                <input type="hidden" name="approve_loan" value="1">

                                <div class="space-y-3">
                                    <div class="flex justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                                        <span class="text-gray-500 dark:text-gray-400">User</span>
                                        <span class="font-medium text-gray-900 dark:text-white" x-text="modalData.user"></span>
                                    </div>
                                    <div x-show="modalData.employee" class="flex justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                                        <span class="text-gray-500 dark:text-gray-400">Employee</span>
                                        <span class="font-medium text-gray-900 dark:text-white" x-text="modalData.employee"></span>
                                    </div>
                                    <div class="border-b border-gray-100 dark:border-gray-700 pb-2">
                                        <span class="text-gray-500 dark:text-gray-400 block mb-1">Items</span>
                                        <ul class="font-medium text-gray-900 dark:text-white text-sm list-disc pl-5 space-y-1">
                                            <template x-for="item in modalData.items" :key="item.id">
                                                <li>
                                                    <span x-text="item.nama_barang"></span>
                                                    <span class="text-gray-500" x-text="'(x' + item.jumlah + ')'"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                                        <span class="text-gray-500 dark:text-gray-400">Borrowed On</span>
                                        <span class="font-medium text-gray-900 dark:text-white" x-text="modalData.date"></span>
                                    </div>

                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Return Date Plan <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="tanggal_kembali_rencana" required
                                               :min="modalData.minReturnDate"
                                               x-model="modalData.defaultReturn"
                                               class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 outline-none">
                                        <p class="text-xs text-gray-500 mt-1">Specify when these items must be returned.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/30 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                    Approve Loan
                                </button>
                                <button type="button" @click="modalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    
    <script>lucide.createIcons();</script>

    <?php include '../includes/chatbot-widget.php'; ?>
    <script src="../assets/js/chatbot.js"></script>
</body>
</html>
