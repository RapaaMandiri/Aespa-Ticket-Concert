<?php
// user/tiket-saya.php
require_once '../config/database.php';

global $conn;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi Halaman: Cek apakah user sudah login
if (!isset($_SESSION['login_user']) || $_SESSION['login_user'] !== true) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data transaksi sukses milik user ini beserta detail kategori tiketnya
$query_tiket = mysqli_query($conn, "SELECT t.*, tr.kode_transaksi, tr.jumlah_tiket, tr.total_bayar, tr.photocard_status, tr.created_at 
                                    FROM transaksi tr 
                                    JOIN tiket t ON tr.tiket_id = t.id 
                                    WHERE tr.user_id = $user_id AND tr.status_pembayaran = 'sukses'
                                    ORDER BY tr.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket & Multi Photocard - aespa 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass-card {
            background: linear-gradient(135deg, rgba(13, 20, 38, 0.8) 0%, rgba(20, 30, 54, 0.6) 100%);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .card-container {
            perspective: 1200px;
        }
        .card-3d {
            transition: transform 0.5s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.5s ease;
            transform-style: preserve-3d;
        }
        .card-container:hover .card-3d {
            transform: rotateY(12deg) rotateX(8deg) scale(1.04);
            box-shadow: 0 15px 30px -10px rgba(168, 85, 247, 0.5), 
                        0 0 20px 2px rgba(34, 211, 238, 0.25);
        }
        .card-3d::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0) 100%);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
            z-index: 5;
        }
        .card-container:hover .card-3d::before {
            transform: translateX(100%);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen pt-28 pb-16 px-4 md:px-8 relative overflow-x-hidden select-none">

    <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[130px] pointer-events-none z-0"></div>
    <div class="absolute bottom-[5%] right-[-10%] w-[500px] h-[500px] bg-cyan-600/10 rounded-full blur-[130px] pointer-events-none z-0"></div>

    <nav class="bg-slate-950/60 backdrop-blur-md border-b border-slate-900 fixed top-0 w-full z-50 px-6 py-4 flex justify-between items-center">
        <div class="text-xl font-black bg-clip-text text-transparent bg-gradient-to-r from-purple-400 via-fuchsia-400 to-cyan-400 tracking-widest uppercase">
            <a href="dashboard.php">AESPA SYNK 2026</a>
        </div>
        <div class="flex items-center space-x-6 text-xs font-bold uppercase tracking-wider">
            <a href="dashboard.php" class="text-slate-400 hover:text-white transition">← Dashboard</a>
            <a href="../auth/logout.php" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 px-3 py-1.5 rounded-lg font-mono transition">LOGOUT</a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto relative z-10">
        <div class="mb-8">
            <span class="text-[9px] font-black tracking-widest text-purple-400 uppercase bg-purple-500/10 border border-purple-500/20 px-2.5 py-1 rounded-md">VIRTUAL PASS</span>
            <h1 class="text-3xl font-black mt-3 tracking-tight uppercase">INVENTORY TIKET KAMU</h1>
            <p class="text-xs text-slate-400 mt-1">Gunakan kode booking resmi di bawah untuk ditukarkan dengan wristband fisik di venue.</p>
        </div>

        <?php if (mysqli_num_rows($query_tiket) > 0): ?>
            <div class="space-y-8">
                <?php while($row = mysqli_fetch_assoc($query_tiket)): ?>
                    
                    <div class="glass-card border border-slate-800/80 rounded-3xl grid grid-cols-1 md:grid-cols-3 shadow-2xl relative overflow-hidden">
                        
                        <div class="hidden md:block absolute left-[-12px] top-[50%] -translate-y-1/2 w-6 h-6 bg-slate-950 rounded-full border border-slate-800 z-20"></div>

                        <div class="p-8 md:col-span-2 flex flex-col justify-between space-y-6">
                            <div class="flex flex-wrap justify-between items-center gap-3">
                                <span class="bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 px-3 py-1 rounded-md text-[10px] font-mono font-bold tracking-wider uppercase">
                                    ✓ SECURE PASSED / E-TICKET
                                </span>
                                <span class="text-[11px] text-slate-500 font-mono"><?= $row['created_at']; ?></span>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                                <div class="space-y-2">
                                    <h3 class="text-3xl font-black tracking-tight text-white uppercase bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-300">
                                        <?= htmlspecialchars($row['kategori']); ?>
                                    </h3>
                                    <p class="text-xs text-slate-400 font-mono uppercase tracking-wide">
                                        Booking Code: <span class="text-purple-400 font-bold bg-purple-500/5 border border-purple-500/10 px-2 py-0.5 rounded ml-1 font-sans text-sm"><?= $row['kode_transaksi']; ?></span>
                                    </p>
                                </div>

                                <div class="bg-white p-2 rounded-xl border-2 border-cyan-500/30 shadow-lg shadow-cyan-500/5 self-center sm:self-auto flex flex-col items-center space-y-1">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=95x95&data=VALIDATION-PASS-<?= $row['kode_transaksi']; ?>" 
                                         alt="Gate Barcode" 
                                         class="w-24 h-24 object-contain">
                                    <span class="text-[8px] font-mono font-bold text-slate-700 tracking-widest">SCAN ACCESS</span>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4 bg-slate-950/60 p-4 rounded-xl border border-slate-900/60 font-mono text-xs">
                                    <div>
                                        <span class="text-slate-500 block mb-1 uppercase tracking-wider font-bold text-[9px]">Kuantitas</span>
                                        <span class="text-sm font-bold text-slate-200"><?= $row['jumlah_tiket']; ?> Tiket Pas</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 block mb-1 uppercase tracking-wider font-bold text-[9px]">Venue Location</span>
                                        <span class="text-sm font-bold text-cyan-400">ICE BSD, Hall 5-6</span>
                                    </div>
                                </div>
                                
                                <div class="border border-slate-900 bg-slate-950/30 p-3.5 rounded-xl text-[10px] font-mono text-slate-400 space-y-1.5 leading-relaxed">
                                    <p class="text-cyan-400 font-black tracking-wider text-[11px] uppercase">📋 INFORMASI & REGULASI MASUK:</p>
                                    <p>• Pintu gerbang utama (*Main Gate*) dibuka mulai pukul **15:00 WIB**.</p>
                                    <p>• Kuota bonus item photocard disesuaikan otomatis dengan jumlah total kuantitas pembelian tiket anda.</p>
                                    <p>• Penukaran gelang fisik (*Wristband*) wajib menyertakan KTP asli pembeli sesuai nama akun terdaftar.</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative flex flex-row md:flex-col items-center justify-between my-2 md:my-0">
                            <div class="absolute top-[-12px] left-[50%] -translate-x-1/2 w-6 h-6 bg-slate-950 rounded-full border border-slate-800 z-20 hidden md:block"></div>
                            <div class="absolute bottom-[-12px] left-[50%] -translate-x-1/2 w-6 h-6 bg-slate-950 rounded-full border border-slate-800 z-20 hidden md:block"></div>
                            
                            <div class="w-full md:w-0 h-0 md:h-full border-t-2 md:border-l-2 border-dashed border-slate-800/80 mx-4 md:mx-auto"></div>
                        </div>

                        <div class="p-6 md:col-span-1 bg-slate-950/40 flex flex-col items-center justify-center space-y-4">
                            
                            <div class="w-full grid gap-3 <?= ($row['jumlah_tiket'] > 1) ? 'grid-cols-2 md:grid-cols-2' : 'grid-cols-1' ?> justify-items-center">
                                
                                <?php 
                                // Master data array member aespa
                                $list_member = [
                                    ['nama' => 'KARINA', 'file' => 'karina.jpg', 'color' => 'from-blue-600/60'],
                                    ['nama' => 'WINTER', 'file' => 'winter.jpg', 'color' => 'from-slate-200/20'],
                                    ['nama' => 'GISELLE', 'file' => 'gisele.jpg', 'color' => 'from-pink-600/60'],
                                    ['nama' => 'NINGNING', 'file' => 'ningning.jpg', 'color' => 'from-purple-600/60']
                                ];
                                
                                // Mengunci seed acakan konstan berdasarkan kode_transaksi
                                $kode_unik = $row['kode_transaksi'];
                                srand(crc32($kode_unik));
                                shuffle($list_member);
                                
                                // Tentukan batas looping gacha sesuai jumlah tiket yang dibeli (Maksimal 4)
                                $jumlah_loop = min($row['jumlah_tiket'], 4);
                                
                                for ($i = 0; $i < $jumlah_loop; $i++): 
                                    $member_terpilled = $list_member[$i];
                                ?>
                                    <div class="card-container relative cursor-pointer <?= ($row['jumlah_tiket'] > 1) ? 'w-24 h-36' : 'w-36 h-52' ?>" title="Bonus Photocard <?= $member_terpilled['nama']; ?>!">
                                        <div class="card-3d w-full h-full bg-gradient-to-br <?= $member_terpilled['color']; ?> via-slate-900 to-slate-950 rounded-xl border border-white/10 flex flex-col justify-between relative overflow-hidden shadow-xl">
                                            
                                            <div class="absolute inset-0 w-full h-full z-0 opacity-80">
                                                <img src="../assets/image/<?= $member_terpilled['file']; ?>" alt="<?= $member_terpilled['nama']; ?>" class="w-full h-full object-cover">
                                            </div>

                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-slate-950/40 z-10"></div>

                                            <div class="flex justify-between items-center relative z-20 p-2">
                                                <span class="text-[7px] font-black tracking-widest text-cyan-300 font-mono">aespa</span>
                                                <span class="text-[6px] bg-purple-500/40 backdrop-blur-xs px-1 py-0.5 rounded text-white font-bold">3D PC</span>
                                            </div>

                                            <div class="text-center my-auto relative z-20"></div>

                                            <div class="flex flex-col relative z-20 p-2 space-y-0.5">
                                                <span class="font-black tracking-wider text-white font-sans text-center uppercase bg-slate-950/70 py-0.5 rounded border border-white/5 <?= ($row['jumlah_tiket'] > 1) ? 'text-[7px]' : 'text-[11px]' ?>">
                                                    <?= $member_terpilled['nama']; ?>
                                                </span>
                                                <div class="flex justify-between items-center border-t border-white/10 pt-1 text-[5px] font-mono text-slate-400">
                                                    <span>SYNK PASS</span>
                                                    <span class="text-purple-400 font-bold">GEN-1</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>

                            <div class="w-full text-center">
                                <?php if($row['photocard_status'] === 'belum_diambil'): ?>
                                    <div class="text-[9px] font-black bg-amber-500/10 text-amber-400 border border-amber-500/20 py-1.5 px-3 rounded-xl uppercase tracking-wider animate-pulse inline-block w-full">
                                        🎁 Ready at Booth (<?= $row['jumlah_tiket']; ?> PC)
                                    </div>
                                <?php else: ?>
                                    <div class="text-[9px] font-black bg-green-500/20 text-green-400 border border-green-500/30 py-1.5 px-3 rounded-xl uppercase tracking-wider inline-block w-full">
                                        ✓ CLAIMED
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>

                    </div>

                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="glass-card border border-dashed border-slate-800 text-center p-16 rounded-3xl text-slate-500 font-mono text-sm">
                Kamu belum memilik e-ticket terdaftar di sistem.
            </div>
        <?php endif; ?>
    </div>

    <footer class="mt-24 text-center text-[10px] text-slate-700 font-mono tracking-widest">
        SECURE PASS INTERFACE // ENCRYPTED TRANSACTION TRANSMISSION
    </footer>

</body>
</html>