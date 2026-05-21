<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    // Cek apakah sudah ada
    $stmt = $pdo->prepare("SELECT id FROM kehadiran WHERE siswa_id = ? AND jenis_kegiatan = ? AND tanggal = ?");
    $stmt->execute([$_POST['siswa_id'], $_POST['jenis_kegiatan'], $_POST['tanggal']]);
    
    if ($stmt->fetch()) {
        // Update
        $stmt = $pdo->prepare("UPDATE kehadiran SET status = ?, keterangan = ? WHERE siswa_id = ? AND jenis_kegiatan = ? AND tanggal = ?");
        $result = $stmt->execute([
            $_POST['status'],
            $_POST['keterangan'],
            $_POST['siswa_id'],
            $_POST['jenis_kegiatan'],
            $_POST['tanggal']
        ]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO kehadiran (siswa_id, jenis_kegiatan, tanggal, status, keterangan) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $_POST['siswa_id'],
            $_POST['jenis_kegiatan'],
            $_POST['tanggal'],
            $_POST['status'],
            $_POST['keterangan']
        ]);
    }
    echo json_encode(['success' => $result]);
} elseif ($method == 'GET') {
    $jenis = $_GET['jenis'] ?? '';
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
    
    $sql = "SELECT k.*, s.nama, s.nis, s.kelas 
            FROM kehadiran k 
            JOIN siswa s ON k.siswa_id = s.id 
            WHERE k.tanggal = ?";
    $params = [$tanggal];
    
    if ($jenis) {
        $sql .= " AND k.jenis_kegiatan = ?";
        $params[] = $jenis;
    }
    
    $sql .= " ORDER BY s.nama";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll();
    
    // Get all students for reference
    $stmtAll = $pdo->query("SELECT * FROM siswa ORDER BY nama");
    $allSiswa = $stmtAll->fetchAll();
    
    echo json_encode([
        'kehadiran' => $data,
        'semua_siswa' => $allSiswa
    ]);
}
?>