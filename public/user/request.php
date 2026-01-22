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

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inventaris_id = intval($_POST['inventaris_id'] ?? 0);
    $jumlah = intval($_POST['jumlah'] ?? 0);
    $tanggal_pinjam = $_POST['tanggal_pinjam'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');
    
    if ($inventaris_id && $jumlah && $tanggal_pinjam) {
        $result = $loan->create($currentUser['id'], $inventaris_id, $jumlah, $tanggal_pinjam, $keterangan);
        
        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['message'];
        }
    } else {
        $error = 'Semua field harus diisi';
    }
}

// Get available items
$availableItems = $inventory->getAvailable();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Peminjaman - NINEVENTORY</title>
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
                <a href="request.php" class="menu-item active">
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
                    <h1>Ajukan Peminjaman</h1>
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
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Form Pengajuan Peminjaman</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label class="form-label">Pilih Barang</label>
                                <select name="inventaris_id" class="form-control" required id="itemSelect">
                                    <option value="">-- Pilih Barang --</option>
                                    <?php foreach ($availableItems as $item): ?>
                                        <option value="<?= $item['id'] ?>" 
                                                data-stock="<?= $item['stok_tersedia'] ?>"
                                                data-name="<?= htmlspecialchars($item['nama_barang']) ?>">
                                            <?= htmlspecialchars($item['nama_barang']) ?> 
                                            (Tersedia: <?= $item['stok_tersedia'] ?> unit)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="jumlah" class="form-control" 
                                       min="1" required id="quantityInput">
                                <small class="text-muted" id="stockInfo"></small>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Tanggal Pinjam</label>
                                <input type="date" name="tanggal_pinjam" class="form-control" 
                                       required min="<?= date('Y-m-d') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Keterangan / Alasan</label>
                                <textarea name="keterangan" class="form-control" rows="4" 
                                          placeholder="Jelaskan keperluan peminjaman..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
                            <a href="browse.php" class="btn btn-outline">Lihat Barang Tersedia</a>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <?php include '../includes/chatbot-widget.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/chatbot.js"></script>
    <script>
        // Update max quantity based on selected item
        document.getElementById('itemSelect').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const stock = selected.getAttribute('data-stock');
            const quantityInput = document.getElementById('quantityInput');
            const stockInfo = document.getElementById('stockInfo');
            
            if (stock) {
                quantityInput.max = stock;
                stockInfo.textContent = `Maksimal: ${stock} unit`;
            } else {
                quantityInput.max = '';
                stockInfo.textContent = '';
            }
        });
    </script>
</body>
</html>
