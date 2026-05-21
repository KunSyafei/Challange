<?php
require_once '../config/database.php';
if (!isLoggedIn()) redirect('../index.php');

// Get all students for dropdown
$stmt = $pdo->query("SELECT * FROM siswa ORDER BY nama");
$siswa = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bina Jasmani - Monitoring Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h4>Monitoring Siswa</h4></div>
        <div class="sidebar-menu">
            <a href="../dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="notulensi.php"><i class="bi bi-file-text"></i> Notulensi</a>
            <a href="mentoring.php"><i class="bi bi-people"></i> Mentoring</a>
            <a href="timeline.php"><i class="bi bi-calendar"></i> Timeline</a>
            <a href="pra_operasional.php"><i class="bi bi-clipboard-check"></i> Pra Operasional</a>
            <a href="operasional.php"><i class="bi bi-play-circle"></i> Operasional</a>
            <a href="pasca_operasional.php"><i class="bi bi-tools"></i> Pasca Operasional</a>
            <a href="bina_jasmani.php" class="active"><i class="bi bi-heart"></i> Bina Jasmani</a>
            <a href="kehadiran.php"><i class="bi bi-check-circle"></i> Kehadiran</a>
            <a href="virtualisasi.php"><i class="bi bi-graph-up"></i> Virtualisasi</a>
            <a href="../api/auth.php?action=logout" class="logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="navbar-top">
            <h2>Bina Jasmani</h2>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Input Nilai Latihan Fisik</h5>
            </div>
            <div class="card-body">
                <form id="nilaiForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Siswa</label>
                            <select name="siswa_id" class="form-control" required>
                                <option value="">Pilih Siswa</option>
                                <?php foreach($siswa as $s): ?>
                                <option value="<?php echo $s['id']; ?>"><?php echo $s['nama'] . ' (' . $s['nis'] . ')'; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Latihan</label>
                            <input type="text" name="latihan_fisik" class="form-control" placeholder="Contoh: Lari 100m, Push Up, dll" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nilai (0-100)</label>
                            <input type="number" name="nilai" class="form-control" min="0" max="100" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Riwayat Nilai Bina Jasmani</h5>
            </div>
            <div class="card-body">
                <div id="riwayatNilai"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            loadRiwayat();
            
            $('#nilaiForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '../api/bina_jasmani.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            alert('Nilai berhasil disimpan!');
                            $('#nilaiForm')[0].reset();
                            loadRiwayat();
                        } else {
                            alert('Gagal menyimpan nilai!');
                        }
                    }
                });
            });
        });
        
        function loadRiwayat() {
            $.get('../api/bina_jasmani.php', function(data) {
                var html = '<table class="table table-bordered">';
                html += '<thead><tr><th>Siswa</th><th>Latihan</th><th>Nilai</th><th>Standarisasi</th><th>Status</th><th>Tanggal</th><th>Catatan</th></thead><tbody>';
                
                data.forEach(function(nilai) {
                    var status = nilai.nilai >= nilai.standarisasi_nilai ? 
                        '<span class="badge bg-success">Lulus</span>' : 
                        '<span class="badge bg-danger">Perbaikan</span>';
                    
                    html += `<tr>
                        <td>${nilai.nama} (${nilai.nis})</td>
                        <td>${nilai.latihan_fisik}</td>
                        <td><strong>${nilai.nilai}</strong></td>
                        <td>${nilai.standarisasi_nilai}</td>
                        <td>${status}</td>
                        <td>${nilai.tanggal}</td>
                        <td>${nilai.catatan || '-'}</td>
                    </tr>`;
                });
                
                html += '</tbody></table>';
                $('#riwayatNilai').html(html || '<p>Belum ada data nilai</p>');
            });
        }
    </script>
</body>
</html>