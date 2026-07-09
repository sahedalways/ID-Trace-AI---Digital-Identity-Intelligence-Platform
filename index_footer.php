<?php
/**
 * Identity Trace AI — Unified Landing Page Footer Component
 * File: index_footer.php
 * Context: Included at the bottom of index.php structures
 */
?>
<footer class="relative overflow-hidden w-full border-t border-gray-200 pt-14 pb-10">
    <!-- Background Gradient -->
    <div class="absolute inset-0 -z-10" style="background: linear-gradient(180deg, #ffffff 0%, #fafdfa 40%, #f5fcf7 60%, #ffffff 100%);"></div>
    <!-- Animated Blobs -->
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="blob-1 absolute -top-40 left-1/2 w-[900px] h-[900px] bg-emerald-50 rounded-full blur-[100px] opacity-40 -translate-x-1/2 will-change-transform"></div>
        <div class="blob-2 absolute top-10 -left-20 w-96 h-96 bg-[#128c7e]/5 rounded-full blur-[100px] will-change-transform"></div>
        <div class="blob-3 absolute -bottom-20 right-0 w-96 h-96 bg-emerald-50 rounded-full blur-[100px] opacity-30 will-change-transform"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Top Row -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 pb-10 border-b border-gray-100">

            <!-- Brand Column -->
            <div class="md:col-span-4 space-y-4">
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-[#128c7e] text-white flex items-center justify-center text-sm">
                        <i class="fa-solid fa-fingerprint"></i>
                    </span>
                    <span class="text-lg font-bold text-gray-900 tracking-tight">Identity Search <span class="text-[11px] font-bold bg-black text-white px-2 py-0.5 rounded-md ml-1 align-middle tracking-wider">AI</span></span>
                </div>
                <p class="text-sm text-gray-500 font-medium leading-relaxed max-w-xs">
                    AI-powered digital identity intelligence platform. Analyze public footprints and generate comprehensive reports.
                </p>
                <div class="flex items-center gap-3 pt-1">
                    <a href="mailto:support@idtrace.ai" class="w-9 h-9 rounded-full bg-emerald-50 text-[#128c7e] hover:bg-[#128c7e] hover:text-white flex items-center justify-center transition-all duration-200">
                        <i class="fa-solid fa-envelope text-xs"></i>
                    </a>
                    <a href="contact.php" class="w-9 h-9 rounded-full bg-emerald-50 text-[#128c7e] hover:bg-[#128c7e] hover:text-white flex items-center justify-center transition-all duration-200">
                        <i class="fa-solid fa-headset text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="md:col-span-2 space-y-4">
                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Quick Links</h4>
                <ul class="space-y-3">
                    <li><a href="buy-credit.php" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Pricing</a></li>
                    <li><a href="signin.php" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Login</a></li>
                    <li><a href="signin.php" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Sign Up</a></li>
                    <li><a href="contact.php" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Contact Us</a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div class="md:col-span-2 space-y-4">
                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Resources</h4>
                <ul class="space-y-3">
                    <li><a href="affiliate-portal.php" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Affiliate Portal</a></li>
                    <li><a href="terms.php" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Terms of Service</a></li>
                    <li><a href="privacy.php" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Privacy Policy</a></li>
                </ul>
            </div>

            <!-- Legal Disclaimer -->
            <div class="md:col-span-4 space-y-4">
                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest">FCRA & Legal Notice</h4>
                <div class="p-4 bg-emerald-50/60 rounded-2xl border border-emerald-100">
                    <p class="text-xs text-gray-600 font-medium leading-relaxed">
                        <strong class="text-gray-900"><i class="fa-solid fa-gavel mr-1 text-[#128c7e]"></i> Disclaimer:</strong>
                        Identity Search AI functions strictly as an OSINT directory interface and does not compile consumer reporting statistics under the FCRA. Data processed here cannot be used for credit, tenancy, employment screening, or insurance decisions. Operators assume full responsibility for local jurisdiction compliance.
                    </p>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6">
            <p class="text-xs text-gray-400 font-semibold">
                &copy; 2026 Identity Search AI. All rights reserved. Developed and Designed by <a href="https://sahedahmed.netlify.app/" target="_blank" class="text-gray-500 hover:text-[#128c7e] transition-colors">Enostation IT</a>.
            </p>
            <div class="flex items-center gap-4 text-xs text-gray-400 font-semibold">
                <a href="terms.php" class="hover:text-[#128c7e] transition-colors">Terms</a>
                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                <a href="privacy.php" class="hover:text-[#128c7e] transition-colors">Privacy</a>
                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                <a href="contact.php" class="hover:text-[#128c7e] transition-colors">Support</a>
            </div>
        </div>
    </div>
</footer>
