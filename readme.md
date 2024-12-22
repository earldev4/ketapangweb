# TUGAS UAS MATA KULIAH PEMOGRAMAN WEB
## _Website Profile Desa Ketapang_

```sh
Nama    : Deva Ahmad
NIM     : 122140015
Kelas   : Pemograman Web RA
```

_**Tentang Website**_: Website ini saya buat sebagai tugas UAS mata kuliah pemograman web sekaligus menjadi progja saya untuk KKN nanti. Ini adalah website profile untuk Desa Ketapang yang terletak di Kecamatan Limau, Kabupaten Tanggamus, Lampung. Website ini memuat halaman:
- _Halaman Utama_, menampilkan carousel foto pemandangan desa
- _Halaman Profile_, menampilkan identitas desa, seperti sejarah, visi misi, struktur perangkat desa, dsb
- _Halaman Berita_, menampilkan berita terkini yang terjadi di desa, atau diluar lingkungan desa
- _Halaman Detail Berita_, menampilkan detail berita terkait
- _Halaman UMKM_, menampilkan daftar UMKM yang ada di desa ketapang
- _Halaman Detail UMKM_, menampilkan detail UMKM terkait
- _Halaman Pengaduan_, menampilkan daftar pengaduan dari warga
- _Halaman Detail Pengaduan_, menampilkan detail pengaduan terkait
- _Modal Login Admin_, untuk admin melakukan sesi login
- _Modal Logout Admin_, untuk admin melakukan sesi logout
- _Modal Pengaduan_, untuk warga dapat melakukan pengaduan kepada perangkat daerah
- _Modal Menambahkan Berita_, untuk admin melakukan penambahan berita
- _Modal Mengedit Berita_, untuk admin melakukan pengeditan berita
- _Modal Menghapus Berita_, untuk admin melakukan penghapusan berita
- _Modal Menambahkan UMKM_, untuk admin melakukan penambahan UMKM
- _Modal Mengedit UMKM_, untuk admin melakukan pengeditan UMKM
- _Modal Menghapus UMKM_, untuk admin melakukan penghapusan UMKM
- _Modal Menghapus Pengaduan_, untuk admin melakukan penghapusan pengaduan

## Teknologi

Teknologi yang digunakan pada pembangunan website ini antara lain adalah:

- [HTML] - HTML digunakan untuk mendefinisikan tujuan dan struktur dalam konten website.
- [CSS] - CSS digunakan untuk mendeskripsikan bagaimana elemen harus dirender pada layar.
- [Javascript] - Javascript digunakan untuk memberikan logika dan interaksi pada halaman website.
- [Bootstrap 5] - Boostrap adalah framework CSS yang digunakan untuk memberikan elemen CSS tanpa harus mendefinisikan dari awal.
- [jQuery] - jQuery adalah library javascript yang digunakan untuk _simplifikasi_ manipulasi HTML, event handling, animasi, dan Ajax.
- [PHP] - PHP _(Hypertext Preprocessor)_ merupakan bahasa pemrograman server-side yang digunakan untuk membuat dan mengembangkan website.
- [PHP Flasher] - PHP Flasher adalah alat yang membantu menambahkan pesan flash ke aplikasi web.
- [MySQL Workbench] - MySQL Workbench adalah alat visual terpadu untuk arsitek database, pengembang, dan DBA. 

## Struktur Folder
└── 📁 web_uas_pemweb_ra/
    ├── 📁 assets
    ├── 📁 image/
    │   ├── 📁 berita
    │   └── 📁 umkm
    ├── 📁 includes/
    │   └── 🐘 db.php
    ├── 📁 style/
    │   └── styles.css
    ├── 🐘 berita.php
    ├── ⛁ db_oop.sql
    ├── 🐘 detailberita.php
    ├── 🐘 detailpengaduan.php
    ├── 🐘 detailumkm.php
    ├── 🐘 index.php
    ├── 🐘 pengaduan.php
    ├── 🐘 profile.php
    ├── 🐘 umkm.php
    └── 🐘 readme.md

## Instalasi
- Download repositori dari github
- Ekstrak file ZIP repositori
- Ubah nama folder menjadi _ketapangweb.com_
- Copy folder ke directori **C:\xampp\htdocs**
- Masuk ke file **db.php**, hapus password database
- Buka aplikasi MySQL Workbench atau PHPMyAdmin, import file **db_oop.sql** dan jalankan query
- ketikan _localhost/ketapangweb.com_ pada browser

## Kriteria Penilaian UAS
| Kriteria | Bobot Nilai |
| ------ | ------ |
| Manipulasi DOM dengan JavaScript | 15% |
| Event Handling | 15% |
| Pengelolaan Data dengan PHP | 20% |
| Objek PHP Berbasis OOP | 10% |
| Pembuatan Tabel Database | 5% |
| Konfigurasi Koneksi Database | 5% |
| Manipulasi Data pada Database | 10% |
| State Management dengan Session | 10% |
| Pengelolaan State dengan Cookie dan Browser Storage | 10% |

## Detail Pengerjaan
### Bagian 1: Client-side Programming (Bobot: 30%)
#### 1.1 Manipulasi DOM dengan Javascript
- Membuat form Pengaduan dengan 4 jenis elemen input, yaitu **text** untuk nama/alamat, **number** untuk umur, **radio** untuk memilih jenis kelamin, dan **textarea** untuk deskripsi pengaduan. Form kemudian akan dikirimkan pada backend file index.php menggunakan method POST, namun sebelum dikumpulkan form akan divalidasi pada fungsi _validateForm(event)_ di Javascript.
```` ruby
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
````
- Menampilkan data dari database ke dalam sebuah tabel menggunakan perulangan foreach yang memecah variabel **$pengaduan** menjadi **$row[]** dan menampilkan sesuai indeksnya.
````ruby
<table class="table table-hover">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Umur</th>
            <th>Kelamin</th>
            <th>Alamat</th>
            <th class="on-mobile">IP Perangkat</th>
            <th class="on-mobile">Browser</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
            if (empty($pengaduan)){
                echo "
                        <tr>
                            <td colspan='8' class='text-center'>Tidak ada data</td>
                        </tr>
                    ";
            } else {
                $no = 1;
                foreach ($pengaduan as $row) { ?>
                    <?php 
                        echo "
                            <tr>
                                <td>{$no}</td>
                                <td>{$row['nama']}</td>
                                <td>{$row['umur']}</td>
                                <td>{$row['kelamin']}</td>
                                <td>{$row['alamat']}</td>
                                <td class='on-mobile'>{$row['ip_perangkat']}</td>
                                <td class='on-mobile '>{$row['browser']}</td>
                                <td>
                                    <a href='detailpengaduan.php?id_pengaduan={$row['id_pengaduan']}&&no={$no}' class='btn btn-primary'>Detail</a>
                                    <button class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#deletePengaduanModal' data-username='{$row['nama']}' data-id='{$row['id_pengaduan']}' >Hapus</a>
                                </td>
                            </tr>
                        ";
                    ?> 
                <?php $no++; }  ?>
            <?php }
        ?>
    </tbody>
</table>
````
#### 1.2 Event Handling
- Sebelum form di 1.1 disubmit, form divalidasi pada function _validateForm(event)_. Validasi terdiri dari memeriksa panjang karakter pada input name, address, dan description. Function juga melakukan query selector dari elemen dengan dengan id **errorName**, **errorAddress**, **errorDescription** yang akan berisikan textContent jika karakter inputan di form kurang dari jumlah karakter yang ditentukan.
````ruby
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
````
### Bagian 2: Server-side Programming (Bobot: 30%)
#### 2.1 Pengelolaan Data dengan PHP
- Form 1.1 disubmit menggunakan metode POST, form akan masuk ke method **kirimPengaduan**. Di dalam method, data diparsing menjadi variable **$nama, $umur, $kelamin, $alamat, $deskripsi**, variabel **$ip_address** berisi nilai method **getClientIp()** dan variabel **$browser** yang menyimpan informasi browser pengadu. Semua input divalidasi pada percabangan if. Jika salah, maka akan mereturn status yang _error_, message yang _data tidak lengkap_. Jika benar, terdapat variabel **$stmt** yang akan melakukan fungsi **prepare()** dan memasukan variabel-variabel tersebut dengan query **'INSERT INTO pengaduan (nama, umur, kelamin, alamat, ip_perangkat, browser, deskripsi) VALUES (?, ?, ?, ?, ?, ?, ?)'**. Kemudian variabel **$stmt** melakukan fungsi **bind_param()** untuk mengikat parameter ke variabel-variabel. Selanjutnya variabel **$stmt** melakukan fungsi **execute()**. Kemudian akan mereturn status yang _success_, message yang _Pengaduan berhasil dikirim_ dan akan redirect ke _index.php_.
````ruby
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
````
#### 2.2 Objek PHP Berbasis OOP
- Terdapat kelas **HalIndex** yang akan menghandle segala fungsi yang dibutuhkan di halaman pengaduan. Kelas ini memiliki constructor function yang memasukan variabel koneksi database ke variabel **$this->conn**. Kelas ini akan dipanggil dalam variabel **$desaKetapang**. Method pada kelas akan ditrigger pada fungsi **$_SERVER['REQUEST_METHOD']**. Contohnya untuk mentrigger method **$kirimPengaduan** melalui **REQUEST_METHOD** dan melalui variabel global **$_POST** yang memiliki indeks _name_.
````ruby
class HalIndex{
    private $conn;
    public function __construct($dbConnection){
        $this->conn = $dbConnection;
        session_start();
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
include "includes/db.php";
$desaKetapang = new HalIndex($conn);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['name'])) {
        $response = $desaKetapang->kirimPengaduan($_POST);
        echo json_encode($response);
        exit();
    }
}
````
### Bagian 3: Database Management (Bobot: 20%)
#### 3.1 Pembuatan Tabel Database
- Membuat tabel database pengaduan pada MySQL Workbench dengan query berikut. 
    - Kolom **id_pengaduan** dengan tipe data interger yang auto increment dan not null. 
    - Kolom **nama** dengan tipe data varchar(100) 100 karakter dan not null. 
    - Kolom **umur** dengan tipe data interger dan not null. 
    - Kolom **kelamin** dengan tipe data enum yang memuat dua jenis inputan (Laki-laki/Perempuan) dan not null. 
    - Kolom **alamat** dengan tipe data longtext dan not null. 
    - Kolom **ip_perangkat** dengan tipe data varchar(45) 45 karakter dan not null. 
    - Kolom **deskripsi** dengan tipe data longtext dan not null. 
    - Kolom **created_at** dengan tipe data datetime dan not null dan memiliki nilai default **current_timestamp**.
````ruby
CREATE TABLE `pengaduan` (
  `id_pengaduan` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `umur` int NOT NULL,
  `kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `alamat` longtext NOT NULL,
  `ip_perangkat` varchar(45) NOT NULL,
  `browser` longtext NOT NULL,
  `deskripsi` longtext NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pengaduan`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
````
#### 3.2 Konfigurasi Koneksi Database
- Terdapat sebuah kelas Database, kelas ini memiliki private variabel **$hostname**, **$username**, **$password**, **$database**, dan **$connection**. Terdapat **constructor function** yang tidak memiliki parameter. Di dalamnya terdapat variabel **$this->connect()** dan function **date_default_timezone_set('Asia/Jakarta')** untuk menetapkan tanggal berdasarkan region website tersebut. Terdapat method **connect()** yang akan melakukan koneksi ke database dengan function _mysqli()_. Jika **$this->connection->connect_error** maka akan menampilkan pesan "Koneksi database rusak" dan memunculkan function **die("Error: " .  $this->connection->connect_error)**. Terdapat method getter **getConnection()** yang akan mereturn variabel **$this->connection**. Yang terakhir ada method **closeConnection()** yang akan mengecek jika koneksi selesai dilakukan maka akan diclose. Untuk memanggil kelas, terdapat variabel **$db** menyimpan koneksi **Database()**. Kemudian terdapat variabel **$conn** yang menyimpan method **$db->getConnection();**.
````ruby
class Database {
    private $hostname = "localhost";
    private $username = "root";
    private $password = "devola3465";
    private $database = "db_oop";
    private $connection;

    public function __construct(){
        $this->connect();
        date_default_timezone_set("Asia/Jakarta");
    }

    private function connect(){
        $this->connection = new mysqli($this->hostname, $this->username, $this->password, $this->database);
        if($this->connection->connect_error){
            echo "Koneksi database rusak";
            die ("Error: " .  $this->connection->connect_error);
        }
    }
    public function getConnection() {
        return $this->connection;
    }

    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

$db = new Database();
$conn = $db->getConnection();
````
#### 3.3 Manipulasi Data pada Database 
- Terdapat sebuah method **handleTampilkanPengaduan** untuk menampilkan pengaduan yang terdiri dari variabel **$page = 1**, dan **$rows_per_page = 10**. Di Dalam method terdapat variabel **$start_from** yang menampung nilai variabel **$page** dikurang 1 dan dikalikan **$rows_per_page**. Terdapat variabel **$stmt** yang berisi perintah function **prepare()** dengan query **"SELECT id_pengaduan, nama, umur, kelamin, alamat, ip_perangkat, browser FROM pengaduan ORDER BY created_at DESC LIMIT ?, ?"**. Variabel **$stmt** melakukan perintah function **bind_param**, kemudian mengeksekusi query. Hasil eksekusi query ditampung dalam variabel **$result**. Terdapat variabel **$pengaduan** berisi array kosong. Kemudian terdapat perulangan _while_ yang akan berjalan selama variabel **row** mendapatkan array assosiasi dari variabel **$result**. Variabel **$row** akan disimpan di dalam variabel array **$pengaduan**. Setelah selesai perulangan, maka variabel **$pengaduan** akan direturn.
````ruby
public function handleTampilkanPengaduan($page = 1, $rows_per_page = 10) {
    $start_from = ($page - 1) * $rows_per_page;
    $stmt = $this->conn->prepare("SELECT id_pengaduan, nama, umur, kelamin, alamat, ip_perangkat, browser FROM pengaduan ORDER BY created_at DESC LIMIT ?, ?");
    $stmt->bind_param("ii", $start_from, $rows_per_page);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_news_result = $this->conn->query("SELECT COUNT(*) AS total FROM pengaduan");
    $total_news = $total_news_result->fetch_assoc()['total'];
    $total_pages = ceil($total_news / $rows_per_page);

    $pengaduan = [];
      while ($row = $result->fetch_assoc()) {
        $pengaduan[] = $row;
      }
    return [
      "pengaduan" => $pengaduan,
      "total_pages" => $total_pages,
    ];
 }
````
### Bagian 4: State Management (Bobot: 20%)
#### 4.1 State Management dengan Session
- Session dimulai dengan menggunakan fungsi **session_start()**. Ketika admin melakukan sesi login, maka variabel global **$_SESSION[]** akan mendefinisikan index "username" dan menyimpan nilai array asosiatif **$dataAdmin['username']** dan **$_SESSION[]** akan mendefinisikan index "is_login" dan menyimpan nilai _true_, tanda bahwa admin sudah melakukan proses login. 
- Ketika admin melakukan sesi logout, maka sesi akan di unset dengan function **session_unset()** dan kemudian "dihancurkan" dengan function **session_destroy()**.
````ruby
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
````
#### 4.2 Pengelolaan State dengan Cookie dan Browser Storage
- Ketika admin melakukan login, maka cookie akan diset berdasarkan username admin dengan durasi cookie adalah 1 hari. 
````ruby
public function handleLogin($data){
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    setcookie('username', $username, time() + 60*60*24,'/');

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
````
- Cookie akan digunakan untuk menampilkan informasi "Halo, admin" untuk mengindikasikan bahwa admin berhasil login
````ruby
<div class="d-sm-block d-none mx-2">
    <h5 class="fw-bold">DESA KETAPANG</h5>
    <h6 class="fw-bold">KABUPATEN TANGGAMUS</h6>
    <?php if(isset($_COOKIE['username'])){ ?>
    <h6 class="fw-bold">Halo, <?php echo $_COOKIE['username']; ?></h6>
    <?php }?>
</div>
````
### Bagian 5: Hosting Aplikasi Web (Bobot: 20%)
#### 5.1 Langkah-langkah melakukan hosting website
- Pilih penyedia jasa hosting, pada kasus ini saya menggunakan _Niagahoster_
- Buat akun menggunakan email
- Pilih paket hosting
- Setelah melakukan pembayaran, buat nama dan pilih domain
- Pada dashboard domain, pilih menu **File Manager**
- Pada folder **public_html**, upload file-file kedalam folder
- Selanjutnya kembali ke beranda, pilih menu **Database**
- Buatlah database baru dengan memasukan nama database, nama username, dan password database, selanjutnya tekan **create**
- Copy nama username dan nama database
- Tekan **EnterPHPMyAdmin**, kemudian pilih IMPORT dan masukan file database dengan format _.sql_
- Setelah berhasil di import, kembali ke menu beranda
- Masuk lagi ke menu **File Manager**, cari file db.php
- Buka file db.php, ubah username, database, dan password sesuai database yang telah dibuat
- Akses link domain pada google
#### 5.2 Pilih penyedia hosting web yang menurut Anda paling cocok untuk aplikasi web Anda
Alasan memilih Niagahoster untuk website profile desa adalah Niagahoster menawarkan beberapa keunggulan yang membuatnya cocok untuk website profile desa. Seperti memiliki rekam jejak uptime yang sangat baik, infrastruktur server yang canggih, menggunakan teknologi terkini untuk menjaga stabilitas server, sehingga website desa tidak mudah down.
#### 5.3 Bagaimana Anda memastikan keamanan aplikasi web yang Anda host?
Penyedia hosting __NiagaHoster__ menawarkan beberapa fitur keamanan untuk melindungi aplikasi web. Berikut adalah fitur-fitur untuk membantu menjaga keamanan aplikasi web:
- **Proteksi DDoS Standar**: Fitur ini berfungsi untuk melindungi website dari serangan DDoS (Distributed Denial of Service). Serangan DDoS adalah upaya untuk membanjiri server dengan lalu lintas yang sangat besar sehingga membuat website tidak dapat diakses. 
- **Pendeteksi Malware**: Fitur ini secara aktif memantau website untuk mendeteksi adanya malware atau kode berbahaya lainnya. Jika ditemukan malware, sistem akan segera memberikan notifikasi sehingga dapat mengambil tindakan untuk membersihkan website.
- **Proteksi Privasi Domain WHOIS**: Fitur ini menyembunyikan informasi pribadi yang terdaftar pada domain. Hal ini berguna untuk melindungi dari spam, penipuan, dan kontak yang tidak diinginkan.
- **Aplikasi Web Firewall**: Fitur ini bertindak sebagai tembok api untuk aplikasi web. Web firewall akan menyaring semua lalu lintas yang masuk ke aplikasi web dan memblokir permintaan yang mencurigakan atau berbahaya.
- **Nameserver Dilindungi Cloudflare**: Cloudflare adalah layanan CDN (Content Delivery Network) yang juga menyediakan fitur keamanan tambahan. Dengan menggunakan nameserver Cloudflare, website akan mendapatkan perlindungan dari berbagai jenis serangan seperti DDoS, SQL injection, dan XSS.
#### 5.4 Jelaskan konfigurasi server yang Anda terapkan untuk mendukung aplikasi web Anda
 Konfigurasi server yang optimal akan sangat bergantung pada jenis aplikasi web, lalu lintas yang diharapkan, dan sumber daya yang tersedia. 
#### Terima Kasih!
> Created By Deva Ahmad || Know me more in [@earldev](https://www.instagram.com/earl.dev?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==)

   [HTML]: <https://developer.mozilla.org/en-US/docs/Web/HTML>
   [CSS]: <https://developer.mozilla.org/en-US/docs/Web/CSS>
   [Javascript]: <https://developer.mozilla.org/en-US/docs/Web/JavaScript>
   [Bootstrap 5]: <https://getbootstrap.com/docs/5.0/getting-started/introduction/>
   [jQuery]: <https://jquery.com/>
   [PHP]: <https://www.php.net/>
   [PHP Flasher]: <https://php-flasher.io/>
   [MySQL Workbench]: <https://www.mysql.com/products/workbench/>
   
   
