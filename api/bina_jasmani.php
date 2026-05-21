<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    // Get standarisasi nilai berdasarkan latihan
    $standarisasi = 75; // Default standarisasi
    
    $stmt = $pdo->prepare("INSERT INTO bina_jasmani (siswa_id, latihan_fisik, nilai, standarisasi_nilai, tanggal, catatan) VALUES (?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([
        $_POST['siswa_id'],
        $_POST['latihan_fisik'],
        $_POST['nilai'],
        $standarisasi,
        $_POST['tanggal'],
        $_POST['catatan'] ?? ''
    ]);
    
    echo json_encode(['success' => $result]);
} elseif ($method == 'GET') {
    if (isset($_GET['siswa_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM bina_jasmani WHERE siswa_id = ? ORDER BY tanggal DESC");
        $stmt->execute([$_GET['siswa_id']]);
        $data = $stmt->fetchAll();
    } else {
        $stmt = $pdo->query("SELECT b.*, s.nama, s.nis FROM bina_jasmani b JOIN siswa s ON b.siswa_id = s.id ORDER BY b.tanggal DESC");
        $data = $stmt->fetchAll();
    }
    echo json_encode($data);
}
?>