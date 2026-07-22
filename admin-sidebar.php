<?php

/**
 * File: admin-sidebar.php
 * Admin panel sidebar navigation with collapse toggle.
 */
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    die('Direct access not permitted.');
}
if (session_status() === PHP_SESSION_NONE) session_start();

$active_script = pathinfo(basename($_SERVER['SCRIPT_FILENAME']), PATHINFO_FILENAME);

$pendingCount = 0;
$pendingPaymentsCount = 0;
try {
    if (isset($pdo)) {
        $pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM `affiliates` WHERE `status` = 'pending'")->fetchColumn();
        $pendingPaymentsCount = (int)$pdo->query("SELECT COUNT(*) FROM `withdraw` WHERE `status` = 'pending'")->fetchColumn();
    }
} catch (Exception $e) {}

function getSidebarClass($current, $targets)
{
    if (in_array($current, (array)$targets)) {
        return 'bg-indigo-50 text-indigo-700 border-l-[3px] border-indigo-600 font-bold';
    }
    return 'text-slate-600 hover:bg-gray-100 hover:text-slate-900';
}
?>

<style>
    #adminSidebar.collapsed {
        width: 72px;
    }

    #adminSidebar.collapsed .sidebar-label,
    #adminSidebar.collapsed .sidebar-section-label,
    #adminSidebar.collapsed .sidebar-brand-text {
        display: none;
    }

    #adminSidebar.collapsed .sidebar-nav-link {
        justify-content: center;
        padding-left: 0;
        padding-right: 0;
    }

    #adminSidebar.collapsed .sidebar-nav-link i {
        margin-right: 0;
    }

    /* Adjust navbar + main content when sidebar is collapsed */
    body.sidebar-collapsed #adminNavbar {
        left: 72px !important;
    }

    body.sidebar-collapsed #sidebarContent {
        margin-left: 72px !important;
    }

    /* On mobile, ignore collapsed state for navbar */
    @media (max-width: 1023px) {
        body.sidebar-collapsed #adminNavbar {
            left: 0 !important;
        }

        body.sidebar-collapsed #sidebarContent {
            margin-left: 0 !important;
        }

        #adminSidebar.collapsed {
            width: 260px;
        }

        #adminSidebar.collapsed .sidebar-label,
        #adminSidebar.collapsed .sidebar-section-label,
        #adminSidebar.collapsed .sidebar-brand-text {
            display: block;
        }

        #adminSidebar.collapsed .sidebar-nav-link {
            justify-content: flex-start;
            padding-left: 12px;
            padding-right: 12px;
        }
    }
</style>

<aside id="adminSidebar" class="fixed top-0 left-0 z-40 h-screen w-64 bg-white border-r border-gray-200 flex flex-col transition-all duration-300 lg:translate-x-0 -translate-x-full" data-sidebar>

    <!-- Header with logo + collapse toggle -->
    <div class="flex items-center justify-between px-4 h-16 border-b border-gray-200 flex-shrink-0">
        <a href="admin-dashboard" class="flex items-center gap-3 overflow-hidden">
            <img src="public/logo.png" alt="Logo" class="h-9 w-auto flex-shrink-0">

        </a>
        <button type="button" onclick="toggleSidebarCollapse()" class="hidden lg:flex items-center justify-center w-7 h-7 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition cursor-pointer flex-shrink-0" title="Collapse sidebar">
            <i class="fa-solid fa-angles-left text-xs sidebar-collapse-icon"></i>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

        <a href="admin-dashboard" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition-all <?= getSidebarClass($active_script, 'admin-dashboard') ?>">
            <i class="fa-solid fa-chart-pie text-base w-5 text-center flex-shrink-0"></i>
            <span class="sidebar-label">Dashboard</span>
        </a>

        <div class="sidebar-section-label pt-3 pb-1">
            <span class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Affiliates</span>
        </div>

        <a href="admin-affiliates" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition-all <?= ($active_script === 'admin-affiliates' && (!isset($_GET['tab']) || $_GET['tab'] === 'all')) ? 'bg-indigo-50 text-indigo-700 border-l-[3px] border-indigo-600 font-bold' : 'text-slate-600 hover:bg-gray-100 hover:text-slate-900' ?>">
            <i class="fa-solid fa-handshake text-base w-5 text-center flex-shrink-0"></i>
            <span class="sidebar-label">All Affiliates</span>
        </a>

        <a href="admin-affiliates?tab=pending" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition-all <?= ($active_script === 'admin-affiliates' && isset($_GET['tab']) && $_GET['tab'] === 'pending') ? 'bg-indigo-50 text-indigo-700 border-l-[3px] border-indigo-600 font-bold' : 'text-slate-600 hover:bg-gray-100 hover:text-slate-900' ?>">
            <i class="fa-solid fa-clock text-base w-5 text-center flex-shrink-0"></i>
            <span class="sidebar-label">Pending Affiliates</span>
            <?php if ($pendingCount > 0): ?>
                <span class="sidebar-label ml-auto bg-amber-100 text-amber-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none"><?= $pendingCount ?></span>
            <?php endif; ?>
        </a>

        <a href="admin-affiliates?tab=payments" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition-all <?= ($active_script === 'admin-affiliates' && isset($_GET['tab']) && $_GET['tab'] === 'payments') ? 'bg-indigo-50 text-indigo-700 border-l-[3px] border-indigo-600 font-bold' : 'text-slate-600 hover:bg-gray-100 hover:text-slate-900' ?>">
            <i class="fa-solid fa-credit-card text-base w-5 text-center flex-shrink-0"></i>
            <span class="sidebar-label">Affiliate Payments</span>
            <?php if ($pendingPaymentsCount > 0): ?>
                <span class="sidebar-label ml-auto bg-amber-100 text-amber-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none"><?= $pendingPaymentsCount ?></span>
            <?php endif; ?>
        </a>

        <div class="sidebar-section-label pt-3 pb-1">
            <span class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Analytics</span>
        </div>

        <a href="admin-reports" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition-all <?= getSidebarClass($active_script, 'admin-reports') ?>">
            <i class="fa-solid fa-chart-line text-base w-5 text-center flex-shrink-0"></i>
            <span class="sidebar-label">Affiliate Reports</span>
        </a>

        <a href="admin-clients" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition-all <?= getSidebarClass($active_script, ['admin-clients', 'admin-client-detail']) ?>">
            <i class="fa-solid fa-users text-base w-5 text-center flex-shrink-0"></i>
            <span class="sidebar-label">Customers</span>
        </a>

    </nav>

    <div class="border-t border-gray-200 p-3 flex-shrink-0">
        <a href="admin-logout" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] text-rose-600 hover:bg-rose-50 font-bold transition-all justify-center lg:justify-start">
            <i class="fa-solid fa-right-from-bracket text-base w-5 text-center flex-shrink-0"></i>
            <span class="sidebar-label">Logout</span>
        </a>
    </div>

</aside>

<script>
    function toggleSidebarCollapse() {
        const sidebar = document.getElementById('adminSidebar');
        const icon = document.querySelector('.sidebar-collapse-icon');
        sidebar.classList.toggle('collapsed');
        document.body.classList.toggle('sidebar-collapsed', sidebar.classList.contains('collapsed'));
        if (sidebar.classList.contains('collapsed')) {
            icon.classList.replace('fa-angles-left', 'fa-angles-right');
        } else {
            icon.classList.replace('fa-angles-right', 'fa-angles-left');
        }
    }

    // Mobile toggle — also managed here so navbar calls it
    function toggleAdminSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        sidebar.classList.toggle('-translate-x-full');
    }
</script>