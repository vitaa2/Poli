<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: index.php?page=loginUser");
    exit;
}

if (isset($_POST['simpanData'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_ktp = $_POST['no_ktp'];
    $no_hp = $_POST['no_hp'];

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "UPDATE pasien SET nama='$nama', alamat='$alamat', no_ktp='$no_ktp', no_hp='$no_hp' WHERE id='$id'";
        $edit = mysqli_query($mysqli, $sql);

        echo "
            <script> 
                alert('Berhasil mengubah data.');
                document.location='index.php?page=pasien';
            </script>
        ";
    } else {
        $currentMonth = date('Ym'); 
        $query = "SELECT COUNT(*) as total FROM pasien WHERE no_rm LIKE '$currentMonth%'";
        $result = mysqli_query($mysqli, $query);
        $row = mysqli_fetch_assoc($result);
        $urutPasien = $row['total'] + 1;

        $no_rm = sprintf('%s-%03d', $currentMonth, $urutPasien);

        $sql = "INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm) 
                VALUES ('$nama', '$alamat', '$no_ktp', '$no_hp', '$no_rm')";
        $tambah = mysqli_query($mysqli, $sql);

        echo "
            <script> 
                alert('Berhasil menambah data.');
                document.location='index.php?page=pasien';
            </script>
        ";
    }
}

if (isset($_GET['aksi'])) {
    if ($_GET['aksi'] == 'hapus') {
        $id = $_GET['id'];

        // Hapus data terkait di tabel lain sebelum menghapus pasien
        $deleteDetailPeriksa = mysqli_query($mysqli, "DELETE FROM detail_periksa WHERE id_periksa IN (SELECT id FROM periksa WHERE id_daftar_poli IN (SELECT id FROM daftar_poli WHERE id_pasien = '$id'))");
        $deletePeriksa = mysqli_query($mysqli, "DELETE FROM periksa WHERE id_daftar_poli IN (SELECT id FROM daftar_poli WHERE id_pasien = '$id')");
        $deleteDaftarPoli = mysqli_query($mysqli, "DELETE FROM daftar_poli WHERE id_pasien = '$id'");

        if ($deleteDetailPeriksa && $deletePeriksa && $deleteDaftarPoli) {
            $hapus = mysqli_query($mysqli, "DELETE FROM pasien WHERE id = '$id'");

            if ($hapus) {
                echo "
                    <script> 
                        alert('Berhasil menghapus data.');
                        document.location='index.php?page=pasien';
                    </script>
                ";
            } else {
                echo "
                    <script> 
                        alert('Gagal menghapus data: " . mysqli_error($mysqli) . "');
                        document.location='index.php?page=pasien';
                    </script>
                ";
            }
        } else {
            echo "
                <script> 
                    alert('Gagal menghapus data: " . mysqli_error($mysqli) . "');
                    document.location='index.php?page=pasien';
                </script>
            ";
        }
    }
}
?>
<main id="pasien-page">
    <div class="container" style="margin-top: 5.5rem;">
        <div class="row">
            <h2 class="ps-0">Halaman Pasien</h2>
            <div class="container">
                <form action="" method="POST" onsubmit="return(validate());">
                    <?php
                    $nama = '';
                    $alamat = '';
                    $no_ktp = '';
                    $no_hp = '';
                    if (isset($_GET['id'])) {
                        $get = mysqli_query($mysqli, "SELECT * FROM pasien 
                                WHERE id='" . $_GET['id'] . "'");
                        while ($row = mysqli_fetch_array($get)) {
                            $nama = $row['nama'];
                            $alamat = $row['alamat'];
                            $no_ktp = $row['no_ktp'];
                            $no_hp = $row['no_hp'];
                        }
                    ?>
                        <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
                    <?php
                    }
                    ?>
                    <div class="mb-3 w-25">
                        <label for="nama">Nama Pasien <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama pasien" value="<?php echo $nama ?>">
                    </div>
                    <div class="mb-3 w-25">
                        <label for="alamat">Alamat <span class="text-danger">*</span></label>
                        <input type="text" name="alamat" class="form-control" required placeholder="Masukkan alamat" value="<?php echo $alamat ?>">
                    </div>
                    <div class="mb-3 w-25">
                        <label for="no_ktp">No. KTP <span class="text-danger">*</span></label>
                        <input type="number" name="no_ktp" class="form-control" required placeholder="16 digit No. KTP" value="<?php echo $no_ktp ?>">
                    </div>
                    <div class="mb-3 w-25">
                        <label for="no_hp">No. HP <span class="text-danger">*</span></label>
                        <input type="number" name="no_hp" class="form-control" required placeholder="08xxxx" value="<?php echo $no_hp ?>">
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <button type="submit" name="simpanData" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive mt-3 px-0">
                <table class="table text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Nama Pasien</th>
                            <th>Alamat</th>
                            <th>No. KTP</th>
                            <th>No. HP</th>
                            <th>No. RM</th>
                            <th colspan="2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $result = mysqli_query($mysqli, "SELECT * FROM pasien");
                            $no = 1;
                            while ($data = mysqli_fetch_array($result)) :
                            ?>
                                <tr>
                                    <td><?php echo $no++ ?></td>
                                    <td><?php echo $data['nama'] ?></td>
                                    <td><?php echo $data['alamat'] ?></td>
                                    <td><?php echo $data['no_ktp'] ?></td>
                                    <td><?php echo $data['no_hp'] ?></td>
                                    <td><?php echo $data['no_rm'] ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-warning text-white" href="index.php?page=pasien&id=<?php echo $data['id'] ?>">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="index.php?page=pasien&id=<?php echo $data['id'] ?>&aksi=hapus" class="btn btn-sm btn-danger text-white">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
