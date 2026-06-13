<?php
// user/dashboard.php
require_once '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi Halaman: Cek apakah pembeli sudah login
if (!isset($_SESSION['login_user']) || $_SESSION['login_user'] !== true) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 1. Ambil data user terupdate untuk komponen foto profil di Navbar
$query_navbar = mysqli_query($conn, "SELECT nama, foto_profil FROM users WHERE id = $user_id");
$user_nav = mysqli_fetch_assoc($query_navbar);

// 2. Ambil data tiket resmi dari database
$query_tiket = mysqli_query($conn, "SELECT * FROM tiket ORDER BY harga DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY Dashboard - aespa Concert 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Gaya Efek Kaca Premium Transparan Modis untuk Panel di atas Video */
        .glass-panel {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.65) 0%, rgba(30, 41, 59, 0.45) 100%);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }
        /* Animasi Teks Berjalan Khas Portal Tiket Ramai */
        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .animate-marquee {
            animation: marquee 25s linear infinite;
        }
    </style>
</head>
<body class="text-slate-100 font-sans min-h-screen flex flex-col justify-between relative bg-slate-950 select-none">

    <div class="fixed inset-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <video class="w-full h-full object-cover" autoplay loop muted playsinline poster="../assets/image/aespa2026.jpg">
            <source src="../assets/video/mv-aespa.mp4" type="video/mp4">
        </video>
        <div class="absolute inset-0 bg-gradient-to-bottom from-slate-950/85 via-slate-950/80 to-slate-950/95 backdrop-blur-[4px]"></div>
    </div>

    <div class="relative z-10 w-full flex flex-col min-h-screen justify-between">
        
        <nav class="bg-slate-950/60 backdrop-blur-md border-b border-slate-900/60 fixed top-0 w-full z-50 px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="text-xl font-black bg-clip-text text-transparent bg-gradient-to-r from-purple-400 via-fuchsia-400 to-cyan-400 tracking-widest uppercase">
                    <a href="dashboard.php">AESPA SYNK 2026</a>
                </span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="tiket-saya.php" class="text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-white transition">Tiket Saya</a>
                
                <a href="profile.php" class="flex items-center space-x-2 group border border-slate-800 bg-slate-950/40 py-1 pl-3 pr-1 rounded-full hover:border-purple-500/40 transition duration-300">
                    <span class="text-xs font-semibold text-slate-300 group-hover:text-purple-400 transition"><?= htmlspecialchars($user_nav['nama']); ?></span>
                    <div class="w-7 h-7 rounded-full overflow-hidden bg-purple-600/20 flex items-center justify-center border border-purple-500/40">
                        <?php if ($user_nav['foto_profil'] === 'default-pp.png'): ?>
                            <span class="text-xs font-bold text-purple-400"><?= strtoupper(substr($user_nav['nama'], 0, 1)); ?></span>
                        <?php else: ?>
                            <img src="../assets/uploads/profile/<?= $user_nav['foto_profil']; ?>" alt="Avatar" class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                </a>

                <a href="../auth/logout.php" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 px-3 py-1.5 rounded-lg text-xs font-mono transition">LOGOUT</a>
            </div>
        </nav>

        <div class="fixed top-[61px] left-0 w-full bg-purple-950/80 backdrop-blur-sm border-b border-purple-900/50 py-1.5 overflow-hidden z-40 text-[10px] font-mono tracking-wider text-cyan-300 flex items-center">
            <div class="whitespace-nowrap animate-marquee flex space-x-8">
                <span>🔥 ATTENTION: LIVE WAR TICKETING SEDANG BERLANGSUNG! AMANKAN SLOT KATEGORI ANDA SEBELUM RESERVASI EXPIRED.</span>
                <span>⚡ NOTICE: KLAIM EXCLUSIVE 3D PHOTOCARD HANYA TERSEDIA UNTUK PEMBELIAN KELAS GENERASI PERTAMA (GEN-1).</span>
                <span>📍 LOCATION VENUE UPDATE: ICE BSD HALL 5-6 JAKARTA INDONESIA.</span>
            </div>
        </div>

        <div class="pt-56 pb-12 px-6 max-w-5xl mx-auto w-full">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-8 items-center bg-slate-900/40 border border-slate-800/60 p-8 rounded-3xl backdrop-blur-md shadow-2xl">
                
                <div class="md:col-span-3 text-left space-y-4">
                    <div class="inline-block bg-purple-500/20 border border-purple-500/30 rounded-md px-3 py-1 text-[10px] font-black text-purple-300 tracking-widest uppercase">
                        🚨 LIVE TICKETING PORTAL ACTIVE
                    </div>
                    <h1 class="text-3xl md:text-5xl font-black tracking-tight uppercase leading-none text-white">
                        aespa Live Concert <br>
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 via-fuchsia-500 to-purple-500">[SYNK : PARALLEL LINE]</span>
                    </h1>
                    <p class="text-slate-300 text-xs md:text-sm leading-relaxed">
                        Amankan tiket resmi konser megah aespa langsung untuk lokasi pertunjukan **ICE BSD Hall 5-6**. Klik tombol pemicu di bawah untuk masuk ke ruang simulasi antrean pesanan.
                    </p>

                    <div class="pt-2">
                        <a href="waiting-room.php" class="inline-block bg-gradient-to-r from-purple-600 via-fuchsia-600 to-cyan-500 hover:brightness-110 text-white font-black tracking-widest text-xs py-4 px-8 rounded-xl shadow-lg shadow-purple-600/20 transition transform hover:scale-102 duration-200 uppercase">
                            🔥 MULAI WAR TIKET SEKARANG
                        </a>
                    </div>
                </div>

                <div class="md:col-span-2 flex justify-center">
                    <div class="w-full max-w-[280px] rounded-2xl overflow-hidden border-2 border-slate-800 shadow-2xl shadow-purple-500/10 transform rotate-1 hover:rotate-0 transition duration-300">
                        <img src="../assets/image/aespa2026.jpg" alt="aespa 2026 Concert Poster" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 max-w-5xl w-full mx-auto pb-12 relative z-10">
            <div class="glass-panel border border-slate-800/60 p-6 rounded-2xl grid grid-cols-1 md:grid-cols-3 gap-6 text-xs font-mono">
                <div class="space-y-1">
                    <span class="text-purple-400 block font-bold uppercase tracking-wider text-[10px]">📅 DATE & TIME</span>
                    <p class="text-white text-sm font-black">2026 TOUR EDITION</p>
                    <p class="text-slate-400 text-[11px]">Gate Open: 15:00 WIB<br>Show Time: 19:00 WIB</p>
                </div>
                <div class="space-y-1 md:border-x md:border-slate-800/80 md:px-6">
                    <span class="text-cyan-400 block font-bold uppercase tracking-wider text-[10px]">📍 LOCATION VENUE</span>
                    <p class="text-white text-sm font-black">ICE BSD - HALL 5-6</p>
                    <p class="text-slate-400 text-[11px]">Tangerang, Banten, Greater Jakarta Area Region.</p>
                </div>
                <div class="space-y-1">
                    <span class="text-fuchsia-400 block font-bold uppercase tracking-wider text-[10px]">✨ SPECIAL COMPONENT</span>
                    <p class="text-white text-sm font-black">3D HOLOGRAM PASS</p>
                    <p class="text-slate-400 text-[11px]">Setiap tiket sukses mendapatkan benefit klaim fisik merchandise reward.</p>
                </div>
            </div>
        </div>
            
        <div class="px-6 max-w-5xl w-full mx-auto pb-24 relative z-10">
            <h2 class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-6 border-b border-slate-800 pb-3">
                Live Stock Status Plan & Benefit
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php if ($query_tiket && mysqli_num_rows($query_tiket) > 0): ?>
                    <?php while($tiket = mysqli_fetch_assoc($query_tiket)): ?>
                        
                        <div class="glass-panel border border-slate-800/60 hover:border-purple-500/40 p-6 rounded-2xl transition duration-300 flex flex-col justify-between group shadow-2xl relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-12 h-12 bg-gradient-to-bl from-white/5 to-transparent pointer-events-none"></div>
                            
                            <div>
                                <div class="flex justify-between items-start mb-4 gap-2">
                                    <h3 class="font-black text-sm text-white group-hover:text-cyan-400 transition truncate max-w-[150px]" title="<?= htmlspecialchars($tiket['kategori']); ?>">
                                        <?= htmlspecialchars($tiket['kategori']); ?>
                                    </h3>
                                    <span class="bg-slate-950/80 text-slate-300 border border-slate-800 text-[9px] px-2 py-0.5 rounded font-mono font-bold">
                                        Stok: <?= $tiket['stok']; ?> Pcs
                                    </span>
                                </div>
                                <p class="text-xl font-bold font-mono text-cyan-400 mb-3">
                                    Rp <?= number_format($tiket['harga'], 0, ',', '.'); ?>
                                </p>
                                <div class="text-slate-300 text-[11px] leading-relaxed border-t border-slate-800/60 pt-3">
                                    <strong class="text-slate-400 block mb-1 uppercase tracking-wider text-[9px]">Perks Benefit:</strong>
                                    <?= htmlspecialchars($tiket['benefit']); ?>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <span class="block w-full text-center text-[9px] bg-slate-950/80 text-purple-400 border border-purple-500/20 py-2.5 rounded-xl font-mono tracking-widest uppercase font-bold">
                                    SECURED IN WAR ROOM
                                </span>
                            </div>
                        </div>

                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <footer class="bg-slate-950/90 backdrop-blur-md border-t border-slate-900 pt-12 pb-6 px-6 relative z-20">
            <div class="max-w-6xl mx-auto">
                
                <div class="text-center mb-10">
                    <p class="text-[10px] tracking-widest font-bold uppercase text-slate-500 mb-6">OFFICIAL SPONSORS & MEDIA PARTNERS</p>
                    
                    <div class="flex flex-wrap justify-center items-center gap-8 md:gap-14">
                        
                        <img src="../assets/image/sm.png" 
                             alt="SM Entertainment" 
                             class="h-8 md:h-10 object-contain brightness-0 invert opacity-60 hover:opacity-100 hover:brightness-100 hover:invert-0 active:scale-95 transition-all duration-300 cursor-pointer">

                        <img src="../assets/image/bca.png" 
                             alt="Bank Central Asia" 
                             class="h-6 md:h-8 object-contain brightness-0 invert opacity-60 hover:opacity-100 hover:brightness-100 hover:invert-0 active:scale-95 transition-all duration-300 cursor-pointer">

                        <img src="../assets/image/KbBank.png" 
                             alt="KB Bank" 
                             class="h-6 md:h-8 object-contain brightness-0 invert opacity-60 hover:opacity-100 hover:brightness-100 hover:invert-0 active:scale-95 transition-all duration-300 cursor-pointer">

                        <img src="../assets/image/mandiri.png" 
                             alt="Mandiri" 
                             class="h-5 md:h-7 object-contain brightness-0 invert opacity-60 hover:opacity-100 hover:brightness-100 hover:invert-0 active:scale-95 transition-all duration-300 cursor-pointer">
                             
                    </div>
                </div>

                <hr class="border-slate-900 my-6">

                <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-[11px] text-slate-500">
                    <div>
                        <p class="font-bold text-slate-400">aespa SYNK Parallel Line 2026 Ticketing Platform</p>
                        <p class="mt-0.5 text-slate-600">Sistem terenkripsi anti-bot untuk kenyamanan berburu e-ticket konser.</p>
                    </div>
                    <div class="text-center md:text-right font-mono text-[10px]">
                        <p>&copy; 2026 SYNK PORTAL GLOBAL. ALL RIGHTS RESERVED.</p>
                        <p class="text-purple-500/60 mt-0.5">Vibecoding Core Execution Engine // PHP Native 8</p>
                    </div>
                </div>

            </div>
        </footer>

    </div>

</body>
</html>