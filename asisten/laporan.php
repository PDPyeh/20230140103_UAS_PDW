<?php
session_start();
require_once '../config.php';

$where = '1=1';
if (!empty($_GET['mahasiswa'])) {
    $where .= " AND u.nama LIKE '%" . $conn->real_escape_string($_GET['mahasiswa']) . "%'";
}
if (!empty($_GET['modul'])) {
    $where .= " AND mo.judul LIKE '%" . $conn->real_escape_string($_GET['modul']) . "%'";
}
if (isset($_GET['status']) && $_GET['status'] !== '') {
    if ($_GET['status'] === 'belum') {
        $where .= " AND l.nilai IS NULL";
    } else {
        $where .= " AND l.nilai IS NOT NULL";
    }
}


if (isset($_POST['beri_nilai'])) {
    $id_laporan = $_POST['id_laporan'];
    $nilai = intval($_POST['nilai']);
    $feedback = $_POST['feedback'];
    $conn->query("UPDATE laporan SET nilai=$nilai, feedback='$feedback' WHERE id=$id_laporan");
    header("Location: laporan.php");
    exit;
}


$laporan = $conn->query("SELECT l.*, u.nama AS mahasiswa, mo.judul AS modul FROM laporan l 
    JOIN users u ON l.id_user = u.id 
    JOIN modul mo ON l.id_modul = mo.id 
    WHERE $where 
    ORDER BY l.uploaded_at DESC
");

$pageTitle = 'Laporan Masuk';
$activePage = 'laporan';
require_once 'templates/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold mb-4">Filter Laporan</h2>
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <input name="mahasiswa" placeholder="Nama Mahasiswa" class="border p-2 rounded" value="<?= $_GET['mahasiswa'] ?? '' ?>">
        <input name="modul" placeholder="Judul Modul" class="border p-2 rounded" value="<?= $_GET['modul'] ?? '' ?>">
        <select name="status" class="border p-2 rounded">
            <option value="">Semua Status</option>
            <option value="belum" <?= ($_GET['status'] ?? '') == 'belum' ? 'selected' : '' ?>>Belum Dinilai</option>
            <option value="sudah" <?= ($_GET['status'] ?? '') == 'sudah' ? 'selected' : '' ?>>Sudah Dinilai</option>
        </select>
        <button class="bg-blue-600 text-white px-4 py-2 rounded col-span-1">Filter</button>
    </form>
</div>

<div>
    <h2 class="text-2xl font-bold mb-4">Daftar Laporan</h2>
    <div class="space-y-4">
        <?php while ($r = $laporan->fetch_assoc()) : ?>
            <div class="bg-white p-4 rounded shadow-md">
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <strong><?= $r['mahasiswa'] ?></strong> — <?= $r['modul'] ?><br>
                        <small>Dikirim: <?= $r['uploaded_at'] ?></small>
                    </div>
                    <a href="/SistemPengumpulanTugas/assets/laporan/<?= $r['file_laporan'] ?>" class="text-blue-500 underline">Download</a>
                </div>
                <form method="POST" class="flex flex-col gap-2 md:flex-row md:items-center">
                    <input type="hidden" name="id_laporan" value="<?= $r['id'] ?>">
                    <input type="number" name="nilai" placeholder="Nilai" class="border p-2 rounded w-24" value="<?= $r['nilai'] ?>">
                    <input type="text" name="feedback" placeholder="Feedback" class="border p-2 rounded flex-1" value="<?= $r['feedback'] ?>">
                    <button name="beri_nilai" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
