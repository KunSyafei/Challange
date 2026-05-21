<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO pasca_operasional (nama_peralatan, kondisi, checklist, keterangan, tanggal_cek, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([
        $_POST['nama_peralatan'],
        $_POST['kondisi'],
        isset($_POST['checklist']) ? 1 : 0,
        $_POST['keterangan'],
        $_POST['tanggal_cek'],
        $_SESSION['user_id']
    ]);
    echo json_encode(['success' => $result]);
} elseif ($method == 'GET') {
    if (isset($_GET['tanggal'])) {
        $stmt = $pdo->prepare("SELECT * FROM pasca_operasional WHERE tanggal_cek = ?");
        $stmt->execute([$_GET['tanggal']]);
        $data = $stmt->fetchAll();
    } else {
        $stmt = $pdo->query("SELECT p.*, u.full_name as checker FROM pasca_operasional p LEFT JOIN users u ON p.created_by = u.id ORDER BY p.tanggal_cek DESC");
        $data = $stmt->fetchAll();
    }
    echo json_encode($data);
} elseif ($method == 'PUT') {
    parse_str(file_get_contents("php://input"), $put);
    $stmt = $pdo->prepare("UPDATE pasca_operasional SET kondisi = ?, checklist = ?, keterangan = ? WHERE id = ?");
    $result = $stmt->execute([
        $put['kondisi'],
        isset($put['checklist']) ? 1 : 0,
        $put['keterangan'],
        $put['id']
    ]);
    echo json_encode(['success' => $result]);
} elseif ($method == 'DELETE') {
    parse_str(file_get_contents("php://input"), $delete);
    $stmt = $pdo->prepare("DELETE FROM pasca_operasional WHERE id = ?");
    $result = $stmt->execute([$delete['id']]);
    echo json_encode(['success' => $result]);
}
?>