<?php
require_once 'config/database.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Monitoring Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h4>Monitoring Siswa</h4>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="pages/notulensi.php"><i class="bi bi-file-text"></i> Notulensi Rabuan</a>
            <a href="pages/mentoring.php"><i class="bi bi-people"></i> Mentoring</a>
            <a href="pages/timeline.php"><i class="bi bi-calendar"></i> Timeline Kegiatan</a>
            <a href="pages/pra_operasional.php"><i class="bi bi-clipboard-check"></i> Pra Operasional</a>
            <a href="pages/operasional.php"><i class="bi bi-play-circle"></i> Operasional</a>
            <a href="pages/pasca_operasional.php"><i class="bi bi-tools"></i> Pasca Operasional</a>
            <a href="pages/bina_jasmani.php"><i class="bi bi-heart"></i> Bina Jasmani</a>
            <a href="pages/kehadiran.php"><i class="bi bi-check-circle"></i> Kehadiran</a>
            <a href="pages/virtualisasi.php"><i class="bi bi-graph-up"></i> Virtualisasi</a>
            <?php if(isSuperAdmin()): ?>
            <a href="pages/users.php"><i class="bi bi-person-badge"></i> Manajemen User</a>
            <?php endif; ?>
            <a href="api/auth.php?action=logout" class="logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="navbar-top">
            <h2>Dashboard</h2>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                </div>
            </div>
        </div>

        <div class="row" id="statsContainer">
            <div class="col-md-3">
                <div class="stat-card">
                    <h5>Total Siswa</h5>
                    <h2 id="totalSiswa">0</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h5>Kehadiran Hari Ini</h5>
                    <h2 id="kehadiranHariIni">0</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h5>Kegiatan Aktif</h5>
                    <h2 id="kegiatanAktif">0</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h5>Rata-rata Nilai</h5>
                    <h2 id="rataNilai">0</h2>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Statistik Kehadiran</div>
                    <div class="card-body">
                        <canvas id="kehadiranChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Distribusi Nilai</div>
                    <div class="card-body">
                        <canvas id="nilaiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $.get('api/dashboard.php', function(data) {
                $('#totalSiswa').text(data.total_siswa);
                $('#kehadiranHariIni').text(data.kehadiran_hari_ini);
                $('#kegiatanAktif').text(data.kegiatan_aktif);
                $('#rataNilai').text(data.rata_nilai);
            }, 'json');

            $.get('api/charts.php', function(data) {
                new Chart(document.getElementById('kehadiranChart'), {
                    type: 'bar',
                    data: {
                        labels: data.kehadiran.labels,
                        datasets: [{
                            label: 'Kehadiran per Kegiatan',
                            data: data.kehadiran.data,
                            backgroundColor: 'rgba(102, 126, 234, 0.5)'
                        }]
                    }
                });

                new Chart(document.getElementById('nilaiChart'), {
                    type: 'doughnut',
                    data: {
                        labels: data.nilai.labels,
                        datasets: [{
                            data: data.nilai.data,
                            backgroundColor: ['#667eea', '#764ba2', '#f093fb']
                        }]
                    }
                });
            });
        });
    </script>
</body>
</html>