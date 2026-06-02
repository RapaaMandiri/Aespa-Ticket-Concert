<?php
// admin/index.php
require_once '../config/database.php';

global $conn;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi Halaman Admin: Cek apakah yang login adalah admin
// (Silakan sesuaikan variabel session admin milikmu jika berbeda)
if (!isset($_SESSION['login_admin']) || $_SESSION['login_admin'] !== true) {
    // Fail-safe jika kamu pakai satu session login: cek level/role user
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        echo "<script>alert('Akses ditolak! Halaman ini khusus Administrator.'); window.location='../auth/login.php';</script>";
        exit;
    }
}

// 1. HITUNG STATISTIK UTAMA (Read Data Agregat)
// Total Pendapatan Real-time dari Transaksi Sukses
$query_pendapatan = mysqli_query($conn, "SELECT SUM(total_bayar) AS total FROM transaksi WHERE status_pembayaran = 'sukses'");
$data_pendapatan = mysqli_fetch_assoc($query_pendapatan);
$total_gross = $data_pendapatan['total'] ?? 0;

// Total Tiket Terjual (Kuantitas)
$query_terjual = mysqli_query($conn, "SELECT SUM(jumlah_tiket) AS total_qty FROM transaksi WHERE status_pembayaran = 'sukses'");
$data_terjual = mysqli_fetch_assoc($query_terjual);
$total_tiket_terjual = $data_terjual['total_qty'] ?? 0;

// Total User yang Terdaftar di Sistem
$query_users = mysqli_query($conn, "SELECT COUNT(id) AS total_usr FROM users WHERE role = 'user'");
$data_users = mysqli_fetch_assoc($query_users);
$total_pembeli = $data_users['total_usr'] ?? 0;

// 2. AMBIL DATA MONITOR ANTREAN AKTIF
$query_antrean = mysqli_query($conn, "SELECT status_antrean, COUNT(*) as jumlah FROM antrean GROUP BY status_antrean");
$antrean_stats = ['mengantre' => 0, 'lolos' => 0, 'expired' => 0];
while($row = mysqli_fetch_assoc($query_antrean)) {
    $antrean_stats[$row['status_antrean']] = $row['jumlah'];
}

// 3. AMBIL LOG TRANSAKSI TERBARU (JOIN DATA)
$query_log_transaksi = mysqli_query($conn, "SELECT tr.*, u.nama, t.kategori 
                                           FROM transaksi tr
                                           JOIN users u ON tr.user_id = u.id
                                           JOIN tiket t ON tr.tiket_id = t.id
                                           ORDER BY tr.id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔒 Admin Control Center - aespa Ticket 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass-panel {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.7) 0%, rgba(30, 41, 59, 0.5) 100%);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 font-sans min-h-screen pt-24 pb-12 px-4 md:px-8 relative overflow-x-hidden">

    <!-- Ornamen Pencahayaan Background Khas Core SYNK -->
    <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-fuchsia-900/10 rounded-full blur-[130px] pointer-events-none z-0"></div>
    <div class="absolute bottom-[5%] left-[-5%] w-[500px] h-[500px] bg-purple-900/10 rounded-full blur-[130px] pointer-events-none z-0"></div>

    <!-- Navigation Bar Admin -->
    <nav class="bg-slate-950/60 backdrop-blur-md border-b border-slate-900 fixed top-0 w-full z-50 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <span class="bg-red-500/10 text-red-400 border border-red-500/20 text-[9px] font-black tracking-widest px-2 py-0.5 rounded-md uppercase">SYSTEM ROOT</span>
            <span class="text-lg font-black tracking-wider text-white">AESPA CONTROL DASHBOARD</span>
        </div>
        <div class="flex items-center space-x-6 text-xs font-bold uppercase tracking-wider">
            <span class="text-slate-400">Status: <strong class="text-emerald-400 font-mono">ONLINE</strong></span>
            <a href="../auth/logout.php" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 px-3 py-1.5 rounded-lg font-mono transition">LOGOUT ADMIN</a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto relative z-10 space-y-8">
        
        <!-- Header Judul -->
        <div>
            <h1 class="text-3xl font-black tracking-tight uppercase">SYSTEM MANAGEMENT INDEX</h1>
            <p class="text-xs text-slate-400 mt-1">Pantau pergerakan antrean war room, rekap keuangan, dan validasi tiket masuk secara real-time.</p>
        </div>

        <!-- ==========================================
             BARIS KARTU KILAS STATISTIK UTAMA
             ========================================== -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Card 1: Total Omzet Pendapatan -->
            <div class="glass-panel border border-slate-800 p-6 rounded-2xl shadow-xl flex flex-col justify-between">
                <div>
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Gross Revenue</span>
                    <h3 class="text-2xl font-black font-mono text-green-400">
                        Rp <?= number_format($total_gross, 0, ',', '.'); ?>
                    </h3>
                </div>
                <div class="text-[10px] text-slate-400 mt-4 border-t border-slate-900 pt-2 font-mono">
                    Berdasarkan akumulasi pembayaran sukses
                </div>
            </div>

            <!-- Card 2: Tiket Laku -->
            <div class="glass-panel border border-slate-800 p-6 rounded-2xl shadow-xl flex flex-col justify-between">
                <div>
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Tickets Issued</span>
                    <h3 class="text-2xl font-black font-mono text-cyan-400">
                        <?= $total_tiket_terjual; ?> <span class="text-xs font-sans text-slate-400 font-normal">Pcs Terjual</span>
                    </h3>
                </div>
                <div class="text-[10px] text-slate-400 mt-4 border-t border-slate-900 pt-2 font-mono">
                    Total alokasi kursi terjual di database
                </div>
            </div>

            <!-- Card 3: Jumlah Pengguna Aktif -->
            <div class="glass-panel border border-slate-800 p-6 rounded-2xl shadow-xl flex flex-col justify-between">
                <div>
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Registered Accounts</span>
                    <h3 class="text-2xl font-black font-mono text-purple-400">
                        <?= $total_pembeli; ?> <span class="text-xs font-sans text-slate-400 font-normal">Akun Pembeli</span>
                    </h3>
                </div>
                <div class="text-[10px] text-slate-400 mt-4 border-t border-slate-900 pt-2 font-mono">
                    User terdaftar di luar level administrator
                </div>
            </div>

        </div>

        <!-- ==========================================
             MONITORING LIVE TRAFFIC ANTREAN WAR
             ========================================== -->
        <div class="glass-panel border border-slate-800/80 p-6 rounded-3xl shadow-xl">
            <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400 border-b border-slate-900 pb-3 mb-4">
                📊 LIVE TRAFFIC CONTROL (WAITING ROOM MONITOR)
            </h2>
            <div class="grid grid-cols-3 gap-4 text-center font-mono text-xs">
                <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-900">
                    <span class="text-amber-400 font-bold block text-lg mb-0.5"><?= $antrean_stats['mengantre']; ?></span>
                    <span class="text-slate-500 text-[9px] uppercase font-bold">Sedang Mengantre</span>
                </div>
                <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-900">
                    <span class="text-green-400 font-bold block text-lg mb-0.5"><?= $antrean_stats['lolos']; ?></span>
                    <span class="text-slate-500 text-[9px] uppercase font-bold">Lolos Ke Checkout</span>
                </div>
                <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-900">
                    <span class="text-slate-400 font-bold block text-lg mb-0.5"><?= $antrean_stats['expired']; ?></span>
                    <span class="text-slate-500 text-[9px] uppercase font-bold">Selesai Membayar</span>
                </div>
            </div>
        </div>

        <!-- ==========================================
             TABEL LOG TRANSAKSI MASUK TERBARU
             ========================================== -->
        <div class="glass-panel border border-slate-800/80 rounded-3xl shadow-xl overflow-hidden">
            <div class="p-6 border-b border-slate-900">
                <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400">
                    📑 5 TRANSAKSI MASUK TERBARU (LIVE AUDIT)
                </h2>
            </div>
            
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left font-mono text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-950/80 border-b border-slate-900 text-slate-500 font-bold uppercase">
                            <th class="p-4 text-center w-12">NO</th>
                            <th class="p-4">KODE BOOKING</th>
                            <th class="p-4">NAMA PEMBELI</th>
                            <th class="p-4">KELAS TIKET</th>
                            <th class="p-4 text-center">QTY</th>
                            <th class="p-4 text-right">TOTAL NOMINAL</th>
                            <th class="p-4 text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-900/60 text-slate-300">
                        <?php if (mysqli_num_rows($query_log_transaksi) > 0): ?>
                            <?php $no = 1; while($log = mysqli_fetch_assoc($query_log_transaksi)): ?>
                                <tr class="hover:bg-slate-900/40 transition">
                                    <td class="p-4 text-center text-slate-600 font-bold"><?= $no++; ?></td>
                                    <td class="p-4 font-bold text-purple-400 tracking-wider"><?= $log['kode_transaksi']; ?></td>
                                    <td class="p-4 font-sans font-medium text-white"><?= htmlspecialchars($log['nama']); ?></td>
                                    <td class="p-4 text-slate-400"><?= htmlspecialchars($log['kategori']); ?></td>
                                    <td class="p-4 text-center font-bold"><?= $log['jumlah_tiket']; ?></td>
                                    <td class="p-4 text-right font-bold text-green-400">Rp <?= number_format($log['total_bayar'], 0, ',', '.'); ?></td>
                                    <td class="p-4 text-center">
                                        <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2 py-0.5 rounded text-[10px] font-bold">
                                            <?= strtoupper($log['status_pembayaran']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="p-12 text-center text-slate-500">
                                    [BELUM ADA TRANSAKSI SUKSES MASUK KEDALAM SISTEM DATABASE LOG]
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Footer Root Panel -->
    <footer class="mt-20 text-center text-[10px] text-slate-700 font-mono tracking-widest">
        ADMIN SECURE ROOT PROTOCOL // PARALLEL CORE GATEWAY SYSTEM TERMINAL
    </footer>

</body>
</html>