<?php
if (!isset($_SESSION)) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $no_ktp = $_POST['no_ktp'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];

    if ($no_ktp) {
        $query = "SELECT * FROM pasien WHERE nama = '$nama'";
        $result = $mysqli->query($query);

        if ($result === false) {
            die("Query error: " . $mysqli->error);
        }

        if ($result->num_rows == 0) {
            $hashed_ktp = password_hash($no_ktp, PASSWORD_DEFAULT);

            // Get the total number of pasien
            $result = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM pasien");
            $row = mysqli_fetch_assoc($result);
            $totalPasien = $row['total'];

            // Generate no_rm based on the current date and total number of pasien
            $no_rm = date('Y-m-d') . '-' . ($totalPasien + 1);

            $insert_query = "INSERT INTO pasien (nama, no_ktp, alamat, no_hp, no_rm) VALUES ('$nama', '$hashed_ktp', '$alamat', '$no_hp', '$no_rm')";
            if (mysqli_query($mysqli, $insert_query)) {
                $_SESSION['no_rm'] = $no_rm; // Store no_rm in session
                echo "<script>
                alert('Pendaftaran Berhasil. Nomor RM Anda adalah: $no_rm'); 
                document.location='index.php?page=cekRM';
                </script>";
            } else {
                $error = "Pendaftaran gagal";
            }
        } else {
            $error = "nama sudah digunakan";
        }
    } else {
        $error = "No.KTP tidak cocok";
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
                            <label for="nama">nama</label>
                            <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama anda">
                        </div>
                        <div class="form-group">
                            <label for="alamat">alamat</label>
                            <input type="text" name="alamat" class="form-control" required placeholder="Masukkan alamat anda">
                        </div>
                        <div class="form-group">
                            <label for="no_ktp">No_KTP</label>
                            <input type="no_ktp" name="no_ktp" class="form-control" required placeholder="Masukkan no ktp anda">
                        </div>
                        <div class="form-group">
                            <label for="no_hp">No.HP</label>
                            <input type="text" name="no_hp" class="form-control" required placeholder="Masukkan no hp anda">
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