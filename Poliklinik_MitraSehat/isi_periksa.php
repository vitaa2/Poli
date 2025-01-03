<?php  
include_once("koneksi.php");  

if (!isset($_SESSION['id'])) {  
    header("Location: index.php?page=loginDokter");  
    exit();  
}  

$id_dokter = $_SESSION['id'];  

// Ambil ID pasien dari parameter URL  
$id_daftar_poli = $_GET['id'];  

// Jika form disubmit  
if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    $catatan = $_POST['catatan'];  
    $id_daftar_poli = $_POST['id_daftar_poli'];  
    
    $biaya_jasa_dokter = 150000; // Biaya jasa dokter tetap  
    $total_biaya_obat = 0; // Total biaya obat  

    // Menyimpan hasil pemeriksaan ke dalam tabel `periksa`  
    $query_periksa = "INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa) VALUES ('$id_daftar_poli', NOW(), '$catatan', 0)";  
    
    if ($mysqli->query($query_periksa)) {  
        $id_periksa = $mysqli->insert_id; // Ambil ID pemeriksaan yang baru saja dimasukkan  

        // Menyimpan obat yang diberikan  
        if (isset($_POST['obat'])) {  
            foreach ($_POST['obat'] as $obat_id) {  
                // Ambil harga obat dari tabel obat  
                $query_harga = "SELECT harga FROM obat WHERE id = '$obat_id'";  
                $result_harga = $mysqli->query($query_harga);  

                if ($result_harga) {  
                    $obat_data = $result_harga->fetch_assoc();  
                    if ($obat_data) {  
                        $total_biaya_obat += $obat_data['harga'];  
                        // Simpan detail periksa  
                        $query_detail = "INSERT INTO detail_periksa (id_periksa, id_obat) VALUES ('$id_periksa', '$obat_id')";  
                        $mysqli->query($query_detail);  
                    }  
                }  
            }  
        }  

        // Hitung total biaya periksa  
        $total_biaya_periksa = $biaya_jasa_dokter + $total_biaya_obat;  

        // Update biaya periksa  
        $update_biaya = "UPDATE periksa SET biaya_periksa = '$total_biaya_periksa' WHERE id = '$id_periksa'";  
        $mysqli->query($update_biaya);  

        header("Location: berandaDokter.php?page=periksa");  
    } else {  
        echo "Error: " . $mysqli->error;  
    }  
}  

// Ambil data pasien berdasarkan ID  
$query_patient = "SELECT daftar_poli.*, pasien.nama AS nama, jadwal_periksa.hari, jadwal_periksa.jam_mulai, jadwal_periksa.jam_selesai  
                  FROM daftar_poli  
                  JOIN pasien ON daftar_poli.id_pasien = pasien.id  
                  JOIN jadwal_periksa ON daftar_poli.id_jadwal = jadwal_periksa.id  
                  WHERE daftar_poli.id = '$id_daftar_poli'";  
$result_patient = $mysqli->query($query_patient);  
$patient_data = $result_patient->fetch_assoc();  

// Ambil daftar obat  
$query_obat = "SELECT * FROM obat";  
$result_obat = $mysqli->query($query_obat);  
?>  

<h2>Pemeriksaan Pasien: <?php echo $patient_data['nama']; ?></h2>  
<form method="POST" action="">  
    <input type="hidden" name="id_daftar_poli" value="<?php echo $id_daftar_poli; ?>">  
    <div class="mb-3">  
        <label for="catatan" class="form-label">Catatan Pemeriksaan</label>  
        <textarea class="form-control" name="catatan" id="catatan" rows="5" required></textarea>  
    </div>  

    <h3>Obat yang Diberikan</h3>  
    <div id="obat-section">  
        <div class="obat-row mb-3">  
            <select name="obat[]" class="form-control mb-2" onchange="updateTotal()" required>  
                <option value="">Pilih Obat</option>  
                <?php while ($obat = $result_obat->fetch_assoc()): ?>  
                    <option value="<?php echo $obat['id']; ?>" data-harga="<?php echo $obat['harga']; ?>">  
                        <?php echo $obat['nama_obat']; ?> (Rp <?php echo number_format($obat['harga'], 0, ',', '.'); ?>)  
                    </option>  
                <?php endwhile; ?>  
            </select>  
            <span class="harga-obat">Harga: Rp. 0</span>  
        </div>  
    </div>  
    <button type="button" class="btn btn-secondary" onclick="addObat()">Tambah Obat</button>  
    
    <h4>Rincian Biaya Pemeriksaan:</h4>  
    <p>Biaya Jasa Dokter: <span id="biaya-jasa">Rp. 150.000</span></p>  
    <p>Total Biaya Obat: <span id="biaya-obat">Rp. 0</span></p>  
    <h4>Total Biaya Periksa: <span id="total-biaya">Rp. 150.000</span></h4>  
    <button type="submit" class="btn btn-primary mt-3">Simpan Pemeriksaan</button>  
</form>  

<script>  
let totalBiaya = 150000;  // Biaya jasa dokter  
let totalBiayaObat = 0; // Total biaya obat  

function updateTotal() {  
    const obatSelects = document.querySelectorAll('select[name="obat[]"]');  
    totalBiayaObat = 0;  

    obatSelects.forEach(select => {  
        const harga = parseInt(select.options[select.selectedIndex].getAttribute('data-harga')) || 0;  
        totalBiayaObat += harga;  
        select.closest('.obat-row').querySelector('.harga-obat').innerText = 'Harga: Rp. ' + harga.toLocaleString();  
    });  

    totalBiaya = 150000 + totalBiayaObat; // Total biaya = biaya jasa dokter + total obat  
    document.getElementById('biaya-obat').innerText = 'Rp. ' + totalBiayaObat.toLocaleString();  
    document.getElementById('total-biaya').innerText = 'Rp. ' + totalBiaya.toLocaleString();  
}  

function addObat() {  
    const obatSection = document.getElementById('obat-section');  
    const newRow = document.createElement('div');  
    newRow.className = 'obat-row mb-3';  
    newRow.innerHTML = `  
        <select name="obat[]" class="form-control mb-2" onchange="updateTotal()" required>  
            <option value="">Pilih Obat</option>  
            <?php  
            // Pastikan untuk menampilkan obat lagi  
            $result_obat->data_seek(0); // Reset pointer hasil query  
            while ($obat = $result_obat->fetch_assoc()) {  
                echo '<option value="' . $obat['id'] . '" data-harga="' . $obat['harga'] . '">' . $obat['nama_obat'] . ' (Rp ' . number_format($obat['harga'], 0, ',', '.') . ')</option>';  
            }  
            ?>  
        </select>  
        <span class="harga-obat">Harga: Rp. 0</span>  
    `;  
    obatSection.appendChild(newRow);  
}  
</script>