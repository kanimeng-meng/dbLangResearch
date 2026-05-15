<?php
require_once 'auth.php';
require_once 'env.php';
require_once 'UAS_koneksi.php';

requireAdmin(); // Hanya admin yang boleh akses

$database = new Database();
$db       = $database->getConnection();

$success = '';
$error   = '';

// Hapus user
if (isset($_GET['hapus'])) {
    $hapus_id = (int)$_GET['hapus'];
    if ($hapus_id === $_SESSION['user_id']) {
        $error = 'Tidak bisa menghapus akun sendiri.';
    } else {
        $db->prepare("DELETE FROM users WHERE id = :id")->execute([':id' => $hapus_id]);
        $success = 'User berhasil dihapus.';
    }
}

// Ganti role user
if (isset($_POST['ganti_role'])) {
    $target_id   = (int)$_POST['user_id'];
    $target_role = $_POST['role'] === 'admin' ? 'admin' : 'peneliti';
    if ($target_id === $_SESSION['user_id']) {
        $error = 'Tidak bisa mengubah role akun sendiri.';
    } else {
        $db->prepare("UPDATE users SET role = :role WHERE id = :id")
           ->execute([':role' => $target_role, ':id' => $target_id]);
        $success = 'Role berhasil diubah.';
    }
}

// Ambil semua user
$users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User — Admin</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        .role-admin    { background: #000; color: #fff; padding: 2px 8px; font-size: 12px; }
        .role-peneliti { background: #888; color: #fff; padding: 2px 8px; font-size: 12px; }
        .error-msg   { color: #cc0000; border: 1px solid #cc0000; padding: 8px; margin: 10px 0; }
        .success-msg { color: #006600; border: 1px solid #006600; padding: 8px; margin: 10px 0; }
        .btn-sm { padding: 5px 12px; font-size: 13px; cursor: pointer;
                  background: #000; color: #fff; border: 1px solid #000;
                  font-family: 'Times New Roman', serif; }
        .btn-sm.hapus { background: #fff; color: #cc0000; border: 1px solid #cc0000; }
        select { width: auto; padding: 4px; margin: 0; display: inline; }
    </style>
</head>
<body>
<div style="padding: 40px;">

    <h2>[ KELOLA USER ]</h2>
    <a href="UAS_databasePalestinaIsrael.php">&lt;&lt; Kembali ke Halaman Utama</a>
    <br><br>

    <?php if ($error):   ?><p class="error-msg"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success-msg"><?= htmlspecialchars($success) ?></p><?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>NAMA</th>
            <th>EMAIL</th>
            <th>ROLE</th>
            <th>TERDAFTAR</th>
            <th>AKSI</th>
        </tr>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['nama']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td>
                <span class="role-<?= $u['role'] ?>"><?= strtoupper($u['role']) ?></span>
            </td>
            <td><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
            <td>
                <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                <!-- Ganti Role -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <select name="role">
                        <option value="peneliti" <?= $u['role']==='peneliti' ? 'selected' : '' ?>>Peneliti</option>
                        <option value="admin"    <?= $u['role']==='admin'    ? 'selected' : '' ?>>Admin</option>
                    </select>
                    <button type="submit" name="ganti_role" class="btn-sm">Simpan</button>
                </form>
                &nbsp;
                <!-- Hapus -->
                <a href="?hapus=<?= $u['id'] ?>"
                   onclick="return confirm('Hapus user <?= htmlspecialchars($u['nama']) ?>?')"
                   class="btn-sm hapus" style="text-decoration:none;">Hapus</a>
                <?php else: ?>
                    <i style="color:#888;">(akun kamu)</i>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>