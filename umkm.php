<?php
class HalUMKM{
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
    public function handleTambahUMKM($data){
        $nama = $data["nama"] ?? '';
        $gambar = $_FILES["gambar"];;
        $gambarName = $_FILES['gambar']['name'];
        $gambarTmpName = $_FILES['gambar']['tmp_name'];
        $gambarSize = $_FILES['gambar']['size'];
        $gambarError = $_FILES['gambar']['error'];
        $gambarType = $_FILES['gambar']['type'];
        $alamat = $data["alamat"] ?? '';
        $kategori = $data["kategori"] ?? '';
        $deskripsi = $data["deskripsi"] ?? '';
        $fileExt = explode('.', $gambarName);
        $fileExtActual = strtolower(end($fileExt));
        $allowed = array('jpg', 'jpeg', 'png');
        if ($nama && $gambar && $alamat && $kategori && $deskripsi != '') {
            if (in_array($fileExtActual, $allowed)) {
                if ($gambarSize < 5000000) {
                    $gambarNameNew = uniqid('', true) . "." . $fileExtActual;
                    $gambarFolder = 'assets/image/umkm/' . $gambarNameNew;
                    move_uploaded_file($gambarTmpName, $gambarFolder);
                    $stmt = $this->conn->prepare('INSERT INTO umkm (nama, gambar, alamat, kategori, deskripsi) VALUES (?, ?, ?, ?, ?)');
                    $stmt->bind_param('sssss', $nama, $gambarNameNew, $alamat, $kategori, $deskripsi);
                    $stmt->execute();
                    return [
                        "status" => "success",
                        "message" => "UMKM berhasil ditambahkan",
                        "redirect" => "umkm.php"
                    ];
                } else {
                    return [
                        "status" => "error",
                        "message" => "Gambar harus kurang dari 5MB!",
                        "redirect" => ""
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
    public function handleEditUMKM($data){
        $id = $data["id"] ?? '';
        $nama = $data["editnama"] ?? '';
        $gambar = $_FILES["editgambar"] ?? '';
        $gambarName = $_FILES['editgambar']['name'] ??  '';
        $gambarTmpName = $_FILES['editgambar']['tmp_name'] ?? '';
        $gambarSize = $_FILES['editgambar']['size'] ??  '';
        $gambarType = $_FILES['editgambar']['type'] ??  '';
        $alamat = $data["editalamat"] ?? '';
        $kategori = $data["editkategori"] ?? '';
        $deskripsi = $data["editdeskripsi"] ?? '';
        $fileExt = explode('.', $gambarName);
        $fileExtActual = strtolower(end($fileExt));
        $allowed = array('jpg', 'jpeg', 'png');
        if ($nama && $alamat && $kategori && $deskripsi != '') {
            if (!empty($gambarName)) {
                if (in_array($fileExtActual, $allowed)) {
                    if ($gambarSize < 5000000) {
                        $queryunlink = $this->conn->prepare('SELECT * FROM umkm WHERE id_umkm = ?');
                        $queryunlink->bind_param('i', $id);
                        $queryunlink->execute();
                        $result = $queryunlink->get_result()->fetch_assoc();
                        $fileName = $result["gambar"];
                        unlink('assets/image/umkm/' . $fileName);

                        $gambarNameNew = uniqid('', true) . "." . $fileExtActual;
                        $gambarFolder = 'assets/image/umkm/' . $gambarNameNew;
                        move_uploaded_file($gambarTmpName, $gambarFolder);
                        $stmt = $this->conn->prepare('UPDATE umkm SET nama = ?, gambar = ?, alamat = ?, kategori = ?, deskripsi = ? WHERE id_umkm = ?');
                        $stmt->bind_param('sssssi', $nama, $gambarNameNew, $alamat, $kategori, $deskripsi, $id);
                        $stmt->execute();
                        return [
                            "status" => "success",
                            "message" => "UMKM berhasil diedit",
                            "redirect" => "umkm.php"
                        ];
                    } else {
                        return [
                            "status" => "error",
                            "message" => "Gambar harus kurang dari 5MB!",
                            "redirect" => ""
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
                $stmtnt = $this->conn->prepare('UPDATE umkm SET nama = ?, alamat = ?, kategori = ?, deskripsi = ? WHERE id_umkm = ?');
                $stmtnt->bind_param('ssssi', $nama, $alamat, $kategori, $deskripsi, $id);
                $stmtnt->execute();
                return [
                    "status" => "success",
                    "message" => "UMKM berhasil diedit",
                    "redirect" => "umkm.php"
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
    public function handleDeleteUMKM($data){
        $id_delete = $data["id_delete"] ?? '';

        $queryunlink = $this->conn->prepare('SELECT * FROM umkm WHERE id_umkm = ?');
        $queryunlink->bind_param('i', $id_delete);
        $queryunlink->execute();
        $result = $queryunlink->get_result()->fetch_assoc();
        $fileName = $result["gambar"];
        unlink('assets/image/umkm/' . $fileName);

        $stmt = $this->conn->prepare('DELETE FROM umkm WHERE id_umkm = ?');
        $stmt->bind_param('i', $id_delete);
        $stmt->execute();
        return [
            "status" => "success",
            "message" => "UMKM berhasil dihapus",
            "redirect" => "umkm.php"
        ];
    }
    public function handleTampilkanUMKM($kategori = "kuliner") {
        $stmt = $this->conn->prepare('SELECT * FROM umkm WHERE kategori = ?');
        $stmt->bind_param('s', $kategori);
        $stmt->execute();
        $result = $stmt->get_result();
        $umkm = [];
          while ($row = $result->fetch_assoc()) {
            $umkm[] = $row;
          }
        return $umkm;
    }
}

include "includes/db.php";
$desaKetapang = new HalUMKM($conn);
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
    if (isset($_POST['nama'])) {
        $response = $desaKetapang->handleTambahUMKM($_POST);
        echo json_encode($response);
        exit();
    }
    if (isset($_POST['editnama'])) {
        $response = $desaKetapang->handleEditUMKM($_POST);
        echo json_encode($response);
        exit();
    }
    if (isset($_POST['id_delete'])) {
        $response = $desaKetapang->handleDeleteUMKM($_POST);
        echo json_encode($response);
        exit();
    }
}

if (isset($_GET['kategori'])) {
    $kategori = $_GET['kategori'];
} else{
    $kategori = 'kuliner';
}
$UMKM = $desaKetapang->handleTampilkanUMKM($kategori);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desa Ketapang - UMKM</title>
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
                        <a href="profile.php" class="nav-link text-white  mx-3 fs-5 fw-bold ">PROFILE</a>
                    </li>
                    <li class="nav-item py-3 text-center">
                        <a href="berita.php" class="nav-link text-white  mx-3 fs-5 fw-bold ">BERITA</a>
                    </li>
                    <li class="nav-item py-3 text-center">
                        <a href="umkm.php" class="nav-link text-white  mx-3 fs-5 fw-bold <?php echo ($currentPage == 'umkm.php') ? 'active' : ''; ?>">UMKM</a>
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
    <!-- UMKM -->
    <main>
        <div class="container-fluid py-5 min-vh-100">
            <div class="row mt-4">
                <?php if (isset($_SESSION["is_login"])) { ?>
                    <div class="col-12 d-flex justify-content-end mt-2">
                        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                            data-bs-target="#tambahUMKM"><span class="px-2">+</span>Tambah UMKM</button>
                    </div>
                <?php } ?>
            </div>
            <?php if (isset($_SESSION["is_login"])) { ?>
            <div class="row mt-1">
            <?php } else { ?>
            <div class="row mt-3">
            <?php } ?>
                    <h1>UMKM</h1>
                    <div>
                        <a href="umkm.php?kategori=Produk " class="btn btn-primary fw-bold"><span class="fs-5">📦</span>Produk</a>
                        <a href="umkm.php?kategori=Pariwisata " class="btn btn-primary fw-bold"><span class="fs-5">⛱️</span>Pariwisata</a>
                        <a href="umkm.php" class="btn btn-primary fw-bold"><span class="fs-5">🍛</span>Kuliner</a>
                    </div>
                    <?php
                    if (empty($UMKM)){
                        echo "
                            <div class='col-12  d-flex align-items-center justify-content-center'>
                                <h1>Belum ada UMKM 😢😢😢</h1>
                            </div>
                        ";
                    } else{
                        $no = 1;
                        foreach ($UMKM as $row){ ?>
                        <div class='col-md-4 col-12 col-sm-6 my-2 p-sm-2 p-3'>
                            <div class='card shadow ' style="min-height: 300px;">
                                <img src="assets/image/umkm/<?= $row['gambar'] ?>" alt="" class='card-img-top img-fluid'
                                    style="height: 200px; object-fit: containt;">
                                <div class='card-body'>
                                    <h5 class='card-title text-truncate' style="max-width: 100%;"><?= $row['nama'] ?></h5>
                                    <p class='card-text' style="height: 60px; overflow: hidden; text-overflow: ellipsis;"><?= substr($row['deskripsi'], 0, 50) ?>.....<i>baca selengkapnya</i>
                                    </p>
                                    <p class='card-text fw-bold' style="height: 60px; overflow: hidden; text-overflow: ellipsis;">📝<?= $row['kategori'] ?>
                                    </p>
                                    <a href='detailumkm.php?id_umkm=<?= $row['id_umkm'] ?>' class='btn btn-primary w-100'>Lihat Selengkapnya</a>
                                </div>
                                <?php if (isset($_SESSION["is_login"])) { ?>
                                    <div class='d-flex p-2 justify-content-end'>
                                        <button class='btn btn-primary mx-1 w-50' data-bs-toggle='modal' data-bs-target='#editUMKM'
                                            data-id='<?= $row['id_umkm'] ?>' data-nama="<?= $row['nama'] ?>" data-alamat="<?= $row['alamat'] ?>"
                                            data-deskripsi="<?= $row['deskripsi'] ?>"
                                            data-kategori="<?= $row['kategori'] ?>" id="buttonEditUMKM">Edit</button>
                                        <button class='btn btn-danger w-50' data-bs-toggle='modal' data-bs-target='#deleteUMKM'
                                            data-id="<?= $row['id_umkm'] ?>">Hapus</button>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php $no++;
                    }
                } 
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
                        <form action="umkm.php" method="post" id="adminLogin">
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
                        <form action='umkm.php' method='post' id="adminout">
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
    <!-- Modal untuk tambah UMKM -->
    <div class="tambahUMKM">
        <div class="modal fade" id="tambahUMKM" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">TAMBAH UMKM</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="umkm.php" method="post" id="tambahkanUMKM" onsubmit="validateForm(event)" name="formTambahUMKM">
                            <label for="nama" class="form-label">Masukan Nama UMKM</label>
                            <input type="text" class="form-control" name="nama" id="nama"><br>
                            <p class="text-muted" id="errorNama"></p>
                            <label for="gambar" class="form-label">Pilih gambar</label>
                            <input type="file" class="form-control" name="gambar" id="gambar">
                            <p>Gambar harus berbentuk landscape</p>
                            <label for="alamat" class="form-label">Masukan Alamat</label>
                            <input type="text" class="form-control" name="alamat" id="alamat"><br>
                            <p class="text-muted" id="errorAlamat"></p>
                            <label for="kategori" class="form-label">Pilih Kategori</label>
                            <select name="kategori" id="kategori" class="form-select">
                                <option value="Kuliner">Kuliner</option>
                                <option value="Pariwisata">Pariwisata</option>
                                <option value="Produk">Produk</option>
                            </select><br>
                            <label for="deskripsi" class="form-label">Masukan deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"
                                cols="50"></textarea><br>
                            <p class="text-muted" id="errorDeskripsi"></p>
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
    <!-- Modal untuk edit UMKM -->
    <div class="editUMKM">
        <div class="modal fade" id="editUMKM" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">EDIT BERITA</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="umkm.php" method="post" id="editkanUMKM" enctype="multipart/form-data" name="formEditUMKM" onsubmit="editValidateForm(event)"> 
                            <input type="hidden" class="form-control" name="id" id="editId">
                            <label for="nama" class="form-label">Masukan Nama UMKM</label>
                            <input type="text" class="form-control" name="editnama" id="editnama"><br>
                            <p class="text-muted" id="editerrorNama"></p>
                            <label for="gambar" class="form-label">Pilih gambar</label>
                            <input type="file" class="form-control" name="editgambar" id="editgambar">
                            <p>Gambar harus berbentuk landscape</p>
                            <label for="alamat" class="form-label">Masukan Alamat</label>
                            <input type="text" class="form-control" name="editalamat" id="editalamat"><br>
                            <p class="text-muted" id="editerrorAlamat"></p>
                            <label for="editkategori" class="form-label">pilih Kategori</label>
                            <select name="editkategori" id="editkategori" class="form-select">
                                <option value="Kuliner">Kuliner</option>
                                <option value="Pariwisata">Pariwisata</option>
                                <option value="Produk">Produk</option>
                            </select><br>
                            <label for="deskripsi" class="form-label">Masukan deskripsi</label>
                            <textarea name="editdeskripsi" id="editdeskripsi" class="form-control" rows="3"
                                cols="50"></textarea><br>
                            <p class="text-muted" id="editerrorDeskripsi"></p>
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
    <!-- Modal untuk hapus UMKM -->
    <div class="hapusUMKM">
        <div class="modal fade" id="deleteUMKM" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">HAPUS BERITA</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah anda yakin ingin menghapus berita ini?</p>
                        <form action="umkm.php" method="post" id="hapusUMKM">
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
            // Untuk notifikasi ajax hapus umkm
            $('.hapusUMKM').on('submit', '#hapusUMKM', function (e) {
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
            // Untuk notifikasi ajax tambah UMKM
            $('.tambahUMKM').on('submit', '#tambahkanUMKM', function (e) {
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
            // Untuk notifikasi ajax edit UMKM
            $('.editUMKM').on('submit', '#editkanUMKM', function (e) {
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
            })
            // Untuk mengisi value sementara pada form edit UMKM
            const editUMKM = document.getElementById('editUMKM');
            editUMKM.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; 
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama'); 
            const alamat = button.getAttribute('data-alamat'); 
            const deskripsi = button.getAttribute('data-deskripsi');
            const kategori = button.getAttribute('data-kategori');

            const formId = document.getElementById('editId');
            const formNama = document.getElementById('editnama');
            const formGambar = document.getElementById('editgambar');
            const formAlamat = document.getElementById('editalamat');
            const formKategori = document.getElementById('editkategori');
            const formDeskripsi = document.getElementById('editdeskripsi');

            console.log(deskripsi);
            formId.value = id;
            formNama.value = nama;
            formAlamat.value = alamat;
            formKategori.value = kategori;
            formDeskripsi.innerHTML =  deskripsi;
            });
            // Untuk menghapus UMKM
            const hapusUMKM = document.getElementById('deleteUMKM');
            deleteUMKM.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; 
            const id = button.getAttribute('data-id');
            const formId = document.getElementById('deleteId');
            console.log(id);
            formId.value = id;
            });
        });
        function validateForm(event) {
            let isValid = true;

            const nama = document.forms['formTambahUMKM']['nama'].value.trim();
            const alamat = document.forms['formTambahUMKM']['alamat'].value.trim();
            const deskripsi = document.forms['formTambahUMKM']['deskripsi'].value.trim();

            const errorNama= document.getElementById('errorNama');
            const errorAlamat = document.getElementById('errorAlamat');
            const errorDeskripsi = document.getElementById('errorDeskripsi');

            if (!nama || nama.length < 20){
                errorNama.textContent = 'Nama minimal 20 karakter';
                isValid = false;
            } else{
                errorNama.textContent = '';
            }
            if (!alamat || alamat.length < 30){
                errorAlamat.textContent = 'Alamat minimal 30 karakter';
                isValid = false;
            } else{
                errorAlamat.textContent = '';
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

            const nama = document.forms['formEditUMKM']['editnama'].value.trim();
            const alamat = document.forms['formEditUMKM']['editalamat'].value.trim();
            const deskripsi = document.forms['formEditUMKM']['editdeskripsi'].value.trim();

            const errorNama= document.getElementById('editerrorNama');
            const errorAlamat = document.getElementById('editerrorAlamat');
            const errorDeskripsi = document.getElementById('editerrorDeskripsi');

            if (!nama || nama.length < 20){
                errorNama.textContent = 'Nama minimal 20 karakter';
                isValid = false;
            } else{
                errorNama.textContent = '';
            }
            if (!alamat || alamat.length < 30){
                errorAlamat.textContent = 'Alamat minimal 30 karakter';
                isValid = false;
            } else{
                errorAlamat.textContent = '';
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