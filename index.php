<?php 
    class HalIndex{
        private $conn;
        public function __construct($dbConnection){
            $this->conn = $dbConnection;
            session_start();
        }
        public function handleLogin($data){
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            if ($username && $password){
                $stmt = $this->conn->prepare('SELECT username, password FROM akun WHERE username = ? AND password = ?');
                $stmt->bind_param('ss', $username, $password);
                $stmt->execute();
                $result = $stmt->get_result();

                if($result->num_rows > 0){
                    $dataAdmin = $result->fetch_assoc();
                    $_SESSION["username"] = $dataAdmin["username"];
                    $_SESSION["is_login"] = true;

                    return [
                        "status"=> "success",
                        "message"=> "Login Berhasil",
                        "redirect"=> "index.php"
                    ];
                } else{
                    return [
                        "status"=> "error",
                        "message"=> "Username atau Password Salah",
                        "redirect"=> ""
                    ];
                }
            }
        }
        public function handleLogout() {
            session_unset();
            session_destroy();
    
            return [
                "status" => "success",
                "message" => "Logout Berhasil",
                "redirect" => "index.php"
            ];
        }
        public function kirimPengaduan($data){
            $nama = $data['name'] ?? '';
            $umur = $data['age'] ?? '';
            $kelamin = $data['gender'] ?? '';
            $alamat = $data['address'] ?? '';
            $deskripsi = $data['description'] ?? '';
            $ip_address = $this->getClientIp();
            $browser = $_SERVER['HTTP_USER_AGENT'];

            if($nama && $umur && $kelamin && $alamat && $deskripsi != ''){
                $stmt = $this->conn->prepare('INSERT INTO pengaduan (nama, umur, kelamin, alamat, ip_perangkat, browser, deskripsi) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('sisssss', $nama, $umur, $kelamin, $alamat, $ip_address, $browser, $deskripsi);
                $stmt->execute();

                return [
                    'status'=> 'success',
                    'message'=> 'Pengaduan berhasil dikirim',
                    'redirect'=> 'pengaduan.php'
                ];
            } else{
                    return [
                        "status" => "error",
                        "message" => "Data tidak lengkap",
                    ];
            }
        }
        private function getClientIp() {
            $ipaddress = '';
            if (isset($_SERVER['HTTP_CLIENT_IP']))
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            else if (isset($_SERVER['HTTP_X_FORWARDED']))
                $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
                $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            else if (isset($_SERVER['HTTP_FORWARDED']))
                $ipaddress = $_SERVER['HTTP_FORWARDED'];
            else if (isset($_SERVER['REMOTE_ADDR']))
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            else
                $ipaddress = 'UNKNOWN';
    
            return $ipaddress;
        }
    }
    include "includes/db.php";
    $desaKetapang = new HalIndex($conn);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['name'])) {
            $response = $desaKetapang->kirimPengaduan($_POST);
            echo json_encode($response);
            exit();
        }

        if (isset($_POST['username']) && isset($_POST['password'])) {
            $response = $desaKetapang->handleLogin($_POST);
            echo json_encode($response);
            exit();
        }

        if (isset($_POST['logoutinput'])) {
            $response = $desaKetapang->handleLogout();
            echo json_encode($response);
            exit();
        }
    }
    //-----
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desa Ketapang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <!-- Navigasi Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light py-0">
        <div class="container-fluid position-fixed fixed-top indexnavigation">
            <a class="navbar-brand d-flex text-white text-decoration-none" href="index.php">
               <img src="assets/iconketapang.png" alt="" class="img-fluid"> 
               <div class="d-sm-block d-none mx-2">
                    <h5 class="fw-bold">DESA KETAPANG</h5>
                    <h6 class="fw-bold">KABUPATEN TANGGAMUS</h6>
               </div>
               <div class="d-sm-none d-block mx-2 ">
                    <p class="fs-6 fw-bold" >DESA KETAPANG<br>KABUPATEN TANGGAMUS</p>
               </div>
            </a>
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse d-lg-flex justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item py-3 text-center">
                        <a href="profile.php" class="nav-link text-white  mx-3 fs-5 fw-bold ">PROFILE</a>
                    </li>
                    <li class="nav-item py-3 text-center">
                        <a href="berita.php" class="nav-link text-white  mx-3 fs-5 fw-bold ">BERITA</a>
                    </li>
                    <li class="nav-item py-3 text-center">
                        <a href="umkm.php" class="nav-link text-white  mx-3 fs-5 fw-bold ">UMKM</a>
                    </li>
                    <?php if(isset($_SESSION["is_login"] ) == true){?>
                        <li class="nav-item py-3 text-center">
                            <a href='pengaduan.php' class='nav-link text-white  mx-3 fs-5 fw-bold '>PENGADUAN</a>
                        </li>
                        <li class="nav-item py-3 text-center">
                            <a href='#' class='nav-link text-white  mx-3 fs-5 fw-bold ' data-bs-toggle='modal' data-bs-target='#adminLogout'>LOGOUT</a>
                        </li>
                    <?php } else{?>
                    <li class="nav-item py-3 text-center">
                        <a href='#' class='nav-link text-white  mx-3 fs-5 fw-bold' data-bs-toggle='modal' data-bs-target='#adminLogin'>LOGIN</a>
                    </li>
                    <?php }?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Modal Admin Login -->
    <div class="loginAdmin">
        <div class="modal fade" id="adminLogin" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">LOGIN SEBAGAI ADMIN</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="index.php" method="post" id="adminLogin">
                            <label for="username" class="form-label">Masukkan Username Admin</label>
                            <input type="text" class="form-control" name="username" id="username"><br>
                            <label for="password" class="form-label">Masukkan Password Admin</label><br>
                            <input type="password" class="form-control" name="password" id="password">
                            <button type="submit" class="btn btn-primary mt-2" name="loginAdmin">Login</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Pengaduan -->
    <div class="formPengaduan">
        <div class="modal fade" id="layananPengaduan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">LAYANAN PENGADUAN</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="index.php" method="post" id="kumpulPengaduan" onsubmit="validateForm(event)" name="formPengaduan">
                            <label for="name" class="form-label">Masukkan Nama Anda</label>
                            <input type="text" class="form-control" name="name" id="name" required><br>
                            <p class="text-muted" id="errorName"></p>
                            <label for="age" class="form-label">Masukkan Umur Anda</label><br>
                            <input type="number" class="form-control" name="age" id="age" min="15" required><br>
                            <p class="text-muted" id="errorAge"></p>
                            <label for="gender" class="form-label">Masukkan Jenis Kelamin Anda</label><br>
                            <label for="lakilaki" class="form-label">Laki-Laki</label>
                            <input type="radio" name="gender" id="lakilaki" value="Laki-laki" class="form-check-input" required>
                            <label for="lakilaki" class="form-label">Perempuan</label>
                            <input type="radio" name="gender" id="perempuan" value="Perempuan" class="form-check-input" required><br>
                            <label for="address" class="form-label">Masukkan Alamat Anda</label>
                            <input type="text" class="form-control" name="address" id="address" required><br>
                            <p class="text-muted" id="errorAddress"></p>
                            <label for="description" class="form-label">Masukkan Pengaduan Anda</label>
                            <textarea name="description" id="description" class="form-control" rows="3" cols="50" required></textarea>
                            <p class="text-muted" id="errorDescription"></p>
                            <button type="submit" class="btn btn-primary mt-2" name="kumpulPengaduan">Kumpulkan</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>            
    </div>
    <!-- Modal Admin Logout -->
    <div class="adminlogout">
        <div class="modal fade" id="adminLogout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">ANDA YAKIN INGIN LOGOUT?</h5>
                    </div>
                    <div class="modal-footer">
                        <form action='index.php' method='post' id="adminout">
                            <input type="hidden" name="logoutinput" value="logout">
                            <button type='submit' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#adminLogout' name="logout">LOGOUT</button>
                        </form>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    <!-- Carousel Header -->
    <div id="carouselExampleIndicators" class="carousel slide position-relative" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" aria-label="Slide 4"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="4" aria-label="Slide 5"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item ">
            <img src="assets/desa.jpg" class="d-block w-100 img-fluid" alt="..." style="object-fit: cover; height: 100vh;">
            </div>
            <div class="carousel-item">
            <img src="assets/desa2.jpg" class="d-block w-100 img-fluid" alt="..." style="object-fit: cover; height: 100vh;">
            </div>
            <div class="carousel-item">
            <img src="assets/desa3.jpg" class="d-block w-100 img-fluid" alt="..." style="object-fit: cover; height: 100vh;">
            </div>
            <div class="carousel-item">
            <img src="assets/desa4.jpg" class="d-block w-100 img-fluid" alt="..." style="object-fit: cover; height: 100vh;">
            </div>
            <div class="carousel-item active">
            <img src="assets/desa5.jpg" class="d-block w-100 img-fluid" alt="..." style="object-fit: cover; height: 100vh;">
            </div>
        </div>
        <button class="carousel-control-prev " type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon shadow-lg" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next " type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon shadow-lg" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <!-- Layanan Pengaduan -->
    <div class="button-position" style="z-index:1000;">
        <a class="text-decoration-none btn btn-danger rounded p-2 shadow" href="#" data-bs-toggle="modal" data-bs-target="#layananPengaduan" >
            <h4 class="">Layanan Pengaduan</h4>
            <h5 class="text-center">Tekan disini!</h5>
        </a>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        $(document).ready(function(){
            // Untuk notifikasi ajax login admin
            $('.loginAdmin').on('submit', '#adminLogin', function(e){
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');
                let method = form.attr('method');
                let data = new FormData(form[0]);
                console.log("Coba")
                $.ajax({
                    url: url,
                    type: method,
                    processData: false,
                    contentType: false,
                    data: data,
                    dataType: 'JSON',
                    success: function(response){
                        if(response.status == "success"){
                            toastr.success(response.message, "Success !",{
                                closeButton: true,
                                progressBar: true,
                                timeOut: 1500
                            });
                            setTimeout(function(){
                                if (response.redirect != "") {
                                    location.href = response.redirect
                                }
                            }, 1800);
                        } else{
                            toastr.error(response.message, "Error !",{
                                closeButton: true,
                                progressBar: true,
                                timeOut: 1500
                            });
                        }
                    }
                })
            })
            // Untuk notifikasi ajax logout admin
            $('.adminlogout').on('submit', '#adminout', function(e){
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');
                let method = form.attr('method');
                let data = new FormData(form[0]);
                console.log("Coba")
                $.ajax({
                    url: url,
                    type: method,
                    processData: false,
                    contentType: false,
                    data: data,
                    dataType: 'JSON',
                    success: function(response){
                        if(response.status == "success"){
                            toastr.success(response.message, "Success !",{
                                closeButton: true,
                                progressBar: true,
                                timeOut: 1500
                            });
                            setTimeout(function(){
                                if (response.redirect != "") {
                                    location.href = response.redirect
                                }
                            }, 1800);
                        } else{
                            toastr.error(response.message, "Error !",{
                                closeButton: true,
                                progressBar: true,
                                timeOut: 1500
                            });
                        }
                    }
                })
            })
            // Untuk notifikasi ajax pengaduan
            $('.formPengaduan').on('submit', '#kumpulPengaduan', function(e){
                e.preventDefault();
                if (!validateForm(e)) {
                    return; 
                }
                let form = $(this);
                let url = form.attr('action');
                let method = form.attr('method');
                let data = new FormData(form[0]);
                console.log("COBA LAGI");
                $.ajax({
                    url: url,
                    type: method,
                    processData: false,
                    contentType: false,
                    data: data,
                    dataType: 'JSON',
                    success: function(response){
                        if(response.status == "success"){
                            toastr.success(response.message, "Success !",{
                                closeButton: true,
                                progressBar: true,
                                timeOut: 1500
                            });
                            setTimeout(function(){
                                if (response.redirect != "") {
                                    location.href = response.redirect
                                }
                            }, 1800);
                        } else{
                            toastr.error(response.message, "Error !",{
                                closeButton: true,
                                progressBar: true,
                                timeOut: 1500
                            });
                        }
                    }
                })
            })
        })
        function validateForm(event) {
            let isValid = true;

            const name = document.forms['formPengaduan']['name'].value.trim();
            const address = document.forms['formPengaduan']['address'].value.trim();
            const description = document.forms['formPengaduan']['description'].value.trim();

            const errorName = document.getElementById('errorName');
            const errorAddress = document.getElementById('errorAddress');
            const errorDescription = document.getElementById('errorDescription');

            if (!name || name.length < 7){
                errorName.textContent = 'Nama minimal 7 karakter';
                isValid = false;
            } else{
                errorName.textContent = '';
            }
            if (!address || address.length < 30){
                errorAddress.textContent = 'Alamat minimal 30 karakter';
                isValid = false;
            } else{
                errorAddress.textContent = '';
            }
            if (!description || description.length < 50){
                errorDescription.textContent = 'Pengaduan minimal 50 karakter';
                isValid = false;
            } else{
                errorDescription.textContent = '';
            }

            return isValid;
        }
    </script>
</body>
</html>