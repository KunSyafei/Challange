<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Total siswa
$stmt = $pdo->query("SELECT COUNT(*) as total FROM siswa");
$totalSiswa = $stmt->fetch()['total'];

// Kehadiran hari ini
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM kehadiran WHERE tanggal = ? AND status = 'hadir'");
$stmt->execute([$today]);
$kehadiranHariIni = $stmt->fetch()['total'];

// Kegiatan aktif
$stmt = $pdo->query("SELECT COUNT(*) as total FROM timeline_kegiatan WHERE status = 'ongoing'");
$kegiatanAktif = $stmt->fetch()['total'];

// Rata-rata nilai
$stmt = $pdo->query("SELECT AVG(nilai) as rata FROM bina_jasmani");
$rataNilai = round($stmt->fetch()['rata'] ?? 0);

echo json_encode([
    'total_siswa' => $totalSiswa,
    'kehadiran_hari_ini' => $kehadiranHariIni,
    'kegiatan_aktif' => $kegiatanAktif,
    'rata_nilai' => $rataNilai
]);
?>