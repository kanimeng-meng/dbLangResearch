<?php
require_once 'auth.php';
requireLogin(); // Wajib login dulu

if (isset($_POST['gas'])) {
    include 'UAS_fungsi.php'; 
    
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        echo "<script>alert('Koneksi Database Gagal!');</script>";
    } else {
        $app = ($_POST['platform'] == 'X') ? new Twitter($db, $_POST) : new Telegram($db, $_POST);
        if ($app->simpan()) {
            echo "<script>alert('Data Berhasil Disimpan!'); window.location='UAS_databasePalestinaIsrael.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan data ke database.');</script>";
        }
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Coding Riset Bahasa - UAS</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        .navbar {
            background: #000;
            color: #fff;
            padding: 12px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: -40px -40px 30px -40px;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            font-family: 'Times New Roman', serif;
            margin-left: 20px;
            font-size: 14px;
        }
        .navbar a:hover { text-decoration: underline; }
        .navbar .role-badge {
            background: #fff;
            color: #000;
            padding: 2px 8px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 8px;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- NAVBAR -->
    <div class="navbar">
        <span>
            Halo, <b><?= htmlspecialchars($_SESSION['nama']) ?></b>
            <span class="role-badge"><?= strtoupper($_SESSION['role']) ?></span>
        </span>
        <span>
            <?php if (isAdmin()): ?>
                <a href="admin_users.php">&#9881; Kelola User</a>
            <?php endif; ?>
            <a href="logout.php">Logout &rarr;</a>
        </span>
    </div>

    <?php include 'UAS_generator.php'; ?>

    <hr>

    <div id="area-tabel">
        <?php include 'UAS_tabel.php'; ?>
    </div>
</div>

<script>
var platformSekarang = "";

function pindahLayar(tutup, buka) {
    document.getElementById(tutup).style.display = 'none';
    document.getElementById(buka).style.display  = 'block';
}

function pilihPihakKonten(p) {
    document.getElementById('val_pihak_konten').value = p;
    var container = document.getElementById('container-platform');
    container.innerHTML = `<button type="button" class="btn-big" onclick="pilihPlatform('X')">X (TWITTER)</button>`;
    if (p === 'PP') {
        container.innerHTML += `<button type="button" class="btn-big" onclick="pilihPlatform('Telegram')">TELEGRAM</button>`;
    }
    pindahLayar('step-pihak-konten', 'step-platform');
}

function pilihPlatform(plat) {
    document.getElementById('val_platform').value = plat;
    platformSekarang = plat;
    if (plat === 'X') {
        pindahLayar('step-platform', 'step-pihak-komen');
    } else {
        document.getElementById('val_pihak_komen').value = '-';
        pindahLayar('step-platform', 'step-kategori-tele');
    }
}

function pilihPihakKomen(pk) {
    document.getElementById('val_pihak_komen').value = pk;
    aturTombolBackMedia('step-pihak-komen');
    pindahLayar('step-pihak-komen', 'step-media');
}

function pilihKategoriTele(kat) {
    document.getElementById('val_kategori_tele').value = kat;
    aturTombolBackMedia('step-kategori-tele');
    pindahLayar('step-kategori-tele', 'step-media');
}

function aturTombolBackMedia(asalStep) {
    document.getElementById('btn-back-media').setAttribute('onclick', "kembaliKe('" + asalStep + "')");
}

function pilihMedia(med) {
    document.getElementById('val_media').value = med;
    var areaEng      = document.getElementById('input-eng');
    var areaKomentar = document.getElementById('input-komentar');

    if (platformSekarang === 'X') {
        areaEng.innerHTML = `
            <p class="text-black">Engagement X (Kuantitatif):</p>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <div>CM: <input type="number" name="CM" min="0" value="0" style="width:70px" required></div>
                <div>RT: <input type="number" name="RT" min="0" value="0" style="width:70px" required></div>
                <div>L:  <input type="number" name="L"  min="0" value="0" style="width:70px" required></div>
                <div>VW: <input type="number" name="VW" min="0" value="0" style="width:70px" required></div>
            </div>`;
        areaKomentar.innerHTML = `
            <p class="text-black">Isi Komentar Netizen:</p>
            <input type="text" name="isi_komentar" placeholder="Masukkan tanggapan komentar di sini...">`;
    } else {
        areaEng.innerHTML = `
            <p class="text-black">Engagement Telegram:</p>
            Jumlah Peserta (J): <input type="number" name="j" value="0" min="0" style="width:120px" required>`;
        areaKomentar.innerHTML = '';
    }
    pindahLayar('step-media', 'step-final');
}

function kembaliKe(targetId) {
    document.querySelectorAll('.step, #step-pihak-konten').forEach(s => {
        s.style.display = 'none';
    });
    document.getElementById(targetId).style.display = 'block';
}
</script>
</body>
</html>