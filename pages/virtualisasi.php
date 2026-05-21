<?php
require_once '../config/database.php';
if (!isLoggedIn()) redirect('../index.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtualisasi Data Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .student-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .student-card:hover {
            transform: translateY(-5px);
        }
        .student-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 32px;
            color: white;
            font-weight: bold;
        }
        .nilai-standar { color: #10b981; font-weight: bold; }
        .nilai-dibawah { color: #ef4444; }
        .progress-bar-custom {
            height: 8px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 4px;
            transition: width 0.3s;
        }
        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
        .badge-lulus { background: #d1fae5; color: #065f46; }
        .badge-bimbingan { background: #fee2e2; color: #991b1b; }
    </style>
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
            <a href="kehadiran.php"><i class="bi bi-check-circle"></i> Kehadiran</a>
            <a href="virtualisasi.php" class="active"><i class="bi bi-graph-up"></i> Virtualisasi</a>
            <a href="../api/auth.php?action=logout" class="logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="navbar-top">
            <h2>Virtualisasi Data Siswa</h2>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Filter Data</div>
                    <div class="card-body">
                        <select id="filterKelas" class="form-control">
                            <option value="">Semua Kelas</option>
                            <option value="XII RPL 1">XII RPL 1</option>
                            <option value="XII RPL 2">XII RPL 2</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Rekap Keseluruhan</div>
                    <div class="card-body" id="rekapData"></div>
                </div>
            </div>
        </div>

        <div id="virtualisasiContainer"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            loadVirtualisasi();
            
            $('#filterKelas').on('change', function() {
                loadVirtualisasi();
            });
        });
        
        function loadVirtualisasi() {
            $.ajax({
                url: '../api/virtualisasi.php',
                type: 'GET',
                success: function(data) {
                    var filterKelas = $('#filterKelas').val();
                    if (filterKelas) {
                        data = data.filter(s => s.kelas == filterKelas);
                    }
                    displayVirtualisasi(data);
                    displayRekap(data);
                }
            });
        }
        
        function displayRekap(data) {
            var totalSiswa = data.length;
            var rataKehadiran = data.reduce((sum, s) => sum + s.kehadiran_persen, 0) / totalSiswa;
            var rataNilai = data.reduce((sum, s) => sum + s.nilai_rata, 0) / totalSiswa;
            var lulusCount = data.filter(s => s.status == 'Lulus').length;
            
            var html = `
                <div class="row text-center">
                    <div class="col-4">
                        <h5>Total Siswa</h5>
                        <h3>${totalSiswa}</h3>
                    </div>
                    <div class="col-4">
                        <h5>Rata Kehadiran</h5>
                        <h3>${Math.round(rataKehadiran)}%</h3>
                    </div>
                    <div class="col-4">
                        <h5>Rata Nilai</h5>
                        <h3>${Math.round(rataNilai)}</h3>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="progress">
                            <div class="progress-bar-custom" style="width: ${(lulusCount/totalSiswa)*100}%"></div>
                        </div>
                        <small>${lulusCount} dari ${totalSiswa} siswa lulus standarisasi</small>
                    </div>
                </div>
            `;
            $('#rekapData').html(html);
        }
        
        function displayVirtualisasi(data) {
            var html = '<div class="row">';
            
            data.forEach(function(student) {
                var statusClass = student.status == 'Lulus' ? 'badge-lulus' : 'badge-bimbingan';
                var nilaiClass = student.nilai_rata >= student.standarisasi ? 'nilai-standar' : 'nilai-dibawah';
                
                html += `
                    <div class="col-md-6 col-lg-4">
                        <div class="student-card">
                            <div class="student-avatar">
                                ${student.nama.charAt(0)}
                            </div>
                            <div class="text-center">
                                <h4>${student.nama}</h4>
                                <p class="text-muted">NIS: ${student.nis} | Kelas: ${student.kelas}</p>
                                <span class="badge-status ${statusClass}">${student.status}</span>
                            </div>
                            
                            <hr>
                            
                            <h6>Kehadiran</h6>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Rabuan</span>
                                    <span>${student.kehadiran.rabuan}%</span>
                                </div>
                                <div class="progress mb-2">
                                    <div class="progress-bar-custom" style="width: ${student.kehadiran.rabuan}%"></div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <span>Mentoring</span>
                                    <span>${student.kehadiran.mentoring}%</span>
                                </div>
                                <div class="progress mb-2">
                                    <div class="progress-bar-custom" style="width: ${student.kehadiran.mentoring}%"></div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <span>Bina Jasmani</span>
                                    <span>${student.kehadiran.bina_jasmani}%</span>
                                </div>
                                <div class="progress mb-2">
                                    <div class="progress-bar-custom" style="width: ${student.kehadiran.bina_jasmani}%"></div>
                                </div>
                            </div>
                            
                            <h6>Nilai Bina Jasmani</h6>
                            <div class="text-center mb-3">
                                <h3 class="${nilaiClass}">${student.nilai_rata}</h3>
                                <small>Standarisasi: ${student.standarisasi}</small>
                                <p class="mt-2"><small>Total Latihan: ${student.total_latihan} kali</small></p>
                            </div>
                            
                            <h6>Partisipasi Mentoring</h6>
                            <div class="progress mb-3">
                                <div class="progress-bar-custom" style="width: ${student.partisipasi_mentoring}%"></div>
                            </div>
                            <div class="text-center">
                                <small>${student.partisipasi_mentoring}% dari total mentoring</small>
                            </div>
                            
                            <canvas id="chart_${student.id}" style="height: 200px; margin-top: 15px;"></canvas>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            $('#virtualisasiContainer').html(html);
            
            // Render charts for each student
            data.forEach(function(student) {
                var ctx = document.getElementById(`chart_${student.id}`).getContext('2d');
                new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: ['Kehadiran', 'Nilai Jasmani', 'Partisipasi'],
                        datasets: [{
                            label: student.nama,
                            data: [
                                student.kehadiran_persen,
                                (student.nilai_rata / 100) * 100,
                                student.partisipasi_mentoring
                            ],
                            backgroundColor: 'rgba(102, 126, 234, 0.2)',
                            borderColor: 'rgba(102, 126, 234, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(102, 126, 234, 1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 100
                            }
                        }
                    }
                });
            });
        }
    </script>
</body>
</html>