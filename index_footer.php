<?php
/**
 * Identity Search AI — Unified Landing Page Footer Component
 * File: index_footer.php
 * Context: Included at the bottom of index.php structures
 */
?>
<footer class="relative overflow-hidden w-full border-t border-slate-800/50 pt-14 pb-10 bg-slate-950">
    <!-- Animated Blobs -->
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="blob-1 absolute -top-40 left-1/2 w-[900px] h-[900px] bg-[#0B1F40]/60 rounded-full blur-[120px] -translate-x-1/2"></div>
        <div class="blob-2 absolute top-10 -left-20 w-96 h-96 bg-[#4FB3C9]/10 rounded-full blur-[120px]"></div>
        <div class="blob-3 absolute -bottom-20 right-0 w-96 h-96 bg-[#1E3A8A]/40 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Top Row -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 pb-10 border-b border-slate-800/50">

            <!-- Brand Column -->
            <div class="md:col-span-4 space-y-4">
                <div class="flex items-center gap-2.5">
                    <h2 class="text-2xl font-black text-white tracking-tight">Identity Search <span class="text-[#4FB3C9]">AI</span></h2>
                </div>
                <p class="text-sm text-slate-400 font-medium leading-relaxed max-w-xs">
                    AI-powered digital identity intelligence platform. Analyze public footprints and generate comprehensive reports.
                </p>
                <div class="flex items-center gap-3 pt-1">
                    <a href="mailto:support@identitysearch.ai" class="w-9 h-9 rounded-full bg-slate-900 border border-slate-800 text-[#4FB3C9] hover:bg-[#4FB3C9] hover:text-white flex items-center justify-center transition-all duration-300 shadow-[0_0_10px_rgba(79,179,201,0.1)]">
                        <i class="fa-solid fa-envelope text-xs"></i>
                    </a>
                    <a href="contact" class="w-9 h-9 rounded-full bg-slate-900 border border-slate-800 text-[#4FB3C9] hover:bg-[#4FB3C9] hover:text-white flex items-center justify-center transition-all duration-300 shadow-[0_0_10px_rgba(79,179,201,0.1)]">
                        <i class="fa-solid fa-headset text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="md:col-span-2 space-y-4">
                <h4 class="text-xs font-bold text-slate-200 uppercase tracking-widest">Quick Links</h4>
                <ul class="space-y-3">
                    <li><a href="buy-credit" class="text-sm text-slate-400 font-semibold hover:text-[#4FB3C9] transition-colors">Pricing</a></li>
                    <li><a href="signin" class="text-sm text-slate-400 font-semibold hover:text-[#4FB3C9] transition-colors">Login</a></li>
                    <li><a href="signin" class="text-sm text-slate-400 font-semibold hover:text-[#4FB3C9] transition-colors">Sign Up</a></li>
                    <li><a href="contact" class="text-sm text-slate-400 font-semibold hover:text-[#4FB3C9] transition-colors">Contact Us</a></li>
                    <li><a href="opt-out" class="text-sm text-slate-400 font-semibold hover:text-[#4FB3C9] transition-colors">Opt-Out</a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div class="md:col-span-2 space-y-4">
                <h4 class="text-xs font-bold text-slate-200 uppercase tracking-widest">Resources</h4>
                <ul class="space-y-3">
                    <li><a href="affiliate-portal" class="text-sm text-slate-400 font-semibold hover:text-[#4FB3C9] transition-colors">Affiliates</a></li>
                    <li><a href="terms" class="text-sm text-slate-400 font-semibold hover:text-[#4FB3C9] transition-colors">Terms of Service</a></li>
                    <li><a href="privacy" class="text-sm text-slate-400 font-semibold hover:text-[#4FB3C9] transition-colors">Privacy Policy</a></li>
                    <li><a href="fcra" class="text-sm text-slate-400 font-semibold hover:text-[#4FB3C9] transition-colors">Understanding the FCRA</a></li>
                    <li><a href="refund" class="text-sm text-slate-400 font-semibold hover:text-[#4FB3C9] transition-colors">Billing Cancellation & Refund Policy</a></li>
                </ul>
            </div>

            <!-- Legal Disclaimer -->
            <div class="md:col-span-4 space-y-4">
                <h4 class="text-xs font-bold text-slate-200 uppercase tracking-widest">FCRA & Legal Notice</h4>
                <div class="p-4 bg-slate-900/80 rounded-2xl border border-slate-700/50 backdrop-blur-md">
                    <p class="text-[11px] text-slate-400 font-medium leading-relaxed">
                        <strong class="text-slate-200"><i class="fa-solid fa-gavel mr-1 text-[#4FB3C9]"></i> Disclaimer:</strong>
                        Identity Search AI functions strictly as an OSINT directory interface and does not compile consumer reporting statistics under the FCRA. You may not use our service or the information it provides to make decisions about consumer credit, employment, insurance, tenant screening, or any other purpose that would require FCRA compliance. Identity Search AI does not provide consumer reports and is not a consumer reporting agency. The information available on our website may not be 100% accurate, complete, or up to date. For more information, please review our <a href="https://identitysearch.ai/terms" class="text-[#4FB3C9] hover:underline" target="_blank" rel="noopener noreferrer">"Terms &amp; Conditions"</a>.
                    </p>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6">
            <p class="text-xs text-slate-500 font-semibold">
                &copy; 2026 Identity Search AI. All rights reserved.
            </p>
            <div class="flex items-center gap-4 text-xs text-slate-500 font-semibold">
                <a href="terms" class="hover:text-[#4FB3C9] transition-colors">Terms</a>
                <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                <a href="privacy" class="hover:text-[#4FB3C9] transition-colors">Privacy</a>
                <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                <a href="contact" class="hover:text-[#4FB3C9] transition-colors">Support</a>
            </div>
        </div>
    </div>
</footer>