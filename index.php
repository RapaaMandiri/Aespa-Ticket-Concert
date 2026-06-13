<?php
// index.php
require_once 'config/database.php';

// Ambil preview data tiket dari database untuk ditampilkan di landing page
$query_preview = mysqli_query($conn, "SELECT * FROM tiket ORDER BY harga DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>aespa Live Concert 2026 - SYNK : PARALLEL LINE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.15; }
            50% { opacity: 0.35; }
        }
        .glow-effect {
            animation: pulse-glow 4s infinite;
        }
        /* Animasi Teks Berjalan Sesuai Halaman Utama Dashboard */
        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .animate-marquee {
            animation: marquee 25s linear infinite;
        }
    </style>
</head>
<body class="text-white font-sans min-h-screen relative overflow-x-hidden bg-slate-950 select-none">

    <!-- ==========================================
         BACKGROUND VIDEO WITH BLUR EFFECT (DISAMAKAN)
         ========================================== -->
    <div class="fixed inset-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <video class="w-full h-full object-cover" autoplay loop muted playsinline poster="assets/image/aespa2026.jpg">
            <source src="assets/video/mv-aespa.mp4" type="video/mp4">
        </video>
        <!-- Lapisan kaca gelap pengaman ketajaman kontras teks -->
        <div class="absolute inset-0 bg-gradient-to-bottom from-slate-950/85 via-slate-950/80 to-slate-950/95 backdrop-blur-[4px]"></div>
    </div>

    <!-- Efek Lampu Aurora/Neon Tipis di Background -->
    <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[130px] glow-effect pointer-events-none z-0"></div>
    <div class="absolute bottom-[30%] right-[-10%] w-[600px] h-[600px] bg-cyan-600/10 rounded-full blur-[160px] glow-effect pointer-events-none z-0"></div>

    <!-- Wrapper Konten Utama (Berada di atas lapisan video background) -->
    <div class="relative z-10 flex flex-col min-h-screen justify-between">
        
        <!-- ==========================================
             NAVBAR PREMIUM LOOK
             ========================================== -->
        <nav class="bg-slate-950/60 backdrop-blur-md border-b border-slate-900/60 fixed top-0 w-full z-50 px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="text-xl font-black bg-clip-text text-transparent bg-gradient-to-r from-purple-400 via-fuchsia-400 to-cyan-400 tracking-widest uppercase">
                    <a href="index.php">AESPA SYNK 2026</a>
                </span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="admin/login.php" class="text-xs font-mono text-slate-400 hover:text-red-400 border border-transparent hover:border-red-500/30 hover:bg-red-500/10 px-3 py-1.5 rounded transition">Admin Portal</a>
                <a href="auth/login.php" class="bg-gradient-to-r from-purple-600 to-cyan-600 hover:from-purple-500 hover:to-cyan-500 text-white text-sm font-black px-6 py-2.5 rounded-full transition shadow-lg shadow-purple-500/30 transform hover:scale-105 duration-200">
                    LOG IN MY
                </a>
            </div>
        </nav>

        <!-- LIVE RUNNING TEXT ANNOUNCEMENT -->
        <div class="fixed top-[61px] left-0 w-full bg-purple-950/80 backdrop-blur-sm border-b border-purple-900/50 py-1.5 overflow-hidden z-40 text-[10px] font-mono tracking-wider text-cyan-300 flex items-center">
            <div class="whitespace-nowrap animate-marquee flex space-x-8">
                <span>🔥 ATTENTION: LIVE WAR TICKETING SEDANG BERLANGSUNG! AMANKAN SLOT KATEGORI ANDA SEBELUM RESERVASI EXPIRED.</span>
                <span>⚡ NOTICE: KLAIM EXCLUSIVE 3D PHOTOCARD HANYA TERSEDIA UNTUK PEMBELIAN KELAS GENERASI PERTAMA (GEN-1).</span>
                <span>📍 LOCATION VENUE UPDATE: ICE BSD HALL 5-6 JAKARTA INDONESIA.</span>
            </div>
        </div>

        <!-- ==========================================
             MAIN HERO HEADER SECTION
             ========================================== -->
        <header class="max-w-5xl mx-auto px-6 pt-56 pb-16 text-center">
            <span class="bg-purple-950/50 border border-purple-500/40 text-purple-300 text-[11px] px-4 py-1.5 rounded-full font-black tracking-widest uppercase mb-6 inline-block backdrop-blur-md">
                ⚡ JAKARTA OFFICIAL TICKETING PORTAL ⚡
            </span>
            
            <h1 class="text-5xl md:text-8xl font-black tracking-tighter uppercase mb-6 leading-none">
                SYNK : <br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-purple-400 via-fuchsia-500 to-cyan-400 animate-pulse">PARALLEL LINE</span>
            </h1>
            
            <p class="text-slate-300 text-base md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed drop-shadow-md">
                Rasakan dimensi baru musik masa depan bersama Karina, Giselle, Winter, dan Ningning. Amankan tiket resmi kamu melalui sistem enkripsi antrean *Real-Time War*.
            </p>

            <!-- Tombol Interaksi Utama -->
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <a href="auth/login.php" class="w-full sm:w-auto bg-gradient-to-r from-cyan-400 to-purple-500 hover:from-cyan-300 hover:to-purple-400 text-slate-950 font-black tracking-wider text-base px-10 py-4 rounded-full transition text-center shadow-xl shadow-cyan-500/20">
                    🎟️ MASUK & IKUT WAR TIKET
                </a>
                <a href="auth/register.php" class="w-full sm:w-auto bg-slate-900/80 border border-slate-800 hover:border-slate-700 font-bold text-base px-8 py-4 rounded-full transition text-center backdrop-blur-sm">
                    Belum Punya Akun? Daftar
                </a>
            </div>
        </header>

        <!-- Section Daftar Harga Preview -->
        <section class="max-w-6xl mx-auto px-6 pb-20 w-full">
            <div class="text-center mb-10">
                <h2 class="text-xs uppercase tracking-widest font-black text-slate-500 mb-2">Available Class & Pricing</h2>
                <p class="text-sm text-slate-400">Termasuk Kuota Bonus K-Pop 3D Hologram Photocard</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php if (mysqli_num_rows($query_preview) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($query_preview)): ?>
                        <!-- Menggunakan style glass panel transparan agar video tembus anggun -->
                        <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/40 hover:border-purple-500/40 p-6 rounded-2xl transition duration-300 shadow-xl">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="font-bold text-base text-white truncate w-40"><?= htmlspecialchars($row['kategori']) ?></h3>
                                <span class="text-[9px] bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 px-2 py-0.5 rounded font-mono font-bold">READY</span>
                            </div>
                            <div class="text-xl font-mono font-bold text-purple-400 mb-3">
                                Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                            </div>
                            <p class="text-[11px] text-slate-400 leading-relaxed border-t border-slate-800/60 pt-2.5">
                                <?= htmlspecialchars($row['benefit']) ?>
                            </p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center py-8 text-slate-500 border border-dashed border-slate-800 rounded-2xl bg-slate-900/20">
                        Belum ada kategori tiket aktif yang di-input oleh admin.
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- ==========================================
             PREMIUM FOOTER + BARIS SPONSOR INTERAKTIF BERWARNA
             ========================================== -->
        <footer class="bg-slate-950/90 backdrop-blur-md border-t border-slate-900 pt-12 pb-6 px-6 relative z-20">
            <div class="max-w-6xl mx-auto">
                
                <div class="text-center mb-10">
                    <p class="text-[10px] tracking-widest font-bold uppercase text-slate-500 mb-6">OFFICIAL SPONSORS & MEDIA PARTNERS</p>
                    
                    <div class="flex flex-wrap justify-center items-center gap-8 md:gap-14">
                        
                        <img src="assets/image/sm.png" 
                             alt="SM Entertainment" 
                             class="h-8 md:h-10 object-contain brightness-0 invert opacity-60 hover:opacity-100 hover:brightness-100 hover:invert-0 active:scale-95 transition-all duration-300 cursor-pointer">

                        <img src="assets/image/bca.png" 
                             alt="Bank Central Asia" 
                             class="h-6 md:h-8 object-contain brightness-0 invert opacity-60 hover:opacity-100 hover:brightness-100 hover:invert-0 active:scale-95 transition-all duration-300 cursor-pointer">

                        <img src="assets/image/KbBank.png" 
                             alt="KB Bank" 
                             class="h-6 md:h-8 object-contain brightness-0 invert opacity-60 hover:opacity-100 hover:brightness-100 hover:invert-0 active:scale-95 transition-all duration-300 cursor-pointer">

                        <img src="assets/image/mandiri.png" 
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