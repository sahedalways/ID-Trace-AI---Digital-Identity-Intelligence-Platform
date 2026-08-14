<?php
/**
 * Identity Search AI — Customer Support Page
 * File: support.php
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "signin");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customer Support | Identity Search AI</title>
    <?php include 'head.php'; ?>
    <style>
        .manager-card {
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .profile-ring {
            padding: 5px;
            background: linear-gradient(to right, #0072bc, #4aa8e6);
            border-radius: 2rem;
        }
        .contact-link {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .contact-link:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: #0072bc;
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="text-slate-200 bg-slate-950">

    <?php include 'navbar.php'; ?>

    <div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
        <div class="manager-card w-full max-w-md rounded-[3rem] p-8 md:p-12 text-center">

            <div class="relative inline-block mb-8">
                <div class="profile-ring">
                    <div class="w-32 h-32 rounded-[1.7rem] bg-gradient-to-br from-[#0072bc] to-blue-400 flex items-center justify-center border-4 border-[#0f172a]">
                        <i class="fa-solid fa-headset text-5xl text-white"></i>
                    </div>
                </div>
                <div class="absolute bottom-1 right-1 w-6 h-6 bg-emerald-500 border-4 border-[#1e293b] rounded-full"></div>
            </div>

            <div class="mb-10">
                <h1 class="text-3xl font-black text-white tracking-tight mb-2">Customer Support</h1>
                <p class="text-[#0072bc] font-bold text-xs uppercase tracking-[0.2em]">Identity Search AI</p>
                <div class="flex items-center justify-center gap-2 mt-4">
                    <span class="h-1 w-1 rounded-full bg-slate-500"></span>
                    <p class="text-slate-400 text-sm italic">"We're here to help you, anytime"</p>
                    <span class="h-1 w-1 rounded-full bg-slate-500"></span>
                </div>
            </div>

            <div class="space-y-4">
                <a href="https://t.me/identitysearchai" target="_blank" rel="noopener"
                   class="contact-link flex items-center justify-between p-5 rounded-2xl group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-[#0072bc]/10 text-[#0072bc] group-hover:bg-[#0072bc] group-hover:text-white transition-all">
                            <i class="fa-brands fa-telegram text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold text-slate-500 uppercase">Telegram</p>
                            <p class="text-white font-semibold">@identitysearchai</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-arrow-up-right-from-square text-slate-600 group-hover:text-[#0072bc] transition-colors"></i>
                </a>

                <a href="mailto:support@identitysearch.ai"
                   class="contact-link flex items-center justify-between p-5 rounded-2xl group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-500/10 text-slate-400 group-hover:bg-white group-hover:text-slate-900 transition-all">
                            <i class="fa-solid fa-envelope text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold text-slate-500 uppercase">Email</p>
                            <p class="text-white font-semibold">support@identitysearch.ai</p>
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
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Response</p>
                </div>
                <div class="text-center">
                    <p class="text-white font-black">Friendly</p>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Team</p>
                </div>
            </div>

        </div>
    </div>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

</body>
</html>
