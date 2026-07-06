<?php
// dashboard.php
session_start();

// Proteksi Halaman: Jika belum login, tendang kembali ke halaman login
if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

// Data Dummy untuk keperluan Tampilan UI (Nanti kita hubungkan ke Database di modul berikutnya)
$nama_pemilik = $_SESSION['username'];

require_once 'config/database.php';

// 1. Hitung total uang MASUK hari ini secara riil dari database
$stmtMasuk = $pdo->query("SELECT SUM(nominal) FROM transaksi WHERE jenis = 'masuk' AND DATE(tanggal) = CURRENT_DATE()");
$pemasukan_hari_ini = (int)$stmtMasuk->fetchColumn();

// 2. Hitung total uang KELUAR hari ini secara riil dari database
$stmtKeluar = $pdo->query("SELECT SUM(nominal) FROM transaksi WHERE jenis = 'keluar' AND DATE(tanggal) = CURRENT_DATE()");
$pengeluaran_hari_ini = (int)$stmtKeluar->fetchColumn();

// 3. Kalkulasi Laba Bersih hari ini
$laba_hari_ini = $pemasukan_hari_ini - $pengeluaran_hari_ini;

// 4. Ambil 5 riwayat transaksi terupdate untuk komponen tabel dashboard
$stmtTx = $pdo->query("SELECT * FROM transaksi ORDER BY tanggal DESC LIMIT 5");
$transaksi_terbaru = $stmtTx->fetchAll();

// 5. Hitung indikator alert stok menipis
$stmtStok = $pdo->query("SELECT COUNT(*) FROM barang WHERE stok <= stok_minimum");
$stok_menipis_count = $stmtStok->fetchColumn();

$stok_menipis_count = 3;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - WarungKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@400;700;900&display=swap');
        body { font-family: 'Lexend', sans-serif; }
    </style>
</head>
<body class="bg-[#F3F4F6] min-h-screen flex flex-col md:flex-row">

    <aside class="w-full md:w-64 bg-[#FEF08A] border-b-4 md:border-b-0 md:border-r-4 border-black p-6 flex flex-col justify-between shadow-[4px_0px_0px_0px_rgba(0,0,0,1)] z-10">
        <div>
            <div class="bg-black text-[#22C55E] font-black text-2xl px-4 py-2 uppercase tracking-wider text-center border-2 border-black shadow-[4px_4px_0px_0px_rgba(34,197,94,1)] mb-8">
                🏪 WarungKu
            </div>
            
            <div class="bg-white border-2 border-black p-3 mb-6 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                <p class="text-xs font-black text-gray-500 uppercase">Pengguna:</p>
                <p class="text-sm font-black text-black uppercase truncate">👤 <?= htmlspecialchars($nama_pemilik); ?></p>
            </div>

            <nav class="space-y-3">
                <a href="dashboard.php" class="block bg-black text-white border-2 border-black font-black px-4 py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(255,255,255,1)] transition-all">
                    🎯 Dashboard
                </a>
                <a href="transaksi.php" class="block bg-white hover:bg-black hover:text-white border-2 border-black font-black px-4 py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                    💸 Catat Transaksi
                </a>
                <a href="stok.php" class="block bg-white hover:bg-[#C7D2FE] text-black border-2 border-black font-black px-4 py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none transition-all">
                    📦 Kelola Stok
                </a>
                <a href="laporan.php" class="block bg-white hover:bg-[#FDE047] text-black border-2 border-black font-black px-4 py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-none transition-all">
                    📊 Laporan Buku
                </a>
            </nav>
        </div>

        <div class="mt-8 md:mt-0">
            <a href="auth/logout.php" class="block text-center bg-[#F43F5E] hover:bg-rose-600 text-white border-2 border-black font-black py-2.5 uppercase text-sm tracking-wider shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                Keluar Sistem 🚪
            </a>
        </div>
    </aside>

    <main class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto">
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between bg-white border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
            <div>
                <h1 class="text-3xl font-black text-black uppercase tracking-tight">Pusat Kendali Harian</h1>
                <p class="text-sm font-bold text-gray-600 mt-1">Selamat bekerja kembali <span><?= htmlspecialchars($nama_pemilik); ?></span>!</p>
            </div>
            <div class="mt-4 md:mt-0 bg-[#6366F1] text-white border-2 border-black font-black px-4 py-2 uppercase text-xs tracking-wider shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                📅 <?= date('D, d M Y'); ?>
            </div>
        </div>

        <?php if ($stok_menipis_count > 0): ?>
            <div class="bg-[#F97316] text-black border-4 border-black p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">⚠️</span>
                    <p class="font-black text-sm uppercase tracking-tight text-white">
                        Perhatian: Ada <?= $stok_menipis_count; ?> barang dagangan yang hampir habis di gudang!
                    </p>
                </div>
                <a href="stok.php" class="bg-black text-white border-2 border-white font-black text-xs uppercase px-3 py-1.5 shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] hover:shadow-none transition-all">
                    Cek Barang
                </a>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="bg-[#4ADE80] border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                <p class="text-xs font-black text-black uppercase tracking-wider opacity-80">Total Pemasukan Hari Ini</p>
                <h3 class="text-2xl font-black text-black mt-2">Rp <?= number_format($pemasukan_hari_ini, 0, ',', '.'); ?></h3>
                <div class="mt-4 inline-block bg-white border-2 border-black font-black text-xs px-2 py-0.5 uppercase">🟢 Naik 12%</div>
            </div>

            <div class="bg-[#F87171] border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                <p class="text-xs font-black text-black uppercase tracking-wider opacity-80">Total Pengeluaran Hari Ini</p>
                <h3 class="text-2xl font-black text-black mt-2">Rp <?= number_format($pengeluaran_hari_ini, 0, ',', '.'); ?></h3>
                <div class="mt-4 inline-block bg-white border-2 border-black font-black text-xs px-2 py-0.5 uppercase">🔴 Operasional</div>
            </div>

            <div class="bg-[#60A5FA] border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                <p class="text-xs font-black text-black uppercase tracking-wider opacity-80">Estimasi Laba Bersih</p>
                <h3 class="text-2xl font-black text-black mt-2">Rp <?= number_format($laba_hari_ini, 0, ',', '.'); ?></h3>
                <div class="mt-4 inline-block bg-black text-white border-2 border-black font-black text-xs px-2 py-0.5 uppercase">💰 Aman</div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            
            <div class="bg-white border-4 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] lg:col-span-2">
                <div class="bg-black text-white p-4 border-b-4 border-black flex justify-between items-center">
                    <h3 class="font-black text-sm uppercase tracking-wider">📋 5 Transaksi Paling Gress</h3>
                    <span class="text-xs bg-[#FEF08A] text-black font-black px-2 py-1 border border-black uppercase">Real-Time</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-black">
                                <th class="p-3 text-xs font-black uppercase text-black">Waktu</th>
                                <th class="p-3 text-xs font-black uppercase text-black">Jenis</th>
                                <th class="p-3 text-xs font-black uppercase text-black">Kategori</th>
                                <th class="p-3 text-xs font-black uppercase text-black text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-black font-bold text-sm">
                            <?php if (count($transaksi_terbaru) == 0): ?>
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-400 uppercase">Belum ada transaksi hari ini.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($transaksi_terbaru as $t): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3 text-xs text-gray-500"><?= date('H:i', strtotime($t['tanggal'])); ?> WIB</td>
                                    <td class="p-3">
                                        <span class="border border-black text-xs px-2 py-0.5 uppercase font-black <?= $t['jenis'] == 'masuk' ? 'bg-[#4ADE80] text-black' : 'bg-[#F87171] text-white'; ?>">
                                            <?= $t['jenis'] == 'masuk' ? 'Masuk' : 'Keluar'; ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-black"><?= htmlspecialchars($t['kategori']); ?></td>
                                    <td class="p-3 text-right font-black <?= $t['jenis'] == 'masuk' ? 'text-emerald-600' : 'text-rose-600'; ?>">
                                        <?= $t['jenis'] == 'masuk' ? '+' : '-'; ?>Rp <?= number_format($t['nominal'], 0, ',', '.'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-[#C7D2FE] border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex flex-col justify-between">
                <div>
                    <h3 class="font-black text-lg text-black uppercase tracking-tight mb-2">⚡ Tindakan Cepat</h3>
                    <p class="text-xs font-bold text-gray-700 mb-6">Gunakan tombol pintas di bawah ini untuk menginput data keuangan warung Anda dalam sekejap.</p>
                </div>
                
                <div class="space-y-4">
                    <a href="transaksi.php?jenis=masuk" class="block text-center w-full bg-[#4ADE80] hover:bg-emerald-500 text-black border-2 border-black font-black py-3 uppercase text-sm tracking-wide shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                        🟢 Catat Transaksi Masuk
                    </a>
                    
                    <a href="transaksi.php?jenis=keluar" class="block text-center w-full bg-[#F87171] hover:bg-rose-500 text-black border-2 border-black font-black py-3 uppercase text-sm tracking-wide shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                        🔴 Catat Transaksi Keluar
                    </a>
                </div>
            </div>

        </div>
    </main>

</body>
</html>