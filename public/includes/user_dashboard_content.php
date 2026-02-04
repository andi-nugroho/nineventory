<?php
// This file is included in dashboard.php for non-admin users.
// Variables available: $userStats, $activeUserLoans
?>
<?php if ($userStats['overdue'] > 0): ?>
<div class="mb-6 p-4 rounded-2xl bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 border border-red-200 dark:border-red-700 flex items-center gap-3">
    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
    <span class="font-bold">You have <?= $userStats['overdue'] ?> overdue item(s). Please return them as soon as possible.</span>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-2xl">
                <i data-lucide="hand-heart" class="w-6 h-6"></i>
            </div>
        </div>
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1"><?= $userStats['approved'] ?></h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Items Borrowed</p>
    </div>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-2xl">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
        </div>
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1"><?= $userStats['pending'] ?></h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Pending Requests</p>
    </div>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-2xl">
                <i data-lucide="alert-circle" class="w-6 h-6"></i>
            </div>
        </div>
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1"><?= $userStats['overdue'] ?></h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Overdue Items</p>
    </div>
</div>

<div class="space-y-4 md:space-y-0 md:flex md:gap-4 mb-8">
    <a href="user/browse.php" class="w-full md:w-auto flex-1 text-center px-6 py-3 bg-orange-600 text-white rounded-xl text-base font-bold hover:bg-orange-700 transition-colors shadow-lg shadow-orange-500/20">
        Browse Inventory
    </a>
    <a href="user/request.php" class="w-full md:w-auto flex-1 text-center px-6 py-3 bg-gray-900 dark:bg-gray-700 text-white dark:text-gray-200 rounded-xl text-base font-bold hover:bg-black dark:hover:bg-gray-600 transition-colors">
        New Loan Request
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">My Current Loans</h3>
    </div>
    <?php if (empty($activeUserLoans)): ?>
        <div class="p-8 text-center text-gray-500">You have no active loans.</div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Item</th>
                        <th class="px-6 py-4 font-semibold">Due Date</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($activeUserLoans as $loan): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                        <td class="px-6 py-4">
                            <?php foreach($loan['details'] as $detail): ?>
                                <div class="flex items-center gap-4">
                                    <?php if (!empty($detail['image'])): ?>
                                        <img src="assets/product/<?= htmlspecialchars($detail['image']) ?>" alt="<?= htmlspecialchars($detail['nama_barang']) ?>" class="w-12 h-12 object-cover rounded-lg">
                                    <?php else: ?>
                                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center"><i data-lucide="box" class="w-6 h-6 text-gray-400"></i></div>
                                    <?php endif; ?>
                                    <div>
                                        <a href="user/detail.php?id=<?= $detail['inventaris_id'] ?>" class="font-bold text-gray-900 dark:text-white hover:text-orange-500 hover:underline">
                                            <?= htmlspecialchars($detail['nama_barang']) ?>
                                        </a>
                                        <span class="text-gray-500">(x<?= $detail['jumlah'] ?>)</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <?php 
                            $isOverdue = false;
                            if ($loan['tanggal_kembali_rencana']) {
                                try {
                                    $dueDate = new DateTime($loan['tanggal_kembali_rencana']);
                                    $today = new DateTime('today');
                                    if ($dueDate < $today) {
                                        $isOverdue = true;
                                    }
                                } catch (Exception $e) {}
                            }
                            ?>
                            <span class="font-medium <?= $isOverdue ? 'text-red-500' : 'text-gray-600 dark:text-gray-300' ?>">
                                <?= date('M d, Y', strtotime($loan['tanggal_kembali_rencana'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                             <a href="user/history.php?action=mark_returned&id=<?= $loan['id'] ?>" 
                                onclick="return confirm('Are you sure you want to mark this loan as returned?')" 
                                class="px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-lg text-xs font-semibold hover:bg-blue-200 transition">
                                Mark as Returned
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
