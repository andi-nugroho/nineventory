<?php
namespace Nineventory;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ChatBot
{
    private $apiKey;
    private $client;
    private $inventory;
    private $loan;
    private $aiProvider = 'cohere';

    public function __construct($pdo)
    {

        if (!empty($_ENV['COHERE_API_KEY'])) {
            $this->apiKey = $_ENV['COHERE_API_KEY'];
            $this->aiProvider = 'cohere';
        } 

        $this->client = new Client([
            'timeout' => 30,
            'verify' => false
        ]);
        $this->inventory = new Inventory($pdo);
        $this->loan = new Loan($pdo);
    }

    /**
     * Send message to chatbot and get response
     * Tries Cohere AI first, falls back to rule-based
     */
    public function sendMessage($userMessage, $userId = null)
    {
        try {
            // Get inventory context
            $context = $this->getInventoryContext($userId);

            // Try Cohere AI if available
            if (!empty($this->apiKey) && $this->aiProvider === 'cohere') {
                try {
                    $prompt = $this->buildPrompt($userMessage, $context);
                    $response = $this->callCohereAPI($prompt);

                    return [
                        'success' => true,
                        'message' => $response
                    ];
                } catch (\Exception $e) {
                    // Fall through to rule-based on AI error
                }
            }

            // Fallback to rule-based response
            $response = $this->generateResponse($userMessage, $context);

            return [
                'success' => true,
                'message' => $response
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Maaf, terjadi kesalahan. Silakan coba lagi.'
            ];
        }
    }

    /**
     * Call Cohere API (Chat endpoint)
     */
    private function callCohereAPI($prompt)
    {
        $url = "https://api.cohere.ai/v1/chat";

        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'model' => 'command-a-03-2025',  // Current Cohere model
                'message' => $prompt,
                'temperature' => 0.7,
                'chat_history' => [],
                'prompt_truncation' => 'AUTO'
            ]
        ]);

        $body = json_decode($response->getBody(), true);

        if (isset($body['message'])) {
            throw new \Exception($body['message']);
        }

        if (isset($body['text'])) {
            return trim($body['text']);
        }

        throw new \Exception("Unexpected response format");
    }

    /**
     * Build prompt with inventory context for AI
     */
    private function buildPrompt($userMessage, $context)
    {
        $systemRole = "Anda adalah asisten virtual untuk sistem inventaris kantor NINEVENTORY. " .
                      "Tugas Anda adalah membantu pengguna dengan informasi tentang stok barang, " .
                      "status peminjaman, dan panduan penggunaan sistem. " .
                      "Selalu jawab dalam Bahasa Indonesia dengan ramah, profesional, dan informatif. " .
                      "Gunakan emoji yang sesuai untuk membuat percakapan lebih menarik. " .
                      "Jawab HANYA berdasarkan data yang diberikan di bawah ini.";

        $contextInfo = "\n\nDATA SISTEM SAAT INI:\n";

        if (!empty($context['stats'])) {
            $contextInfo .= "\nSTATISTIK INVENTARIS:\n";
            $contextInfo .= "- Total Barang: {$context['stats']['total_items']} jenis\n";
            $contextInfo .= "- Stok Tersedia: {$context['stats']['available_stock']} unit\n";
            $contextInfo .= "- Sedang Dipinjam: {$context['stats']['borrowed_stock']} unit\n";
        }

        if (!empty($context['inventory'])) {
            $contextInfo .= "\nDAFTAR BARANG:\n";
            foreach ($context['inventory'] as $item) {
                $contextInfo .= "- {$item['nama']} ({$item['kategori']}): {$item['stok_tersedia']}/{$item['stok_total']} unit tersedia, ";
                $contextInfo .= "Kondisi: {$item['kondisi']}, Lokasi: {$item['lokasi']}\n";
            }
        }

        return $systemRole . $contextInfo . "\n\nPERTANYAAN USER: " . $userMessage .
               "\n\nJawab berdasarkan data di atas dengan singkat dan jelas.";
    }

    /**
     * Call Hugging Face Inference API
     */
    private function callHuggingFaceAPI($prompt)
    {
        $url = "https://router.huggingface.co/models/{$this->model}";

        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'inputs' => $prompt,
                'parameters' => [
                    'max_new_tokens' => 400,
                    'temperature' => 0.7,
                    'top_p' => 0.95,
                    'return_full_text' => false
                ]
            ]
        ]);

        $body = json_decode($response->getBody(), true);

        if (isset($body['error'])) {
            throw new \Exception($body['error']);
        }

        if (isset($body[0]['generated_text'])) {
            return trim($body[0]['generated_text']);
        }

        throw new \Exception("Unexpected response format");
    }

    /**
     * Generate response based on user message pattern
     */
    private function generateResponse($message, $context)
    {
        $message = strtolower($message);

        // Pattern: Stok barang tertentu
        if (preg_match('/(berapa|cek|lihat).*(stok|tersedia).*(laptop|proyektor|printer|mouse|keyboard|monitor|kamera|speaker|headset|tablet)/i', $message, $matches)) {
            $itemName = $matches[3];
            return $this->getStockInfo($itemName, $context);
        }

        // Pattern: Daftar semua barang
        if (preg_match('/(barang|item|inventaris).*(apa|ada|tersedia|list|daftar)/i', $message) ||
            preg_match('/(apa|ada).*(barang|item)/i', $message)) {
            return $this->listAllItems($context);
        }

        // Pattern: Total peminjaman
        if (preg_match('/(berapa|total).*(pinjam|dipinjam|sedang dipinjam)/i', $message)) {
            return $this->getLoanInfo($context);
        }

        // Pattern: Lokasi barang
        if (preg_match('/(dimana|lokasi|tempat).*(laptop|proyektor|printer|mouse|keyboard|monitor|kamera|speaker|headset|tablet)/i', $message, $matches)) {
            $itemName = $matches[2];
            return $this->getLocationInfo($itemName, $context);
        }

        // Pattern: Statistik
        if (preg_match('/(statistik|stats|total|jumlah).*(barang|inventaris)/i', $message)) {
            return $this->getStatistics($context);
        }

        // Pattern: Riwayat user
        if (preg_match('/(riwayat|history|peminjaman saya)/i', $message)) {
            return $this->getUserHistory($context);
        }

        // Default response with suggestions
        return $this->getDefaultResponse();
    }

    /**
     * Get inventory context for AI
     */
    private function getInventoryContext($userId = null)
    {
        $context = [];

        // Get inventory data
        $inventoryItems = $this->inventory->getAll();
        $context['inventory'] = array_map(function($item) {
            return [
                'nama' => $item['nama_barang'],
                'kategori' => $item['kategori'],
                'stok_tersedia' => $item['stok_tersedia'],
                'stok_total' => $item['stok_total'],
                'kondisi' => $item['kondisi'],
                'lokasi' => $item['lokasi']
            ];
        }, $inventoryItems);

        // Get inventory stats
        $context['stats'] = $this->inventory->getStats();

        // Get loan stats
        $context['loan_stats'] = $this->loan->getStats();

        // If user is logged in, get their loan history
        if ($userId) {
            $userLoans = $this->loan->getByUserId($userId);
            $context['user_loans'] = array_map(function($loan) {
                return [
                    'nama_barang' => $loan['nama_barang'],
                    'jumlah' => $loan['jumlah'],
                    'tanggal_pinjam' => $loan['tanggal_pinjam'],
                    'status' => $loan['status']
                ];
            }, $userLoans);
        }

        return $context;
    }

    /**
     * Get stock information for specific item
     */
    private function getStockInfo($itemName, $context)
    {
        foreach ($context['inventory'] as $item) {
            if (stripos($item['nama'], $itemName) !== false) {
                $emoji = $item['stok_tersedia'] > 0 ? '✅' : '❌';
                return "$emoji **{$item['nama']}**\n\n" .
                       "📦 Stok Tersedia: **{$item['stok_tersedia']}** dari {$item['stok_total']} unit\n" .
                       "🏷️ Kategori: {$item['kategori']}\n" .
                       "⚙️ Kondisi: {$item['kondisi']}\n" .
                       "📍 Lokasi: {$item['lokasi']}";
            }
        }
        return "❌ Maaf, barang '$itemName' tidak ditemukan dalam inventaris.";
    }

    /**
     * List all inventory items
     */
    private function listAllItems($context)
    {
        $response = "📦 **Daftar Barang Inventaris:**\n\n";

        foreach ($context['inventory'] as $item) {
            $status = $item['stok_tersedia'] > 0 ? '✅' : '❌';
            $response .= "$status **{$item['nama']}** ({$item['kategori']})\n";
            $response .= "   Stok: {$item['stok_tersedia']}/{$item['stok_total']} unit | Lokasi: {$item['lokasi']}\n\n";
        }

        return $response;
    }

    /**
     * Get loan information
     */
    private function getLoanInfo($context)
    {
        $stats = $context['loan_stats'];
        return "📊 **Statistik Peminjaman:**\n\n" .
               "⏳ Pending: **{$stats['pending']}** pengajuan\n" .
               "✅ Disetujui: **{$stats['approved']}** peminjaman\n" .
               "📥 Dikembalikan: **{$stats['returned']}** barang\n" .
               "❌ Ditolak: **{$stats['rejected']}** pengajuan\n\n" .
               "Total barang sedang dipinjam: **{$stats['approved']}** unit";
    }

    /**
     * Get location information for specific item
     */
    private function getLocationInfo($itemName, $context)
    {
        foreach ($context['inventory'] as $item) {
            if (stripos($item['nama'], $itemName) !== false) {
                return "📍 **Lokasi {$item['nama']}:**\n\n" .
                       "🏢 {$item['lokasi']}\n" .
                       "📦 Stok tersedia: {$item['stok_tersedia']} unit\n" .
                       "⚙️ Kondisi: {$item['kondisi']}";
            }
        }
        return "❌ Maaf, barang '$itemName' tidak ditemukan.";
    }

    /**
     * Get overall statistics
     */
    private function getStatistics($context)
    {
        $stats = $context['stats'];
        return "📊 **Statistik Inventaris NINEVENTORY:**\n\n" .
               "📦 Total Jenis Barang: **{$stats['total_items']}** jenis\n" .
               "📈 Total Stok: **{$stats['total_stock']}** unit\n" .
               "✅ Stok Tersedia: **{$stats['available_stock']}** unit\n" .
               "🔄 Sedang Dipinjam: **{$stats['borrowed_stock']}** unit\n\n" .
               "Tingkat penggunaan: " . round(($stats['borrowed_stock'] / $stats['total_stock']) * 100, 1) . "%";
    }

    /**
     * Get user loan history
     */
    private function getUserHistory($context)
    {
        if (!isset($context['user_loans']) || empty($context['user_loans'])) {
            return "📝 Anda belum memiliki riwayat peminjaman.";
        }

        $response = "📝 **Riwayat Peminjaman Anda:**\n\n";
        foreach ($context['user_loans'] as $loan) {
            $statusEmoji = [
                'pending' => '⏳',
                'approved' => '✅',
                'returned' => '📥',
                'rejected' => '❌'
            ];
            $emoji = $statusEmoji[$loan['status']] ?? '📌';

            $response .= "$emoji **{$loan['nama_barang']}** ({$loan['jumlah']} unit)\n";
            $response .= "   Tanggal: {$loan['tanggal_pinjam']} | Status: {$loan['status']}\n\n";
        }

        return $response;
    }

    /**
     * Default response with suggestions
     */
    private function getDefaultResponse()
    {
        return "👋 Halo! Saya asisten NINEVENTORY. Saya bisa membantu Anda dengan:\n\n" .
               "📦 Cek stok barang (contoh: \"Berapa stok laptop?\")\n" .
               "📋 Lihat daftar barang (contoh: \"Barang apa saja yang ada?\")\n" .
               "📊 Statistik peminjaman (contoh: \"Berapa total barang yang dipinjam?\")\n" .
               "📍 Lokasi barang (contoh: \"Dimana lokasi proyektor?\")\n" .
               "📝 Riwayat peminjaman (contoh: \"Riwayat peminjaman saya\")\n\n" .
               "Silakan tanyakan apa yang Anda butuhkan! 😊";
    }
}
