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


    public function create($user_id, $inventaris_id, $jumlah, $tanggal_pinjam, $keterangan = null)
    {
        try {

            $item = $this->inventory->getById($inventaris_id);

            if (!$item) {
                return ['success' => false, 'message' => 'Barang tidak ditemukan'];
            }

            if ($item['stok_tersedia'] < $jumlah) {
                return ['success' => false, 'message' => 'Stok tidak mencukupi. Tersedia: ' . $item['stok_tersedia']];
            }

            $stmt = $this->pdo->prepare(
                "INSERT INTO peminjaman (user_id, inventaris_id, jumlah, tanggal_pinjam, keterangan)
                 VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->execute([$user_id, $inventaris_id, $jumlah, $tanggal_pinjam, $keterangan]);

            return ['success' => true, 'message' => 'Pengajuan peminjaman berhasil dibuat', 'id' => $this->pdo->lastInsertId()];

        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Gagal membuat pengajuan: ' . $e->getMessage()];
        }
    }


    public function getByUserId($user_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT p.*, i.nama_barang, i.kategori
                 FROM peminjaman p
                 JOIN inventaris i ON p.inventaris_id = i.id
                 WHERE p.user_id = ?
                 ORDER BY p.created_at DESC"
            );
            $stmt->execute([$user_id]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }


    public function getPending()
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT p.*, u.username, u.email, i.nama_barang, i.kategori, i.stok_tersedia
                 FROM peminjaman p
                 JOIN users u ON p.user_id = u.id
                 JOIN inventaris i ON p.inventaris_id = i.id
                 WHERE p.status = 'pending'
                 ORDER BY p.created_at ASC"
            );
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }


    public function getAll()
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT p.*, u.username, u.email, i.nama_barang, i.kategori
                 FROM peminjaman p
                 JOIN users u ON p.user_id = u.id
                 JOIN inventaris i ON p.inventaris_id = i.id
                 ORDER BY p.created_at DESC"
            );
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }


    public function getById($id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT p.*, u.username, u.email, i.nama_barang, i.kategori, i.stok_tersedia
                 FROM peminjaman p
                 JOIN users u ON p.user_id = u.id
                 JOIN inventaris i ON p.inventaris_id = i.id
                 WHERE p.id = ?"
            );
            $stmt->execute([$id]);
            return $stmt->fetch();
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
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Peminjaman tidak ditemukan'];
            }

            if ($loan['status'] !== 'pending') {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Peminjaman sudah diproses'];
            }


            if ($loan['stok_tersedia'] < $loan['jumlah']) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Stok tidak mencukupi'];
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


            $stmt = $this->pdo->prepare("UPDATE inventaris SET stok_tersedia = stok_tersedia - ? WHERE id = ?");
            $stmt->execute([$loan['jumlah'], $loan['inventaris_id']]);

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


            $loan = $this->getById($id);

            if (!$loan) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Peminjaman tidak ditemukan'];
            }

            if ($loan['status'] !== 'approved') {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Hanya peminjaman yang disetujui yang bisa dikembalikan'];
            }


            $stmt = $this->pdo->prepare(
                "UPDATE peminjaman SET status = 'returned', tanggal_kembali = CURDATE() WHERE id = ?"
            );
            $stmt->execute([$id]);


            $stmt = $this->pdo->prepare("UPDATE inventaris SET stok_tersedia = stok_tersedia + ? WHERE id = ?");
            $stmt->execute([$loan['jumlah'], $loan['inventaris_id']]);

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
            $stats = [];


            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM peminjaman WHERE status = 'pending'");
            $stats['pending'] = $stmt->fetch()['total'];


            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM peminjaman WHERE status = 'approved'");
            $stats['approved'] = $stmt->fetch()['total'];


            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM peminjaman WHERE status = 'returned'");
            $stats['returned'] = $stmt->fetch()['total'];


            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM peminjaman WHERE status = 'rejected'");
            $stats['rejected'] = $stmt->fetch()['total'];

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
                "SELECT p.*, u.username, i.nama_barang
                 FROM peminjaman p
                 JOIN users u ON p.user_id = u.id
                 JOIN inventaris i ON p.inventaris_id = i.id
                 ORDER BY p.created_at DESC
                 LIMIT ?"
            );
            $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }
}
