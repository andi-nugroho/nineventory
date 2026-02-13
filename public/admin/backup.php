<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../lib/functions.php';

use Nineventory\Auth;

$auth = new Auth($pdo);
$auth->requireAdmin();

$currentUser = $auth->getCurrentUser();
$isAdmin = $auth->isAdmin();

$message = '';
$error = '';

// Handle backup action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'backup') {
        $result = backupDatabase('nineventory');
        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['error'];
        }
    } elseif ($_POST['action'] === 'delete' && isset($_POST['filename'])) {
        $result = deleteBackupFile($_POST['filename']);
        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['error'];
        }
    }
}

// Handle download
if (isset($_GET['download'])) {
    downloadBackupFile($_GET['download']);
}

// Get backup files
$backupFiles = getBackupFiles();
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Database - NINEVENTORY</title>
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
                        primary: '#FF6626',
                        secondary: '#1E293B',
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

        <!-- Background Effects -->
        <div class="fixed inset-0 -z-10 bg-gray-50 dark:bg-gray-950">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-orange-500/10 blur-[100px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-red-500/10 blur-[100px] animate-pulse"></div>
        </div>

        <?php
        $activePage = 'backup';
        $pathPrefix = '../';
        include '../includes/sidebar.php';
        ?>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">

            <?php
            $pageTitle = 'Backup Database';
            include '../includes/header.php';
            ?>

            <main class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth hide-scrollbar">
                <div class="max-w-5xl mx-auto space-y-6">

                    <!-- Success Message -->
                    <?php if ($message): ?>
                        <div class="p-4 rounded-2xl bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800 flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5"></i> <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Error Message -->
                    <?php if ($error): ?>
                        <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Backup Action Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    <i data-lucide="database" class="w-5 h-5 text-orange-500"></i>
                                    Backup Database
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Buat backup database untuk mengamankan data Anda
                                </p>
                            </div>
                            <form method="POST" class="flex-shrink-0">
                                <input type="hidden" name="action" value="backup">
                                <button type="submit" class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl flex items-center justify-center gap-2 hover:from-orange-600 hover:to-red-600 transition-all shadow-lg hover:shadow-orange-500/25 font-medium">
                                    <i data-lucide="hard-drive-download" class="w-5 h-5"></i>
                                    Backup Sekarang
                                </button>
                            </form>
                        </div>
                        
                        <!-- Info Banner -->
                        <div class="px-6 py-4 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-100 dark:border-blue-800">
                            <div class="flex items-start gap-3">
                                <i data-lucide="info" class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5"></i>
                                <div class="text-sm text-blue-700 dark:text-blue-300">
                                    <p class="font-medium">Informasi Backup</p>
                                    <ul class="mt-1 space-y-1 text-blue-600 dark:text-blue-400">
                                        <li>• File backup akan disimpan dalam format SQL</li>
                                        <li>• Nama file mencakup timestamp untuk identifikasi</li>
                                        <li>• Database: <span class="font-mono bg-blue-100 dark:bg-blue-800 px-1 rounded">nineventory</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Backup Files List -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <i data-lucide="folder-archive" class="w-5 h-5 text-orange-500"></i>
                                Daftar File Backup
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Total: <?= count($backupFiles) ?> file backup tersedia
                            </p>
                        </div>
                        <div class="overflow-x-auto">
                            <?php if (empty($backupFiles)): ?>
                                <div class="p-12 text-center">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <i data-lucide="archive" class="w-8 h-8 text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada file backup</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Klik tombol "Backup Sekarang" untuk membuat backup pertama</p>
                                </div>
                            <?php else: ?>
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                                        <tr>
                                            <th class="px-6 py-4 font-semibold">
                                                <div class="flex items-center gap-2">
                                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                                    Nama File
                                                </div>
                                            </th>
                                            <th class="px-6 py-4 font-semibold">
                                                <div class="flex items-center gap-2">
                                                    <i data-lucide="hard-drive" class="w-4 h-4"></i>
                                                    Ukuran
                                                </div>
                                            </th>
                                            <th class="px-6 py-4 font-semibold">
                                                <div class="flex items-center gap-2">
                                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                                    Tanggal
                                                </div>
                                            </th>
                                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        <?php foreach ($backupFiles as $file): ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                                        <i data-lucide="database" class="w-5 h-5 text-orange-600 dark:text-orange-400"></i>
                                                    </div>
                                                    <span class="font-medium text-gray-900 dark:text-white font-mono text-sm"><?= htmlspecialchars($file['name']) ?></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                    <?= htmlspecialchars($file['size']) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($file['date']) ?></td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="?download=<?= urlencode($file['name']) ?>" class="p-2 text-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors" title="Download">
                                                        <i data-lucide="download" class="w-4 h-4"></i>
                                                    </a>
                                                    <form method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus file backup ini?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="filename" value="<?= htmlspecialchars($file['name']) ?>">
                                                        <button type="submit" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Hapus">
                                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>lucide.createIcons();</script>

    <?php include '../includes/chatbot-widget.php'; ?>
    <script src="../assets/js/chatbot.js"></script>
</body>
</html>
