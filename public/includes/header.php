<?php

$pathPrefix = $pathPrefix ?? './';
?>
<header class="h-16 flex items-center justify-between px-4 md:px-6 bg-white/50 dark:bg-gray-900/50 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 z-10 pt-16 md:pt-0">
    <div class="flex items-center gap-3">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white"><?= $pageTitle ?? 'Overview' ?></h1>
    </div>

    <div class="flex items-center gap-4">

        <button @click="toggleTheme()" class="p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors relative overflow-hidden group">
            <div class="relative z-10">
                <i data-lucide="sun" class="w-5 h-5 block dark:hidden"></i>
                 <i data-lucide="moon" class="w-5 h-5 hidden dark:block"></i>
            </div>
        </button>

        <!-- Cart Menu -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false" class="relative p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <i data-lucide="shopping-cart" class="w-5 h-5 text-gray-700 dark:text-gray-200"></i>
                <span x-show="$store.cart.count > 0" 
                      x-text="$store.cart.count"
                      class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">
                </span>
            </button>

            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden"
                 style="display: none;">
                
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 dark:text-white">Your Cart</h3>
                    <span class="text-xs text-gray-500" x-text="$store.cart.count + ' items'"></span>
                </div>

                <div class="max-h-64 overflow-y-auto">
                    <template x-for="item in $store.cart.items" :key="item.id">
                        <div class="p-4 border-b border-gray-50 dark:border-gray-700/50 flex gap-3">
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate" x-text="item.name"></p>
                                <p class="text-xs text-gray-500 mb-2" x-text="item.category"></p>
                                <div class="flex items-center gap-2">
                                    <button @click="$store.cart.updateQty(item.id, item.qty - 1)" class="p-1 rounded bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-600 dark:text-gray-300">
                                        <!-- Minus Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    </button>
                                    <span class="text-sm font-medium w-6 text-center" x-text="item.qty"></span>
                                    <button @click="$store.cart.updateQty(item.id, item.qty + 1)" class="p-1 rounded bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-600 dark:text-gray-300">
                                        <!-- Plus Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    </button>
                                </div>
                            </div>
                            <button @click="$store.cart.remove(item.id)" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                                <!-- Trash Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </button>
                        </div>
                    </template>
                    <div x-show="!$store.cart.hasItems" class="p-8 text-center text-gray-500">
                        <p class="text-sm">Your cart is empty.</p>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-900/50" x-show="$store.cart.hasItems">
                    <a href="<?= $pathPrefix ?>user/request.php" class="block w-full py-2 bg-orange-600 text-white text-center rounded-lg font-bold text-sm hover:bg-orange-700 transition shadow-lg shadow-orange-500/20">
                        Proceed to Request
                    </a>
                </div>
            </div>
        </div>

        <div class="h-6 w-px bg-gray-200 dark:bg-gray-700"></div>


        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none group">
                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-white text-xs font-bold shadow-sm ring-2 ring-transparent group-hover:ring-orange-200 dark:group-hover:ring-orange-900/50 transition-all">
                    <?= strtoupper(substr($currentUser['username'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="hidden md:block text-left">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                        <?= htmlspecialchars($currentUser['username'] ?? 'User') ?>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <?= isset($isAdmin) && $isAdmin ? 'Administrator' : 'User' ?>
                    </p>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>

            
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-50 text-left"
                 style="display: none;">

                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 md:hidden">
                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($currentUser['username'] ?? 'User') ?></p>
                    <p class="text-xs text-gray-500"><?= isset($isAdmin) && $isAdmin ? 'Administrator' : 'User' ?></p>
                </div>

                <a href="<?= $pathPrefix ?>logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex items-center gap-2">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    Sign out
                </a>
            </div>
        </div>
    </div>
</header>
<script src="<?= $pathPrefix ?>assets/js/cart.js"></script>
