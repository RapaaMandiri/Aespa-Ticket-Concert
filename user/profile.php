<?php
// user/profile.php
require_once '../config/database.php';

if (!isset($_SESSION['login_user']) || $_SESSION['login_user'] !== true) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pesan = "";

$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($query_user);

if (isset($_POST['update_profile'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nama_file_baru = $user['foto_profil'];

    if ($_FILES['foto']['error'] === 0) {
        $nama_file = $_FILES['foto']['name'];
        $ukuran_file = $_FILES['foto']['size'];
        $tmp_name = $_FILES['foto']['tmp_name'];
        
        $ekstensi_valid = ['jpg', 'jpeg', 'png'];
        $ekstensi_file = explode('.', $nama_file);
        $ekstensi_file = strtolower(end($ekstensi_file));

        if (!in_array($ekstensi_file, $ekstensi_valid)) {
            $pesan = "<div class='bg-red-500/10 border border-red-500/30 text-red-400 p-3 rounded-xl text-xs font-mono mb-4 text-center'>[ERROR] Format harus JPG, JPEG, atau PNG!</div>";
        } elseif ($ukuran_file > 2000000) {
            $pesan = "<div class='bg-red-500/10 border border-red-500/30 text-red-400 p-3 rounded-xl text-xs font-mono mb-4 text-center'>[ERROR] Ukuran file maksimal 2MB!</div>";
        } else {
            $nama_file_baru = "pp-" . $user_id . "-" . time() . "." . $ekstensi_file;
            move_uploaded_file($tmp_name, '../assets/uploads/profile/' . $nama_file_baru);
        }
    }

    if (empty($pesan)) {
        $update = "UPDATE users SET nama = '$nama', foto_profil = '$nama_file_baru' WHERE id = $user_id";
        if (mysqli_query($conn, $update)) {
            $_SESSION['user_nama'] = $nama;
            echo "<script>alert('🚀 Profile updated successfully!'); window.location='profile.php';</script>";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY Profile - aespa 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass-panel {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.6) 0%, rgba(30, 41, 59, 0.4) 100%);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen relative overflow-x-hidden flex flex-col justify-between">

    <!-- Ornamen Cahaya Neon Background -->
    <div class="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] bg-purple-600/10 rounded-full blur-[150px] pointer-events-none z-0"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-cyan-600/10 rounded-full blur-[130px] pointer-events-none z-0"></div>

    <!-- Container Utama Konten -->
    <div class="relative z-10 w-full flex flex-col min-h-screen">
        
        <!-- Navbar Premium -->
        <nav class="bg-slate-950/60 backdrop-blur-md border-b border-slate-900 fixed top-0 w-full z-50 px-6 py-4 flex justify-between items-center">
            <div class="text-xl font-black bg-clip-text text-transparent bg-gradient-to-r from-purple-400 via-fuchsia-400 to-cyan-400 tracking-widest uppercase">
                <a href="dashboard.php">AESPA SYNK 2026</a>
            </div>
            <div class="flex items-center space-x-6 text-xs font-bold uppercase tracking-wider">
                <a href="dashboard.php" class="text-slate-400 hover:text-white transition">← Dashboard</a>
                <a href="tiket-saya.php" class="text-slate-400 hover:text-white transition">Tiket Saya</a>
            </div>
        </nav>

        <!-- Form Profile Card Modis -->
        <div class="max-w-md w-full mx-auto mt-28 mb-16 px-4">
            <div class="glass-panel border border-slate-800/80 p-8 rounded-3xl shadow-2xl relative overflow-hidden">
                
                <!-- Dekorasi Sudut Cyberpunk -->
                <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-bl from-purple-500/10 to-transparent pointer-events-none"></div>
                
                <div class="mb-6">
                    <span class="text-[9px] font-black tracking-widest text-purple-400 uppercase bg-purple-500/10 border border-purple-500/20 px-2.5 py-1 rounded-md">USER IDENTITY</span>
                    <h2 class="text-2xl font-black mt-3 tracking-tight">MY PROFILE</h2>
                    <p class="text-xs text-slate-400 mt-1">Perbarui info akun dan enkripsi avatar digital kamu.</p>
                </div>

                <?= $pesan; ?>

                <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                    
                    <!-- Desain Avatar Bulat + Custom Input File Button -->
                    <div class="flex flex-col items-center justify-center space-y-4 bg-slate-950/40 p-5 rounded-2xl border border-slate-900">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-purple-500/50 shadow-lg shadow-purple-500/10 bg-slate-900 flex items-center justify-center transition group-hover:border-cyan-400/70 duration-300">
                                <?php if ($user['foto_profil'] === 'default-pp.png'): ?>
                                    <span class="text-4xl font-black text-purple-400 bg-clip-text text-transparent bg-gradient-to-br from-purple-400 to-cyan-400"><?= strtoupper(substr($user['nama'], 0, 1)); ?></span>
                                <?php else: ?>
                                    <img src="../assets/uploads/profile/<?= $user['foto_profil']; ?>" alt="Avatar" class="w-full h-full object-cover">
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Menyembunyikan input file bawaan browser yang jelek dan menggantinya dengan tombol modis -->
                        <div class="relative">
                            <input type="file" name="foto" id="file-upload" class="hidden" onchange="previewLabel(this)"/>
                            <label Bres-id for="file-upload" class="bg-slate-900 border border-slate-700/80 hover:border-slate-600 text-slate-300 px-4 py-2 rounded-xl text-xs font-bold tracking-wide cursor-pointer transition inline-block hover:bg-slate-800">
                                📷 Ubah Foto Profil
                            </label>
                        </div>
                        <span id="file-name" class="text-[10px] text-slate-500 font-mono italic">Maksimal resolusi file: 2MB (JPG, PNG)</span>
                    </div>

                    <!-- Input Nama Lengkap -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold">Nama Lengkap</label>
                        <input type="text" name="nama" required value="<?= htmlspecialchars($user['nama']); ?>" class="w-full bg-slate-950/80 border border-slate-800 focus:border-purple-500/60 rounded-xl p-3 text-white text-sm focus:outline-none transition font-medium">
                    </div>

                    <!-- Input Email (Read-Only Premium Look) -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] uppercase tracking-wider text-slate-500 font-bold">Email Address (Permanen)</label>
                        <div class="w-full bg-slate-950/30 border border-slate-900/60 text-slate-500 p-3 rounded-xl text-sm font-mono cursor-not-allowed select-none">
                            <?= htmlspecialchars($user['email']); ?>
                        </div>
                    </div>

                    <!-- Tombol Submit Akhir Bergradasi Cantik -->
                    <button type="submit" name="update_profile" class="w-full bg-gradient-to-r from-purple-600 via-fuchsia-600 to-cyan-500 hover:brightness-110 text-white font-black tracking-widest py-3.5 rounded-xl transition shadow-lg shadow-purple-600/10 text-xs uppercase duration-200">
                        Simpan Perubahan Profile
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer Mini -->
        <footer class="border-t border-slate-900/60 py-6 text-center text-[10px] text-slate-600 font-mono">
            SECURE PORTAL CORE SYSTEM // PROTOCOL EXECUTION
        </footer>
    </div>

    <!-- Script Kecil untuk Mengubah Label Nama File yang Dipilih -->
    <script>
        function previewLabel(input) {
            const label = document.getElementById('file-name');
            if (input.files && input.files[0]) {
                label.innerText = "📄 Terpilih: " + input.files[0].name;
                label.className = "text-[10px] text-cyan-400 font-mono";
            }
        }
    </script>
</body>
</html>