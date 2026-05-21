<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode([]);
    exit();
}

// Get all students
$stmt = $pdo->query("SELECT * FROM siswa");
$students = $stmt->fetchAll();

$result = [];

foreach ($students as $student) {
    // Get kehadiran data
    $stmt = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN jenis_kegiatan = 'rabuan' AND status = 'hadir' THEN 1 ELSE 0 END) as rabuan_hadir,
            SUM(CASE WHEN jenis_kegiatan = 'mentoring' AND status = 'hadir' THEN 1 ELSE 0 END) as mentoring_hadir,
            SUM(CASE WHEN jenis_kegiatan = 'bina_jasmani' AND status = 'hadir' THEN 1 ELSE 0 END) as bina_hadir,
            COUNT(CASE WHEN jenis_kegiatan = 'rabuan' THEN 1 END) as rabuan_total,
            COUNT(CASE WHEN jenis_kegiatan = 'mentoring' THEN 1 END) as mentoring_total,
            COUNT(CASE WHEN jenis_kegiatan = 'bina_jasmani' THEN 1 END) as bina_total
        FROM kehadiran 
        WHERE siswa_id = ?
    ");
    $stmt->execute([$student['id']]);
    $kehadiran = $stmt->fetch();
    
    // Get bina jasmani data
    $stmt = $pdo->prepare("SELECT AVG(nilai) as rata_nilai, AVG(standarisasi_nilai) as rata_standarisasi FROM bina_jasmani WHERE siswa_id = ?");
    $stmt->execute([$student['id']]);
    $nilai = $stmt->fetch();
    
    $result[] = [
        'id' => $student['id'],
        'nis' => $student['nis'],
        'nama' => $student['nama'],
        'kelas' => $student['kelas'],
        'kehadiran' => [
            'rabuan' => $kehadiran['rabuan_total'] > 0 ? round(($kehadiran['rabuan_hadir'] / $kehadiran['rabuan_total']) * 100) : 0,
            'mentoring' => $kehadiran['mentoring_total'] > 0 ? round(($kehadiran['mentoring_hadir'] / $kehadiran['mentoring_total']) * 100) : 0,
            'bina_jasmani' => $kehadiran['bina_total'] > 0 ? round(($kehadiran['bina_hadir'] / $kehadiran['bina_total']) * 100) : 0
        ],
        'kehadiran_persen' => 0,
        'nilai_rata' => round($nilai['rata_nilai'] ?? 0),
        'standarisasi' => round($nilai['rata_standarisasi'] ?? 75),
        'partisipasi' => rand(60, 100)
    ];
    
    $result[count($result)-1]['kehadiran_persen'] = round(($result[count($result)-1]['kehadiran']['rabuan'] + 
                                                           $result[count($result)-1]['kehadiran']['mentoring'] + 
                                                           $result[count($result)-1]['kehadiran']['bina_jasmani']) / 3);
}

echo json_encode($result);
?>