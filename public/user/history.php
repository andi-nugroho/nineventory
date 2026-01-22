<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

use Nineventory\Auth;
use Nineventory\Loan;

$auth = new Auth($pdo);
$auth->requireLogin();

$loan = new Loan($pdo);
$currentUser = $auth->getCurrentUser();

$myLoans = $loan->getByUserId($currentUser['id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - NINEVENTORY</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/chatbot.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <img src="../assets/images/logo.svg" alt="NINEVENTORY">
                <h3>NINEVENTORY</h3>
            </div>
            
            <nav class="sidebar-menu">
                <div class="menu-section-title">Main Menu</div>
                <a href="../dashboard.php" class="menu-item">
                    <span class="menu-item-icon">📊</span>
                    <span>Dashboard</span>
                </a>
                
                <div class="menu-section-title">User</div>
                <a href="browse.php" class="menu-item">
                    <span class="menu-item-icon">🔍</span>
                    <span>Lihat Barang</span>
                </a>
                <a href="request.php" class="menu-item">
                    <span class="menu-item-icon">➕</span>
                    <span>Ajukan Peminjaman</span>
                </a>
                <a href="history.php" class="menu-item active">
                    <span class="menu-item-icon">📜</span>
                    <span>Riwayat Peminjaman</span>
                </a>
                
                <div class="menu-section-title">Account</div>
                <a href="../logout.php" class="menu-item">
                    <span class="menu-item-icon">🚪</span>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Riwayat Peminjaman</h1>
                </div>
                <div class="topbar-actions">
                    <div class="user-profile">
                        <div class="user-avatar"><?= strtoupper(substr($currentUser['username'], 0, 1)) ?></div>
                        <div class="user-info">
                            <h4><?= htmlspecialchars($currentUser['username']) ?></h4>
                            <p>User</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="content-area">
                <div class="card">
                    <div class="card-header">
                        <h3>Riwayat Peminjaman Saya</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($myLoans)): ?>
                            <p class="text-muted">Anda belum pernah mengajukan peminjaman.</p>
                            <a href="request.php" class="btn btn-primary">Ajukan Peminjaman Sekarang</a>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Barang</th>
                                            <th>Kategori</th>
                                            <th>Jumlah</th>
                                            <th>Tanggal Pinjam</th>
                                            <th>Tanggal Kembali</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($myLoans as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                                <td><?= htmlspecialchars($item['kategori']) ?></td>
                                                <td><?= $item['jumlah'] ?></td>
                                                <td><?= date('d/m/Y', strtotime($item['tanggal_pinjam'])) ?></td>
                                                <td><?= $item['tanggal_kembali'] ? date('d/m/Y', strtotime($item['tanggal_kembali'])) : '-' ?></td>
                                                <td>
                                                    <?php
                                                    $badgeClass = match($item['status']) {
                                                        'approved' => 'badge-success',
                                                        'pending' => 'badge-warning',
                                                        'rejected' => 'badge-danger',
                                                        'returned' => 'badge-primary',
                                                        default => 'badge-primary'
                                                    };
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <?= ucfirst($item['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($item['status'] === 'rejected' && $item['alasan_reject']): ?>
                                                        <small class="text-danger"><?= htmlspecialchars($item['alasan_reject']) ?></small>
                                                    <?php else: ?>
                                                        <?= htmlspecialchars($item['keterangan'] ?? '-') ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <?php include '../includes/chatbot-widget.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/chatbot.js"></script>
</body>
</html>
