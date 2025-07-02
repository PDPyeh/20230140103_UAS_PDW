<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

$id_user = intval($_SESSION['user_id']);

// Ambil daftar praktikum yang diikuti
$praktikum = $conn->query("
    SELECT p.id, p.nama, p.deskripsi
    FROM pendaftaran_praktikum pp
    JOIN praktikum p ON pp.id_praktikum = p.id
    WHERE pp.id_user = $id_user
");

// Tangani upload laporan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_laporan'])) {
    $id_modul = intval($_POST['id_modul']);
    $filename = $_FILES['laporan']['name'];
    $tmp = $_FILES['laporan']['tmp_name'];
    move_uploaded_file($tmp, "/SistemPengumpulanTugas/assets/laporan/$filename");

    // Insert laporan
    $conn->query("INSERT INTO laporan (id_user, id_modul, file_laporan) VALUES ($id_user, $id_modul, '$filename')");
    header("Location: my_courses.php");
    exit;
}
?>

<?php
$pageTitle = 'Praktikum Saya';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Praktikum yang Diikuti</h2>
</div>

<?php while ($p = $praktikum->fetch_assoc()): ?>
    <div class="bg-white p-4 rounded shadow mb-6">
        <h3 class="text-xl font-semibold text-blue-700"><?= $p['nama'] ?></h3>
        <p class="text-gray-600 mb-2"><?= $p['deskripsi'] ?></p>

        <?php
        $id_prak = $p['id'];
        $modul = $conn->query("SELECT * FROM modul WHERE id_praktikum = $id_prak");
        while ($m = $modul->fetch_assoc()):
            $id_modul = $m['id'];
            $lap = $conn->query("SELECT * FROM laporan WHERE id_user=$id_user AND id_modul=$id_modul")->fetch_assoc();
        ?>
        <div class="border-t pt-3 mt-3">
            <strong><?= $m['judul'] ?></strong><br>
            <a href="/SistemPengumpulanTugas/assets/materi/<?= $m['file_materi'] ?>" class="text-blue-500 underline text-sm">Unduh Materi</a><br>

            <?php if ($lap): ?>
                ✅ <span class="text-green-600 text-sm">Sudah dikumpulkan</span><br>
                <span class="text-sm text-gray-600">Nilai: <?= $lap['nilai'] ?? 'Belum dinilai' ?></span><br>
                <span class="text-sm">Feedback: <?= $lap['feedback'] ?? '-' ?></span><br>
                <a href="/SistemPengumpulanTugas/assets/laporan/<?= $lap['file_laporan'] ?>" class="text-blue-500 underline text-sm">Download Laporan</a>
            <?php else: ?>
                <form method="POST" enctype="multipart/form-data" class="mt-2">
                    <input type="hidden" name="id_modul" value="<?= $id_modul ?>">
                    <input type="file" name="laporan" required class="border p-1 rounded w-full text-sm">
                    <button name="upload_laporan" class="bg-blue-600 text-white px-3 py-1 rounded mt-2 text-sm">Kumpulkan</button>
                </form>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
<?php endwhile; ?>

<?php require_once 'templates/footer_mahasiswa.php'; ?>
