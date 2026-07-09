<?php
/**
 * File: affiliate-portal.php
 * Landing Index Homepage for the Identity Search AI Affiliate Network.
 * Markets network value propositions and routes users to authentication vectors.
 * Verified layout synchronized directly with global system UI dimensions.
 */
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Affiliate Partner Program — PartnerTerminal</title>
    <?php include 'affiliate-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-between selection:bg-[#128c7e] selection:text-white bg-[#f9fafb]">

    <?php include 'affiliate-navbar.php'; ?>

    <main class="w-full grow">
        
        <!-- HERO VALUE STATEMENT SECTION -->
        <section class="max-w-5xl mx-auto px-4 pt-16 pb-12 text-center space-y-6">
            <div class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-100 text-[#128c7e] text-xs font-bold px-3.5 py-1 rounded-full uppercase tracking-wider">
                <i class="fa-solid fa-gift animate-bounce text-[11px]"></i> Mega Commission Launched
            </div>
            
            <h1 class="text-4xl sm:text-6xl font-bold text-gray-900 tracking-tight max-w-4xl mx-auto leading-[1.1]">
                Send Customers.<br>
                Claim <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#128c7e] to-[#0e6f64]">50% Recurring Cash</span>.
            </h1>
            
            <p class="text-gray-500 text-xs sm:text-sm max-w-xl mx-auto font-medium leading-relaxed">
                Join the official Identity Search AI partner ecosystem. Monetize traffic streams by routing users into our next-gen predictive OSINT data intelligence funnels.
            </p>

            <div class="flex justify-center items-center pt-2">
                <!-- Refactored CTA: Large Text Size, Semibold, Normal Case with Register Icon -->
                <a href="affiliate-register.php" class="w-full sm:w-auto bg-[#128c7e] hover:bg-[#0e6f64] text-white text-base md:text-lg font-semibold px-12 py-4.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-3 cursor-pointer border border-transparent">
                    <i class="fa-solid fa-user-plus text-sm md:text-base"></i> Become an affiliate
                </a>
            </div>
        </section>

        <!-- HIGH-YIELD COMMISSION CHART MATRIX -->
        <section class="max-w-6xl mx-auto px-4 py-8">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden text-left">
                <div class="px-6 py-4 border-b border-gray-100 bg-slate-50/40 flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-[#128c7e]"></i>
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Platform Product Commission Matrices</h3>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-[10px] font-bold uppercase tracking-wider text-gray-400 bg-slate-50/10">
                                <th class="px-6 py-4">Plan Node</th>
                                <th class="px-6 py-4">Validity Horizon</th>
                                <th class="px-6 py-4">Retail Value</th>
                                <th class="px-6 py-4 text-right">Your 50% Payout Split</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs font-semibold text-slate-700">
                            <tr class="hover:bg-slate-50/40 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap"><span class="bg-emerald-50 border border-emerald-100 text-[#128c7e] font-bold font-mono px-2 py-0.5 rounded text-[11px]">M1</span></td>
                                <td class="px-6 py-4 text-gray-400 font-medium">3 Reports Billed Monthly</td>
                                <td class="px-6 py-4 font-mono font-bold text-gray-900">$36.00</td>
                                <td class="px-6 py-4 text-right font-mono font-bold text-sm text-[#128c7e]">+$18.00 <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Recurring</span></td>
                            </tr>
                            <tr class="hover:bg-slate-50/40 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap"><span class="bg-teal-50 border border-teal-100 text-teal-700 font-bold font-mono px-2 py-0.5 rounded text-[11px]">Q3</span></td>
                                <td class="px-6 py-4 text-gray-400 font-medium">5 Reports + 2 Free Billed Every 3 Months</td>
                                <td class="px-6 py-4 font-mono font-bold text-gray-900">$55.00</td>
                                <td class="px-6 py-4 text-right font-mono font-bold text-sm text-[#128c7e]">+$27.50 <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Recurring</span></td>
                            </tr>
                            <tr class="hover:bg-slate-50/40 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap"><span class="bg-amber-50 border border-amber-200 text-amber-800 font-bold font-mono px-2 py-0.5 rounded text-[11px]">B6</span></td>
                                <td class="px-6 py-4 text-gray-400 font-medium">8 Reports + 3 Free Billed Every 6 Months</td>
                                <td class="px-6 py-4 font-mono font-bold text-gray-900">$72.00</td>
                                <td class="px-6 py-4 text-right font-mono font-bold text-sm text-[#128c7e]">+$36.00 <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Recurring</span></td>
                            </tr>
                            <tr class="hover:bg-slate-50/40 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap"><span class="bg-sky-50 border border-sky-100 text-sky-800 font-bold font-mono px-2 py-0.5 rounded text-[11px]">Y12</span></td>
                                <td class="px-6 py-4 text-gray-400 font-medium">12 Reports + 4 Free Billed Annually</td>
                                <td class="px-6 py-4 font-mono font-bold text-gray-900">$96.00</td>
                                <td class="px-6 py-4 text-right font-mono font-bold text-sm text-[#128c7e]">+$48.00 <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Recurring</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- DYNAMIC FORECAST REVENUE CALCULATOR ENGINE -->
        <section class="max-w-6xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
                
                <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-6 text-left">
                    <div class="space-y-1">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-calculator text-[#128c7e]"></i> Estimated Monthly Earnings Forecaster
                        </h3>
                        <p class="text-[11px] text-gray-400 font-medium">Adjust volume parameters down below to forecast real-time cumulative downstream monthly commission loops.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold text-slate-700">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#128c7e]"></span> M1 Referrals (<span id="val_m1_count" class="font-bold">10</span>)</span>
                                <span class="font-mono font-bold text-gray-400">$36.00 Plan</span>
                            </div>
                            <input type="range" id="input_m1" min="0" max="250" value="10" oninput="calculateProjections()" class="w-full accent-[#128c7e] cursor-pointer h-1.5 bg-slate-100 rounded-lg appearance-none">
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold text-slate-700">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-teal-500"></span> Q3 Referrals (<span id="val_q3_count" class="font-bold">5</span>)</span>
                                <span class="font-mono font-bold text-gray-400">$55.00 Plan</span>
                            </div>
                            <input type="range" id="input_q3" min="0" max="250" value="5" oninput="calculateProjections()" class="w-full accent-[#128c7e] cursor-pointer h-1.5 bg-slate-100 rounded-lg appearance-none">
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold text-slate-700">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> B6 Referrals (<span id="val_b6_count" class="font-bold">3</span>)</span>
                                <span class="font-mono font-bold text-gray-400">$72.00 Plan</span>
                            </div>
                            <input type="range" id="input_b6" min="0" max="250" value="3" oninput="calculateProjections()" class="w-full accent-[#128c7e] cursor-pointer h-1.5 bg-slate-100 rounded-lg appearance-none">
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold text-slate-700">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-sky-500"></span> Y12 Referrals (<span id="val_y12_count" class="font-bold">2</span>)</span>
                                <span class="font-mono font-bold text-gray-400">$96.00 Plan</span>
                            </div>
                            <input type="range" id="input_y12" min="0" max="250" value="2" oninput="calculateProjections()" class="w-full accent-[#128c7e] cursor-pointer h-1.5 bg-slate-100 rounded-lg appearance-none">
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-slate-900 to-[#0e6f64] text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between border border-transparent text-left min-h-[220px] relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 opacity-5 text-8xl font-bold font-mono select-none pointer-events-none">$$</div>
                    
                    <div class="flex justify-between items-start">
                        <span class="text-[10px] font-bold text-emerald-100 uppercase tracking-wider">Estimated Monthly Passive Revenue</span>
                        <div class="w-8 h-8 rounded-lg bg-white/10 text-emerald-300 flex items-center justify-center text-sm backdrop-blur-xs"><i class="fa-solid fa-chart-line"></i></div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-white tracking-tight font-mono">$<span id="projected_sum">0.00</span></div>
                        <p class="text-emerald-100/60 text-[10px] leading-relaxed mt-2">Projection based on active baseline customer retention metrics, applying strict 50% monthly-normalized split values directly.</p>
                    </div>
                </div>

            </div>
        </section>

        <!-- VALUE ADVANTAGE LOGISTICS SECTION -->
        <section class="max-w-6xl mx-auto px-4 py-8">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 text-left space-y-6">
                <div class="text-center space-y-1">
                    <h2 class="text-lg font-bold tracking-tight text-gray-900 uppercase">Engineered for Rapid Conversions</h2>
                    <p class="text-xs font-mono text-gray-400 uppercase tracking-widest">Optimized Traffic Monetization Attributes</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs font-medium text-slate-700">
                    <div class="p-4 rounded-xl bg-slate-50/40 border border-gray-100 space-y-2">
                        <div class="w-8 h-8 bg-emerald-50 text-[#128c7e] rounded-lg flex items-center justify-center text-sm shadow-2xs"><i class="fa-solid fa-bolt"></i></div>
                        <h4 class="font-bold text-gray-900 text-sm">High-Converting Funnels</h4>
                        <p class="text-gray-400 leading-relaxed font-semibold">Our target search dossiers sell themselves. Route users onto optimized landing frames fine-tuned to squeeze maximum conversions from raw hits.</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50/40 border border-gray-100 space-y-2">
                        <div class="w-8 h-8 bg-emerald-50 text-[#128c7e] rounded-lg flex items-center justify-center text-sm shadow-2xs"><i class="fa-solid fa-chart-pie"></i></div>
                        <h4 class="font-bold text-gray-900 text-sm">Real-Time S2S Telemetry</h4>
                        <p class="text-gray-400 leading-relaxed font-semibold">Track hits, dynamic sub-ids logs, payout matrix parameters changes, and full downstream postback integration responses live in your terminal dashboard layer.</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50/40 border border-gray-100 space-y-2">
                        <div class="w-8 h-8 bg-emerald-50 text-[#128c7e] rounded-lg flex items-center justify-center text-sm shadow-2xs"><i class="fa-solid fa-sack-dollar"></i></div>
                        <h4 class="font-bold text-gray-900 text-sm">Guaranteed Fast Payouts</h4>
                        <p class="text-gray-400 leading-relaxed font-semibold">No excessive processing delays or hidden escrow windows. Submit funding request balances straight into your verified Payoneer account nodes instantly.</p>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <script>
        /**
         * Real-time monthly normalized projection calculator logic
         */
        function calculateProjections() {
            // Read active counts
            const m1 = parseInt(document.getElementById('input_m1').value) || 0;
            const q3 = parseInt(document.getElementById('input_q3').value) || 0;
            const b6 = parseInt(document.getElementById('input_b6').value) || 0;
            const y12 = parseInt(document.getElementById('input_y12').value) || 0;

            // Sync structural element numbers labels
            document.getElementById('val_m1_count').textContent = m1;
            document.getElementById('val_q3_count').textContent = q3;
            document.getElementById('val_b6_count').textContent = b6;
            document.getElementById('val_y12_count').textContent = y12;

            // Compute monthly normalized split value weights:
            // M1:  $18.00 monthly payout
            // Q3:  $27.50 total payout over 3 months -> $9.17 monthly weight
            // B6:  $36.00 total payout over 6 months -> $6.00 monthly weight
            // Y12: $48.00 total payout over 12 months -> $4.00 monthly weight
            const monthlySumForecast = (m1 * 18.00) + (q3 * 9.166) + (b6 * 6.00) + (y12 * 4.00);

            // Display formatted sum parameters inside layout context
            document.getElementById('projected_sum').textContent = monthlySumForecast.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Initialize calculation instantly upon DOM lifecycle ready state
        document.addEventListener('DOMContentLoaded', calculateProjections);
    </script>

    <footer class="w-full bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400 font-semibold">
        &copy; 2026 Identity Search AI Affiliate Portal. All rights reserved. Run with absolute precision.
    </footer>

</body>
</html>
