<?php
require_once('../config/connect.php');
session_start();

/* ================= RESET PASSWORD ================= */
if (isset($_POST['reset'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // cek username valid
    $cek = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");

    if (mysqli_num_rows($cek) == 1) {
        $query = "UPDATE user SET password='$password' WHERE username='$username'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = [
                'type' => 'success',
                'text' => 'Password berhasil direset. Silakan login.'
            ];
        } else {
            $_SESSION['msg'] = [
                'type' => 'error',
                'text' => 'Gagal mereset password.'
            ];
        }
    } else {
        $_SESSION['msg'] = [
            'type' => 'error',
            'text' => 'Username tidak ditemukan.'
        ];
    }

    header("Location: forgot_password.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100">

    <div class="bg-white p-6 rounded shadow w-96">
        <h1 class="text-2xl font-bold text-center mb-4">Forgot Password</h1>

        <!-- MESSAGE -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="mb-4 px-4 py-2 rounded
        <?= $_SESSION['msg']['type'] == 'success'
                ? 'bg-green-200 text-green-800'
                : 'bg-red-200 text-red-800' ?>">
                <?= $_SESSION['msg']['text'] ?>
            </div>
        <?php unset($_SESSION['msg']);
        endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="block mb-1 font-semibold">Username</label>
                <input type="text" name="username"
                    class="w-full border p-2 rounded"
                    placeholder="Masukkan username"
                    required>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-semibold">Password Baru</label>
                <input type="password" name="password"
                    class="w-full border p-2 rounded"
                    placeholder="Password baru"
                    required>
            </div>

            <button type="submit" name="reset"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Reset Password
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="login.php" class="text-blue-600 hover:underline">
                Kembali ke Login
            </a>
        </div>
    </div>

</body>
<