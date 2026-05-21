<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    // Cek apakah sudah ada untuk tanggal ini
    $stmt = $pdo->prepare("SELECT id FROM pra_operasional WHERE tanggal = ?");
    $stmt->execute([$_POST['tanggal']]);
    
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE pra_operasional SET perencanaan = ?, perbekalan_regu = ?, peralatan_pribadi = ?, peralatan_regu = ?, perbekalan = ? WHERE tanggal = ?");
        $result = $stmt->execute([
            $_POST['perencanaan'],
            $_POST['perbekalan_regu'],
            $_POST['peralatan_pribadi'],
            $_POST['peralatan_regu'],
            $_POST['perbekalan'],
            $_POST['tanggal']
        ]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO pra_operasional (perencanaan, perbekalan_regu, peralatan_pribadi, peralatan_regu, perbekalan, tanggal, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $_POST['perencanaan'],
            $_POST['perbekalan_regu'],
            $_POST['peralatan_pribadi'],
            $_POST['peralatan_regu'],
            $_POST['perbekalan'],
            $_POST['tanggal'],
            $_SESSION['user_id']
        ]);
    }
    echo json_encode(['success' => $result]);
} elseif ($method == 'GET') {
    if (isset($_GET['tanggal'])) {
        $stmt = $pdo->prepare("SELECT * FROM pra_operasional WHERE tanggal = ?");
        $stmt->execute([$_GET['tanggal']]);
        $data = $stmt->fetch();
    } else {
        $stmt = $pdo->query("SELECT * FROM pra_operasional ORDER BY tanggal DESC LIMIT 10");
        $data = $stmt->fetchAll();
    }
    echo json_encode($data);
}
?>