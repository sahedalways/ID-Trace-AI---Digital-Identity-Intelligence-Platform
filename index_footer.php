<?php
/**
 * Identity Search AI — Unified Landing Page Footer Component
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
                    <img src="public/logo.png" alt="Identity Search AI Logo" class="h-12 w-auto">
                </div>
                <p class="text-sm text-gray-500 font-medium leading-relaxed max-w-xs">
                    AI-powered digital identity intelligence platform. Analyze public footprints and generate comprehensive reports.
                </p>
                <div class="flex items-center gap-3 pt-1">
                    <a href="mailto:support@identitysearch.ai" class="w-9 h-9 rounded-full bg-emerald-50 text-[#128c7e] hover:bg-[#128c7e] hover:text-white flex items-center justify-center transition-all duration-200">
                        <i class="fa-solid fa-envelope text-xs"></i>
                    </a>
                    <a href="contact" class="w-9 h-9 rounded-full bg-emerald-50 text-[#128c7e] hover:bg-[#128c7e] hover:text-white flex items-center justify-center transition-all duration-200">
                        <i class="fa-solid fa-headset text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="md:col-span-2 space-y-4">
                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Quick Links</h4>
                <ul class="space-y-3">
                    <li><a href="buy-credit" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Pricing</a></li>
                    <li><a href="signin" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Login</a></li>
                    <li><a href="signin" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Sign Up</a></li>
                    <li><a href="contact" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Contact Us</a></li>
                    <li><a href="opt-out" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Opt-Out</a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div class="md:col-span-2 space-y-4">
                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Resources</h4>
                <ul class="space-y-3">
                    <li><a href="affiliate-portal" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Affiliates</a></li>
                    <li><a href="terms" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Terms of Service</a></li>
                    <li><a href="privacy" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Privacy Policy</a></li>
                    <li><a href="fcra" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Understanding the FCRA</a></li>
                    <li><a href="refund" class="text-sm text-gray-500 font-semibold hover:text-[#128c7e] transition-colors">Billing Cancellation & Refund Policy</a></li>
                </ul>
            </div>

            <!-- Legal Disclaimer -->
            <div class="md:col-span-4 space-y-4">
                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest">FCRA & Legal Notice</h4>
                <div class="p-4 bg-emerald-50/60 rounded-2xl border border-emerald-100">
                    <p class="text-xs text-gray-600 font-medium leading-relaxed">
                        <strong class="text-gray-900"><i class="fa-solid fa-gavel mr-1 text-[#128c7e]"></i> Disclaimer:</strong>
                        Identity Search AI functions strictly as an OSINT directory interface and does not compile consumer reporting statistics under the FCRA. You may not use our service or the information it provides to make decisions about consumer credit, employment, insurance, tenant screening, or any other purpose that would require FCRA compliance. Identity Search AI does not provide consumer reports and is not a consumer reporting agency. (These terms have special meanings under the Fair Credit Reporting Act, 15 USC 1681 et seq., ("Fair Credit Reporting Act"), which are incorporated herein by reference.) The information available on our website may not be 100% accurate, complete, or up to date, so do not use it as a substitute for your own due diligence, especially if you have concerns about a person's criminal history. Identity Search AI does not make any representation or warranty about the accuracy of the information available through our website or about the character or integrity of the person about whom you inquire. For more information governing permitted and prohibited uses, please review our "Terms &amp; Conditions" (<a href="https://identitysearch.ai/terms" class="text-[#128c7e] hover:underline" target="_blank" rel="noopener noreferrer">https://identitysearch.ai/terms</a>).
                    </p>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6">
            <p class="text-xs text-gray-400 font-semibold">
                &copy; 2026 Identity Search AI. All rights reserved.
            </p>
            <div class="flex items-center gap-4 text-xs text-gray-400 font-semibold">
                <a href="terms" class="hover:text-[#128c7e] transition-colors">Terms</a>
                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                <a href="privacy" class="hover:text-[#128c7e] transition-colors">Privacy</a>
                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                <a href="contact" class="hover:text-[#128c7e] transition-colors">Support</a>
            </div>
        </div>
    </div>
</footer>