<?php
// transaksi.php

session_start(); // Jalankan session pembungkus data user
require_once 'config/database.php'; // Konek ke database MySQL via PDO

// Cek apakah user sudah login, kalau belum tendang ke halaman login
if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

$message = ''; // Wadah teks pesan sukses/gagal
$alert_type = 'sukses'; // Penentu warna notifikasi (hijau/merah)

// PROSES TAMBAH TRANSAKSI BARU (C - CREATE)
if (isset($_POST['simpan_transaksi'])) { 
    $jenis = $_POST['jenis']; // Ambil pilihan 'masuk' atau 'keluar'
    $kategori = trim($_POST['kategori']); // Ambil teks kategori, buang spasi ujung
    $nominal = (int)$_POST['nominal']; // Paksa jadi angka bulat demi keamanan keuangan
    $keterangan = trim($_POST['keterangan']); // Ambil catatan tambahan jika ada

    // Validasi: Form tidak boleh kosong & nominal harus di atas Rp 0
    if (!empty($jenis) && !empty($kategori) && $nominal > 0) {
        // Gunakan prepared statement agar aman dari SQL Injection
        $stmt = $pdo->prepare("INSERT INTO transaksi (jenis, kategori, nominal, keterangan) VALUES (?, ?, ?, ?)");
        $stmt->execute([$jenis, $kategori, $nominal, $keterangan]);
        
        $message = '🎉 BERHASIL: Transaksi baru berhasil disimpan ke pembukuan!';
        $alert_type = 'sukses';
    }
}

// PROSES EDIT / UPDATE TRANSAKSI (U - UPDATE)
if (isset($_POST['update_transaksi'])) { 
    $id = (int)$_POST['id_transaksi']; // Ambil ID baris yang mau diubah
    $jenis = $_POST['jenis'];
    $kategori = trim($_POST['kategori']);
    $nominal = (int)$_POST['nominal'];
    $keterangan = trim($_POST['keterangan']);

    // Pastikan ID valid dan input ralatnya masuk akal
    if ($id > 0 && !empty($jenis) && !empty($kategori) && $nominal > 0) {
        $stmt = $pdo->prepare("UPDATE transaksi SET jenis = ?, kategori = ?, nominal = ?, keterangan = ? WHERE id = ?");
        $stmt->execute([$jenis, $kategori, $nominal, $keterangan, $id]);
        
        $message = '✏️ BERHASIL: Catatan transaksi telah diperbarui!';
        $alert_type = 'sukses';
    }
}

// PROSES HAPUS TRANSAKSI (D - DELETE)
if (isset($_GET['hapus'])) { 
    $id_hapus = (int)$_GET['hapus']; // Ambil parameter ID dari URL (?hapus=ID)
    
    if ($id_hapus > 0) {
        $stmt = $pdo->prepare("DELETE FROM transaksi WHERE id = ?");
        $stmt->execute([$id_hapus]); // Hapus data dari tabel
        
        $message = '🗑️ BERHASIL: Catatan transaksi telah dihapus dari jurnal keuangan!';
        $alert_type = 'bahaya'; // Set notifikasi warna merah
    }
}

// TARIK DATA UNTUK DITAMPILKAN (R - READ)
// Ambil semua data transaksi, urutkan dari tanggal paling baru
$stmt = $pdo->query("SELECT * FROM transaksi ORDER BY tanggal DESC");
$semua_transaksi = $stmt->fetchAll(); // Simpan hasil tarikan ke dalam array
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Transaksi CRUD - WarungKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@400;700;900&display=swap');
        body { font-family: 'Lexend', sans-serif; } /* Set font default web pakai Lexend */
    </style>
</head>
<body class="bg-[#F3F4F6] min-h-screen flex flex-col md:flex-row pb-24 md:pb-0">

    <aside class="w-full md:w-64 bg-[#FEF08A] border-b-4 md:border-b-0 md:border-r-4 border-black p-6 flex flex-col justify-between shadow-[4px_0px_0px_0px_rgba(0,0,0,1)] z-10">
        <div>
            <div class="bg-black text-[#22C55E] font-black text-2xl px-4 py-2 uppercase text-center border-2 border-black shadow-[4px_4px_0px_0px_rgba(34,197,94,1)] mb-8">
                🏪 WarungKu
            </div>
            <nav class="space-y-3">
                <a href="dashboard.php" class="block bg-white hover:bg-black hover:text-white border-2 border-black font-black px-4 py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                    🎯 Dashboard
                </a>
                <a href="transaksi.php" class="block bg-black text-white border-2 border-black font-black px-4 py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(255,255,255,1)] transition-all">
                    💸 Catat Transaksi
                </a>
                <a href="stok.php" class="block bg-white hover:bg-black hover:text-white border-2 border-black font-black px-4 py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                    📦 Kelola Stok
                </a>
                <a href="laporan.php" class="block bg-white hover:bg-black hover:text-white border-2 border-black font-black px-4 py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
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
        
        <div class="bg-white border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
            <h1 class="text-3xl font-black text-black uppercase tracking-tight">Pencatatan Keuangan</h1>
            <p class="text-sm font-bold text-gray-600 mt-1">Kelola pembukuan arus kas masuk dan pengeluaran operasional warung secara disiplin.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="border-4 border-black p-4 font-black text-sm shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] <?= $alert_type == 'sukses' ? 'bg-[#4ADE80] text-black' : 'bg-[#F87171] text-white'; ?>">
                <?= $message; ?> 
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            
            <div class="bg-[#C7D2FE] border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] h-fit">
                <h3 class="font-black text-lg text-black uppercase mb-4">➕ Tambah Transaksi</h3>
                <form action="transaksi.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-black uppercase text-black mb-1">Jenis Arus Kas</label>
                        <select name="jenis" required class="w-full border-2 border-black p-2 text-sm font-bold bg-white cursor-pointer focus:bg-[#FEF08A]">
                            <option value="masuk">🟢 PEMASUKAN (UANG MASUK)</option>
                            <option value="keluar">🔴 PENGELUARAN (UANG KELUAR)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-black mb-1">Kategori</label>
                        <input type="text" name="kategori" required class="w-full border-2 border-black p-2 text-sm font-bold bg-white" placeholder="Contoh: Penjualan Produk, Bayar Listrik">
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-black mb-1">Nominal (Rupiah)</label>
                        <input type="number" name="nominal" required min="1" class="w-full border-2 border-black p-2 text-sm font-bold bg-white" placeholder="Rp">
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-black mb-1">Keterangan Tambahan</label>
                        <textarea name="keterangan" rows="3" class="w-full border-2 border-black p-2 text-sm font-bold bg-white" placeholder="Catatan ringkas..."></textarea>
                    </div>
                    <button type="submit" name="simpan_transaksi" class="w-full bg-[#4ADE80] text-black border-2 border-black font-black py-3 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 transition-all">
                        Simpan Transaksi ↓
                    </button>
                </form>
            </div>

            <div class="bg-white border-4 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] lg:col-span-2 overflow-hidden">
                <div class="bg-black text-white p-4 font-black text-sm uppercase tracking-wider">
                    📋 Buku Jurnal Arus Kas Warung
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-black font-black text-xs uppercase">
                                <th class="p-3 text-black">Tanggal</th>
                                <th class="p-3 text-black">Kategori / Keterangan</th>
                                <th class="p-3 text-black text-right">Nominal</th>
                                <th class="p-3 text-black text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-black font-bold text-sm">
                            <?php if (count($semua_transaksi) == 0): ?>
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-gray-400 font-bold uppercase">Belum ada catatan transaksi keuangan.</td>
                                </tr>
                            <?php endif; ?>
                            
                            <?php foreach ($semua_transaksi as $tx): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3 text-xs text-gray-500">
                                        <?= date('d M Y | H:i', strtotime($tx['tanggal'])); ?>
                                    </td>
                                    <td class="p-3">
                                        <div class="flex items-center space-x-2">
                                            <?php if ($tx['jenis'] == 'masuk'): ?>
                                                <span class="bg-[#4ADE80] text-black text-xxs font-black px-2 py-0.5 border border-black uppercase">Masuk</span>
                                            <?php else: ?>
                                                <span class="bg-[#F87171] text-white text-xxs font-black px-2 py-0.5 border border-black uppercase">Keluar</span>
                                            <?php endif; ?>
                                            <span class="font-black text-black"><?= htmlspecialchars($tx['kategori']); ?></span>
                                        </div>
                                        <?php if(!empty($tx['keterangan'])): ?>
                                            <p class="text-xs text-gray-400 mt-1 italic">"<?= htmlspecialchars($tx['keterangan']); ?>"</p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3 text-right font-black text-base <?= $tx['jenis'] == 'masuk' ? 'text-emerald-600' : 'text-rose-600'; ?>">
                                        <?= $tx['jenis'] == 'masuk' ? '+' : '-'; ?>Rp <?= number_format($tx['nominal'], 0, ',', '.'); ?>
                                    </td>
                                    
                                    <td class="p-3 text-center space-y-1 sm:space-y-0 sm:space-x-1">
                                        <button onclick='bukaModalEditTx(<?= json_encode($tx); ?>)' 
                                                class="bg-[#FDE047] hover:bg-yellow-400 text-black border-2 border-black font-black text-xs px-2 py-1 uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 transition-all">
                                            Edit
                                        </button>
                                        <a href="transaksi.php?hapus=<?= $tx['id']; ?>" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus jurnal keuangan ini?')" 
                                           class="inline-block bg-[#F87171] hover:bg-red-500 text-white border-2 border-black font-black text-xs px-2 py-1 uppercase shadow-[2px_2px_0px_0px_rgba(254,254,254,1)] active:translate-x-0.5 active:translate-y-0.5 transition-all">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?> 
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <div id="modalEditTransaksi" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 z-50">
        <div class="bg-[#F3F4F6] border-4 border-black p-6 max-w-md w-full shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] relative">
            
            <div class="flex justify-between items-center border-b-4 border-black pb-3 mb-4">
                <h3 class="text-xl font-black text-black uppercase tracking-tight">✏️ Edit Catatan Kas</h3>
                <button onclick="tutupModalEditTx()" class="bg-black text-white border-2 border-black font-black px-2 py-0.5 text-xs">X</button>
            </div>

            <form action="transaksi.php" method="POST" class="space-y-4">
                <input type="hidden" id="edit_tx_id" name="id_transaksi">
                <div>
                    <label class="block text-xs font-black uppercase text-black mb-1">Jenis Arus Kas</label>
                    <select id="edit_tx_jenis" name="jenis" required class="w-full border-2 border-black p-2 text-sm font-bold bg-white">
                        <option value="masuk">🟢 PEMASUKAN (UANG MASUK)</option>
                        <option value="keluar">🔴 PENGELUARAN (UANG KELUAR)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase text-black mb-1">Kategori</label>
                    <input type="text" id="edit_tx_kategori" name="kategori" required class="w-full border-2 border-black p-2 text-sm font-bold bg-white">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase text-black mb-1">Nominal (Rupiah)</label>
                    <input type="number" id="edit_tx_nominal" name="nominal" required min="1" class="w-full border-2 border-black p-2 text-sm font-bold bg-white">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase text-black mb-1">Keterangan Tambahan</label>
                    <textarea id="edit_tx_keterangan" name="keterangan" rows="3" class="w-full border-2 border-black p-2 text-sm font-bold bg-white"></textarea>
                </div>

                <div class="flex space-x-2 pt-2">
                    <button type="submit" name="update_transaksi" class="flex-1 bg-[#6366F1] text-white border-2 border-black font-black py-2.5 uppercase text-xs shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 transition-all">
                        Simpan Perubahan ✔️
                    </button>
                    <button type="button" onclick="tutupModalEditTx()" class="bg-white text-black border-2 border-black font-black py-2.5 uppercase text-xs px-4">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modalTx = document.getElementById('modalEditTransaksi');

        // Fungsi pemicu saat tombol 'Edit' di klik: Munculkan modal dan isi nilainya
        function bukaModalEditTx(tx) {
            document.getElementById('edit_tx_id').value = tx.id;
            document.getElementById('edit_tx_jenis').value = tx.jenis;
            document.getElementById('edit_tx_kategori').value = tx.kategori;
            document.getElementById('edit_tx_nominal').value = tx.nominal;
            document.getElementById('edit_tx_keterangan').value = tx.keterangan;
            
            modalTx.classList.remove('hidden'); // Hilangkan class hidden agar modal tampil
        }

        // Fungsi menyembunyikan modal edit kembali
        function tutupModalEditTx() {
            modalTx.classList.add('hidden'); // Pasang kembali class hidden
        }
    </script>
</body>
</html>