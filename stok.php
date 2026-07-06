<?php
// stok.php
session_start();
require_once 'config/database.php';

// Proteksi halaman
if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

$message = '';
$alert_type = 'sukses';


// 1. PROSES CRUD: CREATE (TAMBAH BARANG)
if (isset($_POST['tambah_barang'])) {
    $nama = trim($_POST['nama_barang']);
    $satuan = trim($_POST['satuan']);
    $harga_beli = (int)$_POST['harga_beli'];
    $harga_jual = (int)$_POST['harga_jual'];
    $stok = (int)$_POST['stok'];
    $stok_min = (int)$_POST['stok_minimum'];

    if (!empty($nama) && !empty($satuan)) {
        $stmt = $pdo->prepare("INSERT INTO barang (nama_barang, satuan, harga_beli, harga_jual, stok, stok_minimum) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nama, $satuan, $harga_beli, $harga_jual, $stok, $stok_min]);
        $message = '🎉 BERHASIL: Barang baru telah disimpan ke gudang!';
        $alert_type = 'sukses';
    }
}


// 2. PROSES CRUD: UPDATE (UBAH/EDIT BARANG)
if (isset($_POST['update_barang'])) {
    $id = (int)$_POST['id_barang'];
    $nama = trim($_POST['nama_barang']);
    $satuan = trim($_POST['satuan']);
    $harga_beli = (int)$_POST['harga_beli'];
    $harga_jual = (int)$_POST['harga_jual'];
    $stok = (int)$_POST['stok'];
    $stok_min = (int)$_POST['stok_minimum'];

    if ($id > 0 && !empty($nama) && !empty($satuan)) {
        $stmt = $pdo->prepare("UPDATE barang SET nama_barang = ?, satuan = ?, harga_beli = ?, harga_jual = ?, stok = ?, stok_minimum = ? WHERE id = ?");
        $stmt->execute([$nama, $satuan, $harga_beli, $harga_jual, $stok, $stok_min, $id]);
        $message = '✏️ BERHASIL: Data barang telah diperbarui!';
        $alert_type = 'sukses';
    }
}


// 3. PROSES CRUD: DELETE (HAPUS BARANG)
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    if ($id_hapus > 0) {
        $stmt = $pdo->prepare("DELETE FROM barang WHERE id = ?");
        $stmt->execute([$id_hapus]);
        $message = '🗑️ BERHASIL: Barang telah dihapus dari sistem gudang!';
        $alert_type = 'bahaya';
    }
}


// 4. PROSES CRUD: READ (TAMPILKAN BARANG)
$stmt = $pdo->query("SELECT * FROM barang ORDER BY id DESC");
$daftar_barang = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Stok CRUD - WarungKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@400;700;900&display=swap');
        body { font-family: 'Lexend', sans-serif; }
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
                <a href="transaksi.php" class="block bg-white hover:bg-black hover:text-white border-2 border-black font-black px-4 py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                    💸 Catat Transaksi
                </a>
                <a href="stok.php" class="block bg-black text-white border-2 border-black font-black px-4 py-2.5 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(255,255,255,1)] transition-all">
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
            <h1 class="text-3xl font-black text-black uppercase tracking-tight">Gudang & Inventaris Barang</h1>
            <p class="text-sm font-bold text-gray-600 mt-1">Pantau jumlah stok pasokan dan kelola harga modal/jual warung Anda.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="border-4 border-black p-4 font-black text-sm shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] <?= $alert_type == 'sukses' ? 'bg-[#4ADE80] text-black' : 'bg-[#F87171] text-white'; ?>">
                <?= $message; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            
            <div class="bg-[#C7D2FE] border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] h-fit">
                <h3 class="font-black text-lg text-black uppercase mb-4">➕ Tambah Barang</h3>
                <form action="stok.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-black uppercase text-black mb-1">Nama Barang</label>
                        <input type="text" name="nama_barang" required class="w-full border-2 border-black p-2 text-sm font-bold bg-white" placeholder="Contoh: Aqua Botol 600ml">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-black uppercase text-black mb-1">Satuan</label>
                            <input type="text" name="satuan" required class="w-full border-2 border-black p-2 text-sm font-bold bg-white" placeholder="Pcs / Kg">
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase text-black mb-1">Jumlah Stok</label>
                            <input type="number" name="stok" required min="0" class="w-full border-2 border-black p-2 text-sm font-bold bg-white" value="0">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-black uppercase text-black mb-1">Harga Beli</label>
                            <input type="number" name="harga_beli" required min="0" class="w-full border-2 border-black p-2 text-sm font-bold bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase text-black mb-1">Harga Jual</label>
                            <input type="number" name="harga_jual" required min="0" class="w-full border-2 border-black p-2 text-sm font-bold bg-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-black mb-1">Batas Minimum Stok</label>
                        <input type="number" name="stok_minimum" required min="1" class="w-full border-2 border-black p-2 text-sm font-bold bg-white" value="5">
                    </div>
                    <button type="submit" name="tambah_barang" class="w-full bg-[#4ADE80] text-black border-2 border-black font-black py-3 uppercase text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 transition-all">
                        Simpan ke Gudang ↓
                    </button>
                </form>
            </div>

            <div class="bg-white border-4 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] lg:col-span-2 overflow-hidden">
                <div class="bg-black text-white p-4 font-black text-sm uppercase tracking-wider">
                    📦 Daftar Stok Barang Warung
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-black font-black text-xs uppercase">
                                <th class="p-3 text-black">Nama Barang</th>
                                <th class="p-3 text-black">Harga Beli/Jual</th>
                                <th class="p-3 text-black text-center">Stok</th>
                                <th class="p-3 text-black text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-black font-bold text-sm">
                            <?php foreach ($daftar_barang as $brg): 
                                $is_menipis = $brg['stok'] <= $brg['stok_minimum'];
                            ?>
                                <tr class="<?= $is_menipis ? 'bg-amber-50' : 'hover:bg-gray-50'; ?>">
                                    <td class="p-3">
                                        <p class="font-black text-black"><?= htmlspecialchars($brg['nama_barang']); ?></p>
                                        <p class="text-xs text-gray-400">Satuan: <?= htmlspecialchars($brg['satuan']); ?></p>
                                    </td>
                                    <td class="p-3 text-xs">
                                        <p class="text-gray-500">Beli: Rp<?= number_format($brg['harga_beli'], 0, ',', '.'); ?></p>
                                        <p class="text-black font-black">Jual: Rp<?= number_format($brg['harga_jual'], 0, ',', '.'); ?></p>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="font-black"><?= $brg['stok']; ?></span><br>
                                        <span class="text-xxs px-1 border border-black uppercase font-black <?= $is_menipis ? 'bg-[#F97316] text-white' : 'bg-[#4ADE80] text-black'; ?>">
                                            <?= $is_menipis ? 'Menipis' : 'Aman'; ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-center space-y-1 sm:space-y-0 sm:space-x-1">
                                        <button onclick='bukaModalEdit(<?= json_encode($brg); ?>)' 
                                                class="bg-[#FDE047] hover:bg-yellow-400 text-black border-2 border-black font-black text-xs px-2 py-1 uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                                            Edit
                                        </button>
                                        <a href="stok.php?hapus=<?= $brg['id']; ?>" 
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus barang <?= htmlspecialchars($brg['nama_barang']); ?> dari gudang?')"
                                            class="inline-block bg-[#F87171] hover:bg-red-500 text-white border-2 border-black font-black text-xs px-2 py-1 uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
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

    <div id="modalEditBarang" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 z-50">
        <div class="bg-[#F3F4F6] border-4 border-black p-6 max-w-md w-full shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] relative">
            
            <div class="flex justify-between items-center border-b-4 border-black pb-3 mb-4">
                <h3 class="text-xl font-black text-black uppercase tracking-tight">✏️ Edit Data Barang</h3>
                <button onclick="tutupModalEdit()" class="bg-black text-white border-2 border-black font-black px-2 py-0.5 text-xs uppercase">X</button>
            </div>

            <form action="stok.php" method="POST" class="space-y-4">
                <input type="hidden" id="edit_id" name="id_barang">

                <div>
                    <label class="block text-xs font-black uppercase text-black mb-1">Nama Barang</label>
                    <input type="text" id="edit_nama" name="nama_barang" required class="w-full border-2 border-black p-2 text-sm font-bold bg-white">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-black uppercase text-black mb-1">Satuan</label>
                        <input type="text" id="edit_satuan" name="satuan" required class="w-full border-2 border-black p-2 text-sm font-bold bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-black mb-1">Jumlah Stok</label>
                        <input type="number" id="edit_stok" name="stok" required min="0" class="w-full border-2 border-black p-2 text-sm font-bold bg-white">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-black uppercase text-black mb-1">Harga Beli</label>
                        <input type="number" id="edit_harga_beli" name="harga_beli" required min="0" class="w-full border-2 border-black p-2 text-sm font-bold bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-black mb-1">Harga Jual</label>
                        <input type="number" id="edit_harga_jual" name="harga_jual" required min="0" class="w-full border-2 border-black p-2 text-sm font-bold bg-white">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase text-black mb-1">Batas Minimum Stok</label>
                    <input type="number" id="edit_stok_minimum" name="stok_minimum" required min="1" class="w-full border-2 border-black p-2 text-sm font-bold bg-white">
                </div>

                <div class="flex space-x-2 pt-2">
                    <button type="submit" name="update_barang" class="flex-1 bg-[#6366F1] text-white border-2 border-black font-black py-2.5 uppercase text-xs shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 transition-all">
                        Simpan Perubahan ✔️
                    </button>
                    <button type="button" onclick="tutupModalEdit()" class="bg-white text-black border-2 border-black font-black py-2.5 uppercase text-xs px-4 border border-black">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modalEditBarang');

        function bukaModalEdit(barang) {
            document.getElementById('edit_id').value = barang.id;
            document.getElementById('edit_nama').value = barang.nama_barang;
            document.getElementById('edit_satuan').value = barang.satuan;
            document.getElementById('edit_stok').value = barang.stok;
            document.getElementById('edit_harga_beli').value = barang.harga_beli;
            document.getElementById('edit_harga_jual').value = barang.harga_jual;
            document.getElementById('edit_stok_minimum').value = barang.stok_minimum;
            
            modal.classList.remove('hidden');
        }

        function tutupModalEdit() {
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>