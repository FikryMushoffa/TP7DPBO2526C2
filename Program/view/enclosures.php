<link rel="stylesheet" href="../styles.css?v=<?= time() ?>">
<?php
require_once '../config/db.php';
require_once '../class/Enclosure.php';

$db = (new Database())->conn;
$enclosure = new Enclosure($db);
$action = $_GET['action'] ?? 'list';
?>
<div class="container">
<?php
switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $enclosure->name = $_POST['name'];
            $enclosure->habitat_type = $_POST['habitat_type'];
            $enclosure->capacity = $_POST['capacity'];
            $enclosure->create();
            header('Location: enclosures.php');
            exit;
        }
        ?>
        <h2>Tambah Fasilitas Kandang</h2>
        <div class="header-actions">
            <a href="enclosures.php" class="back-btn">&larr; Kembali</a>
        </div>
        <form method="POST">
            <label>Nama Kandang:</label>
            <input type="text" name="name" required placeholder="Contoh: Aviary A">

            <label>Tipe Habitat:</label>
            <select name="habitat_type" required>
                <option value="">-- Pilih Tipe Habitat --</option>
                <option value="Terestrial">Terestrial</option>
                <option value="Akuatik">Akuatik</option>
                <option value="Udara">Udara</option>
                <option value="Amfibi">Amfibi</option>
            </select>

            <label>Kapasitas (Ekor):</label>
            <input type="number" name="capacity" min="1" required placeholder="Contoh: 5">

            <button type="submit">Simpan</button>
        </form>
        <?php
        break;

    case 'edit':
        $id = $_GET['id'] ?? 0;
        $data = $enclosure->getById($id);
        if (!$data) {
            echo "<p>Data tidak ditemukan. <a href='enclosures.php'>Kembali</a></p>";
            break;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $enclosure->id_enclosure = $id;
            $enclosure->name = $_POST['name'];
            $enclosure->habitat_type = $_POST['habitat_type'];
            $enclosure->capacity = $_POST['capacity'];
            $enclosure->update();
            header('Location: enclosures.php');
            exit;
        }
        ?>
        <h2>Edit Fasilitas Kandang</h2>
        <div class="header-actions">
            <a href="enclosures.php" class="back-btn">&larr; Kembali</a>
        </div>
        <form method="POST">
            <label>Nama Kandang:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>

            <label>Tipe Habitat:</label>
            <select name="habitat_type" required>
                <option value="Terestrial" <?= $data['habitat_type'] == 'Terestrial' ? 'selected' : '' ?>>Terestrial</option>
                <option value="Akuatik" <?= $data['habitat_type'] == 'Akuatik' ? 'selected' : '' ?>>Akuatik</option>
                <option value="Udara" <?= $data['habitat_type'] == 'Udara' ? 'selected' : '' ?>>Udara</option>
                <option value="Amfibi" <?= $data['habitat_type'] == 'Amfibi' ? 'selected' : '' ?>>Amfibi</option>
            </select>

            <label>Kapasitas (Ekor):</label>
            <input type="number" name="capacity" min="1" value="<?= htmlspecialchars($data['capacity']) ?>" required>

            <button type="submit">Update</button>
        </form>
        <?php
        break;

    case 'delete':
        $id = $_GET['id'] ?? 0;
        $enclosure->id_enclosure = $id;
        $enclosure->delete();
        header('Location: enclosures.php');
        exit;
        break;

    default:
        $list = $enclosure->readAll();
        ?>
        <h2>Daftar Fasilitas Kandang</h2>
        <div class="header-actions">
            <a href="../index.php" class="back-btn">&larr; Menu Utama</a>
            <a href="enclosures.php?action=create" class="add-btn">+ Tambah Kandang</a>
        </div>
        <div class="table-responsive">
        <table>
            <tr>
                <th>ID</th>
                <th>Nama Kandang</th>
                <th>Tipe Habitat</th>
                <th>Kapasitas</th>
                <th>Aksi</th>
            </tr>
            <?php if (empty($list)): ?>
            <tr>
                <td colspan="5" style="text-align:center;">Belum ada data kandang.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($list as $row): ?>
            <tr>
                <td><?= $row['id_enclosure'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['habitat_type']) ?></td>
                <td><?= $row['capacity'] ?> ekor</td>
                <td>
                    <div class="action-buttons">
                        <a href="enclosures.php?action=edit&id=<?= $row['id_enclosure'] ?>" class="edit-btn">Edit</a>
                        <a href="enclosures.php?action=delete&id=<?= $row['id_enclosure'] ?>" class="delete-btn" onclick="return confirm('Yakin ingin menghapus kandang ini? Satwa di dalamnya juga akan terhapus.')">Hapus</a>
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

