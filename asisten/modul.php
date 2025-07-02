<?php
session_start();
require_once '../config.php';



if (isset($_POST['tambah_praktikum'])) {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $conn->query("INSERT INTO praktikum (nama, deskripsi) VALUES ('$nama', '$deskripsi')");
    header("Location: modul.php");
    exit;
}


if (isset($_GET['hapus_praktikum'])) {
    $id = intval($_GET['hapus_praktikum']);
    $conn->query("DELETE FROM praktikum WHERE id=$id");
    header("Location: modul.php");
    exit;
}


if (isset($_POST['tambah_modul'])) {
    $id_praktikum = $_POST['id_praktikum'];
    $judul = $_POST['judul'];

    
    $file_name = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];
    move_uploaded_file($tmp, "/SistemPengumpulanTugas/assets/materi/$file_name");

    $conn->query("INSERT INTO modul (id_praktikum, judul, file_materi) VALUES ($id_praktikum, '$judul', '$file_name')");
    header("Location: modul.php");
    exit;
}


$praktikum = $conn->query("SELECT * FROM praktikum");


$modul = $conn->query("SELECT m.*, p.nama as nama_praktikum FROM modul m JOIN praktikum p ON m.id_praktikum = p.id");

$pageTitle = 'Kelola Praktikum & Modul';
$activePage = 'modul';
require_once 'templates/header.php'; 
?>

<div class="mb-8">
    <h2 class="text-2xl font-bold mb-4">Tambah Mata Praktikum</h2>
    <form method="POST" class="space-y-3">
        <input name="nama" placeholder="Nama Praktikum" class="w-full border p-2 rounded" required>
        <textarea name="deskripsi" placeholder="Deskripsi" class="w-full border p-2 rounded" required></textarea>
        <button name="tambah_praktikum" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah Praktikum</button>
    </form>
</div>

<div class="mb-12">
    <h2 class="text-2xl font-bold mb-4">Daftar Praktikum</h2>
    <ul class="space-y-2">
        <?php while ($p = $praktikum->fetch_assoc()) : ?>
            <li class="bg-white p-4 rounded shadow flex justify-between">
                <div>
                    <strong><?= $p['nama'] ?></strong><br>
                    <small><?= $p['deskripsi'] ?></small>
                </div>
                <a href="?hapus_praktikum=<?= $p['id'] ?>" class="text-red-500">Hapus</a>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

<div class="mb-12">
    <h2 class="text-2xl font-bold mb-4">Tambah Modul</h2>
    <form method="POST" enctype="multipart/form-data" class="space-y-3">
        <select name="id_praktikum" class="w-full border p-2 rounded" required>
            <option value="">Pilih Praktikum</option>
            <?php
            $praktikum->data_seek(0);
            while ($p = $praktikum->fetch_assoc()) :
            ?>
                <option value="<?= $p['id'] ?>"><?= $p['nama'] ?></option>
            <?php endwhile; ?>
        </select>
        <input name="judul" placeholder="Judul Modul" class="w-full border p-2 rounded" required>
        <input type="file" name="file" accept=".pdf,.docx" class="w-full border p-2 rounded" required>
        <button name="tambah_modul" class="bg-green-600 text-white px-4 py-2 rounded">Tambah Modul</button>
    </form>
</div>

<div>
    <h2 class="text-2xl font-bold mb-4">Daftar Modul</h2>
    <ul class="space-y-2">
        <?php while ($m = $modul->fetch_assoc()) : ?>
            <li class="bg-white p-4 rounded shadow">
                <strong><?= $m['judul'] ?></strong> — <?= $m['nama_praktikum'] ?><br>
                <a href="/SistemPengumpulanTugas/assets/materi/<?= $m['file_materi'] ?>" class="text-blue-500 underline">Download Materi</a>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

<?php require_once 'templates/footer.php'; ?>
