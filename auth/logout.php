<?php
// auth/logout.php

// Pastikan session aktif sebelum dihancurkan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Bersihkan semua data session yang menempel
$_SESSION = array();

// 2. Hancurkan cookie session di browser jika ada
if (ini_get("session_use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hancurkan session secara permanen di server
session_destroy();

// 4. PENGALIHAN: Lempar kembali ke halaman utama / index paling depan
// Jika file index.php kamu ada di folder utama (root), gunakan ../index.php
header("Location: ../index.php");
exit;