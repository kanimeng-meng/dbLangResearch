<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isPeneliti() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'peneliti';
}

// Paksa login, panggil di halaman yang butuh autentikasi
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Paksa admin, panggil di halaman khusus admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: UAS_databasePalestinaIsrael.php');
        exit;
    }
}
?>