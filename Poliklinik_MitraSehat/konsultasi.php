<?php
include 'koneksi.php'; // Pastikan Anda sudah mengonfigurasi koneksi database

// Pastikan pasien sudah login
if (!isset($_SESSION['id_pasien'])) {
    header('Location: cekRM.php');
    exit();
}

// Proses menambah pertanyaan
if (isset($_POST['tambah'])) {
    $subject = $_POST['subject'];
    $pertanyaan = $_POST['pertanyaan'];
    $id_pasien = $_SESSION['id_pasien'];  // ID pasien dari session
    $id_dokter = $_POST['id_dokter'];  // ID dokter yang dipilih
    $tgl_konsultasi = date('Y-m-d H:i:s');

    $query = "INSERT INTO konsultasi (subject, pertanyaan, tgl_konsultasi, id_pasien, id_dokter) 
              VALUES ('$subject', '$pertanyaan', '$tgl_konsultasi', '$id_pasien', '$id_dokter')";
    
    if (mysqli_query($mysqli, $query)) {
        echo "<script>alert('Pertanyaan berhasil ditambahkan!'); window.location.href='konsultasi.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah pertanyaan.');</script>";
    }
}

// Proses mengedit pertanyaan
if (isset($_POST['edit'])) {
    $id_konsultasi = $_POST['id_konsultasi'];
    $subject = $_POST['subject'];
    $pertanyaan = $_POST['pertanyaan'];
    $id_dokter = $_POST['id_dokter'];

    $update_query = "UPDATE konsultasi SET subject = '$subject', pertanyaan = '$pertanyaan', id_dokter = '$id_dokter' 
                     WHERE id = '$id_konsultasi' AND id_pasien = '".$_SESSION['id_pasien']."'";
    
    if (mysqli_query($mysqli, $update_query)) {
        echo "<script>alert('Pertanyaan berhasil diperbarui!'); window.location.href='konsultasi.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui pertanyaan.');</script>";
    }
}

// Proses menghapus pertanyaan
if (isset($_GET['hapus'])) {
    $id_konsultasi = $_GET['hapus'];

    $delete_query = "DELETE FROM konsultasi WHERE id = '$id_konsultasi' AND id_pasien = '".$_SESSION['id_pasien']."'";
    
    if (mysqli_query($mysqli, $delete_query)) {
        echo "<script>alert('Pertanyaan berhasil dihapus!'); window.location.href='konsultasi.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus pertanyaan.');</script>";
    }
}

// Mengambil daftar konsultasi pasien
$query = "SELECT konsultasi.*, dokter.nama AS nama_dokter FROM konsultasi 
          JOIN dokter ON konsultasi.id_dokter = dokter.id
          WHERE konsultasi.id_pasien = '".$_SESSION['id_pasien']."' 
          ORDER BY tgl_konsultasi DESC";
$result = mysqli_query($mysqli, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi Medis Pasien</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Aplikasi Kesehatan</a>
    </div>
</nav>

<div class="container">
    <h2>Konsultasi Medis Pasien</h2>
    <br>
    <!-- Tombol untuk membuka modal tambah pertanyaan -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Pertanyaan</button>
    <br><br>

    <!-- Tabel Daftar Pertanyaan -->
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Subject</th>
                <th>Pertanyaan</th>
                <th>Jawaban</th>
                <th>Tanggal Konsultasi</th>
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
                    <td><?php echo $data['subject'] ?></td>
                    <td><?php echo $data['pertanyaan'] ?></td>
                    <td><?php echo $data['jawaban'] ?: 'Belum Dijawab' ?></td>
                    <td><?php echo $data['tgl_konsultasi'] ?></td>
                    <td class="text-center">
                        <!-- Edit dan Hapus tombol -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $data['id']; ?>">Edit</button>
                        <a href="konsultasi.php?hapus=<?php echo $data['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>

                <!-- Modal Edit Pertanyaan -->
                <div class="modal fade" id="modalEdit<?php echo $data['id']; ?>" tabindex="-1" aria-labelledby="modalEditLabel<?php echo $data['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalEditLabel<?php echo $data['id']; ?>">Edit Pertanyaan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="konsultasi.php">
                                    <input type="hidden" name="id_konsultasi" value="<?php echo $data['id']; ?>">
                                    <div class="mb-3">
                                        <label for="subject" class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="subject" name="subject" value="<?php echo $data['subject']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pertanyaan" class="form-label">Pertanyaan</label>
                                        <textarea class="form-control" id="pertanyaan" name="pertanyaan" rows="4" required><?php echo $data['pertanyaan']; ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="id_dokter" class="form-label">Pilih Dokter</label>
                                        <select class="form-select" id="id_dokter" name="id_dokter" required>
                                            <option value="">-- Pilih Dokter --</option>
                                            <?php 
                                            // Ambil daftar dokter
                                            $dokter_query = "SELECT * FROM dokter";
                                            $dokter_result = mysqli_query($mysqli, $dokter_query);
                                            while ($dokter = mysqli_fetch_array($dokter_result)) {
                                                $selected = ($data['id_dokter'] == $dokter['id']) ? "selected" : "";
                                                echo "<option value='" . $dokter['id'] . "' $selected>" . $dokter['nama'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="edit" class="btn btn-warning">Perbarui Pertanyaan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah Pertanyaan -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahLabel">Tambah Pertanyaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="konsultasi.php">
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="pertanyaan" class="form-label">Pertanyaan</label>
                        <textarea class="form-control" id="pertanyaan" name="pertanyaan" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="id_dokter" class="form-label">Pilih Dokter</label>
                        <select class="form-select" id="id_dokter" name="id_dokter" required>
                            <option value="">-- Pilih Dokter --</option>
                            <?php 
                            // Ambil daftar dokter
                            $dokter_query = "SELECT * FROM dokter";
                            $dokter_result = mysqli_query($mysqli, $dokter_query);
                            while ($dokter = mysqli_fetch_array($dokter_result)) {
                                echo "<option value='" . $dokter['id'] . "'>" . $dokter['nama'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="tambah" class="btn btn-primary">Tambah Pertanyaan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
