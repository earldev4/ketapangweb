<?php
class HalProfile{
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
                    "redirect"=> "profile.php"
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
            "redirect" => "profile.php"
        ];
    }

}
include "includes/db.php";
$desaKetapang = new HalProfile($conn);
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desa Ketapang - Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="style/styles.css">
</head>

<body>
    <?php 
        $currentPage = basename($_SERVER['REQUEST_URI']);
    ?>
    <!-- Navigasi Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light py-0">
        <div class="container-fluid position-fixed fixed-top bg-primary">
            <a class="navbar-brand d-flex text-white text-decoration-none" href="index.php">
                <img src="assets/iconketapang.png" alt="" class="img-fluid">
                <div class="d-sm-block d-none mx-2">
                    <h5 class="fw-bold">DESA KETAPANG</h5>
                    <h6 class="fw-bold">KABUPATEN TANGGAMUS</h6>
                </div>
                <div class="d-sm-none d-block mx-2 ">
                    <p class="fs-6 fw-bold">DESA KETAPANG<br>KABUPATEN TANGGAMUS</p>
                </div>
            </a>
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse d-lg-flex justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item py-3 text-center">
                        <a href="profile.php" class="nav-link text-white  mx-3 fs-5 fw-bold <?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>">PROFILE</a>
                    </li>
                    <li class="nav-item py-3 text-center">
                        <a href="berita.php" class="nav-link text-white  mx-3 fs-5 fw-bold ">BERITA</a>
                    </li>
                    <li class="nav-item py-3 text-center">
                        <a href="umkm.php" class="nav-link text-white  mx-3 fs-5 fw-bold ">UMKM</a>
                    </li>
                    <?php if (isset($_SESSION["is_login"]) == true) { ?>
                        <li class="nav-item py-3 text-center">
                            <a href='pengaduan.php' class='nav-link text-white  mx-3 fs-5 fw-bold '>PENGADUAN</a>
                        </li>
                        <li class="nav-item py-3 text-center">
                            <a href='#' class='nav-link text-white  mx-3 fs-5 fw-bold ' data-bs-toggle='modal'
                                data-bs-target='#adminLogout'>LOGOUT</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item py-3 text-center">
                            <a href='#' class='nav-link text-white  mx-3 fs-5 fw-bold' data-bs-toggle='modal'
                                data-bs-target='#adminLogin'>LOGIN</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Profile Desa Ketapang -->
    <div class="container mt-5 min-vh-100">
        <div class="mt-5 pt-5 px-md-2 px-3">
            <div class="row p-3 shadow rounded mb-4 mt-3">
                <div class="col-12">
                    <h4 class="text-center">SEJARAH DESA KETAPANG</h4>
                    <p class="text-justify">Di suatu masa, sekitar abad ke-16, berdirilah sebuah desa kecil di tepi
                        sebuah sungai yang besar dan
                        tenang. Desa ini diberi nama Ketapang, terinspirasi dari pohon ketapang yang tumbuh subur di
                        sepanjang tepian sungai tersebut. Pohon-pohon itu memberikan naungan, melindungi para penduduk
                        dari
                        terik matahari, dan daunnya sering digunakan untuk ramuan obat-obatan tradisional. Legenda
                        menyebutkan bahwa Desa Ketapang didirikan oleh seorang pelaut bernama Ki Jatiwangsa, yang
                        terdampar
                        di tepian sungai setelah kapalnya dihantam badai besar. Ki Jatiwangsa, seorang pria bijak yang
                        ahli
                        dalam bertani dan memanfaatkan alam, memutuskan untuk tinggal di tempat itu karena keindahan dan
                        kesuburannya. Ia mengajak beberapa penduduk dari desa tetangga untuk bergabung dengannya, dan
                        bersama-sama mereka membuka lahan pertanian serta membangun rumah-rumah sederhana dari bambu dan
                        daun ketapang.</p>
                </div>
            </div>
            <div class="row p-3 shadow rounded mb-4">
                <div class="col-12">
                    <h4 class="text-center">VISI</h4>
                    <p class="text-justify">Mewujudkan Desa Ketapang yang Mandiri, Berbudaya, dan Berkelanjutan melalui
                        Pemberdayaan Masyarakat dan Pelestarian Lingkungan.</p>
                </div>
            </div>
            <div class="row p-3 shadow rounded mb-4">
                <div class="col-12">
                    <h4 class="text-center">MISI</h4>
                    <ol>
                        <li>Meningkatkan Kesejahteraan Masyarakat
                            <ul>
                                <li>Mendorong pengembangan ekonomi kreatif dan sektor UMKM berbasis potensi lokal.</li>
                                <li>Memperluas akses pendidikan, kesehatan, dan pelayanan sosial bagi seluruh warga
                                    desa.</li>
                            </ul>
                        </li>
                        <li>Melestarikan Budaya Lokal
                            <ul>
                                <li>Menghidupkan kembali tradisi dan seni budaya khas Desa Ketapang, seperti kerajinan
                                    anyaman dan upacara adat.</li>
                                <li>Menjadikan budaya lokal sebagai daya tarik wisata untuk meningkatkan perekonomian
                                    desa.</li>
                            </ul>
                        </li>
                        <li>Mengelola Lingkungan Secara Berkelanjutan
                            <ul>
                                <li>Melindungi hutan dan mata air desa sebagai warisan alam untuk generasi mendatang.
                                </li>
                                <li>Menggalakkan program penghijauan dengan menanam pohon ketapang dan tanaman produktif
                                    lainnya.</li>
                            </ul>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="row p-3 shadow rounded mb-4">
                <div class="col-12">
                    <h4 class="text-center">STRUKTUR PERANGKAT DESA</h4>
                    <img src="assets/perangkat.png" alt="" class="img-fluid mx-auto d-flex p-md-5 p-0">
                </div>
            </div>
        </div>
    </div>
    <div class="container mb-3">
        <div class="row mb-4 d-flex justify-content-around">
            <div class="col-md-3 mx-2 shadow rounded col-12  p-2">
                <h5 class="text-center">STRUKTUR PERANGKAT DESA</h5>
                <iframe
                    src="https://www.openstreetmap.org/export/embed.html?bbox=-0.004017949104309083%2C51.47612752641776%2C0.00030577182769775396%2C51.478569861898606&layer=mapnik"
                    frameborder="0" title="Lokasi Desa Ketapang" id="inlineFrameExample"
                    title="Inline Frame Example"></iframe>
            </div>
            <div class="col-md-3 mx-2 shadow rounded col-12 p-2">
                <h5 class="text-center">POPULASI DESA</h5>
                <ul>
                    <li>Total Penduduk: ± 3.500 jiwa</li>
                    <li>Jumlah Kepala Keluarga: ± 900 KK</li>
                    <li>Lansia (65 tahun ke atas): ± 15%</li>
                    <li>Usia Produktif (15-64 tahun): ± 60%</li>
                    <li>Anak-anak (0-14 tahun): ± 25%</li>
                </ul>
            </div>
            <div class="col-md-3 mx-2 shadow rounded col-12 p-2">
                <h5 class="text-center">LUAS AREA DESA</h5>
                <ul>
                    <li>Total Luas: ± 15 km²</li>
                    <li>Pemukiman: ± 30%</li>
                    <li>Pertanian dan Perkebunan: ± 50%</li>
                    <li>Hutan dan Lahan Konservasi: ± 15%</li>
                    <li>Fasilitas Umum dan Infrastruktur: ± 5%</li>
                </ul>
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
                        <form action="profile.php" method="post" id="adminLogin">
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
                        <form action='profile.php' method='post' id="adminout">
                            <input type="hidden" name="logoutinput" value="logout">
                            <button type='submit' class='btn btn-primary' data-bs-toggle='modal'
                                data-bs-target='#adminLogout' name="logout">LOGOUT</button>
                        </form>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        let footer = document.querySelector(".footer-desc");
        let copyright = document.createElement("p");
        copyright.setAttribute("class", "fs-4 text-justify")
        copyright.innerHTML = `<p class="text-muted text-center fs-6">&copy ${new Date().getFullYear()} All Rights Reserved | Created by <a class="text-decoration-none text-muted" href="https://www.instagram.com/earl.dev/profilecard/?igsh=ZG0ya2h3djZscDl3">@earldev</a></p>`;
        footer.appendChild(copyright);

        $(document).ready(function () {
            // Untuk notifikasi ajax login admin
            $('.loginAdmin').on('submit', '#adminLogin', function (e) {
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
                    success: function (response) {
                        if (response.status == "success") {
                            toastr.success(response.message, "Success !", {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 1500
                            });
                            setTimeout(function () {
                                if (response.redirect != "") {
                                    location.href = response.redirect
                                }
                            }, 1800);
                        } else {
                            toastr.error(response.message, "Error !", {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 1500
                            });
                        }
                    }
                })
            })
            // Untuk notifikasi ajax logout admin
            $('.adminlogout').on('submit', '#adminout', function (e) {
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
                    success: function (response) {
                        if (response.status == "success") {
                            toastr.success(response.message, "Success !", {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 1500
                            });
                            setTimeout(function () {
                                if (response.redirect != "") {
                                    location.href = response.redirect
                                }
                            }, 1800);
                        } else {
                            toastr.error(response.message, "Error !", {
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