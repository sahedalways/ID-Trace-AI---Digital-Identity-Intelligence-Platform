<?php
/**
 * File: fcra.php
 * Understanding the FCRA — exact content from Understanding the FCRA.docx
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Understanding the FCRA — Identity Search AI</title>
    <?php include 'head.php'; ?>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white bg-slate-50">

    <?php include 'navbar.php'; ?>

    <main class="flex-grow max-w-4xl w-full mx-auto px-4 sm:px-6 pt-12 pb-16">
        <div class="space-y-8">

            <div class="text-center space-y-2">
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Understanding the FCRA</h1>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest pt-2">Last Updated: July 2026</p>
            </div>

            <div class="space-y-4" id="fcraAccordionContainer">

                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">1. What is the FCRA?</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>The Fair Credit Reporting Act (FCRA) regulates how and when information about you can be utilized. Generally speaking, when an employment, credit, or tenant screening decision is being made about you, the entity receiving your information must provide you with two things:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>Notice that they will be accessing your public or private record information.</li>
                                <li>Disclosure regarding where they received this information if an adverse decision is made.</li>
                            </ul>
                            <p>Entities that provide this information to employers, landlords, or mortgage brokers, among others, are known as Consumer Reporting Agencies (CRAs).</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">2. Identity Search AI and the FCRA</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>According to the Fair Credit Reporting Act (FCRA), Identity Search AI is not a Consumer Reporting Agency. This means there are a number of restrictions on how you can use Identity Search AI. Below is a brief summary of these restrictions. We encourage you to educate yourself on the FCRA.</p>
                            <p>Identity Search AI was built for the everyday consumer to access public records, both to look up themselves and others.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">3. Prohibited Uses</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>You are strictly restricted from using Identity Search AI for:</p>
                            <ul class="list-disc ml-6 space-y-2">
                                <li><b>Employment Screening:</b> Evaluating a person for employment, reassignment, promotion, or retention.</li>
                                <li><b>Hiring Household Workers:</b> Including, but not limited to, nannies and domestic workers.</li>
                                <li><b>Tenant Screening:</b> Including, but not limited to, leasing a residential or commercial space.</li>
                                <li><b>Educational Qualification:</b> Assessing a person's qualifications for an educational program or scholarship.</li>
                                <li><b>Credit or Insurance:</b> Assessing the risk of existing credit obligations or determining eligibility for issuing credit or insurance.</li>
                                <li><b>Business Transactions:</b> Reviewing a personal customer account to determine whether the person continues to meet the terms of the account.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">4. Consequences of Violation</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>Using Identity Search AI for any of these purposes violates both our Terms &amp; Conditions and the law, which can lead to civil and criminal penalties. We take this very seriously and reserve the right to terminate user accounts and/or report violators to law enforcement.</p>
                            <p>If you are not sure whether your desired use of information obtained from Identity Search AI complies with these restrictions, please contact us at support@identitysearch.ai before conducting any queries.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const container = document.getElementById("fcraAccordionContainer");
        if (!container) return;
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
