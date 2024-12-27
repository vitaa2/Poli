<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['nama'])) {
    // Jika pengguna belum login, redirect ke halaman login
    header("Location: index.php?page=daftarPasien");
    exit;
}

// Cek apakah no_rm di-submit
if (isset($_POST['cari'])) {
    $no_rm = $_POST['no_rm'];
    $query = "SELECT daftar_poli.*, pasien.nama AS nama, jadwal_periksa.hari, periksa.tgl_periksa, periksa.catatan, periksa.biaya_periksa, obat.nama_obat AS nama_obat
              FROM daftar_poli
              JOIN jadwal_periksa ON daftar_poli.id_jadwal = jadwal_periksa.id 
              JOIN pasien ON daftar_poli.id_pasien = pasien.id
              LEFT JOIN periksa ON daftar_poli.id = periksa.id_daftar_poli
              LEFT JOIN detail_periksa ON periksa.id = detail_periksa.id_periksa
              LEFT JOIN obat ON detail_periksa.id_obat = obat.id
              WHERE pasien.no_rm = '$no_rm' AND periksa.id_daftar_poli IS NOT NULL";
    $result = mysqli_query($mysqli, $query);
}
?>

<h2>Cek Riwayat Periksa</h2>
<br>

<div class="container">
    <!-- Form input no_rm -->
    <form method="POST" action="">
        <div class="mb-3">
            <label for="no_rm" class="form-label">Masukkan Nomor Rekam Medis (no_rm):</label>
            <input type="text" class="form-control" id="no_rm" name="no_rm" required>
        </div>
        <button type="submit" name="cari" class="btn btn-primary">Cek Riwayat</button>
    </form>

    <br>

    <?php if (isset($result) && mysqli_num_rows($result) > 0): ?>
        <!-- Tabel riwayat periksa -->
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Tanggal Periksa</th>
                    <th>Nama Pasien</th>
                    <th>Nomor Antrian</th>
                    <th>Keluhan</th>
                    <th>Catatan</th>
                    <th>Biaya Periksa</th>
                    <th>Nama Obat</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($data = mysqli_fetch_array($result)) {
                    ?>
                    <tr>
                        <th scope="row"><?php echo $no++ ?></th>
                        <td><?php echo $data['tgl_periksa'] ?></td>
                        <td><?php echo $data['nama'] ?></td>
                        <td class="text-center"><?php echo $data['no_antrian'] ?></td>
                        <td><?php echo $data['keluhan'] ?></td>
                        <td><?php echo $data['catatan'] ?></td>
                        <td class="text-center"><?php echo $data['biaya_periksa'] ?></td>
                        <td><?php echo $data['nama_obat'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php elseif (isset($result)): ?>
        <p class="text-danger">Tidak ada data riwayat periksa untuk nomor rekam medis tersebut.</p>
    <?php endif; ?>
</div>
