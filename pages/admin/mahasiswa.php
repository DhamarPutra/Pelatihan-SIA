<?php
require_once('../../config/connect.php');
session_start();

if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../../../../auth/logout.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM mahasiswa");

// TAMBAH
if (isset($_POST["tambah"])) {
    $nim = $_POST["nim"];
    $nama = $_POST["nama"];
    $prodi = $_POST["prodi"];
    $angkatan = $_POST["angkatan"];
    $query = "INSERT INTO mahasiswa (nim, nama, prodi, angkatan) VALUES ('$nim', '$nama', '$prodi', '$angkatan')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = ['type'=>'success','text'=>'Data mahasiswa berhasil ditambahkan.'];
    } else {
        $_SESSION['msg'] = ['type'=>'error','text'=>'Gagal menambahkan mahasiswa: '.mysqli_error($conn)];
    }
    header("Location: mahasiswa.php");
    exit();
}

// HAPUS
if (isset($_GET["hapus"])) {
    $nim = $_GET["hapus"];
    $query = "DELETE FROM mahasiswa WHERE nim='$nim'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = ['type'=>'success','text'=>'Data mahasiswa berhasil dihapus.'];
    } else {
        $_SESSION['msg'] = ['type'=>'error','text'=>'Gagal menghapus mahasiswa: '.mysqli_error($conn)];
    }
    header("Location: mahasiswa.php");
    exit();
}

// UPDATE
if (isset($_POST["update"])) {
    $nim = $_POST["nim"];
    $nama = $_POST["nama"];
    $prodi = $_POST["prodi"];
    $angkatan = $_POST["angkatan"];
    $query = "UPDATE mahasiswa SET nama='$nama', prodi='$prodi', angkatan='$angkatan' WHERE nim='$nim'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = ['type'=>'success','text'=>'Data mahasiswa berhasil diupdate.'];
    } else {
        $_SESSION['msg'] = ['type'=>'error','text'=>'Gagal mengupdate mahasiswa: '.mysqli_error($conn)];
    }
    header("Location: mahasiswa.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Data Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 bg-gray-100">

<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Data Mahasiswa</h1>
    <button onclick="openAdd()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah</button>
</div>

<!-- PESAN CRUD -->
<?php if(isset($_SESSION['msg'])): ?>
    <div class="mb-4 px-4 py-2 rounded <?= $_SESSION['msg']['type']=='success' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' ?>">
        <?= $_SESSION['msg']['text'] ?>
    </div>
    <?php unset($_SESSION['msg']); ?>
<?php endif; ?>

<table class="w-full bg-white rounded shadow border">
<thead class="bg-gray-200">
<tr>
<th class="border p-2">NIM</th>
<th class="border p-2">Nama</th>
<th class="border p-2">Prodi</th>
<th class="border p-2">Angkatan</th>
<th class="border p-2">Aksi</th>
</tr>
</thead>
<tbody>
<?php while($row=mysqli_fetch_assoc($result)): ?>
<tr>
<td class="border p-2"><?= htmlspecialchars($row['nim']) ?></td>
<td class="border p-2"><?= htmlspecialchars($row['nama']) ?></td>
<td class="border p-2"><?= htmlspecialchars($row['prodi']) ?></td>
<td class="border p-2"><?= htmlspecialchars($row['angkatan']) ?></td>
<td class="border p-2 space-x-2">
    <button onclick="openEdit('<?= $row['nim'] ?>','<?= $row['nama'] ?>','<?= $row['prodi'] ?>','<?= $row['angkatan'] ?>')" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
    <a href="?hapus=<?= $row['nim'] ?>" onclick="return confirm('Hapus data?')" class="bg-red-600 text-white px-2 py-1 rounded">Hapus</a>
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
    <input type="text" name="nim" id="nim" class="w-full border p-2 mb-2" placeholder="NIM" required>
    <input type="text" name="nama" id="nama" class="w-full border p-2 mb-2" placeholder="Nama" required>
    <input type="text" name="prodi" id="prodi" class="w-full border p-2 mb-2" placeholder="Prodi" required>
    <input type="number" name="angkatan" id="angkatan" class="w-full border p-2 mb-4" placeholder="Angkatan" required>
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
const nimInput = document.getElementById('nim');
const namaInput = document.getElementById('nama');
const prodiInput = document.getElementById('prodi');
const angkatanInput = document.getElementById('angkatan');

function openAdd(){
    modal.classList.remove('hidden');
    modalTitle.textContent='Tambah Mahasiswa';
    submitBtn.name='tambah';
    submitBtn.textContent='Simpan';
    nimInput.removeAttribute('readonly');
    nimInput.value=namaInput.value=prodiInput.value=angkatanInput.value='';
}

function openEdit(nim,nama,prodi,angkatan){
    modal.classList.remove('hidden');
    modalTitle.textContent='Edit Mahasiswa';
    submitBtn.name='update';
    submitBtn.textContent='Update';
    nimInput.value=nim;
    nimInput.setAttribute('readonly',true);
    namaInput.value=nama;
    prodiInput.value=prodi;
    angkatanInput.value=angkatan;
}

function closeModal(){
    modal.classList.add('hidden');
}
</script>
</body>
</html>
