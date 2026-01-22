<?php
/**
 * User Management Page
 * 
 * Helper for managing users (CRUD).
 * Supports adding, editing, and deleting users (Admin, Dosen, Mahasiswa).
 * 
 * @package Admin
 */

require_once('../../config/connect.php');
session_start();

if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../../../../auth/logout.php");
    exit;
}

/**
 * @var mysqli_result $result Result set of all users
 */
$result = mysqli_query($conn, "SELECT * FROM user");

/* ================= TAMBAH ================= */
if (isset($_POST["tambah"])) {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $level    = $_POST["level"];

    $query = "INSERT INTO user (username, password, level)
              VALUES ('$username','$password','$level')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = ['type' => 'success', 'text' => 'User berhasil ditambahkan'];
    } else {
        $_SESSION['msg'] = ['type' => 'error', 'text' => 'Gagal menambahkan user'];
    }
    header("Location: user.php");
    exit();
}

/* ================= HAPUS ================= */
if (isset($_GET["hapus"])) {
    $id = intval($_GET["hapus"]);

    if (mysqli_query($conn, "DELETE FROM user WHERE id=$id")) {
        $_SESSION['msg'] = ['type' => 'success', 'text' => 'User berhasil dihapus'];
    } else {
        $_SESSION['msg'] = ['type' => 'error', 'text' => 'Gagal menghapus user'];
    }
    header("Location: user.php");
    exit();
}

/* ================= UPDATE ================= */
if (isset($_POST["update"])) {
    $id       = intval($_POST["id"]);
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $level    = $_POST["level"];

    if (!empty($_POST["password"])) {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $query = "UPDATE user SET
                  username='$username',
                  password='$password',
                  level='$level'
                  WHERE id=$id";
    } else {
        $query = "UPDATE user SET
                  username='$username',
                  level='$level'
                  WHERE id=$id";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = ['type' => 'success', 'text' => 'User berhasil diupdate'];
    } else {
        $_SESSION['msg'] = ['type' => 'error', 'text' => 'Gagal update user'];
    }
    header("Location: user.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <title>Data User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-6 bg-gray-100">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Data User</h1>
        <button onclick="openAdd()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah
        </button>
    </div>

    <!-- PESAN CRUD -->
    <?php if (isset($_SESSION['msg'])): ?>
        <div class="mb-4 px-4 py-2 rounded
<?= $_SESSION['msg']['type'] == 'success'
            ? 'bg-green-200 text-green-800'
            : 'bg-red-200 text-red-800' ?>">
            <?= $_SESSION['msg']['text'] ?>
        </div>
    <?php unset($_SESSION['msg']);
    endif; ?>

    <table class="w-full bg-white rounded shadow border">
        <thead class="bg-gray-200">
            <tr>
                <th class="border p-2">Username</th>
                <th class="border p-2">Level</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td class="border p-2"><?= htmlspecialchars($row['username']) ?></td>
                    <td class="border p-2"><?= $row['level'] ?></td>
                    <td class="border p-2 space-x-2">
                        <button
                            onclick="openEdit(
                '<?= $row['id'] ?>',
                '<?= $row['username'] ?>',
                '<?= $row['level'] ?>'
            )"
                            class="bg-yellow-500 text-white px-2 py-1 rounded">
                            Edit
                        </button>

                        <a href="?hapus=<?= $row['id'] ?>"
                            onclick="return confirm('Hapus user?')"
                            class="bg-red-600 text-white px-2 py-1 rounded">
                            Hapus
                        </a>
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

                <input type="text" name="username" id="username"
                    class="w-full border p-2 mb-2"
                    placeholder="Username" required>

                <input type="password" name="password" id="password"
                    class="w-full border p-2 mb-2"
                    placeholder="Password">

                <select name="level" id="level"
                    class="w-full border p-2 mb-4" required>
                    <option value="">-- Pilih Level --</option>
                    <option value="admin">Admin</option>
                    <option value="dosen">Dosen</option>
                    <option value="mahasiswa">Mahasiswa</option>
                </select>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-gray-300 rounded">
                        Batal
                    </button>
                    <button type="submit" id="submitBtn"
                        class="px-4 py-2 bg-blue-600 text-white rounded">
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modal');
        const modalTitle = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('submitBtn');

        const idInput = document.getElementById('id');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const levelInput = document.getElementById('level');

        function openAdd() {
            modal.classList.remove('hidden');
            modalTitle.textContent = 'Tambah User';
            submitBtn.name = 'tambah';
            submitBtn.textContent = 'Simpan';

            idInput.value = '';
            usernameInput.value = '';
            passwordInput.value = '';
            levelInput.value = '';
        }

        function openEdit(id, username, level) {
            modal.classList.remove('hidden');
            modalTitle.textContent = 'Edit User';
            submitBtn.name = 'update';
            submitBtn.textContent = 'Update';

            idInput.value = id;
            usernameInput.value = username;
            levelInput.value = level;
            passwordInput.value = '';
        }

        function closeModal() {
            modal.classList.add('hidden');
        }
    </script>

</body>

</html>