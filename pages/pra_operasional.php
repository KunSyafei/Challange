<?php
require_once '../config/database.php';
if (!isLoggedIn()) redirect('../index.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pra Operasional - Monitoring Siswa</title>
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
            <a href="pra_operasional.php" class="active"><i class="bi bi-clipboard-check"></i> Pra Operasional</a>
            <a href="operasional.php"><i class="bi bi-play-circle"></i> Operasional</a>
            <a href="pasca_operasional.php"><i class="bi bi-tools"></i> Pasca Operasional</a>
            <a href="bina_jasmani.php"><i class="bi bi-heart"></i> Bina Jasmani</a>
            <a href="kehadiran.php"><i class="bi bi-check-circle"></i> Kehadiran</a>
            <a href="virtualisasi.php"><i class="bi bi-graph-up"></i> Virtualisasi</a>
            <a href="../api/auth.php?action=logout" class="logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="navbar-top">
            <h2>Pra Operasional</h2>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Persiapan Pra Operasional</h5>
            </div>
            <div class="card-body">
                <form id="praOperasionalForm">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Perencanaan</label>
                            <textarea name="perencanaan" id="perencanaan" class="form-control" rows="4" placeholder="Rencana kegiatan yang akan dilaksanakan..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Perbekalan Regu</label>
                            <textarea name="perbekalan_regu" id="perbekalan_regu" class="form-control" rows="3" placeholder="Makanan, minuman, dll"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Peralatan Pribadi</label>
                            <textarea name="peralatan_pribadi" id="peralatan_pribadi" class="form-control" rows="3" placeholder="Peralatan yang dibawa masing-masing siswa"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Peralatan Regu</label>
                            <textarea name="peralatan_regu" id="peralatan_regu" class="form-control" rows="3" placeholder="Peralatan yang digunakan bersama"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Perbekalan</label>
                            <textarea name="perbekalan" id="perbekalan" class="form-control" rows="3" placeholder="Perbekalan tambahan"></textarea>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Simpan Data</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Data Informasi Siswa</h5>
                <button class="btn btn-sm btn-primary" onclick="loadSiswaData()">Refresh</button>
            </div>
            <div class="card-body">
                <div id="siswaInfo"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            loadPraOperasional();
            loadSiswaData();
            
            $('#praOperasionalForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '../api/pra_operasional.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            alert('Data pra operasional berhasil disimpan!');
                            loadPraOperasional();
                        }
                    }
                });
            });
        });
        
        function loadPraOperasional() {
            var tanggal = $('#tanggal').val();
            $.get(`../api/pra_operasional.php?tanggal=${tanggal}`, function(data) {
                if (data) {
                    $('#perencanaan').val(data.perencanaan || '');
                    $('#perbekalan_regu').val(data.perbekalan_regu || '');
                    $('#peralatan_pribadi').val(data.peralatan_pribadi || '');
                    $('#peralatan_regu').val(data.peralatan_regu || '');
                    $('#perbekalan').val(data.perbekalan || '');
                }
            });
        }
        
        function loadSiswaData() {
            $.get('../api/virtualisasi.php', function(data) {
                var html = '<table class="table table-bordered">';
                html += '<thead><tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>JK</th><th>Status Kehadiran</th><th>Rata-rata Nilai</th></tr></thead><tbody>';
                
                data.forEach(function(siswa) {
                    html += `<tr>
                        <td>${siswa.nis}</td>
                        <td>${siswa.nama}</td>
                        <td>${siswa.kelas}</td>
                        <td>${siswa.jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan'}</td>
                        <td>${siswa.kehadiran_persen}%</td>
                        <td>${siswa.nilai_rata}</td>
                    </tr>`;
                });
                
                html += '</tbody></table>';
                $('#siswaInfo').html(html);
            });
        }
    </script>
</body>
</html>