<?php
/**
 * File: refund.php
 * Billing Cancellation & Refund Policy â€” exact content from Billing Cancellation and Refund Policy.docx
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Billing Cancellation & Refund Policy â€” Identity Search AI</title>
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
                Billing &amp; Payments
            </div>

            <h1 class="mt-7 text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-[1.08] onload-anim onload-delay-100">
                <span class="spark-text">Subscription, Billing Cancellation and Refund Policy</span>
            </h1>

            <p class="mt-6 text-sm sm:text-base lg:text-lg text-black font-semibold max-w-2xl leading-relaxed mx-auto onload-anim onload-delay-200">
                Everything you need to know about subscriptions, recurring billing, cancellation and refunds.
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
                            PLEASE REVIEW THIS BILLING CANCELLATION AND REFUND POLICY CAREFULLY BEFORE PURCHASING OR RENEWING ANY SUBSCRIPTION. BY COMPLETING A PURCHASE, YOU AUTHORIZE US TO CHARGE YOUR SELECTED PAYMENT METHOD ON A RECURRING BASIS IN ACCORDANCE WITH YOUR SELECTED PLAN.
                        </p>
                        <p>
                            SUBSCRIPTION CHARGES ARE NON-REFUNDABLE EXCEPT AS EXPLICITLY DESCRIBED BELOW. UNLESS YOU CANCEL BEFORE THE NEXT RENEWAL DATE, YOUR SUBSCRIPTION WILL AUTOMATICALLY RENEW. PLEASE REVIEW THE CANCELLATION AND REFUND TERMS SET FORTH BELOW.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- REFUND CONTENT -->
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
                            <li><a href="#refund-section-1" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">1. Subscription &amp; Billing</a></li>
                            <li><a href="#refund-section-2" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">2. How do I cancel my account?</a></li>
                            <li><a href="#refund-section-3" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">3. How to cancel app subscription?</a></li>
                            <li><a href="#refund-section-4" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">4. How can I get a refund?</a></li>
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
                            <li><a href="#refund-section-1" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">1. Subscription &amp; Billing</a></li>
                            <li><a href="#refund-section-2" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">2. How do I cancel my account?</a></li>
                            <li><a href="#refund-section-3" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">3. How to cancel app subscription?</a></li>
                            <li><a href="#refund-section-4" class="terms-toc-link block text-sm font-semibold text-gray-600 leading-snug py-2 px-3 rounded-lg border-l-2 border-transparent hover:text-[#0072bc] hover:bg-[#0072bc]/5 transition-all duration-300">4. How can I get a refund?</a></li>
                        </ul>
                    </div>
                </div>
            </aside>

            <!-- Accordion -->
            <div class="space-y-4" id="refundAccordionContainer">

                <div id="refund-section-1" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">1</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">1. Subscription &amp; Billing</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>By selecting a membership plan (Monthly, Quarterly, Semi-Annual, or Annual) on Identity Search AI, you expressly authorize us to charge your payment method immediately for the initial term and automatically on a recurring basis at the start of each renewal period until you cancel.</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li><b>Billing Cycles:</b> Depending on the selected plan, your card will be charged every 30 days ($36/month), every 3 months ($55/quarter), every 6 months ($72/semi-annually), or every 12 months ($96/annually).</li>
                                <li><b>Billing Descriptor:</b> Charges will appear on your bank or credit card statement as "IDENTITYSEARCH.AI".</li>
                                <li><b>Cancellation:</b> You may cancel your subscription at any time to avoid future recurring billing through your account dashboard or by contacting support@identitysearch.ai at least 24 hours prior to your next renewal date.</li>
                                <li><b>30-Day Money-Back Guarantee:</b> If you are not satisfied with our service, you are eligible to request a refund within 30 days of your initial purchase by emailing support@identitysearch.ai.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div id="refund-section-2" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">2</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">2. How do I cancel my account?</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>Identity Search AI provides hassle-free online cancellation in many easy ways:</p>
                            <p>For best results, login to your account.</p>
                            <p>When logged in, visit the "Identity Search AI Account" page. Click "Subscription management" select "Cancel My Subscription."</p>
                            <p>or You may also cancel by emailing support@identitysearch.ai, providing your Email Address and indicating your wish to cancel.</p>
                        </div>
                    </div>
                </div>

                <div id="refund-section-3" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">3</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">3. How to cancel app subscription?</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-4 border-t border-gray-100">
                            <div>
                                <p class="font-black text-gray-900 mb-2"><i class="fa-brands fa-apple text-gray-800"></i> Subscribed through Apple Store</p>
                                <p>If you purchased your Identity Search AI subscription through the iOS app, your subscription is managed by Apple. Apple requires that you cancel your subscription through your Apple ID account. You can follow these steps to cancel your subscription:</p>
                                <ol class="list-decimal ml-6 mt-2 space-y-1">
                                    <li>On your device, open the Settings app.</li>
                                    <li>Tap your name.</li>
                                    <li>Tap Subscriptions.</li>
                                    <li>Tap the active Identity Search AI subscription.</li>
                                    <li>Tap Cancel Subscription. You might need to scroll down to find the Cancel Subscription button. If there is no Cancel button or you see an expiration message in red text, the subscription is already canceled.</li>
                                </ol>
                                <p class="mt-2">For more information and screenshots of these steps, please see <a href="https://support.apple.com/en-us/118223" target="_blank" class="text-[#0072bc] underline font-bold">https://support.apple.com/en-us/118223</a>. If you need assistance with this process, please contact Apple.</p>
                            </div>
                            <div>
                                <p class="font-black text-gray-900 mb-2"><i class="fa-brands fa-google text-blue-500"></i> Steps to cancel (iPhone or iPad):</p>
                                <ol class="list-decimal ml-6 space-y-1">
                                    <li>Open the Settings app.</li>
                                    <li>Tap your name.</li>
                                    <li>Tap Subscriptions.</li>
                                    <li>Tap the subscription that you want to manage.</li>
                                    <li>Tap Cancel Subscription. If you don't see Cancel, the subscription is already cancelled and won't renew.</li>
                                </ol>
                            </div>
                            <div>
                                <p class="font-black text-gray-900 mb-2">Subscribed through Google Play</p>
                                <p>The user can cancel the subscription by opening the Google Play app, tapping Account, then selecting Subscriptions and finally tapping the Cancel button.</p>
                                <p>When a user cancels the subscription, the user will still have access to the product until the current paid period expires.</p>
                                <p>Uninstalling the app will not automatically stop the subscription. The user must follow the described process to properly cancel the plan.</p>
                            </div>
                            <div>
                                <p class="font-black text-gray-900 mb-2">Steps to cancel (phone or tablet)</p>
                                <ol class="list-decimal ml-6 space-y-1">
                                    <li>On your Android device, go to your subscriptions in Google Play.</li>
                                    <li>Select the subscription you want to cancel.</li>
                                    <li>Tap Cancel subscription.</li>
                                    <li>Follow the instructions.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="refund-section-4" class="scroll-mt-28 bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 hover:ring-gray-900/10 transition-all duration-300 terms-card">
    <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none terms-toggle-trigger group">
        <span class="flex items-center gap-3 sm:gap-4 min-w-0">
            <span class="terms-number-badge w-10 h-10 shrink-0 rounded-xl bg-[#0072bc]/10 border border-[#0072bc]/20 text-[#0072bc] font-black flex items-center justify-center text-sm transition-all duration-300">4</span>
            <span class="text-base font-black text-gray-900 group-hover:text-[#0072bc] transition-colors leading-snug">4. How can I get a refund?</span>
        </span>
        <span class="w-7 h-7 shrink-0 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center group-hover:bg-[#0072bc] group-hover:border-[#0072bc] transition-all duration-300">
            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-all duration-300 transform rotate-180 group-hover:text-white"></i>
        </span>
    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-800 font-semibold leading-relaxed space-y-3 border-t border-gray-100">
                            <p>If you are unhappy with our service or the data provided, you are covered by our 30-Day Money-Back Guarantee. You may request a full refund within 30 days of your initial plan purchase by emailing us at support@identitysearch.ai.</p>
                            <p>Please note the following general guidelines regarding our refund policy:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li><b>Initial Purchases:</b> Refund requests submitted within 30 days of the first transaction will be processed promptly.</li>
                                <li><b>Recurring Renewals:</b> Subsequent automatic renewal charges (monthly, quarterly, semi-annual, or annual) are non-refundable once billed, unless requested prior to the renewal date.</li>
                                <li><b>Processing Time:</b> Refunds are processed immediately on our end, but depending on your financial institution, it may take 5 to 10 business days for funds to reflect in your account.</li>
                                <li><b>In-App Purchases:</b> For charges made through the Apple App Store or Google Play, refunds must be requested directly through Apple or Google according to their respective policies.</li>
                            </ul>
                            <p>Also please note the following general guidelines regarding our refund process:</p>
                            <p>Refunds are processed immediately on our end, but depending on your bank or financial institution, it may take up to 10 days for the refund to post to your bank. Feel free to contact us if you have any questions or want to confirm your refund.</p>
                            <p>For charges made through the Apple App Store or Google Play, you must request a refund through Apple or Google. Please see: Apple App Store Refunds or Google Play Refunds.</p>
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

        // REFUND ACCORDION ENGINE
        document.addEventListener("DOMContentLoaded", () => {
            const container = document.getElementById("refundAccordionContainer");
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
            const sections = document.querySelectorAll("#refundAccordionContainer .terms-card");
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