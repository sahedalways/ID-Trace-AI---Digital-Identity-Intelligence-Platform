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
        header("Location: " . BASE_URL . "search.php?q=" . $url_parameter . "&engine=" . $engine_parameter);
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
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white bg-slate-50">

    <?php include 'navbar.php'; ?>

    <main class="flex-grow max-w-4xl w-full mx-auto px-4 sm:px-6 pt-16 pb-12 md:pb-4 text-center">
        <div class="space-y-6">
            
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-50 border border-emerald-100 text-xs font-semibold text-emerald-800 tracking-wide">
                #1 Tool For Identity Intelligence
            </div>

            <h1 class="text-4xl sm:text-5xl font-black text-gray-900 tracking-tight max-w-2xl mx-auto leading-tight">
                AI Tool Will Find Everything About Anyone Online
            </h1>
            <p class="text-base text-black font-semibold max-w-xl mx-auto leading-relaxed">
               Deep scan to trace digital footprint of any person and generate intelligent report
            </p>

            <?php if (!empty($error)): ?>
                <div class="max-w-xl mx-auto p-4 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3 text-left">
                    <i class="fa-solid fa-circle-exclamation text-red-600 text-base"></i>
                    <p class="text-sm text-red-800 font-bold"><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>

            <form id="searchFormContainer" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="max-w-xl mx-auto p-2.5 bg-white rounded-2xl border border-gray-200 shadow-xl flex flex-col sm:flex-row items-stretch gap-2 focus-within:border-[#128c7e] transition-all duration-200">
                <div class="flex flex-grow items-center gap-1 min-w-0 pl-3">
                    <div class="w-7 h-7 flex items-center justify-center text-[#128c7e] shrink-0">
                        <i class="fa-solid fa-magnifying-glass text-lg"></i>
                    </div>
                    <div class="flex-grow min-w-0 pl-2">
                        <input 
                            type="text" 
                            name="search_query" 
                            id="searchQueryInputField"
                            placeholder="Enter Full Name or Social Profile Link" 
                            class="w-full bg-transparent border-0 outline-none text-base text-black font-semibold py-2.5 focus:ring-0"
                            autocomplete="off"
                            required
                        >
                    </div>
                </div>

                <button type="submit" id="submitScanButton" class="bg-[#128c7e] hover:bg-[#0e6f64] active:scale-[0.98] text-white px-7 py-3.5 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2 shadow-sm shadow-emerald-200 cursor-pointer min-w-[130px]">
                    <span id="buttonIconNode" class="w-5 h-5 flex items-center justify-center shrink-0">
                        <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.10008 21.221C6.71021 19.2375 5.89258 16.8243 5.89258 14.2187C5.89258 10.8443 8.6265 8.10938 11.9989 8.10938C15.3712 8.10938 18.1051 10.8443 18.1051 14.2187" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18.4359 20.3118C18.3259 20.3179 18.218 20.3281 18.107 20.3281C14.7347 20.3281 12.0007 17.5931 12.0007 14.2188" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M13.2694 21.9999C10.675 20.382 8.94705 17.5024 8.94705 14.2187C8.94705 12.5315 10.3145 11.164 12.0007 11.164C13.6869 11.164 15.0543 12.5315 15.0543 14.2187C15.0543 15.9059 16.4218 17.2733 18.108 17.2733C19.7942 17.2733 21.1616 15.9059 21.1616 14.2187C21.1616 9.1571 17.0602 5.05469 12.0017 5.05469C6.94319 5.05469 2.8418 9.1571 2.8418 14.2187C2.8418 15.3469 2.96806 16.4455 3.20021 17.5045" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20.5257 5.86313C18.4435 3.4978 15.399 2 12.0002 2C8.60136 2 5.55687 3.4978 3.47461 5.86313" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span id="buttonTextNode">Start Scan</span>
                </button>
            </form>
        </div>
    </main>

    <section class="w-full max-w-4xl mx-auto px-4 sm:px-6 py-12 md:pt-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm space-y-3">
                <div class="w-10 h-10 bg-emerald-50 text-[#128c7e] flex items-center justify-center rounded-xl text-base shadow-2xs"><i class="fa-solid fa-user-shield"></i></div>
                <h4 class="text-base font-bold text-gray-900">Smarter Personal Decisions</h4>
                <p class="text-sm text-black font-semibold leading-normal">Audit individual digital history tracks before scheduling transactions or professional onboarding pipelines.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm space-y-3">
                <div class="w-10 h-10 bg-emerald-50 text-[#128c7e] flex items-center justify-center rounded-xl text-base shadow-2xs"><i class="fa-solid fa-chart-line"></i></div>
                <h4 class="text-base font-bold text-gray-900">Increase Company Trust</h4>
                <p class="text-sm text-black font-semibold leading-normal">Maintain corporate entity transparency thresholds cleanly utilizing open-source historical records lookups.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm space-y-3">
                <div class="w-10 h-10 bg-emerald-50 text-[#128c7e] flex items-center justify-center rounded-xl text-base shadow-2xs"><i class="fa-solid fa-ban"></i></div>
                <h4 class="text-base font-bold text-gray-900">Avoid Bad Engagements</h4>
                <p class="text-sm text-black font-semibold leading-normal">Instantly recognize malicious behavior flags or platform spoofing identities before issues materialize.</p>
            </div>
        </div>
    </section>

    <?php if (file_exists('index_faq.php')) { include 'index_faq.php'; } ?>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const container = document.getElementById("faqAccordionContainer");
        if (!container) return;

        // Force all sliding panels to register full size heights by default layout parameters
        container.querySelectorAll(".faq-content-slider").forEach(panel => {
            panel.style.maxHeight = panel.scrollHeight + "px";
        });

        container.addEventListener("click", (e) => {
            const trigger = e.target.closest(".faq-toggle-trigger");
            if (!trigger) return;

            const panel = trigger.parentElement.querySelector(".faq-content-slider");
            const icon  = trigger.querySelector("i");

            if (panel.style.maxHeight === "0px" || panel.style.maxHeight === "") {
                panel.style.maxHeight = panel.scrollHeight + "px";
                panel.style.opacity = "1";
                icon.style.transform = "rotate(180deg)";
            } else {
                panel.style.maxHeight = "0px";
                panel.style.opacity = "0";
                icon.style.transform = "rotate(0deg)";
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
            "Michael Jones", "David Miller",  "William Davis",
            "Mary Johnson",  "Patricia Williams", "Jennifer Brown", "Linda Jones"
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
    </script>
</body>
</html>
