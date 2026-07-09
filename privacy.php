<?php
/**
 * Identity Trace AI — Privacy Policy Legal Workspace
 * File: privacy.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Privacy Policy — Identity Search AI</title>
    <?php include 'head.php'; ?>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white bg-slate-50">

    <?php include 'navbar.php'; ?>

    <main class="flex-grow max-w-4xl w-full mx-auto px-4 sm:px-6 pt-12 pb-16">
        <div class="space-y-8">
            
            <!-- HEADER BLOCK -->
            <div class="text-center space-y-2">
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Privacy Policy</h1>
                <p class="text-base text-black font-semibold max-w-md mx-auto leading-relaxed">
                    How we process operational dashboard data, secure workspace profiles, and treat open-source records.
                </p>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest pt-2">Last Updated: June 2026</p>
            </div>

            <!-- PRIVACY MATRICES ACCORDION WRAPPER -->
            <div class="space-y-4" id="privacyAccordionContainer">

                <!-- SECTION 1 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">1. Data Collection Frameworks</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>1.1. <b>Account Metadata:</b> When creating an account profile signature, we store your electronic mail address, submitted name configurations, and baseline geo-location values sent via network security layers.</p>
                            <p>1.2. <b>Search Context Records:</b> We temporarily log inbound lookups (e.g., target profile strings or full names) to systematically cycle automated profiling hooks across indexed public directories.</p>
                            <p>1.3. <b>Payment Tokenization:</b> Financial transactions are managed entirely by secure third-party billing providers. Our databases do not store raw credit card credentials or banking access indices.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">2. How We Utilize Collected Parameters</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>2.1. To initialize real-time open-source intelligence (OSINT) parsing mechanisms and compile coherent behavioral matrices for generated dossiers.</p>
                            <p>2.2. To authenticate dashboard logins using single-use security tokens (OTP) and securely deliver requested PDF data files or receipts straight to your inbox.</p>
                            <p>2.3. To safeguard the application console against excessive query flooding, scraping exploits, and automated profile mining attacks.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">3. OSINT Aggregation & Data Processing</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>3.1. Identity Trace AI acts as a search interface pipeline. The platform does not host, create, or maintain the underlying biographical entries returned inside our intelligence dossiers.</p>
                            <p>3.2. All analytical results are gathered dynamically on-demand from publicly searchable registers, social network tracks, metadata indexes, and open web directories.</p>
                            <p>3.3. Generated profiling history dossiers are automatically purged from our staging servers 30 days after creation to guarantee user query isolation.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 4 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">4. Data Sharing & Third-Party Protection</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>4.1. We do not sell, rent, lease, or lease-swap user dashboard logs or account lists to marketing networks, broker chains, or commercial advertising pools.</p>
                            <p>4.2. Operational metrics are only shared with verified system nodes (e.g., mail dispatch pathways, data routing providers, billing operators) strictly necessary to run the service interface.</p>
                            <p>4.3. We retain authority to disclose account variables exclusively if required to comply with binding court documentation, legal statutory requests, or active Wyoming judicial processes.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 5 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">5. Security & Infrastructure Protection</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>5.1. All incoming and outgoing data packages pass through high-tier Secure Socket Layer (SSL/TLS) encryption layers during active runtime processes.</p>
                            <p>5.2. Account authorization sequences leverage dynamic, single-use email verification tokens (OTP tokens) to eliminate risks linked to standard static password leaks or credential stuffing exploits.</p>
                            <p>5.3. While we enforce strict server monitoring protocols to isolate databases, no method of digital transmission over public routing channels can guarantee absolute, unbreachable protection metrics.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 6 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">6. User Privacy Rights & Deletion Opt-Outs</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>6.1. Users maintain full authority to inspect, update, or completely erase their registered account signatures and historical trace structures from the active management panel.</p>
                            <p>6.2. If you want to request a manual deletion of your workspace profile or log history from all platform database nodes, you can file an explicit ticket request with our support desk at support@idtrace.io.</p>
                            <p>6.3. Once a profile signature removal request is confirmed, all associated user attributes are dropped immediately from our active production staging systems.</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

    <!-- INTERACTIVE ACCORDION INTERFACE LOGIC SCRIPT -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const container = document.getElementById("privacyAccordionContainer");
        if (!container) return;

        // Force all sliding panels to register full size heights by default layout parameters
        container.querySelectorAll(".privacy-content-slider").forEach(panel => {
            panel.style.maxHeight = panel.scrollHeight + "px";
        });

        container.addEventListener("click", (e) => {
            const trigger = e.target.closest(".privacy-toggle-trigger");
            if (!trigger) return;

            const panel = trigger.parentElement.querySelector(".privacy-content-slider");
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
    </script>
</body>
</html>
