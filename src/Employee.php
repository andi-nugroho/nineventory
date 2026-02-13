<?php
namespace Nineventory;

class Employee
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($nama_karyawan, $jabatan, $departemen, $email, $telepon, $user_id = null)
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO employees (nama_karyawan, jabatan, departemen, email, telepon, user_id) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$nama_karyawan, $jabatan, $departemen, $email, $telepon, $user_id]);
            return ['success' => true, 'message' => 'Karyawan berhasil ditambahkan.'];
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 1062) { 
                return ['success' => false, 'message' => 'Gagal: Alamat email atau akun pengguna sudah digunakan oleh karyawan lain.'];
            }
            return ['success' => false, 'message' => 'Gagal menambahkan karyawan: ' . $e->getMessage()];
        }
    }

    public function getAll()
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT e.*, u.username 
                 FROM employees e
                 LEFT JOIN users u ON e.user_id = u.id
                 ORDER BY e.nama_karyawan ASC"
            );
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getAvailableUsers()
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT u.id, u.username
                 FROM users u
                 WHERE u.id NOT IN (SELECT user_id FROM employees WHERE user_id IS NOT NULL)"
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
                "SELECT e.*, u.username
                 FROM employees e
                 LEFT JOIN users u ON e.user_id = u.id
                 WHERE e.id = ?"
            );
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function update($id, $nama_karyawan, $jabatan, $departemen, $email, $telepon, $user_id = null)
    {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE employees SET nama_karyawan = ?, jabatan = ?, departemen = ?, email = ?, telepon = ?, user_id = ? WHERE id = ?"
            );
            $stmt->execute([$nama_karyawan, $jabatan, $departemen, $email, $telepon, $user_id, $id]);
            return ['success' => true, 'message' => 'Data karyawan berhasil diperbarui.'];
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 1062) { 
                return ['success' => false, 'message' => 'Gagal: Alamat email atau akun pengguna sudah digunakan oleh karyawan lain.'];
            }
            return ['success' => false, 'message' => 'Gagal memperbarui data karyawan: ' . $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM employees WHERE id = ?");
            $stmt->execute([$id]);
            return ['success' => true, 'message' => 'Karyawan berhasil dihapus.'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Gagal menghapus karyawan: ' . $e->getMessage()];
        }
    }
}
