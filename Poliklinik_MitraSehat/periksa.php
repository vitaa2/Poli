<?php  
include_once("koneksi.php"); // Pastikan ini ada di file ini dan mengarah dengan benar  

// session_start();  

if (!isset($_SESSION['id'])) {  
    header("Location: index.php?page=loginDokter");  
    exit();  
}  

$id_dokter = $_SESSION['id'];  

// Query untuk mengambil data pasien yang belum diperiksa  
$result = mysqli_query($mysqli, "  
    SELECT daftar_poli.*, pasien.nama AS nama, jadwal_periksa.hari, jadwal_periksa.jam_mulai, jadwal_periksa.jam_selesai  
    FROM daftar_poli  
    JOIN pasien ON daftar_poli.id_pasien = pasien.id  
    JOIN jadwal_periksa ON daftar_poli.id_jadwal = jadwal_periksa.id  
    WHERE jadwal_periksa.id_dokter = '$id_dokter' AND daftar_poli.id NOT IN (SELECT id_daftar_poli FROM periksa)  
");  

if (!$result) {  
    die("Query error: " . mysqli_error($mysqli));  
}  

// Hitung jumlah baris  
$total_rows = mysqli_num_rows($result);  

?>  

<h2>Daftar Pasien untuk Diperiksa</h2>  
<table class="table text-center mt-3">  
    <thead class="table-primary">  
        <tr>  
            <th>No</th>  
            <th>Nama Pasien</th>  
            <th>Hari</th>  
            <th>Jam Periksa</th>  
            <th>Keluhan</th>  
            <th>Aksi</th>  
        </tr>  
    </thead>  
    <tbody>  
        <?php if ($total_rows > 0): ?>  
            <?php $no = 1; while ($data = mysqli_fetch_array($result)): ?>  
                <tr>  
                    <td><?php echo $no++ ?></td>  
                    <td><?php echo $data['nama'] ?></td>  
                    <td><?php echo $data['hari'] ?></td>  
                    <td><?php echo $data['jam_mulai'] . ' - ' . $data['jam_selesai'] ?></td>  
                    <td><?php echo $data['keluhan'] ?></td>  
                    <td>  
                        <a class="btn btn-sm btn-warning text-white" href="berandaDokter.php?page=isi_periksa&id=<?php echo $data['id'] ?>">Periksa</a>  
                    </td>  
                </tr>  
            <?php endwhile; ?>  
        <?php else: ?>  
            <tr>  
                <td colspan="6">Tidak ada data pasien untuk ditampilkan.</td>  
            </tr>  
        <?php endif; ?>  
    </tbody>  
</table>