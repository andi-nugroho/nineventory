<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

use Nineventory\Auth;
use Nineventory\Loan;

$auth = new Auth($pdo);
$auth->requireLogin(); // User can print their own, Admin can print anyone's

$loan = new Loan($pdo);
$id = $_GET['id'] ?? 0;
// Note: In real app, verify user owns this loan or is admin
$data = $loan->getById($id);

if (!$data) {
    die("Data not found");
}

$currentUser = $auth->getCurrentUser();
if (!$auth->isAdmin() && $currentUser['id'] != $data['user_id']) {
   die("Unauthorized");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Proof #<?= $data['kode_peminjaman'] ?? $data['id'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; -webkit-print-color-adjust: exact; }
        }
        body { font-family: 'Times New Roman', serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-8 flex justify-center text-gray-900">

    <div class="bg-white p-12 max-w-4xl w-full shadow-lg print:shadow-none print:w-full print:max-w-none print:p-0">
        
        <!-- Header -->
        <div class="flex items-center gap-6 border-b-2 border-gray-800 pb-6 mb-8">
            <img src="../logo.svg" alt="NINEVENTORY Logo" class="w-16 h-16 object-contain">
            <div>
                <h1 class="text-2xl font-bold uppercase tracking-wider">NINEVENTORY OFFICE</h1>
                <p class="text-sm">Jl. Teknologi No. 9, Jakarta Selatan</p>
                <p class="text-sm">Tel: (021) 555-0199 | Email: admin@nineventory.com</p>
            </div>
            <div class="ml-auto text-right">
                <h2 class="text-xl font-bold uppercase">Bukti Peminjaman</h2>
                <p class="text-sm font-mono"><?= $data['kode_peminjaman'] ?? 'LOAN-'.$data['id'] ?></p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-8 text-sm">
            <div>
                <h3 class="font-bold border-b border-gray-300 mb-2 pb-1 uppercase text-xs text-gray-500">Peminjam</h3>
                <p class="font-bold text-lg"><?= htmlspecialchars($data['username']) ?></p>
                <p><?= htmlspecialchars($data['email']) ?></p>
            </div>
            <div class="text-right">
                 <h3 class="font-bold border-b border-gray-300 mb-2 pb-1 uppercase text-xs text-gray-500">Detail Waktu</h3>
                 <p>Tanggal Pinjam: <span class="font-semibold"><?= date('d F Y', strtotime($data['tanggal_pinjam'])) ?></span></p>
                 <p>Rencana Kembali: <span class="font-semibold"><?= $data['tanggal_kembali_rencana'] ? date('d F Y', strtotime($data['tanggal_kembali_rencana'])) : '-' ?></span></p>
                 <p class="mt-2">Status: <span class="uppercase font-bold border px-1 border-gray-800"><?= $data['status'] ?></span></p>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="font-bold uppercase text-xs text-gray-500 mb-2">Daftar Barang</h3>
            <table class="w-full border-collapse border border-gray-800 text-sm">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-800">
                        <th class="border-r border-gray-800 px-3 py-2 text-left w-12">No</th>
                        <th class="border-r border-gray-800 px-3 py-2 text-left">Nama Barang</th>
                        <th class="border-r border-gray-800 px-3 py-2 text-center w-24">Jumlah</th>
                        <th class="px-3 py-2 text-left">Kondisi Saat Pinjam</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['details'] as $index => $item): ?>
                    <tr class="border-b border-gray-800">
                        <td class="border-r border-gray-800 px-3 py-2"><?= $index + 1 ?></td>
                        <td class="border-r border-gray-800 px-3 py-2">
                            <span class="font-bold block"><?= htmlspecialchars($item['nama_barang']) ?></span>
                            <span class="text-xs text-gray-500"><?= htmlspecialchars($item['kategori']) ?></span>
                        </td>
                        <td class="border-r border-gray-800 px-3 py-2 text-center"><?= $item['jumlah'] ?></td>
                        <td class="px-3 py-2 text-gray-500 italic">Baik</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mb-12 text-sm italic text-gray-500 border p-4">
            <strong>Catatan:</strong><br>
            <?= nl2br(htmlspecialchars($data['keterangan'] ?? '-')) ?>
            <br><br>
            * Harap menjaga barang dengan baik. Kerusakan atau kehilangan menjadi tanggung jawab peminjam.
        </div>

        <div class="grid grid-cols-3 gap-8 text-center text-sm mt-12">
            <div>
                <p class="mb-16">Peminjam,</p>
                <p class="font-bold border-t border-dashed border-gray-400 pt-2"><?= htmlspecialchars($data['username']) ?></p>
            </div>
            <div></div>
            <div>
                <p class="mb-16">Menyetujui (Admin),</p>
                <p class="font-bold border-t border-dashed border-gray-400 pt-2">Admin NINEVENTORY</p>
            </div>
        </div>

        
        <div class="fixed bottom-8 right-8 no-print flex gap-2">
            <button onclick="window.print()" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-full shadow-lg hover:bg-blue-700 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Export as PDF
            </button>
            <button onclick="window.close()" class="px-6 py-3 bg-gray-600 text-white font-bold rounded-full shadow-lg hover:bg-gray-700 transition">
                Close
            </button>
        </div>

    </div>

    <script>
        // Auto print on load optionally
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
