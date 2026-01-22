<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

use Nineventory\Auth;
use Nineventory\Inventory;

$auth = new Auth($pdo);
$auth->requireAdmin();

$inventory = new Inventory($pdo);
$currentUser = $auth->getCurrentUser();
$isAdmin = $auth->isAdmin();

$message = '';
$error = '';

// Handle actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? null;
    
    if ($action === 'delete' && $id) {
        $result = $inventory->delete($id);
        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_barang' => trim($_POST['nama_barang'] ?? ''),
        'kategori' => trim($_POST['kategori'] ?? ''),
        'stok_total' => intval($_POST['stok_total'] ?? 0),
        'kondisi' => $_POST['kondisi'] ?? 'baik',
        'lokasi' => trim($_POST['lokasi'] ?? ''),
        'deskripsi' => trim($_POST['deskripsi'] ?? '')
    ];
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $result = $inventory->update($_POST['id'], $data);
    } else {
        // Create
        $result = $inventory->create($data);
    }
    
    if ($result['success']) {
        $message = $result['message'];
    } else {
        $error = $result['message'];
    }
}

// Get all inventory
$items = $inventory->getAll();
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inventory - NINEVENTORY</title>
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
        $activePage = 'inventory';
        $pathPrefix = '../';
        include '../includes/sidebar.php'; 
        ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            
            <?php 
            $pageTitle = 'Manage Inventory';
            include '../includes/header.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth hide-scrollbar">
                <div class="max-w-7xl mx-auto space-y-6">
                    
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

                    <!-- Add Item Form -->
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="w-full md:w-auto px-4 py-2 bg-orange-600 text-white rounded-xl mb-4 flex items-center gap-2 hover:bg-orange-700 transition">
                            <i data-lucide="plus" class="w-4 h-4"></i> <span x-text="open ? 'Close Form' : 'Add New Item'"></span>
                        </button>
                        
                        <div x-show="open" x-transition.origin.top class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm mb-8">
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Add New Item</h3>
                            </div>
                            <div class="p-6">
                                <form method="POST" class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Item Name</label>
                                            <input type="text" name="nama_barang" required class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 dark:focus:ring-orange-500/40 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                            <input type="text" name="kategori" required class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 dark:focus:ring-orange-500/40 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Stock</label>
                                            <input type="number" name="stok_total" min="0" required class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 dark:focus:ring-orange-500/40 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Condition</label>
                                            <select name="kondisi" class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 dark:focus:ring-orange-500/40 outline-none">
                                                <option value="baik">Good (Baik)</option>
                                                <option value="rusak ringan">Minor Damage (Rusak Ringan)</option>
                                                <option value="rusak berat">Major Damage (Rusak Berat)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                                            <input type="text" name="lokasi" required class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 dark:focus:ring-orange-500/40 outline-none">
                                        </div>
                                        <div class="col-span-full">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                            <textarea name="deskripsi" rows="2" class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 dark:focus:ring-orange-500/40 outline-none"></textarea>
                                        </div>
                                    </div>
                                    <button type="submit" class="px-6 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-xl hover:opacity-90 transition">Save Item</button>
                                </form>
                            </div>
                        </div>
                    </div>
                
                    <!-- Inventory List -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Inventory List</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                                    <tr>
                                        <th class="px-6 py-4 font-semibold">Item Name</th>
                                        <th class="px-6 py-4 font-semibold">Category</th>
                                        <th class="px-6 py-4 font-semibold">Stock (Total/Avail)</th>
                                        <th class="px-6 py-4 font-semibold">Condition</th>
                                        <th class="px-6 py-4 font-semibold">Location</th>
                                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <?php foreach ($items as $item): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($item['nama_barang']) ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($item['kategori']) ?></td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="font-bold text-gray-900 dark:text-gray-200"><?= $item['stok_total'] ?></span>
                                            <span class="text-gray-400 mx-1">/</span>
                                            <span class="<?= $item['stok_tersedia'] > 0 ? 'text-green-600' : 'text-red-500' ?>"><?= $item['stok_tersedia'] ?></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-lg text-xs font-semibold <?= $item['kondisi'] === 'baik' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' ?>">
                                                <?= ucfirst($item['kondisi']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($item['lokasi']) ?></td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="?action=delete&id=<?= $item['id'] ?>" onclick="return confirm('Delete this item?')" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Initialize Lucide -->
    <script>lucide.createIcons();</script>
    
    <?php include '../includes/chatbot-widget.php'; ?>
    <script src="../assets/js/chatbot.js"></script>
</body>
</html>
