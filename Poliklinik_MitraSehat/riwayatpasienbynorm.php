<?php
if (!isset($_SESSION)) {
    session_start();
}

// Cek apakah no_rm di-submit
if (isset($_POST['cari'])) {
    $no_rm = $_POST['no_rm'];
    $query = "SELECT daftar_poli.*, pasien.nama AS nama, jadwal_periksa.hari, 
                     periksa.tgl_periksa, periksa.catatan, periksa.biaya_periksa, 
                     obat.nama_obat AS nama_obat
              FROM daftar_poli
              JOIN jadwal_periksa ON daftar_poli.id_jadwal = jadwal_periksa.id 
              JOIN pasien ON daftar_poli.id_pasien = pasien.id
              LEFT JOIN periksa ON daftar_poli.id = periksa.id_daftar_poli
              LEFT JOIN detail_periksa ON periksa.id = detail_periksa.id_periksa
              LEFT JOIN obat ON detail_periksa.id_obat = obat.id
              WHERE pasien.no_rm = '$no_rm'";
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
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($data = mysqli_fetch_array($result)) {
                    ?>
                    <tr>
                        <th scope="row"><?php echo $no++ ?></th>
                        <td>
                            <?php echo $data['tgl_periksa'] ?: 'Belum Diperiksa'; ?>
                        </td>
                        <td><?php echo $data['nama'] ?></td>
                        <td class="text-center"><?php echo $data['no_antrian'] ?></td>
                        <td><?php echo $data['keluhan'] ?></td>
                        <td class="text-center">
                            <?php 
                            echo $data['tgl_periksa'] ? 'Sudah Diperiksa' : 'Belum Diperiksa';
                            ?>
                        </td>
                        <td class="text-center">
                            <!-- Tombol untuk melihat rincian -->
                            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalRincian<?php echo $data['id']; ?>">Lihat Rincian</button>
                        </td>
                    </tr>

                    <!-- Modal Rincian -->
                    <div class="modal fade" id="modalRincian<?php echo $data['id']; ?>" tabindex="-1" aria-labelledby="modalRincianLabel<?php echo $data['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalRincianLabel<?php echo $data['id']; ?>">Rincian Riwayat Periksa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Nama Pasien:</strong> <?php echo $data['nama']; ?></p>
                                    <p><strong>Tanggal Periksa:</strong> <?php echo $data['tgl_periksa'] ?: 'Belum Diperiksa'; ?></p>
                                    <p><strong>Keluhan:</strong> <?php echo $data['keluhan']; ?></p>
                                    <p><strong>Catatan:</strong> <?php echo $data['catatan'] ?: 'Tidak Ada Catatan'; ?></p>
                                    <p><strong>Biaya Periksa:</strong> <?php echo $data['biaya_periksa'] ?: 'Belum Diperiksa'; ?></p>
                                    <p><strong>Nama Obat:</strong> <?php echo $data['nama_obat'] ?: 'Tidak Ada Obat'; ?></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </tbody>
        </table>
    <?php elseif (isset($result)): ?>
        <p class="text-danger">Tidak ada data riwayat periksa untuk nomor rekam medis tersebut.</p>
    <?php endif; ?>
</div>

<!-- Pastikan untuk menambahkan Bootstrap JS dan CSS di footer untuk modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
