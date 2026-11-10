<link rel="stylesheet" href="../styles.css?v=<?= time() ?>">
<?php
require_once '../config/db.php';
require_once '../class/HealthLog.php';
require_once '../class/Animal.php';
require_once '../class/Veterinarian.php';

$db = (new Database())->conn;
$healthLog = new HealthLog($db);
$animal = new Animal($db);
$vet = new Veterinarian($db);

$animals = $animal->readAll();
$veterinarians = $vet->readAll();

$action = $_GET['action'] ?? 'list';
?>
<div class="container">
<?php
switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $healthLog->id_animal = $_POST['id_animal'];
            $healthLog->id_vet = $_POST['id_vet'];
            $healthLog->checkup_date = $_POST['checkup_date'];
            $healthLog->diagnosis = $_POST['diagnosis'];
            $healthLog->create();
            header('Location: health_logs.php');
            exit;
        }
        ?>
        <h2>Tambah Rekam Medis</h2>
        <div class="header-actions">
            <a href="health_logs.php" class="back-btn">&larr; Kembali</a>
        </div>
        <form method="POST">
            <label>Satwa Pasien:</label>
            <select name="id_animal" required>
                <option value="">-- Pilih Satwa --</option>
                <?php foreach ($animals as $a): ?>
                    <option value="<?= $a['id_animal'] ?>">
                        <?= htmlspecialchars($a['nickname']) ?> (<?= htmlspecialchars($a['species_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Dokter Hewan:</label>
            <select name="id_vet" required>
                <option value="">-- Pilih Dokter Hewan --</option>
                <?php foreach ($veterinarians as $v): ?>
                    <option value="<?= $v['id_vet'] ?>">
                        <?= htmlspecialchars($v['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Waktu Pemeriksaan:</label>
            <input type="datetime-local" name="checkup_date" value="<?= date('Y-m-d\TH:i') ?>" required>

            <label>Hasil Diagnosa:</label>
            <textarea name="diagnosis" required placeholder="Contoh: Malnutrisi ringan"></textarea>

            <button type="submit">Simpan</button>
        </form>
        <?php
        break;

    case 'edit':
        $id = $_GET['id'] ?? 0;
        $data = $healthLog->getById($id);
        if (!$data) {
            echo "<p>Data tidak ditemukan. <a href='health_logs.php'>Kembali</a></p>";
            break;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $healthLog->id_log = $id;
            $healthLog->id_animal = $_POST['id_animal'];
            $healthLog->id_vet = $_POST['id_vet'];
            $healthLog->checkup_date = $_POST['checkup_date'];
            $healthLog->diagnosis = $_POST['diagnosis'];
            $healthLog->status = $_POST['status'] ?? 'Dalam Perawatan';
            $healthLog->recovery_date = !empty($_POST['recovery_date']) ? $_POST['recovery_date'] : null;
            $healthLog->update();
            header('Location: health_logs.php');
            exit;
        }
        ?>
        <h2>Edit Rekam Medis</h2>
        <div class="header-actions">
            <a href="health_logs.php" class="back-btn">&larr; Kembali</a>
        </div>
        <form method="POST">
            <label>Satwa Pasien:</label>
            <select name="id_animal" required>
                <?php foreach ($animals as $a): ?>
                    <option value="<?= $a['id_animal'] ?>" <?= $a['id_animal'] == $data['id_animal'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['nickname']) ?> (<?= htmlspecialchars($a['species_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Dokter Hewan:</label>
            <select name="id_vet" required>
                <?php foreach ($veterinarians as $v): ?>
                    <option value="<?= $v['id_vet'] ?>" <?= $v['id_vet'] == $data['id_vet'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($v['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Waktu Pemeriksaan:</label>
            <input type="datetime-local" name="checkup_date" value="<?= date('Y-m-d\TH:i', strtotime($data['checkup_date'])) ?>" required>

            <label>Hasil Diagnosa:</label>
            <textarea name="diagnosis" required><?= htmlspecialchars($data['diagnosis']) ?></textarea>

            <label>Status Rawat:</label>
            <select name="status" id="status-select" onchange="toggleRecoveryDate()">
                <option value="Dalam Perawatan" <?= ($data['status'] ?? '') === 'Dalam Perawatan' ? 'selected' : '' ?>>Dalam Perawatan</option>
                <option value="Sembuh" <?= ($data['status'] ?? '') === 'Sembuh' ? 'selected' : '' ?>>Sembuh</option>
            </select>

            <label>Waktu Selesai Rawat:</label>
            <input type="datetime-local" name="recovery_date" id="recovery-date" value="<?= !empty($data['recovery_date']) ? date('Y-m-d\TH:i', strtotime($data['recovery_date'])) : '' ?>">
            <small style="display:block;margin-top:-8px;margin-bottom:14px;color:#666;">(Otomatis terisi jika memilih Sembuh, kosongkan jika masih Dalam Perawatan)</small>

            <script>
            function toggleRecoveryDate() {
                var status = document.getElementById('status-select').value;
                var recInput = document.getElementById('recovery-date');
                if (status === 'Sembuh') {
                    if (!recInput.value) {
                        var now = new Date();
                        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                        recInput.value = now.toISOString().slice(0, 16);
                    }
                } else {
                    recInput.value = '';
                }
            }
            </script>

            <button type="submit">Update</button>
        </form>
        <?php
        break;

    case 'delete':
        $id = $_GET['id'] ?? 0;
        $healthLog->id_log = $id;
        $healthLog->delete();
        header('Location: health_logs.php');
        exit;
        break;

    case 'complete':
        $id = $_GET['id'] ?? 0;
        $healthLog->completeTreatment($id);
        header('Location: health_logs.php');
        exit;
        break;

    default:
        $list = $healthLog->readAll();
        ?>
        <h2>Daftar Rekam Medis Satwa</h2>
        <div class="header-actions">
            <a href="../index.php" class="back-btn">&larr; Menu Utama</a>
            <a href="health_logs.php?action=create" class="add-btn">+ Tambah Rekam Medis</a>
        </div>
        <div class="table-responsive">
        <table>
            <tr>
                <th>ID</th>
                <th>Pasien Satwa</th>
                <th>Dokter Hewan</th>
                <th>Diagnosa</th>
                <th>Waktu Cek</th>
                <th>Waktu Selesai</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php if (empty($list)): ?>
            <tr>
                <td colspan="8" style="text-align:center;">Belum ada catatan medis.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($list as $row): ?>
            <tr>
                <td><?= $row['id_log'] ?></td>
                <td><strong><?= htmlspecialchars($row['nickname']) ?></strong></td>
                <td><?= htmlspecialchars($row['vet_name']) ?></td>
                <td><?= htmlspecialchars($row['diagnosis']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['checkup_date'])) ?></td>
                <td>
                    <?= !empty($row['recovery_date']) ? date('d/m/Y H:i', strtotime($row['recovery_date'])) : '-' ?>
                </td>
                <td>
                    <?php if (($row['status'] ?? '') === 'Sembuh' || !empty($row['recovery_date'])): ?>
                        <span class="badge-healed">Sembuh</span>
                    <?php else: ?>
                        <span class="badge-care">Dalam Rawat</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="action-buttons">
                        <?php if (empty($row['recovery_date']) || ($row['status'] ?? '') !== 'Sembuh'): ?>
                            <a href="health_logs.php?action=complete&id=<?= $row['id_log'] ?>" class="complete-btn" onclick="return confirm('Konfirmasi satwa telah sembuh dan selesai rawat?')">Selesai Rawat</a>
                        <?php endif; ?>
                        <a href="health_logs.php?action=edit&id=<?= $row['id_log'] ?>" class="edit-btn">Edit</a>
                        <a href="health_logs.php?action=delete&id=<?= $row['id_log'] ?>" class="delete-btn" onclick="return confirm('Hapus catatan medis ini?')">Hapus</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </table>
        </div>
        <?php
        break;
}
?>
</div>
