<?php 
    class HalDetailPengaduan{
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
                        "redirect"=> "umkm.php"
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
                "redirect" => "umkm.php"
            ];
        }
        public function handleTampilkanDetailPengaduan($id_pengaduan) {
            $stmt = $this->conn->prepare('SELECT * FROM pengaduan WHERE id_pengaduan = ?');
            $stmt->bind_param('i', $id_pengaduan);
            $stmt->execute();
            $result = $stmt->get_result();
            $pengaduan = [];
              while ($row = $result->fetch_assoc()) {
                $pengaduan[] = $row;
              }
            return $pengaduan;
          }
    }
    include "includes/db.php";
    $desaKetapang = new HalDetailPengaduan($conn);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $id_pengaduan = isset($_GET['id_pengaduan']) ? intval($_GET['id_pengaduan']) :'';
    if (empty($id_pengaduan)) {
        die('ID Pengaduan tidak ditemukan.');
    }
    $pengaduan = $desaKetapang->handleTampilkanDetailPengaduan($id_pengaduan);
    if (empty($pengaduan)) {
        die('Pengaduan tidak ditemukan.');
    }
    $pengaduan = $pengaduan[0];
    // Mengecek session login/tidak
    if($_SESSION['is_login'] == false){
        header("location: index.php");
        exit();
    }
    $no = $_GET['no'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desa Ketapang - Detail Pengaduan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <!-- Navigasi Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light py-0">
        <div class="container-fluid position-fixed fixed-top bg-primary">
            <a class="navbar-brand d-flex text-white text-decoration-none" href="index.php">
               <img src="assets/iconketapang.png" alt="" class="img-fluid" > 
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
    <main>
        <div class="container-fluid py-5 min-vh-100">
            <div class="row mt-2">
                <div class="col-12 py-5 px-3">
                    <h1>Detail Pengaduan</h1>
                    <a class="btn btn-primary my-2" href="pengaduan.php">Kembali</a>
                    <table class="table table-striped mt-2">
                        <tr>
                            <th class="table-secondary">Nomor Pengaduan:</th>
                            <td><p><?php echo $no; ?></p></td>
                        </tr>
                        <tr>
                            <th class="table-secondary">Waktu Pengaduan:</th>
                            <td>
                                <?php 
                                    $createdAt = $pengaduan['created_at'];
                                    $timestamp = strtotime($createdAt);
                                    $bulan = [
                                        1=> "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                                    ];
                                    $formattedDate = date("d", $timestamp) . " " . $bulan[date("n", $timestamp)] . " " . date("Y", $timestamp);
                                    echo $formattedDate;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-secondary">Informasi Pengaduan</th>
                            <td>
                                <ul class="list-group list-unstyled" >
                                    <li>
                                        Nama Pengadu            : <?php echo $pengaduan['nama']; ?>
                                    </li>
                                    <li>
                                        Umur Pengadu            : <?php echo $pengaduan['umur']; ?>
                                    </li>
                                    <li>
                                        Jenis Kelamin Pengadu   : <?php echo $pengaduan['kelamin']; ?>
                                    </li>
                                    <li>
                                        Alamat Pengadu          : <?php echo $pengaduan['alamat']; ?>
                                    </li>
                                    <li>
                                        IP Perangkat            : <?php echo $pengaduan['ip_perangkat']; ?>
                                    </li>
                                    <li>
                                        Browser                 : <?php echo $pengaduan['browser']; ?>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-secondary">Deskripsi Pengaduan</th>
                            <td><p><?php echo $pengaduan['deskripsi']; ?></p></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
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
        <!-- Modal Admin Logout -->
        <div class="adminlogout">
            <div class="modal fade" id="adminLogout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">ANDA YAKIN INGIN LOGOUT?</h5>
                        </div>
                        <div class="modal-footer">
                            <form action='detailpengaduan.php' method='post' id="adminout">
                                <input type="hidden" name="logoutinput" value="logout">
                                <button type='submit' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#adminLogout' name="logout">LOGOUT</button>
                            </form>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <footer>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 text-center footer-desc">
                    </div>
                </div>
            </div>
        </footer>
    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        let footer = document.querySelector(".footer-desc");
        let copyright = document.createElement("p");
        copyright.setAttribute("class","fs-4 text-justify")
        copyright.innerHTML = `<p class="text-muted text-center fs-6">&copy ${new Date().getFullYear()} All Rights Reserved | Created by <a class="text-decoration-none text-muted" href="https://www.instagram.com/earl.dev/profilecard/?igsh=ZG0ya2h3djZscDl3">@earldev</a></p>`;
        footer.appendChild(copyright);
    // Untuk notifikasi ajax login admin
        $(document).ready(function(){
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
        })
    </script>
</body>
</html>