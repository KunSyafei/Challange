<?php
require_once '../config/database.php';
if (!isLoggedIn()) redirect('../index.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentoring - Monitoring Siswa</title>
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
            <a href="mentoring.php" class="active"><i class="bi bi-people"></i> Mentoring</a>
            <a href="timeline.php"><i class="bi bi-calendar"></i> Timeline</a>
            <a href="pra_operasional.php"><i class="bi bi-clipboard-check"></i> Pra Operasional</a>
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
            <h2>Mentoring</h2>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Tambah/Edit Mentoring</h5>
                <button class="btn btn-sm btn-primary" onclick="showForm()">+ Tambah Baru</button>
            </div>
            <div class="card-body">
                <div id="formMentoring" style="display:none;" class="mb-4">
                    <form id="mentoringForm">
                        <input type="hidden" id="mentoring_id" name="id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Materi</label>
                                <input type="text" name="materi" id="materi" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pengisi Materi</label>
                                <input type="text" name="pengisi_materi" id="pengisi_materi" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Bahan Ajar</label>
                                <textarea name="bahan_ajar" id="bahan_ajar" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Kebutuhan Selama Materi</label>
                                <textarea name="kebutuhan" id="kebutuhan" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <button type="button" class="btn btn-secondary" onclick="hideForm()">Batal</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div id="daftarMentoring"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            loadMentoring();
            
            $('#mentoringForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var id = $('#mentoring_id').val();
                var url = '../api/mentoring.php';
                var method = 'POST';
                
                if (id) {
                    formData += '&_method=PUT&id=' + id;
                }
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            alert('Data berhasil disimpan!');
                            hideForm();
                            loadMentoring();
                        }
                    }
                });
            });
        });
        
        function loadMentoring() {
            $.get('../api/mentoring.php', function(data) {
                var html = '<table class="table table-bordered">';
                html += '<thead><tr><th>Materi</th><th>Pengisi</th><th>Tanggal</th><th>Bahan Ajar</th><th>Kebutuhan</th><th>Aksi</th></tr></thead><tbody>';
                
                data.forEach(function(m) {
                    html += `<tr>
                        <td>${m.materi}</td>
                        <td>${m.pengisi_materi}</td>
                        <td>${m.tanggal}</td>
                        <td>${m.bahan_ajar || '-'}</td>
                        <td>${m.kebutuhan || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editMentoring(${m.id})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteMentoring(${m.id})">Hapus</button>
                        </td>
                    </tr>`;
                });
                
                html += '</tbody></table>';
                $('#daftarMentoring').html(html || '<p>Belum ada data mentoring</p>');
            });
        }
        
        function showForm() {
            $('#formMentoring').show();
            $('#mentoring_id').val('');
            $('#materi').val('');
            $('#pengisi_materi').val('');
            $('#tanggal').val('');
            $('#bahan_ajar').val('');
            $('#kebutuhan').val('');
        }
        
        function hideForm() {
            $('#formMentoring').hide();
        }
        
        function editMentoring(id) {
            $.get(`../api/mentoring.php?id=${id}`, function(data) {
                $('#mentoring_id').val(data.id);
                $('#materi').val(data.materi);
                $('#pengisi_materi').val(data.pengisi_materi);
                $('#tanggal').val(data.tanggal);
                $('#bahan_ajar').val(data.bahan_ajar);
                $('#kebutuhan').val(data.kebutuhan);
                $('#formMentoring').show();
            });
        }
        
        function deleteMentoring(id) {
            if (confirm('Yakin ingin menghapus?')) {
                $.ajax({
                    url: '../api/mentoring.php',
                    type: 'POST',
                    data: {_method: 'DELETE', id: id},
                    success: function(response) {
                        if (response.success) {
                            alert('Data berhasil dihapus!');
                            loadMentoring();
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>