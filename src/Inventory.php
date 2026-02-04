<?php
namespace Nineventory;

class Inventory
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    public function getAll($search = '', $category = '')
    {
        try {
            $sql = "SELECT * FROM inventaris WHERE 1=1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (nama_barang LIKE ? OR deskripsi LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            if (!empty($category)) {
                $sql .= " AND kategori = ?";
                $params[] = $category;
            }

            $sql .= " ORDER BY created_at DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }


    public function getById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM inventaris WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            return null;
        }
    }


    public function getAvailable()
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT * FROM inventaris WHERE stok_tersedia > 0 ORDER BY nama_barang ASC"
            );
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }


    public function create($data)
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO inventaris (nama_barang, kategori, stok_total, stok_tersedia, kondisi, lokasi, deskripsi, image)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $data['nama_barang'],
                $data['kategori'],
                $data['stok_total'],
                $data['stok_total'], // stok_tersedia = stok_total initially
                $data['kondisi'],
                $data['lokasi'],
                $data['deskripsi'] ?? null,
                $data['image'] ?? null
            ]);

            return ['success' => true, 'message' => 'Barang berhasil ditambahkan', 'id' => $this->pdo->lastInsertId()];

        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Gagal menambahkan barang: ' . $e->getMessage()];
        }
    }


    public function update($id, $data)
    {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE inventaris
                 SET nama_barang = ?, kategori = ?, stok_total = ?, kondisi = ?, lokasi = ?, deskripsi = ?, image = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $data['nama_barang'],
                $data['kategori'],
                $data['stok_total'],
                $data['kondisi'],
                $data['lokasi'],
                $data['deskripsi'] ?? null,
                $data['image'] ?? null,
                $id
            ]);

            return ['success' => true, 'message' => 'Barang berhasil diupdate'];

        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Gagal mengupdate barang: ' . $e->getMessage()];
        }
    }


    public function delete($id)
    {
        try {

            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) as count FROM peminjaman
                 WHERE inventaris_id = ? AND status IN ('pending', 'approved')"
            );
            $stmt->execute([$id]);
            $result = $stmt->fetch();

            if ($result['count'] > 0) {
                return ['success' => false, 'message' => 'Tidak dapat menghapus barang yang sedang dipinjam'];
            }

            $stmt = $this->pdo->prepare("DELETE FROM inventaris WHERE id = ?");
            $stmt->execute([$id]);

            return ['success' => true, 'message' => 'Barang berhasil dihapus'];

        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Gagal menghapus barang: ' . $e->getMessage()];
        }
    }


    public function updateStock($id, $quantity, $operation = 'subtract')
    {
        try {
            $this->pdo->beginTransaction();


            $stmt = $this->pdo->prepare("SELECT stok_tersedia FROM inventaris WHERE id = ? FOR UPDATE");
            $stmt->execute([$id]);
            $item = $stmt->fetch();

            if (!$item) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Barang tidak ditemukan'];
            }

            $newStock = $operation === 'subtract'
                ? $item['stok_tersedia'] - $quantity
                : $item['stok_tersedia'] + $quantity;

            if ($newStock < 0) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Stok tidak mencukupi'];
            }


            $stmt = $this->pdo->prepare("UPDATE inventaris SET stok_tersedia = ? WHERE id = ?");
            $stmt->execute([$newStock, $id]);

            $this->pdo->commit();

            return ['success' => true, 'message' => 'Stok berhasil diupdate', 'new_stock' => $newStock];

        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Gagal mengupdate stok: ' . $e->getMessage()];
        }
    }


    public function getStats()
    {
        try {
            $stats = [];


            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM inventaris");
            $stats['total_items'] = $stmt->fetch()['total'];


            $stmt = $this->pdo->query("SELECT SUM(stok_total) as total FROM inventaris");
            $stats['total_stock'] = $stmt->fetch()['total'] ?? 0;


            $stmt = $this->pdo->query("SELECT SUM(stok_tersedia) as total FROM inventaris");
            $stats['available_stock'] = $stmt->fetch()['total'] ?? 0;


            $stats['borrowed_stock'] = $stats['total_stock'] - $stats['available_stock'];

            return $stats;

        } catch (\PDOException $e) {
            return [
                'total_items' => 0,
                'total_stock' => 0,
                'available_stock' => 0,
                'borrowed_stock' => 0
            ];
        }
    }


    public function countAll()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM inventaris");
        return $stmt->fetchColumn();
    }


    public function countAvailable()
    {
        $stmt = $this->pdo->query("SELECT SUM(stok_tersedia) FROM inventaris");
        return (int) $stmt->fetchColumn();
    }

    
    public function getCategories()
    {
        try {
            $stmt = $this->pdo->query("SELECT DISTINCT kategori FROM inventaris WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC");
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getLowStock($limit = 5)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM inventaris WHERE stok_tersedia <= ? ORDER BY stok_tersedia ASC"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getRelatedItems($category, $excludeId = 0, $limit = 3)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM inventaris 
                 WHERE kategori = ? AND id != ? AND stok_tersedia > 0 
                 ORDER BY RAND() LIMIT ?"
            );
            $stmt->bindValue(1, $category);
            $stmt->bindValue(2, $excludeId);
            $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }
}
