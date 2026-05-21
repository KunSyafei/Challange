<?php
require_once '../config/database.php';
require_once '../config/google_drive.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    if (isset($_FILES['file']) && isset($_POST['judul']) && isset($_POST['tanggal_kegiatan'])) {
        $upload = uploadToDrive($_FILES['file']['tmp_name'], $_FILES['file']['name']);
        
        if ($upload['success']) {
            $stmt = $pdo->prepare("INSERT INTO operasional_kegiatan (judul, file_path, drive_link, tanggal_kegiatan, uploaded_by) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $_POST['judul'],
                $upload['path'],
                $upload['drive_link'],
                $_POST['tanggal_kegiatan'],
                $_SESSION['user_id']
            ]);
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Upload failed']);
        }
    }
} elseif ($method == 'GET') {
    $stmt = $pdo->query("SELECT o.*, u.full_name as uploader FROM operasional_kegiatan o LEFT JOIN users u ON o.uploaded_by = u.id ORDER BY o.tanggal_kegiatan DESC");
    $data = $stmt->fetchAll();
    echo json_encode($data);
}
?>