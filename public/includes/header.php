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

