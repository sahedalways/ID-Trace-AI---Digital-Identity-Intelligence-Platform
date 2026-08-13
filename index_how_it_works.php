<?php
/**
 * Identity Search AI — How Identity Search Works Section
 * File: index_how_it_works.php
 * Three-step visual process explaining the product workflow.
 */
?>
<section class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 py-10 md:py-16 relative overflow-hidden">
    <!-- Background decorations -->
    <div class="absolute inset-0 -z-10 pointer-events-none">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[1200px] h-[600px] bg-gradient-to-r from-[#0072bc]/5 via-blue-100/10 to-[#0072bc]/5 rounded-full blur-3xl"></div>
    </div>

    <!-- Section Header -->
    <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16 animate-on-scroll">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-[#0072bc]/20 bg-[#0072bc]/10 text-xs font-bold text-[#0072bc] tracking-wide shadow-sm">
            <span class="w-2 h-2 rounded-full bg-[#0072bc]"></span>
            How It Works
        </div>

        <h2 class="mt-5 text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 tracking-tight leading-tight">
            How Identity Search Works
        </h2>

        <p class="mt-3 text-xs sm:text-sm text-gray-500 font-medium leading-relaxed">
            From a single search query to a complete intelligence report in three simple steps.
        </p>
    </div>

    <!-- Three-Step Process -->
    <div class="relative">
        <!-- Connecting line (desktop) -->
        <div class="hidden lg:block absolute top-[72px] left-[16%] right-[16%] h-0.5 bg-gradient-to-r from-[#0072bc]/10 via-[#0072bc]/40 to-[#0072bc]/10"></div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-6 xl:gap-10">

            <!-- Step 01 -->
            <div class="relative animate-on-scroll">
                <div class="group relative bg-white/80 backdrop-blur border border-blue-100/60 rounded-3xl p-6 sm:p-8 shadow-sm hover:shadow-[0_20px_60px_rgba(0,114,188,0.12)] hover:-translate-y-1.5 transition-all duration-500 h-full overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-bl from-[#BFE4FD]/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                    <div class="relative">
                        <!-- Step number badge -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#0072bc] to-blue-500 text-white flex items-center justify-center text-xl font-black shadow-lg shadow-blue-200 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                                01
                            </div>
                            <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-[#0072bc] group-hover:bg-[#0072bc] group-hover:text-white transition-all duration-500">
                                <i class="fa-solid fa-user-pen text-sm"></i>
                            </div>
                        </div>

                        <h3 class="text-lg font-black text-gray-900">
                            Enter Identity
                        </h3>
                        <p class="mt-3 text-sm text-gray-600 font-semibold leading-relaxed">
                            Enter a name, username, email, phone, or other available information to begin the intelligence scan.
                        </p>

                        <!-- Mini input mockup -->
                        <div class="mt-6 p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center gap-2.5">
                            <i class="fa-solid fa-magnifying-glass text-[#0072bc] text-xs"></i>
                            <span class="text-xs text-gray-400 font-semibold truncate">John Carter</span>
                            <span class="ml-auto w-6 h-6 rounded-full bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Arrow connector (mobile) -->
                <div class="lg:hidden flex justify-center py-3 text-[#0072bc]/40">
                    <i class="fa-solid fa-arrow-down-long text-lg"></i>
                </div>
            </div>

            <!-- Step 02 -->
            <div class="relative animate-on-scroll scroll-delay-100">
                <div class="group relative bg-white/80 backdrop-blur border border-blue-100/60 rounded-3xl p-6 sm:p-8 shadow-sm hover:shadow-[0_20px_60px_rgba(0,114,188,0.12)] hover:-translate-y-1.5 transition-all duration-500 h-full overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-bl from-[#BFE4FD]/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#0072bc] to-blue-500 text-white flex items-center justify-center text-xl font-black shadow-lg shadow-blue-200 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                                02
                            </div>
                            <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-[#0072bc] group-hover:bg-[#0072bc] group-hover:text-white transition-all duration-500">
                                <i class="fa-solid fa-microchip text-sm"></i>
                            </div>
                        </div>

                        <h3 class="text-lg font-black text-gray-900">
                            AI Scans the Web
                        </h3>
                        <p class="mt-3 text-sm text-gray-600 font-semibold leading-relaxed">
                            The intelligence engine analyzes public digital footprints and relevant sources across the web.
                        </p>

                        <!-- Mini scan animation mockup -->
                        <div class="mt-6 p-3 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#0072bc] animate-pulse"></span>
                                <div class="h-1.5 flex-1 rounded-full bg-blue-100 overflow-hidden">
                                    <div class="h-full w-2/3 rounded-full bg-gradient-to-r from-[#0072bc] to-blue-400"></div>
                                </div>
                                <span class="text-[10px] text-gray-400 font-bold">66%</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                                <div class="h-1.5 flex-1 rounded-full bg-blue-100 overflow-hidden">
                                    <div class="h-full w-1/3 rounded-full bg-gradient-to-r from-blue-400 to-blue-300"></div>
                                </div>
                                <span class="text-[10px] text-gray-400 font-bold">33%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Arrow connector (mobile) -->
                <div class="lg:hidden flex justify-center py-3 text-[#0072bc]/40">
                    <i class="fa-solid fa-arrow-down-long text-lg"></i>
                </div>
            </div>

            <!-- Step 03 -->
            <div class="relative animate-on-scroll scroll-delay-200">
                <div class="group relative bg-white/80 backdrop-blur border border-blue-100/60 rounded-3xl p-6 sm:p-8 shadow-sm hover:shadow-[0_20px_60px_rgba(0,114,188,0.12)] hover:-translate-y-1.5 transition-all duration-500 h-full overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-bl from-[#BFE4FD]/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#0072bc] to-blue-500 text-white flex items-center justify-center text-xl font-black shadow-lg shadow-blue-200 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                                03
                            </div>
                            <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-[#0072bc] group-hover:bg-[#0072bc] group-hover:text-white transition-all duration-500">
                                <i class="fa-solid fa-file-shield text-sm"></i>
                            </div>
                        </div>

                        <h3 class="text-lg font-black text-gray-900">
                            Get Your Intelligence Report
                        </h3>
                        <p class="mt-3 text-sm text-gray-600 font-semibold leading-relaxed">
                            Receive a structured report with discovered information and relevant identity signals.
                        </p>

                        <!-- Mini report mockup -->
                        <div class="mt-6 p-3 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-check text-[8px]"></i>
                                </span>
                                <span class="text-[11px] text-gray-600 font-bold">Report ready</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-file-lines text-[8px]"></i>
                                </span>
                                <span class="text-[11px] text-gray-600 font-bold">View intelligence</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>