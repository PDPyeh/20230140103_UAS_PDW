<?php
session_start();
require_once '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

$id_user = intval($_SESSION['user_id']);

// Hitung praktikum diikuti
$jumlah_praktikum = $conn->query("SELECT COUNT(*) AS total FROM pendaftaran_praktikum WHERE id_user = $id_user")->fetch_assoc()['total'];

// Hitung laporan selesai
$laporan_selesai = $conn->query("SELECT COUNT(*) AS total FROM laporan WHERE id_user = $id_user AND file_laporan IS NOT NULL")->fetch_assoc()['total'];

// Hitung laporan menunggu dinilai
$laporan_menunggu = $conn->query("SELECT COUNT(*) AS total FROM laporan WHERE id_user = $id_user AND nilai IS NULL")->fetch_assoc()['total'];

$notif = $conn->query("
    SELECT l.*, mo.judul AS nama_modul, p.nama AS nama_praktikum
    FROM laporan l
    JOIN modul mo ON l.id_modul = mo.id
    JOIN praktikum p ON mo.id_praktikum = p.id
    WHERE l.id_user = $id_user
    ORDER BY l.uploaded_at DESC
    LIMIT 5
");

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header_mahasiswa.php';

?>


<div class="bg-gradient-to-r from-blue-500 to-blue-200 text-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold">Selamat Datang Kembali, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
    <p class="mt-2 opacity-90">Terus semangat dalam menyelesaikan semua modul praktikummu.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-blue-600"><?= $jumlah_praktikum ?></div>
        <div class="mt-2 text-lg text-gray-600">Praktikum Diikuti</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-green-500"><?= $laporan_selesai ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Selesai</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-yellow-500"><?= $laporan_menunggu ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Menunggu</div>
    </div>
    
</div>

<div class="bg-white p-6 rounded-xl shadow-md">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Notifikasi Terbaru</h3>
    <ul class="space-y-4">
        <?php while ($n = $notif->fetch_assoc()): ?>
            <li class="flex items-start p-3 border-b border-gray-100 last:border-b-0">
                <span class="text-xl mr-4">
                    <?= $n['nilai'] !== null ? '✅' : '⏳' ?>
                </span>
                <div>
                    <?php if ($n['nilai'] !== null): ?>
                        Nilai untuk <strong><?= $n['nama_modul'] ?></strong> pada praktikum <strong><?= $n['nama_praktikum'] ?></strong> telah diberikan.
                        <br><span class="text-sm text-gray-500">Nilai: <?= $n['nilai'] ?> | Feedback: <?= $n['feedback'] ?></span>
                    <?php else: ?>
                        Kamu telah mengumpulkan laporan untuk <strong><?= $n['nama_modul'] ?></strong>.
                        <br><span class="text-sm text-gray-500">Menunggu penilaian...</span>
                    <?php endif; ?>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
</div>


<?php
// Panggil Footer
require_once 'templates/footer_mahasiswa.php';
?>