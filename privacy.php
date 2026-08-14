<?php
/**
 * File: privacy.php
 * Privacy and Cookies Policy â€” exact content from Privacy Policy.docx
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Privacy Policy â€” Identity Search AI</title>
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

    <main class="relative flex-grow w-full mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20" style="max-width: 1600px;">
        <style>
            @keyframes blobMove1 {
                0%, 100% { transform: translateX(-50%) translateY(0); }
                25% { transform: translateX(-40%) translateY(-20px); }
                50% { transform: translateX(-50%) translateY(-10px); }
                75% { transform: translateX(-60%) translateY(10px); }
            }
            @keyframes blobMove2 {
                0%, 100% { transform: translate(0, 0); }
                33% { transform: translate(30px, -15px); }
                66% { transform: translate(-20px, 20px); }
            }
            @keyframes blobMove3 {
                0%, 100% { transform: translate(0, 0); }
                50% { transform: translate(-40px, -20px); }
            }
            .blob-1 { animation: blobMove1 18s ease-in-out infinite; }
            .blob-2 { animation: blobMove2 14s ease-in-out infinite; }
            .blob-3 { animation: blobMove3 16s ease-in-out infinite; }

            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .onload-anim { animation: fadeInUp 0.8s ease-out forwards; opacity: 0; }
            .onload-delay-100 { animation-delay: 100ms; }
            .onload-delay-200 { animation-delay: 200ms; }
            .onload-delay-300 { animation-delay: 300ms; }

            .animate-on-scroll {
                opacity: 0;
                transform: translateY(30px);
                transition: opacity 0.8s ease-out, transform 0.8s ease-out;
                will-change: opacity, transform;
            }
            .animate-on-scroll.is-visible { opacity: 1; transform: translateY(0); }

            @keyframes spark-sweep {
                0% { background-position: 0% 50%; }
                100% { background-position: 200% 50%; }
            }
            .spark-text {
                background: linear-gradient(135deg, #020617 35%, #0072bc 50%, #020617 65%);
                background-size: 200% auto;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                color: transparent;
                animation: spark-sweep 5s linear infinite;
            }

            .navbar-scrolled {
                background: linear-gradient(to right, #eef5ff 20%, #0072bc 100%);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            }
            #mainNavbar #siteNavbar {
                background: transparent !important;
                border-bottom: none !important;
                box-shadow: none !important;
            }
            #siteNavbar a,
            #siteNavbar button,
            #siteNavbar .text-black,
            #siteNavbar .text-slate-400,
            #siteNavbar .text-\[\#0072bc\] {
                transition: color 0.35s ease, background-color 0.35s ease, border-color 0.35s ease;
            }
            #mainNavbar.navbar-scrolled #siteNavbar a:not(#userDropdownMenu a, #mobileDrawer a),
            #mainNavbar.navbar-scrolled #siteNavbar button:not(#userDropdownMenu button, #mobileDrawer button),
            #mainNavbar.navbar-scrolled #siteNavbar .text-black:not(#userDropdownMenu, #mobileDrawer),
            #mainNavbar.navbar-scrolled #siteNavbar .text-slate-400:not(#userDropdownMenu .text-slate-400, #mobileDrawer .text-slate-400),
            #mainNavbar.navbar-scrolled #siteNavbar .text-\[\#0072bc\]:not(#userDropdownMenu .text-\[\#0072bc\], #mobileDrawer .text-\[\#0072bc\]) {
                color: #ffffff !important;
            }
            #mainNavbar.navbar-scrolled #siteNavbar a:hover:not(#userDropdownMenu a, #mobileDrawer a),
            #mainNavbar.navbar-scrolled #siteNavbar button:hover:not(#userDropdownMenu button, #mobileDrawer button, #mobileMenuButton) {
                color: #ffffff !important;
            }
            #mainNavbar.navbar-scrolled #siteNavbar #mobileMenuButton {
                color: #ffffff !important;
                border-color: rgba(255, 255, 255, 0.25);
            }
            #mainNavbar.navbar-scrolled #siteNavbar #mobileMenuButton:hover,
            #mainNavbar.navbar-scrolled #siteNavbar #userMenuButton:hover {
                background-color: rgba(255, 255, 255, 0.15);
                color: #ffffff !important;
            }

            /* Accordion card enhancements */
            .terms-card.active {
                border-color: rgba(0, 114, 188, 0.35);
                box-shadow: 0 12px 40px rgba(0, 114, 188, 0.12);
            }
            .terms-card.active .terms-number-badge {
                background-color: #0072bc;
                border-color: #0072bc;
                color: #ffffff;
                box-shadow: 0 4px 14px rgba(0, 114, 188, 0.35);
            }
            .terms-toc-link.active {
                color: #0072bc !important;
                background-color: rgba(0, 114, 188, 0.06);
                border-left-color: #0072bc;
            }
        </style>

        <!-- HERO -->
        <section class="max-w-3xl mx-auto text-center pt-6 sm:pt-10">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#0072bc]/10 backdrop-blur-md border border-[#0072bc]/20 text-xs font-semibold text-[#0072bc] tracking-wide shadow-lg shadow-[#0072bc]/10 hover:-translate-y-0.5 hover:shadow-[#0072bc]/25 transition-all duration-300 onload-anim">
                <span class="w-2 h-2 rounded-full bg-[#0072bc] shadow-[0_0_8px_rgba(18,140,126,0.9)] animate-pulse"></span>
                Privacy &amp; Compliance
            </div>

            <h1 class="mt-7 text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-[1.08] onload-anim onload-delay-100">
                <span class="spark-text">Privacy and Cookies Policy</span>
            </h1>

            <p class="mt-6 text-sm sm:text-base lg:text-lg text-black font-semibold max-w-2xl leading-relaxed mx-auto onload-anim onload-delay-200">
                How we process operational dashboard data, secure workspace profiles, and treat open-source records.
            </p>

            <div class="mt-6 flex items-center justify-center onload-anim onload-delay-300">
                <span class="inline-flex items-center gap-2.5 px-4 sm:px-5 py-2.5 rounded-full bg-white/80 backdrop-blur border border-white/60 shadow-sm ring-1 ring-black/5 text-xs font-bold text-gray-800">
                    <i class="fa-solid fa-calendar-check text-[#0072bc] text-sm"></i>
                    Last Updated: July 2026
                </span>
            </div>
        </section>

        <!-- LEGAL NOTICE BANNER -->
        <section class="max-w-7xl mx-auto mt-10 mb-4 animate-on-scroll">
            <div class="relative overflow-hidden p-6 sm:p-8 bg-gradient-to-br from-amber-50 via-amber-50 to-orange-50 border border-amber-200 rounded-3xl shadow-sm ring-1 ring-amber-900/5">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-amber-200/50 to-transparent rounded-bl-[80px] opacity-70 pointer-events-none"></div>
                <div class="relative flex items-start gap-3 sm:gap-4">
                    <div class="hidden sm:flex w-10 h-10 shrink-0 rounded-xl bg-amber-100 border border-amber-200 items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-lg"></i>
                    </div>
                    <div class="text-xs sm:text-sm text-amber-900 font-semibold leading-relaxed space-y-3">
                        <p>
                            PLEASE REVIEW THIS PRIVACY AND COOKIES POLICY CAREFULLY BEFORE USING OUR WEBSITE OR ANY OF OUR SERVICES. BY ACCESSING OR USING THE WEBSITE, YOU ACKNOWLEDGE THAT YOU HAVE READ, UNDERSTOOD, AND AGREE TO THE PRACTICES DESCRIBED IN THIS POLICY, INCLUDING OUR USE OF COOKIES AND THE PROCESSING OF YOUR PERSONAL DATA.
                        </p>
                        <p>
                            WE MAY UPDATE THIS POLICY FROM TIME TO TIME, AND ANY CHANGES WILL BE POSTED ON THIS PAGE. YOUR CONTINUED USE OF THE WEBSITE OR SERVICES AFTER SUCH CHANGES CONSTITUTES YOUR ACCEPTANCE OF THE REVISED POLICY. IF YOU DO NOT AGREE WITH ANY PART OF THIS POLICY, PLEASE DO NOT USE OUR WEBSITE OR SERVICES.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- PRIVACY CONTENT -->
        <section class="max-w-7xl mx-auto mt-8 mb-16 grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-8 lg:gap-12">

            <!-- Mobile / Tablet Table of Contents -->
            <div class="lg:hidden">
                <div class="bg-white/80 backdrop-blur border border-gray-200 rounded-2xl shadow-sm ring-1 ring-black/5 p-4">
                    <button type="button" id="tocMobileToggle" class="w-full flex items-center justify-between gap-3 text-left focus:outline-none cursor-pointer">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-list text-[#0072bc]"></i>
                            Table of Contents
                        </span>
                        <span class="w-6 h-6 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center">
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                        </span>
                    </button>
                    <div id="tocMobilePanel" class="hidden mt-3 pt-3 border-t border-gray-100">
                        <ul class="space-y-0.5">
                            <li><a href="#privacy-section-1" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">1. Introduction</a></li>
                            <li><a href="#privacy-section-2" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">2. Data Collection Frameworks</a></li>
                            <li><a href="#privacy-section-3" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">3. How We Utilize Collected Parameters</a></li>
                            <li><a href="#privacy-section-4" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">4. Purposes of Processing and Legal Bases</a></li>
                            <li><a href="#privacy-section-5" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">5. Data Sharing &amp; Third-Party Protection</a></li>
                            <li><a href="#privacy-section-6" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">6. Security &amp; Infrastructure Protection</a></li>
                            <li><a href="#privacy-section-7" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">7. User Privacy Rights &amp; Deletion Opt-Outs</a></li>
                            <li><a href="#privacy-section-8" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">8. Retaining and Deleting Personal Data</a></li>
                            <li><a href="#privacy-section-9" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">9. Security of Personal Data</a></li>
                            <li><a href="#privacy-section-10" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">10. Your Rights</a></li>
                            <li><a href="#privacy-section-11" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">11. Third Party Websites</a></li>
                            <li><a href="#privacy-section-12" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">12. Personal Data of Children</a></li>
                            <li><a href="#privacy-section-13" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">13. About Cookies</a></li>
                            <li><a href="#privacy-section-14" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">14. Cookies That We Use</a></li>
                            <li><a href="#privacy-section-15" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">15. Cookies Used by Our Service Providers</a></li>
                            <li><a href="#privacy-section-16" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">16. Our Details</a></li>
                            <li><a href="#privacy-section-17" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">17. State-Specific Privacy Rights</a></li>
                            <li><a href="#privacy-section-18" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">18. In Summary</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Sticky Table of Contents -->
            <aside class="hidden lg:block">
                <div class="sticky top-20 space-y-4">
                    <div class="bg-white/80 backdrop-blur border border-gray-200 rounded-2xl shadow-sm ring-1 ring-black/5 p-5">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-list text-[#0072bc]"></i>
                            Table of Contents
                        </p>
                        <ul class="space-y-0.5 max-h-[calc(100vh-11rem)] overflow-y-auto pr-1">
                            <li><a href="#privacy-section-1" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">1. Introduction</a></li>
                            <li><a href="#privacy-section-2" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">2. Data Collection Frameworks</a></li>
                            <li><a href="#privacy-section-3" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">3. How We Utilize Collected Parameters</a></li>
                            <li><a href="#privacy-section-4" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">4. Purposes of Processing and Legal Bases</a></li>
                            <li><a href="#privacy-section-5" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">5. Data Sharing &amp; Third-Party Protection</a></li>
                            <li><a href="#privacy-section-6" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">6. Security &amp; Infrastructure Protection</a></li>
                            <li><a href="#privacy-section-7" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">7. User Privacy Rights &amp; Deletion Opt-Outs</a></li>
                            <li><a href="#privacy-section-8" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">8. Retaining and Deleting Personal Data</a></li>
                            <li><a href="#privacy-section-9" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">9. Security of Personal Data</a></li>
                            <li><a href="#privacy-section-10" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">10. Your Rights</a></li>
                            <li><a href="#privacy-section-11" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">11. Third Party Websites</a></li>
                            <li><a href="#privacy-section-12" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">12. Personal Data of Children</a></li>
                            <li><a href="#privacy-section-13" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">13. About Cookies</a></li>
                            <li><a href="#privacy-section-14" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">14. Cookies That We Use</a></li>
                            <li><a href="#privacy-section-15" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">15. Cookies Used by Our Service Providers</a></li>
                            <li><a href="#privacy-section-16" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">16. Our Details</a></li>
                            <li><a href="#privacy-section-17" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">17. State-Specific Privacy Rights</a></li>
                            <li><a href="#privacy-section-18" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">18. In Summary</a></li>
                        </ul>
                    </div>
                </div>
            </aside>

            <!-- Accordion -->
            <div class="space-y-4" id="privacyAccordionContainer">

                <!-- 1 -->
                <div id="privacy-section-1" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">1</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">1. Introduction</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>1.1. IdentitySearch.ai (our website) is provided by CPABOSSAFFILIATE LLC, acting as Identity Search AI ('we', 'our' or 'us'). We are the controller of personal data obtained via our website, meaning we are the organization legally responsible for deciding how and for what purposes it is used. You can find out more about us in Section 16.</p>
                            <p>1.2. We are committed to safeguarding the privacy of our website visitors, service users, individual customers, customer personnel, individual contractors, consultants, and freelancers.</p>
                            <p>1.3. Our website incorporates privacy controls that allow you to manage the use of cookies and similar technologies. By adjusting these settings, you can choose which types of cookies are permitted (for example, performance or targeting cookies). You can access these controls at any time through the "Cookie Settings" button in the footer of our website.</p>
                            <p>1.4. We use cookies on our website. Insofar as those cookies are not strictly necessary for the provision of our website and services, we will ask you to consent to our use of cookies when you first visit our website.</p>
                        </div>
                    </div>
                </div>

                <!-- 2 -->
                <div id="privacy-section-2" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">2</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">2. Data Collection Frameworks</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>2.1. Account Metadata: When creating an account profile signature, we store your electronic mail address, submitted name configurations, and baseline geo-location values sent via network security layers.</p>
                            <p>2.2. Search Context Records: We temporarily log inbound lookups (e.g., target profile strings or full names) to systematically cycle automated profiling hooks across indexed public directories.</p>
                            <p>2.3. Payment Tokenization: Financial transactions are managed entirely by secure third-party billing providers. Our databases do not store raw credit card credentials or banking access indices.</p>
                            <p>2.4. We may process data enabling us to get in touch with you ("contact data"). The contact data may include your name, email address, telephone number, and postal address. The source of the contact data is you.</p>
                            <p>2.5. We may process your personal data in order to provide our data provision services ("service data"). The source of the service data is our third-party data providers, and it consists solely of information lawfully obtained from publicly available records and sources. This publicly available data may include:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>(a) Contact and Identity Information: Full name, previous names, aliases, addresses (current and historical), phone numbers, and email addresses.</li>
                                <li>(b) Relatives and Associates Information: Contact and identity information for possible relatives, associates, and neighbors.</li>
                                <li>(c) Social Media Data: Publicly available social media profiles, usernames, and identifiers.</li>
                                <li>(d) Other Public Information: Obituary records, unclaimed property information, and other legally accessible public data.</li>
                            </ul>
                            <p>2.6. We may process your personal data that are provided in the course of the use of our chatbot services and generated by our services in the course of such use ("chatbot data"). The chatbot data may include the personal data that you input into our chatbot. The source of the chatbot data is you and/or our services.</p>
                            <p>2.7. We may process information contained in or relating to any communication that you send to us or that we send to you ("communication data"). The communication data may include the communication content and metadata associated with the communication. Our website will generate the metadata associated with communications made using the website contact forms.</p>
                            <p>2.8. We may process data about your use of our website and services ("usage data"). The usage data may include your IP address, device ID, geographical location, browser type and version, operating system, referral source, length of visit, page views, and website navigation paths, as well as information about the timing, frequency, and pattern of your service use. The source of the usage data is our analytics tracking system, our advertising networks, and search information providers.</p>
                            <p>2.9. Please do not supply any other person's personal data to us, unless we prompt you to do so.</p>
                        </div>
                    </div>
                </div>

                <!-- 3 -->
                <div id="privacy-section-3" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">3</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">3. How We Utilize Collected Parameters</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>3.1. To initialize real-time open-source intelligence (OSINT) parsing mechanisms and compile coherent behavioral matrices for generated dossiers.</p>
                            <p>3.2. To authenticate dashboard logins using single-use security tokens (OTP) and securely deliver requested PDF data files or receipts straight to your inbox.</p>
                            <p>3.3. To safeguard the application console against excessive query flooding, scraping exploits, and automated profile mining attacks.</p>
                        </div>
                    </div>
                </div>

                <!-- 4 -->
                <div id="privacy-section-4" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">4</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">4. Purposes of Processing and Legal Bases</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>4.1. Identity Search AI acts as a search interface pipeline. The platform does not host, create, or maintain the underlying biographical entries returned inside our intelligence dossiers.</p>
                            <p>4.2. All analytical results are gathered dynamically on-demand from publicly searchable registers, social network tracks, metadata indexes, and open web directories.</p>
                            <p>4.3. Generated profiling history dossiers are automatically purged from our staging servers 30 days after creation to guarantee user query isolation.</p>
                            <p>4.4. Operations - We may process your personal data for the purposes of operating our website, the processing and fulfilment of orders, providing our services, generating invoices, bills and other payment-related documentation, and credit control. The legal basis for this processing is our legitimate interests, namely the proper administration of our website, services, suppliers and business.</p>
                            <p>4.5. Relationships and communications - We may process contact data, account data, transaction data and/or communication data for the purposes of managing our relationships, communicating with you (excluding communicating for the purposes of direct marketing) by email, SMS, mail, online chat and/or telephone, providing support services and complaint handling. The legal basis for this processing is our legitimate interests, namely communications with our website visitors, service users, individual customers and customer personnel, the maintenance of our relationships, enabling the use of our services and supplied services, and the proper administration of our website, services and business.</p>
                            <p>4.6. Marketing - We may process contact data, account data, customer relationship data, transaction data and/or usage data for the purposes of creating, targeting and sending direct marketing communications by email, making personalized suggestions and recommendations to you about our services that may be of interest to you, to deliver relevant website content and online advertisements to you and measure or understand the effectiveness of the advertising we serve to you. The legal basis for this processing is our legitimate interests (namely to carry out direct marketing, develop our services and grow our business to study how customers use our products/services, to develop them, to grow our business and to inform our marketing strategy) and (where we are required by law to obtain it) consent.</p>
                            <p>4.7. Record keeping - We may process your personal data for the purposes of creating and maintaining our databases, back-up copies of our databases and our business records generally. The legal basis for this processing is our legitimate interests, namely ensuring that we have access to all the information we need to properly and efficiently run our business in accordance with this policy.</p>
                            <p>4.8. Security - We may process your personal data for the purposes of security and the prevention of fraud and other criminal activity. The legal basis of this processing is our legitimate interests, namely the protection of our website, services and business, and the protection of others.</p>
                            <p>4.9. Insurance and risk management - We may process your personal data where necessary for the purposes of obtaining or maintaining insurance coverage, managing risks and/or obtaining professional advice. The legal basis for this processing is our legitimate interests, namely the proper protection of our business against risks.</p>
                            <p>4.10. Legal claims - We may process your personal data where necessary for the establishment, exercise or defense of legal claims, whether in court proceedings or in an administrative or out-of-court procedure. The legal basis for this processing is our legitimate interests, namely the protection and assertion of our legal rights, your legal rights and the legal rights of others.</p>
                            <p>4.11. Legal compliance and vital interests - We may also process your personal data where such processing is necessary for compliance with a legal obligation to which we are subject or in order to protect your vital interests or the vital interests of another natural person.</p>
                        </div>
                    </div>
                </div>

                <!-- 5 -->
                <div id="privacy-section-5" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">5</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">5. Data Sharing & Third-Party Protection</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>5.1. We do not sell, rent, lease, or lease-swap user dashboard logs or account lists to marketing networks, broker chains, or commercial advertising pools.</p>
                            <p>5.2. Operational metrics are only shared with verified system nodes (e.g., mail dispatch pathways, data routing providers, billing operators) strictly necessary to run the service interface.</p>
                            <p>5.3. We retain authority to disclose account variables exclusively if required to comply with binding court documentation, legal statutory requests, or active judicial processes.</p>
                        </div>
                    </div>
                </div>

                <!-- 6 -->
                <div id="privacy-section-6" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">6</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">6. Security & Infrastructure Protection</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>6.1. All incoming and outgoing data packages pass through high-tier Secure Socket Layer (SSL/TLS) encryption layers during active runtime processes.</p>
                            <p>6.2. Account authorization sequences leverage dynamic, single-use email verification tokens (OTP tokens) to eliminate risks linked to standard static password leaks or credential stuffing exploits.</p>
                            <p>6.3. While we enforce strict server monitoring protocols to isolate databases, no method of digital transmission over public routing channels can guarantee absolute, unbreachable protection metrics.</p>
                        </div>
                    </div>
                </div>

                <!-- 7 -->
                <div id="privacy-section-7" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">7</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">7. User Privacy Rights & Deletion Opt-Outs</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>7.1. Users maintain full authority to inspect, update, or completely erase their registered account signatures and historical trace structures from the active management panel.</p>
                            <p>7.2. If you want to request a manual deletion of your workspace profile or log history from all platform database nodes, you can file an explicit ticket request with our support desk at support@identitysearch.ai.</p>
                            <p>7.3. Once a profile signature removal request is confirmed, all associated user attributes are dropped immediately from our active production staging systems.</p>
                        </div>
                    </div>
                </div>

                <!-- 8 -->
                <div id="privacy-section-8" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">8</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">8. Retaining and Deleting Personal Data</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>8.1. This Section 8 sets out our data retention policies and procedures, which are designed to help ensure that we comply with our legal obligations in relation to the retention and deletion of personal data.</p>
                            <p>8.2. Personal data that we process for any purpose or purposes shall not be kept for longer than is necessary for that purpose or those purposes.</p>
                            <p>8.3. We will retain your personal data as follows:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>(a) Contact data (such as names, email addresses, phone numbers): retained for up to 2 years from last contact where used for marketing, and up to 10 years from last contact where held in customer relations records;</li>
                                <li>(b) Account data (such as login credentials, subscription information, and user account history): retained for up to 10 years from the date of account closure;</li>
                                <li>(c) Service data (data provided in connection with the performance of a contract): retained for up to 10 years from the end of the relevant contract;</li>
                                <li>(d) Chatbot data: retained for up to 10 years from the end of the relevant contract;</li>
                                <li>(e) Transaction data (such as payment records and expense records): retained for up to 10 years from the date of transaction. We do not retain full credit card numbers;</li>
                                <li>(f) Communication data (such as customer support requests, queries, and complaints): retained for up to 10 years from closure;</li>
                                <li>(g) Usage data (such as cookie/analytics data and system monitoring logs): generally retained for up to 2 years from collection, or up to 6 years in the case of security and system monitoring logs.</li>
                            </ul>
                            <p>8.4. Notwithstanding the other provisions of this Section 8, we may retain your personal data where such retention is necessary for compliance with a legal obligation to which we are subject, or in order to protect your vital interests or the vital interests of another natural person.</p>
                        </div>
                    </div>
                </div>

                <!-- 9 -->
                <div id="privacy-section-9" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">9</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">9. Security of Personal Data</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>9.1. We will take appropriate technical and organizational precautions to secure your personal data and to prevent the loss, misuse or alteration of your personal data.</p>
                            <p>9.2. We will store your personal data on secure servers, personal computers and mobile devices, and in secure manual record-keeping systems.</p>
                            <p>9.3. The following personal data will be stored by us in encrypted form: your name, contact information, and limited payment card details (such as the last four digits). We do not retain full credit card numbers.</p>
                            <p>9.4. Data relating to your enquiries and financial transactions that is sent from your web browser to our web server, or from our web server to your web browser, will be protected using encryption technology.</p>
                            <p>9.5. You acknowledge that the transmission of unencrypted (or inadequately encrypted) data over the internet is inherently insecure, and we cannot guarantee the security of data sent over the internet.</p>
                        </div>
                    </div>
                </div>

                <!-- 10 -->
                <div id="privacy-section-10" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">10</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">10. Your Rights</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>10.1. In this Section 10, we have listed the rights that you have under data protection law.</p>
                            <p>10.2. Your principal rights under data protection law are:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>(a) The right to access - you can ask for copies of your personal data;</li>
                                <li>(b) The right to rectification - you can ask us to rectify inaccurate personal data and to complete incomplete personal data. Please note that we pull service data straight from our third party data providers and any rectification requests will need to be actioned by them â€“ we will provide you with contact details for this purpose;</li>
                                <li>(c) The right to erasure - you can ask us to erase your personal data;</li>
                                <li>(d) The right to restrict processing - you can ask us to restrict the processing of your personal data;</li>
                                <li>(e) The right to object to processing - you can object to the processing of your personal data;</li>
                                <li>(f) The right to data portability - you can ask that we transfer your personal data to another organization or to you;</li>
                                <li>(g) The right to complain to a supervisory authority - you can complain about our processing of your personal data; and</li>
                                <li>(h) The right to withdraw consent - to the extent that the legal basis of our processing of your personal data is consent, you can withdraw that consent.</li>
                            </ul>
                            <p>10.3. These rights are subject to certain limitations and exceptions. You can learn more about the rights of data subjects by visiting https://edpb.europa.eu/our-work-tools/general-guidance/gdpr-guidelines-recommendations-best-practices_en and https://ico.org.uk/for-organizations/guide-to-data-protection/guide-to-the-general-data-protection-regulation-gdpr/individual-rights/.</p>
                            <p>10.4. You may exercise any of your rights in relation to your personal data by written notice to us, using the contact details set out below.</p>
                        </div>
                    </div>
                </div>

                <!-- 11 -->
                <div id="privacy-section-11" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">11</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">11. Third Party Websites</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>11.1. Our website includes hyperlinks to, and details of, third party websites.</p>
                            <p>11.2. In general we have no control over, and are not responsible for, the privacy policies and practices of third parties.</p>
                        </div>
                    </div>
                </div>

                <!-- 12 -->
                <div id="privacy-section-12" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">12</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">12. Personal Data of Children</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>12.1. Our website and services are targeted at persons over the age of 18.</p>
                            <p>12.2. If we have reason to believe that we hold personal data of a person under that age in our databases, we will delete that personal data.</p>
                        </div>
                    </div>
                </div>

                <!-- 13 -->
                <div id="privacy-section-13" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">13</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">13. About Cookies</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>13.1. A cookie is a file containing an identifier (a string of letters and numbers) that is sent by a web server to a web browser and is stored by the browser. The identifier is then sent back to the server each time the browser requests a page from the server.</p>
                            <p>13.2. Cookies may be either "persistent" cookies or "session" cookies: a persistent cookie will be stored by a web browser and will remain valid until its set expiry date, unless deleted by the user before the expiry date; a session cookie, on the other hand, will expire at the end of the user session, when the web browser is closed.</p>
                            <p>13.3. Cookies may not contain any information that personally identifies a user, but personal data that we store about you may be linked to the information stored in and obtained from cookies.</p>
                        </div>
                    </div>
                </div>

                <!-- 14 -->
                <div id="privacy-section-14" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">14</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">14. Cookies That We Use</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>14.1. We use cookies for the following purposes:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>(a) Authentication and status - we use cookies to identify you when you visit our website and as you navigate our website, and to help us determine if you are logged into our website;</li>
                                <li>(b) Personalization - we use cookies to store information about your preferences and to personalize our website for you;</li>
                                <li>(c) Security - we use cookies as an element of the security measures used to protect user accounts, including preventing fraudulent use of login credentials, and to protect our website and services generally;</li>
                                <li>(d) Analysis - we use cookies to help us to analyze the use and performance of our website and services; and</li>
                                <li>(e) Cookie consent - we use cookies to store your preferences in relation to the use of cookies more generally.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 15 -->
                <div id="privacy-section-15" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">15</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">15. Cookies Used by Our Service Providers</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>15.1. Our service providers use cookies and those cookies may be stored on your computer when you visit our website. In particular, we use:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>(a) Analytics cookies - We use providers such as Google Analytics to help us understand how visitors use our website, improve performance, and create usage reports. Analytics cookies may track information such as pages visited, time spent on site, and referral sources.</li>
                                <li>(b) Advertising cookies - We work with advertising partners such as Google Ads/AdSense and Meta (Facebook, Instagram) to deliver relevant ads, measure ad performance, and provide personalized marketing.</li>
                                <li>(c) Social media cookies - Platforms such as Meta (Facebook, Instagram), LinkedIn, and Twitter/X may set cookies to enable integration of their services, personalize content, or measure engagement with our website.</li>
                                <li>(d) Security and fraud prevention cookies - Tools such as Google reCAPTCHA and Cloudflare may set cookies to help protect our website from abuse, detect fraudulent activity, and ensure system security.</li>
                                <li>(e) Payment service cookies - Payment processors may set cookies to enable secure transactions and prevent fraud.</li>
                                <li>(f) Customer support and communication cookies - Tools such as Zendesk or similar providers may use cookies to enable live chat, help desk services, or in-site messaging.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 16 -->
                <div id="privacy-section-16" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">16</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">16. Our Details</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>16.1. This website is owned and operated by CPABOSSAFFILIATE LLC.</p>
                            <p>16.2. You can contact us at: support@identitysearch.ai.</p>
                        </div>
                    </div>
                </div>

                <!-- 17 -->
                <div id="privacy-section-17" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">17</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">17. State-Specific Privacy Rights</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>17.1. State consumer privacy laws may provide residents with additional rights regarding our use of consumers' personal information. If you are a resident of California, your privacy rights are described in the Notice for California Residents section below.</p>
                            <p>17.2. If you are a resident of Colorado, Connecticut, Delaware, Iowa, Maryland, Minnesota, Montana, Nebraska, New Hampshire, New Jersey, Oregon, Tennessee, Texas, Utah or Virginia, you have the following rights:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>(a) Right of Access. You have the right to confirm whether we process your personal information and to access your personal information in a portable and easily usable format.</li>
                                <li>(b) Right to Deletion. You have the right to delete certain personal information.</li>
                                <li>(c) Right to Opt-out. You have the right to opt-out of the processing of personal information for purposes of sales/sharing, targeted advertising, or in furtherance of decisions that produce legal or similarly significant effects.</li>
                                <li>(d) Right to Correction. You have the right to request the correction of inaccuracies in certain personal information, taking into account the nature and the purposes of the processing of the personal information.</li>
                            </ul>
                            <p>17.3. You or your authorized agent may submit a request to exercise your opt-out, access, or deletion rights by emailing us at privacy@identitysearch.ai. Requests to correct inaccurate personal information that we maintain may also be sent to privacy@identitysearch.ai.</p>
                            <p>17.4. To update or correct inaccuracies in your personal information that was provided to us as part of creating or maintaining your account, you may do so by accessing your My Account page and selecting "Edit account info," or by contacting us at privacy@identitysearch.ai.</p>
                        </div>
                    </div>
                </div>

                <!-- 18 -->
                <div id="privacy-section-18" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">18</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">18. In Summary</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>18.1. At no cost, you may request information each year regarding any disclosure of your Public or Personal information to third parties for their own direct marketing purposes during the preceding calendar year. You have the right not to be discriminated against for exercising any of the rights listed above. To request access to or deletion of your information, or to exercise any other data rights under California law, please contact us using one of the methods set forth above.</p>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>

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
                    navbar.classList.add("navbar-scrolled");
                } else {
                    navbar.classList.remove("navbar-scrolled");
                }
            };
            window.addEventListener("scroll", handleScroll, { passive: true });
            handleScroll();
        });

        // INTERSECTION OBSERVER FOR SCROLL REVEAL ANIMATIONS
        document.addEventListener("DOMContentLoaded", () => {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
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

        // PRIVACY ACCORDION ENGINE
        document.addEventListener("DOMContentLoaded", () => {
            const container = document.getElementById("privacyAccordionContainer");
            if (!container) return;
            container.querySelectorAll(".terms-content-slider").forEach(panel => {
                panel.style.maxHeight = panel.scrollHeight + "px";
            });
            container.addEventListener("click", (e) => {
                const trigger = e.target.closest(".terms-toggle-trigger");
                if (!trigger) return;
                const card = trigger.parentElement;
                const panel = card.querySelector(".terms-content-slider");
                const icon = trigger.querySelector("i");
                if (panel.style.maxHeight === "0px" || panel.style.maxHeight === "") {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                    panel.style.opacity = "1";
                    icon.style.transform = "rotate(180deg)";
                    card.classList.add("active");
                } else {
                    panel.style.maxHeight = "0px";
                    panel.style.opacity = "0";
                    icon.style.transform = "rotate(0deg)";
                    card.classList.remove("active");
                }
            });
        });

        // DOCUMENT INDEX SCROLL SPY
        document.addEventListener("DOMContentLoaded", () => {
            const links = document.querySelectorAll(".terms-toc-link");
            const sections = document.querySelectorAll("#privacyAccordionContainer .terms-card");
            if (!links.length || !sections.length) return;
            const spy = () => {
                let currentId = sections[0].id;
                sections.forEach(section => {
                    if (window.scrollY >= section.offsetTop - 180) {
                        currentId = section.id;
                    }
                });
                links.forEach(link => {
                    link.classList.toggle("active", link.getAttribute("href") === "#" + currentId);
                });
            };
            window.addEventListener("scroll", spy, { passive: true });
            spy();
        });

        // MOBILE TABLE OF CONTENTS TOGGLE
        document.addEventListener("DOMContentLoaded", () => {
            const toggle = document.getElementById("tocMobileToggle");
            const panel = document.getElementById("tocMobilePanel");
            const chevron = toggle?.querySelector(".fa-chevron-down");
            if (!toggle || !panel || !chevron) return;
            toggle.addEventListener("click", () => {
                const isOpen = !panel.classList.toggle("hidden");
                chevron.style.transform = isOpen ? "rotate(180deg)" : "rotate(0deg)";
                toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
            });
            panel.querySelectorAll(".terms-toc-link").forEach(link => {
                link.addEventListener("click", () => {
                    panel.classList.add("hidden");
                    chevron.style.transform = "rotate(0deg)";
                    toggle.setAttribute("aria-expanded", "false");
                });
            });
        });
    </script>
</body>
</html>