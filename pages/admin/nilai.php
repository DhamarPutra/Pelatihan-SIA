<?php
require_once('../../config/connect.php');
session_start();

if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../../../../auth/logout.php");
    exit;
}

$query = "
    SELECT nilai.id, nilai.nim, mahasiswa.nama AS nama_mhs,
           nilai.kode_matkul, matkul.nama_matkul, nilai.nilai
    FROM nilai
    JOIN mahasiswa ON nilai.nim = mahasiswa.nim
    JOIN matkul ON nilai.kode_matkul = matkul.kode_matkul
";
$result = mysqli_query($conn, $query);

$mahasiswaResult = mysqli_query($conn, "SELECT nim, nama FROM mahasiswa");
$matkulResult = mysqli_query($conn, "SELECT kode_matkul, nama_matkul FROM matkul");

// TAMBAH
if (isset($_POST['tambah'])) {
    $nim = $_POST['nim'];
    $kode_matkul = $_POST['kode_matkul'];
    $nilai = $_POST['nilai'];

    $cek = "SELECT * FROM nilai WHERE nim='$nim' AND kode_matkul='$kode_matkul'";
    $result = mysqli_query($conn, $cek);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['msg'] = [
            'type' => 'error',
            'text' => 'Mahasiswa ini sudah memiliki nilai untuk mata kuliah tersebut.'
        ];
    } else {
        $query = "INSERT INTO nilai (nim, kode_matkul, nilai) VALUES ('$nim', '$kode_matkul', '$nilai')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = [
                'type' => 'success',
                'text' => 'Data nilai berhasil ditambahkan.'
            ];
        } else {
            $_SESSION['msg'] = [
                'type' => 'error',
                'text' => 'Gagal menambahkan nilai: ' . mysqli_error($conn)
            ];
        }
    }

    header("Location: nilai.php");
    exit();
}


// HAPUS
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM nilai WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = ['type' => 'success', 'text' => 'Data nilai berhasil dihapus.'];
    } else {
        $_SESSION['msg'] = ['type' => 'error', 'text' => 'Gagal menghapus nilai: ' . mysqli_error($conn)];
    }
    header("Location: nilai.php");
    exit();
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nim = $_POST['nim'];
    $kode_matkul = $_POST['kode_matkul'];
    $nilai = $_POST['nilai'];
    $query = "UPDATE nilai SET nim='$nim', kode_matkul='$kode_matkul', nilai='$nilai' WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = ['type' => 'success', 'text' => 'Data nilai berhasil diupdate.'];
    } else {
        $_SESSION['msg'] = ['type' => 'error', 'text' => 'Gagal mengupdate nilai: ' . mysqli_error($conn)];
    }
    header("Location: nilai.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <title>Data Nilai</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-6 bg-gray-100">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Data Nilai</h1>
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
                <th class="border p-2">NIM</th>
                <th class="border p-2">Nama Mahasiswa</th>
                <th class="border p-2">Kode Matkul</th>
                <th class="border p-2">Nama Matkul</th>
                <th class="border p-2">Nilai</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td class="border p-2"><?= htmlspecialchars($row['nim']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($row['nama_mhs']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($row['kode_matkul']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($row['nama_matkul']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($row['nilai']) ?></td>
                    <td class="border p-2 space-x-2">
                        <button onclick="openEdit('<?= $row['id'] ?>', '<?= $row['nim'] ?>', '<?= $row['kode_matkul'] ?>', '<?= $row['nilai'] ?>')" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                        <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus data?')" class="bg-red-600 text-white px-2 py-1 rounded">Hapus</a>
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
                <input type="hidden" name="id" id="id">

                <label class="block font-semibold mb-1">Mahasiswa</label>
                <select name="nim" id="nim" class="w-full border p-2 mb-4" required>
                    <option value="" disabled selected>Pilih Mahasiswa</option>
                    <?php while ($mhs = mysqli_fetch_assoc($mahasiswaResult)) : ?>
                        <option value="<?= htmlspecialchars($mhs['nim']) ?>"><?= htmlspecialchars($mhs['nim']) ?> - <?= htmlspecialchars($mhs['nama']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label class="block font-semibold mb-1">Mata Kuliah</label>
                <select name="kode_matkul" id="kode_matkul" class="w-full border p-2 mb-4" required>
                    <option value="" disabled selected>Pilih Mata Kuliah</option>
                    <?php while ($mk = mysqli_fetch_assoc($matkulResult)) : ?>
                        <option value="<?= htmlspecialchars($mk['kode_matkul']) ?>"><?= htmlspecialchars($mk['kode_matkul']) ?> - <?= htmlspecialchars($mk['nama_matkul']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label class="block font-semibold mb-1">Nilai</label>
                <input type="text" name="nilai" id="nilai" class="w-full border p-2 mb-4" placeholder="Nilai (misal: A, B+)" maxlength="2" required>

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
        const idInput = document.getElementById('id');
        const nimSelect = document.getElementById('nim');
        const kodeMatkulSelect = document.getElementById('kode_matkul');
        const nilaiInput = document.getElementById('nilai');

        function openAdd() {
            modal.classList.remove('hidden');
            modalTitle.textContent = 'Tambah Nilai';
            submitBtn.name = 'tambah';
            submitBtn.textContent = 'Simpan';
            idInput.value = '';
            nimSelect.value = '';
            kodeMatkulSelect.value = '';
            nilaiInput.value = '';
        }

        function openEdit(id, nim, kode_matkul, nilai) {
            modal.classList.remove('hidden');
            modalTitle.textContent = 'Edit Nilai';
            submitBtn.name = 'update';
            submitBtn.textContent = 'Update';
            idInput.value = id;
            nimSelect.value = nim;
            kodeMatkulSelect.value = kode_matkul;
            nilaiInput.value = nilai;
        }

        function closeModal() {
            modal.classList.add('hidden');
        }
    </script>
</body>

</html>