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
</head>

<body class="min-h-screen flex flex-col justify-between selection:bg-[#0072bc] selection:text-white bg-slate-50 relative">

    <!-- Sticky Glassmorphic Navbar Container -->
    <header id="mainNavbar" class="sticky top-0 z-50 bg-transparent transition-all duration-300">
        <?php include 'navbar.php'; ?>
    </header>

    <!-- Full-width Background Decorations -->
    <div class="absolute inset-x-0 top-0 -z-10 overflow-hidden" style="height: 900px; background: linear-gradient(180deg, #BFE4FD 0%, #FFFFFF 100%);">
        <div class="blob-1 absolute top-0 left-1/2 w-[900px] h-[900px] bg-[#0072bc]/10 rounded-full blur-3xl opacity-60 -translate-x-1/2 will-change-transform"></div>
        <div class="blob-2 absolute top-24 -left-20 w-96 h-96 bg-[#0072bc]/15 rounded-full blur-3xl will-change-transform"></div>
        <div class="blob-3 absolute bottom-0 right-0 w-96 h-96 bg-[#BFE4FD]/50 rounded-full blur-3xl opacity-70 will-change-transform"></div>
    </div>

    <main class="relative flex-grow w-full mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-16 md:pb-10" style="max-width: 1600px;">
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

            .blob-1 {
                animation: blobMove1 18s ease-in-out infinite;
            }

            .blob-2 {
                animation: blobMove2 14s ease-in-out infinite;
            }

            .blob-3 {
                animation: blobMove3 16s ease-in-out infinite;
            }
        </style>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-10 xl:gap-24 items-center">

            <!-- Left Content -->
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#0072bc]/10 border border-[#0072bc]/20 text-xs font-semibold text-[#0072bc] tracking-wide shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-[#0072bc]"></span>
                    #1 Tool For Identity Intelligence
                </div>

                <h1 class="mt-7 text-3xl sm:text-4xl lg:text-[2.75rem] xl:text-[3.25rem] font-black text-gray-900 tracking-tight max-w-3xl leading-[1.08] mx-auto lg:mx-0">
                    AI Tool Will Find Everything About Anyone Online
                </h1>

                <p class="mt-6 text-sm sm:text-base lg:text-lg text-black font-semibold max-w-2xl leading-relaxed mx-auto lg:mx-0">
                    Deep scan to trace digital footprint of any person and generate intelligent report
                </p>

                <!-- Feature Highlights -->
                <div class="mt-9 flex flex-wrap justify-center lg:justify-start gap-2 sm:gap-3">
                    <div class="inline-flex items-center gap-2 sm:gap-2.5 px-3 sm:px-5 py-2 sm:py-2.5 rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm text-[11px] sm:text-sm font-semibold text-gray-800">
                        <i class="fa-solid fa-user-shield text-[#0072bc] text-xs sm:text-base"></i>
                        Digital Identity Search
                    </div>
                    <div class="inline-flex items-center gap-2 sm:gap-2.5 px-3 sm:px-5 py-2 sm:py-2.5 rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm text-[11px] sm:text-sm font-semibold text-gray-800">
                        <i class="fa-solid fa-globe text-[#0072bc] text-xs sm:text-base"></i>
                        Online Footprint Analysis
                    </div>
                    <div class="inline-flex items-center gap-2 sm:gap-2.5 px-3 sm:px-5 py-2 sm:py-2.5 rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm text-[11px] sm:text-sm font-semibold text-gray-800">
                        <i class="fa-solid fa-file-lines text-[#0072bc] text-xs sm:text-base"></i>
                        Smart Identity Reports
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="mt-6 max-w-xl mx-auto lg:mx-0 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3 text-left shadow-sm">
                        <i class="fa-solid fa-circle-exclamation text-red-600 text-base"></i>
                        <p class="text-sm text-red-800 font-bold"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Search Form -->
                <div class="mt-9 max-w-2xl mx-auto lg:mx-0">
                    <form id="searchFormContainer" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="p-3 bg-white/95 backdrop-blur rounded-3xl border border-gray-200 shadow-[0_20px_60px_rgba(0,0,0,0.08)] flex flex-col sm:flex-row items-stretch gap-2.5 focus-within:border-[#0072bc] focus-within:shadow-[0_20px_70px_rgba(0,114,188,0.15)] transition-all duration-200">
                        <div class="flex flex-grow items-center gap-1.5 min-w-0 pl-4">
                            <div class="w-9 h-9 flex items-center justify-center text-[#0072bc] shrink-0">
                                <i class="fa-solid fa-magnifying-glass text-lg"></i>
                            </div>
                            <div class="flex-grow min-w-0 pl-2">
                                <input
                                    type="text"
                                    name="search_query"
                                    id="searchQueryInputField"
                                    placeholder="Enter Full Name or Social Profile Link"
                                    class="w-full bg-transparent border-0 outline-none text-sm lg:text-base text-black font-semibold py-3.5 focus:ring-0 placeholder:text-gray-400"
                                    autocomplete="off"
                                    required>
                            </div>
                        </div>

                        <button type="submit" id="submitScanButton" class="bg-[#0072bc] hover:bg-[#005a96] active:scale-[0.98] text-white px-8 py-4 rounded-2xl text-sm lg:text-base font-bold transition-all flex items-center justify-center gap-2.5 shadow-sm shadow-blue-200 cursor-pointer min-w-[155px]">
                            <span id="buttonIconNode" class="w-5 h-5 lg:w-6 lg:h-6 flex items-center justify-center shrink-0">
                                <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.10008 21.221C6.71021 19.2375 5.89258 16.8243 5.89258 14.2187C5.89258 10.8443 8.6265 8.10938 11.9989 8.10938C15.3712 8.10938 18.1051 10.8443 18.1051 14.2187" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M18.4359 20.3118C18.3259 20.3179 18.218 20.3281 18.107 20.3281C14.7347 20.3281 12.0007 17.5931 12.0007 14.2188" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M13.2694 21.9999C10.675 20.382 8.94705 17.5024 8.94705 14.2187C8.94705 12.5315 10.3145 11.164 12.0007 11.164C13.6869 11.164 15.0543 12.5315 15.0543 14.2187C15.0543 15.9059 16.4218 17.2733 18.108 17.2733C19.7942 17.2733 21.1616 15.9059 21.1616 14.2187C21.1616 9.1571 17.0602 5.05469 12.0017 5.05469C6.94319 5.05469 2.8418 9.1571 2.8418 14.2187C2.8418 15.3469 2.96806 16.4455 3.20021 17.5045" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M20.5257 5.86313C18.4435 3.4978 15.399 2 12.0002 2C8.60136 2 5.55687 3.4978 3.47461 5.86313" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <span id="buttonTextNode">Start Scan</span>
                        </button>
                    </form>

                    <div class="mt-5 flex flex-wrap items-center justify-center lg:justify-start gap-x-6 gap-y-2 text-xs lg:text-sm text-gray-600 font-medium">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-[#0072bc]"></i>
                            Fast identity lookup
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-[#0072bc]"></i>
                            AI-powered scan
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-[#0072bc]"></i>
                            Detailed report generation
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Hero Image / Visual -->
            <div class="relative flex items-center justify-center">
                <img src="<?php echo BASE_URL; ?>public/identity_hero_graphic_blue.svg" alt="Hero Right Image" class="w-full h-auto max-h-[300px] sm:max-h-[400px] lg:max-h-[500px] object-contain">
            </div>
        </div>
    </main>

    <section class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 mt-0 sm:mt-10 pt-4 sm:pt-14 pb-8 sm:pb-16">
        <!-- Section Header -->
        <div class="text-center max-w-2xl mx-auto mb-10">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-[#0072bc]/20 bg-[#0072bc]/10 text-xs font-bold text-[#0072bc] tracking-wide shadow-sm">
                <span class="w-2 h-2 rounded-full bg-[#0072bc]"></span>
                Why Identity Intelligence Matters
            </div>

            <h2 class="mt-5 text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 tracking-tight leading-tight">
                Make Smarter, Safer Decisions With Identity Insights
            </h2>

            <p class="mt-3 text-xs sm:text-sm text-gray-500 font-medium leading-relaxed">
                Analyze online signals, public traces, and identity patterns before making important decisions.
            </p>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-7">

            <!-- Card 1 -->
            <div class="group relative overflow-hidden bg-white p-6 sm:p-7 rounded-3xl border border-gray-200/80 shadow-sm hover:shadow-[0_20px_60px_rgba(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
                <div class="absolute top-0 right-0 w-28 h-28 bg-[#BFE4FD]/30 rounded-bl-[80px] opacity-70 group-hover:opacity-100 origin-top-right group-hover:scale-[6] transition-all duration-500 ease-in-out"></div>

                <div class="relative space-y-4">
                    <div class="w-12 h-12 bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center rounded-2xl text-lg shadow-sm border border-[#0072bc]/20 group-hover:bg-[#0072bc] group-hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>

                    <div>
                        <h4 class="text-base font-black text-gray-900">
                            Smarter Personal Decisions
                        </h4>
                        <p class="mt-2 text-sm text-gray-600 font-semibold leading-relaxed">
                            Audit individual digital history tracks before scheduling transactions or professional onboarding pipelines.
                        </p>
                    </div>

                    <div class="pt-2 flex items-center gap-2 text-xs font-bold text-[#0072bc]">
                        <span class="w-6 h-6 rounded-full bg-[#0072bc]/10 flex items-center justify-center">
                            <i class="fa-solid fa-check text-[10px]"></i>
                        </span>
                        Personal risk awareness
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="group relative overflow-hidden bg-white p-6 sm:p-7 rounded-3xl border border-gray-200/80 shadow-sm hover:shadow-[0_20px_60px_rgba(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
                <div class="absolute top-0 right-0 w-28 h-28 bg-[#BFE4FD]/30 rounded-bl-[80px] opacity-70 group-hover:opacity-100 origin-top-right group-hover:scale-[6] transition-all duration-500 ease-in-out"></div>

                <div class="relative space-y-4">
                    <div class="w-12 h-12 bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center rounded-2xl text-lg shadow-sm border border-[#0072bc]/20 group-hover:bg-[#0072bc] group-hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>

                    <div>
                        <h4 class="text-base font-black text-gray-900">
                            Increase Company Trust
                        </h4>
                        <p class="mt-2 text-sm text-gray-600 font-semibold leading-relaxed">
                            Maintain corporate entity transparency thresholds cleanly utilizing open-source historical records lookups.
                        </p>
                    </div>

                    <div class="pt-2 flex items-center gap-2 text-xs font-bold text-[#0072bc]">
                        <span class="w-6 h-6 rounded-full bg-[#0072bc]/10 flex items-center justify-center">
                            <i class="fa-solid fa-check text-[10px]"></i>
                        </span>
                        Better business confidence
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="group relative overflow-hidden bg-white p-6 sm:p-7 rounded-3xl border border-gray-200/80 shadow-sm hover:shadow-[0_20px_60px_rgba(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
                <div class="absolute top-0 right-0 w-28 h-28 bg-[#BFE4FD]/30 rounded-bl-[80px] opacity-70 group-hover:opacity-100 origin-top-right group-hover:scale-[6] transition-all duration-500 ease-in-out"></div>

                <div class="relative space-y-4">
                    <div class="w-12 h-12 bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center rounded-2xl text-lg shadow-sm border border-[#0072bc]/20 group-hover:bg-[#0072bc] group-hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-ban"></i>
                    </div>

                    <div>
                        <h4 class="text-base font-black text-gray-900">
                            Avoid Bad Engagements
                        </h4>
                        <p class="mt-2 text-sm text-gray-600 font-semibold leading-relaxed">
                            Instantly recognize malicious behavior flags or platform spoofing identities before issues materialize.
                        </p>
                    </div>

                    <div class="pt-2 flex items-center gap-2 text-xs font-bold text-[#0072bc]">
                        <span class="w-6 h-6 rounded-full bg-[#0072bc]/10 flex items-center justify-center">
                            <i class="fa-solid fa-check text-[10px]"></i>
                        </span>
                        Early warning signals
                    </div>
                </div>
            </div>

        </div>
    </section>

    <?php if (file_exists('index_faq.php')) {
        include 'index_faq.php';
    } ?>

    <?php if (file_exists('index_footer.php')) {
        include 'index_footer.php';
    } ?>

    <script>
        // NAVBAR SCROLL GLASS EFFECT
        document.addEventListener("DOMContentLoaded", () => {
            const navbar = document.getElementById("mainNavbar");
            if (!navbar) return;

            const handleScroll = () => {
                if (window.scrollY > 20) {
                    navbar.classList.remove("bg-transparent");
                    navbar.classList.add("bg-white/80", "backdrop-blur-md", "shadow-sm");
                } else {
                    navbar.classList.add("bg-transparent");
                    navbar.classList.remove("bg-white/80", "backdrop-blur-md", "shadow-sm");
                }
            };

            window.addEventListener("scroll", handleScroll);
            handleScroll(); // Trigger initial state
        });

        document.addEventListener("DOMContentLoaded", () => {
            const container = document.getElementById("faqAccordionContainer");
            if (!container) return;

            container.addEventListener("click", (e) => {
                const trigger = e.target.closest(".faq-toggle-trigger");
                if (!trigger) return;

                const panel = trigger.parentElement.querySelector(".faq-content-slider");
                const icon = trigger.querySelector(".fa-chevron-down");

                if (panel.style.maxHeight === "0px" || panel.style.maxHeight === "") {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                    panel.style.opacity = "1";
                    icon.style.transform = "rotate(180deg)";
                    trigger.parentElement.classList.add("active");
                } else {
                    panel.style.maxHeight = "0px";
                    panel.style.opacity = "0";
                    icon.style.transform = "rotate(0deg)";
                    trigger.parentElement.classList.remove("active");
                }
            });
        });

        document.getElementById('searchFormContainer').addEventListener('submit', function(e) {
            const inputField = document.getElementById('searchQueryInputField');
            if (inputField && inputField.value.trim() !== "") {
                const btn = document.getElementById('submitScanButton');
                const iconNode = document.getElementById('buttonIconNode');
                const textNode = document.getElementById('buttonTextNode');

                if (btn && iconNode && textNode) {
                    btn.style.pointerEvents = 'none';
                    iconNode.innerHTML = `<i class="fa-solid fa-spinner animate-spin text-sm"></i>`;
                    textNode.textContent = 'Processing...';
                }
            }
        });

        // DYNAMIC MIXED/SHUFFLED SERIAL PLACEHOLDER ENGINE
        (function() {
            const inputEl = document.getElementById('searchQueryInputField');
            if (!inputEl) return;

            const defaultPlaceholder = "Enter Full Name or Social Profile Link";

            // Base seed array of 10 heavily used name configurations
            let namesPool = [
                "James Johnson", "John Williams", "Robert Brown",
                "Michael Jones", "David Miller", "William Davis",
                "Mary Johnson", "Patricia Williams", "Jennifer Brown", "Linda Jones"
            ];

            // Fisher-Yates shuffle engine to mix the array completely on landing/refresh
            for (let i = namesPool.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [namesPool[i], namesPool[j]] = [namesPool[j], namesPool[i]];
            }

            let typeTimeout, phaseTimeout;
            let isUserInteracting = false;
            let currentNameIndex = 0;

            function runTypewriterCycle() {
                if (isUserInteracting) return;

                // Extract the name sequentially from our scrambled set
                const selectedName = namesPool[currentNameIndex];
                let currentText = "";
                let letterIndex = 0;

                function typeLetter() {
                    if (isUserInteracting) return;

                    if (letterIndex < selectedName.length) {
                        currentText += selectedName.charAt(letterIndex);
                        inputEl.placeholder = currentText;
                        letterIndex++;
                        typeTimeout = setTimeout(typeLetter, 120); // Typing iteration speed configuration
                    } else {
                        // Entire hint string successfully drawn onto viewport field block
                        phaseTimeout = setTimeout(() => {
                            if (isUserInteracting) return;
                            inputEl.placeholder = defaultPlaceholder;

                            // Increment pointer array index sequentially through the shuffled list
                            currentNameIndex = (currentNameIndex + 1) % namesPool.length;

                            // Retain base instruction layout line safely before routing back into execution lines
                            phaseTimeout = setTimeout(runTypewriterCycle, 1500);
                        }, 2000);
                    }
                }
                typeLetter();
            }

            // Initialize engine timeline exactly 1.5 seconds post initial document execution
            phaseTimeout = setTimeout(runTypewriterCycle, 1500);

            // UI Safe Intercept Blockers: Deconstruct programmatic script handlers instantly if real human events fire
            function stopHintAnimation() {
                isUserInteracting = true;
                clearTimeout(typeTimeout);
                clearTimeout(phaseTimeout);
                inputEl.placeholder = defaultPlaceholder;
            }

            inputEl.addEventListener('focus', stopHintAnimation);
            inputEl.addEventListener('input', stopHintAnimation);
        })();

        // HERO SLIDER
        (function() {
            const slider = document.getElementById('heroSlider');
            if (!slider) return;
            const track = slider.querySelector('.slider-track');
            const slides = slider.querySelectorAll('.slider-slide');
            const prevBtn = slider.querySelector('.slider-prev');
            const nextBtn = slider.querySelector('.slider-next');
            let current = 0;
            let interval;

            function goTo(index) {
                if (index < 0) index = slides.length - 1;
                if (index >= slides.length) index = 0;
                current = index;
                track.style.transform = 'translateX(-' + (current * 100) + '%)';
            }

            function next() {
                goTo(current + 1);
            }

            function prev() {
                goTo(current - 1);
            }

            function startAuto() {
                interval = setInterval(next, 5000);
            }

            function stopAuto() {
                clearInterval(interval);
            }

            if (nextBtn) nextBtn.addEventListener('click', () => {
                next();
                stopAuto();
                startAuto();
            });
            if (prevBtn) prevBtn.addEventListener('click', () => {
                prev();
                stopAuto();
                startAuto();
            });

            slider.addEventListener('mouseenter', stopAuto);
            slider.addEventListener('mouseleave', startAuto);

            goTo(0);
            startAuto();
        })();
    </script>
</body>

</html>