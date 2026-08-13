<?php

/**
 * File: index.php
 * Automated Intel Search Portal Engine Node — Identity Search AI
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

// Handle the inbound lookup initiation request safely
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_query = trim($_POST['search_query'] ?? '');

    // Dynamic Engine Routing Matrix Layer
    $selected_engine = 'linkedin'; // Default fallback target anchor destination

    if (!empty($search_query)) {
        $lowerQuery = strtolower($search_query);

        // Map all possible URL structural pattern traces to detect specific target engines
        if (strpos($lowerQuery, 'facebook.com') !== false || strpos($lowerQuery, 'fb.com') !== false) {
            $selected_engine = 'facebook';
        } elseif (strpos($lowerQuery, 'instagram.com') !== false || strpos($lowerQuery, 'ig.me') !== false) {
            $selected_engine = 'instagram';
        } elseif (strpos($lowerQuery, 'twitter.com') !== false || strpos($lowerQuery, 'x.com') !== false) {
            $selected_engine = 'twitter';
        } elseif (strpos($lowerQuery, 'tiktok.com') !== false) {
            $selected_engine = 'tiktok';
        } elseif (strpos($lowerQuery, 'truecaller.com') !== false) {
            $selected_engine = 'truecaller';
        }

        // Formats spaces to '+' and safely strips malicious URL injection segments
        $url_parameter = urlencode($search_query);
        $engine_parameter = urlencode($selected_engine);

        // Push the operator explicitly to the professional search engine query-string module
        header("Location: " . BASE_URL . "search?q=" . $url_parameter . "&engine=" . $engine_parameter);
        exit;
    } else {
        $error = "Please enter a search target or profile identifier to initiate an identity scan.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Identity Search AI - Digital Intelligence Tool</title>
    <meta name="title" content="Identity Search AI - Digital Intelligence Tool">
    <meta name="description" content="Identity Tracing tool that will analyze digital footprint of any person and generate intelligent report">
    <?php include 'head.php'; ?>
    <style>
        /* Override head.php forced light mode for dark home page */
        body {
            background-color: #020617 !important;
            color: #ffffff !important;
        }

        /* ====== Navbar Dark Mode Override (home page only) ====== */
        /* Transparent at top; scroll handler applies the dark background */
        #mainNavbar {
            background-color: transparent !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            border-color: transparent !important;
            box-shadow: none !important;
        }

        /* Nav links: make text white on dark nav */
        #mainNavbar a,
        #mainNavbar button,
        #mainNavbar span {
            color: #e2e8f0 !important;
        }
        #mainNavbar a:hover,
        #mainNavbar button:hover {
            color: #4fb3c9 !important;
        }
        /* Active page link stays brand teal */
        #mainNavbar a[style*="color: #128c7e"],
        #mainNavbar a.text-\\[\\#128c7e\\] {
            color: #4fb3c9 !important;
        }
        /* Icon colors */
        #mainNavbar i {
            color: #94a3b8 !important;
        }
        #mainNavbar a:hover i,
        #mainNavbar button:hover i {
            color: #4fb3c9 !important;
        }

        /* "Get Report" button override */
        #mainNavbar .bg-gray-100,
        #mainNavbar .bg-gray-200,
        #mainNavbar .hover\\:bg-gray-200:hover {
            background-color: rgba(30, 41, 59, 0.8) !important;
            border: 1px solid rgba(51, 65, 85, 0.6) !important;
        }
        #mainNavbar .bg-gray-100:hover {
            background-color: rgba(79, 179, 201, 0.15) !important;
            border-color: rgba(79, 179, 201, 0.4) !important;
        }

        /* Dropdown menu dark override */
        #userDropdownMenu {
            background-color: #0f172a !important;
            border-color: rgba(51, 65, 85, 0.6) !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5) !important;
        }
        #userDropdownMenu a {
            color: #cbd5e1 !important;
        }
        #userDropdownMenu a:hover {
            background-color: rgba(30, 41, 59, 0.8) !important;
            color: #4fb3c9 !important;
        }
        #userDropdownMenu hr {
            border-color: #1e293b !important;
        }

        /* Hamburger button */
        #mainNavbar .fa-bars {
            color: #e2e8f0 !important;
        }

        /* Logo treatment for dark background */
        #mainNavbar img[alt="Identity Search AI Logo"] {
            border-radius: 8px;
            filter: brightness(1.1);
            height: 100px !important;
            width: auto;
        }

        /* Dark scrollbar override */
        ::-webkit-scrollbar-track { background: #0f172a !important; }
        ::-webkit-scrollbar-thumb { background: #3a93a8 !important; }
        ::-webkit-scrollbar-thumb:hover { background: #4fb3c9 !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#4FB3C9] selection:text-white bg-slate-950 text-white relative font-sans overflow-x-hidden">

    <?php include 'navbar.php'; ?>

    <!-- Override navbar scroll behavior for dark home page -->
    <script>
        (function() {
            const nav = document.getElementById('mainNavbar');
            if (!nav) return;

            // Fix logo path — use relative path instead of BASE_URL
            const logoImg = nav.querySelector('img[alt="Identity Search AI Logo"]');
            if (logoImg) {
                logoImg.src = 'public/logo.png';
            }

            // Strip all light-mode classes immediately
            nav.classList.remove('bg-white', 'shadow-sm', 'border-b-gray-200', 'bg-emerald-50/60', 'backdrop-blur-3xl');
            nav.className = 'sticky top-0 z-50 transition-all duration-300 border-b';

            // Style the "Get Report" button with a cyan gradient
            const allLinks = nav.querySelectorAll('a');
            allLinks.forEach(function(link) {
                if (link.textContent.trim() === 'Get Report') {
                    link.style.cssText = 'background: linear-gradient(135deg, #4fb3c9, #2f7e8f) !important; color: #ffffff !important; border: none !important; padding: 10px 20px !important; border-radius: 12px !important; font-weight: 700 !important; box-shadow: 0 0 20px rgba(79, 179, 201, 0.3) !important; transition: all 0.3s !important;';
                    link.addEventListener('mouseenter', function() {
                        this.style.boxShadow = '0 0 30px rgba(79, 179, 201, 0.5)';
                        this.style.transform = 'translateY(-1px)';
                    });
                    link.addEventListener('mouseleave', function() {
                        this.style.boxShadow = '0 0 20px rgba(79, 179, 201, 0.3)';
                        this.style.transform = 'translateY(0)';
                    });
                }
            });

            // Replace the scroll handler
            window.onscroll = null;
            window.addEventListener('scroll', function() {
                if (window.scrollY > 20) {
                    nav.style.setProperty('background-color', 'rgba(2, 6, 23, 0.92)', 'important');
                    nav.style.setProperty('border-color', 'rgba(79, 179, 201, 0.15)', 'important');
                    nav.style.setProperty('box-shadow', '0 8px 32px rgba(79, 179, 201, 0.08)', 'important');
                    nav.style.setProperty('backdrop-filter', 'blur(20px)', 'important');
                    nav.style.setProperty('-webkit-backdrop-filter', 'blur(20px)', 'important');
                } else {
                    nav.style.setProperty('background-color', 'transparent', 'important');
                    nav.style.setProperty('border-color', 'transparent', 'important');
                    nav.style.setProperty('box-shadow', 'none', 'important');
                    nav.style.setProperty('backdrop-filter', 'none', 'important');
                    nav.style.setProperty('-webkit-backdrop-filter', 'none', 'important');
                }
            });
            // Set initial state (transparent at top)
            nav.style.setProperty('background-color', 'transparent', 'important');
            nav.style.setProperty('border-color', 'transparent', 'important');
            nav.style.setProperty('box-shadow', 'none', 'important');
            nav.style.setProperty('backdrop-filter', 'none', 'important');
            nav.style.setProperty('-webkit-backdrop-filter', 'none', 'important');
        })();
    </script>

    <!-- Deep Space glowing background -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-[#0B1F40]/50 rounded-full blur-[120px] mix-blend-screen animate-blob"></div>
        <div class="absolute top-[20%] right-[-10%] w-[40%] h-[40%] bg-[#4FB3C9]/15 rounded-full blur-[120px] mix-blend-screen animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-[-20%] left-[20%] w-[50%] h-[50%] bg-[#1E3A8A]/40 rounded-full blur-[120px] mix-blend-screen animate-blob animation-delay-4000"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0IiBoZWlnaHQ9IjQiPgo8cmVjdCB3aWR0aD0iNCIgaGVpZ2h0PSI0IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMDUiLz4KPC9zdmc+')] opacity-10"></div>
    </div>

    <!-- Floating Particles Canvas -->
    <canvas id="particleCanvas" class="fixed inset-0 -z-5 pointer-events-none" style="z-index: -5;"></canvas>

    <style>
        /* ====== Keyframes ====== */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: blob 15s infinite alternate; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(79,179,201,0.2), 0 0 60px rgba(79,179,201,0.05); }
            50% { box-shadow: 0 0 30px rgba(79,179,201,0.4), 0 0 80px rgba(79,179,201,0.1); }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes floatY {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        @keyframes gradientRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes countUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes borderGlow {
            0%, 100% { border-color: rgba(79,179,201,0.3); }
            50% { border-color: rgba(79,179,201,0.5); }
        }

        /* ====== Animation Classes ====== */
        .anim-fade-up {
            opacity: 0;
            transform: translateY(40px);
        }
        .anim-fade-up.visible {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .anim-fade-scale {
            opacity: 0;
            transform: scale(0.9);
        }
        .anim-fade-scale.visible {
            animation: fadeInScale 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .anim-delay-1 { animation-delay: 0.1s !important; }
        .anim-delay-2 { animation-delay: 0.2s !important; }
        .anim-delay-3 { animation-delay: 0.3s !important; }
        .anim-delay-4 { animation-delay: 0.4s !important; }
        .anim-delay-5 { animation-delay: 0.5s !important; }
        .anim-delay-6 { animation-delay: 0.6s !important; }

        .search-glow { animation: pulseGlow 3s ease-in-out infinite; }

        .shimmer-text {
            background: linear-gradient(90deg, #e2e8f0 25%, #4fb3c9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmer 3s infinite;
        }

        .float-animation { animation: floatY 4s ease-in-out infinite; }
        .float-delay-1 { animation-delay: 0.5s; }
        .float-delay-2 { animation-delay: 1s; }
        .float-delay-3 { animation-delay: 1.5s; }

        /* Animated gradient border for search bar */
        .gradient-border-spin {
            position: relative;
            overflow: hidden;
            border: none !important;
        }
        .gradient-border-spin::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 1.5rem;
            padding: 2px;
            background: conic-gradient(from var(--border-angle, 0deg), #4fb3c9, #2f7e8f, #0b1f40, #4fb3c9);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0.5;
            transition: opacity 0.3s;
            pointer-events: none;
        }
        .gradient-border-spin:hover::before {
            opacity: 1;
        }

        /* Feature card animated border */
        .card-glow-border {
            position: relative;
            overflow: hidden;
        }
        .card-glow-border::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, transparent 40%, rgba(79,179,201,0.3) 50%, transparent 60%);
            background-size: 300% 300%;
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            animation: shimmer 4s ease-in-out infinite;
        }

        /* Stats counter animation */
        .stat-number {
            font-variant-numeric: tabular-nums;
        }

        /* Trusted brand strip */
        .trust-strip {
            animation: borderGlow 3s ease-in-out infinite;
        }
    </style>

    <main class="relative flex-grow w-full mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16 flex flex-col items-center justify-center" style="max-width: 1400px; min-height: 85vh;">
        
        <div class="text-center w-full max-w-4xl mx-auto z-10 mt-10">
            <!-- Badge -->
            <div class="anim-fade-up anim-delay-1 inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-slate-900/60 border border-slate-700/50 backdrop-blur-md mb-8 shadow-[0_0_15px_rgba(79,179,201,0.15)]">
                <span class="relative flex h-2.5 w-2.5">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#4FB3C9] opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#3A93A8]"></span>
                </span>
                <span class="text-xs font-bold bg-clip-text text-transparent bg-gradient-to-r from-[#8FD8E8] to-[#2F7E8F] tracking-wider uppercase">Next-Gen Identity Intelligence</span>
            </div>

            <!-- Headline -->
            <h1 class="anim-fade-up anim-delay-2 text-5xl sm:text-6xl lg:text-7xl font-black text-white tracking-tight mb-6 leading-tight">
                Uncover the Truth <br/>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#8FD8E8] via-[#4FB3C9] to-[#2F7E8F] drop-shadow-[0_0_30px_rgba(79,179,201,0.3)]">Hidden Online</span>
            </h1>

            <p class="anim-fade-up anim-delay-3 mt-6 text-base sm:text-lg lg:text-xl text-slate-300 font-medium max-w-2xl mx-auto mb-12 leading-relaxed">
                Our advanced AI engine deep-scans the digital footprint of any person, generating intelligent, comprehensive identity reports in seconds.
            </p>

            <?php if (!empty($error)): ?>
                <div class="mb-8 max-w-xl mx-auto p-4 bg-red-950/50 border border-red-500/50 rounded-2xl flex items-center gap-3 text-left backdrop-blur-sm shadow-[0_0_20px_rgba(239,68,68,0.2)]">
                    <i class="fa-solid fa-circle-exclamation text-red-400 text-lg"></i>
                    <p class="text-sm text-red-200 font-bold"><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>

            <!-- Search Form with animated border -->
            <div class="anim-fade-scale anim-delay-4 relative max-w-3xl mx-auto group mt-4">
                <div class="absolute -inset-1.5 bg-gradient-to-r from-[#4FB3C9] via-[#2F7E8F] to-[#0B1F40] rounded-3xl blur-md opacity-20 group-hover:opacity-60 transition duration-700 group-hover:duration-300 animate-pulse"></div>
                <form id="searchFormContainer" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="relative p-2 bg-slate-900/90 backdrop-blur-xl rounded-3xl border border-slate-700/50 flex flex-col sm:flex-row items-stretch gap-2 transition-all shadow-2xl search-glow gradient-border-spin">
                    <div class="flex flex-grow items-center gap-3 pl-5 py-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#4FB3C9]/20 to-[#0B1F40]/40 flex items-center justify-center border border-slate-700/50">
                            <i class="fa-solid fa-microchip text-[#4FB3C9] text-lg"></i>
                        </div>
                        <input
                            type="text"
                            name="search_query"
                            id="searchQueryInputField"
                            placeholder="Enter Name, Email, or Social Profile..."
                            class="w-full bg-transparent border-0 outline-none text-base lg:text-lg text-white font-medium py-3 focus:ring-0 placeholder:text-slate-500"
                            autocomplete="off"
                            required>
                    </div>

                    <button type="submit" id="submitScanButton" class="bg-gradient-to-r from-[#4FB3C9] to-[#2F7E8F] hover:from-[#8FD8E8] hover:to-[#3A93A8] text-white px-8 py-4 rounded-2xl text-base font-bold transition-all flex items-center justify-center gap-3 shadow-lg shadow-[#4FB3C9]/25 hover:shadow-[#4FB3C9]/50 active:scale-95 cursor-pointer sm:min-w-[180px] hover:scale-[1.02]">
                        <i class="fa-solid fa-radar text-lg" id="buttonIconNode"></i>
                        <span id="buttonTextNode">Initiate Scan</span>
                    </button>
                </form>
            </div>

            <!-- Trust Indicators -->
            <div class="anim-fade-up anim-delay-5 mt-12 flex flex-wrap items-center justify-center gap-x-8 gap-y-4 text-sm text-slate-400 font-medium">
                <div class="flex items-center gap-2.5 float-animation">
                    <div class="w-8 h-8 rounded-lg bg-[#0B1F40]/50 flex items-center justify-center border border-[#0B1F40]">
                        <i class="fa-solid fa-bolt text-[#8FD8E8] text-xs"></i>
                    </div>
                    <span>AI-Powered Analysis</span>
                </div>
                <div class="flex items-center gap-2.5 float-animation float-delay-1">
                    <div class="w-8 h-8 rounded-lg bg-[#4FB3C9]/10 flex items-center justify-center border border-[#4FB3C9]/25">
                        <i class="fa-solid fa-shield-halved text-[#4FB3C9] text-xs"></i>
                    </div>
                    <span>Military-Grade Encryption</span>
                </div>
                <div class="flex items-center gap-2.5 float-animation float-delay-2">
                    <div class="w-8 h-8 rounded-lg bg-[#2F7E8F]/15 flex items-center justify-center border border-[#2F7E8F]/30">
                        <i class="fa-solid fa-globe text-[#6FB6C8] text-xs"></i>
                    </div>
                    <span>50+ Networks Scanned</span>
                </div>
            </div>
        </div>
    </main>

    <!-- ====== Stats Counter Section ====== -->
    <section class="w-full relative z-10 py-10 border-y border-slate-800/50">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-8">
                <div class="anim-fade-up text-center p-6 rounded-2xl bg-slate-900/30 backdrop-blur-sm border border-slate-800/30 hover:border-[#4FB3C9]/40 transition-all duration-300">
                    <div class="stat-number text-3xl sm:text-4xl font-black text-white mb-1" data-target="50" data-suffix="+">0+</div>
                    <p class="text-xs sm:text-sm text-slate-400 font-semibold uppercase tracking-wider">Networks Scanned</p>
                </div>
                <div class="anim-fade-up text-center p-6 rounded-2xl bg-slate-900/30 backdrop-blur-sm border border-slate-800/30 hover:border-[#4FB3C9]/40 transition-all duration-300">
                    <div class="stat-number text-3xl sm:text-4xl font-black text-white mb-1" data-target="2" data-suffix="M+">0</div>
                    <p class="text-xs sm:text-sm text-slate-400 font-semibold uppercase tracking-wider">Reports Generated</p>
                </div>
                <div class="anim-fade-up text-center p-6 rounded-2xl bg-slate-900/30 backdrop-blur-sm border border-slate-800/30 hover:border-[#4FB3C9]/40 transition-all duration-300">
                    <div class="stat-number text-3xl sm:text-4xl font-black text-white mb-1" data-target="99" data-suffix="%">0%</div>
                    <p class="text-xs sm:text-sm text-slate-400 font-semibold uppercase tracking-wider">Accuracy Rate</p>
                </div>
                <div class="anim-fade-up text-center p-6 rounded-2xl bg-slate-900/30 backdrop-blur-sm border border-slate-800/30 hover:border-[#4FB3C9]/40 transition-all duration-300">
                    <div class="stat-number text-3xl sm:text-4xl font-black text-white mb-1" data-target="120" data-suffix="s">0s</div>
                    <p class="text-xs sm:text-sm text-slate-400 font-semibold uppercase tracking-wider">Avg. Scan Time</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ====== Features Section ====== -->
    <section class="w-full max-w-[1400px] mx-auto px-4 sm:px-6 py-20 relative z-10">
        <!-- Section Header -->
        <div class="text-center max-w-2xl mx-auto mb-14 anim-fade-up">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-900/50 border border-slate-700/50 backdrop-blur-md text-xs font-bold text-slate-300 tracking-wide shadow-sm mb-5">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#4FB3C9] opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#3A93A8]"></span>
                </span>
                Core Capabilities
            </div>
            <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight mb-4">
                Intelligence at Your <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#8FD8E8] to-[#4FB3C9]">Fingertips</span>
            </h2>
            <p class="text-slate-400 text-sm sm:text-base font-medium leading-relaxed">Powered by advanced algorithms that analyze, correlate, and compile digital intelligence from across the open web.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            <!-- Card 1 -->
            <div class="anim-fade-up anim-delay-1 group relative bg-slate-900/50 backdrop-blur-md p-8 rounded-3xl border border-slate-700/50 hover:border-[#4FB3C9]/50 transition-all duration-500 hover:shadow-[0_0_40px_rgba(79,179,201,0.15)] hover:-translate-y-3 card-glow-border">
                <div class="absolute top-0 right-0 w-40 h-40 origin-top-right bg-gradient-to-bl from-[#4FB3C9]/10 to-transparent rounded-bl-[100px] scale-0 group-hover:scale-[6] transition-transform duration-700 ease-in-out"></div>
                <div class="w-16 h-16 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center text-[#4FB3C9] text-2xl mb-6 shadow-lg border border-slate-700 group-hover:bg-gradient-to-br group-hover:from-[#4FB3C9] group-hover:to-[#2F7E8F] group-hover:text-white group-hover:scale-110 transition-all duration-500 group-hover:shadow-[0_0_30px_rgba(79,179,201,0.3)]">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3 group-hover:text-[#8FD8E8] transition-colors">Identity Resolution</h3>
                <p class="text-slate-400 text-sm leading-relaxed mb-5">
                    Connect the dots between disconnected online profiles to reveal the true digital identity behind any footprint.
                </p>
                <div class="flex items-center gap-2 text-[#4FB3C9] text-sm font-semibold opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-y-2 group-hover:translate-y-0">
                    <span>Learn more</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="anim-fade-up anim-delay-3 group relative bg-slate-900/50 backdrop-blur-md p-8 rounded-3xl border border-slate-700/50 hover:border-[#0B1F40]/80 transition-all duration-500 hover:shadow-[0_0_40px_rgba(11,31,64,0.6)] hover:-translate-y-3 card-glow-border">
                <div class="absolute top-0 right-0 w-40 h-40 origin-top-right bg-gradient-to-bl from-[#0B1F40]/70 to-transparent rounded-bl-[100px] scale-0 group-hover:scale-[6] transition-transform duration-700 ease-in-out"></div>
                <div class="w-16 h-16 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center text-[#8FD8E8] text-2xl mb-6 shadow-lg border border-slate-700 group-hover:bg-gradient-to-br group-hover:from-[#0B1F40] group-hover:to-[#1E3A8A] group-hover:text-white group-hover:scale-110 transition-all duration-500 group-hover:shadow-[0_0_30px_rgba(11,31,64,0.5)]">
                    <i class="fa-solid fa-network-wired"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3 group-hover:text-[#8FD8E8] transition-colors">Deep Network Trace</h3>
                <p class="text-slate-400 text-sm leading-relaxed mb-5">
                    We scour deep web sources, professional networks, and social media platforms simultaneously for comprehensive results.
                </p>
                <div class="flex items-center gap-2 text-[#8FD8E8] text-sm font-semibold opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-y-2 group-hover:translate-y-0">
                    <span>Learn more</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="anim-fade-up anim-delay-5 group relative bg-slate-900/50 backdrop-blur-md p-8 rounded-3xl border border-slate-700/50 hover:border-[#2F7E8F]/50 transition-all duration-500 hover:shadow-[0_0_40px_rgba(47,126,143,0.3)] hover:-translate-y-3 card-glow-border">
                <div class="absolute top-0 right-0 w-40 h-40 origin-top-right bg-gradient-to-bl from-[#2F7E8F]/20 to-transparent rounded-bl-[100px] scale-0 group-hover:scale-[6] transition-transform duration-700 ease-in-out"></div>
                <div class="w-16 h-16 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center text-[#6FB6C8] text-2xl mb-6 shadow-lg border border-slate-700 group-hover:bg-gradient-to-br group-hover:from-[#2F7E8F] group-hover:to-[#1E3A8A] group-hover:text-white group-hover:scale-110 transition-all duration-500 group-hover:shadow-[0_0_30px_rgba(47,126,143,0.4)]">
                    <i class="fa-solid fa-file-shield"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3 group-hover:text-[#8FD8E8] transition-colors">Actionable Intelligence</h3>
                <p class="text-slate-400 text-sm leading-relaxed mb-5">
                    Generate beautiful, structured reports detailing risk factors, linked accounts, and behavioral analysis.
                </p>
                <div class="flex items-center gap-2 text-[#6FB6C8] text-sm font-semibold opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-y-2 group-hover:translate-y-0">
                    <span>Learn more</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- ====== How It Works Section ====== -->
    <section class="w-full max-w-[1400px] mx-auto px-4 sm:px-6 py-20 relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-14 anim-fade-up">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-900/50 border border-slate-700/50 backdrop-blur-md text-xs font-bold text-slate-300 tracking-wide shadow-sm mb-5">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#4FB3C9] opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#3A93A8]"></span>
                </span>
                Simple Process
            </div>
            <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight mb-4">
                How It <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#4FB3C9] to-[#1E3A8A]">Works</span>
            </h2>
            <p class="text-slate-400 text-sm sm:text-base font-medium leading-relaxed">Three simple steps to uncover any digital identity.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 relative">
            <!-- Connecting line (desktop only) -->
            <div class="hidden md:block absolute top-20 left-[20%] right-[20%] h-px bg-gradient-to-r from-[#4FB3C9]/30 via-[#0B1F40]/70 to-[#2F7E8F]/30"></div>

            <!-- Step 1 -->
            <div class="anim-fade-up anim-delay-1 text-center relative">
                <div class="w-16 h-16 mx-auto bg-gradient-to-br from-[#4FB3C9] to-[#3A93A8] rounded-2xl flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-[#4FB3C9]/25 float-animation relative z-10">
                    <i class="fa-solid fa-keyboard"></i>
                </div>
                <div class="absolute top-2 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-slate-950 border-2 border-[#4FB3C9] flex items-center justify-center text-[#4FB3C9] text-xs font-black z-20 -mt-1 ml-8">1</div>
                <h3 class="text-lg font-bold text-white mb-2">Enter Identity</h3>
                <p class="text-slate-400 text-sm leading-relaxed max-w-xs mx-auto">Type a name, email, phone number, or social media profile URL into the search bar.</p>
            </div>

            <!-- Step 2 -->
            <div class="anim-fade-up anim-delay-3 text-center relative">
                <div class="w-16 h-16 mx-auto bg-gradient-to-br from-[#0B1F40] to-[#1E3A8A] rounded-2xl flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-[#0B1F40]/60 float-animation float-delay-1 relative z-10">
                    <i class="fa-solid fa-microchip"></i>
                </div>
                <div class="absolute top-2 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-slate-950 border-2 border-[#0B1F40] flex items-center justify-center text-[#8FD8E8] text-xs font-black z-20 -mt-1 ml-8">2</div>
                <h3 class="text-lg font-bold text-white mb-2">AI Analyzes</h3>
                <p class="text-slate-400 text-sm leading-relaxed max-w-xs mx-auto">Our engine scans 50+ networks, cross-references data points, and builds a comprehensive profile.</p>
            </div>

            <!-- Step 3 -->
            <div class="anim-fade-up anim-delay-5 text-center relative">
                <div class="w-16 h-16 mx-auto bg-gradient-to-br from-[#2F7E8F] to-[#1E3A8A] rounded-2xl flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-[#2F7E8F]/40 float-animation float-delay-2 relative z-10">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <div class="absolute top-2 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-slate-950 border-2 border-[#2F7E8F] flex items-center justify-center text-[#6FB6C8] text-xs font-black z-20 -mt-1 ml-8">3</div>
                <h3 class="text-lg font-bold text-white mb-2">Get Report</h3>
                <p class="text-slate-400 text-sm leading-relaxed max-w-xs mx-auto">Receive a detailed, structured intelligence report with all discovered digital footprints.</p>
            </div>
        </div>
    </section>

    <?php if (file_exists('index_faq.php')) { include 'index_faq.php'; } ?>
    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

    <script>
        // ====== Floating Particles System ======
        (function() {
            const canvas = document.getElementById('particleCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let particles = [];
            const PARTICLE_COUNT = 60;

            function resize() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            }
            resize();
            window.addEventListener('resize', resize);

            class Particle {
                constructor() {
                    this.reset();
                }
                reset() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.size = Math.random() * 2 + 0.5;
                    this.speedX = (Math.random() - 0.5) * 0.4;
                    this.speedY = (Math.random() - 0.5) * 0.4;
                    this.opacity = Math.random() * 0.5 + 0.1;
                    this.color = ['#4fb3c9', '#2f7e8f', '#0b1f40'][Math.floor(Math.random() * 3)];
                }
                update() {
                    this.x += this.speedX;
                    this.y += this.speedY;
                    if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
                    if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
                }
                draw() {
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fillStyle = this.color;
                    ctx.globalAlpha = this.opacity;
                    ctx.fill();
                    ctx.globalAlpha = 1;
                }
            }

            for (let i = 0; i < PARTICLE_COUNT; i++) {
                particles.push(new Particle());
            }

            function connectParticles() {
                for (let i = 0; i < particles.length; i++) {
                    for (let j = i + 1; j < particles.length; j++) {
                        const dx = particles[i].x - particles[j].x;
                        const dy = particles[i].y - particles[j].y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < 120) {
                            ctx.beginPath();
                            ctx.strokeStyle = '#4fb3c9';
                            ctx.globalAlpha = 0.05 * (1 - dist / 120);
                            ctx.lineWidth = 0.5;
                            ctx.moveTo(particles[i].x, particles[i].y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            ctx.stroke();
                            ctx.globalAlpha = 1;
                        }
                    }
                }
            }

            function animateParticles() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                particles.forEach(p => { p.update(); p.draw(); });
                connectParticles();
                requestAnimationFrame(animateParticles);
            }
            animateParticles();
        })();

        // ====== Scroll Reveal Observer ======
        (function() {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

            document.querySelectorAll('.anim-fade-up, .anim-fade-scale').forEach(function(el) {
                observer.observe(el);
            });
        })();

        // ====== Stats Counter Animation ======
        (function() {
            const counters = document.querySelectorAll('.stat-number[data-target]');
            let counted = false;
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting && !counted) {
                        counted = true;
                        counters.forEach(function(counter) {
                            const target = parseInt(counter.getAttribute('data-target'));
                            const suffix = counter.getAttribute('data-suffix') || '';
                            let current = 0;
                            const step = Math.max(1, Math.floor(target / 60));
                            const timer = setInterval(function() {
                                current += step;
                                if (current >= target) {
                                    current = target;
                                    clearInterval(timer);
                                }
                                counter.textContent = current + suffix;
                            }, 30);
                        });
                    }
                });
            }, { threshold: 0.3 });
            counters.forEach(function(c) { observer.observe(c); });
        })();

        // ====== FAQ Accordion ======
        document.addEventListener("DOMContentLoaded", () => {
            const container = document.getElementById("faqAccordionContainer");
            if (!container) return;

            container.addEventListener("click", (e) => {
                const trigger = e.target.closest(".faq-toggle-trigger");
                if (!trigger) return;

                const item = trigger.parentElement;
                const panel = item.querySelector(".faq-content-slider");
                const icon = trigger.querySelector(".fa-chevron-down");

                if (panel.style.maxHeight === "0px" || panel.style.maxHeight === "") {
                    container.querySelectorAll(".faq-content-slider").forEach((other) => {
                        other.style.maxHeight = "0px";
                        other.style.opacity = "0";
                    });
                    container.querySelectorAll(".fa-chevron-down").forEach((other) => {
                        other.style.transform = "rotate(0deg)";
                    });
                    container.querySelectorAll(".active").forEach((other) => {
                        other.classList.remove("active");
                    });

                    panel.style.maxHeight = panel.scrollHeight + "px";
                    panel.style.opacity = "1";
                    icon.style.transform = "rotate(180deg)";
                    item.classList.add("active");
                } else {
                    panel.style.maxHeight = "0px";
                    panel.style.opacity = "0";
                    icon.style.transform = "rotate(0deg)";
                    item.classList.remove("active");
                }
            });
        });

        // ====== Form Submit Handler ======
        document.getElementById('searchFormContainer').addEventListener('submit', function(e) {
            const inputField = document.getElementById('searchQueryInputField');
            if (inputField && inputField.value.trim() !== "") {
                const btn = document.getElementById('submitScanButton');
                const iconNode = document.getElementById('buttonIconNode');
                const textNode = document.getElementById('buttonTextNode');

                if (btn && iconNode && textNode) {
                    btn.style.pointerEvents = 'none';
                    iconNode.className = 'fa-solid fa-circle-notch animate-spin text-lg';
                    textNode.textContent = 'Analyzing...';
                }
            }
        });

        // ====== Smart Typewriter Effect for Placeholder ======
        (function() {
            const inputEl = document.getElementById('searchQueryInputField');
            if (!inputEl) return;

            const defaultPlaceholder = "Enter Name, Email, or Social Profile...";
            let namesPool = [
                "john.doe@gmail.com", "linkedin.com/in/johndoe", "Jane Smith",
                "twitter.com/janesmith", "michael.jones@company.com", "David Miller"
            ];

            for (let i = namesPool.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [namesPool[i], namesPool[j]] = [namesPool[j], namesPool[i]];
            }

            let typeTimeout, phaseTimeout;
            let isUserInteracting = false;
            let currentNameIndex = 0;

            function runTypewriterCycle() {
                if (isUserInteracting) return;
                const selectedName = namesPool[currentNameIndex];
                let currentText = "";
                let letterIndex = 0;

                function typeLetter() {
                    if (isUserInteracting) return;
                    if (letterIndex < selectedName.length) {
                        currentText += selectedName.charAt(letterIndex);
                        inputEl.placeholder = currentText;
                        letterIndex++;
                        typeTimeout = setTimeout(typeLetter, 100); 
                    } else {
                        phaseTimeout = setTimeout(() => {
                            if (isUserInteracting) return;
                            inputEl.placeholder = defaultPlaceholder;
                            currentNameIndex = (currentNameIndex + 1) % namesPool.length;
                            phaseTimeout = setTimeout(runTypewriterCycle, 2000);
                        }, 2500);
                    }
                }
                typeLetter();
            }

            phaseTimeout = setTimeout(runTypewriterCycle, 1500);

            function stopHintAnimation() {
                isUserInteracting = true;
                clearTimeout(typeTimeout);
                clearTimeout(phaseTimeout);
                inputEl.placeholder = defaultPlaceholder;
            }

            inputEl.addEventListener('focus', stopHintAnimation);
            inputEl.addEventListener('input', stopHintAnimation);
        })();
    </script>
</body>
</html>