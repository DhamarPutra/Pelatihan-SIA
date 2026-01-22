<?php
/**
 * Dosen Management Page
 * 
 * Helper for managing lecturers (CRUD).
 * Supports adding, editing, and deleting lecturer data.
 * 
 * @package Admin
 */

require_once('../../config/connect.php');
session_start();

if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../../../../auth/logout.php");
    exit;
}

// CRUD DOSEN
/**
 * @var mysqli_result $result Result set of all dosen
 */
$result = mysqli_query($conn, "SELECT * FROM dosen");

// TAMBAH
if (isset($_POST['tambah'])) {
    $nidn = $_POST['nidn'];
    $nama = $_POST['nama'];
    $query = "INSERT INTO dosen (nidn, nama) VALUES ('$nidn', '$nama')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = ['type' => 'success', 'text' => 'Data dosen berhasil ditambahkan.'];
    } else {
        $_SESSION['msg'] = ['type' => 'error', 'text' => 'Gagal menambahkan dosen: ' . mysqli_error($conn)];
    }
    header("Location: dosen.php");
    exit();
}

// HAPUS
if (isset($_GET['hapus'])) {
    $nidn = $_GET['hapus'];
    $query = "DELETE FROM dosen WHERE nidn='$nidn'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = ['type' => 'success', 'text' => 'Data dosen berhasil dihapus.'];
    } else {
        $_SESSION['msg'] = ['type' => 'error', 'text' => 'Gagal menghapus dosen: ' . mysqli_error($conn)];
    }
    header("Location: dosen.php");
    exit();
}

// UPDATE
if (isset($_POST['update'])) {
    $nidn = $_POST['nidn'];
    $nama = $_POST['nama'];
    $query = "UPDATE dosen SET nama='$nama' WHERE nidn='$nidn'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = ['type' => 'success', 'text' => 'Data dosen berhasil diupdate.'];
    } else {
        $_SESSION['msg'] = ['type' => 'error', 'text' => 'Gagal mengupdate dosen: ' . mysqli_error($conn)];
    }
    header("Location: dosen.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Data Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 bg-gray-100">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Data Dosen</h1>
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
                <th class="border p-2">NIDN</th>
                <th class="border p-2">Nama</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td class="border p-2"><?= htmlspecialchars($row['nidn']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($row['nama']) ?></td>
                    <td class="border p-2 space-x-2">
                        <button onclick="openEdit('<?= $row['nidn'] ?>', '<?= $row['nama'] ?>')" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                        <a href="?hapus=<?= $row['nidn'] ?>" onclick="return confirm('Hapus data?')" class="bg-red-600 text-white px-2 py-1 rounded">Hapus</a>
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
                <input type="text" name="nidn" id="nidn" class="w-full border p-2 mb-2" placeholder="NIDN" required>
                <input type="text" name="nama" id="nama" class="w-full border p-2 mb-4" placeholder="Nama" required>
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
        const nidn = document.getElementById('nidn');
        const nama = document.getElementById('nama');

        function openAdd() {
            modal.classList.remove('hidden');
            modalTitle.textContent = 'Tambah Dosen';
            submitBtn.name = 'tambah';
            submitBtn.textContent = 'Simpan';
            nidn.removeAttribute('readonly');
            nidn.value = nama.value = '';
        }

        function openEdit(n, nm) {
            modal.classList.remove('hidden');
            modalTitle.textContent = 'Edit Dosen';
            submitBtn.name = 'update';
            submitBtn.textContent = 'Update';
            nidn.value = n;
            nidn.setAttribute('readonly', true);
            nama.value = nm;
        }

        function closeModal() {
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>
