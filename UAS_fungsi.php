<?php
include 'UAS_koneksi.php';

class Riset {
    protected $conn, $tanggal, $pihak_konten, $pihak_komen, $media_id, $platform_id;

    public function __construct($db, $postData) {
        $this->conn         = $db;
        $this->tanggal      = $postData['tanggal'];
        $this->pihak_konten = $postData['pihak_konten'];
        $this->pihak_komen  = $postData['pihak_komen'] ?? null;
    }

    // Cari atau buat akun baru, return akun_id
    protected function getOrCreateAkun($nama_akun, $platform_id) {
        $stmt = $this->conn->prepare(
            "SELECT id FROM akun WHERE nama_akun = :nama AND platform_id = :pid"
        );
        $stmt->execute([':nama' => $nama_akun, ':pid' => $platform_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) return $row['id'];

        $stmt = $this->conn->prepare(
            "INSERT INTO akun (nama_akun, platform_id) VALUES (:nama, :pid) RETURNING id"
        );
        $stmt->execute([':nama' => $nama_akun, ':pid' => $platform_id]);
        return $stmt->fetchColumn();
    }

    // Ambil id dari tabel master berdasarkan nama
    protected function getId($tabel, $nama) {
        $stmt = $this->conn->prepare("SELECT id FROM $tabel WHERE nama = :nama");
        $stmt->execute([':nama' => $nama]);
        return $stmt->fetchColumn();
    }

    protected function TanggalIndo() {
        return date('dmy', strtotime($this->tanggal));
    }
}

// ============================================================
class Twitter extends Riset {
    private $cm, $rt, $l, $vw;
    private $status_konten, $isi_komentar, $akun, $link, $keterangan;

    public function __construct($db, $postData) {
        parent::__construct($db, $postData);
        $this->cm             = max(0, (int)($postData['CM'] ?? 0));
        $this->rt             = max(0, (int)($postData['RT'] ?? 0));
        $this->l              = max(0, (int)($postData['L']  ?? 0));
        $this->vw             = max(0, (int)($postData['VW'] ?? 0));
        $this->status_konten  = $postData['status_konten']  ?? '';
        $this->isi_komentar   = $postData['isi_komentar']   ?? '';
        $this->akun           = $postData['akun']           ?? '';
        $this->link           = $postData['link']           ?? '';
        $this->keterangan     = $postData['isi_konten']     ?? '';
    }

    public function generateKodeMining($tgl) {
        return "X-{$tgl}-{$this->pihak_konten}-NET:{$this->pihak_komen}-CM{$this->cm}-RT{$this->rt}-L{$this->l}-VW{$this->vw}";
    }

    public function simpan() {
        $platform_id = $this->getId('platform', 'X');
        $media_id    = $this->getId('media', $_POST['media']);
        $akun_id     = $this->getOrCreateAkun($this->akun, $platform_id);
        $tgl         = $this->TanggalIndo();
        $kode        = $this->generateKodeMining($tgl);

        // Simpan engagement
        $stmt = $this->conn->prepare(
            "INSERT INTO engagement (cm, rt, l, vw) VALUES (?,?,?,?) RETURNING id"
        );
        $stmt->execute([$this->cm, $this->rt, $this->l, $this->vw]);
        $engagement_id = $stmt->fetchColumn();

        // Simpan riset utama
        $sql = "INSERT INTO riset_utama
                (platform_id, akun_id, tanggal, media_id, status_konten,
                 pihak_konten, isi_komentar, pihak_komen, keterangan_konten,
                 engagement_id, kode_hasil, tautan_linkx)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $platform_id, $akun_id, $this->tanggal, $media_id,
            $this->status_konten, $this->pihak_konten,
            $this->isi_komentar, $this->pihak_komen,
            $this->keterangan, $engagement_id, $kode, $this->link
        ]);
    }
}

// ============================================================
class Telegram extends Riset {
    private $akun, $j, $keterangan, $kategori_tele, $status_konten;

    public function __construct($db, $postData) {
        parent::__construct($db, $postData);
        $this->akun           = $postData['akun']          ?? '';
        $this->j              = max(0, (int)($postData['j'] ?? 0));
        $this->keterangan     = $postData['isi_konten']    ?? '';
        $this->kategori_tele  = $postData['kategori_tele'] ?? '';
        $this->status_konten  = $postData['status_konten'] ?? '';
    }

    public function generateKodeMining($tgl) {
        $kat = ($this->kategori_tele == 'Gerakan Sosial') ? "GS"
             : (($this->kategori_tele == 'Berita')        ? "B" : "P");
        $m   = substr($_POST['media'], 0, 1);
        return "T-{$tgl}-{$this->pihak_konten}-{$m}-{$kat}-J{$this->j}-@{$this->akun}";
    }

    public function simpan() {
        $platform_id     = $this->getId('platform', 'Telegram');
        $media_id        = $this->getId('media', $_POST['media']);
        $kategori_tele_id = $this->getId('kategori_tele', $this->kategori_tele);
        $akun_id         = $this->getOrCreateAkun($this->akun, $platform_id);
        $tgl             = $this->TanggalIndo();
        $kode            = $this->generateKodeMining($tgl);

        // Simpan engagement
        $stmt = $this->conn->prepare(
            "INSERT INTO engagement (j) VALUES (?) RETURNING id"
        );
        $stmt->execute([$this->j]);
        $engagement_id = $stmt->fetchColumn();

        // Simpan riset utama
        $sql = "INSERT INTO riset_utama
                (platform_id, akun_id, tanggal, media_id, kategori_tele_id,
                 status_konten, pihak_konten, keterangan_konten,
                 engagement_id, kode_hasil)
                VALUES (?,?,?,?,?,?,?,?,?,?)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $platform_id, $akun_id, $this->tanggal, $media_id,
            $kategori_tele_id, $this->status_konten, $this->pihak_konten,
            $this->keterangan, $engagement_id, $kode
        ]);
    }
}
?>