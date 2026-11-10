<?php
require_once __DIR__ . '/conf.php';

class Database {
    public $conn;

    public function __construct() {
        global $db_host, $db_user, $db_pass, $db_name;

        try {
            $this->conn = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Cek apakah tabel veterinarians sudah ada
            try {
                $check = $this->conn->prepare("SELECT 1 FROM veterinarians LIMIT 1");
                $check->execute();
            } catch (PDOException $tableErr) {
                // Jika tabel veterinarians belum ada, perbarui database otomatis dari skrip SQL terbaru
                $sqlFile = __DIR__ . '/../database/db_wildlife_rescue.sql';
                if (file_exists($sqlFile)) {
                    $sql = file_get_contents($sqlFile);
                    $this->conn->exec($sql);
                }
            }

            // Cek apakah kolom recovery_date & status sudah ada di health_logs
            try {
                $checkCol = $this->conn->prepare("SELECT recovery_date FROM health_logs LIMIT 1");
                $checkCol->execute();
            } catch (PDOException $colErr) {
                try {
                    $this->conn->exec("ALTER TABLE health_logs 
                                       ADD COLUMN recovery_date DATETIME DEFAULT NULL AFTER diagnosis,
                                       ADD COLUMN status ENUM('Dalam Perawatan', 'Sembuh') NOT NULL DEFAULT 'Dalam Perawatan' AFTER recovery_date");
                    $this->conn->exec("UPDATE health_logs SET recovery_date = '2026-01-25 10:00:00', status = 'Sembuh' WHERE id_log IN (1, 2, 4)");
                } catch (PDOException $alterErr) {
                    // Abaikan jika sudah ada
                }
            }

            // Pastikan kolom action_taken dihapus jika masih ada
            try {
                $checkAct = $this->conn->prepare("SHOW COLUMNS FROM health_logs LIKE 'action_taken'");
                $checkAct->execute();
                if ($checkAct->fetch()) {
                    $this->conn->exec("ALTER TABLE health_logs DROP COLUMN action_taken");
                }
            } catch (PDOException $actErr) {
                // Abaikan jika tabel belum ada
            }

        } catch (PDOException $e) {
            // Jika database belum dibuat sama sekali, buat otomatis dari skrip SQL
            if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
                try {
                    $temp = new PDO("mysql:host={$db_host};charset=utf8mb4", $db_user, $db_pass);
                    $sql = file_get_contents(__DIR__ . '/../database/db_wildlife_rescue.sql');
                    $temp->exec($sql);
                    $this->conn = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass);
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    return;
                } catch (PDOException $ex) {
                    // Lanjut ke error handler di bawah
                }
            }

            die("<div style='padding:20px;font-family:sans-serif;color:#721c24;background:#f8d7da;border:1px solid #f5c6cb;border-radius:6px;max-width:600px;margin:30px auto;'>
                    <strong>Koneksi database gagal:</strong> " . htmlspecialchars($e->getMessage()) . "<br><br>
                    Pastikan MySQL di Laragon sudah aktif (klik tombol <em>Start All</em> pada Laragon).
                 </div>");
        }
    }
}
?>
