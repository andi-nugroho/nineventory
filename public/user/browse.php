<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

use Nineventory\Auth;
use Nineventory\Inventory;

$auth = new Auth($pdo);
$auth->requireLogin();

$inventory = new Inventory($pdo);
$currentUser = $auth->getCurrentUser();

$items = $inventory->getAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Barang - NINEVENTORY</title>
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
                <a href="browse.php" class="menu-item active">
                    <span class="menu-item-icon">🔍</span>
                    <span>Lihat Barang</span>
                </a>
                <a href="request.php" class="menu-item">
                    <span class="menu-item-icon">➕</span>
                    <span>Ajukan Peminjaman</span>
                </a>
                <a href="history.php" class="menu-item">
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
                    <h1>Lihat Barang</h1>
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
                        <h3>Daftar Inventaris</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Stok Tersedia</th>
                                        <th>Kondisi</th>
                                        <th>Lokasi</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                            <td><?= htmlspecialchars($item['kategori']) ?></td>
                                            <td>
                                                <strong><?= $item['stok_tersedia'] ?></strong> / <?= $item['stok_total'] ?>
                                            </td>
                                            <td>
                                                <span class="badge <?= $item['kondisi'] === 'baik' ? 'badge-success' : 'badge-warning' ?>">
                                                    <?= ucfirst($item['kondisi']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($item['lokasi']) ?></td>
                                            <td><?= htmlspecialchars($item['deskripsi'] ?? '-') ?></td>
                                            <td>
                                                <?php if ($item['stok_tersedia'] > 0): ?>
                                                    <a href="request.php" class="btn btn-primary btn-sm">Pinjam</a>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Habis</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
