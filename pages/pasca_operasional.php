<?php
require_once '../config/database.php';
if (!isLoggedIn()) redirect('../index.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasca Operasional - Monitoring Siswa</title>
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
            <a href="pasca_operasional.php" class="active"><i class="bi bi-tools"></i> Pasca Operasional</a>
            <a href="bina_jasmani.php"><i class="bi bi-heart"></i> Bina Jasmani</a>
            <a href="kehadiran.php"><i class="bi bi-check-circle"></i> Kehadiran</a>
            <a href="virtualisasi.php"><i class="bi bi-graph-up"></i> Virtualisasi</a>
            <a href="../api/auth.php?action=logout" class="logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="navbar-top">
            <h2>Pasca Operasional - Pengecekan Peralatan</h2>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Checklist Peralatan</h5>
            </div>
            <div class="card-body">
                <form id="peralatanForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Peralatan</label>
                            <input type="text" name="nama_peralatan" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Pengecekan</label>
                            <input type="date" name="tanggal_cek" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kondisi</label>
                            <select name="kondisi" class="form-control" required>
                                <option value="layak">Layak Pakai</option>
                                <option value="tidak_layak">Tidak Layak</option>
                                <option value="perbaikan">Butuh Perbaikan</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <input type="checkbox" name="checklist" value="1"> Sudah Diperiksa
                            </label>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Tambah Peralatan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Daftar Peralatan</h5>
            </div>
            <div class="card-body">
                <div id="daftarPeralatan"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            loadPeralatan();
            
            $('#peralatanForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '../api/pasca_operasional.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            alert('Peralatan berhasil ditambahkan!');
                            $('#peralatanForm')[0].reset();
                            loadPeralatan();
                        }
                    }
                });
            });
        });
        
        function loadPeralatan() {
            $.get('../api/pasca_operasional.php', function(data) {
                var html = '<table class="table table-bordered">';
                html += '<thead><tr><th>Peralatan</th><th>Tanggal Cek</th><th>Kondisi</th><th>Status</th><th>Keterangan</th><th>Aksi</th></tr></thead><tbody>';
                
                data.forEach(function(item) {
                    var kondisiBadge = {
                        'layak': '<span class="badge bg-success">Layak</span>',
                        'tidak_layak': '<span class="badge bg-danger">Tidak Layak</span>',
                        'perbaikan': '<span class="badge bg-warning">Perbaikan</span>'
                    }[item.kondisi];
                    
                    html += `<tr>
                        <td>${item.nama_peralatan}</td>
                        <td>${item.tanggal_cek}</td>
                        <td>${kondisiBadge}</td>
                        <td>${item.checklist ? '✓ Sudah dicek' : '✗ Belum dicek'}</td>
                        <td>${item.keterangan || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="deletePeralatan(${item.id})">Hapus</button>
                        </td>
                    </tr>`;
                });
                
                html += '</tbody></table>';
                $('#daftarPeralatan').html(html || '<p>Belum ada data peralatan</p>');
            });
        }
        
        function deletePeralatan(id) {
            if (confirm('Yakin ingin menghapus data peralatan ini?')) {
                $.ajax({
                    url: '../api/pasca_operasional.php',
                    type: 'POST',
                    data: {_method: 'DELETE', id: id},
                    success: function(response) {
                        if (response.success) {
                            alert('Data berhasil dihapus!');
                            loadPeralatan();
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>