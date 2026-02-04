<?php
namespace Nineventory;

class Loan
{
    private $pdo;
    private $inventory;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->inventory = new Inventory($pdo);
    }

    /**
     * Create a new loan request (Master-Detail)
     * @param int $user_id
     * @param array $items Array of ['inventaris_id' => int, 'jumlah' => int, 'catatan' => string]
     * @param string $tanggal_pinjam
     * @param string|null $keterangan
     */
    public function create($user_id, $employee_id, $items, $tanggal_pinjam, $keterangan = null)
    {
        try {
            $this->pdo->beginTransaction();

            
            $kode_peminjaman = 'LOAN-' . date('Ymd') . '-' . rand(100, 999);

            
            $stmt = $this->pdo->prepare(
                "INSERT INTO peminjaman (kode_peminjaman, user_id, employee_id, tanggal_pinjam, keterangan, status)
                 VALUES (?, ?, ?, ?, ?, 'pending')"
            );
            $stmt->execute([$kode_peminjaman, $user_id, $employee_id, $tanggal_pinjam, $keterangan]);
            $peminjaman_id = $this->pdo->lastInsertId();

            
            $stmtDetail = $this->pdo->prepare(
                "INSERT INTO peminjaman_detail (peminjaman_id, inventaris_id, jumlah, catatan)
                 VALUES (?, ?, ?, ?)"
            );

            foreach ($items as $item) {
                // Defensive check to ensure the required key exists
                if (!isset($item['inventaris_id'])) {
                    throw new \Exception("Data item tidak lengkap: 'inventaris_id' tidak ditemukan.");
                }

                $inv = $this->inventory->getById($item['inventaris_id']);
                if (!$inv) {
                     throw new \Exception("Barang dengan ID " . $item['inventaris_id'] . " tidak ditemukan.");
                }
                if ($inv['stok_tersedia'] < $item['jumlah']) {
                    throw new \Exception("Stok tidak mencukupi untuk barang: " . $inv['nama_barang']);
                }

                $stmtDetail->execute([
                    $peminjaman_id,
                    $item['inventaris_id'],
                    $item['jumlah'],
                    $item['catatan'] ?? null
                ]);
            }

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Pengajuan peminjaman berhasil dibuat', 'id' => $peminjaman_id, 'kode' => $kode_peminjaman];

        } catch (\Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'message' => 'Gagal membuat pengajuan: ' . $e->getMessage()];
        }
    }

    public function getByUserId($user_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT p.*, e.nama_karyawan
                 FROM peminjaman p
                 LEFT JOIN employees e ON p.employee_id = e.id
                 WHERE p.user_id = ?
                 ORDER BY p.created_at DESC"
            );
            $stmt->execute([$user_id]);
            $loans = $stmt->fetchAll();

            $stmtDetails = $this->pdo->prepare(
                "SELECT pd.*, i.nama_barang, i.kategori
                 FROM peminjaman_detail pd
                 JOIN inventaris i ON pd.inventaris_id = i.id
                 WHERE pd.peminjaman_id = ?"
            );

            foreach ($loans as &$loan) {
                $stmtDetails->execute([$loan['id']]);
                $loan['details'] = $stmtDetails->fetchAll();
            }

            return $loans;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getPending()
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT p.*, u.username, u.email, e.nama_karyawan
                 FROM peminjaman p
                 JOIN users u ON p.user_id = u.id
                 LEFT JOIN employees e ON p.employee_id = e.id
                 WHERE p.status = 'pending'
                 ORDER BY p.created_at ASC"
            );
            $loans = $stmt->fetchAll();

            $stmtDetails = $this->pdo->prepare(
                "SELECT pd.*, i.nama_barang, i.kategori
                 FROM peminjaman_detail pd
                 JOIN inventaris i ON pd.inventaris_id = i.id
                 WHERE pd.peminjaman_id = ?"
            );

            foreach ($loans as &$loan) {
                $stmtDetails->execute([$loan['id']]);
                $loan['details'] = $stmtDetails->fetchAll();
            }

            return $loans;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getAll()
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT p.*, u.username, u.email, e.nama_karyawan
                 FROM peminjaman p
                 JOIN users u ON p.user_id = u.id
                 LEFT JOIN employees e ON p.employee_id = e.id
                 ORDER BY p.created_at DESC"
            );
            $loans = $stmt->fetchAll();

            $stmtDetails = $this->pdo->prepare(
                "SELECT pd.*, i.nama_barang, i.kategori
                 FROM peminjaman_detail pd
                 JOIN inventaris i ON pd.inventaris_id = i.id
                 WHERE pd.peminjaman_id = ?"
            );

            foreach ($loans as &$loan) {
                $stmtDetails->execute([$loan['id']]);
                $loan['details'] = $stmtDetails->fetchAll();
            }

            return $loans;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById($id)
    {
        try {
            
            $stmt = $this->pdo->prepare(
                "SELECT p.*, u.username, u.email, e.nama_karyawan
                 FROM peminjaman p
                 JOIN users u ON p.user_id = u.id
                 LEFT JOIN employees e ON p.employee_id = e.id
                 WHERE p.id = ?"
            );
            $stmt->execute([$id]);
            $header = $stmt->fetch();

            if (!$header) return null;

            
            $stmtDetails = $this->pdo->prepare(
                "SELECT pd.*, i.nama_barang, i.kategori, i.stok_tersedia, i.lokasi
                 FROM peminjaman_detail pd
                 JOIN inventaris i ON pd.inventaris_id = i.id
                 WHERE pd.peminjaman_id = ?"
            );
            $stmtDetails->execute([$id]);
            $details = $stmtDetails->fetchAll();

            $header['details'] = $details;
            return $header;

        } catch (\PDOException $e) {
            return null;
        }
    }

    public function approve($id, $tanggal_kembali = null)
    {
        try {
            $this->pdo->beginTransaction();

            $loan = $this->getById($id);

            if (!$loan) {
                return ['success' => false, 'message' => 'Peminjaman tidak ditemukan'];
            }

            if ($loan['status'] !== 'pending') {
                return ['success' => false, 'message' => 'Peminjaman sudah diproses'];
            }

            
            foreach ($loan['details'] as $detail) {
                if ($detail['stok_tersedia'] < $detail['jumlah']) {
                    $this->pdo->rollBack();
                    return ['success' => false, 'message' => 'Stok tidak mencukupi untuk barang: ' . $detail['nama_barang']];
                }
            }

            
            if ($tanggal_kembali) {
                $tanggal_pinjam = strtotime($loan['tanggal_pinjam']);
                $tanggal_kembali_ts = strtotime($tanggal_kembali);

                if ($tanggal_kembali_ts <= $tanggal_pinjam) {
                    $this->pdo->rollBack();
                    return ['success' => false, 'message' => 'Tanggal pengembalian harus setelah tanggal pinjam'];
                }
            }

            
            $stmt = $this->pdo->prepare(
                "UPDATE peminjaman SET status = 'approved', tanggal_kembali_rencana = ? WHERE id = ?"
            );
            $stmt->execute([$tanggal_kembali, $id]);

            
            $stmtStock = $this->pdo->prepare("UPDATE inventaris SET stok_tersedia = stok_tersedia - ? WHERE id = ?");
            foreach ($loan['details'] as $detail) {
                $stmtStock->execute([$detail['jumlah'], $detail['inventaris_id']]);
            }

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Peminjaman berhasil disetujui'];

        } catch (\PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'message' => 'Gagal menyetujui peminjaman: ' . $e->getMessage()];
        }
    }

    public function reject($id, $reason = null)
    {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE peminjaman SET status = 'rejected', alasan_reject = ? WHERE id = ? AND status = 'pending'"
            );
            $stmt->execute([$reason, $id]);

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Peminjaman tidak ditemukan atau sudah diproses'];
            }

            return ['success' => true, 'message' => 'Peminjaman berhasil ditolak'];

        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Gagal menolak peminjaman: ' . $e->getMessage()];
        }
    }

    public function markReturned($id)
    {
        try {
            $this->pdo->beginTransaction();

            $loan = $this->getById($id); // This now returns header + details

            if (!$loan) {
                return ['success' => false, 'message' => 'Peminjaman tidak ditemukan'];
            }

            if ($loan['status'] !== 'approved') {
                return ['success' => false, 'message' => 'Hanya peminjaman yang disetujui yang bisa dikembalikan'];
            }

            
            $stmt = $this->pdo->prepare(
                "UPDATE peminjaman SET status = 'returned', tanggal_kembali = CURDATE() WHERE id = ?"
            );
            $stmt->execute([$id]);

            
            $stmtStock = $this->pdo->prepare("UPDATE inventaris SET stok_tersedia = stok_tersedia + ? WHERE id = ?");
            foreach ($loan['details'] as $detail) {
                $stmtStock->execute([$detail['jumlah'], $detail['inventaris_id']]);
            }

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Barang berhasil dikembalikan'];

        } catch (\PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'message' => 'Gagal mengembalikan barang: ' . $e->getMessage()];
        }
    }

    public function getStats()
    {
        try {
             $stats = [
                'pending' => 0,
                'approved' => 0,
                'returned' => 0,
                'rejected' => 0
            ];
            
            $stmt = $this->pdo->query("SELECT status, COUNT(*) as total FROM peminjaman GROUP BY status");
            $rows = $stmt->fetchAll();
            
            foreach($rows as $row) {
                $stats[$row['status']] = $row['total'];
            }
            return $stats;

        } catch (\PDOException $e) {
             return [
                'pending' => 0,
                'approved' => 0,
                'returned' => 0,
                'rejected' => 0
            ];
        }
    }

    public function countActive()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'approved'");
        return $stmt->fetchColumn();
    }

    public function countPending()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'pending'");
        return $stmt->fetchColumn();
    }
    
    public function getRecent($limit = 5)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT p.*, u.username, e.nama_karyawan
                 FROM peminjaman p
                 JOIN users u ON p.user_id = u.id
                 LEFT JOIN employees e ON p.employee_id = e.id
                 ORDER BY p.created_at DESC
                 LIMIT ?"
            );
            $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
            $stmt->execute();
            $loans = $stmt->fetchAll();

            $stmtDetails = $this->pdo->prepare(
                "SELECT pd.*, i.nama_barang, i.kategori
                 FROM peminjaman_detail pd
                 JOIN inventaris i ON pd.inventaris_id = i.id
                 WHERE pd.peminjaman_id = ?"
            );

            foreach ($loans as &$loan) {
                $stmtDetails->execute([$loan['id']]);
                $details = $stmtDetails->fetchAll();
                $loan['details'] = $details;
                
                // Create a summary of item names for the dashboard
                $itemNames = array_column($details, 'nama_barang');
                $loan['items_summary'] = implode(', ', $itemNames);
            }

            return $loans;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getMostBorrowedItems($limit = 5)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT i.nama_barang, SUM(pd.jumlah) as total_borrowed
                 FROM peminjaman_detail pd
                 JOIN inventaris i ON pd.inventaris_id = i.id
                 JOIN peminjaman p ON pd.peminjaman_id = p.id
                 WHERE p.status IN ('approved', 'returned')
                 GROUP BY i.id, i.nama_barang
                 ORDER BY total_borrowed DESC
                 LIMIT ?"
            );
            $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getOverdueLoans()
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT p.*, u.username, e.nama_karyawan
                 FROM peminjaman p
                 JOIN users u ON p.user_id = u.id
                 LEFT JOIN employees e ON p.employee_id = e.id
                 WHERE p.status = 'approved' AND p.tanggal_kembali_rencana < CURDATE()"
            );
            $loans = $stmt->fetchAll();
            $stmtDetails = $this->pdo->prepare(
                "SELECT pd.jumlah, i.nama_barang 
                 FROM peminjaman_detail pd
                 JOIN inventaris i ON pd.inventaris_id = i.id
                 WHERE pd.peminjaman_id = ?"
            );
            foreach ($loans as &$loan) {
                $stmtDetails->execute([$loan['id']]);
                $loan['details'] = $stmtDetails->fetchAll();
            }
            return $loans;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getTopBorrowers($limit = 5)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT u.username, COUNT(p.id) as total_loans
                 FROM peminjaman p
                 JOIN users u ON p.user_id = u.id
                 GROUP BY u.id, u.username
                 ORDER BY total_loans DESC
                 LIMIT ?"
            );
            $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getCategoryBreakdown()
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT i.kategori, COUNT(p.id) as total_loans
                 FROM peminjaman p
                 JOIN peminjaman_detail pd ON p.id = pd.peminjaman_id
                 JOIN inventaris i ON pd.inventaris_id = i.id
                 GROUP BY i.kategori
                 ORDER BY total_loans DESC"
            );
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getLoanStatsForUser($user_id)
    {
        $stats = [
            'approved' => 0,
            'pending' => 0,
            'overdue' => 0
        ];

        try {
            // Get approved count
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM peminjaman WHERE user_id = ? AND status = 'approved'");
            $stmt->execute([$user_id]);
            $stats['approved'] = $stmt->fetchColumn();

            // Get pending count
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM peminjaman WHERE user_id = ? AND status = 'pending'");
            $stmt->execute([$user_id]);
            $stats['pending'] = $stmt->fetchColumn();

            // Get overdue count
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM peminjaman WHERE user_id = ? AND status = 'approved' AND tanggal_kembali_rencana < CURDATE()");
            $stmt->execute([$user_id]);
            $stats['overdue'] = $stmt->fetchColumn();

            return $stats;
        } catch (\PDOException $e) {
            return $stats;
        }
    }

    public function getActiveLoansByUserId($user_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT p.*, e.nama_karyawan
                 FROM peminjaman p
                 LEFT JOIN employees e ON p.employee_id = e.id
                 WHERE p.user_id = ? AND p.status = 'approved'
                 ORDER BY p.tanggal_kembali_rencana ASC"
            );
            $stmt->execute([$user_id]);
            $loans = $stmt->fetchAll();

            $stmtDetails = $this->pdo->prepare(
                "SELECT pd.jumlah, i.id as inventaris_id, i.nama_barang, i.image
                 FROM peminjaman_detail pd
                 JOIN inventaris i ON pd.inventaris_id = i.id
                 WHERE pd.peminjaman_id = ?"
            );

            foreach ($loans as &$loan) {
                $stmtDetails->execute([$loan['id']]);
                $loan['details'] = $stmtDetails->fetchAll();
            }

            return $loans;
        } catch (\PDOException $e) {
            return [];
        }
    }
}

