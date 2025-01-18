<?php
// Mulai session untuk memastikan dokter sudah login

include('koneksi.php'); // Pastikan Anda menyertakan koneksi database di sini

// Ambil id dokter dari session atau URL
$id_dokter = $_SESSION['id']; // Asumsi id dokter sudah ada di session

// Ambil data dokter dari database untuk ditampilkan di form
$query = "SELECT * FROM dokter WHERE id = '$id_dokter'";
$result = mysqli_query($mysqli, $query);
$doctor = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    // Ambil data yang dikirim dari form
    $nama = $_POST['nama'];
    $no_telp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];

    // Update data dokter di database
    $updateQuery = "UPDATE dokter SET nama = '$nama', no_hp = '$no_telp', alamat = '$alamat' WHERE id = '$id_dokter'";
    if (mysqli_query($mysqli, $updateQuery)) {
        echo "<script>alert('Profil berhasil diperbarui'); window.location.href='berandaDokter.php?page=updatedokter';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil');</script>";
    }
}
?>

<!-- Halaman Update Profil -->
<main id="update-profile">
    <div class="container">
        <h2>Update Profil Dokter</h2>

        <form method="POST" action="">
            <div class="mb-5 mt-4">
                <label for="nama" class="form-label">Nama:</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $doctor['nama']; ?>" required>
            </div>

            <div class="mb-5">
                <label for="no_hp" class="form-label">No. Telepon:</label>
                <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo $doctor['no_hp']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat:</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="4" required><?php echo $doctor['alamat']; ?></textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</main>
