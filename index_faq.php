<?php
/**
 * Identity Search AI — Landing Page FAQ Component Module
 * File: index_faq.php
 */
?>
<style>
    #faqAccordionContainer .active .faq-toggle-trigger span {
        color: #4fb3c9; /* brand teal */
    }
    #faqAccordionContainer .active .faq-toggle-trigger {
        padding-bottom: 0.75rem;
    }
    #faqAccordionContainer .active .faq-content-slider {
        padding-bottom: 0.25rem;
    }
</style>
<section class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 py-10 md:py-12 mb-8 border-t border-slate-800/50 relative z-10">
    <!-- FAQ Header -->
    <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-700/50 bg-slate-900/50 backdrop-blur-md text-xs font-bold text-slate-300 tracking-wide shadow-sm">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#4FB3C9] opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#3A93A8]"></span>
            </span>
            Frequently Asked Questions
        </div>

        <h2 class="mt-5 text-xl sm:text-2xl md:text-3xl font-bold text-white tracking-tight leading-tight drop-shadow-md">
            Still Have Questions?
        </h2>

        <p class="mt-3 text-xs sm:text-sm text-slate-400 font-medium leading-relaxed">
            Find quick answers about Identity Search AI, digital footprint scanning, reports, data sources, and payment policy.
        </p>
    </div>

    <!-- FAQ Accordion -->
    <div class="grid md:grid-cols-2 gap-4 md:gap-6" id="faqAccordionContainer">

        <div class="space-y-4">
            <!-- FAQ Item 1 -->
            <div class="group bg-slate-900/80 backdrop-blur-md border border-slate-700/50 rounded-3xl overflow-hidden shadow-lg hover:shadow-[0_0_20px_rgba(79,179,201,0.1)] hover:border-[#4FB3C9]/50 transition-all duration-300">
                <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none faq-toggle-trigger group">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-10 h-10 rounded-2xl bg-slate-800 text-[#4FB3C9] border border-slate-700 flex items-center justify-center shrink-0 shadow-inner group-hover:bg-[#4FB3C9] group-hover:text-white transition-all">
                            <i class="fa-solid fa-fingerprint text-sm"></i>
                        </div>

                        <span class="text-base font-bold text-slate-200 group-hover:text-[#4FB3C9] transition-colors leading-snug">
                            What is Identity Search AI
                        </span>
                    </div>

                    <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0 group-hover:bg-[#4FB3C9] group-hover:border-[#4FB3C9] transition-all">
                        <i class="fa-solid fa-chevron-down text-slate-400 group-hover:text-white text-xs transition-transform duration-300 transform rotate-0"></i>
                    </div>
                </button>

                <div class="faq-content-slider transition-all duration-300 ease-in-out bg-slate-900/80" style="max-height: 0; opacity: 0;">
                    <div class="px-5 sm:px-6 pb-6">
                        <div class="pl-0 sm:pl-14">
                            <p class="text-sm text-slate-400 font-medium leading-relaxed">
                                Identity Search AI is an advanced investigative profiling engine that aggregates, structures, and correlates publicly accessible OSINT parameters into highly cohesive intelligence dossiers in real time. Instead of manually parsing hours of scattered public items, our system automatically tracks footprints, cross-examines indicators, and maps the output into a single scannable canvas to help verify digital authenticity instantly.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="group bg-slate-900/80 backdrop-blur-md border border-slate-700/50 rounded-3xl overflow-hidden shadow-lg hover:shadow-[0_0_20px_rgba(79,179,201,0.1)] hover:border-[#4FB3C9]/50 transition-all duration-300">
                <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none faq-toggle-trigger group">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-10 h-10 rounded-2xl bg-slate-800 text-[#4FB3C9] border border-slate-700 flex items-center justify-center shrink-0 shadow-inner group-hover:bg-[#4FB3C9] group-hover:text-white transition-all">
                            <i class="fa-solid fa-magnifying-glass-chart text-sm"></i>
                        </div>

                        <span class="text-base font-bold text-slate-200 group-hover:text-[#4FB3C9] transition-colors leading-snug">
                            Why choose Identity Search AI over standard background lookups
                        </span>
                    </div>

                    <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0 group-hover:bg-[#4FB3C9] group-hover:border-[#4FB3C9] transition-all">
                        <i class="fa-solid fa-chevron-down text-slate-400 group-hover:text-white text-xs transition-transform duration-300 transform rotate-0"></i>
                    </div>
                </button>

                <div class="faq-content-slider transition-all duration-300 ease-in-out bg-slate-900/80" style="max-height: 0; opacity: 0;">
                    <div class="px-5 sm:px-6 pb-6">
                        <div class="pl-0 sm:pl-14">
                            <p class="text-sm text-slate-400 font-medium leading-relaxed">
                                Traditional screening checks take days or weeks and rely on stale historical datasets. Identity Search AI delivers real-time information processing across hundreds of live open-source vectors in roughly 2 minutes, allowing you to secure fast, accurate insights without hidden operational costs or delays.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="group bg-slate-900/80 backdrop-blur-md border border-slate-700/50 rounded-3xl overflow-hidden shadow-lg hover:shadow-[0_0_20px_rgba(79,179,201,0.1)] hover:border-[#4FB3C9]/50 transition-all duration-300">
                <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none faq-toggle-trigger group">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-10 h-10 rounded-2xl bg-slate-800 text-[#4FB3C9] border border-slate-700 flex items-center justify-center shrink-0 shadow-inner group-hover:bg-[#4FB3C9] group-hover:text-white transition-all">
                            <i class="fa-solid fa-layer-group text-sm"></i>
                        </div>

                        <span class="text-base font-bold text-slate-200 group-hover:text-[#4FB3C9] transition-colors leading-snug">
                            What makes our intelligence console different
                        </span>
                    </div>

                    <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0 group-hover:bg-[#4FB3C9] group-hover:border-[#4FB3C9] transition-all">
                        <i class="fa-solid fa-chevron-down text-slate-400 group-hover:text-white text-xs transition-transform duration-300 transform rotate-0"></i>
                    </div>
                </button>

                <div class="faq-content-slider transition-all duration-300 ease-in-out bg-slate-900/80" style="max-height: 0; opacity: 0;">
                    <div class="px-5 sm:px-6 pb-6">
                        <div class="pl-0 sm:pl-14 text-sm sm:text-base text-slate-400 font-medium leading-relaxed space-y-3">
                            <p>
                                By harnessing specialized automation arrays, we offer competitive performance metrics across multiple critical performance channels:
                            </p>

                            <ul class="space-y-3">
                                <li class="flex gap-3">
                                    <span class="mt-1 w-5 h-5 rounded-full bg-slate-800 text-[#4FB3C9] flex items-center justify-center shrink-0 shadow-inner">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </span>
                                    <span>
                                        <b class="text-slate-200">Live Infrastructure Verification:</b>
                                        We query active digital metrics loops dynamically on demand rather than storing cached snapshots.
                                    </span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="mt-1 w-5 h-5 rounded-full bg-slate-800 text-[#4FB3C9] flex items-center justify-center shrink-0 shadow-inner">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </span>
                                    <span>
                                        <b class="text-slate-200">Automated Reverse Analysis:</b>
                                        Features integrated reverse image search frameworks and skip-trace data tracking under a unified processing flow.
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <!-- FAQ Item 4 -->
            <div class="group bg-slate-900/80 backdrop-blur-md border border-slate-700/50 rounded-3xl overflow-hidden shadow-lg hover:shadow-[0_0_20px_rgba(79,179,201,0.1)] hover:border-[#4FB3C9]/50 transition-all duration-300">
                <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none faq-toggle-trigger group">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-10 h-10 rounded-2xl bg-slate-800 text-[#4FB3C9] border border-slate-700 flex items-center justify-center shrink-0 shadow-inner group-hover:bg-[#4FB3C9] group-hover:text-white transition-all">
                            <i class="fa-solid fa-file-lines text-sm"></i>
                        </div>

                        <span class="text-base font-bold text-slate-200 group-hover:text-[#4FB3C9] transition-colors leading-snug">
                            What information will I get in my generated dossier
                        </span>
                    </div>

                    <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0 group-hover:bg-[#4FB3C9] group-hover:border-[#4FB3C9] transition-all">
                        <i class="fa-solid fa-chevron-down text-slate-400 group-hover:text-white text-xs transition-transform duration-300 transform rotate-0"></i>
                    </div>
                </button>

                <div class="faq-content-slider transition-all duration-300 ease-in-out bg-slate-900/80" style="max-height: 0; opacity: 0;">
                    <div class="px-5 sm:px-6 pb-6">
                        <div class="pl-0 sm:pl-14">
                            <p class="text-sm text-slate-400 font-medium leading-relaxed">
                                The system compiles a multi-layered profile breakdown including personal information verification indicators, educational milestones, corporate career trails, public interest category metrics, and behavioral lifestyle markers.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 5 -->
            <div class="group bg-slate-900/80 backdrop-blur-md border border-slate-700/50 rounded-3xl overflow-hidden shadow-lg hover:shadow-[0_0_20px_rgba(79,179,201,0.1)] hover:border-[#4FB3C9]/50 transition-all duration-300">
                <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none faq-toggle-trigger group">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-10 h-10 rounded-2xl bg-slate-800 text-[#4FB3C9] border border-slate-700 flex items-center justify-center shrink-0 shadow-inner group-hover:bg-[#4FB3C9] group-hover:text-white transition-all">
                            <i class="fa-solid fa-database text-sm"></i>
                        </div>

                        <span class="text-base font-bold text-slate-200 group-hover:text-[#4FB3C9] transition-colors leading-snug">
                            Where are the background records gathered from
                        </span>
                    </div>

                    <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0 group-hover:bg-[#4FB3C9] group-hover:border-[#4FB3C9] transition-all">
                        <i class="fa-solid fa-chevron-down text-slate-400 group-hover:text-white text-xs transition-transform duration-300 transform rotate-0"></i>
                    </div>
                </button>

                <div class="faq-content-slider transition-all duration-300 ease-in-out bg-slate-900/80" style="max-height: 0; opacity: 0;">
                    <div class="px-5 sm:px-6 pb-6">
                        <div class="pl-0 sm:pl-14">
                            <p class="text-sm text-slate-400 font-medium leading-relaxed">
                                Data is meticulously extracted from open-source indices across a wide digital landscape, including major public social media platforms (Facebook, Instagram, LinkedIn, TikTok, Twitter/X), indexed public directories, reverse metadata archives, and global breach tracking registers.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 6 -->
            <div class="group bg-slate-900/80 backdrop-blur-md border border-slate-700/50 rounded-3xl overflow-hidden shadow-lg hover:shadow-[0_0_20px_rgba(79,179,201,0.1)] hover:border-[#4FB3C9]/50 transition-all duration-300">
                <button type="button" class="w-full p-5 sm:p-6 flex items-center justify-between gap-4 text-left focus:outline-none faq-toggle-trigger group">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-10 h-10 rounded-2xl bg-slate-800 text-[#4FB3C9] border border-slate-700 flex items-center justify-center shrink-0 shadow-inner group-hover:bg-[#4FB3C9] group-hover:text-white transition-all">
                            <i class="fa-solid fa-credit-card text-sm"></i>
                        </div>

                        <span class="text-base font-bold text-slate-200 group-hover:text-[#4FB3C9] transition-colors leading-snug">
                            What payment methods do you accept, and what is your refund policy
                        </span>
                    </div>

                    <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0 group-hover:bg-[#4FB3C9] group-hover:border-[#4FB3C9] transition-all">
                        <i class="fa-solid fa-chevron-down text-slate-400 group-hover:text-white text-xs transition-transform duration-300 transform rotate-0"></i>
                    </div>
                </button>

                <div class="faq-content-slider transition-all duration-300 ease-in-out bg-slate-900/80" style="max-height: 0; opacity: 0;">
                    <div class="px-5 sm:px-6 pb-6">
                        <div class="pl-0 sm:pl-14">
                            <p class="text-sm text-slate-400 font-medium leading-relaxed">
                                We accept all major global credit cards, debit channels, and secure digital wallet transactions. To ensure absolute platform assurance, all individual scan plans are fully backed by our risk-free 30-day money-back guarantee policy if results do not meet standard processing guidelines.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>