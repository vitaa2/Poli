<?php
if (isset($_POST['simpanData'])) {
    $id_dokter = $_SESSION['id'];
    $statues = $_POST['statues'];

    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Jika status ingin diubah menjadi 'Aktif'
        if ($statues == 1) {
            // Cek apakah ada jadwal aktif lain selain yang sedang diedit
            $stmt_check = $mysqli->prepare("SELECT COUNT(*) FROM jadwal_periksa WHERE id_dokter=? AND statues=1 AND id!=?");
            $stmt_check->bind_param("ii", $id_dokter, $id);
            $stmt_check->execute();
            $stmt_check->bind_result($activeCount);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($activeCount > 0) {
                echo "
                    <script>
                        alert('Gagal! Tidak bisa mengubah status karena sudah ada jadwal aktif lainnya.');
                    </script>
                ";
                return; // Hentikan eksekusi
            }
        }

        // Update status jadwal
        $stmt = $mysqli->prepare("UPDATE jadwal_periksa SET statues=? WHERE id=?");
        $stmt->bind_param("ii", $statues, $id);

        if ($stmt->execute()) {
            echo "
                <script>
                    alert('Berhasil mengubah data.');
                    document.location='berandaDokter.php?page=aturJadwalDokter';
                </script>
            ";
        } else {
            echo "
                <script>
                    alert('Gagal mengubah data.');
                </script>
            ";
        }
        $stmt->close();
    } else {
        $hari = $_POST['hari'];
        $jam_mulai = $_POST['jam_mulai'];
        $jam_selesai = $_POST['jam_selesai'];

        // Cek apakah ada jadwal aktif lain jika ingin menyimpan dengan status aktif
        if ($statues == 1) {
            $stmt_check = $mysqli->prepare("SELECT COUNT(*) FROM jadwal_periksa WHERE id_dokter=? AND statues=1");
            $stmt_check->bind_param("i", $id_dokter);
            $stmt_check->execute();
            $stmt_check->bind_result($activeCount);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($activeCount > 0) {
                echo "
                    <script>
                        alert('Gagal! Tidak bisa menyimpan data karena sudah ada jadwal aktif lainnya.');
                    </script>
                ";
                return; // Hentikan eksekusi
            }
        }

        // Tambahkan jadwal baru
        $stmt = $mysqli->prepare("INSERT INTO jadwal_periksa (id_dokter, hari, jam_mulai, jam_selesai, statues) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $id_dokter, $hari, $jam_mulai, $jam_selesai, $statues);

        if ($stmt->execute()) {
            echo "
                <script>
                    alert('Berhasil menambah data.');
                    document.location='berandaDokter.php?page=aturJadwalDokter';
                </script>
            ";
        } else {
            echo "
                <script>
                    alert('Gagal menambah data.');
                </script>
            ";
        }
        $stmt->close();
    }
}
?>
<main id="aturJadwalDokter-page">  
    <div class="container" style="margin-top: 5.5rem;">  
        <div class="row">  
            <h2 class="ps-0">Jadwal Dokter</h2>  
            <div class="container">  
                <form action="" method="POST">  
                    <?php  
                    $id_dokter = '';  
                    $hari = '';  
                    $jam_mulai = '';  
                    $jam_selesai = '';  
                    $statues = '';  
                    if (isset($_GET['id'])) {  
                        $get = mysqli_query($mysqli, "SELECT * FROM jadwal_periksa WHERE id='" . $_GET['id'] . "'");  
                        while ($row = mysqli_fetch_array($get)) {  
                            $id_dokter = $row['id_dokter'];  
                            $hari = $row['hari'];  
                            $jam_mulai = $row['jam_mulai'];  
                            $jam_selesai = $row['jam_selesai'];  
                            $statues = $row['statues'];  
                        }  
                    ?>  
                        <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">  
                    <?php  
                    }  
                    ?>  
                    <div class="dropdown mb-3 w-25">  
                        <label for="hari">Hari <span class="text-danger">*</span></label>  
                        <select class="form-select" name="hari" aria-label="hari" <?php echo isset($_GET['id']) ? 'disabled' : ''; ?>>  
                            <option value="" selected>Pilih Hari...</option>  
                            <option value="Senin" <?php echo $hari == 'Senin' ? 'selected' : ''; ?>>Senin</option>  
                            <option value="Selasa" <?php echo $hari == 'Selasa' ? 'selected' : ''; ?>>Selasa</option>  
                            <option value="Rabu" <?php echo $hari == 'Rabu' ? 'selected' : ''; ?>>Rabu</option>  
                            <option value="Kamis" <?php echo $hari == 'Kamis' ? 'selected' : ''; ?>>Kamis</option>  
                            <option value="Jumat" <?php echo $hari == "Jumat" ? 'selected' : ''; ?>>Jumat</option>  
                            <option value="Sabtu" <?php echo $hari == 'Sabtu' ? 'selected' : ''; ?>>Sabtu</option>  
                        </select>  
                    </div>  
                    <div class="mb-3 w-25">  
                        <label for="jam_mulai">Jam Mulai <span class="text-danger">*</span></label>  
                        <input type="time" name="jam_mulai" class="form-control" required value="<?php echo $jam_mulai ?>" <?php echo isset($_GET['id']) ? 'disabled' : ''; ?>>  
                    </div>  
                    <div class="mb-3 w-25">  
                        <label for="jam_selesai">Jam Selesai <span class="text-danger">*</span></label>  
                        <input type="time" name="jam_selesai" class="form-control" required value="<?php echo $jam_selesai ?>" <?php echo isset($_GET['id']) ? 'disabled' : ''; ?>>  
                    </div>  
                    <div class="dropdown mb-3 w-25">  
                        <label for="statues">Status <span class="text-danger">*</span></label>  
                        <select class="form-select" name="statues" aria-label="statues">  
                            <option value="1" <?php echo $statues == 1 ? 'selected' : ''; ?>>Aktif</option>  
                            <option value="0" <?php echo $statues == 0 ? 'selected' : ''; ?>>Nonaktif</option>  
                        </select>  
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
                            <th>Nama Dokter</th>  
                            <th>Hari</th>  
                            <th colspan="2">Waktu</th>  
                            <th>Status</th>  
                            <th>Aksi</th>  
                        </tr>  
                    </thead>  
                    <tbody>  
                        <?php  
                        $id_dokter = $_SESSION['id'];  
                        $result = mysqli_query($mysqli, "SELECT dokter.nama, jadwal_periksa.id, jadwal_periksa.hari, jadwal_periksa.jam_mulai, jadwal_periksa.jam_selesai, jadwal_periksa.statues   
                            FROM dokter   
                            JOIN jadwal_periksa ON dokter.id = jadwal_periksa.id_dokter   
                            WHERE dokter.id = $id_dokter");  
                        $no = 1;  
                        while ($data = mysqli_fetch_array($result)) :  
                        ?>  
                            <tr>  
                                <td><?php echo $no++ ?></td>  
                                <td><?php echo $data['nama'] ?></td>  
                                <td><?php echo $data['hari'] ?></td>  
                                <td><?php echo $data['jam_mulai'] ?> WIB</td>  
                                <td><?php echo $data['jam_selesai'] ?> WIB</td>  
                                <td>  
                                    <?php   
                                        echo ($data['statues'] == 1)   
                                        ? '<p class="bg-success text-white border rounded p-1 mb-0">Aktif</p>'   
                                        : '<p class="bg-danger text-white border rounded p-1 mb-0">Nonaktif</p>';   
                                    ?>  
                                </td>  
                                <td>  
                                    <a class="btn btn-sm btn-warning text-white" href="berandaDokter.php?page=aturJadwalDokter&id=<?php echo $data['id'] ?>">  
                                        <i class="fa-solid fa-pen-to-square"></i>  
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