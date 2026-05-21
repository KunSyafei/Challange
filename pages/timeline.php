<?php
require_once '../config/database.php';
if (!isLoggedIn()) redirect('../index.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timeline Kegiatan - Monitoring Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .timeline-item {
            border-left: 3px solid #667eea;
            padding-left: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 0;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: #667eea;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-planned { background: #e0e7ff; color: #3730a3; }
        .status-ongoing { background: #fef3c7; color: #92400e; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h4>Monitoring Siswa</h4></div>
        <div class="sidebar-menu">
            <a href="../dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="notulensi.php"><i class="bi bi-file-text"></i> Notulensi</a>
            <a href="mentoring.php"><i class="bi bi-people"></i> Mentoring</a>
            <a href="timeline.php" class="active"><i class="bi bi-calendar"></i> Timeline</a>
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
            <h2>Timeline Kegiatan</h2>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Tambah Kegiatan</h5>
                <button class="btn btn-sm btn-primary" onclick="showForm()">+ Tambah Baru</button>
            </div>
            <div class="card-body">
                <div id="formTimeline" style="display:none;" class="mb-4">
                    <form id="timelineForm">
                        <input type="hidden" id="kegiatan_id" name="id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Kegiatan</label>
                                <input type="text" name="kegiatan" id="kegiatan" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lokasi</label>
                                <input type="text" name="lokasi" id="lokasi" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="datetime-local" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="datetime-local" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="planned">Direncanakan</option>
                                    <option value="ongoing">Berlangsung</option>
                                    <option value="completed">Selesai</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <button type="button" class="btn btn-secondary" onclick="hideForm()">Batal</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div id="daftarTimeline"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            loadTimeline();
            
            $('#timelineForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var id = $('#kegiatan_id').val();
                var url = '../api/timeline.php';
                
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
                            loadTimeline();
                        }
                    }
                });
            });
        });
        
        function loadTimeline() {
            $.get('../api/timeline.php', function(data) {
                var html = '<div class="timeline">';
                
                data.forEach(function(kegiatan) {
                    var statusClass = `status-${kegiatan.status}`;
                    var statusText = {
                        'planned': 'Direncanakan',
                        'ongoing': 'Berlangsung',
                        'completed': 'Selesai',
                        'cancelled': 'Dibatalkan'
                    }[kegiatan.status];
                    
                    html += `
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5>${kegiatan.kegiatan}</h5>
                                    <p class="text-muted">${kegiatan.lokasi || 'Lokasi belum ditentukan'}</p>
                                </div>
                                <div>
                                    <span class="status-badge ${statusClass}">${statusText}</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> ${new Date(kegiatan.tanggal_mulai).toLocaleString()} - ${new Date(kegiatan.tanggal_selesai).toLocaleString()}
                                </small>
                            </div>
                            <p class="mt-2">${kegiatan.deskripsi || 'Tidak ada deskripsi'}</p>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-warning" onclick="editKegiatan(${kegiatan.id})">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteKegiatan(${kegiatan.id})">Hapus</button>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                $('#daftarTimeline').html(html || '<p>Belum ada kegiatan</p>');
            });
        }
        
        function showForm() {
            $('#formTimeline').show();
            $('#kegiatan_id').val('');
            $('#kegiatan').val('');
            $('#lokasi').val('');
            $('#tanggal_mulai').val('');
            $('#tanggal_selesai').val('');
            $('#status').val('planned');
            $('#deskripsi').val('');
        }
        
        function hideForm() {
            $('#formTimeline').hide();
        }
        
        function editKegiatan(id) {
            $.get(`../api/timeline.php?id=${id}`, function(data) {
                $('#kegiatan_id').val(data.id);
                $('#kegiatan').val(data.kegiatan);
                $('#lokasi').val(data.lokasi);
                $('#tanggal_mulai').val(data.tanggal_mulai.replace(' ', 'T'));
                $('#tanggal_selesai').val(data.tanggal_selesai.replace(' ', 'T'));
                $('#status').val(data.status);
                $('#deskripsi').val(data.deskripsi);
                $('#formTimeline').show();
            });
        }
        
        function deleteKegiatan(id) {
            if (confirm('Yakin ingin menghapus kegiatan ini?')) {
                $.ajax({
                    url: '../api/timeline.php',
                    type: 'POST',
                    data: {_method: 'DELETE', id: id},
                    success: function(response) {
                        if (response.success) {
                            alert('Kegiatan berhasil dihapus!');
                            loadTimeline();
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>