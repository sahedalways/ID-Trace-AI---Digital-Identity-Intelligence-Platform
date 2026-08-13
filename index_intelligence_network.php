<?php
/**
 * Identity Search AI — Digital Intelligence Network Section
 * File: index_intelligence_network.php
 * Visual identity intelligence graph with central profile and connected nodes.
 */
?>
<section class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 py-10 md:py-16 relative overflow-hidden">
    <!-- Background decorations -->
    <div class="absolute inset-0 -z-10 pointer-events-none">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[700px] bg-gradient-to-b from-[#0072bc]/10 via-blue-50/20 to-transparent rounded-full blur-3xl"></div>
    </div>

    <!-- Section Header -->
    <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16 animate-on-scroll">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-[#0072bc]/20 bg-[#0072bc]/10 text-xs font-bold text-[#0072bc] tracking-wide shadow-sm">
            <span class="w-2 h-2 rounded-full bg-[#0072bc]"></span>
            Intelligence Network
        </div>

        <h2 class="mt-5 text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 tracking-tight leading-tight">
            One Identity. Connected Across the Digital World.
        </h2>

        <p class="mt-3 text-xs sm:text-sm text-gray-500 font-medium leading-relaxed">
            Our AI maps every public signal connected to an identity — from social profiles to web mentions — into a single intelligence graph.
        </p>
    </div>

    <!-- Network Visualization -->
    <div class="relative max-w-4xl mx-auto animate-on-scroll">
        <!-- Circular scanning rings -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="w-[280px] h-[280px] sm:w-[380px] sm:h-[380px] rounded-full border border-[#0072bc]/10"></div>
            <div class="absolute w-[380px] h-[380px] sm:w-[520px] sm:h-[520px] rounded-full border border-[#0072bc]/5"></div>
            <div class="absolute w-[480px] h-[480px] sm:w-[660px] sm:h-[660px] rounded-full border border-[#0072bc]/5"></div>
        </div>

        <!-- Connection lines (SVG) -->
        <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 800 600" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g stroke="#0072bc" stroke-width="1" stroke-dasharray="4 4" opacity="0.3">
                <!-- Top -->
                <line x1="400" y1="300" x2="400" y2="80" />
                <!-- Top-right -->
                <line x1="400" y1="300" x2="620" y2="130" />
                <!-- Right -->
                <line x1="400" y1="300" x2="700" y2="300" />
                <!-- Bottom-right -->
                <line x1="400" y1="300" x2="620" y2="470" />
                <!-- Bottom -->
                <line x1="400" y1="300" x2="400" y2="520" />
                <!-- Bottom-left -->
                <line x1="400" y1="300" x2="180" y2="470" />
                <!-- Left -->
                <line x1="400" y1="300" x2="100" y2="300" />
                <!-- Top-left -->
                <line x1="400" y1="300" x2="180" y2="130" />
            </g>
        </svg>

        <!-- Central Identity Profile -->
        <div class="relative z-10 flex items-center justify-center pt-16 sm:pt-20 pb-16 sm:pb-20">
            <div class="relative">
                <!-- Pulsing glow -->
                <div class="absolute inset-0 rounded-full bg-[#0072bc]/20 blur-2xl animate-pulse"></div>

                <!-- Central node -->
                <div class="relative w-28 h-28 sm:w-36 sm:h-36 rounded-full bg-gradient-to-br from-[#0072bc] to-blue-600 border-4 border-white shadow-[0_20px_60px_rgba(0,114,188,0.4)] flex flex-col items-center justify-center text-white">
                    <span class="text-2xl sm:text-3xl font-black">JC</span>
                    <span class="text-[9px] sm:text-[10px] font-bold text-blue-100 mt-0.5">John Carter</span>
                </div>

                <!-- Verified badge -->
                <div class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-green-500 border-2 border-white flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-check text-white text-xs"></i>
                </div>
            </div>
        </div>

        <!-- Surrounding Nodes -->
        <div class="absolute inset-0 z-20 pointer-events-none">

            <!-- Social Profiles (Top) -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 pointer-events-auto">
                <div class="group bg-white/90 backdrop-blur rounded-2xl border border-blue-100 shadow-lg shadow-blue-100/40 px-4 py-3 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-default">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center">
                        <i class="fa-solid fa-users text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Social</p>
                        <p class="text-sm font-black text-gray-900">12 Profiles</p>
                    </div>
                </div>
            </div>

            <!-- Web Mentions (Top-right) -->
            <div class="absolute top-6 sm:top-10 right-0 sm:right-8 pointer-events-auto">
                <div class="group bg-white/90 backdrop-blur rounded-2xl border border-blue-100 shadow-lg shadow-blue-100/40 px-4 py-3 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-default">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 text-white flex items-center justify-center">
                        <i class="fa-solid fa-comment-dots text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Web</p>
                        <p class="text-sm font-black text-gray-900">24 Mentions</p>
                    </div>
                </div>
            </div>

            <!-- Public Records (Right) -->
            <div class="absolute top-1/2 -translate-y-1/2 right-0 pointer-events-auto">
                <div class="group bg-white/90 backdrop-blur rounded-2xl border border-blue-100 shadow-lg shadow-blue-100/40 px-4 py-3 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-default">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white flex items-center justify-center">
                        <i class="fa-solid fa-folder-open text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Records</p>
                        <p class="text-sm font-black text-gray-900">8 Public</p>
                    </div>
                </div>
            </div>

            <!-- Locations (Bottom-right) -->
            <div class="absolute bottom-6 sm:bottom-10 right-0 sm:right-8 pointer-events-auto">
                <div class="group bg-white/90 backdrop-blur rounded-2xl border border-blue-100 shadow-lg shadow-blue-100/40 px-4 py-3 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-default">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white flex items-center justify-center">
                        <i class="fa-solid fa-location-dot text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Locations</p>
                        <p class="text-sm font-black text-gray-900">3 Found</p>
                    </div>
                </div>
            </div>

            <!-- Emails (Bottom) -->
            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 pointer-events-auto">
                <div class="group bg-white/90 backdrop-blur rounded-2xl border border-blue-100 shadow-lg shadow-blue-100/40 px-4 py-3 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-default">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-500 to-rose-600 text-white flex items-center justify-center">
                        <i class="fa-solid fa-envelope text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Emails</p>
                        <p class="text-sm font-black text-gray-900">2 Found</p>
                    </div>
                </div>
            </div>

            <!-- Usernames (Bottom-left) -->
            <div class="absolute bottom-6 sm:bottom-10 left-0 sm:left-8 pointer-events-auto">
                <div class="group bg-white/90 backdrop-blur rounded-2xl border border-blue-100 shadow-lg shadow-blue-100/40 px-4 py-3 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-default">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white flex items-center justify-center">
                        <i class="fa-solid fa-at text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Usernames</p>
                        <p class="text-sm font-black text-gray-900">5 Found</p>
                    </div>
                </div>
            </div>

            <!-- Related Identities (Left) -->
            <div class="absolute top-1/2 -translate-y-1/2 left-0 pointer-events-auto">
                <div class="group bg-white/90 backdrop-blur rounded-2xl border border-blue-100 shadow-lg shadow-blue-100/40 px-4 py-3 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-default">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 text-white flex items-center justify-center">
                        <i class="fa-solid fa-user-group text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Related</p>
                        <p class="text-sm font-black text-gray-900">6 Matches</p>
                    </div>
                </div>
            </div>

            <!-- Risk Signals (Top-left) -->
            <div class="absolute top-6 sm:top-10 left-0 sm:left-8 pointer-events-auto">
                <div class="group bg-white/90 backdrop-blur rounded-2xl border border-blue-100 shadow-lg shadow-blue-100/40 px-4 py-3 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-default">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Risk</p>
                        <p class="text-sm font-black text-gray-900">3 Signals</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom note -->
    <div class="text-center mt-10 animate-on-scroll">
        <p class="text-xs sm:text-sm text-gray-500 font-semibold max-w-xl mx-auto">
            Every connection represents a verified public signal. Identity Search AI automatically discovers and maps these relationships for you.
        </p>
    </div>
</section>