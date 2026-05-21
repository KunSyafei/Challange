<?php
require_once '../config/database.php';
if (!isLoggedIn()) redirect('../index.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kehadiran - Monitoring Siswa</title>
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
            <a href="bina_jasmani.php"><i class="bi bi-heart"></i> Bina Jasmani</a>
            <a href="kehadiran.php" class="active"><i class="bi bi-check-circle"></i> Kehadiran</a>
            <a href="virtualisasi.php"><i class="bi bi-graph-up"></i> Virtualisasi</a>
            <a href="../api/auth.php?action=logout" class="logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="navbar-top">
            <h2>Input Kehadiran</h2>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Form Kehadiran</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Jenis Kegiatan</label>
                        <select id="jenis_kegiatan" class="form-control">
                            <option value="rabuan">Rabuan</option>
                            <option value="mentoring">Mentoring</option>
                            <option value="bina_jasmani">Bina Jasmani</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" id="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-primary d-block" onclick="loadKehadiran()">Tampilkan</button>
                    </div>
                </div>
                
                <div id="kehadiranTable"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            loadKehadiran();
        });
        
        function loadKehadiran() {
            var jenis = $('#jenis_kegiatan').val();
            var tanggal = $('#tanggal').val();
            
            $.get(`../api/kehadiran.php?jenis=${jenis}&tanggal=${tanggal}`, function(data) {
                var html = '<form id="kehadiranForm">';
                html += '<table class="table table-bordered">';
                html += '<thead><tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>Status</th><th>Keterangan</th></tr></thead><tbody>';
                
                data.semua_siswa.forEach(function(siswa) {
                    var kehadiran = data.kehadiran.find(k => k.siswa_id == siswa.id);
                    var status = kehadiran ? kehadiran.status : 'hadir';
                    var keterangan = kehadiran ? kehadiran.keterangan : '';
                    
                    html += `<tr>
                        <td>${siswa.nis}</td>
                        <td>${siswa.nama}</td>
                        <td>${siswa.kelas}</td>
                        <td>
                            <select name="status_${siswa.id}" class="form-control">
                                <option value="hadir" ${status == 'hadir' ? 'selected' : ''}>Hadir</option>
                                <option value="izin" ${status == 'izin' ? 'selected' : ''}>Izin</option>
                                <option value="sakit" ${status == 'sakit' ? 'selected' : ''}>Sakit</option>
                                <option value="alpha" ${status == 'alpha' ? 'selected' : ''}>Alpha</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="keterangan_${siswa.id}" class="form-control" value="${keterangan}">
                            <input type="hidden" name="siswa_id[]" value="${siswa.id}">
                        </td>
                    </tr>`;
                });
                
                html += '</tbody></table>';
                html += '<button type="submit" class="btn btn-primary mt-3">Simpan Semua</button>';
                html += '</form>';
                
                $('#kehadiranTable').html(html);
                
                $('#kehadiranForm').on('submit', function(e) {
                    e.preventDefault();
                    var formData = $(this).serializeArray();
                    var promises = [];
                    
                    formData.forEach(function(item) {
                        if (item.name.startsWith('status_')) {
                            var siswaId = item.name.replace('status_', '');
                            var status = item.value;
                            var keterangan = $('input[name="keterangan_' + siswaId + '"]').val();
                            
                            promises.push($.ajax({
                                url: '../api/kehadiran.php',
                                type: 'POST',
                                data: {
                                    siswa_id: siswaId,
                                    jenis_kegiatan: jenis,
                                    tanggal: tanggal,
                                    status: status,
                                    keterangan: keterangan
                                }
                            }));
                        }
                    });
                    
                    Promise.all(promises).then(function() {
                        alert('Kehadiran berhasil disimpan!');
                        loadKehadiran();
                    });
                });
            });
        }
    </script>
</body>
</html>