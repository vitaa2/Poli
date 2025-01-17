<?php  
// session_start();  
include_once("koneksi.php"); // Pastikan ini mengarah ke file koneksi database  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    $nama = $_POST['nama'];  
    $password = $_POST['password'];  

    // Mencari dokter berdasarkan nama  
    $query = "SELECT * FROM dokter WHERE nama = '$nama'";  
    $result = $mysqli->query($query);  

    if (!$result) {  
        die("Query error: " . $mysqli->error);  
    }  

    if ($result->num_rows == 1) {  
        $row = $result->fetch_assoc();  
        if (password_verify($password, $row['password'])) {  
            // Menyimpan informasi sesi  
            $_SESSION['id'] = $row['id'];  
            $_SESSION['nama'] = $row['nama'];  
            header("Location: berandaDokter.php"); // Redirect ke beranda dokter  
            exit();  
        } else {  
            $error = "Password salah.";  
        }  
    } else {  
        $error = "User tidak ditemukan.";  
    }  
}  
?>  

<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="utf-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">  
    <title>Login Dokter</title>  
</head>  
<body style="background-color: #DEE6F6;">  
    <div class="container" style="margin-top: 10rem;">  
        <div class="row justify-content-center">  
            <div class="col-md-8">  
                <div class="card">  
                    <div class="card-body">  
                        <div class="row d-flex justify-content-center align-items-center px-5 py-4">  
                            <div class="col lg-6">  
                                <img src="images/logindokter.jpg" class="img-fluid" alt="login-pic">  
                            </div>  
                            <div class="col-lg-6">  
                                <h1 class="text-center">Login Dokter</h1>  
                                <form method="POST" action="">  
                                    <?php  
                                    if (isset($error)) {  
                                        echo '<div class="alert alert-danger">' . $error . '</div>';  
                                    }  
                                    ?>  
                                    <div class="form-group">  
                                        <label for="nama">Username</label>  
                                        <input type="text" name="nama" class="form-control" required placeholder="Masukkan Username anda">  
                                    </div>  
                                    <div class="form-group mt-2">  
                                        <label for="password">Password</label>  
                                        <input type="password" name="password" class="form-control" required placeholder="Masukkan password anda">  
                                    </div>  
                                    <div class="text-center mt-3">  
                                        <button type="submit" class="btn btn-outline-primary px-4 btn-block">Login</button>  
                                    </div>  
                                </form>  
                                <div class="text-center">  
                                    <p class="mt-3">Login sebagai admin? <a href="index.php?page=loginUser">Ya, Saya Admin</a></p>  
                                </div>  
                            </div>  
                        </div>  
                    </div>  
                </div>  
            </div>  
        </div>  
    </div>  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>  
</body>  
</html>