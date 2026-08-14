<?php
/**
 * Identity Search AI — Unified Landing Page Footer Component
 * File: index_footer.php
 * Context: Included at the bottom of index.php structures
 */
?>
<footer class="relative overflow-hidden w-full text-white">
    <!-- Deep Navy Background -->
    <div class="absolute inset-0 -z-10" style="background: radial-gradient(1200px 500px at 80% -10%, rgba(37,99,235,0.16) 0%, transparent 60%), linear-gradient(180deg, #0B0F19 0%, #0E1426 100%);"></div>
    <!-- Glow Accents -->
    <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="blob-1 absolute -top-40 left-1/3 w-[700px] h-[700px] bg-[#0072bc]/15 rounded-full blur-[120px] will-change-transform"></div>
        <div class="blob-2 absolute -bottom-40 -right-20 w-[500px] h-[500px] bg-[#2563EB]/10 rounded-full blur-[120px] will-change-transform"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-8">

        <!-- Top Row: 4-Column Grid -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-10 md:gap-12 pb-12 border-b border-white/10">

            <!-- Brand Column -->
            <div class="md:col-span-4 space-y-5">
                <div class="inline-flex items-center rounded-xl bg-white px-4 py-2.5 shadow-lg shadow-black/20">
                    <img src="<?php echo LOGO_URL; ?>" alt="Identity Search AI Logo" class="h-9 w-auto">
                </div>
                <p class="text-sm text-slate-300 font-medium leading-relaxed max-w-xs">
                    AI-powered digital identity intelligence platform. Analyze public footprints and generate comprehensive reports.
                </p>
                <div class="flex items-center gap-3">
                    <a href="mailto:support@identitysearch.ai" class="w-9 h-9 rounded-full bg-white/5 border border-white/10 text-slate-300 hover:bg-[#0072bc] hover:border-[#0072bc] hover:text-white flex items-center justify-center transition-all duration-200" title="Email Support">
                        <i class="fa-solid fa-envelope text-xs"></i>
                    </a>
                    <a href="contact" class="w-9 h-9 rounded-full bg-white/5 border border-white/10 text-slate-300 hover:bg-[#0072bc] hover:border-[#0072bc] hover:text-white flex items-center justify-center transition-all duration-200" title="Contact Support">
                        <i class="fa-solid fa-headset text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Product Links -->
            <div class="md:col-span-2 space-y-4">
                <h4 class="text-xs font-bold text-white uppercase tracking-widest">Product</h4>
                <ul class="space-y-3">
                    <li><a href="buy-credit" class="text-sm text-slate-400 font-semibold hover:text-[#2563EB] transition-colors">Pricing</a></li>
                    <li><a href="index" class="text-sm text-slate-400 font-semibold hover:text-[#2563EB] transition-colors">Features</a></li>
                    <li><a href="opt-out" class="text-sm text-slate-400 font-semibold hover:text-[#2563EB] transition-colors">Opt-Out</a></li>
                    <li><a href="affiliate-portal" class="text-sm text-slate-400 font-semibold hover:text-[#2563EB] transition-colors">Affiliate Program</a></li>
                    <li><a href="test-login" class="text-sm text-slate-400 font-semibold hover:text-[#2563EB] transition-colors">Test Login</a></li>
                </ul>
            </div>

            <!-- Legal & Support -->
            <div class="md:col-span-2 space-y-4">
                <h4 class="text-xs font-bold text-white uppercase tracking-widest">Legal & Support</h4>
                <ul class="space-y-3">
                    <li><a href="terms" class="text-sm text-slate-400 font-semibold hover:text-[#2563EB] transition-colors">Terms of Service</a></li>
                    <li><a href="privacy" class="text-sm text-slate-400 font-semibold hover:text-[#2563EB] transition-colors">Privacy Policy</a></li>
                    <li><a href="fcra" class="text-sm text-slate-400 font-semibold hover:text-[#2563EB] transition-colors">FCRA Guide</a></li>
                    <li><a href="refund" class="text-sm text-slate-400 font-semibold hover:text-[#2563EB] transition-colors">Refund Policy</a></li>
                    <li><a href="contact" class="text-sm text-slate-400 font-semibold hover:text-[#2563EB] transition-colors">Contact Us</a></li>
                </ul>
            </div>

            <!-- FCRA Disclaimer Box -->
            <div class="md:col-span-4 space-y-4">
                <details open class="group rounded-2xl border border-white/10 bg-white/5 backdrop-blur overflow-hidden">
                    <summary class="flex items-center justify-between gap-3 px-4 py-3.5 cursor-pointer list-none select-none [&::-webkit-details-marker]:hidden">
                        <span class="text-xs font-bold text-white uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-gavel text-[#2563EB]"></i> FCRA & Legal Notice
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200 group-open:rotate-180"></i>
                    </summary>
                    <div class="px-4 pb-4">
                        <p id="fcraDisclaimerText" class="text-xs text-slate-300 font-medium leading-relaxed line-clamp-3">
                            <strong class="text-white">Disclaimer:</strong>
                            Identity Search AI functions strictly as an OSINT directory interface and does not compile consumer reporting statistics under the FCRA. You may not use our service or the information it provides to make decisions about consumer credit, employment, insurance, tenant screening, or any other purpose that would require FCRA compliance. Identity Search AI does not provide consumer reports and is not a consumer reporting agency. (These terms have special meanings under the Fair Credit Reporting Act, 15 USC 1681 et seq., ("Fair Credit Reporting Act"), which are incorporated herein by reference.) The information available on our website may not be 100% accurate, complete, or up to date, so do not use it as a substitute for your own due diligence, especially if you have concerns about a person's criminal history. Identity Search AI does not make any representation or warranty about the accuracy of the information available through our website or about the character or integrity of the person about whom you inquire. For more information governing permitted and prohibited uses, please review our <a href="https://identitysearch.ai/terms" class="text-[#2563EB] hover:underline" target="_blank" rel="noopener noreferrer">"Terms & Conditions"</a>.
                        </p>
                        <button id="fcraReadMoreBtn" type="button" onclick="toggleFcraDisclaimer()" class="mt-2.5 inline-flex items-center gap-1.5 text-xs font-bold text-[#2563EB] hover:underline cursor-pointer">
                            <span id="fcraReadMoreLabel">Read More</span>
                            <i id="fcraReadMoreIcon" class="fa-solid fa-chevron-down text-[10px]"></i>
                        </button>
                    </div>
                </details>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="pt-6">
            <p class="text-xs text-slate-400 font-semibold text-center">
                &copy; 2026 Identity Search AI. All rights reserved.
            </p>
        </div>
    </div>
</footer>

<script>
    function toggleFcraDisclaimer() {
        const text = document.getElementById('fcraDisclaimerText');
        const label = document.getElementById('fcraReadMoreLabel');
        const icon = document.getElementById('fcraReadMoreIcon');
        if (!text || !label || !icon) return;

        const isExpanded = text.classList.contains('line-clamp-none');
        if (isExpanded) {
            text.classList.remove('line-clamp-none');
            text.classList.add('line-clamp-3');
            label.textContent = 'Read More';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        } else {
            text.classList.add('line-clamp-none');
            text.classList.remove('line-clamp-3');
            label.textContent = 'Show Less';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        }
    }
</script>
