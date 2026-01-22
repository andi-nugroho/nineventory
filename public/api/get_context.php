<?php
/**
 * API endpoint to get inventory context for chatbot
 * Returns inventory data, stats, and user loan history
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

use Nineventory\Inventory;
use Nineventory\Loan;

header('Content-Type: application/json');

try {
    $inventory = new Inventory($pdo);
    $loan = new Loan($pdo);
    
    // Get user ID if logged in
    $userId = $_SESSION['user_id'] ?? null;
    
    // Gather context data
    $context = [
        'inventory' => $inventory->getAll(),
        'stats' => $inventory->getStats(),
        'loan_stats' => $loan->getStats()
    ];
    
    // Add user loans if logged in
    if ($userId) {
        $userLoans = $loan->getByUserId($userId);
        $context['user_loans'] = array_map(function($loanItem) {
            return [
                'nama_barang' => $loanItem['nama_barang'],
                'jumlah' => $loanItem['jumlah'],
                'tanggal_pinjam' => $loanItem['tanggal_pinjam'],
                'status' => $loanItem['status']
            ];
        }, $userLoans);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $context
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load context'
    ]);
}
