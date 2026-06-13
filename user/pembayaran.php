<?php
// user/pembayaran.php
require_once '../config/database.php';

global $conn;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah data pemesanan di session tersedia
if (!isset($_SESSION['booking'])) {
    header("Location: pesan-tiket.php");
    exit;
}

// FITUR DINAMIS: Set batas waktu pembayaran (10 Menit dari pertama kali masuk halaman)
if (!isset($_SESSION['booking_expiry'])) {
    $_SESSION['booking_expiry'] = time() + 600; // 600 detik = 10 menit
}

// Cek jika waktu pembayaran di server sudah habis (Bypass Proteksi Backend)
if (time() > $_SESSION['booking_expiry']) {
    unset($_SESSION['booking']);
    unset($_SESSION['booking_expiry']);
    echo "<script>alert('⏰ Waktu pembayaran Anda telah habis! Tiket Anda telah dikembalikan ke sistem.'); window.location='dashboard.php';</script>";
    exit;
}

$booking = $_SESSION['booking'];
$user_id = $_SESSION['user_id'];
$sisa_waktu_detik = $_SESSION['booking_expiry'] - time();

// ==========================================
// PROSES KONFIRMASI PEMBAYARAN
// ==========================================
if (isset($_POST['konfirmasi_pembayaran'])) {
    $metode_dipilih = mysqli_real_escape_string($conn, $_POST['metode_pembayaran']);
    
    $tiket_id = $booking['tiket_id'];
    $jumlah = $booking['jumlah'];
    $kode_transaksi = $booking['kode_transaksi'];
    $total_bayar = $booking['total_bayar'];
    
    // 1. Kurangi stok tiket di database
    mysqli_query($conn, "UPDATE tiket SET stok = stok - $jumlah WHERE id = $tiket_id");
    
    // 2. SIMPAN TRANSAKSI
    $insert_transaksi = "INSERT INTO transaksi (user_id, tiket_id, kode_transaksi, jumlah_tiket, total_bayar, metode_pembayaran, status_pembayaran, photocard_status) 
                         VALUES ($user_id, $tiket_id, '$kode_transaksi', $jumlah, $total_bayar, '$metode_dipilih', 'sukses', 'belum_diambil')";
    
    if (mysqli_query($conn, $insert_transaksi)) {
        mysqli_query($conn, "UPDATE antrean SET status_antrean = 'expired' WHERE user_id = $user_id AND status_antrean = 'lolos'");
        
        // Bersihkan session pencatat waktu dan booking
        unset($_SESSION['booking']);
        unset($_SESSION['booking_expiry']);
        
        echo "<script>alert('🎉 Pembayaran via " . $metode_dipilih . " Sukses Dikonfirmasi!'); window.location='tiket-saya.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment Gateway - aespa 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: linear-gradient(to bottom, rgba(2, 6, 23, 0.92) 0%, rgba(2, 6, 23, 0.98) 100%), url('../assets/image/aespa2026.jpg');
            background-size: cover;
            background-position: center top;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        .glass-panel {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.6) 0%, rgba(30, 41, 59, 0.4) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
    </style>
</head>
<body class="text-white min-h-screen flex items-center justify-center p-4 md:p-8 relative overflow-x-hidden">

    <!-- Container Utama Form dengan Efek Kaca Modis -->
    <div class="glass-panel border border-slate-800/80 rounded-3xl max-w-4xl w-full shadow-2xl relative z-10 overflow-hidden my-8">
        
        <!-- BANNER KONSER ATAS -->
        <div class="w-full h-48 md:h-60 relative overflow-hidden border-b border-slate-800">
            <img src="../assets/image/aespa2026.jpg" alt="aespa Tour Banner" class="w-full h-full object-cover object-center brightness-75">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
            <div class="absolute bottom-6 left-6 md:left-8">
                <span class="text-[9px] font-black tracking-widest text-cyan-400 bg-cyan-500/10 border border-cyan-500/20 px-2.5 py-1 rounded-md uppercase">SECURE CHECKOUT</span>
                <h2 class="text-2xl md:text-3xl font-black mt-2 tracking-tight uppercase">METODE PEMBAYARAN</h2>
            </div>
        </div>

        <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-5 gap-8">
            
            <!-- SISI KIRI: COUNTDOWN TIMER + NOTA INVOICE + PENJELASAN ALUR -->
            <div class="md:col-span-2 space-y-5 flex flex-col justify-start border-b md:border-b-0 md:border-r border-slate-800/60 pb-6 md:pb-0 md:pr-6">
                
                <!-- FITUR BARU: PANEL LIVE COUNTDOWN TIMER UTAMA -->
                <div class="bg-red-500/10 border border-red-500/30 p-4 rounded-xl text-center space-y-1 font-mono">
                    <span class="text-[9px] text-red-400 block uppercase font-black tracking-widest">⚠️ SISA WAKTU SELESAI PEMBAYARAN</span>
                    <!-- Jam digital countdown yang terus berdetak mundur -->
                    <span id="main-expiry-timer" class="text-2xl font-black text-red-500 tracking-wider">10:00</span>
                </div>

                <!-- Detail Invoice Kaca -->
                <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-900 space-y-3 font-mono text-xs">
                    <div>
                        <span class="text-slate-500 block text-[9px] font-bold uppercase">Kode Transaksi</span>
                        <span class="text-slate-200 text-sm font-bold tracking-wider"><?= $booking['kode_transaksi']; ?></span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[9px] font-bold uppercase">Kategori Kelas</span>
                        <span class="text-slate-200 text-sm font-bold text-purple-400"><?= htmlspecialchars($booking['kategori']); ?></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 border-t border-slate-900 pt-3">
                        <div>
                            <span class="text-slate-500 block text-[9px] font-bold uppercase">Kuantitas</span>
                            <span class="text-slate-200 font-bold"><?= $booking['jumlah']; ?> Tiket</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[9px] font-bold uppercase">Biaya Admin</span>
                            <span class="text-green-400 font-bold">Rp 0 (FREE)</span>
                        </div>
                    </div>
                </div>

                <!-- Monitor Total Tagihan -->
                <div class="bg-gradient-to-br from-slate-950 to-slate-900 p-4 rounded-xl border border-slate-800/80 text-center font-mono">
                    <span class="text-[9px] text-slate-400 block uppercase font-bold mb-1">TOTAL BAYAR</span>
                    <span class="text-xl font-black text-green-400">Rp <?= number_format($booking['total_bayar'], 0, ',', '.'); ?></span>
                </div>

                <!-- KOLOM PENJELASAN ALUR AFTER PEMBAYARAN -->
                <div class="bg-slate-950/40 border border-slate-900 p-4 rounded-xl space-y-3">
                    <span class="block text-[10px] font-bold text-cyan-400 uppercase tracking-widest font-mono border-b border-slate-900 pb-1.5">📌 ALUR SETELAH BAYAR:</span>
                    <ul class="space-y-2.5 text-[11px] text-slate-300 font-sans leading-relaxed">
                        <li class="flex items-start space-x-2">
                            <span class="text-purple-400 font-bold font-mono">1.</span>
                            <span>Sistem melakukan enkripsi database otomatis untuk mencetak **E-Ticket resmi** Anda.</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-purple-400 font-bold font-mono">2.</span>
                            <span>Anda akan diarahkan langsung ke halaman **"Tiket Saya"** untuk mengunduh kode barcode QR.</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-purple-400 font-bold font-mono">3.</span>
                            <span>Bawa E-Ticket tersebut ke lokasi **ICE BSD Hall 5-6** pada hari H untuk ditukarkan menjadi Wristband fisik.</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-purple-400 font-bold font-mono">4.</span>
                            <span>Tunjukkan invoice untuk mengklaim **Exclusive 3D Photocard Reward** di booth merchandise resmi SM.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- SISI KANAN: PILIHAN LOGO METODE BAYAR -->
            <form action="" method="POST" class="md:col-span-3 space-y-6 flex flex-col justify-between">
                
                <div class="space-y-4">
                    
                    <!-- KATEGORI 1: QRIS / E-WALLET -->
                    <div class="space-y-2">
                        <span class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider">📱 QRIS & E-Wallet Gateway</span>
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Pilihan QRIS (KEMBALI MONOKROM - BERWARNA SAAT HOVER) -->
                            <label class="relative flex items-center justify-between p-3 bg-slate-950/50 border border-slate-850 hover:border-cyan-500/40 rounded-xl cursor-pointer transition duration-200 group">
                                <div class="flex items-center space-x-3 w-full">
                                    <input type="radio" name="metode_pembayaran" value="QRIS" required onclick="switchPanel('qris-panel', 'QRIS INSTANT')" class="text-purple-600 focus:ring-0">
                                    <img src="../assets/image/qris.png" alt="QRIS" class="h-4 object-contain brightness-0 invert opacity-60 group-hover:opacity-100 group-hover:brightness-100 group-hover:invert-0 transition duration-200">
                                </div>
                            </label>
                            <!-- Pilihan Dana (FULL COLOR SEJAK AWAL) -->
                            <label class="relative flex items-center justify-between p-3 bg-slate-950/50 border border-slate-850 hover:border-cyan-500/40 rounded-xl cursor-pointer transition duration-200 group">
                                <div class="flex items-center space-x-3 w-full">
                                    <input type="radio" name="metode_pembayaran" value="DANA" onclick="switchPanel('qris-panel', 'DANA GATEWAY')" class="text-purple-600 focus:ring-0">
                                    <img src="../assets/image/dana.png" alt="DANA" class="h-4 object-contain transition duration-200 group-hover:scale-102">
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- KATEGORI 2: VIRTUAL ACCOUNT BANK -->
                    <div class="space-y-2">
                        <span class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider">🏦 Virtual Account Transfer</span>
                        <div class="grid grid-cols-2 gap-2">
                            <!-- BCA VA -->
                            <label class="flex items-center space-x-3 p-3 bg-slate-950/50 border border-slate-850 hover:border-purple-500/40 rounded-xl cursor-pointer transition duration-200 group">
                                <input type="radio" name="metode_pembayaran" value="VA BCA" onclick="switchPanel('va-panel', '0122 2026 8899 7755 (BCA)')" class="text-purple-600 focus:ring-0">
                                <img src="../assets/image/bca.png" alt="BCA" class="h-4 object-contain transition duration-200 group-hover:scale-102">
                            </label>
                            <!-- Mandiri VA -->
                            <label class="flex items-center space-x-3 p-3 bg-slate-950/50 border border-slate-850 hover:border-purple-500/40 rounded-xl cursor-pointer transition duration-200 group">
                                <input type="radio" name="metode_pembayaran" value="VA MANDIRI" onclick="switchPanel('va-panel', '8830 1983 0044 1122 (MANDIRI)')" class="text-purple-600 focus:ring-0">
                                <img src="../assets/image/mandiri.png" alt="Mandiri" class="h-3 object-contain transition duration-200 group-hover:scale-102">
                            </label>
                            <!-- BNI VA -->
                            <label class="flex items-center space-x-3 p-3 bg-slate-950/50 border border-slate-850 hover:border-purple-500/40 rounded-xl cursor-pointer transition duration-200 group">
                                <input type="radio" name="metode_pembayaran" value="VA BNI" onclick="switchPanel('va-panel', '9880 2901 3344 5566 (BNI)')" class="text-purple-600 focus:ring-0">
                                <img src="../assets/image/KbBank.png" alt="BNI" class="h-3 object-contain transition duration-200 group-hover:scale-102">
                            </label>
                            <!-- BRI VA -->
                            <label class="flex items-center space-x-3 p-3 bg-slate-950/50 border border-slate-850 hover:border-purple-500/40 rounded-xl cursor-pointer transition duration-200 group">
                                <input type="radio" name="metode_pembayaran" value="VA BRI" onclick="switchPanel('va-panel', '1250 0988 5544 3321 (BRI)')" class="text-purple-600 focus:ring-0">
                                <img src="../assets/image/bri.png" alt="BRI" class="h-3.5 object-contain transition duration-200 group-hover:scale-102">
                            </label>
                        </div>
                    </div>

                    <!-- KATEGORI 3: MINI MARKET -->
                    <div class="space-y-2">
                        <span class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider">🏪 Gerai Retail Swasembada</span>
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Indomaret -->
                            <label class="flex items-center space-x-3 p-3 bg-slate-950/50 border border-slate-850 hover:border-amber-500/40 rounded-xl cursor-pointer transition duration-200 group">
                                <input type="radio" name="metode_pembayaran" value="INDOMARET" onclick="switchPanel('retail-panel', 'INDOMARET')" class="text-purple-600 focus:ring-0">
                                <img src="../assets/image/indomaret.png" alt="Indomaret" class="h-4 object-contain transition duration-200 group-hover:scale-102">
                            </label>
                            <!-- Alfamart -->
                            <label class="flex items-center space-x-3 p-3 bg-slate-950/50 border border-slate-850 hover:border-amber-500/40 rounded-xl cursor-pointer transition duration-200 group">
                                <input type="radio" name="metode_pembayaran" value="ALFAMART" onclick="switchPanel('retail-panel', 'ALFAMART')" class="text-purple-600 focus:ring-0">
                                <img src="../assets/image/alfamart.png" alt="Alfamart" class="h-4 object-contain transition duration-200 group-hover:scale-102">
                            </label>
                        </div>
                    </div>
                </div>

                <!-- MONITOR DISPLAY SIMULASI AKTIF -->
                <div>
                    <!-- Display QRIS -->
                    <div id="qris-panel" class="hidden bg-slate-950/80 border border-slate-900 p-4 rounded-xl text-center space-y-3">
                        <p class="text-[10px] font-mono text-amber-400">Scan QR Code <span id="qris-title" class="font-bold text-white"></span> di bawah ini:</p>
                        <div class="w-32 h-32 mx-auto bg-white p-1 rounded-lg border-2 border-cyan-400/30">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=AESPA-REWARD-<?= $booking['kode_transaksi']; ?>" alt="QR Code" class="w-full h-full object-contain">
                        </div>
                        <div class="text-[10px] font-mono text-slate-500">Masa Berlaku QRIS: <span id="timer" class="text-red-400 font-bold">05:00</span></div>
                    </div>

                    <!-- Display VA -->
                    <div id="va-panel" class="hidden bg-slate-950/80 border border-slate-900 p-4 rounded-xl space-y-2">
                        <p class="text-[10px] font-mono text-amber-400 text-center">Salin rekening Virtual Account transfer berikut:</p>
                        <div class="bg-slate-900 p-2.5 rounded-lg border border-slate-850 flex justify-between items-center font-mono text-xs">
                            <span id="va-number" class="font-black text-cyan-400 tracking-wider"></span>
                            <button type="button" onclick="alert('Nomor VA berhasil disalin!')" class="bg-slate-800 hover:bg-slate-700 text-[9px] text-slate-300 font-bold px-2 py-1 rounded">SALIN</button>
                        </div>
                    </div>

                    <!-- Display Retail -->
                    <div id="retail-panel" class="hidden bg-slate-950/80 border border-slate-900 p-4 rounded-xl space-y-1 text-center font-mono text-xs">
                        <p class="text-[10px] text-amber-400">Tunjukkan kode bayar ke kasir <span id="retail-title" class="font-bold text-white"></span>:</p>
                        <div class="bg-slate-900 p-2 rounded-lg border border-slate-850 font-black text-fuchsia-400 text-base tracking-widest">
                            AESPA2026<?= rand(10,99); ?>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit Akhir -->
                <button type="submit" name="konfirmasi_pembayaran" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-slate-950 font-black tracking-widest py-3.5 rounded-xl text-xs uppercase transition hover:brightness-110 shadow-lg shadow-green-500/10">
                    ✓ SAYA SUDAH SELESAI MEMBAYAR
                </button>
            </form>
        </div>
    </div>

    <!-- Script Pengendali Screen Monitor & Live Countdown -->
    <script>
        // Hitung mundur sisa waktu transaksi utama dari PHP
        let sisaWaktu = <?= $sisa_waktu_detik; ?>;
        const mainDisplay = document.getElementById('main-expiry-timer');

        const mainCountdown = setInterval(() => {
            let minutes = Math.floor(sisaWaktu / 60);
            let seconds = sisaWaktu % 60;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            minutes = minutes < 10 ? '0' + minutes : minutes;

            mainDisplay.innerText = minutes + ":" + seconds;

            if (--sisaWaktu < 0) {
                clearInterval(mainCountdown);
                mainDisplay.innerText = "EXPIRED";
                alert('⏰ Waktu pembayaran habis! Transaksi dibatalkan otomatis.');
                window.location.href = 'dashboard.php';
            }
        }, 1000);

        function switchPanel(panelId, detailData) {
            document.getElementById('qris-panel').classList.add('hidden');
            document.getElementById('va-panel').classList.add('hidden');
            document.getElementById('retail-panel').classList.add('hidden');
            
            document.getElementById(panelId).classList.remove('hidden');

            if(panelId === 'qris-panel') {
                document.getElementById('qris-title').innerText = detailData;
                startTimer();
            } else if(panelId === 'va-panel') {
                document.getElementById('va-number').innerText = detailData;
            } else if(panelId === 'retail-panel') {
                document.getElementById('retail-title').innerText = detailData;
            }
        }

        let timerStarted = false;
        function startTimer() {
            if(timerStarted) return;
            timerStarted = true;
            let time = 300; 
            const display = document.getElementById('timer');
            
            const countdown = setInterval(() => {
                let minutes = Math.floor(time / 60);
                let seconds = time % 60;
                seconds = seconds < 10 ? '0' + seconds : seconds;
                
                display.innerText = minutes + ":" + seconds;
                
                if (--time < 0) {
                    clearInterval(countdown);
                    display.innerText = "EXPIRED";
                }
            }, 1000);
        }
    </script>
</body>
</html>