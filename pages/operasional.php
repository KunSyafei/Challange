<?php
require_once '../config/database.php';
if (!isLoggedIn()) redirect('../index.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operasional Kegiatan - Monitoring Siswa</title>
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
            <a href="operasional.php" class="active"><i class="bi bi-play-circle"></i> Operasional</a>
            <a href="pasca_operasional.php"><i class="bi bi-tools"></i> Pasca Operasional</a>
            <a href="bina_jasmani.php"><i class="bi bi-heart"></i> Bina Jasmani</a>
            <a href="kehadiran.php"><i class="bi bi-check-circle"></i> Kehadiran</a>
            <a href="virtualisasi.php"><i class="bi bi-graph-up"></i> Virtualisasi</a>
            <a href="../api/auth.php?action=logout" class="logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="navbar-top">
            <h2>Operasional Kegiatan</h2>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Upload Laporan Operasional (PDF)</h5>
            </div>
            <div class="card-body">
                <form id="formOperasional" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Judul Laporan</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kegiatan</label>
                        <input type="date" name="tanggal_kegiatan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File PDF</label>
                        <input type="file" name="file" class="form-control" accept=".pdf" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload ke Drive</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Daftar Laporan Operasional</h5>
            </div>
            <div class="card-body">
                <div id="daftarOperasional"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            loadOperasional();
            
            $('#formOperasional').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                
                $.ajax({
                    url: '../api/operasional.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            alert('Upload berhasil!');
                            loadOperasional();
                            $('#formOperasional')[0].reset();
                        } else {
                            alert('Upload gagal!');
                        }
                    }
                });
            });
        });
        
        function loadOperasional() {
            $.get('../api/operasional.php', function(data) {
                var html = '<table class="table table-bordered">';
                html += '<thead><tr><th>Judul</th><th>Tanggal</th><th>Uploader</th><th>File</th></thead><tbody>';
                
                data.forEach(function(ops) {
                    html += `<tr>
                        <td>${ops.judul}</td>
                        <td>${ops.tanggal_kegiatan}</td>
                        <td>${ops.uploader || '-'}</td>
                        <td><a href="${ops.drive_link}" target="_blank" class="btn btn-sm btn-primary">Lihat PDF</a></td>
                    </tr>`;
                });
                
                html += '</tbody></table>';
                $('#daftarOperasional').html(html || '<p>Belum ada laporan operasional</p>');
            });
        }
    </script>
</body>
</html>