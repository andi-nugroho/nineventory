<?php
/**
 * Authentication Class
 * Handles user registration, login, logout, and session management
 */

namespace Nineventory;

class Auth
{
    private $pdo;
    
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * Register new user with bcrypt password hashing
     */
    public function register($username, $email, $password, $role = 'user')
    {
        try {
            // Check if email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email sudah terdaftar'];
            }
            
            // Check if username already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Username sudah digunakan'];
            }
            
            // Hash password with bcrypt
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert new user
            $stmt = $this->pdo->prepare(
                "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$username, $email, $hashedPassword, $role]);
            
            return ['success' => true, 'message' => 'Registrasi berhasil'];
            
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
    
    /**
     * Login user with password verification
     */
    public function login($email, $password)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'Email atau password salah'];
            }
            
            // Verify password
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Email atau password salah'];
            }
            
            // Create session
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
    
    /**
     * Logout user and destroy session
     */
    public function logout()
    {
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => 'Logout berhasil'];
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->isLoggedIn() && $_SESSION['role'] === 'admin';
    }
    
    /**
     * Get current user data
     */
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
    
    /**
     * Require login - redirect if not logged in
     */
    public function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            header('Location: ' . APP_URL . '/login.php');
            exit;
        }
    }
    
    /**
     * Require admin - redirect if not admin
     */
    public function requireAdmin()
    {
        $this->requireLogin();
        
        if (!$this->isAdmin()) {
            header('Location: ' . APP_URL . '/dashboard.php');
            exit;
        }
    }
}
