<?php

/**
 * File: affiliate-portal.php
 * Landing Index Homepage for the Identity Search AI Affiliate Network.
 * Markets network value propositions and routes users to authentication vectors.
 * Verified layout synchronized directly with global system UI dimensions.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$portal_bonus_amount = getGlobalBonusAmount($pdo);
$portal_bonus_type = getGlobalBonusType($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Affiliate Partner Program — PartnerTerminal</title>
    <?php include 'affiliate-head.php'; ?>
</head>

<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-between selection:bg-[#0072bc] selection:text-white bg-slate-50 relative">

    <!-- Sticky Glassmorphic Navbar Container -->
    <header id="mainNavbar" class="sticky top-0 z-50 bg-transparent transition-all duration-300">
        <?php include 'affiliate-navbar.php'; ?>
    </header>
    <!-- Full-width Background Decorations -->
    <div class="absolute inset-x-0 top-0 -z-10 overflow-hidden" style="height: 900px; background: linear-gradient(180deg, #BFE4FD 0%, #FFFFFF 100%);">
        <div class="blob-1 absolute top-0 left-1/2 w-[900px] h-[900px] bg-[#0072bc]/10 rounded-full blur-3xl opacity-60 -translate-x-1/2 will-change-transform"></div>
        <div class="blob-2 absolute top-24 -left-20 w-96 h-96 bg-[#0072bc]/15 rounded-full blur-3xl will-change-transform"></div>
        <div class="blob-3 absolute bottom-0 right-0 w-96 h-96 bg-[#BFE4FD]/50 rounded-full blur-3xl opacity-70 will-change-transform"></div>
    </div>

    <style>
        @keyframes blobMove1 {

            0%,
            100% {
                transform: translateX(-50%) translateY(0);
            }

            25% {
                transform: translateX(-40%) translateY(-20px);
            }

            50% {
                transform: translateX(-50%) translateY(-10px);
            }

            75% {
                transform: translateX(-60%) translateY(10px);
            }
        }

        @keyframes blobMove2 {

            0%,
            100% {
                transform: translate(0, 0);
            }

            33% {
                transform: translate(30px, -15px);
            }

            66% {
                transform: translate(-20px, 20px);
            }
        }

        @keyframes blobMove3 {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(-40px, -20px);
            }
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-15px);
            }
            100% {
                transform: translateY(0px);
            }
        }

        .float-anim {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .onload-anim {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }
        .onload-delay-100 { animation-delay: 100ms; }
        .onload-delay-200 { animation-delay: 200ms; }
        .onload-delay-300 { animation-delay: 300ms; }

        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
            will-change: opacity, transform;
        }
        .animate-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .scroll-delay-100 { transition-delay: 100ms; }
        .scroll-delay-200 { transition-delay: 200ms; }

        @keyframes spark-sweep {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }
        .spark-text {
            background: linear-gradient(
                135deg,
                #020617 35%,
                #2563eb 50%,
                #020617 65%
            );
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
            animation: spark-sweep 5s linear infinite;
        }

        .blob-1 {
            animation: blobMove1 18s ease-in-out infinite;
        }

        .blob-2 {
            animation: blobMove2 14s ease-in-out infinite;
        }

        .blob-3 {
            animation: blobMove3 16s ease-in-out infinite;
        }

        .navbar-scrolled {
            background: linear-gradient(to right, #eef5ff 20%, #0072bc 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* Smooth color transitions for all navbar links, buttons, and icons */
        #affiliateNavbar a,
        #affiliateNavbar button,
        #affiliateNavbar .text-slate-800,
        #affiliateNavbar .text-slate-900,
        #affiliateNavbar .text-slate-700,
        #affiliateNavbar .text-slate-600,
        #affiliateNavbar .text-slate-500,
        #affiliateNavbar .text-rose-600 {
            transition: color 0.35s ease, background-color 0.35s ease, border-color 0.35s ease;
        }

        /* When scrolled, switch navbar text and icons to white */
        #mainNavbar.navbar-scrolled #affiliateNavbar a:not(#mobileAffiliateDrawer a),
        #mainNavbar.navbar-scrolled #affiliateNavbar button:not(#mobileAffiliateDrawer button),
        #mainNavbar.navbar-scrolled #affiliateNavbar .text-slate-800:not(#mobileAffiliateDrawer .text-slate-800),
        #mainNavbar.navbar-scrolled #affiliateNavbar .text-slate-900:not(#mobileAffiliateDrawer .text-slate-900),
        #mainNavbar.navbar-scrolled #affiliateNavbar .text-slate-700:not(#mobileAffiliateDrawer .text-slate-700),
        #mainNavbar.navbar-scrolled #affiliateNavbar .text-slate-600:not(#mobileAffiliateDrawer .text-slate-600),
        #mainNavbar.navbar-scrolled #affiliateNavbar .text-slate-500:not(#mobileAffiliateDrawer .text-slate-500),
        #mainNavbar.navbar-scrolled #affiliateNavbar .text-rose-600:not(#mobileAffiliateDrawer .text-rose-600) {
            color: #ffffff !important;
        }

        /* Adjust hover states when scrolled for better contrast on dark gradient */
        #mainNavbar.navbar-scrolled #affiliateNavbar a:hover:not(#mobileAffiliateDrawer a),
        #mainNavbar.navbar-scrolled #affiliateNavbar button:hover:not(#mobileAffiliateDrawer button, #affiliateMobileMenuButton) {
            background-color: rgba(255, 255, 255, 0.12);
            color: #ffffff !important;
        }

        /* Mobile hamburger: keep icon white and subtle glass highlight when scrolled */
        #mainNavbar.navbar-scrolled #affiliateNavbar #affiliateMobileMenuButton {
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.25);
        }

        #mainNavbar.navbar-scrolled #affiliateNavbar #affiliateMobileMenuButton:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff !important;
        }

        /* Adjust mobile hamburger border when scrolled */
        #mainNavbar.navbar-scrolled #affiliateNavbar .border-blue-200\/60 {
            border-color: rgba(255, 255, 255, 0.25);
        }

        /* Adjust separator color when scrolled */
        #mainNavbar.navbar-scrolled #affiliateNavbar .bg-blue-300 {
            background-color: rgba(255, 255, 255, 0.3);
        }
    </style>

    <main class="relative w-full grow">

        <!-- HERO VALUE STATEMENT SECTION -->
        <section class="w-full mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-16 md:pb-10" style="max-width: 1600px;">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-10 xl:gap-24 items-center">

                <!-- Left Content -->
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#0072bc]/10 backdrop-blur-md border border-[#0072bc]/20 text-xs font-semibold text-[#0072bc] tracking-wide shadow-lg shadow-[#0072bc]/10 hover:-translate-y-0.5 hover:shadow-[#0072bc]/25 transition-all duration-300 onload-anim">
                        <span class="w-2 h-2 rounded-full bg-[#0072bc] shadow-[0_0_8px_rgba(0,114,188,0.9)] animate-pulse"></span>
                        Mega Commission Launched
                    </div>

                    <h1 class="mt-7 text-3xl sm:text-4xl lg:text-[2.75rem] xl:text-[3.25rem] font-black tracking-tight max-w-3xl leading-[1.08] mx-auto lg:mx-0 onload-anim onload-delay-100">
                        <span class="spark-text">Send Customers.<br>Claim 50% Recurring Cash.</span>
                    </h1>

                    <p class="mt-6 text-sm sm:text-base lg:text-lg text-black font-semibold max-w-2xl leading-relaxed mx-auto lg:mx-0 onload-anim onload-delay-200">
                        Join the official Identity Search AI partner ecosystem. Monetize traffic streams by routing users into our next-gen predictive OSINT data intelligence funnels.
                    </p>

                    <!-- Feature Highlights -->
                    <div class="mt-9 flex flex-wrap justify-center lg:justify-start gap-2 sm:gap-3 onload-anim onload-delay-300">
                        <div class="inline-flex items-center gap-2 sm:gap-2.5 px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl sm:rounded-2xl bg-white/80 backdrop-blur border border-white/60 shadow-sm hover:shadow-md hover:bg-white hover:-translate-y-0.5 transition-all duration-300 text-[11px] sm:text-sm font-bold text-gray-800 cursor-default ring-1 ring-black/5">
                            <i class="fa-solid fa-percent text-[#0072bc] text-xs sm:text-base"></i>
                            50% Recurring Commission
                        </div>
                        <div class="inline-flex items-center gap-2 sm:gap-2.5 px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl sm:rounded-2xl bg-white/80 backdrop-blur border border-white/60 shadow-sm hover:shadow-md hover:bg-white hover:-translate-y-0.5 transition-all duration-300 text-[11px] sm:text-sm font-bold text-gray-800 cursor-default ring-1 ring-black/5">
                            <i class="fa-solid fa-clock text-[#0072bc] text-xs sm:text-base"></i>
                            Real-Time Tracking
                        </div>
                        <div class="inline-flex items-center gap-2 sm:gap-2.5 px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl sm:rounded-2xl bg-white/80 backdrop-blur border border-white/60 shadow-sm hover:shadow-md hover:bg-white hover:-translate-y-0.5 transition-all duration-300 text-[11px] sm:text-sm font-bold text-gray-800 cursor-default ring-1 ring-black/5">
                            <i class="fa-solid fa-wallet text-[#0072bc] text-xs sm:text-base"></i>
                            Instant Payouts
                        </div>
                    </div>

                    <div class="mt-9 max-w-2xl mx-auto lg:mx-0 flex flex-col sm:flex-row items-stretch gap-3 onload-anim onload-delay-300">
                        <a href="affiliate-register" class="bg-gradient-to-r from-[#0072bc] to-blue-600 hover:from-blue-600 hover:to-blue-700 active:scale-[0.98] text-white px-8 py-4 rounded-full text-sm lg:text-base font-bold transition-all duration-300 flex items-center justify-center gap-2.5 shadow-md shadow-blue-200 hover:shadow-lg hover:shadow-blue-300 hover:-translate-y-0.5 cursor-pointer">
                            <i class="fa-solid fa-user-plus"></i> Become an affiliate
                        </a>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-center lg:justify-start gap-x-6 gap-y-3 text-xs lg:text-sm text-gray-700 font-bold onload-anim onload-delay-300">
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-blue-50 text-[#0072bc] border border-blue-100 flex items-center justify-center shrink-0 shadow-sm">
                                <i class="fa-solid fa-check text-[10px]"></i>
                            </div>
                            Free to join
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-blue-50 text-[#0072bc] border border-blue-100 flex items-center justify-center shrink-0 shadow-sm">
                                <i class="fa-solid fa-check text-[10px]"></i>
                            </div>
                            No minimum traffic
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-blue-50 text-[#0072bc] border border-blue-100 flex items-center justify-center shrink-0 shadow-sm">
                                <i class="fa-solid fa-check text-[10px]"></i>
                            </div>
                            Lifetime cookie tracking
                        </div>
                    </div>
                </div>

                <!-- Right Visual -->
                <div class="relative onload-anim onload-delay-200">
                    <div class="relative rounded-[20px] sm:rounded-[32px] border border-[#0072bc]/20 bg-white/90 backdrop-blur shadow-[0_25px_80px_rgba(0,114,188,0.10)] overflow-hidden">
                        <div class="flex items-center justify-between px-3 sm:px-6 lg:px-8 py-3 sm:py-5 border-b border-gray-100 bg-gradient-to-r from-[#BFE4FD]/50 to-white">
                            <div class="flex items-center gap-2.5 sm:gap-3.5">
                                <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-[#0072bc] text-white flex items-center justify-center shadow-sm text-xs sm:text-base">
                                    <i class="fa-solid fa-handshake"></i>
                                </div>
                                <div class="text-left">
                                    <p class="text-[11px] sm:text-sm font-bold text-gray-900">Affiliate Partner Program</p>
                                    <p class="text-[10px] sm:text-sm text-gray-500 font-medium">Earn 50% recurring commissions</p>
                                </div>
                            </div>
                            <div class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#0072bc]/10 border border-[#0072bc]/20 text-xs font-bold text-[#0072bc]">
                                <span class="w-2 h-2 rounded-full bg-[#0072bc] animate-pulse"></span>
                                Now Live
                            </div>
                        </div>
                        <div class="relative p-3 sm:p-5 lg:p-7">
                            <div class="relative rounded-[20px] sm:rounded-[28px] overflow-hidden bg-gradient-to-br from-[#BFE4FD]/50 via-white to-[#BFE4FD]/30 border border-[#0072bc]/20">
                                <img src="https://images.unsplash.com/photo-1559136555-9303baea8ebd?auto=format&fit=crop&w=1200&q=80" alt="Affiliate Partner Program" class="w-full h-[240px] sm:h-[340px] lg:h-[460px] xl:h-[500px] object-cover">
                                <div class="absolute inset-0 bg-black/30"></div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/10 to-transparent"></div>
                                <div class="absolute left-0 right-0 bottom-0 p-4 sm:p-7 lg:p-9 text-left">
                                    <div class="max-w-lg">
                                        <div class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full bg-white/90 backdrop-blur text-[10px] sm:text-xs font-bold text-[#0072bc] mb-2 sm:mb-4">
                                            <i class="fa-solid fa-gem"></i>
                                            Affiliate Network
                                        </div>
                                        <h3 class="text-sm sm:text-xl lg:text-2xl xl:text-3xl font-black text-white leading-tight">
                                            Earn 50% recurring commissions on every referral.
                                        </h3>
                                        <p class="mt-2 sm:mt-3 text-[10px] sm:text-xs lg:text-sm text-white/90 font-medium leading-relaxed">
                                            Promote Identity Search AI and get paid monthly for every customer you bring.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- HIGH-YIELD COMMISSION CHART MATRIX -->
        <section class="mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16" style="max-width: 1600px;">
            <div class="text-center mb-10">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#0072bc]/10 border border-[#0072bc]/20 text-xs font-bold text-[#0072bc] uppercase tracking-wider mb-4">
                    <i class="fa-solid fa-chart-simple"></i> Commission Plans
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Platform Product Commission Matrices</h2>
                <p class="mt-2 text-sm text-gray-500 font-medium">Choose a plan and earn 50% recurring commission on every referral</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- M1 Plan -->
                <div class="group relative bg-white p-6 sm:p-7 rounded-3xl border border-gray-100 shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-500 overflow-hidden">
                    <div class="absolute top-0 right-0 w-28 h-28 bg-gradient-to-bl from-[#BFE4FD]/40 to-transparent rounded-bl-[80px] opacity-70 group-hover:opacity-100 origin-top-right group-hover:scale-[6] transition-all duration-500 ease-in-out"></div>
                    <div class="relative p-1 space-y-5">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-bold font-mono text-sm">M1</span>
                            <span class="text-[10px] font-bold text-[#0072bc] bg-[#0072bc]/10 px-2 py-0.5 rounded-full uppercase tracking-wider">Best Value</span>
                        </div>
                        <div class="space-y-1">
                            <div class="text-3xl font-black text-gray-900">$36<span class="text-sm font-semibold text-gray-400">.00</span></div>
                            <div class="text-xs text-gray-500 font-medium">per month</div>
                        </div>
                        <ul class="space-y-2.5 text-xs font-semibold text-gray-600">
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-check-circle text-[#0072bc] text-sm"></i>
                                <span>3 Reports <span class="text-gray-400 font-medium">Billed Monthly</span></span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-check-circle text-[#0072bc] text-sm"></i>
                                <span>Full OSINT Report Access</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-check-circle text-[#0072bc] text-sm"></i>
                                <span>Email Support</span>
                            </li>
                        </ul>
                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400 font-medium">Your Payout</span>
                                <span class="text-lg font-black text-[#0072bc]">$<?= number_format($portal_bonus_amount, 2) ?> <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">/ <?= $portal_bonus_type === 'recursion' ? 'mo' : 'one-time' ?></span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Q3 Plan -->
                <div class="group relative bg-white p-6 sm:p-7 rounded-3xl border border-gray-100 shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-500 overflow-hidden">
                    <div class="absolute top-0 right-0 w-28 h-28 bg-gradient-to-bl from-[#BFE4FD]/40 to-transparent rounded-bl-[80px] opacity-70 group-hover:opacity-100 origin-top-right group-hover:scale-[6] transition-all duration-500 ease-in-out"></div>
                    <div class="relative p-1 space-y-5">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-bold font-mono text-sm">Q3</span>
                        </div>
                        <div class="space-y-1">
                            <div class="text-3xl font-black text-gray-900">$55<span class="text-sm font-semibold text-gray-400">.00</span></div>
                            <div class="text-xs text-gray-500 font-medium">per quarter</div>
                        </div>
                        <ul class="space-y-2.5 text-xs font-semibold text-gray-600">
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-check-circle text-[#0072bc] text-sm"></i>
                                <span>5 Reports + 2 <span class="text-blue-700 font-bold">Free</span></span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-check-circle text-[#0072bc] text-sm"></i>
                                <span>Priority OSINT Analysis</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-check-circle text-[#0072bc] text-sm"></i>
                                <span>Priority Email Support</span>
                            </li>
                        </ul>
                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400 font-medium">Your Payout</span>
                                <span class="text-lg font-black text-[#0072bc]">$<?= number_format($portal_bonus_amount, 2) ?> <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">/ <?= $portal_bonus_type === 'recursion' ? 'q' : 'one-time' ?></span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- B6 Plan -->
                <div class="group relative bg-white p-6 sm:p-7 rounded-3xl border border-gray-100 shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-500 overflow-hidden">
                    <div class="absolute top-0 right-0 w-28 h-28 bg-gradient-to-bl from-[#BFE4FD]/40 to-transparent rounded-bl-[80px] opacity-70 group-hover:opacity-100 origin-top-right group-hover:scale-[6] transition-all duration-500 ease-in-out"></div>
                    <div class="relative p-1 space-y-5">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-bold font-mono text-sm">B6</span>
                            <span class="text-[10px] font-bold text-[#0072bc] bg-[#0072bc]/10 px-2 py-0.5 rounded-full uppercase tracking-wider">Popular</span>
                        </div>
                        <div class="space-y-1">
                            <div class="text-3xl font-black text-gray-900">$72<span class="text-sm font-semibold text-gray-400">.00</span></div>
                            <div class="text-xs text-gray-500 font-medium">per 6 months</div>
                        </div>
                        <ul class="space-y-2.5 text-xs font-semibold text-gray-600">
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-check-circle text-[#0072bc] text-sm"></i>
                                <span>8 Reports + 3 <span class="text-blue-700 font-bold">Free</span></span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-check-circle text-[#0072bc] text-sm"></i>
                                <span>Advanced OSINT Analytics</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-check-circle text-[#0072bc] text-sm"></i>
                                <span>Priority Support + API</span>
                            </li>
                        </ul>
                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400 font-medium">Your Payout</span>
                                <span class="text-lg font-black text-[#0072bc]">$<?= number_format($portal_bonus_amount, 2) ?> <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">/ <?= $portal_bonus_type === 'recursion' ? '6mo' : 'one-time' ?></span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Y12 Plan -->
                <div class="group relative bg-white border-2 border-[#0072bc] rounded-3xl shadow-lg hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-500 overflow-hidden ring-1 ring-[#0072bc]/10">
                    <div class="absolute top-0 right-0 w-28 h-28 bg-gradient-to-bl from-[#BFE4FD]/50 to-transparent rounded-bl-[80px] opacity-70 group-hover:opacity-100 origin-top-right group-hover:scale-[6] transition-all duration-500 ease-in-out"></div>
                    <div class="absolute top-0 right-0">
                        <div class="bg-[#0072bc] text-white text-[9px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-wider">Best Deal</div>
                    </div>
                    <div class="relative p-6 sm:p-7 space-y-5">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-bold font-mono text-sm">Y12</span>
                        </div>
                        <div class="space-y-1">
                            <div class="text-3xl font-black text-gray-900">$96<span class="text-sm font-semibold text-gray-400">.00</span></div>
                            <div class="text-xs text-gray-500 font-medium">per year</div>
                        </div>
                        <ul class="space-y-2.5 text-xs font-semibold text-gray-600">
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-check-circle text-[#0072bc] text-sm"></i>
                                <span>12 Reports + 4 <span class="text-blue-700 font-bold">Free</span></span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-check-circle text-[#0072bc] text-sm"></i>
                                <span>Full Enterprise OSINT Suite</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="fa-solid fa-check-circle text-[#0072bc] text-sm"></i>
                                <span>Premium Support + API + SLA</span>
                            </li>
                        </ul>
                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400 font-medium">Your Payout</span>
                                <span class="text-lg font-black text-[#0072bc]">$<?= number_format($portal_bonus_amount, 2) ?> <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">/ <?= $portal_bonus_type === 'recursion' ? 'yr' : 'one-time' ?></span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- DYNAMIC FORECAST REVENUE CALCULATOR ENGINE -->
        <section class="mx-auto px-4 sm:px-6 lg:px-8 py-8" style="max-width: 1600px;">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">

                <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm ring-1 ring-gray-900/5 p-6 sm:p-7 space-y-6 text-left animate-on-scroll">
                    <div class="space-y-1">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-calculator text-[#0072bc]"></i> Estimated Monthly Earnings Forecaster
                        </h3>
                        <p class="text-[11px] text-gray-400 font-medium">Adjust volume parameters down below to forecast real-time cumulative downstream monthly commission loops.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold text-slate-700">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#0072bc]"></span> M1 Referrals (<span id="val_m1_count" class="font-bold">10</span>)</span>
                                <span class="font-mono font-bold text-gray-400">$36.00 Plan</span>
                            </div>
                            <input type="range" id="input_m1" min="0" max="250" value="10" oninput="calculateProjections()" class="w-full accent-[#0072bc] cursor-pointer h-1.5 bg-slate-100 rounded-lg appearance-none">
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold text-slate-700">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-sky-500"></span> Q3 Referrals (<span id="val_q3_count" class="font-bold">5</span>)</span>
                                <span class="font-mono font-bold text-gray-400">$55.00 Plan</span>
                            </div>
                            <input type="range" id="input_q3" min="0" max="250" value="5" oninput="calculateProjections()" class="w-full accent-[#0072bc] cursor-pointer h-1.5 bg-slate-100 rounded-lg appearance-none">
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold text-slate-700">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-400"></span> B6 Referrals (<span id="val_b6_count" class="font-bold">3</span>)</span>
                                <span class="font-mono font-bold text-gray-400">$72.00 Plan</span>
                            </div>
                            <input type="range" id="input_b6" min="0" max="250" value="3" oninput="calculateProjections()" class="w-full accent-[#0072bc] cursor-pointer h-1.5 bg-slate-100 rounded-lg appearance-none">
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold text-slate-700">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-600"></span> Y12 Referrals (<span id="val_y12_count" class="font-bold">2</span>)</span>
                                <span class="font-mono font-bold text-gray-400">$96.00 Plan</span>
                            </div>
                            <input type="range" id="input_y12" min="0" max="250" value="2" oninput="calculateProjections()" class="w-full accent-[#0072bc] cursor-pointer h-1.5 bg-slate-100 rounded-lg appearance-none">
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-slate-900 to-[#0072bc] text-white rounded-3xl p-6 sm:p-7 shadow-lg shadow-blue-900/20 ring-1 ring-black/5 flex flex-col justify-between border border-transparent text-left min-h-[220px] relative overflow-hidden animate-on-scroll scroll-delay-100">
                    <div class="absolute -right-6 -bottom-6 opacity-5 text-8xl font-bold font-mono select-none pointer-events-none">$$</div>

                    <div class="flex justify-between items-start">
                        <span class="text-[10px] font-bold text-[#BFE4FD] uppercase tracking-wider">Estimated Monthly Passive Revenue</span>
                        <div class="w-8 h-8 rounded-lg bg-white/10 text-[#BFE4FD] flex items-center justify-center text-sm backdrop-blur-xs"><i class="fa-solid fa-chart-line"></i></div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-white tracking-tight font-mono">$<span id="projected_sum">0.00</span></div>
                        <p class="text-[#BFE4FD]/60 text-[10px] leading-relaxed mt-2">Projection based on active baseline customer retention metrics, applying strict 50% monthly-normalized split values directly.</p>
                    </div>
                </div>

            </div>
        </section>

        <!-- VALUE ADVANTAGE LOGISTICS SECTION -->
        <section class="mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16" style="max-width: 1600px;">
            <div class="text-center mb-10">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#0072bc]/10 border border-[#0072bc]/20 text-xs font-bold text-[#0072bc] uppercase tracking-wider mb-4">
                    <i class="fa-solid fa-bolt"></i> Why Join Us
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Engineered for Rapid Conversions</h2>
                <p class="mt-2 text-sm text-gray-500 font-medium">Optimized traffic monetization attributes built for performance</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-sm text-slate-700">

                <!-- Card 1 -->
                <div class="animate-on-scroll">
                    <div class="group relative overflow-hidden rounded-3xl bg-white border border-gray-100 shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 hover:shadow-[0_22px_70px_rgba(0,0,0,0.08)] hover:-translate-y-1.5 transition-all duration-500 h-full">
                    <!-- Accent Bar -->
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#0072bc] via-blue-400 to-[#BFE4FD]"></div>

                    <!-- Number Badge -->
                    <div class="absolute top-5 right-5 text-5xl font-black text-blue-50 group-hover:text-[#BFE4FD]/60 transition-colors">
                        01
                    </div>

                    <div class="relative p-6 sm:p-7">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] flex items-center justify-center text-xl shadow-sm group-hover:bg-[#0072bc] group-hover:text-white group-hover:shadow-md group-hover:shadow-blue-200 transition-all duration-500 shrink-0">
                                <i class="fa-solid fa-bolt"></i>
                            </div>

                            <div class="min-w-0 pt-1">
                                <h4 class="font-black text-gray-900 text-base leading-snug">
                                    High-Converting Funnels
                                </h4>
                                <div class="mt-2 w-12 h-1 rounded-full bg-[#0072bc]/20"></div>
                            </div>
                        </div>

                        <p class="mt-5 text-sm text-gray-500 leading-relaxed font-semibold">
                            Our target search dossiers sell themselves. Route users onto optimized landing frames fine-tuned to squeeze maximum conversions from raw hits.
                        </p>

                        <div class="mt-6 flex items-center justify-between pt-5 border-t border-gray-100">
                            <span class="inline-flex items-center gap-2 text-xs font-bold text-[#0072bc]">
                                <span class="w-6 h-6 rounded-full bg-[#0072bc]/10 flex items-center justify-center">
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                </span>
                                Optimized traffic flow
                            </span>

                            <span class="w-9 h-9 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-[#0072bc] group-hover:border-blue-100 group-hover:bg-blue-50 transition-all">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </span>
                        </div>
                    </div>
                </div>
                </div>

                <!-- Card 2 -->
                <div class="animate-on-scroll scroll-delay-100">
                    <div class="group relative overflow-hidden rounded-3xl bg-white border border-gray-100 shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 hover:shadow-[0_22px_70px_rgba(0,0,0,0.08)] hover:-translate-y-1.5 transition-all duration-500 h-full">
                    <!-- Accent Bar -->
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#0072bc] via-blue-400 to-[#BFE4FD]"></div>

                    <!-- Number Badge -->
                    <div class="absolute top-5 right-5 text-5xl font-black text-blue-50 group-hover:text-[#BFE4FD]/60 transition-colors">
                        02
                    </div>

                    <div class="relative p-6 sm:p-7">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] flex items-center justify-center text-xl shadow-sm group-hover:bg-[#0072bc] group-hover:text-white group-hover:shadow-md group-hover:shadow-blue-200 transition-all duration-500 shrink-0">
                                <i class="fa-solid fa-chart-pie"></i>
                            </div>

                            <div class="min-w-0 pt-1">
                                <h4 class="font-black text-gray-900 text-base leading-snug">
                                    Real-Time S2S Telemetry
                                </h4>
                                <div class="mt-2 w-12 h-1 rounded-full bg-[#0072bc]/20"></div>
                            </div>
                        </div>

                        <p class="mt-5 text-sm text-gray-500 leading-relaxed font-semibold">
                            Track hits, dynamic sub-ids logs, payout matrix parameters changes, and full downstream postback integration responses live in your terminal dashboard layer.
                        </p>

                        <div class="mt-6 flex items-center justify-between pt-5 border-t border-gray-100">
                            <span class="inline-flex items-center gap-2 text-xs font-bold text-[#0072bc]">
                                <span class="w-6 h-6 rounded-full bg-[#0072bc]/10 flex items-center justify-center">
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                </span>
                                Live tracking system
                            </span>

                            <span class="w-9 h-9 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-[#0072bc] group-hover:border-blue-100 group-hover:bg-blue-50 transition-all">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </span>
                        </div>
                    </div>
                </div>
                </div>

                <!-- Card 3 -->
                <div class="animate-on-scroll scroll-delay-200">
                    <div class="group relative overflow-hidden rounded-3xl bg-white border border-gray-100 shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 hover:shadow-[0_22px_70px_rgba(0,0,0,0.08)] hover:-translate-y-1.5 transition-all duration-500 h-full">
                    <!-- Accent Bar -->
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#0072bc] via-blue-400 to-[#BFE4FD]"></div>

                    <!-- Number Badge -->
                    <div class="absolute top-5 right-5 text-5xl font-black text-blue-50 group-hover:text-[#BFE4FD]/60 transition-colors">
                        03
                    </div>

                    <div class="relative p-6 sm:p-7">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] flex items-center justify-center text-xl shadow-sm group-hover:bg-[#0072bc] group-hover:text-white group-hover:shadow-md group-hover:shadow-blue-200 transition-all duration-500 shrink-0">
                                <i class="fa-solid fa-sack-dollar"></i>
                            </div>

                            <div class="min-w-0 pt-1">
                                <h4 class="font-black text-gray-900 text-base leading-snug">
                                    Guaranteed Fast Payouts
                                </h4>
                                <div class="mt-2 w-12 h-1 rounded-full bg-[#0072bc]/20"></div>
                            </div>
                        </div>

                        <p class="mt-5 text-sm text-gray-500 leading-relaxed font-semibold">
                            No excessive processing delays or hidden escrow windows. Submit funding request balances straight into your verified Payoneer account nodes instantly.
                        </p>

                        <div class="mt-6 flex items-center justify-between pt-5 border-t border-gray-100">
                            <span class="inline-flex items-center gap-2 text-xs font-bold text-[#0072bc]">
                                <span class="w-6 h-6 rounded-full bg-[#0072bc]/10 flex items-center justify-center">
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                </span>
                                Faster payout access
                            </span>

                            <span class="w-9 h-9 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-[#0072bc] group-hover:border-blue-100 group-hover:bg-blue-50 transition-all">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </span>
                        </div>
                    </div>
                </div>
                </div>

            </div>
        </section>

    </main>

    <script>
        /**
         * Real-time monthly normalized projection calculator logic
         */
        function calculateProjections() {
            // Read active counts
            const m1 = parseInt(document.getElementById('input_m1').value) || 0;
            const q3 = parseInt(document.getElementById('input_q3').value) || 0;
            const b6 = parseInt(document.getElementById('input_b6').value) || 0;
            const y12 = parseInt(document.getElementById('input_y12').value) || 0;

            // Sync structural element numbers labels
            document.getElementById('val_m1_count').textContent = m1;
            document.getElementById('val_q3_count').textContent = q3;
            document.getElementById('val_b6_count').textContent = b6;
            document.getElementById('val_y12_count').textContent = y12;

            // Compute monthly normalized split value weights:
            const bonusAmount = <?= $portal_bonus_amount ?>;
            const monthlySumForecast = (m1 * bonusAmount) + (q3 * bonusAmount / 3) + (b6 * bonusAmount / 6) + (y12 * bonusAmount / 12);

            // Display formatted sum parameters inside layout context
            document.getElementById('projected_sum').textContent = monthlySumForecast.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Initialize calculation instantly upon DOM lifecycle ready state
        document.addEventListener('DOMContentLoaded', calculateProjections);
    </script>

    <!-- NAVBAR SCROLL GLASS EFFECT -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const navbar = document.getElementById("mainNavbar");
            if (!navbar) return;

            const handleScroll = () => {
                if (window.scrollY > 20) {
                    navbar.classList.add("navbar-scrolled");
                } else {
                    navbar.classList.remove("navbar-scrolled");
                }
            };

            window.addEventListener("scroll", handleScroll);
            handleScroll();
        });

        // INTERSECTION OBSERVER FOR SCROLL ANIMATIONS
        document.addEventListener("DOMContentLoaded", () => {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.15
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.animate-on-scroll').forEach((el) => {
                observer.observe(el);
            });
        });
    </script>

    <footer class="relative overflow-hidden w-full border-t border-gray-200 py-6 text-center text-xs text-gray-400 font-semibold">
        <div class="absolute inset-0 -z-10" style="background: linear-gradient(180deg, #ffffff 0%, #F0F8FF 40%, #EAF5FF 60%, #FFFFFF 100%);"></div>
        <div class="text-center">
            <div class="flex items-center justify-center gap-2 mb-2">
                <img src="<?php echo BASE_URL; ?>public/logo.png" alt="Identity Search AI Logo" class="h-12 w-auto">
            </div>
            &copy; 2026 Identity Search AI Affiliate Portal. All rights reserved.
        </div>
    </footer>

</body>

</html>