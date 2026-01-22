<?php
require_once('../../config/connect.php');
session_start();

if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../../../../auth/logout.php");
    exit;
}

// Query untuk hitung jumlah data di masing-masing tabel
$query_mahasiswa = "SELECT COUNT(*) AS total_mahasiswa FROM mahasiswa";
$query_dosen = "SELECT COUNT(*) AS total_dosen FROM dosen";
$query_matkul = "SELECT COUNT(*) AS total_matkul FROM matkul";

// Jalankan query
$result_mahasiswa = mysqli_query($conn, $query_mahasiswa);
$result_dosen = mysqli_query($conn, $query_dosen);
$result_matkul = mysqli_query($conn, $query_matkul);

// Ambil hasil
$total_mahasiswa = mysqli_fetch_assoc($result_mahasiswa)['total_mahasiswa'] ?? 0;
$total_dosen = mysqli_fetch_assoc($result_dosen)['total_dosen'] ?? 0;
$total_matkul = mysqli_fetch_assoc($result_matkul)['total_matkul'] ?? 0;

// Karena tidak ada tabel kelas, saya anggap jumlah kelas = jumlah mahasiswa
$total_kelas = $total_mahasiswa;

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">

    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="bg-white p-6 rounded shadow text-center">
            <h2 class="text-xl font-semibold mb-2">Mahasiswa</h2>
            <p class="text-4xl font-bold text-blue-600"><?= htmlspecialchars($total_mahasiswa) ?></p>
        </div>

        <div class="bg-white p-6 rounded shadow text-center">
            <h2 class="text-xl font-semibold mb-2">Dosen</h2>
            <p class="text-4xl font-bold text-green-600"><?= htmlspecialchars($total_dosen) ?></p>
        </div>

        <div class="bg-white p-6 rounded shadow text-center">
            <h2 class="text-xl font-semibold mb-2">Kelas</h2>
            <p class="text-4xl font-bold text-yellow-600"><?= htmlspecialchars($total_kelas) ?></p>
            <p class="text-sm text-gray-500">(Jumlah mahasiswa sebagai pengganti kelas)</p>
        </div>

        <div class="bg-white p-6 rounded shadow text-center">
            <h2 class="text-xl font-semibold mb-2">Mata Kuliah</h2>
            <p class="text-4xl font-bold text-red-600"><?= htmlspecialchars($total_matkul) ?></p>
        </div>

    </div>

</body>

</html>