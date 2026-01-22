<?php
require_once('../../config/connect.php');
session_start();

if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../../../../auth/logout.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM matkul");

// TAMBAH
if (isset($_POST['tambah'])) {
    $kode_matkul = $_POST['kode_matkul'];
    $nama_matkul = $_POST['nama_matkul'];
    $sks = $_POST['sks'];
    $query = "INSERT INTO matkul (kode_matkul, nama_matkul, sks) VALUES ('$kode_matkul', '$nama_matkul', '$sks')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = ['type' => 'success', 'text' => 'Data mata kuliah berhasil ditambahkan.'];
    } else {
        $_SESSION['msg'] = ['type' => 'error', 'text' => 'Gagal menambahkan mata kuliah: ' . mysqli_error($conn)];
    }
    header("Location: mata_kuliah.php");
    exit();
}

// HAPUS
if (isset($_GET['hapus'])) {
    $kode_matkul = $_GET['hapus'];
    $query = "DELETE FROM matkul WHERE kode_matkul='$kode_matkul'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = ['type' => 'success', 'text' => 'Data mata kuliah berhasil dihapus.'];
    } else {
        $_SESSION['msg'] = ['type' => 'error', 'text' => 'Gagal menghapus mata kuliah: ' . mysqli_error($conn)];
    }
    header("Location: mata_kuliah.php");
    exit();
}

// UPDATE
if (isset($_POST['update'])) {
    $kode_matkul = $_POST['kode_matkul'];
    $nama_matkul = $_POST['nama_matkul'];
    $sks = $_POST['sks'];
    $query = "UPDATE matkul SET nama_matkul='$nama_matkul', sks='$sks' WHERE kode_matkul='$kode_matkul'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = ['type' => 'success', 'text' => 'Data mata kuliah berhasil diupdate.'];
    } else {
        $_SESSION['msg'] = ['type' => 'error', 'text' => 'Gagal mengupdate mata kuliah: ' . mysqli_error($conn)];
    }
    header("Location: mata_kuliah.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Data Mata Kuliah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 bg-gray-100">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Data Mata Kuliah</h1>
        <button onclick="openAdd()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah</button>
    </div>

    <!-- PESAN CRUD -->
    <?php if (isset($_SESSION['msg'])): ?>
        <div class="mb-4 px-4 py-2 rounded <?= $_SESSION['msg']['type'] === 'success' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' ?>">
            <?= $_SESSION['msg']['text'] ?>
        </div>
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>

    <table class="w-full bg-white rounded shadow border">
        <thead class="bg-gray-200">
            <tr>
                <th class="border p-2">Kode Matkul</th>
                <th class="border p-2">Nama Matkul</th>
                <th class="border p-2">SKS</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td class="border p-2"><?= htmlspecialchars($row['kode_matkul']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($row['nama_matkul']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($row['sks']) ?></td>
                    <td class="border p-2 space-x-2">
                        <button onclick="openEdit('<?= $row['kode_matkul'] ?>', '<?= $row['nama_matkul'] ?>', '<?= $row['sks'] ?>')" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                        <a href="?hapus=<?= $row['kode_matkul'] ?>" onclick="return confirm('Hapus data?')" class="bg-red-600 text-white px-2 py-1 rounded">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- MODAL -->
    <div id="modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
        <div class="bg-white p-6 rounded w-96">
            <h2 id="modalTitle" class="text-xl font-bold mb-4"></h2>
            <form method="POST">
                <input type="text" name="kode_matkul" id="kode_matkul" class="w-full border p-2 mb-2" placeholder="Kode Matkul" required>
                <input type="text" name="nama_matkul" id="nama_matkul" class="w-full border p-2 mb-2" placeholder="Nama Matkul" required>
                <input type="number" name="sks" id="sks" class="w-full border p-2 mb-4" placeholder="SKS" required>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                    <button type="submit" id="submitBtn" class="px-4 py-2 bg-blue-600 text-white rounded"></button>
                </div>
            </form>
        </div>
    </div>

<script>
const modal = document.getElementById('modal');
const modalTitle = document.getElementById('modalTitle');
const submitBtn = document.getElementById('submitBtn');
const kode_matkul = document.getElementById('kode_matkul');
const nama_matkul = document.getElementById('nama_matkul');
const sks = document.getElementById('sks');

function openAdd() {
    modal.classList.remove('hidden');
    modalTitle.textContent = 'Tambah Mata Kuliah';
    submitBtn.name = 'tambah';
    submitBtn.textContent = 'Simpan';
    kode_matkul.removeAttribute('readonly');
    kode_matkul.value = nama_matkul.value = sks.value = '';
}

function openEdit(kode, nama, sksVal) {
    modal.classList.remove('hidden');
    modalTitle.textContent = 'Edit Mata Kuliah';
    submitBtn.name = 'update';
    submitBtn.textContent = 'Update';
    kode_matkul.value = kode;
    kode_matkul.setAttribute('readonly', true);
    nama_matkul.value = nama;
    sks.value = sksVal;
}

function closeModal() {
    modal.classList.add('hidden');
}
</script>
</body>
</html>
