<?php  
session_start();  
include_once("koneksi.php"); // Pastikan ini mengarah ke file koneksi database  

if (!isset($_SESSION['id'])) {  
    header("Location: index.php?page=loginDokter"); // Redirect ke login jika belum login  
    exit();  
}  

// Ambil ID dokter dari sesi  
$id_dokter = $_SESSION['id'];  
?>  

<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="utf-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">  
    <title>Beranda Dokter</title>  
</head>  
<body style="background-color: #DEE6F6;">  
    <nav class="navbar fixed-top navbar-expand-lg py-3 navbar-dark" style="background-color: #6495ED;">  
        <div class="container d-flex align-items-center">  
            <a class="navbar-brand" href="berandaDokter.php">Poliklinik</a>  
            <div class="collapse navbar-collapse">  
                <ul class="navbar-nav ms-auto">  
                    <li class="nav-item">  
                        <a class="nav-link active" href="berandaDokter.php">Home</a>  
                    </li>  
                    <li class="nav-item">  
                        <a class="nav-link" href="berandaDokter.php?page=periksa">Periksa</a>  
                    </li>  
                    <li class="nav-item">  
                        <a class="nav-link" href="berandaDokter.php?page=riwayat">Riwayat</a>  
                    </li>  
                    <li class="nav-item">  
                        <a class="nav-link" href="berandaDokter.php?page=aturJadwalDokter">Set Jadwal</a>  
                    </li>  
                    <li class="nav-item">  
                        <a class="nav-link" href="berandaDokter.php?page=updatedokter">Profile</a>  
                    </li>  
                </ul>  
                <ul class="navbar-nav ms-auto">  
                    <li class="nav-item">  
                        <a class="nav-link" href="logout.php">Logout (<?php echo $_SESSION['nama'] ?>)</a>  
                    </li>  
                </ul>  
            </div>  
        </div>  
    </nav>  

    <main role="main" class="container" style="margin-top: 5rem;">  
        <?php  
        // Menampilkan konten halaman sesuai parameter 'page'  
        if (isset($_GET['page'])) {  
            include($_GET['page'] . ".php");  
        } else {  
            echo "<h2>Selamat Datang di Poliklinik, " . $_SESSION['nama'] . "</h2><hr>";  
        }  
        ?>  
    </main>  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>  
</body>  
</html>