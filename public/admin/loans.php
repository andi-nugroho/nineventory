<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

use Nineventory\Auth;
use Nineventory\Loan;

$auth = new Auth($pdo);
$auth->requireAdmin();

$loan = new Loan($pdo);
$currentUser = $auth->getCurrentUser();

$message = '';
$error = '';

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];
    
    if ($action === 'reject') {
        $result = $loan->reject($id, 'Ditolak oleh admin');
        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['message'];
        }
    } elseif ($action === 'return') {
        $result = $loan->markReturned($id);
        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

// Handle approval with return date
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_loan'])) {
    $loan_id = intval($_POST['loan_id'] ?? 0);
    $tanggal_kembali = $_POST['tanggal_kembali_rencana'] ?? null;
    
    if ($loan_id && $tanggal_kembali) {
        $result = $loan->approve($loan_id, $tanggal_kembali);
        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['message'];
        }
    } else {
        $error = 'Tanggal pengembalian harus diisi';
    }
}

// Get all loans
$allLoans = $loan->getAll();
$pendingLoans = $loan->getPending();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Peminjaman - NINEVENTORY</title>
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
                
                <div class="menu-section-title">Admin</div>
                <a href="inventory.php" class="menu-item">
                    <span class="menu-item-icon">📦</span>
                    <span>Kelola Inventaris</span>
                </a>
                <a href="loans.php" class="menu-item active">
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
        
        <main class="main-content">
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Persetujuan Peminjaman</h1>
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
                
                <!-- Pending Approvals -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Pengajuan Pending (<?= count($pendingLoans) ?>)</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pendingLoans)): ?>
                            <p class="text-muted">Tidak ada pengajuan pending.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Barang</th>
                                            <th>Jumlah</th>
                                            <th>Stok Tersedia</th>
                                            <th>Tanggal Pinjam</th>
                                            <th>Keterangan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingLoans as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['username']) ?></td>
                                                <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                                <td><?= $item['jumlah'] ?></td>
                                                <td><?= $item['stok_tersedia'] ?></td>
                                                <td><?= date('d/m/Y', strtotime($item['tanggal_pinjam'])) ?></td>
                                                <td><?= htmlspecialchars($item['keterangan'] ?? '-') ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-success btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#approveModal"
                                                            data-loan-id="<?= $item['id'] ?>"
                                                            data-item-name="<?= htmlspecialchars($item['nama_barang']) ?>"
                                                            data-user-name="<?= htmlspecialchars($item['username']) ?>"
                                                            data-tanggal-pinjam="<?= $item['tanggal_pinjam'] ?>">
                                                        Setujui
                                                    </button>
                                                    <a href="?action=reject&id=<?= $item['id'] ?>" 
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Tolak peminjaman ini?')">Tolak</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- All Loans -->
                <div class="card">
                    <div class="card-header">
                        <h3>Semua Peminjaman</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Barang</th>
                                        <th>Jumlah</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tgl Kembali Rencana</th>
                                        <th>Tgl Kembali Aktual</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allLoans as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['username']) ?></td>
                                            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                            <td><?= $item['jumlah'] ?></td>
                                            <td><?= date('d/m/Y', strtotime($item['tanggal_pinjam'])) ?></td>
                                            <td>
                                                <?php if ($item['tanggal_kembali_rencana']): ?>
                                                    <span class="badge badge-primary">
                                                        <?= date('d/m/Y', strtotime($item['tanggal_kembali_rencana'])) ?>
                                                    </span>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
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
                                                <?php if ($item['status'] === 'approved'): ?>
                                                    <a href="?action=return&id=<?= $item['id'] ?>" 
                                                       class="btn btn-primary btn-sm"
                                                       onclick="return confirm('Tandai sebagai dikembalikan?')">Kembalikan</a>
                                                <?php else: ?>
                                                    -
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
    
    <!-- Approval Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Setujui Peminjaman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="loan_id" id="modalLoanId">
                        <input type="hidden" name="approve_loan" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>User:</strong></label>
                            <p id="modalUserName" class="mb-0"></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Barang:</strong></label>
                            <p id="modalItemName" class="mb-0"></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Tanggal Pinjam:</strong></label>
                            <p id="modalTanggalPinjam" class="mb-0"></p>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tanggalKembaliRencana" class="form-label">
                                <strong>Tanggal Pengembalian Rencana: <span class="text-danger">*</span></strong>
                            </label>
                            <input type="date" 
                                   class="form-control" 
                                   id="tanggalKembaliRencana" 
                                   name="tanggal_kembali_rencana" 
                                   required>
                            <small class="text-muted">Tentukan kapan barang harus dikembalikan</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Setujui Peminjaman</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include '../includes/chatbot-widget.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/chatbot.js"></script>
    <script>
        // Populate approval modal with loan data
        const approveModal = document.getElementById('approveModal');
        approveModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            const loanId = button.getAttribute('data-loan-id');
            const itemName = button.getAttribute('data-item-name');
            const userName = button.getAttribute('data-user-name');
            const tanggalPinjam = button.getAttribute('data-tanggal-pinjam');
            
            document.getElementById('modalLoanId').value = loanId;
            document.getElementById('modalItemName').textContent = itemName;
            document.getElementById('modalUserName').textContent = userName;
            document.getElementById('modalTanggalPinjam').textContent = new Date(tanggalPinjam).toLocaleDateString('id-ID');
            
            // Set minimum date to tomorrow
            const tomorrow = new Date(tanggalPinjam);
            tomorrow.setDate(tomorrow.getDate() + 1);
            const minDate = tomorrow.toISOString().split('T')[0];
            document.getElementById('tanggalKembaliRencana').setAttribute('min', minDate);
            
            // Set default to 7 days from borrow date
            const defaultReturn = new Date(tanggalPinjam);
            defaultReturn.setDate(defaultReturn.getDate() + 7);
            document.getElementById('tanggalKembaliRencana').value = defaultReturn.toISOString().split('T')[0];
        });
    </script>
</body>
</html>
