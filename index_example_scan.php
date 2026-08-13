<?php
/**
 * Identity Search AI — Example AI Scan Section
 * File: index_example_scan.php
 * Animated scan process showing the AI analysis in action.
 */
?>
<section class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 py-10 md:py-16 relative overflow-hidden">
    <!-- Background decorations -->
    <div class="absolute inset-0 -z-10 pointer-events-none">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[900px] h-[500px] bg-gradient-to-b from-[#0072bc]/10 to-transparent rounded-full blur-3xl"></div>
    </div>

    <!-- Section Header -->
    <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16 animate-on-scroll">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-[#0072bc]/20 bg-[#0072bc]/10 text-xs font-bold text-[#0072bc] tracking-wide shadow-sm">
            <span class="w-2 h-2 rounded-full bg-[#0072bc]"></span>
            See It In Action
        </div>

        <h2 class="mt-5 text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 tracking-tight leading-tight">
            Watch the AI Analyze an Identity
        </h2>

        <p class="mt-3 text-xs sm:text-sm text-gray-500 font-medium leading-relaxed">
            From raw search query to complete intelligence report — see how the system processes identity signals in real time.
        </p>
    </div>

    <!-- Scan Animation Card -->
    <div class="max-w-2xl mx-auto animate-on-scroll group">
        <div class="relative bg-white rounded-[2rem] border border-blue-100/80 shadow-[0_30px_80px_rgba(0,114,188,0.15)] overflow-hidden">
            <!-- Scan line animation -->
            <div class="absolute inset-x-0 h-0.5 bg-gradient-to-r from-transparent via-[#0072bc] to-transparent scan-line z-10"></div>

            <!-- Card header -->
            <div class="flex items-center justify-between px-5 sm:px-7 py-4 border-b border-gray-100 bg-gradient-to-r from-slate-50 to-blue-50/50">
                <div class="flex items-center gap-3">
                    <div class="flex gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-red-400"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                        <span class="w-3 h-3 rounded-full bg-green-400"></span>
                    </div>
                    <span class="text-xs font-bold text-gray-500 tracking-wide">AI SCAN PROCESS</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full bg-[#0072bc]/10 text-[#0072bc] text-[10px] font-black tracking-wide animate-pulse">
                        <i class="fa-solid fa-circle text-[6px] mr-1"></i>LIVE
                    </span>
                </div>
            </div>

            <div class="p-5 sm:p-8">
                <!-- Query input display -->
                <div class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Scanning identity</p>
                        <p class="text-sm font-black text-gray-900 truncate">John Carter</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-full bg-[#0072bc] text-white text-[10px] font-black">87%</span>
                </div>

                <!-- Progress steps -->
                <div class="mt-6 space-y-3">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-green-50/50 border border-green-100">
                        <div class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-check text-[10px]"></i>
                        </div>
                        <span class="text-sm font-bold text-gray-700">Searching public web</span>
                        <span class="ml-auto text-[10px] font-black text-green-600">DONE</span>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-xl bg-green-50/50 border border-green-100">
                        <div class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-check text-[10px]"></i>
                        </div>
                        <span class="text-sm font-bold text-gray-700">Finding social profiles</span>
                        <span class="ml-auto text-[10px] font-black text-green-600">DONE</span>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-xl bg-green-50/50 border border-green-100">
                        <div class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-check text-[10px]"></i>
                        </div>
                        <span class="text-sm font-bold text-gray-700">Matching identity signals</span>
                        <span class="ml-auto text-[10px] font-black text-green-600">DONE</span>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-xl bg-blue-50/50 border border-blue-100">
                        <div class="w-7 h-7 rounded-full bg-[#0072bc] text-white flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-spinner animate-spin text-[10px]"></i>
                        </div>
                        <span class="text-sm font-bold text-gray-700">Analyzing digital footprint</span>
                        <span class="ml-auto text-[10px] font-black text-[#0072bc]">IN PROGRESS</span>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 opacity-60">
                        <div class="w-7 h-7 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-file-lines text-[10px]"></i>
                        </div>
                        <span class="text-sm font-bold text-gray-500">Generating intelligence report</span>
                        <span class="ml-auto text-[10px] font-black text-gray-400">PENDING</span>
                    </div>
                </div>

                <!-- Progress bar -->
                <div class="mt-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-gray-500">Overall Progress</span>
                        <span class="text-xs font-black text-[#0072bc]">87%</span>
                    </div>
                    <div class="h-2.5 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full w-[87%] rounded-full bg-gradient-to-r from-[#0072bc] to-blue-400 relative">
                            <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                        </div>
                    </div>
                </div>

                <!-- Completion state (always visible) -->
                <div class="mt-6 p-4 rounded-2xl bg-gradient-to-r from-[#0072bc] to-blue-500 shadow-lg shadow-blue-200 text-center">
                    <p class="text-sm font-black text-white">Your identity report is ready</p>
                    <button class="mt-3 inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white text-[#0072bc] text-xs font-black hover:bg-blue-50 transition-colors">
                        <i class="fa-solid fa-file-lines"></i>
                        View Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Note -->
    <div class="text-center mt-8 animate-on-scroll">
        <p class="text-xs sm:text-sm text-gray-500 font-semibold max-w-xl mx-auto">
            This is a preview of the scan process. Start your own scan to see real-time intelligence analysis.
        </p>
    </div>

    <style>
        @keyframes scanLineMove {
            0% { top: 0; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        .scan-line {
            animation: scanLineMove 3s ease-in-out infinite;
        }
    </style>
</section>