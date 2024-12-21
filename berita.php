<?php
    class HalBerita{
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
                        "redirect"=> "berita.php"
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
                "redirect" => "berita.php"
            ];
        }
        public function handleTambahBerita($data){
            $judul = $data["judul"] ?? '';
            $gambar = $_FILES["gambar"];;
            $gambarName = $_FILES['gambar']['name'];
            $gambarTmpName = $_FILES['gambar']['tmp_name'];
            $gambarSize = $_FILES['gambar']['size'];
            $gambarError = $_FILES['gambar']['error'];
            $gambarType = $_FILES['gambar']['type'];
            $penulis = $data["penulis"] ?? '';
            $deskripsi = $data["deskripsi"] ?? '';
            $fileExt = explode('.', $gambarName);
            $fileExtActual = strtolower(end($fileExt));
            $allowed = array('jpg', 'jpeg', 'png');
            if ($judul && $gambar && $penulis && $deskripsi != '') {
                if (in_array($fileExtActual, $allowed)) {
                    if ($gambarSize < 5000000) {
                        $gambarNameNew = uniqid('', true) . "." . $fileExtActual;
                        $gambarFolder = 'assets/image/berita/' . $gambarNameNew;
                        move_uploaded_file($gambarTmpName, $gambarFolder);
                        $stmt = $this->conn->prepare('INSERT INTO berita (judul, gambar, penulis, deskripsi) VALUES (?, ?, ?, ?)');
                        $stmt->bind_param('ssss', $judul, $gambarNameNew, $penulis, $deskripsi);
                        $stmt->execute();
                        return [
                            "status" => "success",
                            "message" => "Berita berhasil ditambahkan",
                            "redirect" => "berita.php"
                        ];
                    } else {
                        return [
                            "status" => "error",
                            "message" => "Gambar harus kurang dari 5MB!",
                            "redirect" => "berita.php"
                        ];
                    }
                } else {
                    return [
                        "status" => "error",
                        "message" => "Gambar harus memiliki format JPEG, JPG, atau PNG!",
                        "redirect" => ""
                    ];
                    }
            } else {
                return [
                    "status" => "error",
                    "message" => "Data tidak lengkap",
                    "redirect" => ""
                ];
            }
        }
        public function handleEditBerita($data){
            $id = $data["id"] ?? '';
            $judul = $data["editjudul"] ?? '';
            $gambar = $_FILES["editgambar"] ?? '';
            $gambarName = $_FILES['editgambar']['name'] ??  '';
            $gambarTmpName = $_FILES['editgambar']['tmp_name'] ?? '';
            $gambarSize = $_FILES['editgambar']['size'] ??  '';
            $gambarType = $_FILES['editgambar']['type'] ??  '';
            $penulis = $data["editpenulis"] ?? '';
            $deskripsi = $data["editdeskripsi"] ?? '';
            $fileExt = explode('.', $gambarName);
            $fileExtActual = strtolower(end($fileExt));
            $allowed = array('jpg', 'jpeg', 'png');
            if ($judul && $penulis && $deskripsi != '') {
                if (!empty($gambarName)) {
                    if (in_array($fileExtActual, $allowed)) {
                        if ($gambarSize < 5000000) {
                            $queryunlink = $this->conn->prepare('SELECT * FROM berita WHERE id_berita = ?');
                            $queryunlink->bind_param('i', $id);
                            $queryunlink->execute();
                            $result = $queryunlink->get_result()->fetch_assoc();
                            $fileName = $result["gambar"];
                            unlink('assets/image/berita/' . $fileName);
    
                            $gambarNameNew = uniqid('', true) . "." . $fileExtActual;
                            $gambarFolder = 'assets/image/berita/' . $gambarNameNew;
                            move_uploaded_file($gambarTmpName, $gambarFolder);
                            $stmt = $this->conn->prepare('UPDATE berita SET judul = ?, gambar = ?, penulis = ?, deskripsi = ? WHERE id_berita = ?');
                            $stmt->bind_param('ssssi', $judul, $gambarNameNew, $penulis, $deskripsi, $id);
                            $stmt->execute();
                            return [
                                "status" => "success",
                                "message" => "Berita berhasil diedit",
                                "redirect" => "berita.php"
                            ];
                        } else {
                            return [
                                "status" => "error",
                                "message" => "Gambar harus kurang dari 5MB!",
                                "redirect" => "berita.php"
                            ];
                        }
                    } else {
                        return [
                            "status" => "error",
                            "message" => "Gambar harus memiliki format JPEG, JPG, atau PNG!",
                            "redirect" => ""
                        ];
                    }
                } else{
                    $stmtnt = $this->conn->prepare('UPDATE berita SET judul = ?, penulis = ?, deskripsi = ? WHERE id_berita = ?');
                    $stmtnt->bind_param('sssi', $judul,  $penulis, $deskripsi, $id);
                    $stmtnt->execute();
                    return [
                        "status" => "success",
                        "message" => "Berita berhasil diedit",
                        "redirect" => "berita.php"
                    ];
                }
            } else {
                return [
                    "status" => "error",
                    "message" => "Data tidak lengkap",
                    "redirect" => ""
                ];
            }
        }
        public function handleDeleteBerita($data){
            $id_delete = $data["id_delete"] ?? '';

            $queryunlink = $this->conn->prepare('SELECT * FROM berita WHERE id_berita = ?');
            $queryunlink->bind_param('i', $id_delete);
            $queryunlink->execute();
            $result = $queryunlink->get_result()->fetch_assoc();
            $fileName = $result["gambar"];
            unlink('assets/image/berita/' . $fileName);

            $stmt = $this->conn->prepare('DELETE FROM berita WHERE id_berita = ?');
            $stmt->bind_param('i', $id_delete);
            $stmt->execute();
            return [
                "status" => "success",
                "message" => "Berita berhasil dihapus",
                "redirect" => "berita.php"
            ];
        }
        public function handleTampilkanBerita($page = 1, $news_per_page = 6) {
            $start_from = ($page - 1) * $news_per_page;
            $stmt = $this->conn->prepare("SELECT id_berita, judul, gambar, penulis, deskripsi FROM berita ORDER BY created_at DESC LIMIT ?, ?");
            $stmt->bind_param("ii", $start_from, $news_per_page);
            $stmt->execute();
            $result = $stmt->get_result();
            $total_news_result = $this->conn->query("SELECT COUNT(*) AS total FROM berita");
            $total_news = $total_news_result->fetch_assoc()['total'];
            $total_pages = ceil($total_news / $news_per_page);
        
            $berita = [];
              while ($row = $result->fetch_assoc()) {
                $berita[] = $row;
              }
            return [
              "berita" => $berita,
              "total_pages" => $total_pages,
            ];
          }
    }
    include "includes/db.php";
    $desaKetapang = new HalBerita($conn);
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
        if (isset($_POST['judul'])) {
            $response = $desaKetapang->handleTambahBerita($_POST);
            echo json_encode($response);
            exit();
        }
        if (isset($_POST['editjudul'])) {
            $response = $desaKetapang->handleEditBerita($_POST);
            echo json_encode($response);
            exit();
        }
        if (isset($_POST['id_delete'])) {
            $response = $desaKetapang->handleDeleteBerita($_POST);
            echo json_encode($response);
            exit();
        }
    }
    $news_per_page = 6;
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }
    $dataBerita = $desaKetapang->handleTampilkanBerita($page, $news_per_page);
    $berita = $dataBerita['berita'];
    $total_pages = $dataBerita['total_pages'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desa Ketapang - Berita</title>
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
                        <a href="berita.php" class="nav-link text-white mx-3 fs-5 fw-bold <?php echo ($currentPage == 'berita.php') ? 'active' : ''; ?>">BERITA</a>
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
        <div class="container-fluid py-1">
            <!-- Menambahkan Berita Untuk Admin -->
            <div class="row mt-4">
                <?php if (isset($_SESSION["is_login"])) { ?>
                    <div class="col-12 d-flex justify-content-end mt-5">
                        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                            data-bs-target="#tambahBerita"><span class="px-2">+</span>Tambah Berita</button>
                    </div>
                <?php } ?>
            </div>
            <!-- Daftar Berita -->
             <?php if (isset($_SESSION['is_login'])) { ?>
            <div class="row mt-2 min-vh-100 p-md-2 p-1">
            <?php } else { ?>
            <div class="row mt-5 min-vh-100 p-md-2 p-1">
            <?php } ?>
                <h1>DAFTAR BERITA</h1>
                <?php
                if (empty($berita)) {
                    echo "
                            <div class='col-12  d-flex align-items-center justify-content-center'>
                                <h1>Belum ada berita 😢😢😢</h1>
                            </div>
                        ";
                } else {
                    $no = 1;
                    foreach ($berita as $row) { ?>
                        <div class='col-md-4 col-12 col-sm-6 my-2'>
                            <div class='card shadow' style="min-height: 300px;">
                                <img src="assets/image/berita/<?= $row['gambar'] ?>" alt="" class='card-img-top img-fluid'
                                    style="height: 200px; object-fit: containt;">
                                <div class='card-body'>
                                    <h5 class='card-title text-truncate' style="max-width: 100%;"><?= $row['judul'] ?></h5>
                                    <p class='card-text' style="height: 60px; overflow: hidden; text-overflow: ellipsis;"><?= substr($row['deskripsi'], 0, 50) ?>.....<i>baca selengkapnya</i>
                                    </p>
                                    <p class='card-text fw-bold' style="height: 60px; overflow: hidden; text-overflow: ellipsis;">📝<?= $row['penulis'] ?>
                                    </p>
                                    <a href='detailberita.php?id_berita=<?= $row['id_berita'] ?>' class='btn btn-primary w-100'>Lihat Selengkapnya</a>
                                </div>
                                <?php if (isset($_SESSION["is_login"])) { ?>
                                    <div class='d-flex p-2 justify-content-end'>
                                        <button class='btn btn-primary mx-1 w-50' data-bs-toggle='modal' data-bs-target='#editBerita'
                                            data-id='<?= $row['id_berita'] ?>' data-nama="<?= $row['judul'] ?>"
                                            data-deskripsi="<?= $row['deskripsi'] ?>"
                                            data-penulis="<?= $row['penulis'] ?>" id="buttonEditBerita">Edit</button>
                                        <button class='btn btn-danger w-50' data-bs-toggle='modal' data-bs-target='#deleteBerita'
                                            data-id="<?= $row['id_berita'] ?>">Hapus</button>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php $no++;
                    }
                }
                ?>
            </div>
            <div class="text-center">
                <?php
                if ($page > 1) {
                    echo "<a href='berita.php?page=" . ($page - 1) . "' class='btn btn-primary rounded-circle'>←</a> ";
                }
                if ($page < $total_pages) {
                    echo "<a href='berita.php?page=" . ($page + 1) . "' class='btn btn-primary rounded-circle'>→</a>";
                }
                echo "<p class=''>Halaman - $page/$total_pages</p>";
                ?>
            </div>
        </div>
    </main>
    <!-- Footer -->
    <footer>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center footer-desc">
                </div>
            </div>
        </div>
    </footer>
    <!-- Modal untuk tambah berita -->
    <div class="tambahBerita">
        <div class="modal fade" id="tambahBerita" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">TAMBAH BERITA</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="berita.php" method="post" id="tambahkanBerita" name="formTambahBerita" onsubmit="validateForm(event)">
                            <label for="judul" class="form-label">Masukan judul</label>
                            <input type="text" class="form-control" name="judul" id="judul"><br>
                            <p class="text-muted" id="errorTitle"></p>
                            <label for="gambar" class="form-label">Pilih gambar</label>
                            <input type="file" class="form-control" name="gambar" id="gambar">
                            <p>Gambar harus berbentuk landscape</p>
                            <label for="penulis" class="form-label">Masukan nama penulis berita</label>
                            <input type="text" class="form-control" name="penulis" id="penulis"><br>
                            <p class="text-muted" id="errorAuthor"></p>
                            <label for="deskripsi" class="form-label">Masukan deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"
                                cols="50"></textarea><br>
                            <p class="text-muted" id="errorDescription"></p>
                            <button type="submit" class="btn btn-primary mt-2">Tambahkan</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal untuk edit berita -->
    <div class="editBerita">
        <div class="modal fade" id="editBerita" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">EDIT BERITA</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="berita.php" method="post" id="editkanBerita" enctype="multipart/form-data" name="formEditBerita" onsubmit="editValidateForm(event)"> 
                            <input type="hidden" class="form-control" name="id" id="editId">
                            <label for="judul" class="form-label">Masukan judul</label>
                            <input type="text" class="form-control" name="editjudul" id="editJudul"><br>
                            <p class="text-muted" id="editErrorTitle"></p>
                            <label for="gambar" class="form-label">Pilih gambar</label>
                            <input type="file" class="form-control" name="editgambar" id="editGambar">
                            <p>Gambar harus berbentuk landscape</p>
                            <label for="penulis" class="form-label">Masukan nama penulis berita</label>
                            <input type="text" class="form-control" name="editpenulis" id="editPenulis"><br>
                            <p class="text-muted" id="editErrorAuthor"></p>
                            <label for="deskripsi" class="form-label">Masukan deskripsi</label>
                            <textarea name="editdeskripsi" id="editDeskripsi" class="form-control" rows="3"
                                cols="50"></textarea><br>
                            <p class="text-muted" id="editErrorDescription"></p>
                            <button type="submit" class="btn btn-primary mt-2">Tambahkan</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal untuk hapus berita -->
    <div class="hapusBerita">
        <div class="modal fade" id="deleteBerita" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">HAPUS BERITA</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah anda yakin ingin menghapus berita ini?</p>
                        <form action="berita.php" method="post" id="hapusBerita">
                            <input type="hidden" class="form-control" name="id_delete" id="deleteId">
                            <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Hapus</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
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
                        <form action="berita.php" method="post" id="adminLogin">
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
                        <form action='berita.php' method='post' id="adminout">
                            <input type="hidden" name="logoutinput" value="logout">
                            <button type='submit' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#adminLogout' name="logout">LOGOUT</button>
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
            });
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
            // Untuk notifikasi ajax hapus berita
            $('.hapusBerita').on('submit', '#hapusBerita', function (e) {
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
            });
            // Untuk notifikasi ajax tambah berita
            $('.tambahBerita').on('submit', '#tambahkanBerita', function (e) {
                e.preventDefault();
                if (!validateForm(e)) {
                    return; 
                }
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
            });
            // Untuk notifikasi ajax edit berita
            $('.editBerita').on('submit', '#editkanBerita', function (e) {
                e.preventDefault();
                if (!editValidateForm(e)) {
                    return; 
                }
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
            });
            // Untuk mengisi value sementara pada form edit berita
            const editBerita = document.getElementById('editBerita');
            editBerita.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; 
            const nama = button.getAttribute('data-nama'); 
            const id = button.getAttribute('data-id');
            const deskripsi = button.getAttribute('data-deskripsi');
            const penulis = button.getAttribute('data-penulis');
            const formId = document.getElementById('editId');
            const formJudul = document.getElementById('editJudul');
            const formGambar = document.getElementById('editGambar');
            const formPenulis = document.getElementById('editPenulis');
            const formDeskripsi = document.getElementById('editDeskripsi');

            console.log(deskripsi);
            formId.value = id;
            formJudul.value = nama;
            formPenulis.value = penulis;
            formDeskripsi.innerHTML =  deskripsi;
            });
            // Untuk menghapus berita
            const hapusBerita = document.getElementById('deleteBerita');
            deleteBerita.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; 
            const id = button.getAttribute('data-id');
            const formId = document.getElementById('deleteId');
            console.log(id);
            formId.value = id;
            });
        })
        function validateForm(event) {
            let isValid = true;

            const judul = document.forms['formTambahBerita']['judul'].value.trim();
            const penulis = document.forms['formTambahBerita']['penulis'].value.trim();
            const deskripsi = document.forms['formTambahBerita']['deskripsi'].value.trim();

            const errorJudul = document.getElementById('errorTitle');
            const errorPenulis = document.getElementById('errorAuthor');
            const errorDeskripsi = document.getElementById('errorDescription');

            if (!judul || judul.length < 20){
                errorJudul.textContent = 'Judul minimal 20 karakter';
                isValid = false;
            } else{
                errorJudul.textContent = '';
            }
            if (!penulis || penulis.length < 7){
                errorPenulis.textContent = 'Penulis minimal 7 karakter';
                isValid = false;
            } else{
                errorPenulis.textContent = '';
            }
            if (!deskripsi || deskripsi.length < 50){
                errorDeskripsi.textContent = 'Deskripsi minimal 50 karakter';
                isValid = false;
            } else{
                errorDeskripsi.textContent = '';
            }

            return isValid;
        }
        function editValidateForm(event) {
            let isValid = true;

            const judul = document.forms['formEditBerita']['editjudul'].value.trim();
            const penulis = document.forms['formEditBerita']['editpenulis'].value.trim();
            const deskripsi = document.forms['formEditBerita']['editdeskripsi'].value.trim();

            const errorJudul = document.getElementById('editErrorTitle');
            const errorPenulis = document.getElementById('editErrorAuthor');
            const errorDeskripsi = document.getElementById('editErrorDescription');

            if (!judul || judul.length < 20){
                errorJudul.textContent = 'Judul minimal 20 karakter';
                isValid = false;
            } else{
                errorJudul.textContent = '';
            }
            if (!penulis || penulis.length < 7){
                errorPenulis.textContent = 'Penulis minimal 7 karakter';
                isValid = false;
            } else{
                errorPenulis.textContent = '';
            }
            if (!deskripsi || deskripsi.length < 50){
                errorDeskripsi.textContent = 'Deskripsi minimal 50 karakter';
                isValid = false;
            } else{
                errorDeskripsi.textContent = '';
            }

            return isValid;
        }
    </script>
</body>

</html>