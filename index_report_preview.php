<?php
/**
 * Identity Search AI — What's Inside Your Report Section
 * File: index_report_preview.php
 * Large realistic report/dashboard preview showing the actual product output.
 */
?>
<section class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 py-10 md:py-16 relative overflow-hidden">
    <!-- Background decorations -->
    <div class="absolute inset-0 -z-10 pointer-events-none">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-gradient-to-b from-[#BFE4FD]/30 to-transparent rounded-full blur-3xl"></div>
    </div>

    <!-- Section Header -->
    <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16 animate-on-scroll">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-[#0072bc]/20 bg-[#0072bc]/10 text-xs font-bold text-[#0072bc] tracking-wide shadow-sm">
            <span class="w-2 h-2 rounded-full bg-[#0072bc]"></span>
            Inside Your Report
        </div>

        <h2 class="mt-5 text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 tracking-tight leading-tight">
            What's Inside Your Intelligence Report
        </h2>

        <p class="mt-3 text-xs sm:text-sm text-gray-500 font-medium leading-relaxed">
            A structured, AI-generated dossier that turns scattered public data into actionable identity intelligence.
        </p>
    </div>

    <!-- Report Dashboard Preview -->
    <div class="relative max-w-5xl mx-auto animate-on-scroll">
        <!-- Floating cards around the dashboard -->
        <div class="hidden md:block absolute -top-6 -left-8 z-20 float-anim">
            <div class="bg-white/90 backdrop-blur rounded-2xl border border-blue-100 shadow-lg shadow-blue-100/50 px-4 py-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                    <i class="fa-solid fa-shield-halved text-sm"></i>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Risk Level</p>
                    <p class="text-sm font-black text-gray-900">Low</p>
                </div>
            </div>
        </div>

        <div class="hidden md:block absolute -top-8 right-0 z-20 float-anim" style="animation-delay: 1.5s;">
            <div class="bg-white/90 backdrop-blur rounded-2xl border border-blue-100 shadow-lg shadow-blue-100/50 px-4 py-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#0072bc] flex items-center justify-center">
                    <i class="fa-solid fa-globe text-sm"></i>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Sources</p>
                    <p class="text-sm font-black text-gray-900">32 Analyzed</p>
                </div>
            </div>
        </div>

        <div class="hidden md:block absolute -bottom-6 -left-10 z-20 float-anim" style="animation-delay: 2.5s;">
            <div class="bg-white/90 backdrop-blur rounded-2xl border border-blue-100 shadow-lg shadow-blue-100/50 px-4 py-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i class="fa-solid fa-user-group text-sm"></i>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Related</p>
                    <p class="text-sm font-black text-gray-900">6 Matches</p>
                </div>
            </div>
        </div>

        <div class="hidden md:block absolute -bottom-8 right-4 z-20 float-anim" style="animation-delay: 3.5s;">
            <div class="bg-white/90 backdrop-blur rounded-2xl border border-blue-100 shadow-lg shadow-blue-100/50 px-4 py-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Concerns</p>
                    <p class="text-sm font-black text-gray-900">3 Signals</p>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Card -->
        <div class="relative bg-white rounded-[2rem] border border-blue-100/80 shadow-[0_30px_80px_rgba(0,114,188,0.15)] overflow-hidden">
            <!-- Dashboard header bar -->
            <div class="flex items-center justify-between px-5 sm:px-7 py-4 border-b border-gray-100 bg-gradient-to-r from-slate-50 to-blue-50/50">
                <div class="flex items-center gap-3">
                    <div class="flex gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-red-400"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                        <span class="w-3 h-3 rounded-full bg-green-400"></span>
                    </div>
                    <span class="text-xs font-bold text-gray-500 tracking-wide hidden sm:block">IDENTITY INTELLIGENCE REPORT</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full bg-[#0072bc]/10 text-[#0072bc] text-[10px] font-black tracking-wide">AI GENERATED</span>
                    <i class="fa-solid fa-ellipsis-vertical text-gray-400 text-sm"></i>
                </div>
            </div>

            <div class="p-5 sm:p-7">
                <!-- Identity Profile Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-5 pb-6 border-b border-gray-100">
                    <div class="relative">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-[#0072bc] to-blue-500 flex items-center justify-center text-white text-2xl sm:text-3xl font-black shadow-lg shadow-blue-200">
                            JC
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-green-500 border-2 border-white flex items-center justify-center">
                            <i class="fa-solid fa-check text-white text-[8px]"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-lg sm:text-xl font-black text-gray-900">John Carter</h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-green-50 text-green-700 text-[10px] font-black border border-green-100">Verified</span>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 font-semibold mt-1">Identity Overview</p>
                        <div class="flex flex-wrap gap-2 mt-2.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-green-50 text-green-700 text-[10px] font-bold">
                                <i class="fa-solid fa-check text-[8px]"></i> Name matches
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-green-50 text-green-700 text-[10px] font-bold">
                                <i class="fa-solid fa-check text-[8px]"></i> Location signals
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-green-50 text-green-700 text-[10px] font-bold">
                                <i class="fa-solid fa-check text-[8px]"></i> Social profiles found
                            </span>
                        </div>
                    </div>
                    <div class="hidden sm:flex flex-col items-end gap-1.5">
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Match Score</span>
                        <div class="flex items-center gap-2">
                            <div class="w-24 h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full w-[92%] rounded-full bg-gradient-to-r from-[#0072bc] to-green-500"></div>
                            </div>
                            <span class="text-sm font-black text-gray-900">92%</span>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mt-6">
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-br from-blue-50 to-white border border-blue-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center">
                                <i class="fa-brands fa-facebook-f text-xs"></i>
                            </div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Social</span>
                        </div>
                        <p class="mt-2 text-xl sm:text-2xl font-black text-gray-900">12</p>
                        <p class="text-[10px] text-gray-500 font-semibold">Profiles</p>
                    </div>

                    <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-br from-purple-50 to-white border border-purple-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                                <i class="fa-solid fa-folder-open text-xs"></i>
                            </div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Records</span>
                        </div>
                        <p class="mt-2 text-xl sm:text-2xl font-black text-gray-900">8</p>
                        <p class="text-[10px] text-gray-500 font-semibold">Public</p>
                    </div>

                    <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-br from-teal-50 to-white border border-teal-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                                <i class="fa-solid fa-comment-dots text-xs"></i>
                            </div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Mentions</span>
                        </div>
                        <p class="mt-2 text-xl sm:text-2xl font-black text-gray-900">24</p>
                        <p class="text-[10px] text-gray-500 font-semibold">Web</p>
                    </div>

                    <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-br from-amber-50 to-white border border-amber-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                            </div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Risk</span>
                        </div>
                        <p class="mt-2 text-xl sm:text-2xl font-black text-gray-900">3</p>
                        <p class="text-[10px] text-gray-500 font-semibold">Signals</p>
                    </div>
                </div>

                <!-- Bottom Row: Related + Sources -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-4">
                    <!-- Related Identities -->
                    <div class="p-4 rounded-2xl border border-gray-100 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-wide">Related Identities</h4>
                            <span class="text-[10px] font-bold text-[#0072bc]">6 possible</span>
                        </div>
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 border-2 border-white flex items-center justify-center text-white text-[10px] font-black">JD</div>
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 border-2 border-white flex items-center justify-center text-white text-[10px] font-black">MC</div>
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-teal-400 to-teal-600 border-2 border-white flex items-center justify-center text-white text-[10px] font-black">RS</div>
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 border-2 border-white flex items-center justify-center text-white text-[10px] font-black">AK</div>
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-rose-400 to-rose-600 border-2 border-white flex items-center justify-center text-white text-[10px] font-black">+2</div>
                        </div>
                    </div>

                    <!-- Sources -->
                    <div class="p-4 rounded-2xl border border-gray-100 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-wide">Sources Analyzed</h4>
                            <span class="text-[10px] font-bold text-[#0072bc]">32 public</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="px-2 py-1 rounded-lg bg-white border border-gray-200 text-[10px] font-bold text-gray-600"><i class="fa-brands fa-facebook-f mr-1 text-[#0072bc]"></i>Facebook</span>
                            <span class="px-2 py-1 rounded-lg bg-white border border-gray-200 text-[10px] font-bold text-gray-600"><i class="fa-brands fa-instagram mr-1 text-pink-500"></i>Instagram</span>
                            <span class="px-2 py-1 rounded-lg bg-white border border-gray-200 text-[10px] font-bold text-gray-600"><i class="fa-brands fa-linkedin-in mr-1 text-blue-600"></i>LinkedIn</span>
                            <span class="px-2 py-1 rounded-lg bg-white border border-gray-200 text-[10px] font-bold text-gray-600"><i class="fa-brands fa-x-twitter mr-1 text-gray-800"></i>X</span>
                            <span class="px-2 py-1 rounded-lg bg-white border border-gray-200 text-[10px] font-bold text-gray-600"><i class="fa-brands fa-tiktok mr-1 text-gray-800"></i>TikTok</span>
                            <span class="px-2 py-1 rounded-lg bg-white border border-gray-200 text-[10px] font-bold text-gray-600">+27</span>
                        </div>
                    </div>
                </div>

                <!-- AI Summary -->
                <div class="mt-4 p-4 rounded-2xl bg-gradient-to-r from-[#0072bc]/5 to-blue-50/50 border border-blue-100/60">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#0072bc] text-white flex items-center justify-center shrink-0 shadow-md shadow-blue-200">
                            <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-gray-900">AI-Generated Summary</h4>
                            <p class="mt-1 text-xs text-gray-600 font-semibold leading-relaxed">
                                John Carter has a moderate digital footprint with 12 social profiles and 24 web mentions. Identity signals are consistent across platforms. 3 potential risk signals require review. 6 related identities were identified for further investigation.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>