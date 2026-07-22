<?php
/**
 * File: affiliate-am.php
 * Affiliate Manager profile & contact card page.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['affiliate_id'])) {
    header("Location: affiliate-login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Account Manager | Identity Search AI</title>
    <?php include 'affiliate-head.php'; ?>
    <style>
        .manager-card {
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .profile-ring {
            padding: 5px;
            background: linear-gradient(to right, #128c7e, #34d399);
            border-radius: 2rem;
        }
        .contact-link {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .contact-link:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: #128c7e;
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="text-slate-200 bg-slate-950">

    <?php include 'affiliate-navbar.php'; ?>

    <div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
        <div class="manager-card w-full max-w-md rounded-[3rem] p-8 md:p-12 text-center">

            <div class="relative inline-block mb-8">
                <div class="profile-ring">
                    <img src="https://i.postimg.cc/BZP7DJSG/IMG-20260425-172607.jpg"
                         alt="James Smith"
                         class="w-32 h-32 rounded-[1.7rem] object-cover border-4 border-[#0f172a]">
                </div>
                <div class="absolute bottom-1 right-1 w-6 h-6 bg-emerald-500 border-4 border-[#1e293b] rounded-full"></div>
            </div>

            <div class="mb-10">
                <h1 class="text-3xl font-black text-white tracking-tight mb-2">James Smith</h1>
                <p class="text-[#128c7e] font-bold text-xs uppercase tracking-[0.2em]">Affiliate Manager</p>
                <div class="flex items-center justify-center gap-2 mt-4">
                    <span class="h-1 w-1 rounded-full bg-slate-500"></span>
                    <p class="text-slate-400 text-sm italic">"Let's scale your traffic together"</p>
                    <span class="h-1 w-1 rounded-full bg-slate-500"></span>
                </div>
            </div>

            <div class="space-y-4">
                <a href="https://t.me/identitysearchai" target="_blank"
                   class="contact-link flex items-center justify-between p-5 rounded-2xl group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-[#128c7e]/10 text-[#128c7e] group-hover:bg-[#128c7e] group-hover:text-white transition-all">
                            <i class="fa-brands fa-telegram text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold text-slate-500 uppercase">Telegram</p>
                            <p class="text-white font-semibold">@identitysearchai</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-600 group-hover:text-[#128c7e] transition-colors"></i>
                </a>

                <a href="mailto:smith@identitysearch.ai"
                   class="contact-link flex items-center justify-between p-5 rounded-2xl group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-500/10 text-slate-400 group-hover:bg-white group-hover:text-slate-900 transition-all">
                            <i class="fa-solid fa-envelope text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold text-slate-500 uppercase">Email</p>
                            <p class="text-white font-semibold">smith@identitysearch.ai</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-600 group-hover:text-white transition-colors"></i>
                </a>
            </div>

            <div class="mt-10 pt-8 border-t border-white/5 flex justify-around">
                <div class="text-center">
                    <p class="text-white font-black">24/7</p>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Support</p>
                </div>
                <div class="text-center">
                    <p class="text-white font-black">Fast</p>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Payouts</p>
                </div>
                <div class="text-center">
                    <p class="text-white font-black">High</p>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Caps</p>
                </div>
            </div>

        </div>
    </div>

    <footer class="w-full bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400 font-semibold">
        <div class="flex items-center justify-center gap-2 mb-2">
            <img src="public/logo.png" alt="Identity Search AI Logo" class="h-12 w-auto">
        </div>
        &copy; <?= date('Y'); ?> Identity Search AI Affiliate Portal. All rights reserved.
    </footer>

</body>
</html>