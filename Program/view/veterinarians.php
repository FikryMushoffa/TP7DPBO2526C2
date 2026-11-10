<link rel="stylesheet" href="../styles.css?v=<?= time() ?>">
<?php
require_once '../config/db.php';
require_once '../class/Veterinarian.php';

$db = (new Database())->conn;
$vet = new Veterinarian($db);
$action = $_GET['action'] ?? 'list';
?>
<div class="container">
<?php
switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $vet->name = $_POST['name'];
            $vet->gender = $_POST['gender'];
            $vet->create();
            header('Location: veterinarians.php');
            exit;
        }
        ?>
        <h2>Tambah Dokter Hewan</h2>
        <div class="header-actions">
            <a href="veterinarians.php" class="back-btn">&larr; Kembali</a>
        </div>
        <form method="POST">
            <label>Nama Dokter:</label>
            <input type="text" name="name" required placeholder="Contoh: drh. Sarah Wijaya">

            <label>Jenis Kelamin:</label>
            <select name="gender" required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>

            <button type="submit">Simpan</button>
        </form>
        <?php
        break;

    case 'edit':
        $id = $_GET['id'] ?? 0;
        $data = $vet->getById($id);
        if (!$data) {
            echo "<p>Data tidak ditemukan. <a href='veterinarians.php'>Kembali</a></p>";
            break;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $vet->id_vet = $id;
            $vet->name = $_POST['name'];
            $vet->gender = $_POST['gender'];
            $vet->update();
            header('Location: veterinarians.php');
            exit;
        }
        ?>
        <h2>Edit Dokter Hewan</h2>
        <div class="header-actions">
            <a href="veterinarians.php" class="back-btn">&larr; Kembali</a>
        </div>
        <form method="POST">
            <label>Nama Dokter:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>

            <label>Jenis Kelamin:</label>
            <select name="gender" required>
                <option value="Laki-laki" <?= $data['gender'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?= $data['gender'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
            </select>

            <button type="submit">Update</button>
        </form>
        <?php
        break;

    case 'delete':
        $id = $_GET['id'] ?? 0;
        $vet->id_vet = $id;
        $vet->delete();
        header('Location: veterinarians.php');
        exit;
        break;

    default:
        $list = $vet->readAll();
        ?>
        <h2>Daftar Dokter Hewan</h2>
        <div class="header-actions">
            <a href="../index.php" class="back-btn">&larr; Menu Utama</a>
            <a href="veterinarians.php?action=create" class="add-btn">+ Tambah Dokter</a>
        </div>
        <div class="table-responsive">
        <table>
            <tr>
                <th>ID</th>
                <th>Nama Dokter</th>
                <th>Jenis Kelamin</th>
                <th>Aksi</th>
            </tr>
            <?php if (empty($list)): ?>
            <tr>
                <td colspan="4" style="text-align:center;">Belum ada data dokter hewan.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($list as $row): ?>
            <tr>
                <td><?= $row['id_vet'] ?></td>
                <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                <td><?= htmlspecialchars($row['gender']) ?></td>
                <td>
                    <div class="action-buttons">
                        <a href="veterinarians.php?action=edit&id=<?= $row['id_vet'] ?>" class="edit-btn">Edit</a>
                        <a href="veterinarians.php?action=delete&id=<?= $row['id_vet'] ?>" class="delete-btn" onclick="return confirm('Hapus dokter hewan ini?')">Hapus</a>
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

