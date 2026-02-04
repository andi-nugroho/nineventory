<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

use Nineventory\Auth;
use Nineventory\Inventory;
use Nineventory\Loan;
use Nineventory\Employee; // Added this line

$auth = new Auth($pdo);
$auth->requireLogin();

$inventory = new Inventory($pdo);
$loan = new Loan($pdo);
$employee = new Employee($pdo); // Instantiate Employee class
$currentUser = $auth->getCurrentUser();
$isAdmin = $auth->isAdmin();

$message = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $itemsJson = $_POST['items_json'] ?? '[]';
    $tanggal_pinjam = $_POST['tanggal_pinjam'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');
    
    // Determine the employee_id based on the current logged-in user
    $linkedEmployee = null;
    $employee_id = null;
    if ($currentUser) {
        // Assuming there's a method to get employee by user ID
        $stmt = $pdo->prepare("SELECT id FROM employees WHERE user_id = ?");
        $stmt->execute([$currentUser['id']]);
        $linkedEmployee = $stmt->fetch();
        if ($linkedEmployee) {
            $employee_id = $linkedEmployee['id'];
        }
    }

    $items = json_decode($itemsJson, true);

    if (!empty($items) && is_array($items) && $tanggal_pinjam) {
        $result = $loan->create($currentUser['id'], $employee_id, $items, $tanggal_pinjam, $keterangan);

        if ($result['success']) {
            $message = $result['message'];
            
             header("Location: history.php?loan_success=1"); 
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
                  x-data="masterDetailLoanRequest(<?= htmlspecialchars(json_encode($availableItems)) ?>)">
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
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Add items to your cart via the "Add Items" section below or the cart icon in the header.</p>
                        </div>

                        <div class="p-6 md:p-8 space-y-8">
                            
                            <!-- Add Item Form -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-700">
                                <h4 class="font-medium mb-4 flex items-center gap-2">
                                    <i data-lucide="plus-circle" class="w-4 h-4 text-orange-500"></i>
                                    Add Items
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                    <div class="md:col-span-8">
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
                                        <label class="block text-sm text-gray-500 dark:text-gray-400 mb-1">Jumlah</label>
                                        <input type="number" x-model.number="currentJumlah" min="1" :max="currentMaxStock"
                                               class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none text-sm">
                                    </div>
                                    <div class="md:col-span-2 mt-2">
                                        <button @click="addToCart" :disabled="!isValidItem"
                                                class="w-full md:w-auto px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-medium text-sm hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2">
                                            <i data-lucide="shopping-cart" class="w-4 h-4"></i> Add to Cart
                                        </button>
                                        <p x-show="currentMaxStock > 0" class="text-xs text-gray-500 mt-2">Max allowed: <span x-text="currentMaxStock"></span></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Cart Summary Table (Read-Only) -->
                             <div x-show="$store.cart.items.length > 0" x-cloak>
                                <h4 class="font-medium mb-4 flex items-center gap-2">
                                    <i data-lucide="list" class="w-4 h-4 text-orange-500"></i>
                                    Summary of Request
                                </h4>
                                <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-500 uppercase">
                                                <th class="p-3">Item</th>
                                                <th class="p-3">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(item, index) in $store.cart.items" :key="item.inventaris_id">
                                                <tr class="border-b border-gray-50 dark:border-gray-700/50 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                                    <td class="p-3">
                                                        <span class="font-medium text-gray-900 dark:text-white" x-text="item.name"></span>
                                                        <span class="text-xs text-gray-500 block" x-text="item.category"></span>
                                                    </td>
                                                    <td class="p-3 font-medium text-gray-900 dark:text-white" x-text="item.jumlah"></td>
                                                    <td class="p-3">
                                                        <button @click="$store.cart.remove(item.inventaris_id)" class="text-red-500 hover:text-red-700 transition-colors p-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                                                            <!-- Trash Icon -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-gray-50 dark:bg-gray-900/50">
                                                <td class="p-3 font-bold text-gray-900 dark:text-white text-right">Total Items</td>
                                                <td class="p-3 font-bold text-gray-900 dark:text-white" x-text="$store.cart.count"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            
                            <form action="" method="POST" @submit="submitLoan" class="space-y-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <input type="hidden" name="items_json" :value="JSON.stringify($store.cart.items)">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Borrowed Date <span class="text-red-500">*</span></label>
                                        <input type="date" name="tanggal_pinjam" required
                                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Purpose / Notes <span class="text-red-500">*</span></label>
                                        <input type="text" name="keterangan" placeholder="e.g. For project X presentation" required
                                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all">
                                    </div>
                                </div>

                                <div class="flex justify-end pt-4">
                                     <button type="submit" 
                                            :disabled="!$store.cart.hasItems"
                                            class="px-8 py-4 bg-orange-600 text-white rounded-xl font-bold shadow-lg shadow-orange-500/30 hover:bg-orange-700 hover:shadow-orange-600/40 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:-translate-y-1">
                                        Submit Request
                                    </button>
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
        document.addEventListener('alpine:init', () => {
             // Main Loan Request App
            Alpine.data('loanRequestApp', (items, initialSelectedId) => ({
                availableItems: items,
                currentItemId: '',
                currentJumlah: 1,
                currentMaxStock: 0,
                // Note: Cart is now global via $store.cart

                init() {
                    if (initialSelectedId) {
                        this.currentItemId = initialSelectedId;
                        this.updateMaxStock();
                    }
                },

                updateMaxStock() {
                    const item = this.availableItems.find(i => i.id == this.currentItemId);
                    if (item) {
                        this.currentMaxStock = item.stok_tersedia;
                        this.currentJumlah = 1;
                    } else {
                        this.currentMaxStock = 0;
                        this.currentJumlah = 0;
                    }
                },

                get isValidItem() {
                    return this.currentItemId && this.currentJumlah > 0 && this.currentJumlah <= this.currentMaxStock;
                },

                addToCart() {
                    if (!this.isValidItem) return;
                    
                    const item = this.availableItems.find(i => i.id == this.currentItemId);
                    if (item) {
                        // Use global store
                        Alpine.store('cart').add(item, this.currentJumlah, this.currentNote);
                        
                        // Reset form
                        this.currentItemId = '';
                        this.currentJumlah = 1;
                        this.currentMaxStock = 0;
                        this.currentNote = '';
                    }
                },

                submitLoan(e) {
                    if (!Alpine.store('cart').hasItems) {
                        alert('Please add items to your cart first.');
                        e.preventDefault();
                        return;
                    }
                    if (!confirm('Are you sure you want to submit this loan request?')) {
                        e.preventDefault();
                    }
                    // If confirmed, form submits naturally.
                }
            }));
        });
    </script>
</body>
</html>
