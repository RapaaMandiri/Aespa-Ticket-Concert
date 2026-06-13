<?php
// user/pesan-tiket.php
require_once '../config/database.php';

global $conn;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi login dasar
if (!isset($_SESSION['login_user']) || $_SESSION['login_user'] !== true) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user saat ini untuk auto-fill form demi kemudahan pengguna
$query_user = mysqli_query($conn, "SELECT nama, email FROM users WHERE id = $user_id");
$data_user = mysqli_fetch_assoc($query_user);

// Ambil opsi tiket resmi aespa dari database
$query_tiket = mysqli_query($conn, "SELECT * FROM tiket ORDER BY harga DESC");

if (isset($_POST['proses_pemesanan'])) {
    $tiket_id = intval($_POST['tiket_id']);
    $jumlah = isset($_POST['jumlah_tiket']) ? intval($_POST['jumlah_tiket']) : 1;
    
    // Ambil input data diri tambahan
    $nama_pemesan = mysqli_real_escape_string($conn, $_POST['nama_pemesan']);
    $email_pemesan = mysqli_real_escape_string($conn, $_POST['email_pemesan']);
    $telepon_pemesan = mysqli_real_escape_string($conn, $_POST['telepon_pemesan']);
    $alamat_pemesan = mysqli_real_escape_string($conn, $_POST['alamat_pemesan']);
    
    if ($tiket_id <= 0) {
        echo "<script>alert('Silakan pilih kategori tiket yang valid!');</script>";
    } else {
        $cek_stok = mysqli_query($conn, "SELECT * FROM tiket WHERE id = $tiket_id");
        $data_tiket = mysqli_fetch_assoc($cek_stok);
        
        if ($data_tiket) {
            if ($data_tiket['stok'] >= $jumlah) {
                $total_bayar = $data_tiket['harga'] * $jumlah;
                $kode_transaksi = "AESPA-" . strtoupper(bin2hex(random_bytes(4)));
                
                // Simpan rincian data diri dan detail booking ke Session
                $_SESSION['booking'] = [
                    'tiket_id' => $tiket_id,
                    'kategori' => $data_tiket['kategori'],
                    'jumlah' => $jumlah,
                    'total_bayar' => $total_bayar,
                    'kode_transaksi' => $kode_transaksi,
                    'data_diri' => [
                        'nama' => $nama_pemesan,
                        'email' => $email_pemesan,
                        'telepon' => $telepon_pemesan,
                        'alamat' => $alamat_pemesan
                    ]
                ];
                
                header("Location: pembayaran.php");
                exit;
            } else {
                echo "<script>alert('Maaf! Stok tidak mencukupi.');</script>";
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
    <title>Formulir Pemesanan Tiket - aespa 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: linear-gradient(to bottom, rgba(2, 6, 23, 0.9) 0%, rgba(2, 6, 23, 0.98) 100%), url('../assets/image/aespa2026.jpg');
            background-size: cover;
            background-position: center top;
            background-attachment: fixed;
        }
        .glass-card {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.6) 0%, rgba(30, 41, 59, 0.4) 100%);
            backdrop-filter: blur(16px);
        }
    </style>
</head>
<body class="text-white min-h-screen flex items-center justify-center p-6 relative">

    <div class="glass-card border border-slate-800/80 p-8 rounded-3xl max-w-2xl w-full shadow-2xl relative my-12">
        <div class="mb-6">
            <span class="text-[9px] font-black tracking-widest text-cyan-400 uppercase bg-cyan-500/10 border border-cyan-500/20 px-2.5 py-1 rounded-md">CHECKOUT ZONE</span>
            <h2 class="text-2xl font-black mt-3 tracking-tight">DATA DIRI & PEMILIHAN TIKET</h2>
            <p class="text-xs text-slate-400 mt-1">Isi informasi identitas legal Anda untuk pencetakan E-Ticket konser resmi.</p>
        </div>

        <form action="" method="POST" class="space-y-5">
            
            <!-- Grid Data Diri -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold">Nama Lengkap Pemesan</label>
                    <input type="text" name="nama_pemesan" required value="<?= htmlspecialchars($data_user['nama']); ?>" class="w-full bg-slate-950 border border-slate-800 focus:border-purple-500/60 rounded-xl p-3 text-white text-sm focus:outline-none transition">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold">Email</label>
                    <input type="email" name="email_pemesan" required value="<?= htmlspecialchars($data_user['email']); ?>" class="w-full bg-slate-950 border border-slate-800 focus:border-purple-500/60 rounded-xl p-3 text-white text-sm focus:outline-none transition">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold">Nomor Telepon / WhatsApp</label>
                <input type="tel" name="telepon_pemesan" required placeholder="Contoh: 081234567xxx" class="w-full bg-slate-950 border border-slate-800 focus:border-purple-500/60 rounded-xl p-3 text-white text-sm focus:outline-none transition">
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold">Alamat Rumah Lengkap</label>
                <textarea name="alamat_pemesan" rows="2" required placeholder="Masukkan alamat pengiriman invoice..." class="w-full bg-slate-950 border border-slate-800 focus:border-purple-500/60 rounded-xl p-3 text-white text-sm focus:outline-none transition"></textarea>
            </div>

            <hr class="border-slate-800/60 my-6">

            <!-- Kategori Tiket -->
            <div class="space-y-1.5">
                <label class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold">Pilih Kategori Kelas</label>
                <select name="tiket_id" id="tiket_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-white text-sm focus:outline-none cursor-pointer" onchange="hitungTotal()">
                    <option value="" data-harga="0">-- Silakan Pilih Kategori Kelas --</option>
                    <?php if ($query_tiket && mysqli_num_rows($query_tiket) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($query_tiket)): ?>
                            <option value="<?= $row['id'] ?>" data-harga="<?= $row['harga'] ?>">
                                <?= htmlspecialchars($row['kategori']) ?> (Rp <?= number_format($row['harga'], 0, ',', '.') ?>) — Sisa <?= $row['stok'] ?> Pcs
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Jumlah Tiket -->
            <div class="space-y-1.5">
                <label class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold">Jumlah Tiket (Maksimal 4)</label>
                <input type="number" name="jumlah_tiket" id="jumlah_tiket" min="1" max="4" value="1" required class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-white text-sm focus:outline-none font-mono font-bold" oninput="hitungTotal()">
            </div>

            <!-- Kalkulator Total -->
            <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-900 font-mono text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 uppercase tracking-wider font-bold">Total Pembayaran:</span>
                    <span id="display-total" class="text-green-400 text-base font-black">Rp 0</span>
                </div>
            </div>

            <button type="submit" name="proses_pemesanan" class="w-full bg-gradient-to-r from-cyan-500 via-purple-500 to-fuchsia-600 text-slate-950 font-black tracking-widest py-3.5 rounded-xl text-xs uppercase transition hover:brightness-110 shadow-lg">
                Lanjut ke Pilih Pembayaran →
            </button>
        </form>
    </div>

    <script>
        function hitungTotal() {
            const selectTiket = document.getElementById('tiket_id');
            const jumlahInput = document.getElementById('jumlah_tiket');
            const displayTotal = document.getElementById('display-total');

            const harga = parseInt(selectTiket.options[selectTiket.selectedIndex].getAttribute('data-harga')) || 0;
            const jumlah = parseInt(jumlahInput.value) || 0;

            const total = harga * jumlah;
            displayTotal.innerText = "Rp " + total.toLocaleString('id-ID');
        }
    </script>
</body>
</html>