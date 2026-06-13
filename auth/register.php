<?php
// auth/register.php
require_once '../config/database.php';

// Jalankan session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika user ternyata sudah login, langsung alihkan ke dashboard
if (isset($_SESSION['login_user']) && $_SESSION['login_user'] === true) {
    header("Location: ../user/dashboard.php");
    exit;
}

$pesan = "";

if (isset($_POST['register'])) {
    // PROTEKSI FORM: Pastikan user sudah mencentang persetujuan persyaratan
    if (!isset($_POST['setuju_syarat'])) {
        $pesan = "<div class='text-center text-xs font-bold text-amber-400 font-mono pb-4 animate-pulse'>[VALIDATION] Anda wajib menyetujui Syarat & Ketentuan Portal!</div>";
    } else {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];
        
        // Cek apakah email sudah terdaftar di database
        $cek_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek_email) > 0) {
            $pesan = "<div class='text-center text-xs font-bold text-red-500 font-mono pb-4 animate-pulse'>[ERROR] Email tersebut sudah terdaftar di sistem!</div>";
        } else {
            // Enkripsi password menggunakan bcrypt standar PHP
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Simpan ke database dengan role 'user' dan set foto_profil ke default-pp.png
            $query = "INSERT INTO users (nama, email, password, role, foto_profil) VALUES ('$nama', '$email', '$password_hash', 'user', 'default-pp.png')";
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('🎉 Pendaftaran akun MY berhasil! Silakan melakukan login portal.'); window.location='login.php';</script>";
                exit;
            } else {
                $pesan = "<div class='text-center text-xs font-bold text-red-500 font-mono pb-4 animate-pulse'>[SYSTEM ERROR] Gagal mendaftar ke database, silakan coba lagi.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun MY - aespa Concert 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Menggunakan Gambar Background JPG Lokal Sinkron dengan Login */
        body {
            background-image: linear-gradient(to bottom, rgba(2, 6, 23, 0.88) 0%, rgba(15, 23, 42, 0.95) 100%), url('../assets/image/aespa2026.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        /* Gaya Efek Kaca Premium Modis Melayang */
        .glass-card {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.75) 0%, rgba(30, 41, 59, 0.55) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4), 
                        inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        /* Animasi Mengambang Alami */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        /* Efek Denyut Pendaran Lampu Neon di Sudut Layar */
        @keyframes pulse-glow {
            0%, 100% { transform: scale(1); opacity: 0.2; }
            50% { transform: scale(1.1); opacity: 0.4; }
        }
        .glow-effect {
            animation: pulse-glow 5s ease-in-out infinite;
        }
    </style>
</head>
<body class="text-white font-sans min-h-screen flex items-center justify-center p-4 relative overflow-hidden select-none">

    <!-- Efek Lampu Aurora/Neon Tipis Bergerak Menyala di Pojok -->
    <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-purple-600/20 rounded-full blur-[120px] glow-effect pointer-events-none z-0"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-cyan-500/20 rounded-full blur-[120px] glow-effect pointer-events-none z-0"></div>

    <!-- Container Form Register Premium (Melayang Anggun & Efek Kaca) -->
    <div class="glass-card border border-slate-800/80 p-8 rounded-3xl w-full max-w-md shadow-2xl relative z-10 animate-float my-6">
        <div class="text-center mb-6">
            <span class="bg-purple-500/10 text-purple-400 border border-purple-500/20 text-[9px] px-2.5 py-1 rounded-md font-black tracking-widest uppercase">CREATE ACCOUNT</span>
            <h2 class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-purple-400 via-fuchsia-400 to-cyan-400 mt-2 tracking-tight uppercase">MY PORTAL</h2>
        </div>
        
        <!-- Wadah Notifikasi Pesan Sistem -->
        <?= $pesan; ?>

        <form action="" method="POST" class="space-y-4">
            <!-- Input Nama Lengkap -->
            <div class="space-y-1.5">
                <label class="block text-[10px] uppercase tracking-wider text-slate-400 font-mono font-bold">NAMA LENGKAP</label>
                <input type="text" name="nama" required placeholder="Rafa Mandala" class="w-full bg-slate-950/60 border border-slate-800 focus:border-purple-500/50 rounded-xl p-3 text-white text-sm focus:outline-none transition-all duration-300">
            </div>

            <!-- Input Email Address -->
            <div class="space-y-1.5">
                <label class="block text-[10px] uppercase tracking-wider text-slate-400 font-mono font-bold">EMAIL ADDRESS</label>
                <input type="email" name="email" required placeholder="name@example.com" class="w-full bg-slate-950/60 border border-slate-800 focus:border-purple-500/50 rounded-xl p-3 text-white text-sm focus:outline-none transition-all duration-300 font-mono">
            </div>

            <!-- Input Password -->
            <div class="space-y-1.5">
                <label class="block text-[10px] uppercase tracking-wider text-slate-400 font-mono font-bold">PASSWORD ACCESS</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-950/60 border border-slate-800 focus:border-purple-500/50 rounded-xl p-3 text-white text-sm focus:outline-none transition-all duration-300 font-mono">
            </div>

            <!-- ==========================================
                 FITUR BARU: CHECKBOX PERSYARATAN & KETENTUAN (ANTI-BOT & LEGAL)
                 ========================================== -->
            <div class="bg-slate-950/50 border border-slate-800/80 p-3.5 rounded-xl font-sans text-xs shadow-inner space-y-2">
                <!-- Teks Ringkasan Aturan Singkat -->
                <div class="text-[10px] text-slate-400 leading-relaxed max-h-16 overflow-y-auto pr-1 border-b border-slate-800 pb-2 font-mono">
                    Saya setuju bahwa 1 akun hanya dapat melakukan war maksimal 4 e-ticket resmi konser aespa 2026. Data identitas tidak dapat diubah setelah transaksi diverifikasi.
                </div>
                
                <!-- Label Elemen Checkbox -->
                <label class="flex items-start space-x-3 cursor-pointer group pt-1">
                    <input type="checkbox" name="setuju_syarat" value="yes" required class="w-4 h-4 mt-0.5 rounded text-purple-500 bg-slate-900 border-slate-700 focus:ring-0 cursor-pointer focus:ring-offset-0">
                    <span class="text-slate-300 group-hover:text-purple-400 transition select-none text-[11px] leading-tight font-semibold">
                        Saya menyetujui seluruh ketentuan War Room Portal ini.
                    </span>
                </label>
            </div>

            <!-- Tombol Submit Core Engine -->
            <div class="pt-2">
                <button type="submit" name="register" class="w-full bg-gradient-to-r from-purple-600 via-fuchsia-500 to-cyan-500 hover:brightness-110 text-white font-black tracking-widest py-3.5 rounded-xl text-xs uppercase transition shadow-lg shadow-purple-500/10 transform active:scale-95 duration-150">
                    CREATE ACCOUNT PASS
                </button>
            </div>
        </form>

        <!-- Link Navigasi Balik Ke Login -->
        <p class="text-center text-xs text-slate-400 mt-5 font-sans">
            Sudah punya akun? <a href="login.php" class="text-purple-400 hover:text-purple-300 font-bold hover:underline ml-1 transition">Login di sini</a>
        </p>
    </div>

</body>
</html>