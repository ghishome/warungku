<?php
// laporan.php
session_start();
require_once 'config/database.php';

// Proteksi halaman
if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

// 1. Hitung Akumulasi Total Selama Ini
$stmtTotalMasuk = $pdo->query("SELECT SUM(nominal) FROM transaksi WHERE jenis = 'masuk'");
$total_pemasukan = (int)$stmtTotalMasuk->fetchColumn();

$stmtTotalKeluar = $pdo->query("SELECT SUM(nominal) FROM transaksi WHERE jenis = 'keluar'");
$total_pengeluaran = (int)$stmtTotalKeluar->fetchColumn();

$total_laba = $total_pemasukan - $total_pengeluaran;

// 2. Ambil Data Transaksi per Tanggal untuk Grafik Chart.js (7 Data Terakhir)
$stmtGrafik = $pdo->query("
    SELECT DATE(tanggal) as tgl, 
           SUM(CASE WHEN jenis = 'masuk' THEN nominal ELSE 0 END) as masuk,
           SUM(CASE WHEN jenis = 'keluar' THEN nominal ELSE 0 END) as keluar
    FROM transaksi 
    GROUP BY DATE(tanggal) 
    ORDER BY DATE(tanggal) ASC 
    LIMIT 7
");
$data_grafik = $stmtGrafik->fetchAll();

// Persiapan data untuk dilempar ke JavaScript Chart.js
$labels = [];
$pemasukan_data = [];
$pengeluaran_data = [];

foreach ($data_grafik as $row) {
    $labels[] = date('d M', strtotime($row['tgl']));
    $pemasukan_data[] = (int)$row['masuk'];
    $pengeluaran_data[] = (int)$row['keluar'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - WarungKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@400;700;900&display=swap');
        body { font-family: 'Lexend', sans-serif; }
        
        /* Styling khusus saat cetak PDF (window.print) */
        @media print {
            aside, .btn-print { display: none !important; }
            main { padding: 0 !important; width: 100% !important; }
            body { background-color: white !important; }
            .brutal-card { shadow: none !important; border-width: 2px !important; }
        }
    </style>
</head>
<body class="bg-[#F3F4F6] min-h-screen flex flex-col md:flex-row">

    <aside class="w-full md:w-64 bg-[#FEF08A] border-b-4 md:border-b-0 md:border-r-4 border-black p-6 flex flex-col justify-between shadow-[4px_0px_0px_0px_rgba(0,0,0,1)] z-10">
        <div>
            <div class="bg-black text-[#22C55E] font-black text-2xl px-4 py-2 uppercase text-center border-2 border-black shadow-[4px_4px_0px_0px_rgba(34,197,94,1)] mb-8">
                🏪 WarungKu
            </div>
            <nav class="space-y-3">
                <a href="dashboard.php" class="block bg-white hover:bg-black hover:text-white border-2 border-black font-black px-4 py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                    🎯 Dashboard
                </a>
                <a href="transaksi.php" class="block bg-white hover:bg-black hover:text-white border-2 border-black font-black px-4 py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                    💸 Catat Transaksi
                </a>
                <a href="stok.php" class="block bg-white hover:bg-black hover:text-white border-2 border-black font-black px-4 py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                    📦 Kelola Stok
                </a>
                <a href="laporan.php" class="block bg-black text-white border-2 border-black font-black px-4 py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(255,255,255,1)] transition-all">
                    📊 Laporan Buku
                </a>
            </nav>
        </div>
        <div class="mt-8 md:mt-0">
            <a href="auth/logout.php" class="block text-center bg-[#F43F5E] text-white border-2 border-black font-black py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                Keluar Sistem 🚪
            </a>
        </div>
    </aside>

    <main class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto">
        
        <div class="bg-white border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex flex-col md:flex-row md:items-center md:justify-between brutal-card">
            <div>
                <h1 class="text-3xl font-black text-black uppercase tracking-tight">Laporan & Analisis Buku</h1>
                <p class="text-sm font-bold text-gray-600 mt-1">Unduh laporan dalam format dokumen cetak atau pantau grafik naik-turun bisnis.</p>
            </div>
            <button onclick="window.print()" class="btn-print mt-4 md:mt-0 bg-[#A7F3D0] hover:bg-emerald-400 text-black border-2 border-black font-black px-5 py-3 uppercase text-sm shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                🖨️ Cetak Laporan PDF
            </button>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="bg-white border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] brutal-card">
                <p class="text-xs font-black text-gray-400 uppercase tracking-wider">Total Omset Pemasukan</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-2">Rp <?= number_format($total_pemasukan, 0, ',', '.'); ?></h3>
            </div>
            <div class="bg-white border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] brutal-card">
                <p class="text-xs font-black text-gray-400 uppercase tracking-wider">Total Pengeluaran Bersih</p>
                <h3 class="text-2xl font-black text-rose-600 mt-2">Rp <?= number_format($total_pengeluaran, 0, ',', '.'); ?></h3>
            </div>
            <div class="bg-white border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] brutal-card">
                <p class="text-xs font-black text-gray-400 uppercase tracking-wider">Akumulasi Saldo / Laba</p>
                <h3 class="text-2xl font-black text-blue-600 mt-2">Rp <?= number_format($total_laba, 0, ',', '.'); ?></h3>
            </div>
        </div>

        <div class="bg-white border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] brutal-card">
            <div class="font-black text-md uppercase border-b-2 border-black pb-3 mb-4 text-black">
                📈 Grafik Tren Arus Kas Masuk vs Keluar
            </div>
            <div class="w-full h-64 md:h-80">
                <canvas id="canvasChartBrutal"></canvas>
            </div>
        </div>

    </main>

    <script>
        const ctx = document.getElementById('canvasChartBrutal').getContext('2d');
        new Chart(ctx, {
            type: 'bar', // Menggunakan chart batang tegas
            data: {
                labels: <?= json_encode($labels); ?>,
                datasets: [
                    {
                        label: 'PEMASUKAN',
                        data: <?= json_encode($pemasukan_data); ?>,
                        backgroundColor: '#4ADE80', // Hijau terang brutalism
                        borderColor: '#000000',
                        borderWidth: 3,
                        borderRadius: 0 // Kotak kaku tanpa lengkungan
                    },
                    {
                        label: 'PENGELUARAN',
                        data: <?= json_encode($pengeluaran_data); ?>,
                        backgroundColor: '#F87171', // Merah terang brutalism
                        borderColor: '#000000',
                        borderWidth: 3,
                        borderRadius: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 20,   // Memberikan ruang di kiri agar angka nominal tidak kepotong
                        right: 20,  // Ruang aman di kanan
                        top: 10,
                        bottom: 10
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: { family: 'Lexend', weight: 'bold' },
                            color: '#000000'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#000000', lineWidth: 1 },
                        ticks: { 
                            font: { family: 'Lexend', weight: 'bold' }, 
                            color: '#000000',
                            padding: 10 // Jarak angka dari garis grafik
                        }
                    },
                    y: {
                        grid: { color: '#000000', lineWidth: 1 },
                        ticks: { 
                            font: { family: 'Lexend', weight: 'bold' }, 
                            color: '#000000',
                            padding: 10, // Jarak angka nominal dari garis kiri
                            // Mengubah format angka menjadi ribuan agar tidak terlalu panjang
                            callback: function(value, index, values) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000) + 'rb';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>