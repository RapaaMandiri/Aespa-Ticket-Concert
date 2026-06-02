<?php
// admin/kelola-tiket.php
require_once '../config/database.php';

// Proteksi Halaman: Cek apakah yang masuk benar-benar admin
if (!isset($_SESSION['login_admin']) || $_SESSION['login_admin'] !== true) {
    header("Location: login.php");
    exit;
}

$pesan = "";

// ==========================================
// 1. PROSES CREATE (TAMBAH TIKET)
// ==========================================
if (isset($_POST['tambah_tiket'])) {
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $benefit = mysqli_real_escape_string($conn, $_POST['benefit']);

    $query = "INSERT INTO tiket (kategori, harga, stok, benefit) VALUES ('$kategori', '$harga', '$stok', '$benefit')";
    if (mysqli_query($conn, $query)) {
        $pesan = "<p class='text-green-400 font-bold mb-4'>[SUCCESS] Kategori tiket berhasil ditambahkan!</p>";
    } else {
        $pesan = "<p class='text-red-400 font-bold mb-4'>[ERROR] Gagal menambah tiket.</p>";
    }
}

// ==========================================
// 2. PROSES UPDATE (EDIT TIKET)
// ==========================================
if (isset($_POST['edit_tiket'])) {
    $id = intval($_POST['id_tiket']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $benefit = mysqli_real_escape_string($conn, $_POST['benefit']);

    $query = "UPDATE tiket SET kategori='$kategori', harga='$harga', stok='$stok', benefit='$benefit' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Tiket berhasil diperbarui!'); window.location='kelola-tiket.php';</script>";
    }
}

// ==========================================
// 3. PROSES DELETE (HAPUS TIKET)
// ==========================================
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $query = "DELETE FROM tiket WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Tiket berhasil dihapus!'); window.location='kelola-tiket.php';</script>";
    }
}

// ==========================================
// 4. PROSES READ (AMBIL DATA UNTUK FORM EDIT / TABEL)
// ==========================================
$tiket_edit = null;
if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);
    $res_edit = mysqli_query($conn, "SELECT * FROM tiket WHERE id = $id_edit");
    $tiket_edit = mysqli_fetch_assoc($res_edit);
}

// Ambil semua data tiket untuk ditampilkan di tabel
$all_tiket = mysqli_query($conn, "SELECT * FROM tiket ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRUD Tiket - Admin aespa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen flex">

    <div class="w-64 bg-zinc-900 border-r border-zinc-800 p-6 flex flex-col justify-between">
        <div>
            <h2 class="text-xl font-black text-red-500 tracking-wider mb-8">AESPA ADMIN</h2>
            <nav class="space-y-4">
                <a href="dashboard.php" class="block text-zinc-400 hover:text-white py-2">Dashboard Utama</a>
                <a href="kelola-tiket.php" class="block text-white font-bold bg-zinc-800 px-4 py-2 rounded">Kelola Tiket (CRUD)</a>
                <a href="verifikasi.php" class="block text-zinc-400 hover:text-white py-2">Verifikasi Pembayaran</a>
            </nav>
        </div>
        <a href="../auth/logout.php" class="text-red-400 hover:underline text-sm font-mono">System Sign-Out -></a>
    </div>

    <div class="flex-1 p-10">
        <h1 class="text-3xl font-extrabold tracking-tight mb-2">MANAJEMEN KATEGORI TIKET</h1>
        <p class="text-zinc-400 mb-8">Tambah, ubah, atau hapus kuota kelas tiket konser aespa 2026 di sini.</p>

        <?= $pesan; ?>

        <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl mb-10">
            <h3 class="text-lg font-bold mb-4 text-purple-400">
                <?= $tiket_edit ? "⚡ EDIT KATEGORI TIKET" : "➕ TAMBAH KATEGORI TIKET BARU" ?>
            </h3>
            
            <form action="kelola-tiket.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php if ($tiket_edit): ?>
                    <input type="hidden" name="id_tiket" value="<?= $tiket_edit['id'] ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-zinc-400 mb-1">Nama Kategori / Kelas</label>
                    <input type="text" name="kategori" required value="<?= $tiket_edit ? $tiket_edit['kategori'] : '' ?>" placeholder="Contoh: MY ZONE - VIP" class="w-full bg-zinc-800 border border-zinc-700 rounded p-2.5 text-white focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-zinc-400 mb-1">Harga Tiket (Rupiah)</label>
                    <input type="number" name="harga" required value="<?= $tiket_edit ? $tiket_edit['harga'] : '' ?>" placeholder="Contoh: 3500000" class="w-full bg-zinc-800 border border-zinc-700 rounded p-2.5 text-white focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-zinc-400 mb-1">Stok Awal Tiket</label>
                    <input type="number" name="stok" required value="<?= $tiket_edit ? $tiket_edit['stok'] : '' ?>" placeholder="Contoh: 100" class="w-full bg-zinc-800 border border-zinc-700 rounded p-2.5 text-white focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-zinc-400 mb-1">Benefit Benefit</label>
                    <input type="text" name="benefit" required value="<?= $tiket_edit ? $tiket_edit['benefit'] : '' ?>" placeholder="Contoh: Soundcheck + 3D PC" class="w-full bg-zinc-800 border border-zinc-700 rounded p-2.5 text-white focus:outline-none focus:border-purple-500">
                </div>

                <div class="md:col-span-2 mt-2">
                    <?php if ($tiket_edit): ?>
                        <button type="submit" name="edit_tiket" class="bg-yellow-500 hover:bg-yellow-400 text-black font-bold px-6 py-2.5 rounded transition">SIMPAN PERUBAHAN</button>
                        <a href="kelola-tiket.php" class="ml-2 text-zinc-400 hover:underline">Batal</a>
                    <?php else: ?>
                        <button type="submit" name="tambah_tiket" class="bg-purple-600 hover:bg-purple-500 text-white font-bold px-6 py-2.5 rounded transition">INPUT TIKET KE DATABASE</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-800 border-b border-zinc-700 text-xs uppercase tracking-wider text-zinc-400">
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Harga</th>
                        <th class="p-4">Sisa Stok</th>
                        <th class="p-4">Benefit</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <?php while($row = mysqli_fetch_assoc($all_tiket)): ?>
                    <tr class="hover:bg-zinc-850 transition">
                        <td class="p-4 font-bold text-white"><?= htmlspecialchars($row['kategori']) ?></td>
                        <td class="p-4 text-cyan-400 font-mono">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                        <td class="p-4 font-mono"><?= $row['stok'] ?> Pcs</td>
                        <td class="p-4 text-zinc-400 text-sm"><?= htmlspecialchars($row['benefit']) ?></td>
                        <td class="p-4 text-center space-x-2 text-sm">
                            <a href="kelola-tiket.php?edit=<?= $row['id'] ?>" class="text-yellow-400 hover:underline">Edit</a>
                            <a href="kelola-tiket.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus kelas tiket ini?')" class="text-red-400 hover:underline">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>