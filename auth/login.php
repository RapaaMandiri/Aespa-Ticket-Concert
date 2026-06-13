<?php
// auth/login.php
require_once '../config/database.php';

// Pastikan session start aktif agar data login tersimpan di browser
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika user ternyata sudah login sebelumnya, langsung lempar ke dashboard
if (isset($_SESSION['login_user']) && $_SESSION['login_user'] === true) {
    header("Location: ../user/dashboard.php");
    exit;
}

$pesan = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Validasi email dan pastikan role-nya adalah 'user'
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND role = 'user'");
    
    if (mysqli_num_rows($query) === 1) {
        $row = mysqli_fetch_assoc($query);
        // Cek kecocokan password terenkripsi
        if (password_verify($password, $row['password'])) {
            // Set data session user pembeli
            $_SESSION['login_user'] = true;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_nama'] = $row['nama'];
            
            header("Location: ../user/dashboard.php");
            exit;
        }
    }
    // Pesan eror tema siber premium jika salah kredensial
    $pesan = "<div class='text-center text-xs font-bold text-red-500 font-mono pb-4 animate-pulse'>[DENIED] Kredensial MY Portal Salah atau Bukan Akun Pembeli!</div>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login MY Portal - aespa Concert 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Menggunakan Gambar Background JPG Lokal */
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
        /* Animasi Mengambang Biar Kotak Hidup */
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
    <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-cyan-500/20 rounded-full blur-[120px] glow-effect pointer-events-none z-0"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-purple-600/20 rounded-full blur-[120px] glow-effect pointer-events-none z-0"></div>

    <!-- Container Form Login Premium (Melayang Anggun) -->
    <div class="glass-card border border-slate-800/80 p-8 rounded-3xl w-full max-w-md shadow-2xl relative z-10 animate-float">
        <div class="text-center mb-6">
            <span class="bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 text-[9px] px-2.5 py-1 rounded-md font-black tracking-widest uppercase">USER SIGN-IN</span>
            <h2 class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 via-fuchsia-400 to-purple-400 mt-2 tracking-tight uppercase">MY PORTAL</h2>
        </div>
        
        <!-- Tempat Munculnya Pesan Eror -->
        <?= $pesan; ?>

        <form action="" method="POST" class="space-y-4">
            <!-- Input Email -->
            <div class="space-y-1.5">
                <label class="block text-[10px] uppercase tracking-wider text-slate-400 font-mono font-bold">EMAIL ADDRESS</label>
                <input type="email" name="email" required placeholder="name@example.com" class="w-full bg-slate-950/60 border border-slate-800 focus:border-cyan-500/50 rounded-xl p-3 text-white text-sm focus:outline-none transition-all duration-300 font-mono">
            </div>

            <!-- Input Password (Dengan Fitur Lihat Sandi) -->
            <div class="space-y-1.5">
                <label class="block text-[10px] uppercase tracking-wider text-slate-400 font-mono font-bold">PASSWORD ACCESS</label>
                <div class="relative">
                    <input type="password" id="password-field" name="password" required placeholder="••••••••" class="w-full bg-slate-950/60 border border-slate-800 focus:border-cyan-500/50 rounded-xl pl-3 pr-10 p-3 text-white text-sm focus:outline-none transition-all duration-300 font-mono">
                    <!-- Tombol Toggle Mata -->
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-cyan-400 transition">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tombol Submit Core Engine -->
            <div class="pt-2">
                <button type="submit" name="login" class="w-full bg-gradient-to-r from-cyan-500 via-fuchsia-500 to-purple-600 hover:brightness-110 text-slate-950 font-black tracking-widest py-3.5 rounded-xl text-xs uppercase transition shadow-lg shadow-cyan-500/10 transform active:scale-95 duration-150">
                    ENTER TRANSMISSION PORTAL
                </button>
            </div>
        </form>

        <!-- Link Registrasi Bawah -->
        <p class="text-center text-xs text-slate-400 mt-5 font-sans">
            Belum mendaftar? <a href="register.php" class="text-cyan-400 hover:text-cyan-300 font-bold hover:underline ml-1 transition">Buat akun MY</a>
        </p>
    </div>

    <!-- Script JavaScript Pengendali Show/Hide Password -->
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password-field');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.822 7.822 3 3m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                `;
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                `;
            }
        }
    </script>

</body>
</html>