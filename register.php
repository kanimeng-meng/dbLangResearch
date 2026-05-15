<?php
require_once 'auth.php';
require_once 'env.php';
require_once 'UAS_koneksi.php';

if (isLoggedIn()) {
    header('Location: UAS_databasePalestinaIsrael.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $nama     = trim($_POST['nama']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $konfirm  = $_POST['konfirm']       ?? '';

    if (empty($nama) || empty($email) || empty($password)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $konfirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $database = new Database();
        $db       = $database->getConnection();

        // Cek email sudah terdaftar
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);

        if ($stmt->fetch()) {
            $error = 'Email sudah terdaftar.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare(
                "INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :pass, 'peneliti')"
            );
            $stmt->execute([':nama' => $nama, ':email' => $email, ':pass' => $hash]);
            $success = 'Akun berhasil dibuat! Silakan login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Sistem Riset Bahasa</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        .auth-box {
            max-width: 420px;
            margin: 60px auto;
        }
        .auth-box h2 { text-align: center; margin-bottom: 20px; }
        .error-msg {
            color: #cc0000;
            border: 1px solid #cc0000;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }
        .success-msg {
            color: #006600;
            border: 1px solid #006600;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }
        .auth-link { text-align: center; margin-top: 15px; }
        .auth-link a { color: #000; font-weight: bold; }
        label { font-weight: bold; display: block; margin-top: 10px; }
    </style>
</head>
<body>
<div class="auth-box box">
    <h2>[ DAFTAR AKUN ]</h2>

    <?php if ($error): ?>
        <p class="error-msg"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class="success-msg"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Nama Lengkap:</label>
        <input type="text" name="nama" placeholder="Nama Lengkap" required>

        <label>Email:</label>
        <input type="email" name="email" placeholder="email@contoh.com" required>

        <label>Password:</label>
        <input type="password" name="password" placeholder="Min. 6 karakter" required>

        <label>Konfirmasi Password:</label>
        <input type="password" name="konfirm" placeholder="Ulangi password" required>

        <br>
        <button type="submit" name="register" class="btn-big" style="width:100%">DAFTAR</button>
    </form>

    <p class="auth-link">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </p>
</div>
</body>
</html>