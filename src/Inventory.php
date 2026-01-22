<?php
/**
 * Inventory Management Class
 * Handles CRUD operations for inventory items
 */

namespace Nineventory;

class Inventory
{
    private $pdo;
    
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * Get all inventory items
     */
    public function getAll()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM inventaris ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get inventory item by ID
     */
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
    
    /**
     * Get available inventory items (stok_tersedia > 0)
     */
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
    
    /**
     * Create new inventory item
     */
    public function create($data)
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO inventaris (nama_barang, kategori, stok_total, stok_tersedia, kondisi, lokasi, deskripsi) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            
            $stmt->execute([
                $data['nama_barang'],
                $data['kategori'],
                $data['stok_total'],
                $data['stok_total'], // stok_tersedia = stok_total initially
                $data['kondisi'],
                $data['lokasi'],
                $data['deskripsi'] ?? null
            ]);
            
            return ['success' => true, 'message' => 'Barang berhasil ditambahkan', 'id' => $this->pdo->lastInsertId()];
            
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Gagal menambahkan barang: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update inventory item
     */
    public function update($id, $data)
    {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE inventaris 
                 SET nama_barang = ?, kategori = ?, stok_total = ?, kondisi = ?, lokasi = ?, deskripsi = ?
                 WHERE id = ?"
            );
            
            $stmt->execute([
                $data['nama_barang'],
                $data['kategori'],
                $data['stok_total'],
                $data['kondisi'],
                $data['lokasi'],
                $data['deskripsi'] ?? null,
                $id
            ]);
            
            return ['success' => true, 'message' => 'Barang berhasil diupdate'];
            
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Gagal mengupdate barang: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete inventory item
     */
    public function delete($id)
    {
        try {
            // Check if item has active loans
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
    
    /**
     * Update stock quantity
     */
    public function updateStock($id, $quantity, $operation = 'subtract')
    {
        try {
            $this->pdo->beginTransaction();
            
            // Get current stock
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
            
            // Update stock
            $stmt = $this->pdo->prepare("UPDATE inventaris SET stok_tersedia = ? WHERE id = ?");
            $stmt->execute([$newStock, $id]);
            
            $this->pdo->commit();
            
            return ['success' => true, 'message' => 'Stok berhasil diupdate', 'new_stock' => $newStock];
            
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Gagal mengupdate stok: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get inventory statistics
     */
    public function getStats()
    {
        try {
            $stats = [];
            
            // Total items
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM inventaris");
            $stats['total_items'] = $stmt->fetch()['total'];
            
            // Total stock
            $stmt = $this->pdo->query("SELECT SUM(stok_total) as total FROM inventaris");
            $stats['total_stock'] = $stmt->fetch()['total'] ?? 0;
            
            // Available stock
            $stmt = $this->pdo->query("SELECT SUM(stok_tersedia) as total FROM inventaris");
            $stats['available_stock'] = $stmt->fetch()['total'] ?? 0;
            
            // Borrowed stock
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
    /**
     * Count all items
     */
    public function countAll()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM inventaris");
        return $stmt->fetchColumn();
    }

    /**
     * Count available items (stock > 0)
     */
    public function countAvailable()
    {
        $stmt = $this->pdo->query("SELECT SUM(stok_tersedia) FROM inventaris");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get all unique categories
     */
    public function getCategories()
    {
        try {
            $stmt = $this->pdo->query("SELECT DISTINCT kategori FROM inventaris WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC");
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
