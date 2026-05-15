<?php
include 'UAS_koneksi.php';

// Query JOIN semua tabel relasi
function baseQuery() {
    return "SELECT 
                r.id,
                r.tanggal,
                a.nama_akun         AS akun_channel,
                m.nama              AS media,
                r.status_konten,
                r.pihak_konten,
                r.isi_komentar,
                r.pihak_komen,
                r.keterangan_konten,
                r.kode_hasil,
                r.tautan_linkx,
                p.nama              AS platform,
                kt.nama             AS kategori_tele,
                e.cm, e.rt, e.l, e.vw, e.j
            FROM riset_utama r
            JOIN platform       p  ON r.platform_id       = p.id
            JOIN akun           a  ON r.akun_id            = a.id
            JOIN media          m  ON r.media_id           = m.id
            LEFT JOIN kategori_tele kt ON r.kategori_tele_id = kt.id
            LEFT JOIN engagement    e  ON r.engagement_id     = e.id";
}

// ============================================================
// RENDER TABEL X (9 kolom lengkap)
// ============================================================
function renderTabelX($data, $judul) {
    echo "<h3 class='title-kategori'>$judul</h3>";
    if (count($data) == 0) {
        echo "<p class='pesan-kosong'>-- Belum ada data --</p>";
        return;
    }
    echo "<table>
            <tr>
                <th>TANGGAL</th>
                <th>AKUN</th>
                <th>MEDIA</th>
                <th>ISI STATUS</th>
                <th>PIHAK STATUS</th>
                <th>ISI KOMENTAR</th>
                <th>PIHAK KOMENTAR</th>
                <th>KETERANGAN</th>
                <th width='220'>KODE MINING</th>
                <th>LINK</th>
            </tr>";
    foreach ($data as $row) {
        $tgl = date('d/m/Y', strtotime($row['tanggal']));
        echo "<tr>
                <td>{$tgl}</td>
                <td>{$row['akun_channel']}</td>
                <td style='text-align:center;'>{$row['media']}</td>
                <td>{$row['status_konten']}</td>
                <td style='text-align:center;'><b>{$row['pihak_konten']}</b></td>
                <td>{$row['isi_komentar']}</td>
                <td style='text-align:center;'><b>{$row['pihak_komen']}</b></td>
                <td>{$row['keterangan_konten']}</td>
                <td class='kode'>{$row['kode_hasil']}</td>
                <td align='center'>";
        echo !empty($row['tautan_linkx'])
            ? "<a href='{$row['tautan_linkx']}' target='_blank' class='btn-link'>Link</a>"
            : "-";
        echo "</td></tr>";
    }
    echo "</table><br>";
}

// ============================================================
// RENDER TABEL TELEGRAM (tanpa kolom komentar)
// ============================================================
function renderTabelTele($data, $judul) {
    echo "<h3 class='title-kategori'>$judul</h3>";
    if (count($data) == 0) {
        echo "<p class='pesan-kosong'>-- Belum ada data --</p>";
        return;
    }
    echo "<table>
            <tr>
                <th>TANGGAL</th>
                <th>AKUN/CHANNEL</th>
                <th>MEDIA</th>
                <th>ISI STATUS</th>
                <th>PIHAK STATUS</th>
                <th>KETERANGAN</th>
                <th width='220'>KODE MINING</th>
                <th>LINK</th>
            </tr>";
    foreach ($data as $row) {
        $tgl = date('d/m/Y', strtotime($row['tanggal']));
        echo "<tr>
                <td>{$tgl}</td>
                <td>{$row['akun_channel']}</td>
                <td style='text-align:center;'>{$row['media']}</td>
                <td>{$row['status_konten']}</td>
                <td style='text-align:center;'><b>{$row['pihak_konten']}</b></td>
                <td>{$row['keterangan_konten']}</td>
                <td class='kode'>{$row['kode_hasil']}</td>
                <td align='center'>";
        echo !empty($row['tautan_linkx'])
            ? "<a href='{$row['tautan_linkx']}' target='_blank' class='btn-link'>Link</a>"
            : "-";
        echo "</td></tr>";
    }
    echo "</table><br>";
}

// ============================================================
// FUNGSI FETCH DATA
// ============================================================
function fetchData($koneksi, $where, $params) {
    $sql  = baseQuery() . " WHERE " . $where . " ORDER BY r.tanggal DESC";
    $stmt = $koneksi->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================================
// INISIALISASI
// ============================================================
$database = new Database();
$conn     = $database->getConnection();
$tahuns   = [2023, 2024, 2025];
$medias   = ["Teks", "Video", "Gambar"];
$kats     = ["Gerakan Sosial", "Berita", "Nama Pribadi"];
?>

<!-- ===== TABEL X PER TAHUN ===== -->
<h2 class="head-x-tahun">[ DATA X &mdash; PER TAHUN ]</h2>
<?php foreach ($tahuns as $t):
    $data = fetchData($conn,
        "p.nama = 'X' AND EXTRACT(YEAR FROM r.tanggal) = :tahun",
        [':tahun' => $t]);
    renderTabelX($data, "[ TABEL X - $t ]");
endforeach; ?>

<!-- ===== TABEL X PER MEDIA ===== -->
<h2 class="head-x-media">[ DATA X &mdash; PER MEDIA ]</h2>
<?php foreach ($medias as $m):
    $data = fetchData($conn,
        "p.nama = 'X' AND m.nama = :media",
        [':media' => $m]);
    renderTabelX($data, "MEDIA: " . strtoupper($m));
endforeach; ?>

<!-- ===== TABEL TELEGRAM PER TAHUN ===== -->
<h2 class="head-tele-tahun">[ DATA TELEGRAM &mdash; PER TAHUN ]</h2>
<?php foreach ($tahuns as $t):
    $data = fetchData($conn,
        "p.nama = 'Telegram' AND EXTRACT(YEAR FROM r.tanggal) = :tahun",
        [':tahun' => $t]);
    renderTabelTele($data, "[ TABEL TELEGRAM - $t ]");
endforeach; ?>

<!-- ===== TABEL TELEGRAM PER KATEGORI ===== -->
<h2 class="head-tele-kat">[ DATA TELEGRAM &mdash; PER KATEGORI ]</h2>
<?php foreach ($kats as $k): ?>
    <div class="sub-tele">
        <h3 class="head-sub-tele">--- <?= strtoupper($k) ?> ---</h3>
        <?php foreach ($medias as $m):
            $data = fetchData($conn,
                "p.nama = 'Telegram' AND kt.nama = :kat AND m.nama = :media",
                [':kat' => $k, ':media' => $m]);
            renderTabelTele($data, "MEDIA: " . strtoupper($m));
        endforeach; ?>
    </div>
<?php endforeach; ?>