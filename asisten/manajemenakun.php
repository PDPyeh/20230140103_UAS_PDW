<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: ../login.php");
    exit;
}


if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $conn->query("INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')");
    header("Location: manajemenakun.php");
    exit;
}


if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: manajemenakun.php");
    exit;
}


if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $query = "UPDATE users SET nama='$nama', email='$email', role='$role'";
    if (!empty($_POST['password'])) {
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query .= ", password='$pass'";
    }
    $query .= " WHERE id=$id";

    $conn->query($query);
    header("Location: manajemenakun.php");
    exit;
}


$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$pageTitle = 'Manajemen Akun';
$activePage = 'akun';
require_once 'templates/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Tambah Akun</h2>
    <form method="POST" class="grid md:grid-cols-2 gap-4">
        <input type="text" name="nama" placeholder="Nama Lengkap" class="border p-2 rounded" required>
        <input type="email" name="email" placeholder="Email" class="border p-2 rounded" required>
        <input type="password" name="password" placeholder="Password" class="border p-2 rounded" required>
        <select name="role" class="border p-2 rounded" required>
            <option value="">Pilih Role</option>
            <option value="mahasiswa">Mahasiswa</option>
            <option value="asisten">Asisten</option>
        </select>
        <button name="tambah" class="bg-blue-600 text-white px-4 py-2 rounded col-span-2">Tambah Akun</button>
    </form>
</div>

<div class="mt-10">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Daftar Akun</h2>
    <table class="w-full border text-sm">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 border">Nama</th>
                <th class="p-2 border">Email</th>
                <th class="p-2 border">Role</th>
                <th class="p-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($u = $users->fetch_assoc()): ?>
            <tr>
                <form method="POST">
                    <td class="border p-2"><input type="text" name="nama" value="<?= $u['nama'] ?>" class="w-full border p-1 rounded"></td>
                    <td class="border p-2"><input type="email" name="email" value="<?= $u['email'] ?>" class="w-full border p-1 rounded"></td>
                    <td class="border p-2">
                        <select name="role" class="w-full border p-1 rounded">
                            <option value="mahasiswa" <?= $u['role'] == 'mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
                            <option value="asisten" <?= $u['role'] == 'asisten' ? 'selected' : '' ?>>Asisten</option>
                        </select>
                    </td>
                    <td class="border p-2 flex gap-2 items-center">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <input type="password" name="password" placeholder="Password baru (opsional)" class="border p-1 rounded w-48">
                        <button name="edit" class="bg-green-600 text-white px-2 py-1 rounded text-sm">Simpan</button>
                        <a href="?hapus=<?= $u['id'] ?>" class="text-red-500 text-sm">Hapus</a>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'templates/footer.php'; ?>
