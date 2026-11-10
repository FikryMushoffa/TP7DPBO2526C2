<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wildlife Rescue Center</title>
    <link rel="stylesheet" href="styles.css?v=<?= time() ?>">
</head>
<body>
    <div class="container home-container">
        <div class="home-header">
            <h1>Wildlife Rescue Center</h1>
            <p>Sistem Informasi Karantina & Rehabilitasi Satwa</p>
        </div>

        <div class="home-grid">
            <a href="view/enclosures.php" class="home-card">
                <h3>Fasilitas Kandang</h3>
                <span>Kelola data kandang dan kapasitas</span>
            </a>
            <a href="view/animals.php" class="home-card">
                <h3>Data Satwa</h3>
                <span>Pendataan satwa dan status perawatan</span>
            </a>
            <a href="view/veterinarians.php" class="home-card">
                <h3>Dokter Hewan</h3>
                <span>Data tenaga medis pemeriksa</span>
            </a>
            <a href="view/health_logs.php" class="home-card">
                <h3>Rekam Medis</h3>
                <span>Riwayat pemeriksaan kesehatan</span>
            </a>
        </div>
    </div>
</body>
</html>
