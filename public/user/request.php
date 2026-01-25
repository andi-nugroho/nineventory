<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

use Nineventory\Auth;
use Nineventory\Inventory;
use Nineventory\Loan;

$auth = new Auth($pdo);
$auth->requireLogin();

$inventory = new Inventory($pdo);
$loan = new Loan($pdo);
$currentUser = $auth->getCurrentUser();
$isAdmin = $auth->isAdmin();

$message = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $itemsJson = $_POST['items_json'] ?? '[]';
    $tanggal_pinjam = $_POST['tanggal_pinjam'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');

    $items = json_decode($itemsJson, true);

    if (!empty($items) && is_array($items) && $tanggal_pinjam) {
        $result = $loan->create($currentUser['id'], $items, $tanggal_pinjam, $keterangan);

        if ($result['success']) {
            $message = $result['message'];
            
             header("Location: history.php"); 
             exit;
        } else {
            $error = $result['message'];
        }
    } else {
        $error = 'Please select at least one item and provide a date.';
    }
}


$availableItems = $inventory->getAvailable();

$selectedItemId = intval($_GET['item_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Loan Request - NINEVENTORY</title>
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
        $activePage = 'request';
        $pathPrefix = '../';
        include '../includes/sidebar.php';
        ?>


        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">

            <?php
            $pageTitle = 'Loan Request';
            include '../includes/header.php';
            ?>

            <main class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth hide-scrollbar"
                  x-data="loanRequestApp(<?= htmlspecialchars(json_encode($availableItems)) ?>, <?= $selectedItemId ?>)">
                <div class="max-w-4xl mx-auto">

                    <?php if ($message): ?>
                        <div class="mb-6 p-4 rounded-2xl bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800 flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="mb-6 p-4 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Create Loan Request</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Add items to your cart and submit the request.</p>
                        </div>

                        <div class="p-6 md:p-8 space-y-8">
                            
                            
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-700">
                                <h4 class="font-medium mb-4 flex items-center gap-2">
                                    <i data-lucide="plus-circle" class="w-4 h-4 text-orange-500"></i>
                                    Add Items
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                    <div class="md:col-span-6">
                                        <label class="block text-sm text-gray-500 dark:text-gray-400 mb-1">Select Item</label>
                                        <select x-model="currentItemId" @change="updateMaxStock"
                                                class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none text-sm">
                                            <option value="">-- Choose Item --</option>
                                            <template x-for="item in availableItems" :key="item.id">
                                                <option :value="item.id" x-text="item.nama_barang + ' (Stock: ' + item.stok_tersedia + ')'"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm text-gray-500 dark:text-gray-400 mb-1">Qty</label>
                                        <input type="number" x-model.number="currentQty" min="1" :max="currentMaxStock"
                                               class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none text-sm">
                                    </div>
                                    <div class="md:col-span-4">
                                         <label class="block text-sm text-gray-500 dark:text-gray-400 mb-1">Notes (Optional)</label>
                                         <input type="text" x-model="currentNote" placeholder="Specific reqs..."
                                               class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none text-sm">
                                    </div>
                                    <div class="md:col-span-12 mt-2">
                                        <button @click="addToCart" :disabled="!isValidItem"
                                                class="w-full md:w-auto px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-medium text-sm hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2">
                                            <i data-lucide="shopping-cart" class="w-4 h-4"></i> Add to List
                                        </button>
                                        <p x-show="currentMaxStock > 0" class="text-xs text-gray-500 mt-2">Max allowed: <span x-text="currentMaxStock"></span></p>
                                    </div>
                                </div>
                            </div>

                            
                            <div x-show="cart.length > 0" x-cloak>
                                <h4 class="font-medium mb-4 flex items-center gap-2">
                                    <i data-lucide="list" class="w-4 h-4 text-orange-500"></i>
                                    Items to Request
                                </h4>
                                <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-500 uppercase">
                                                <th class="p-3">Item</th>
                                                <th class="p-3">Qty</th>
                                                <th class="p-3">Notes</th>
                                                <th class="p-3 text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                            <template x-for="(item, index) in cart" :key="index">
                                                <tr class="text-sm bg-white dark:bg-gray-800">
                                                    <td class="p-3" x-text="item.name"></td>
                                                    <td class="p-3" x-text="item.jumlah"></td>
                                                    <td class="p-3 text-gray-500" x-text="item.catatan || '-'"></td>
                                                    <td class="p-3 text-right">
                                                        <button @click="removeFromCart(index)" class="text-red-500 hover:text-red-700 transition-colors">
                                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            
                            <form method="POST" @submit.prevent="submitForm">
                                <input type="hidden" name="items_json" x-model="itemsJson">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Required</label>
                                        <input type="date" name="tanggal_pinjam" required min="<?= date('Y-m-d') ?>"
                                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all">
                                    </div>
                                    <div>
                                         <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Global Notes (Optional)</label>
                                         <input type="text" name="keterangan" placeholder="Overall purpose..."
                                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all">
                                    </div>
                                </div>


                                <div class="flex items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <button type="submit" :disabled="cart.length === 0"
                                            class="px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white font-medium rounded-xl hover:shadow-lg hover:shadow-orange-500/30 transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:transform-none">
                                        Submit Request
                                    </button>
                                    <a href="browse.php" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>


    <script>lucide.createIcons();</script>

    <?php include '../includes/chatbot-widget.php'; ?>
    <script src="../assets/js/chatbot.js"></script>

    <script>
        function loanRequestApp(items, initialItemId) {
            return {
                availableItems: items,
                cart: [],
                currentItemId: initialItemId || '',
                currentQty: 1,
                currentNote: '',
                currentMaxStock: 0,
                
                get itemsJson() {
                    
                    return JSON.stringify(this.cart.map(i => ({
                         inventaris_id: i.id,
                         jumlah: i.jumlah,
                         catatan: i.catatan
                    })));
                },
                
                get isValidItem() {
                    return this.currentItemId && this.currentQty > 0 && this.currentQty <= this.currentMaxStock;
                },
                
                init() {
                    this.updateMaxStock();
                },

                updateMaxStock() {
                    const item = this.availableItems.find(i => i.id == this.currentItemId);
                    this.currentMaxStock = item ? item.stok_tersedia : 0;
                    if(this.currentQty > this.currentMaxStock) this.currentQty = this.currentMaxStock;
                },

                addToCart() {
                    const item = this.availableItems.find(i => i.id == this.currentItemId);
                    if (!item) return;

                    
                    const existing = this.cart.find(c => c.id == this.currentItemId);
                    if (existing) {
                        if (existing.jumlah + this.currentQty <= this.currentMaxStock) {
                            existing.jumlah += this.currentQty;
                        } else {
                            alert('Cannot add more of this item. Stock limit reached.');
                        }
                    } else {
                        this.cart.push({
                            id: item.id,
                            name: item.nama_barang,
                            jumlah: this.currentQty,
                            catatan: this.currentNote
                        });
                    }

                    
                    this.currentItemId = '';
                    this.currentQty = 1;
                    this.currentNote = '';
                    this.currentMaxStock = 0;
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },
                
                submitForm(e) {
                    if (this.cart.length === 0) {
                        alert('Please add items to your request.');
                        return;
                    }
                    e.target.submit();
                }
            }
        }
    </script>
</body>
</html>
