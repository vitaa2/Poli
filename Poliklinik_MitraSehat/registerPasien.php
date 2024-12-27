<?php
if (!isset($_SESSION)) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $no_ktp = $_POST['no_ktp'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $password = $_POST['password'];  // Ambil password dari form

    // Pastikan koneksi ke database
    if (!$mysqli) {
        die("Koneksi ke database gagal: " . mysqli_connect_error());
    }

    // Periksa apakah nama sudah digunakan
    $query = $mysqli->prepare("SELECT * FROM pasien WHERE nama = ?");
    $query->bind_param("s", $nama);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows == 0) {
        // Ambil tahun dan bulan saat ini
        $year_month = date('Ym'); // Format YYYYMM

        // Hitung jumlah pasien yang terdaftar pada bulan dan tahun yang sama
        $query = $mysqli->prepare("SELECT COUNT(*) AS total FROM pasien WHERE DATE_FORMAT(tanggal_daftar, '%Y%m') = ?");
        $query->bind_param("s", $year_month);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        $totalPasien = $row['total'];

        // Nomor RM adalah tahun-bulan-urutan
        $no_rm = $year_month . '-' . str_pad($totalPasien + 1, 3, '0', STR_PAD_LEFT); // Menambahkan urutan pasien dengan padding 0

        // Masukkan data pasien ke database, tanpa hashing password
        $insert_query = $mysqli->prepare("INSERT INTO pasien (nama, no_ktp, alamat, no_hp, no_rm, password, tanggal_daftar) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $insert_query->bind_param("ssssss", $nama, $no_ktp, $alamat, $no_hp, $no_rm, $password);

        if ($insert_query->execute()) {
            $_SESSION['no_rm'] = $no_rm; // Simpan no_rm dalam session
            echo "<script>
            alert('Pendaftaran Berhasil. Nomor RM Anda adalah: $no_rm'); 
            document.location='index.php?page=cekRM';
            </script>";
        } else {
            $error = "Pendaftaran gagal";
        }
    } else {
        $error = "Nama sudah digunakan";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center" style="font-weight: bold; font-size: 32px;">Register</div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=registerPasien">
                        <?php
                        if (isset($error)) {
                            echo '<div class="alert alert-danger">' . $error . '
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>';
                        }
                        ?>
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama anda">
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input type="text" name="alamat" class="form-control" required placeholder="Masukkan alamat anda">
                        </div>
                        <div class="form-group">
                            <label for="no_ktp">No. KTP</label>
                            <input type="text" name="no_ktp" class="form-control" required placeholder="Masukkan No. KTP">
                        </div>
                        <div class="form-group">
                            <label for="no_hp">No. HP</label>
                            <input type="text" name="no_hp" class="form-control" required placeholder="Masukkan No. HP anda">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Masukkan password anda">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>
                    </form>
                    <div class="text-center">
                        <p class="mt-3">Sudah Terdaftar? <a href="index.php?page=CekRM">Cek Nomor RM</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
