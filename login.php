<?php
require_once 'auth.php';
require_once 'env.php';
require_once 'UAS_koneksi.php';

// Kalau sudah login, langsung ke halaman utama
if (isLoggedIn()) {
    header('Location: UAS_databasePalestinaIsrael.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $database = new Database();
        $db       = $database->getConnection();

        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];
            header('Location: UAS_databasePalestinaIsrael.php');
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Riset Bahasa</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        .auth-box {
            max-width: 420px;
            margin: 80px auto;
        }
        .auth-box h2 { text-align: center; margin-bottom: 20px; }
        .error-msg {
            color: #cc0000;
            border: 1px solid #cc0000;
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
    <h2>[ LOGIN ]</h2>

    <?php if ($error): ?>
        <p class="error-msg"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" placeholder="email@contoh.com" required>

        <label>Password:</label>
        <input type="password" name="password" placeholder="••••••••" required>

        <br>
        <button type="submit" name="login" class="btn-big" style="width:100%">MASUK</button>
    </form>

    <p class="auth-link">
        Belum punya akun? <a href="register.php">Daftar di sini</a>
    </p>
</div>
</body>
</html>