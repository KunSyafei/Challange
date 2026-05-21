<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Data kehadiran per kegiatan
$stmt = $pdo->query("
    SELECT 
        jenis_kegiatan,
        SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
        COUNT(*) as total
    FROM kehadiran 
    GROUP BY jenis_kegiatan
");

$kehadiranData = [];
while ($row = $stmt->fetch()) {
    $kehadiranData['labels'][] = ucfirst($row['jenis_kegiatan']);
    $kehadiranData['data'][] = round(($row['hadir'] / $row['total']) * 100);
}

// Data nilai
$stmt = $pdo->query("
    SELECT 
        CASE 
            WHEN nilai >= 85 THEN 'A (Sangat Baik)'
            WHEN nilai >= 75 THEN 'B (Baik)'
            WHEN nilai >= 60 THEN 'C (Cukup)'
            ELSE 'D (Kurang)'
        END as grade,
        COUNT(*) as total
    FROM bina_jasmani
    GROUP BY grade
");

$nilaiData = ['labels' => [], 'data' => []];
while ($row = $stmt->fetch()) {
    $nilaiData['labels'][] = $row['grade'];
    $nilaiData['data'][] = $row['total'];
}

echo json_encode([
    'kehadiran' => $kehadiranData,
    'nilai' => $nilaiData
]);
?>