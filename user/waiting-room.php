<?php
// user/waiting-room.php
require_once '../config/database.php';

// Proteksi Halaman: Cek apakah user sudah login
if (!isset($_SESSION['login_user']) || $_SESSION['login_user'] !== true) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 1. Cek apakah user sudah punya antrean aktif hari ini
$cek_antrean = mysqli_query($conn, "SELECT * FROM antrean WHERE user_id = $user_id AND status_antrean = 'mengantre'");

if (mysqli_num_rows($cek_antrean) > 0) {
    $data_antrean = mysqli_fetch_assoc($cek_antrean);
    $nomor_anda = $data_antrean['nomor_antrean'];
} else {
    // Cek apakah user ternyata sudah lolos sebelumnya
    $cek_lolos = mysqli_query($conn, "SELECT * FROM antrean WHERE user_id = $user_id AND status_antrean = 'lolos'");
    if (mysqli_num_rows($cek_lolos) > 0) {
        header("Location: pesan-tiket.php");
        exit;
    }

    // Jika belum punya antrean sama sekali, buat nomor antrean acak
    $nomor_anda = rand(50, 300); // Kita perkecil angkanya untuk testing agar cepat sampai 0
    
    mysqli_query($conn, "INSERT INTO antrean (user_id, nomor_antrean, status_antrean) VALUES ($user_id, $nomor_anda, 'mengantre')");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚨 WAITING ROOM - WAR TIKET AESPA 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-white font-sans min-h-screen flex flex-col justify-between">

    <!-- Header info singkat -->
    <header class="p-6 border-b border-slate-900 bg-slate-900/40 backdrop-blur-md flex justify-between items-center">
        <span class="font-mono text-xs text-purple-400 tracking-widest uppercase">SYNK SYSTEM V.2026</span>
        <span class="text-xs text-slate-400">User: <strong class="text-white"><?= htmlspecialchars($_SESSION['user_nama']); ?></strong></span>
    </header>

    <!-- Main Content Area -->
    <main class="max-w-md w-full mx-auto p-6 text-center my-auto">
        
        <!-- Status Indicator / Kipas Loading Animasi -->
        <div class="relative w-24 h-24 mx-auto mb-8">
            <div class="absolute inset-0 rounded-full border-4 border-purple-500/20"></div>
            <div id="spinner" class="absolute inset-0 rounded-full border-4 border-t-purple-500 border-r-cyan-400 animate-spin"></div>
            <div id="success-icon" class="hidden absolute inset-0 flex items-center justify-center text-4xl text-green-400 font-bold">✓</div>
        </div>

        <h1 id="headline" class="text-2xl font-black uppercase tracking-tight mb-2">Kamu Berada di Dalam Antrean</h1>
        <p id="subheadline" class="text-sm text-slate-400 mb-8">Mohon jangan me-refresh atau menutup halaman ini agar posisimu tidak terlempar ke belakang.</p>

        <!-- Box Angka Antrean -->
        <div class="bg-slate-900 border border-slate-800 p-8 rounded-2xl shadow-2xl relative overflow-hidden mb-6">
            <span class="text-xs uppercase tracking-wider text-slate-500 font-bold block mb-1">Nomor Antrean Anda</span>
            
            <!-- Tampilan Angka Real-time -->
            <div id="queue-number" class="text-5xl font-black font-mono text-cyan-400 tracking-wider my-3">
                <?= $nomor_anda; ?>
            </div>
            
            <p id="queue-status" class="text-xs text-purple-400 font-mono">Memproses enkripsi antrean tiket...</p>
        </div>

        <!-- Estimasi Waktu Tunggu Simulasi -->
        <div id="eta-box" class="text-xs text-slate-500 font-mono">
            Estimasi waktu tunggu: <span id="eta-time" class="text-slate-300">Menghitung...</span>
        </div>

        <!-- Tombol Lanjut (Muncul saat Lolos) -->
        <div id="action-box" class="hidden mt-6">
            <a href="pesan-tiket.php" class="block w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-400 hover:to-emerald-500 text-slate-950 font-black tracking-wider text-center py-4 rounded-xl shadow-lg shadow-green-500/20 transition transform hover:scale-105 duration-200">
                🎉 LOLOS ANTREAN! PILIH TIKET SEKARANG
            </a>
        </div>

    </main>

    <footer class="p-6 text-center text-[10px] text-slate-700 font-mono">
        SECURE QUEUE SYSTEM PROTOCOL // PARALLEL LINE
    </footer>

    <!-- JAVASCRIPT BULLETPROOF (ANTI EROR & PASTI MUNCUL) -->
    <script>
        let currentQueue = <?= $nomor_anda; ?>;

        // Cek jalannya hitung mundur
        if (currentQueue <= 0) {
            bukaAksesUser();
        } else {
            const interval = setInterval(() => {
                // Pengurangan acak antara 15 sampai 35 biar super cepat sampai angka 0
                let pengurangan = Math.floor(Math.random() * 20) + 15; 
                currentQueue -= pengurangan;

                if (currentQueue <= 0) {
                    currentQueue = 0;
                }

                // Amankan pengubahan teks di layar dengan validasi objek elemen
                const qElem = document.getElementById('queue-number');
                const etaElem = document.getElementById('eta-time');
                
                if (qElem) qElem.innerText = currentQueue;
                if (etaElem) etaElem.innerText = Math.ceil(currentQueue / 45) + " detik lagi";

                if (currentQueue === 0) {
                    clearInterval(interval);
                    bukaAksesUser(); 
                }
            }, 1000);
        }

        function bukaAksesUser() {
            // Segera panggil elemen tanpa takut bentrok eror null script
            const qElem = document.getElementById('queue-number');
            const qStatus = document.getElementById('queue-status');
            const spinner = document.getElementById('spinner');
            const successIcon = document.getElementById('success-icon');
            const etaBox = document.getElementById('eta-box');
            const headline = document.getElementById('headline');
            const subheadline = document.getElementById('subheadline');
            const actionBox = document.getElementById('action-box');

            // Eksekusi perubahan visual satu per satu secara aman
            if (qElem) {
                qElem.innerText = "0000";
                qElem.className = "text-5xl font-black font-mono text-green-400 tracking-wider my-3 animate-bounce";
            }
            if (qStatus) qStatus.innerText = "Akses pemesanan dibuka!";
            if (spinner) spinner.style.display = 'none';
            if (successIcon) successIcon.style.display = 'flex';
            if (etaBox) etaBox.style.display = 'none';
            
            if (headline) {
                headline.innerText = "Giliran Anda Tiba!";
                headline.className = "text-2xl font-black text-green-400 uppercase tracking-tight mb-2";
            }
            if (subheadline) {
                subheadline.innerText = "Klik tombol di bawah segera, sebelum sesi Anda kedaluwarsa.";
            }
            
            // TAMPILKAN TOMBOL UTAMA MENUJU FORMULIR PESAN TIKET
            if (actionBox) {
                actionBox.style.display = 'block';
                actionBox.classList.remove('hidden');
            }

            // Jalankan sinkronisasi database di belakang layar
            fetch('update-status-antrean.php')
            .then(res => res.text())
            .then(data => { console.log("Sync DB Success:", data); })
            .catch(err => { console.warn("Sync DB Delayed:", err); });
        }
    </script>
</body>
</html>