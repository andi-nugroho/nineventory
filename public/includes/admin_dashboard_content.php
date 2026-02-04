<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-2xl">
                <i data-lucide="box" class="w-6 h-6"></i>
            </div>
        </div>
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1"><?= $totalItems ?></h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Total Items</p>
    </div>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-2xl">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
        </div>
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1"><?= $availableItems ?></h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Available Stock</p>
    </div>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 rounded-2xl">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
        </div>
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1"><?= $activeLoansGlobal ?></h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Active Loans</p>
    </div>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-2xl">
                <i data-lucide="alert-circle" class="w-6 h-6"></i>
            </div>
        </div>
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1"><?= $pendingLoansGlobal ?></h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Pending Requests</p>
    </div>
</div>

<?php if (!empty($lowStockItems)): ?>
<div class="mb-8 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-3xl">
    <h3 class="font-bold text-red-700 dark:text-red-400 mb-2">Low Stock Alerts</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        <?php foreach($lowStockItems as $item): ?>
        <div class="bg-white dark:bg-gray-800 p-3 rounded-2xl border border-red-100 dark:border-red-900/30 flex items-center justify-between">
            <div>
                <div class="font-bold text-gray-900 dark:text-white text-sm"><?= htmlspecialchars($item['nama_barang']) ?></div>
                <div class="text-xs text-gray-500">Stock: <span class="font-bold text-red-600"><?= $item['stok_tersedia'] ?></span> / <?= $item['stok_total'] ?></div>
            </div>
            <a href="admin/inventory.php?action=edit&id=<?= $item['id'] ?>" class="p-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-colors">
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
    <h3 class="font-bold text-gray-900 dark:text-white p-6 border-b border-gray-100 dark:border-gray-700">Recent Activity</h3>
    <?php if (empty($recentLoans)): ?>
        <div class="p-8 text-center text-gray-500">No recent activity.</div>
    <?php else: ?>
        <table class="w-full text-left">
            <thead class="bg-gray-50 dark:bg-gray-750 text-xs uppercase text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-4 font-medium">User</th>
                    <th class="px-6 py-4 font-medium">Item</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($recentLoans as $loan): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                    <td class="px-6 py-4"><?= htmlspecialchars($loan['username']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300"><?= htmlspecialchars($loan['items_summary'] ?? '') ?></td>
                    <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-bold"><?= ucfirst($loan['status']) ?></span></td>
                    <td class="px-6 py-4 text-sm text-gray-500"><?= date('M d', strtotime($loan['tanggal_pinjam'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
