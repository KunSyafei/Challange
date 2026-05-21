<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO mentoring (materi, pengisi_materi, bahan_ajar, kebutuhan, tanggal, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([
        $_POST['materi'],
        $_POST['pengisi_materi'],
        $_POST['bahan_ajar'],
        $_POST['kebutuhan'],
        $_POST['tanggal'],
        $_SESSION['user_id']
    ]);
    echo json_encode(['success' => $result]);
} elseif ($method == 'GET') {
    $stmt = $pdo->query("SELECT m.*, u.full_name as creator FROM mentoring m LEFT JOIN users u ON m.created_by = u.id ORDER BY m.tanggal DESC");
    $data = $stmt->fetchAll();
    echo json_encode($data);
}
?>