<?php
require_once('../config/connect.php');
session_start();

if (isset($_SESSION['level'])) {
    if ($_SESSION["level"] == 'admin') {
        header('Location: ../pages/admin/index.php');
    }
    if ($_SESSION["level"] == 'dosen') {
        header('Location: ../pages/dosen/index.php');
    }
    if ($_SESSION["level"] == 'mahasiswa') {
        header('Location: ../pages/mahasiswa/index.php');
    }
    exit();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $cek_user = mysqli_query($conn, "select * from user where username = '$username'");
    $lihat_user = mysqli_fetch_assoc($cek_user);
    if ($lihat_user) {
        $_SESSION["username"] = $lihat_user["username"];
        $_SESSION["level"] = $lihat_user["level"];
        if (password_verify($password, $lihat_user["password"])) {
            if ($lihat_user["level"] == 'admin') {
                header('Location: ../pages/admin/index.php');
            }
            if ($lihat_user["level"] == 'dosen') {
                header('Location: ../pages/dosen/index.php');
            }
            if ($lihat_user["level"] == 'mahasiswa') {
                header('Location: ../pages/mahasiswa/index.php');
            }
        } else {
            $alert_gagal_login = 'Username atau password salah';
        }
    } else {
        echo "<script>alert('Username tidak ada.')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login | Sistem Informasi Akademik</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen bg-gradient-to-br from-blue-500 to-blue-400 flex items-center justify-center">

    <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
            Login Akademik
        </h2>

        <?php if (isset($alert_gagal_login)) : ?>
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                <?= $alert_gagal_login ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Username
                </label>
                <input type="text" name="username"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg
                    focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg
                    focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <button type="submit" name="login"
                class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold
                hover:bg-blue-700 transition">
                Login
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="./forgot_password.php"
                class="text-sm text-blue-600 hover:underline">
                Lupa password?
            </a>
        </div>

    </div>

</body>

</html>