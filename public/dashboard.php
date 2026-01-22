<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

use Nineventory\Auth;
use Nineventory\Inventory;
use Nineventory\Loan;

$auth = new Auth($pdo);
$auth->requireLogin();

$inventory = new Inventory($pdo);
$loan = new Loan($pdo);

$currentUser = $auth->getCurrentUser();
$isAdmin = $auth->isAdmin();

// Get statistics
$inventoryStats = $inventory->getStats();
$loanStats = $loan->getStats();

// Get recent data based on role
if ($isAdmin) {
    $recentLoans = $loan->getPending();
    $pageTitle = 'Admin Dashboard';
} else {
    $recentLoans = $loan->getByUserId($currentUser['id']);
    $pageTitle = 'User Dashboard';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - NINEVENTORY</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/chatbot.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <img src="assets/images/logo.svg" alt="NINEVENTORY">
                <h3>NINEVENTORY</h3>
            </div>
            
            <nav class="sidebar-menu">
                <div class="menu-section-title">Main Menu</div>
                <a href="dashboard.php" class="menu-item active">
                    <span class="menu-item-icon">📊</span>
                    <span>Dashboard</span>
                </a>
                
                <?php if ($isAdmin): ?>
                    <div class="menu-section-title">Admin</div>
                    <a href="admin/inventory.php" class="menu-item">
                        <span class="menu-item-icon">📦</span>
                        <span>Kelola Inventaris</span>
                    </a>
                    <a href="admin/loans.php" class="menu-item">
                        <span class="menu-item-icon">📋</span>
                        <span>Persetujuan Peminjaman</span>
                    </a>
                <?php else: ?>
                    <div class="menu-section-title">User</div>
                    <a href="user/browse.php" class="menu-item">
                        <span class="menu-item-icon">🔍</span>
                        <span>Lihat Barang</span>
                    </a>
                    <a href="user/request.php" class="menu-item">
                        <span class="menu-item-icon">➕</span>
                        <span>Ajukan Peminjaman</span>
                    </a>
                    <a href="user/history.php" class="menu-item">
                        <span class="menu-item-icon">📜</span>
                        <span>Riwayat Peminjaman</span>
                    </a>
                <?php endif; ?>
                
                <div class="menu-section-title">Account</div>
                <a href="logout.php" class="menu-item">
                    <span class="menu-item-icon">🚪</span>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-title">
                    <h1><?= $pageTitle ?></h1>
                </div>
                <div class="topbar-actions">
                    <div class="user-profile">
                        <div class="user-avatar"><?= strtoupper(substr($currentUser['username'], 0, 1)) ?></div>
                        <div class="user-info">
                            <h4><?= htmlspecialchars($currentUser['username']) ?></h4>
                            <p><?= ucfirst($currentUser['role']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content Area -->
            <div class="content-area">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon primary">📦</div>
                        </div>
                        <div class="stat-value"><?= $inventoryStats['total_items'] ?></div>
                        <div class="stat-label">Total Jenis Barang</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon success">✅</div>
                        </div>
                        <div class="stat-value"><?= $inventoryStats['available_stock'] ?></div>
                        <div class="stat-label">Stok Tersedia</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon warning">⏳</div>
                        </div>
                        <div class="stat-value"><?= $loanStats['pending'] ?></div>
                        <div class="stat-label">Pending Approval</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon danger">📤</div>
                        </div>
                        <div class="stat-value"><?= $inventoryStats['borrowed_stock'] ?></div>
                        <div class="stat-label">Sedang Dipinjam</div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h3><?= $isAdmin ? 'Pengajuan Peminjaman Pending' : 'Riwayat Peminjaman Terbaru' ?></h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentLoans)): ?>
                            <p class="text-muted">Tidak ada data peminjaman.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <?php if ($isAdmin): ?>
                                                <th>User</th>
                                            <?php endif; ?>
                                            <th>Barang</th>
                                            <th>Jumlah</th>
                                            <th>Tanggal Pinjam</th>
                                            <th>Status</th>
                                            <?php if ($isAdmin): ?>
                                                <th>Aksi</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($recentLoans, 0, 5) as $loan): ?>
                                            <tr>
                                                <?php if ($isAdmin): ?>
                                                    <td><?= htmlspecialchars($loan['username']) ?></td>
                                                <?php endif; ?>
                                                <td><?= htmlspecialchars($loan['nama_barang']) ?></td>
                                                <td><?= $loan['jumlah'] ?></td>
                                                <td><?= date('d/m/Y', strtotime($loan['tanggal_pinjam'])) ?></td>
                                                <td>
                                                    <?php
                                                    $badgeClass = match($loan['status']) {
                                                        'approved' => 'badge-success',
                                                        'pending' => 'badge-warning',
                                                        'rejected' => 'badge-danger',
                                                        'returned' => 'badge-primary',
                                                        default => 'badge-primary'
                                                    };
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <?= ucfirst($loan['status']) ?>
                                                    </span>
                                                </td>
                                                <?php if ($isAdmin && $loan['status'] === 'pending'): ?>
                                                    <td>
                                                        <a href="admin/loans.php?action=approve&id=<?= $loan['id'] ?>" 
                                                           class="btn btn-success btn-sm">Setujui</a>
                                                        <a href="admin/loans.php?action=reject&id=<?= $loan['id'] ?>" 
                                                           class="btn btn-danger btn-sm">Tolak</a>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            <?php if ($isAdmin): ?>
                                <a href="admin/inventory.php?action=add" class="btn btn-primary">
                                    ➕ Tambah Barang
                                </a>
                                <a href="admin/loans.php" class="btn btn-primary">
                                    📋 Lihat Semua Pengajuan
                                </a>
                            <?php else: ?>
                                <a href="user/request.php" class="btn btn-primary">
                                    ➕ Ajukan Peminjaman Baru
                                </a>
                                <a href="user/browse.php" class="btn btn-primary">
                                    🔍 Lihat Barang Tersedia
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Chatbot Widget -->
    <?php include 'includes/chatbot-widget.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/chatbot.js"></script>
</body>
</html>
