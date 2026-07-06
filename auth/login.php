<?php
// auth/login.php
session_start();
require_once '../config/database.php';


try {
    // 1. Pastikan kolom password sudah VARCHAR(255) agar hash tidak terpotong
    $pdo->exec("ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NOT NULL");
    
    // 2. Cek apakah user 'aghis' sudah ada
    $stmtCheck = $pdo->prepare("SELECT * FROM users WHERE username = 'aghis'");
    $stmtCheck->execute();
    $userExist = $stmtCheck->fetch();
    
    // 3. Generate hash bersih baru untuk password 'rahasia123'
    $password_polos = 'rahasia123';
    $password_hash_bersih = password_hash($password_polos, PASSWORD_BCRYPT);
    
    if (!$userExist) {
        // Jika belum ada, buat baru
        $stmtInsert = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmtInsert->execute(['aghis', $password_hash_bersih]);
    } else {
        // Jika sudah ada tetapi verify gagal (kasus error tadi), kita timpa dengan hash yang valid
        if (!password_verify($password_polos, $userExist['password'])) {
            $stmtUpdate = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'aghis'");
            $stmtUpdate->execute([$password_hash_bersih]);
        }
    }
} catch (\PDOException $e) {
    // Jika ada masalah koneksi database, tampilkan di layar
    die("Koneksi Database Gagal: " . $e->getMessage());
}


// FR-01-02: Jika sudah login, langsung lempar ke dashboard
if (isset($_SESSION['login'])) {
    header("Location: ../dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WarungKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@400;700;900&display=swap');
        body { font-family: 'Lexend', sans-serif; }
    </style>
</head>
<body class="bg-[#E0E7FF] min-h-screen flex items-center justify-center p-4">

    <div class="bg-white border-4 border-black p-8 max-w-sm w-full shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] rounded-none">
        
        <div class="text-center mb-6">
            <div class="inline-block bg-[#22C55E] text-white border-2 border-black font-black text-xl px-4 py-1 uppercase tracking-wider shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] mb-3">
                🏪 WarungKu
            </div>
            <h2 class="text-2xl font-black text-black uppercase tracking-tight">Masuk Akun</h2>
            <p class="text-xs font-bold text-gray-600 mt-1">Sistem Keuangan & Inventaris UMKM</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-[#F43F5E] text-white border-2 border-black font-bold p-3 text-sm mb-4 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                ⚠️ Username atau Password salah!
            </div>
        <?php endif; ?>

        <form action="proses-login.php" method="POST" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-black text-black uppercase mb-1">Username</label>
                <input type="text" id="username" name="username" required autocomplete="off"
                       class="w-full border-2 border-black p-2.5 text-sm font-bold focus:outline-none focus:bg-[#FEF08A] placeholder-gray-400 bg-gray-50 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] focus:shadow-none transition-all"
                       placeholder="Masukkan username">
            </div>

            <div>
                <label for="password" class="block text-sm font-black text-black uppercase mb-1">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full border-2 border-black p-2.5 text-sm font-bold focus:outline-none focus:bg-[#FEF08A] placeholder-gray-400 bg-gray-50 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] focus:shadow-none transition-all"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="remember" name="remember" 
                       class="w-4 h-4 accent-black border-2 border-black rounded-none cursor-pointer">
                <label for="remember" class="ml-2 text-xs font-black text-black uppercase cursor-pointer select-none">
                    Ingat Saya (30 Hari)
                </label>
            </div>

            <button type="submit" name="login"
                    class="w-full bg-[#6366F1] hover:bg-[#4F46E5] text-white border-2 border-black font-black py-3 text-sm uppercase tracking-wider shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all">
                Masuk ke Dashboard →
            </button>
        </form>
    </div>

</body>
</html>