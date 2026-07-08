<?php
/**
 * Identity Trace AI — Terms of Service Legal Workspace
 * File: terms.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Terms of Service — ID Trace AI</title>
    <?php include 'head.php'; ?>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white bg-slate-50">

    <?php include 'navbar.php'; ?>

    <main class="flex-grow max-w-4xl w-full mx-auto px-4 sm:px-6 pt-12 pb-16">
        <div class="space-y-8">
            
            <!-- HEADER BLOCK -->
            <div class="text-center space-y-2">
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Terms of Service</h1>
                <p class="text-base text-black font-semibold max-w-md mx-auto leading-relaxed">
                    Rules for the use of the platform console and provision of automated intelligence services.
                </p>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest pt-2">Last Updated: June 2026</p>
            </div>

            <!-- LEGAL PROVISIONS ACCORDION WRAPPER -->
            <div class="space-y-4" id="termsAccordionContainer">

                <!-- SECTION 1 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">1. General Provisions</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>1.1. These rules lay down the standard terms and conditions for the utilization of the platform console located at IDTrace.io and any background scanning automated outputs provided by Identity Trace AI.</p>
                            <p>1.2. Services are managed, systematically deployed, and legally administered under international operational mandates by our registered business entity framework in the State of Wyoming, USA.</p>
                            <p>1.3. Users confirm explicit, uncompromised consent to these provisions and undertake to fully comply with them when initializing data tracking modules inside this portal in any way or form.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">2. Definitions</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p><b>Report / Dossier:</b> Means a structured, aggregated compilation of open-source intelligence metrics providing public profile data footprints relative to a validated identity query target.</p>
                            <p><b>Platform Engine:</b> The interactive UI configuration mechanisms, scanning infrastructure algorithms, database layers, and API network gateways accessible under the ID Trace AI brand parameters.</p>
                            <p><b>Private User:</b> Any natural individual looking up infrastructure parameters for personal safety optimization, independent reputation tracking, or private validation objectives outside of primary commercial reselling channels.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">3. User Workspace Accounts</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>3.1. To initialize digital footprint audits, trace data arrays, or persist historical intelligence records, operators must establish a workspace profile using an active electronic communication signature.</p>
                            <p>3.2. Workspace access codes and single-use verification links are strictly non-transferable. Operators assume direct responsibility for maintaining absolute confidentiality barriers around access tokens and account credentials.</p>
                            <p>3.3. Identity Trace AI retains autonomous rights to limit access parameters, freeze analytical processing runs, or drop credentials instantly if behavior thresholds are breached or suspicious activity maps are registered.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 4 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">4. Reporting & System Limitations</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>4.1. Identity Trace AI acts solely as a specialized discovery overlay pipeline. We do not edit public registers, do not manage open data archives, and make no explicit claims regarding the absolute correctness or completeness of the open-source records returned.</p>
                            <p>4.2. Analytical calculations compile directly onto server environments. Dossier availability parameters vary based on system capacities, platform query limits, and target open-source availability levels.</p>
                            <p>4.3. Rendered dossiers remain persistently accessible within user profile storage sections for a fixed period of 30 days following generation, after which records are dropped automatically from storage layers.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 5 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">5. Prohibited Platform Misuse</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-2">
                            <p>Operators explicitly commit to avoiding the following prohibited interaction behaviors:</p>
                            <ul class="space-y-1.5 list-disc pl-5 text-black">
                                <li>Deploying malicious automatic tools, scripts, or continuous automated scraping routines to read system database nodes.</li>
                                <li>Artificially bloating network processing metrics or dropping infrastructure latency via excessive multi-threaded API requests.</li>
                                <li>Using aggregated dossier outputs to engage in harassment, extortion, targeted identity fraud, or malicious exposure.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- SECTION 6 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">6. Protection of Private Users & Refunds</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>6.1. If a Private User orders platform services, they retain full coverage under our risk-free 30-day money-back guarantee policy should analytical processing parameters drop below directory criteria guidelines.</p>
                            <p>6.2. Cancellation and refund processing requests must be filed explicitly via free-form text format allocations directed to our support desk endpoint at support@idtrace.io.</p>
                            <p>6.3. Validated adjustments or premium remittance balance transactions will be returned directly back onto the operator's original financial card processor channel pipeline within a 14-day clearance window.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 7 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">7. Intellectual Property Rights</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>7.1. All foundational software architectures, layout styling parameters, console source configurations, tracking mechanics vectors, icons, design layouts, and system-wide textual expressions are the exclusive ownership properties of Identity Trace AI.</p>
                            <p>7.2. Operators are strictly prohibited from copying, distributing, republishing, reverse-engineering, decompiling, or mapping any internal system processing structures or data schema models without explicit written developer consent authorizations.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 8 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">8. Force Majeure Exceptions</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>8.1. Identity Trace AI cannot be held liable, penalized, or legally restricted for delivery downtime or system tracing failures caused by elements passing beyond logical operator control frameworks.</p>
                            <p>8.2. These exceptions incorporate, but are not limited to, global web protocol network failures, cloud server infrastructure outages, acts of God, unexpected third-party target API structural redesign blocks, or sweeping regulatory updates.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 9 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">9. Liability & Compliance Boundaries</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>9.1. Under no structural or judicial criteria can Identity Trace AI, its technical architects, or corporate entities be held liable for losses or complications resulting from user-side actions executed based on dossier analysis records.</p>
                            <p>9.2. This system compiles open OSINT variables and explicitly does not perform operations as a consumer reporting entity. All tracking results are completely barred from evaluation processes governed by Fair Credit Reporting Act (FCRA) provisions.</p>
                            <p>9.3. Governing legal jurisdiction parameters reside entirely under the statutory framework rules of the State of Wyoming, USA, without regard to conflict of law criteria.</p>
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
        const container = document.getElementById("termsAccordionContainer");
        if (!container) return;

        // Force all sliding panels to register full size heights by default layout parameters
        container.querySelectorAll(".terms-content-slider").forEach(panel => {
            panel.style.maxHeight = panel.scrollHeight + "px";
        });

        container.addEventListener("click", (e) => {
            const trigger = e.target.closest(".terms-toggle-trigger");
            if (!trigger) return;

            const panel = trigger.parentElement.querySelector(".terms-content-slider");
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
