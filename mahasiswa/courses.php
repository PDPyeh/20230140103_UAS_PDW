<?php
session_start();
require_once '../config.php';
$pageTitle = 'Katalog Praktikum';
$activePage = 'course';
require_once 'templates/header_mahasiswa.php';


$id_user = $_SESSION['user_id'];


$sql = "SELECT * FROM praktikum";
$result = $conn->query($sql);


$diikuti = [];
$q_diikuti = $conn->query("SELECT id_praktikum FROM pendaftaran_praktikum WHERE id_user = $id_user");
while ($row = $q_diikuti->fetch_assoc()) {
    $diikuti[] = $row['id_praktikum'];
}


if (isset($_GET['daftar'])) {
    $id_prak = intval($_GET['daftar']);
    if (!in_array($id_prak, $diikuti)) {
        $conn->query("INSERT INTO pendaftaran_praktikum (id_user, id_praktikum) VALUES ($id_user, $id_prak)");
        header("Location: dashboard.php");
        exit;
    }
}

?>

<div class="bg-gradient-to-r from-blue-500 to-blue-200 text-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold">Katalog Mata Praktikum</h1>
    <p class="mt-2 opacity-90">Pilih mata praktikum yang ingin kamu ikuti.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
    <?php while ($row = $result->fetch_assoc()) : ?>
        <div class="bg-white p-6 rounded-xl shadow-md flex flex-col justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($row['nama']) ?></h2>
                <p class="text-gray-600 text-sm"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>
            </div>

            <div class="mt-4">
                <?php if (in_array($row['id'], $diikuti)) : ?>
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">Sudah Terdaftar</span>
                <?php else : ?>
                    <a href="?daftar=<?= $row['id'] ?>" class="inline-block px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php
require_once 'templates/footer_mahasiswa.php';
?>
