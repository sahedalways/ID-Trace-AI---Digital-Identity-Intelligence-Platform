<?php

/**
 * File: admin-navbar.php
 * Admin panel top bar for sidebar layout.
 */
if (session_status() === PHP_SESSION_NONE) session_start();

$adminLoggedIn = isset($_SESSION['admin_id']);
$adminName = !empty($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';
$adminRole = !empty($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'admin';
?>

<nav id="adminNavbar" class="fixed top-0 right-0 z-30 left-0 lg:left-64 bg-white border-b border-gray-200 shadow-sm transition-all duration-300">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6">

        <button type="button" onclick="toggleAdminSidebar()" class="lg:hidden flex items-center justify-center p-2 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 cursor-pointer transition">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        <div class="flex-1"></div>

        <?php if ($adminLoggedIn): ?>
            <div class="relative">
                <button type="button" onclick="toggleAdminDropdown(event)" class="flex items-center gap-2 cursor-pointer hover:opacity-80 transition">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white shadow-md">
                        <i class="fa-solid fa-user-shield text-sm"></i>
                    </div>
                    <div class="text-right hidden sm:block">
                        <div class="text-xs font-bold text-gray-900"><?= htmlspecialchars($adminName) ?></div>
                        <div class="text-[10px] font-mono font-bold text-indigo-600 uppercase"><?= htmlspecialchars($adminRole) ?></div>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-gray-400"></i>
                </button>

                <div id="adminDropdown" class="hidden absolute right-0 mt-2 w-52 bg-white border border-gray-200 rounded-2xl shadow-xl py-2 z-50 text-left">
                    <div class="px-4 py-2 border-b border-gray-100">
                        <div class="text-xs font-bold text-gray-900"><?= htmlspecialchars($adminName) ?></div>
                        <div class="text-[10px] text-gray-400 font-mono"><?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?></div>
                    </div>
                    <a href="admin-logout.php" class="flex items-center gap-2.5 px-4 py-2.5 text-rose-600 hover:bg-rose-50 transition font-bold text-[13px]">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i> Logout
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</nav>

<script>
    function toggleAdminSidebar() {
        const sidebar = document.querySelector('[data-sidebar]');
        if (sidebar) sidebar.classList.toggle('-translate-x-full');
    }

    function toggleAdminDropdown(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('adminDropdown');
        if (dropdown) dropdown.classList.toggle('hidden');
    }

    window.addEventListener('click', function() {
        const dropdown = document.getElementById('adminDropdown');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
        }
    });
</script>
