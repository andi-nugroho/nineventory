<?php
namespace Nineventory;

class Auth
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    public function register($username, $email, $password, $role = 'user')
    {
        try {

            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email sudah terdaftar'];
            }


            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);

            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Username sudah digunakan'];
            }


            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);


            $stmt = $this->pdo->prepare(
                "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$username, $email, $hashedPassword, $role]);

            return ['success' => true, 'message' => 'Registrasi berhasil'];

        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }


    public function login($email, $password)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'Email atau password salah'];
            }


            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Email atau password salah'];
            }


            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;

            return ['success' => true, 'message' => 'Login berhasil', 'user' => $user];

        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }


    public function logout()
    {
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => 'Logout berhasil'];
    }


    public function isLoggedIn()
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }


    public function isAdmin()
    {
        return $this->isLoggedIn() && $_SESSION['role'] === 'admin';
    }


    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role']
        ];
    }


    public function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            header('Location: ' . APP_URL . '/login.php');
            exit;
        }
    }

    
    public function requireAdmin()
    {
        $this->requireLogin();

        if (!$this->isAdmin()) {
            header('Location: ' . APP_URL . '/dashboard.php');
            exit;
        }
    }
}
