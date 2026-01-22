<?php
session_start();
if (
    !isset($_SESSION['username']) ||
    $_SESSION['level'] !== 'admin'
) {
    header("Location: ../../../../auth/logout.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen w-full flex bg-gray-100 overflow-hidden">

    <!-- SIDEBAR -->
    <aside id="sidebar"
        class="fixed md:relative inset-y-0 left-0 w-64 bg-gray-900 text-white
       transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-40 flex flex-col">

        <!-- HEADER -->
        <div class="p-6 border-b border-gray-700">
            <h1 class="text-2xl font-bold">Admin Panel</h1>
        </div>

        <!-- NAV UTAMA -->
        <div class="flex-1 p-4 space-y-2 overflow-y-auto">
            <button onclick="loadPage('dashboard.php')" class="nav-btn bg-gray-800">Dashboard</button>
            <button onclick="loadPage('mahasiswa.php')" class="nav-btn">Mahasiswa</button>
            <button onclick="loadPage('dosen.php')" class="nav-btn">Dosen</button>
            <button onclick="loadPage('mata_kuliah.php')" class="nav-btn">Mata Kuliah</button>
            <button onclick="loadPage('nilai.php')" class="nav-btn">Nilai</button>
            <button onclick="loadPage('user.php')" class="nav-btn">User</button>
        </div>

        <!-- LOGOUT -->
        <div class="p-4 border-t border-gray-700 mt-auto">
            <a href="../../auth/logout.php"
                class="block px-4 py-2 rounded text-red-400 hover:bg-red-600 hover:text-white">
                Logout
            </a>
        </div>
    </aside>

    <!-- OVERLAY -->
    <div id="overlay"
        class="hidden fixed inset-0 bg-black/50 z-30 md:hidden"
        onclick="toggleSidebar()">
    </div>

    <!-- MAIN WRAPPER -->
    <div class="flex flex-col flex-1 min-h-screen w-full">

        <!-- HEADER -->
        <header class="bg-white shadow h-14 flex items-center px-4 z-20">
            <button onclick="toggleSidebar()"
                class="md:hidden mr-4 text-gray-600 hover:text-gray-900">
                ☰
            </button>
            <h2 id="pageTitle" class="text-lg font-semibold">DASHBOARD</h2>
        </header>

        <!-- CONTENT -->
        <main class="flex-1 flex overflow-hidden">
            <iframe
                id="contentFrame"
                src="dashboard.php"
                class="flex-1 w-full h-full min-w-full min-h-full border-0">
            </iframe>
        </main>

    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const frame = document.getElementById('contentFrame');
        const title = document.getElementById('pageTitle');
        const buttons = document.querySelectorAll('.nav-btn');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function loadPage(url) {
            frame.src = url;

            title.innerText = url
                .replace('.php', '')
                .replace('.html', '')
                .replace('_', ' ')
                .toUpperCase();

            buttons.forEach(btn => btn.classList.remove('bg-gray-800'));
            event.target.classList.add('bg-gray-800');

            if (window.innerWidth < 768) {
                toggleSidebar();
            }
        }
    </script>

    <style>
        .nav-btn {
            width: 100%;
            text-align: left;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: background 0.2s;
        }

        .nav-btn:hover {
            background: #1f2937;
        }
    </style>

</body>

</html>