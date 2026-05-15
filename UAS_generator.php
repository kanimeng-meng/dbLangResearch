<div class="box">
    <h2>[ GENERATOR KODE DATA MINING ]</h2>
    <form method="POST">
        <input type="hidden" name="pihak_konten" id="val_pihak_konten">
        <input type="hidden" name="pihak_komen"  id="val_pihak_komen">
        <input type="hidden" name="platform"     id="val_platform">
        <input type="hidden" name="kategori_tele" id="val_kategori_tele">
        <input type="hidden" name="media"        id="val_media">
        
        <div id="step-pihak-konten" style="display:block; text-align:center;">
            <h3>PILIH PIHAK KONTEN:</h3>
            <button type="button" class="btn-big" onclick="pilihPihakKonten('PP')">PRO-PALESTINA (PP)</button>
            <button type="button" class="btn-big" onclick="pilihPihakKonten('PI')">PRO-ISRAEL (PI)</button>
        </div>

        <div id="step-platform" class="step" style="display:none; text-align:center;">
            <h3>PILIH PLATFORM:</h3>
            <div id="container-platform"></div>
            <br>
            <button type="button" class="btn-back" onclick="kembaliKe('step-pihak-konten')">&lt;&lt; Kembali</button>
        </div>

        <div id="step-pihak-komen" class="step" style="display:none; text-align:center;">
            <h3>REAKSI NETIZEN (KOMENTAR):</h3>
            <button type="button" class="btn-big" onclick="pilihPihakKomen('PP')">PP</button>
            <button type="button" class="btn-big" onclick="pilihPihakKomen('PI')">PI</button>
            <button type="button" class="btn-big" onclick="pilihPihakKomen('N')">NETRAL (N)</button>
            <br>
            <button type="button" class="btn-back" onclick="kembaliKe('step-platform')">&lt;&lt; Kembali</button>
        </div>

        <div id="step-kategori-tele" class="step" style="display:none; text-align:center;">
            <h3>KATEGORI TELEGRAM:</h3>
            <button type="button" class="btn-big" onclick="pilihKategoriTele('Gerakan Sosial')">GERAKAN SOSIAL</button>
            <button type="button" class="btn-big" onclick="pilihKategoriTele('Berita')">BERITA</button>
            <button type="button" class="btn-big" onclick="pilihKategoriTele('Nama Pribadi')">NAMA PRIBADI</button>
            <br>
            <button type="button" class="btn-back" onclick="kembaliKe('step-platform')">&lt;&lt; Kembali</button>
        </div>

        <div id="step-media" class="step" style="display:none; text-align:center;">
            <h3>KATEGORI MEDIA:</h3>
            <button type="button" class="btn-big" onclick="pilihMedia('Teks')">TEKS</button>
            <button type="button" class="btn-big" onclick="pilihMedia('Video')">VIDEO</button>
            <button type="button" class="btn-big" onclick="pilihMedia('Gambar')">GAMBAR</button>
            <br>
            <button type="button" class="btn-back" id="btn-back-media" onclick="">&lt;&lt; Kembali</button>
        </div>

        <div id="step-final" class="step" style="display:none;">
            <h3>ISI KETERANGAN KONTEN:</h3>
            <input type="text" name="akun" placeholder="Nama Akun/Channel" required><br>
            <input type="date" name="tanggal" required><br>

            <div id="input-eng"></div>

            <p class="text-black">Isi Status/Postingan:</p>
            <textarea name="status_konten" placeholder="Masukkan isi postingan/tweet di sini..."></textarea>

            <div id="input-komentar"></div>

            <p class="text-black">Keterangan Tambahan (Opsional):</p>
            <textarea name="isi_konten" placeholder="Catatan tambahan peneliti..."></textarea>
            
            <p class="text-black">Tautan Link:</p>
            <input type="text" name="link" class="input-link" placeholder="https://...">
            <br><br>
            
            <button type="submit" name="gas" class="btn-big">SIMPAN & TAMPILKAN TABEL</button>
            <br>
            <button type="button" class="btn-back" onclick="kembaliKe('step-media')">&lt;&lt; Kembali</button>
        </div>
    </form>
</div>