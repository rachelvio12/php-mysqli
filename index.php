<?php
include "config.php";

$npm = "";
$nama = "";
$alamat = "";
$fakultas = "";
$foto = "";
$sukses = "";
$error = "";


if (isset($_GET['op'])) {
    $op = $_GET['op'];
} else {
    $op = "";
}

if ($op == 'delete') {
    $id = $_GET['id'];

    $sql_get = "select foto from mahasiswa where id = '$id'";
    $q_get = mysqli_query($koneksi, $sql_get);
    $r_get = mysqli_fetch_array($q_get);
    if ($r_get && $r_get['foto'] && file_exists("images/" . $r_get['foto'])) {
        unlink("images/" . $r_get['foto']);
    }

    $sql1 = "delete from mahasiswa where id = '$id'";
    $q1 = mysqli_query($koneksi, $sql1);

    if ($q1) {
        $sukses = "Data Berhasil dihapus";
    } else {
        $error = "Data gagal di hapus";
    }
}

if ($op == 'edit') {
    $id = $_GET['id'];
    $sql1 = "select * from mahasiswa where id = $id";
    $q1 = mysqli_query($koneksi, $sql1);
    $r1 = mysqli_fetch_array($q1);

    if ($r1) {
        $npm = $r1['npm'];
        $nama = $r1['nama'];
        $alamat = $r1['alamat'];
        $fakultas = $r1['fakultas'];
        $foto = $r1['foto'];

    } else {
        $error = "Data nya ga ditemukan nich";
    }
}

if (isset($_POST['simpan'])) { //buat create bos
    $npm = $_POST['npm'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $fakultas = $_POST['fakultas'];
    $foto_name = "";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
        $filename = $_FILES['foto']['name'];
        $filetype = $_FILES['foto']['type'];
        $filesize = $_FILES['foto']['size'];

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!array_key_exists($ext, $allowed)) {
            $error = "Lo salah Format bos. Pake JPG, JPEG, atau PNG";
        } elseif ($filesize > 2 * 1024 * 1024) {
            $error = "Ukuran file maksimal 2MB yaa";
        } else {
            $foto_name = time() . "_" . $filename;
            $target = "images/" . $foto_name;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
                // Hapus foto lama jika update
                if ($op == 'edit') {
                    $sql_old = "select foto from mahasiswa where id='$id'";
                    $q_old = mysqli_query($koneksi, $sql_old);
                    $r_old = mysqli_fetch_array($q_old);
                    if ($r_old && $r_old['foto'] && file_exists("images/" . $r_old['foto'])) {
                        unlink("images/" . $r_old['foto']);
                    }
                }
            } else {
                $error = "Gagal mengupload foto";
            }
        }
    } else {

        if ($op == 'edit') {
            $sql_old = "select foto from mahasiswa where id='$id'";
            $q_old = mysqli_query($koneksi, $sql_old);
            $r_old = mysqli_fetch_array($q_old);
            if ($r_old) {
                $foto_name = $r_old['foto'];
            }
        }
    }

    if ($npm && $nama && $alamat && $fakultas) {
        if ($op == 'edit') { // buat update
            $sql1 = "update mahasiswa set npm = '$npm', nama = '$nama', alamat = '$alamat', fakultas = '$fakultas', foto = '$foto_name' where id = $id";
            $q1 = mysqli_query(mysql: $koneksi, query: $sql1);
            if ($q1) {
                $sukses = "Data udah di update yaaaa";
            } else {
                $error = "Waduh data gagal di update nich";
            }
        } else { //buat insert
            $sql1 = "insert into mahasiswa(npm, nama, alamat, fakultas, foto) values ('$npm', '$nama', '$alamat', '$fakultas', '$foto_name')";
            $q1 = mysqli_query($koneksi, $sql1);
            if ($q1) {
                $sukses = "Berhasil memasukkan data baru";
            } else {
                $error = "Gagal memasukkan data: " . mysqli_error($koneksi);
            }
        }
    } else {
        $error = "Silakan masukkan semua data";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        .mx-auto {
            width: 800px;
        }

        .card {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="mx-auto">
        <!-- masukkin data -->
        <div class="card">
            <div class="card-header">
                Create/Edit data
            </div>
            <div class="card-body">
                <?php if ($error) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error ?>
                    </div>
                    <?php
                    header("refresh:0;url=index.php");
                }
                ?>

                <?php
                if ($sukses) {

                    ?>

                    <div class="alert alert-success" role="alert">
                        <?php echo $sukses ?>
                    </div>
                    <?php
                    header("refresh:5;url=index.php");
                }
                ?>


                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="npm" class="form-label">NPM</label>
                        <input type="text" class="form-control" id="npm" name="npm" value="<?php echo $npm ?>">
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $nama ?>">
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="alamat" name="alamat" value="<?php echo $alamat ?>">
                    </div>
                    <div class="mb-3">
                        <label for="fakultas" class="form-label">Fakultas</label>
                        <select class="form-control" name="fakultas" id="fakultas">
                            <option value="">- Pilih Fakultas -</option>
                            <option value="Teknologi Industri" <?php if ($fakultas == "Teknologi Industri")
                                echo "selected" ?>>Teknologi Industri</option>
                                <option value="Ilmu Komputer" <?php if ($fakultas == "Ilmu Komputer")
                                echo "selected" ?>>Ilmu Komputer</option>
                                <option value="Psikologi" <?php if ($fakultas == "Psikologi")
                                echo "selected" ?>>Psikologi</option>
                                <option value="Manajemen" <?php if ($fakultas == "Manajemen")
                                echo "selected" ?>>Manajemen</option>
                                <option value="Kedokteran" <?php if ($fakultas == "Kedokteran")
                                echo "selected" ?>>Kedokteran</option>
                                <option value="Ilmu Komunikasi" <?php if ($fakultas == "Ilmu Komunikasi")
                                echo "selected" ?>>Ilmu Komunikasi</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB</small>

                        <?php if ($foto) { ?>
                            <div class="mt-2">
                                <img src="images/<?php echo $foto ?>" alt="Foto Mahasiswa" class="img-thumbnail"
                                    style="max-width: 200px; max-height: 200px;">
                                <p class="text-muted mt-1">Foto saat ini</p>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="col-12">
                        <input type="submit" name="simpan" value="Simpan Data" class="btn btn-primary" />
                    </div>
                </form>
            </div>
        </div>

        <!-- Mengeluarkan data -->
        <div class="card">
            <div class="card-header text-white bg-secondary">
                Data Mahasiswa
            </div>
            <div class="card-body">
                <form method="GET" class="row g-2 mb-3">
                    <div class="col-md-9">
                        <input type="text" name="nama" class="form-control" placeholder="Cari nama mahasiswa..."
                            value="<?= isset($_GET['nama']) ? $_GET['nama'] : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            Cari
                        </button>
                    </div>
                </form>

                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Foto</th>
                            <th scope="col">NPM</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Alamat</th>
                            <th scope="col">Fakultas</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    <tbody>
                        <?php
                        if (isset($_GET['nama']) && $_GET['nama'] != '') {
                            $nama_cari = mysqli_real_escape_string($koneksi, $_GET['nama']);
                            $sql2 = "SELECT * FROM mahasiswa 
                            WHERE nama LIKE '%$nama_cari%' 
                            ORDER BY id DESC";
                        } else {
                            $sql2 = "SELECT * FROM mahasiswa ORDER BY id DESC";
                        }

                        $q2 = mysqli_query($koneksi, $sql2);
                        $urut = 1;
                        while ($r2 = mysqli_fetch_array($q2)) {
                            $id = $r2['id'];
                            $npm = $r2['npm'];
                            $nama = $r2['nama'];
                            $alamat = $r2['alamat'];
                            $fakultas = $r2['fakultas'];
                            $foto = $r2['foto'];

                            ?>
                            <tr>
                                <th scope="row"><?php echo $urut++ ?></th>
                                <td scope="row">
                                    <?php if ($foto) { ?>
                                        <img src="images/<?php echo $foto ?>" alt="Foto"
                                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    <?php } else { ?>
                                        <span class="text-muted">-</span>
                                    <?php } ?>
                                </td>
                                <td scope="row"><?php echo $npm ?></td>
                                <td scope="row"><?php echo $nama ?></td>
                                <td scope="row"><?php echo $alamat ?></td>
                                <td scope="row"><?php echo $fakultas ?></td>
                                <td scope="row">
                                    <a href="index.php?op=edit&id=<?php echo $id ?>"><button type="button"
                                            class="btn btn-warning">Edit</button></a>
                                    <a href="index.php?op=delete&id=<?= $id ?>"
                                        onclick="return confirm('yakin mau delete data??🤔')"><button type="button"
                                            class="btn btn-danger">Delete</button></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</body>

</html>