<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<main id="periksapasien-page">
    <div class="container" style="margin-top: 5.5rem;">
        <div class="row">
            <h2 class="ps-0">Riwayat Periksa Pasien</h2>

            <div class="table-responsive mt-3 px-0">
                <table class="table text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Nama Pasien</th>
                            <th>No. Antrian</th>
                            <th>Keluhan</th>
                            <th>Hari</th>
                            <th>Tanggal Diperiksa</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $id_dokter = $_SESSION['id'];

                        // Query untuk mengambil data pasien
                        $result = mysqli_query($mysqli, "
                            SELECT daftar_poli.*, pasien.nama AS nama, jadwal_periksa.hari, 
                                   periksa.tgl_periksa, periksa.catatan, periksa.biaya_periksa, 
                                   GROUP_CONCAT(obat.nama_obat SEPARATOR ', ') AS nama_obat,
                                   CASE 
                                       WHEN periksa.id IS NULL THEN 'Belum Diperiksa'
                                       ELSE 'Sudah Diperiksa'
                                   END AS status
                            FROM daftar_poli
                            JOIN jadwal_periksa ON daftar_poli.id_jadwal = jadwal_periksa.id 
                            JOIN pasien ON daftar_poli.id_pasien = pasien.id
                            LEFT JOIN periksa ON daftar_poli.id = periksa.id_daftar_poli
                            LEFT JOIN detail_periksa ON periksa.id = detail_periksa.id_periksa
                            LEFT JOIN obat ON detail_periksa.id_obat = obat.id
                            WHERE jadwal_periksa.id_dokter = '$id_dokter'
                            GROUP BY daftar_poli.id, pasien.nama, jadwal_periksa.hari, periksa.tgl_periksa, periksa.catatan, periksa.biaya_periksa
                        ");

                        $no = 1;
                        while ($data = mysqli_fetch_array($result)) :
                            $modalId = "modalDetail" . $no; // ID unik untuk setiap modal
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $data['nama']; ?></td>
                                <td><?php echo $data['no_antrian']; ?></td>
                                <td><?php echo $data['keluhan']; ?></td>
                                <td><?php echo $data['hari']; ?></td>
                                <td><?php echo $data['tgl_periksa'] ? $data['tgl_periksa'] : '-'; ?></td>
                                <td>
                                    <!-- Tombol untuk membuka modal -->
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#<?php echo $modalId; ?>">
                                        <?php echo $data['status']; ?>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal untuk detail pasien -->
                            <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo $no; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabel<?php echo $no; ?>">Detail Pasien</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Nama:</strong> <?php echo $data['nama']; ?></p>
                                            <p><strong>No. Antrian:</strong> <?php echo $data['no_antrian']; ?></p>
                                            <p><strong>Keluhan:</strong> <?php echo $data['keluhan']; ?></p>
                                            <p><strong>Hari:</strong> <?php echo $data['hari']; ?></p>
                                            <p><strong>Tanggal Diperiksa:</strong> <?php echo $data['tgl_periksa'] ? $data['tgl_periksa'] : '-'; ?></p>
                                            <p><strong>Catatan:</strong> <?php echo $data['catatan'] ? $data['catatan'] : 'Belum ada catatan'; ?></p>
                                            <p><strong>Biaya Periksa:</strong> <?php echo $data['biaya_periksa'] ? $data['biaya_periksa'] : 'Belum ditentukan'; ?></p>
                                            <p><strong>Nama Obat:</strong> <?php echo $data['nama_obat'] ? $data['nama_obat'] : 'Belum ada obat'; ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
