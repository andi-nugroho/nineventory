<?php

$pathPrefix = $pathPrefix ?? './';
?>

<div x-data="{
        open: false,
        mobileOpen: false,
        toggle() { this.open = !this.open }
     }"
     class="flex flex-col md:flex-row h-full z-50">


    <div class="md:hidden h-16 bg-white dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-700 w-full flex items-center justify-between px-4 fixed top-0 z-50">
        <div class="flex items-center gap-2">
            <img src="<?= $pathPrefix ?>../logo.svg" alt="Logo" class="w-8 h-8 rounded-lg flex-shrink-0">
            <span class="font-bold text-lg text-neutral-700 dark:text-neutral-200">
                NINEVENTORY
            </span>
        </div>
        <button @click="mobileOpen = !mobileOpen" class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
    </div>


    <div x-show="mobileOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/50 md:hidden"
         @click="mobileOpen = false"></div>

    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-neutral-900 border-r border-neutral-200 dark:border-neutral-700 transform transition-transform duration-300 ease-in-out md:hidden"
           :class="mobileOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="flex flex-col h-full p-4 relative">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <img src="<?= $pathPrefix ?>../logo.svg" alt="Logo" class="w-8 h-8 rounded-lg">
                    <span class="font-bold text-lg text-neutral-700 dark:text-neutral-200">NINEVENTORY</span>
                </div>
                <button @click="mobileOpen = false" class="text-neutral-500">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <nav class="flex-1 flex flex-col gap-1">

                <a href="<?= $pathPrefix ?>dashboard.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors <?= $activePage === 'dashboard' ? 'bg-neutral-100 dark:bg-neutral-800' : '' ?>">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 text-neutral-700 dark:text-neutral-200"></i>
                    <span class="text-neutral-700 dark:text-neutral-200 text-sm font-medium">Dashboard</span>
                </a>


                <a href="<?= $pathPrefix ?>user/browse.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors <?= $activePage === 'browse' ? 'bg-neutral-100 dark:bg-neutral-800' : '' ?>">
                    <i data-lucide="search" class="w-5 h-5 text-neutral-700 dark:text-neutral-200"></i>
                    <span class="text-neutral-700 dark:text-neutral-200 text-sm font-medium">Browse Inventory</span>
                </a>
                <?php if (empty($isAdmin)): ?>
                <a href="<?= $pathPrefix ?>user/request.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors <?= $activePage === 'request' ? 'bg-neutral-100 dark:bg-neutral-800' : '' ?>">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-neutral-700 dark:text-neutral-200"></i>
                    <span class="text-neutral-700 dark:text-neutral-200 text-sm font-medium">New Request</span>
                </a>
                <a href="<?= $pathPrefix ?>user/history.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors <?= $activePage === 'history' ? 'bg-neutral-100 dark:bg-neutral-800' : '' ?>">
                    <i data-lucide="history" class="w-5 h-5 text-neutral-700 dark:text-neutral-200"></i>
                    <span class="text-neutral-700 dark:text-neutral-200 text-sm font-medium">History</span>
                </a>
                <?php endif; ?>


                <?php if (isset($isAdmin) && $isAdmin): ?>
                <div class="h-px bg-neutral-200 dark:bg-neutral-700 my-1 mx-2"></div>

                <a href="<?= $pathPrefix ?>admin/inventory.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors <?= $activePage === 'inventory' ? 'bg-neutral-100 dark:bg-neutral-800' : '' ?>">
                    <i data-lucide="package" class="w-5 h-5 text-neutral-700 dark:text-neutral-200"></i>
                    <span class="text-neutral-700 dark:text-neutral-200 text-sm font-medium">Inventory</span>
                </a>
                <a href="<?= $pathPrefix ?>admin/employees.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors <?= $activePage === 'employees' ? 'bg-neutral-100 dark:bg-neutral-800' : '' ?>">
                    <i data-lucide="users" class="w-5 h-5 text-neutral-700 dark:text-neutral-200"></i>
                    <span class="text-neutral-700 dark:text-neutral-200 text-sm font-medium">Employees</span>
                </a>
                <a href="<?= $pathPrefix ?>admin/loans.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors <?= $activePage === 'loans' ? 'bg-neutral-100 dark:bg-neutral-800' : '' ?>">
                    <i data-lucide="check-square" class="w-5 h-5 text-neutral-700 dark:text-neutral-200"></i>
                    <span class="text-neutral-700 dark:text-neutral-200 text-sm font-medium">Approvals</span>
                </a>
                <a href="<?= $pathPrefix ?>admin/activity.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors <?= $activePage === 'activity' ? 'bg-neutral-100 dark:bg-neutral-800' : '' ?>">
                    <i data-lucide="activity" class="w-5 h-5 text-neutral-700 dark:text-neutral-200"></i>
                    <span class="text-neutral-700 dark:text-neutral-200 text-sm font-medium">Recent Activity</span>
                </a>
                <a href="<?= $pathPrefix ?>admin/reports.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors <?= $activePage === 'reports' ? 'bg-neutral-100 dark:bg-neutral-800' : '' ?>">
                    <i data-lucide="pie-chart" class="w-5 h-5 text-neutral-700 dark:text-neutral-200"></i>
                    <span class="text-neutral-700 dark:text-neutral-200 text-sm font-medium">Reports</span>
                </a>
                <a href="<?= $pathPrefix ?>admin/backup.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors <?= $activePage === 'backup' ? 'bg-neutral-100 dark:bg-neutral-800' : '' ?>">
                    <i data-lucide="database" class="w-5 h-5 text-neutral-700 dark:text-neutral-200"></i>
                    <span class="text-neutral-700 dark:text-neutral-200 text-sm font-medium">Backup Data</span>
                </a>
                <?php endif; ?>

                <div class="mt-auto border-t border-neutral-200 dark:border-neutral-700 pt-3">
                    <a href="<?= $pathPrefix ?>logout.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 transition-colors">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                        <span class="text-sm font-medium">Logout</span>
                    </a>
                </div>
            </nav>
        </div>
    </aside>


    <aside class="hidden md:flex flex-col bg-neutral-100 dark:bg-neutral-800 border-r border-neutral-200 dark:border-neutral-700 h-full flex-shrink-0 transition-all duration-300 ease-in-out"
           :class="open ? 'w-[280px]' : 'w-[68px]'"
           @mouseenter="open = true"
           @mouseleave="open = false">

        <div class="flex flex-col h-full p-3 overflow-hidden">

            <div class="flex items-center gap-3 h-10 mb-6 flex-shrink-0 relative z-20 px-1">
                <img src="<?= $pathPrefix ?>../logo.svg" alt="Logo" class="w-8 h-8 rounded-lg flex-shrink-0">
                <span x-show="open"
                      x-transition:enter="transition ease-out duration-200"
                      x-transition:enter-start="opacity-0 translate-x-2"
                      x-transition:enter-end="opacity-100 translate-x-0"
                      class="font-semibold text-neutral-800 dark:text-neutral-100 whitespace-pre text-base tracking-tight"
                      style="display: none;">NINEVENTORY</span>
            </div>


            <div class="flex flex-col gap-1 flex-1">
                <a href="<?= $pathPrefix ?>dashboard.php" class="flex items-center justify-start gap-3 group/sidebar p-2 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700/50 transition-colors <?= $activePage === 'dashboard' ? 'bg-neutral-200 dark:bg-neutral-700' : '' ?>">
                    <i data-lucide="layout-dashboard" class="text-neutral-700 dark:text-neutral-200 h-5 w-5 flex-shrink-0"></i>
                    <span x-show="open"
                          class="text-neutral-700 dark:text-neutral-200 text-sm font-medium group-hover/sidebar:translate-x-1 transition duration-150 whitespace-pre inline-block"
                          style="display: none;">Dashboard</span>
                </a>

                <a href="<?= $pathPrefix ?>user/browse.php" class="flex items-center justify-start gap-3 group/sidebar p-2 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700/50 transition-colors <?= $activePage === 'browse' ? 'bg-neutral-200 dark:bg-neutral-700' : '' ?>">
                    <i data-lucide="search" class="text-neutral-700 dark:text-neutral-200 h-5 w-5 flex-shrink-0"></i>
                    <span x-show="open"
                          class="text-neutral-700 dark:text-neutral-200 text-sm font-medium group-hover/sidebar:translate-x-1 transition duration-150 whitespace-pre inline-block"
                          style="display: none;">Browse Inventory</span>
                </a>

                <?php if (empty($isAdmin)): ?>
                <a href="<?= $pathPrefix ?>user/request.php" class="flex items-center justify-start gap-3 group/sidebar p-2 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700/50 transition-colors <?= $activePage === 'request' ? 'bg-neutral-200 dark:bg-neutral-700' : '' ?>">
                    <i data-lucide="plus-circle" class="text-neutral-700 dark:text-neutral-200 h-5 w-5 flex-shrink-0"></i>
                    <span x-show="open"
                          class="text-neutral-700 dark:text-neutral-200 text-sm font-medium group-hover/sidebar:translate-x-1 transition duration-150 whitespace-pre inline-block"
                          style="display: none;">New Request</span>
                </a>

                <a href="<?= $pathPrefix ?>user/history.php" class="flex items-center justify-start gap-3 group/sidebar p-2 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700/50 transition-colors <?= $activePage === 'history' ? 'bg-neutral-200 dark:bg-neutral-700' : '' ?>">
                    <i data-lucide="history" class="text-neutral-700 dark:text-neutral-200 h-5 w-5 flex-shrink-0"></i>
                    <span x-show="open"
                          class="text-neutral-700 dark:text-neutral-200 text-sm font-medium group-hover/sidebar:translate-x-1 transition duration-150 whitespace-pre inline-block"
                          style="display: none;">History</span>
                </a>
                <?php endif; ?>

                <?php if (isset($isAdmin) && $isAdmin): ?>
                    <div class="h-px bg-neutral-200 dark:bg-neutral-700 my-2 mx-1" x-show="open"></div>

                    <a href="<?= $pathPrefix ?>admin/inventory.php" class="flex items-center justify-start gap-3 group/sidebar p-2 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700/50 transition-colors <?= $activePage === 'inventory' ? 'bg-neutral-200 dark:bg-neutral-700' : '' ?>">
                        <i data-lucide="package" class="text-neutral-700 dark:text-neutral-200 h-5 w-5 flex-shrink-0"></i>
                        <span x-show="open"
                              class="text-neutral-700 dark:text-neutral-200 text-sm font-medium group-hover/sidebar:translate-x-1 transition duration-150 whitespace-pre inline-block"
                              style="display: none;">Manage Inventory</span>
                    </a>

                    <a href="<?= $pathPrefix ?>admin/employees.php" class="flex items-center justify-start gap-3 group/sidebar p-2 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700/50 transition-colors <?= $activePage === 'employees' ? 'bg-neutral-200 dark:bg-neutral-700' : '' ?>">
                        <i data-lucide="users" class="text-neutral-700 dark:text-neutral-200 h-5 w-5 flex-shrink-0"></i>
                        <span x-show="open"
                              class="text-neutral-700 dark:text-neutral-200 text-sm font-medium group-hover/sidebar:translate-x-1 transition duration-150 whitespace-pre inline-block"
                              style="display: none;">Manage Employees</span>
                    </a>

                    <a href="<?= $pathPrefix ?>admin/loans.php" class="flex items-center justify-start gap-3 group/sidebar p-2 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700/50 transition-colors <?= $activePage === 'loans' ? 'bg-neutral-200 dark:bg-neutral-700' : '' ?>">
                        <i data-lucide="check-square" class="text-neutral-700 dark:text-neutral-200 h-5 w-5 flex-shrink-0"></i>
                        <span x-show="open"
                              class="text-neutral-700 dark:text-neutral-200 text-sm font-medium group-hover/sidebar:translate-x-1 transition duration-150 whitespace-pre inline-block"
                              style="display: none;">Approvals</span>
                    </a>

                    <a href="<?= $pathPrefix ?>admin/activity.php" class="flex items-center justify-start gap-3 group/sidebar p-2 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700/50 transition-colors <?= $activePage === 'activity' ? 'bg-neutral-200 dark:bg-neutral-700' : '' ?>">
                        <i data-lucide="activity" class="text-neutral-700 dark:text-neutral-200 h-5 w-5 flex-shrink-0"></i>
                        <span x-show="open"
                              class="text-neutral-700 dark:text-neutral-200 text-sm font-medium group-hover/sidebar:translate-x-1 transition duration-150 whitespace-pre inline-block"
                              style="display: none;">Recent Activity</span>
                    </a>
                    <a href="<?= $pathPrefix ?>admin/reports.php" class="flex items-center justify-start gap-3 group/sidebar p-2 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700/50 transition-colors <?= $activePage === 'reports' ? 'bg-neutral-200 dark:bg-neutral-700' : '' ?>">
                        <i data-lucide="pie-chart" class="text-neutral-700 dark:text-neutral-200 h-5 w-5 flex-shrink-0"></i>
                        <span x-show="open"
                              class="text-neutral-700 dark:text-neutral-200 text-sm font-medium group-hover/sidebar:translate-x-1 transition duration-150 whitespace-pre inline-block"
                              style="display: none;">Reports</span>
                    </a>
                    <a href="<?= $pathPrefix ?>admin/backup.php" class="flex items-center justify-start gap-3 group/sidebar p-2 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700/50 transition-colors <?= $activePage === 'backup' ? 'bg-neutral-200 dark:bg-neutral-700' : '' ?>">
                        <i data-lucide="database" class="text-neutral-700 dark:text-neutral-200 h-5 w-5 flex-shrink-0"></i>
                        <span x-show="open"
                              class="text-neutral-700 dark:text-neutral-200 text-sm font-medium group-hover/sidebar:translate-x-1 transition duration-150 whitespace-pre inline-block"
                              style="display: none;">Backup Data</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </aside>
</div>
