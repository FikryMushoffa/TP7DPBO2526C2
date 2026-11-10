<link rel="stylesheet" href="../styles.css?v=<?= time() ?>">
<?php
require_once '../config/db.php';
require_once '../class/Animal.php';
require_once '../class/Enclosure.php';

$db = (new Database())->conn;
$animal = new Animal($db);
$enclosure = new Enclosure($db);
$enclosures = $enclosure->readAll();

$action = $_GET['action'] ?? 'list';
?>
<div class="container">
<?php
switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $animal->id_enclosure = $_POST['id_enclosure'];
            $animal->species_name = $_POST['species_name'];
            $animal->nickname = $_POST['nickname'];
            $animal->rescue_date = $_POST['rescue_date'];
            $animal->status = $_POST['status'];
            $animal->create();
            header('Location: animals.php');
            exit;
        }
        ?>
        <h2>Tambah Data Satwa</h2>
        <div class="header-actions">
            <a href="animals.php" class="back-btn">&larr; Kembali</a>
        </div>
        <form method="POST">
            <label>Nama Panggilan:</label>
            <input type="text" name="nickname" required placeholder="Contoh: Hope">

            <label>Nama Spesies:</label>
            <input type="text" name="species_name" required placeholder="Contoh: Orangutan">

            <label>Kandang:</label>
            <select name="id_enclosure" required>
                <option value="">-- Pilih Kandang --</option>
                <?php foreach ($enclosures as $enc): ?>
                    <option value="<?= $enc['id_enclosure'] ?>">
                        <?= htmlspecialchars($enc['name']) ?> (<?= htmlspecialchars($enc['habitat_type']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Tanggal Penyelamatan:</label>
            <input type="date" name="rescue_date" value="<?= date('Y-m-d') ?>" required>

            <label>Status:</label>
            <select name="status" required>
                <option value="Karantina">Karantina</option>
                <option value="Rehabilitasi">Rehabilitasi</option>
                <option value="Siap Rilis">Siap Rilis</option>
                <option value="Telah Dirilis">Telah Dirilis</option>
            </select>

            <button type="submit">Simpan</button>
        </form>
        <?php
        break;

    case 'edit':
        $id = $_GET['id'] ?? 0;
        $data = $animal->getById($id);
        if (!$data) {
            echo "<p>Data tidak ditemukan. <a href='animals.php'>Kembali</a></p>";
            break;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $animal->id_animal = $id;
            $animal->id_enclosure = $_POST['id_enclosure'];
            $animal->species_name = $_POST['species_name'];
            $animal->nickname = $_POST['nickname'];
            $animal->rescue_date = $_POST['rescue_date'];
            $animal->status = $_POST['status'];
            $animal->update();
            header('Location: animals.php');
            exit;
        }
        ?>
        <h2>Edit Data Satwa</h2>
        <div class="header-actions">
            <a href="animals.php" class="back-btn">&larr; Kembali</a>
        </div>
        <form method="POST">
            <label>Nama Panggilan:</label>
            <input type="text" name="nickname" value="<?= htmlspecialchars($data['nickname']) ?>" required>

            <label>Nama Spesies:</label>
            <input type="text" name="species_name" value="<?= htmlspecialchars($data['species_name']) ?>" required>

            <label>Kandang:</label>
            <select name="id_enclosure" required>
                <?php foreach ($enclosures as $enc): ?>
                    <option value="<?= $enc['id_enclosure'] ?>" <?= $enc['id_enclosure'] == $data['id_enclosure'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($enc['name']) ?> (<?= htmlspecialchars($enc['habitat_type']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Tanggal Penyelamatan:</label>
            <input type="date" name="rescue_date" value="<?= htmlspecialchars($data['rescue_date']) ?>" required>

            <label>Status:</label>
            <select name="status" required>
                <option value="Karantina" <?= $data['status'] == 'Karantina' ? 'selected' : '' ?>>Karantina</option>
                <option value="Rehabilitasi" <?= $data['status'] == 'Rehabilitasi' ? 'selected' : '' ?>>Rehabilitasi</option>
                <option value="Siap Rilis" <?= $data['status'] == 'Siap Rilis' ? 'selected' : '' ?>>Siap Rilis</option>
                <option value="Telah Dirilis" <?= $data['status'] == 'Telah Dirilis' ? 'selected' : '' ?>>Telah Dirilis</option>
            </select>

            <button type="submit">Update</button>
        </form>
        <?php
        break;

    case 'delete':
        $id = $_GET['id'] ?? 0;
        $animal->id_animal = $id;
        $animal->delete();
        header('Location: animals.php');
        exit;
        break;

    default:
        $list = $animal->readAll();
        ?>
        <h2>Daftar Satwa Rescued</h2>
        <div class="header-actions">
            <a href="../index.php" class="back-btn">&larr; Menu Utama</a>
            <a href="animals.php?action=create" class="add-btn">+ Tambah Satwa</a>
        </div>
        <div class="table-responsive">
        <table>
            <tr>
                <th>ID</th>
                <th>Panggilan</th>
                <th>Nama Spesies</th>
                <th>Kandang</th>
                <th>Tgl Rescue</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php if (empty($list)): ?>
            <tr>
                <td colspan="7" style="text-align:center;">Belum ada data satwa.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($list as $row): ?>
            <tr>
                <td><?= $row['id_animal'] ?></td>
                <td><strong><?= htmlspecialchars($row['nickname']) ?></strong></td>
                <td><em><?= htmlspecialchars($row['species_name']) ?></em></td>
                <td><?= htmlspecialchars($row['enclosure_name']) ?></td>
                <td><?= date('d/m/Y', strtotime($row['rescue_date'])) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td>
                    <div class="action-buttons">
                        <a href="animals.php?action=edit&id=<?= $row['id_animal'] ?>" class="edit-btn">Edit</a>
                        <a href="animals.php?action=delete&id=<?= $row['id_animal'] ?>" class="delete-btn" onclick="return confirm('Yakin ingin menghapus satwa ini? Rekam medis terkait juga akan terhapus.')">Hapus</a>
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

