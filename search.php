<?php
/**
 * OSINT Universal Intelligence Console — Main Workspace Container
 * File: search.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$search_query = trim($_GET['q'] ?? '');
$engine = trim($_GET['engine'] ?? 'facebook');

// Validation matrix fallback route handling
if (!in_array($engine, ['facebook', 'linkedin', 'instagram', 'twitter', 'tiktok', 'truecaller'])) {
    $engine = 'facebook';
}

// Font Awesome 6 mapping dictionary array for the dropdown preview state
$engineIcons = [
    'facebook'   => 'fa-brands fa-facebook-f text-blue-600',
    'linkedin'   => 'fa-brands fa-linkedin-in text-blue-700',
    'instagram'  => 'fa-brands fa-instagram text-pink-600',
    'twitter'    => 'fa-brands fa-x-twitter text-slate-800',
    'tiktok'     => 'fa-brands fa-tiktok text-black',
    'truecaller' => 'fa-solid fa-phone text-[#128c7e]'
];
$currentIconClass = $engineIcons[$engine] ?? 'fa-solid fa-magnifying-glass text-gray-400';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Result — Identity Search AI</title>
    <?php include 'head.php'; ?>

    <style>
        .fade-in-up { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white">

    <?php include 'navbar.php'; ?>

    <main class="max-w-4xl w-full mx-auto px-4 sm:px-6 py-8 flex-grow">
        
        <section id="searchConsoleWrapper" class="mb-10">
            <form action="search.php" method="GET" id="intelligenceForm" onsubmit="executeOsintLookup(event)" class="relative flex flex-col sm:flex-row items-stretch sm:items-center w-full bg-white sm:border border-gray-200 focus-within:border-[#128c7e] rounded-2xl transition shadow-xl p-2.5 sm:p-2 z-30 gap-2 sm:gap-0 border">
                
                <div class="flex items-center w-full bg-transparent gap-1 relative">
                    <div class="relative inline-block text-left pl-1">
                        <button type="button" onclick="togglePlatformDropdown(event)" id="dropdownToggleBtn" class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100/80 transition text-gray-700 font-bold text-sm outline-none h-11 cursor-pointer">
                            <i id="activeDropdownIcon" class="<?php echo $currentIconClass; ?> text-base"></i>
                            <i class="fa-solid fa-chevron-down text-[10px] opacity-60 ml-0.5"></i>
                        </button>
                        
                        <div id="platformDropdownMenu" class="hidden absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-lg py-1.5 z-50 animate-fade-in font-medium text-black">
                            <button type="button" onclick="selectDropdownEngine('facebook')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm hover:bg-slate-50 transition text-left cursor-pointer">
                                <i class="fa-brands fa-facebook-f text-blue-600 w-4 text-center"></i> Facebook
                            </button>
                            <button type="button" onclick="selectDropdownEngine('linkedin')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm hover:bg-slate-50 transition text-left cursor-pointer">
                                <i class="fa-brands fa-linkedin-in text-blue-700 w-4 text-center"></i> LinkedIn
                            </button>
                            <button type="button" onclick="selectDropdownEngine('instagram')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm hover:bg-slate-50 transition text-left cursor-pointer">
                                <i class="fa-brands fa-instagram text-pink-600 w-4 text-center"></i> Instagram
                            </button>
                            <button type="button" onclick="selectDropdownEngine('twitter')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm hover:bg-slate-50 transition text-left cursor-pointer">
                                <i class="fa-brands fa-x-twitter text-slate-800 w-4 text-center"></i> Twitter (X)
                            </button>
                            <button type="button" onclick="selectDropdownEngine('tiktok')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm hover:bg-slate-50 transition text-left cursor-pointer">
                                <i class="fa-brands fa-tiktok text-black w-4 text-center"></i> TikTok
                            </button>
                            <button type="button" onclick="selectDropdownEngine('truecaller')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm hover:bg-slate-50 transition text-left cursor-pointer">
                                <i class="fa-solid fa-phone text-[#128c7e] w-4 text-center"></i> TrueCaller
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="engine" id="formEngineField" value="<?php echo htmlspecialchars($engine); ?>">
                    
                    <input type="text" name="q" id="searchMainInput" oninput="handleClearButtonVisibility()" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Instagram Name or Profile Link" class="w-full bg-transparent border-0 outline-none text-black text-base font-semibold px-2 pr-10 py-2 h-11 focus:ring-0" required autocomplete="off">
                    
                    <button type="button" id="clearSearchInputBtn" onclick="clearOsintSearchField()" class="absolute right-2 top-1/2 -translate-y-1/2 hidden text-gray-400 hover:text-gray-700 transition p-1 text-sm outline-none cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                
                <button type="submit" id="searchSubmitBtn" class="bg-[#128c7e] hover:bg-[#0e6f64] text-white font-bold px-6 py-2.5 rounded-xl transition text-sm tracking-wide shadow-sm flex items-center h-11 sm:min-w-[125px] justify-center outline-none disabled:opacity-80 disabled:cursor-not-allowed cursor-pointer">
                    <span id="btnLabelContainer">Search</span>
                </button>
            </form>
        </section>

        <hr class="border-gray-200/60 mb-8">

        <section id="osintResultsWrapper">
            <div id="initialPlaceholderModule" class="<?php echo empty($search_query) ? '' : 'hidden'; ?> bg-white rounded-2xl p-12 border border-gray-200/80 text-center max-w-lg mx-auto shadow-sm my-6 fade-in-up">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-[#128c7e] flex items-center justify-center mx-auto mb-4 shadow-inner"><i class="fa-solid fa-radar text-xl animate-pulse"></i></div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Initiate Lookup Protocol</h3>
                <p class="text-sm text-black font-medium max-w-xs mx-auto">Input structural entity attributes above to orchestrate live web graph data extraction metrics natively.</p>
            </div>

            <!-- Left completely empty to maintain compatibility with your exact search.js tab rendering logic -->
            <div id="globalLoader" class="hidden"></div>

            <div id="tabContentSandbox" class="hidden"></div>
        </section>

    </main>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

    <script>
        let activeTab = "<?php echo $engine; ?>";
        let activeSearchQuery = <?php echo json_encode($search_query); ?>;

        /**
         * Dynamic Placeholder Matrix Configuration Helper without "Enter " prefix string
         */
        function updatePlaceholderText(selectedEngine) {
            const inputEl = document.getElementById('searchMainInput');
            if (!inputEl) return;
            
            if (selectedEngine === 'truecaller') {
                inputEl.placeholder = "Mobile Number with Country Code";
            } else {
                let nameMapping = {
                    'facebook': 'Facebook',
                    'linkedin': 'LinkedIn',
                    'instagram': 'Instagram',
                    'twitter': 'Twitter (X)',
                    'tiktok': 'TikTok'
                };
                let currentLabel = nameMapping[selectedEngine] || 'Social Media';
                inputEl.placeholder = `${currentLabel} Name or Profile Link`;
            }
        }

        /**
         * Checks text fields inputs context to map cross-clear button state
         */
        function handleClearButtonVisibility() {
            const inputVal = document.getElementById('searchMainInput').value.trim();
            const clearBtn = document.getElementById('clearSearchInputBtn');
            if (clearBtn) {
                if (inputVal.length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }
        }

        /**
         * Clears explicit targets input text completely and returns focal pointers
         */
        function clearOsintSearchField() {
            const searchField = document.getElementById('searchMainInput');
            if (searchField) {
                searchField.value = '';
                searchField.focus();
            }
            handleClearButtonVisibility();
        }

        /**
         * Dynamic dropdown select layout toggler
         */
        function togglePlatformDropdown(e) {
            e.stopPropagation();
            const menu = document.getElementById('platformDropdownMenu');
            menu.classList.toggle('hidden');
        }

        /**
         * Update engine state from icon dropdown list without triggering instantly
         */
        function selectDropdownEngine(selectedEngine) {
            document.getElementById('formEngineField').value = selectedEngine;
            activeTab = selectedEngine;
            
            const iconMapping = {
                'facebook': 'fa-brands fa-facebook-f text-blue-600',
                'linkedin': 'fa-brands fa-linkedin-in text-blue-700',
                'instagram': 'fa-brands fa-instagram text-pink-600',
                'twitter': 'fa-brands fa-x-twitter text-slate-800',
                'tiktok': 'fa-brands fa-tiktok text-black',
                'truecaller': 'fa-solid fa-phone text-[#128c7e]'
            };

            const iconEl = document.getElementById('activeDropdownIcon');
            if (iconEl) {
                iconEl.className = iconMapping[selectedEngine] || 'fa-solid fa-magnifying-glass text-gray-400';
                iconEl.classList.add('text-base');
            }
            
            updatePlaceholderText(selectedEngine);
            document.getElementById('platformDropdownMenu').classList.add('hidden');
        }

        /**
         * Re-activates the search button state
         */
        function resetSearchButtonState() {
            const targetBtn = document.getElementById('searchSubmitBtn');
            const labelContainer = document.getElementById('btnLabelContainer');
            
            if (targetBtn && labelContainer) {
                targetBtn.disabled = false;
                labelContainer.textContent = 'Search';
            }
        }

        /**
         * Direct submission re-trigger route from lookback forms
         */
        function triggerDirectProfileLinkLookup(linkValue) {
            const searchInput = document.getElementById('searchMainInput');
            if (searchInput && linkValue.trim()) {
                searchInput.value = linkValue.trim();
                handleClearButtonVisibility();
                executeOsintLookup(null);
            }
        }

        /**
         * Intercepts form submission: handles URL history and begins custom text animation
         */
        function executeOsintLookup(e) {
            if (e) e.preventDefault();
            
            const targetedInput = document.getElementById('searchMainInput').value.trim();
            if (!targetedInput) return;
            
            activeSearchQuery = targetedInput;

            // Update address bar cleanly without page load
            const updatedUrlPath = window.location.pathname + '?engine=' + encodeURIComponent(activeTab) + '&q=' + encodeURIComponent(activeSearchQuery);
            window.history.pushState({ engine: activeTab, q: activeSearchQuery }, '', updatedUrlPath);

            // Handle section states visibility switching
            document.getElementById('initialPlaceholderModule').classList.add('hidden');
            document.getElementById('tabContentSandbox').classList.add('hidden');
            document.getElementById('globalLoader').classList.remove('hidden');

            // Set button to disabled/non-clickable state showing static fixed text
            const targetBtn = document.getElementById('searchSubmitBtn');
            const labelContainer = document.getElementById('btnLabelContainer');
            
            if (targetBtn && labelContainer) {
                targetBtn.disabled = true;
                labelContainer.textContent = 'Searching...';
            }

            // Trigger the asynchronous search handler sequence from search.js
            if (typeof switchNetworkTab === "function") {
                setTimeout(() => {
                    switchNetworkTab(activeTab);
                }, 5);
            }
        }

        // Restore form elements if user hits browser back buttons
        window.addEventListener('popstate', function(event) {
            if (event.state) {
                document.getElementById('formEngineField').value = event.state.engine;
                document.getElementById('searchMainInput').value = event.state.q;
                selectDropdownEngine(event.state.engine);
                
                activeTab = event.state.engine;
                activeSearchQuery = event.state.q;
                
                handleClearButtonVisibility();
                
                if (typeof switchNetworkTab === "function") {
                    switchNetworkTab(activeTab);
                }
            }
        });

        document.addEventListener('click', function() {
            const menu = document.getElementById('platformDropdownMenu');
            if (menu) menu.classList.add('hidden');
        });
        
        // Execute initial input monitoring state evaluation on browser render
        document.addEventListener("DOMContentLoaded", function() {
            updatePlaceholderText(activeTab);
            handleClearButtonVisibility();
        });
    </script>

    <script src="search.js?v=<?php echo time(); ?>"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initial query execution block on explicit deep link parameters direct load
            if (activeSearchQuery !== "") {
                if (typeof switchNetworkTab === "function") {
                    executeOsintLookup(null);
                } else {
                    document.getElementById('tabContentSandbox').classList.remove('hidden');
                    document.getElementById('tabContentSandbox').innerHTML = `<div class="text-center py-12 text-sm text-red-600 font-bold">Script Integration Failure: Engine script 'search.js' did not load correctly.</div>`;
                }
            }
        });
    </script>
</body>
</html>
