<?php
// user/update-status-antrean.php
require_once '../config/database.php';

global $conn;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika session hilang, paksa ambil ID atau set default demi testing
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

// Update status antrean user menjadi lolos
$query = "UPDATE antrean SET status_antrean = 'lolos' WHERE user_id = $user_id";
mysqli_query($conn, $query);

// Keluarkan respons teks polos biasa agar JavaScript tidak crash
echo "OK_LOLOS";
exit;