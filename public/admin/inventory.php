<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

use Nineventory\Auth;
use Nineventory\Inventory;

$auth = new Auth($pdo);
$auth->requireAdmin();

$inventory = new Inventory($pdo);
$currentUser = $auth->getCurrentUser();

$message = '';
$error = '';

// Handle actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? null;
    
    if ($action === 'delete' && $id) {
        $result = $inventory->delete($id);
        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_barang' => trim($_POST['nama_barang'] ?? ''),
        'kategori' => trim($_POST['kategori'] ?? ''),
        'stok_total' => intval($_POST['stok_total'] ?? 0),
        'kondisi' => $_POST['kondisi'] ?? 'baik',
        'lokasi' => trim($_POST['lokasi'] ?? ''),
        'deskripsi' => trim($_POST['deskripsi'] ?? '')
    ];
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $result = $inventory->update($_POST['id'], $data);
    } else {
        // Create
        $result = $inventory->create($data);
    }
    
    if ($result['success']) {
        $message = $result['message'];
    } else {
        $error = $result['message'];
    }
}

// Get all inventory
$items = $inventory->getAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Inventaris - NINEVENTORY</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/chatbot.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
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
                
                <div class="menu-section-title">Admin</div>
                <a href="inventory.php" class="menu-item active">
                    <span class="menu-item-icon">📦</span>
                    <span>Kelola Inventaris</span>
                </a>
                <a href="loans.php" class="menu-item">
                    <span class="menu-item-icon">📋</span>
                    <span>Persetujuan Peminjaman</span>
                </a>
                
                <div class="menu-section-title">Account</div>
                <a href="../logout.php" class="menu-item">
                    <span class="menu-item-icon">🚪</span>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Kelola Inventaris</h1>
                </div>
                <div class="topbar-actions">
                    <div class="user-profile">
                        <div class="user-avatar"><?= strtoupper(substr($currentUser['username'], 0, 1)) ?></div>
                        <div class="user-info">
                            <h4><?= htmlspecialchars($currentUser['username']) ?></h4>
                            <p>Admin</p>
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
                
                <!-- Add Item Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Tambah Barang Baru</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Nama Barang</label>
                                        <input type="text" name="nama_barang" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Kategori</label>
                                        <input type="text" name="kategori" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Stok Total</label>
                                        <input type="number" name="stok_total" class="form-control" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Kondisi</label>
                                        <select name="kondisi" class="form-control" required>
                                            <option value="baik">Baik</option>
                                            <option value="rusak ringan">Rusak Ringan</option>
                                            <option value="rusak berat">Rusak Berat</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Lokasi</label>
                                        <input type="text" name="lokasi" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Tambah Barang</button>
                        </form>
                    </div>
                </div>
                
                <!-- Inventory List -->
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
                                        <th>Stok Total</th>
                                        <th>Stok Tersedia</th>
                                        <th>Kondisi</th>
                                        <th>Lokasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                            <td><?= htmlspecialchars($item['kategori']) ?></td>
                                            <td><?= $item['stok_total'] ?></td>
                                            <td><?= $item['stok_tersedia'] ?></td>
                                            <td>
                                                <span class="badge <?= $item['kondisi'] === 'baik' ? 'badge-success' : 'badge-warning' ?>">
                                                    <?= ucfirst($item['kondisi']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($item['lokasi']) ?></td>
                                            <td>
                                                <a href="?action=delete&id=<?= $item['id'] ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
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
