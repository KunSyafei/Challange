<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO timeline_kegiatan (kegiatan, deskripsi, tanggal_mulai, tanggal_selesai, lokasi, status) VALUES (?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([
        $_POST['kegiatan'],
        $_POST['deskripsi'],
        $_POST['tanggal_mulai'],
        $_POST['tanggal_selesai'],
        $_POST['lokasi'],
        $_POST['status']
    ]);
    echo json_encode(['success' => $result, 'id' => $pdo->lastInsertId()]);
} elseif ($method == 'GET') {
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM timeline_kegiatan WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $data = $stmt->fetch();
    } else {
        $stmt = $pdo->query("SELECT * FROM timeline_kegiatan ORDER BY tanggal_mulai DESC");
        $data = $stmt->fetchAll();
    }
    echo json_encode($data);
} elseif ($method == 'PUT') {
    parse_str(file_get_contents("php://input"), $put);
    $stmt = $pdo->prepare("UPDATE timeline_kegiatan SET kegiatan = ?, deskripsi = ?, tanggal_mulai = ?, tanggal_selesai = ?, lokasi = ?, status = ? WHERE id = ?");
    $result = $stmt->execute([
        $put['kegiatan'],
        $put['deskripsi'],
        $put['tanggal_mulai'],
        $put['tanggal_selesai'],
        $put['lokasi'],
        $put['status'],
        $put['id']
    ]);
    echo json_encode(['success' => $result]);
} elseif ($method == 'DELETE') {
    parse_str(file_get_contents("php://input"), $delete);
    $stmt = $pdo->prepare("DELETE FROM timeline_kegiatan WHERE id = ?");
    $result = $stmt->execute([$delete['id']]);
    echo json_encode(['success' => $result]);
}
?>