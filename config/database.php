<?php
// config/database.php

// Pastikan session dimulai paling pertama sebelum ada kode lain
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$username = "root";
$password = ""; // Kosongkan jika pakai Laragon / XAMPP bawaan
$database = "db_aespa_ticket";

// Membuat koneksi ke database
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Menjadikan variabel koneksi global agar bisa dibaca di file mana pun
global $conn;
?>