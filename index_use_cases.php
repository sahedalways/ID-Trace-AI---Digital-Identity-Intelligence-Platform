<?php
/**
 * Identity Search AI — Use Cases Section
 * File: index_use_cases.php
 * 2x2 layout with different visual treatments for each use case.
 */
?>
<section class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 py-10 md:py-16 relative overflow-hidden">
    <!-- Background decorations -->
    <div class="absolute inset-0 -z-10 pointer-events-none">
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[1000px] h-[400px] bg-gradient-to-t from-[#BFE4FD]/20 to-transparent rounded-full blur-3xl"></div>
    </div>

    <!-- Section Header -->
    <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16 animate-on-scroll">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-[#0072bc]/20 bg-[#0072bc]/10 text-xs font-bold text-[#0072bc] tracking-wide shadow-sm">
            <span class="w-2 h-2 rounded-full bg-[#0072bc]"></span>
            Use Cases
        </div>

        <h2 class="mt-5 text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 tracking-tight leading-tight">
            Built for Real-World Decisions
        </h2>

        <p class="mt-3 text-xs sm:text-sm text-gray-500 font-medium leading-relaxed">
            From personal safety to business verification, Identity Search AI gives you the intelligence you need before you commit.
        </p>
    </div>

    <!-- 2x2 Use Case Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 lg:gap-6">

        <!-- Use Case 1: Personal Safety -->
        <div class="animate-on-scroll">
            <div class="group relative overflow-hidden bg-white hover:bg-blue-50/60 rounded-3xl border border-blue-100/60 shadow-sm hover:shadow-[0_20px_60px_rgba(0,114,188,0.12)] hover:-translate-y-1.5 transition-all duration-500 h-full">
                <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-bl from-blue-100/80 to-transparent rounded-bl-[100px] opacity-60 group-hover:opacity-100 group-hover:scale-[8] origin-top-right transition-all duration-700"></div>

                <div class="relative p-6 sm:p-8">
                    <div class="flex items-start justify-between">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#0072bc] to-blue-500 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-200 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-blue-50 text-[#0072bc] text-[10px] font-black border border-blue-100">01</span>
                    </div>

                    <h3 class="mt-5 text-lg font-black text-gray-900">
                        Personal Safety
                    </h3>
                    <p class="mt-2.5 text-sm text-gray-600 font-semibold leading-relaxed">
                        Understand someone's public digital footprint before engaging. Whether it's a new connection, a date, or a roommate, know who you're dealing with.
                    </p>

                    <!-- Example chip -->
                    <div class="mt-5 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-100 text-xs font-bold text-gray-600">
                        <i class="fa-solid fa-user-check text-[#0072bc]"></i>
                        "Is this person who they say they are?"
                    </div>
                </div>
            </div>
        </div>

        <!-- Use Case 2: Business Verification -->
        <div class="animate-on-scroll scroll-delay-100">
            <div class="group relative overflow-hidden bg-white hover:bg-purple-50/60 rounded-3xl border border-purple-100/60 shadow-sm hover:shadow-[0_20px_60px_rgba(147,51,234,0.12)] hover:-translate-y-1.5 transition-all duration-500 h-full">
                <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-bl from-purple-100/80 to-transparent rounded-bl-[100px] opacity-60 group-hover:opacity-100 group-hover:scale-[8] origin-top-right transition-all duration-700"></div>

                <div class="relative p-6 sm:p-8">
                    <div class="flex items-start justify-between">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 text-white flex items-center justify-center text-xl shadow-lg shadow-purple-200 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-purple-50 text-purple-600 text-[10px] font-black border border-purple-100">02</span>
                    </div>

                    <h3 class="mt-5 text-lg font-black text-gray-900">
                        Business Verification
                    </h3>
                    <p class="mt-2.5 text-sm text-gray-600 font-semibold leading-relaxed">
                        Research potential clients, partners, vendors, or business contacts. Validate legitimacy before signing contracts or extending credit.
                    </p>

                    <!-- Example chip -->
                    <div class="mt-5 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-100 text-xs font-bold text-gray-600">
                        <i class="fa-solid fa-building-shield text-purple-600"></i>
                        "Can I trust this business partner?"
                    </div>
                </div>
            </div>
        </div>

        <!-- Use Case 3: Online Fraud Awareness -->
        <div class="animate-on-scroll">
            <div class="group relative overflow-hidden bg-white hover:bg-amber-50/60 rounded-3xl border border-amber-100/60 shadow-sm hover:shadow-[0_20px_60px_rgba(245,158,11,0.12)] hover:-translate-y-1.5 transition-all duration-500 h-full">
                <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-bl from-amber-100/80 to-transparent rounded-bl-[100px] opacity-60 group-hover:opacity-100 group-hover:scale-[8] origin-top-right transition-all duration-700"></div>

                <div class="relative p-6 sm:p-8">
                    <div class="flex items-start justify-between">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 text-white flex items-center justify-center text-xl shadow-lg shadow-amber-200 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                            <i class="fa-solid fa-user-secret"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-[10px] font-black border border-amber-100">03</span>
                    </div>

                    <h3 class="mt-5 text-lg font-black text-gray-900">
                        Online Fraud Awareness
                    </h3>
                    <p class="mt-2.5 text-sm text-gray-600 font-semibold leading-relaxed">
                        Identify suspicious identity patterns and potential risk signals. Spot fake profiles, impersonation attempts, and scam indicators early.
                    </p>

                    <!-- Example chip -->
                    <div class="mt-5 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-100 text-xs font-bold text-gray-600">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                        "Is this profile legitimate or a scam?"
                    </div>
                </div>
            </div>
        </div>

        <!-- Use Case 4: Digital Reputation -->
        <div class="animate-on-scroll scroll-delay-100">
            <div class="group relative overflow-hidden bg-white hover:bg-teal-50/60 rounded-3xl border border-teal-100/60 shadow-sm hover:shadow-[0_20px_60px_rgba(20,184,166,0.12)] hover:-translate-y-1.5 transition-all duration-500 h-full">
                <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-bl from-teal-100/80 to-transparent rounded-bl-[100px] opacity-60 group-hover:opacity-100 group-hover:scale-[8] origin-top-right transition-all duration-700"></div>

                <div class="relative p-6 sm:p-8">
                    <div class="flex items-start justify-between">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-500 text-white flex items-center justify-center text-xl shadow-lg shadow-teal-200 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-teal-50 text-teal-600 text-[10px] font-black border border-teal-100">04</span>
                    </div>

                    <h3 class="mt-5 text-lg font-black text-gray-900">
                        Digital Reputation
                    </h3>
                    <p class="mt-2.5 text-sm text-gray-600 font-semibold leading-relaxed">
                        Discover where an identity appears across the public web. Monitor your own digital footprint or research someone else's online presence.
                    </p>

                    <!-- Example chip -->
                    <div class="mt-5 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-100 text-xs font-bold text-gray-600">
                        <i class="fa-solid fa-magnifying-glass-chart text-teal-500"></i>
                        "What does the web say about this person?"
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>