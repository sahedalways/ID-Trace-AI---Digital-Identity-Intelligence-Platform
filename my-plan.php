<?php
//File: my-plan.php
require_once 'my-plan-controller.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>My Subscription Plan — Identity Search AI</title>
    <?php include 'head.php'; ?>
    <style>
        body {
            background-color: #f9fafb !important;
            color: #111827 !important;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col justify-between selection:bg-[#0072bc] selection:text-white bg-slate-50 relative">

    <!-- Sticky Glassmorphic Navbar Container -->
    <header id="mainNavbar" class="sticky top-0 z-50 bg-transparent transition-all duration-300">
        <?php include 'navbar.php'; ?>
    </header>

    <!-- Full-width Background Decorations -->
    <div class="absolute inset-x-0 top-0 -z-10 overflow-hidden" style="min-height: 100vh; background: linear-gradient(180deg, #BFE4FD 0%, #FFFFFF 100%);">
        <div class="blob-1 absolute top-0 left-1/2 w-[900px] h-[900px] bg-[#0072bc]/10 rounded-full blur-3xl opacity-60 -translate-x-1/2 will-change-transform"></div>
        <div class="blob-2 absolute top-24 -left-20 w-96 h-96 bg-[#0072bc]/15 rounded-full blur-3xl will-change-transform"></div>
        <div class="blob-3 absolute bottom-0 right-0 w-96 h-96 bg-[#BFE4FD]/50 rounded-full blur-3xl opacity-70 will-change-transform"></div>
    </div>

    <style>
        @keyframes blobMove1 { 0%, 100% { transform: translateX(-50%) translateY(0); } 25% { transform: translateX(-40%) translateY(-20px); } 50% { transform: translateX(-50%) translateY(-10px); } 75% { transform: translateX(-60%) translateY(10px); } }
        @keyframes blobMove2 { 0%, 100% { transform: translate(0, 0); } 33% { transform: translate(30px, -15px); } 66% { transform: translate(-20px, 20px); } }
        @keyframes blobMove3 { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(-40px, -20px); } }
        .blob-1 { animation: blobMove1 18s ease-in-out infinite; }
        .blob-2 { animation: blobMove2 14s ease-in-out infinite; }
        .blob-3 { animation: blobMove3 16s ease-in-out infinite; }
        .navbar-scrolled { background: linear-gradient(to right, #eef5ff 20%, #0072bc 100%); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); }
        #mainNavbar #siteNavbar { background: transparent !important; border-bottom: none !important; box-shadow: none !important; }
        #siteNavbar a, #siteNavbar button, #siteNavbar .text-black, #siteNavbar .text-slate-400, #siteNavbar .text-\[\#0072bc\] { transition: color 0.35s ease, background-color 0.35s ease, border-color 0.35s ease; }
        #mainNavbar.navbar-scrolled #siteNavbar a:not(#userDropdownMenu a, #mobileDrawer a),
        #mainNavbar.navbar-scrolled #siteNavbar button:not(#userDropdownMenu button, #mobileDrawer button),
        #mainNavbar.navbar-scrolled #siteNavbar .text-black:not(#userDropdownMenu, #mobileDrawer),
        #mainNavbar.navbar-scrolled #siteNavbar .text-slate-400:not(#userDropdownMenu .text-slate-400, #mobileDrawer .text-slate-400),
        #mainNavbar.navbar-scrolled #siteNavbar .text-\[\#0072bc\]:not(#userDropdownMenu .text-\[\#0072bc\], #mobileDrawer .text-\[\#0072bc\]) { color: #ffffff !important; }
        #mainNavbar.navbar-scrolled #siteNavbar a:hover:not(#userDropdownMenu a, #mobileDrawer a),
        #mainNavbar.navbar-scrolled #siteNavbar button:hover:not(#userDropdownMenu button, #mobileDrawer button, #mobileMenuButton) { color: #ffffff !important; }
        #mainNavbar.navbar-scrolled #siteNavbar #mobileMenuButton { color: #ffffff !important; border-color: rgba(255, 255, 255, 0.25); }
        #mainNavbar.navbar-scrolled #siteNavbar #mobileMenuButton:hover,
        #mainNavbar.navbar-scrolled #siteNavbar #userMenuButton:hover { background-color: rgba(255, 255, 255, 0.15); color: #ffffff !important; }
    </style>

    <main class="flex-grow max-w-4xl w-full mx-auto px-4 py-8 space-y-6">

        <div class="text-left">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Subscription Management</h1>
            <p class="text-xs text-black font-medium mt-1">Review your allocation status limits, metrics, and billing invoice statements.</p>
        </div>

        <div class="w-full bg-white border border-gray-200 rounded-2xl p-6 shadow-sm relative overflow-hidden">

            <?php if (!empty($user['stripe_subscription_id'])): ?>
                <div class="space-y-6 text-left">
                    <div>
                        <h3 class="text-xs font-semibold text-gray-800 uppercase tracking-wider">Current Package Tier</h3>
                        <p class="text-xl font-bold text-gray-900 mt-0.5">
                            <?php echo !empty($user['plan']) ? strtoupper(htmlspecialchars($user['plan'])) . ' Subscription' : 'Free Trial Baseline'; ?>
                        </p>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-8 gap-4 border-t border-gray-50 pt-5">
                        <div class="min-w-0">
                            <span class="text-[11px] font-semibold text-gray-400 block uppercase tracking-wider">Status</span>
                            <?php if (strtolower($subscription_status) === 'active' || strtolower($subscription_status) === 'trialing'): ?>
                                <span class="text-xs font-semibold uppercase text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/60 inline-block mt-1">Active</span>
                            <?php else: ?>
                                <span class="text-xs font-semibold uppercase text-gray-700 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-200 inline-block mt-1"><?php echo htmlspecialchars($subscription_status); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[11px] font-semibold text-gray-400 block uppercase tracking-wider">Credits</span>
                            <span class="text-sm font-semibold text-gray-900 mt-1 inline-flex items-center">
                                <?php echo (int)$user['credit']; ?>
                                <span class="text-xs font-normal text-gray-500 ml-1">Reports</span>
                            </span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[11px] font-semibold text-gray-400 block uppercase tracking-wider">Plan Cost</span>
                            <span class="text-sm font-semibold text-gray-900 mt-1 inline-block">$<?php echo $plan_amount; ?></span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[11px] font-semibold text-gray-400 block uppercase tracking-wider">Frequency</span>
                            <span class="text-sm font-semibold text-gray-900 mt-1 inline-block capitalize"><?php echo $plan_frequency; ?></span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[11px] font-semibold text-gray-400 block uppercase tracking-wider">Payment Card</span>
                            <span class="text-sm font-semibold text-gray-900 mt-1 inline-block tracking-tight break-words">
                                <i class="fa-solid fa-credit-card text-xs mr-0.5 text-gray-400"></i> <?php echo htmlspecialchars($payment_method_display); ?>
                            </span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[11px] font-semibold text-gray-400 block uppercase tracking-wider">Auto Renewal</span>
                            <span class="text-sm font-semibold text-gray-900 mt-1 inline-block"><?php echo $cancel_at_period_end ? 'Disabled' : 'Enabled'; ?></span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[11px] font-semibold text-gray-400 block uppercase tracking-wider">Last Charged</span>
                            <span class="text-sm font-semibold text-gray-900 mt-1 inline-block"><?php echo $last_charge_date; ?></span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[11px] font-semibold text-gray-400 block uppercase tracking-wider">Next Renewal</span>
                            <span class="text-sm font-semibold text-gray-900 mt-1 inline-block"><?php echo $next_charge_date; ?></span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100/70 space-y-3">
                        <a href="<?php echo BASE_URL; ?>buy-credit" class="w-auto flex items-center justify-center gap-2 bg-[#0072bc] hover:bg-[#005ea3] text-white py-4 px-6 rounded-xl text-sm font-bold transition shadow-sm cursor-pointer">
                            <i class="fa-solid fa-circle-plus"></i> Upgrade Plan / Add Credits
                        </a>

                        <?php if (!$cancel_at_period_end && in_array($subscription_status, ['active', 'trialing'])): ?>
                            <a href="cancel-subscription" onclick="return confirm('Your remaining credits and generated report will be lost on cancellation your plan. Do you want to cancel your subscription?');" class="w-auto flex items-center justify-center bg-[#B22222] hover:bg-[#b81032] text-white py-4 px-6 rounded-xl text-sm font-bold transition shadow-sm cursor-pointer border border-transparent">
                                Cancel Subscription
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-300 text-xl mb-4 border border-gray-100">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">You have no active subscription</h3>
                    <p class="text-xs text-black font-medium mt-1 mb-6">Activate your account to start generating intelligence reports.</p>
                    <a href="<?php echo BASE_URL; ?>buy-credit" class="w-full sm:w-auto flex items-center justify-center gap-2 bg-[#0072bc] hover:bg-[#005ea3] text-white py-4 px-12 rounded-xl text-sm font-bold transition shadow-sm cursor-pointer">
                        <i class="fa-solid fa-circle-plus"></i> Activate Subscription
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden text-left">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900">Billing History & Invoices</h3>
                <p class="text-[11px] text-black font-medium mt-0.5">Historical verification logs containing verified transaction records.</p>
            </div>

            <?php if (empty($transactions)): ?>
                <div class="p-12 text-center text-xs font-semibold text-gray-800 space-y-2 flex flex-col items-center justify-center">
                    <i class="fa-solid fa-receipt text-3xl text-gray-200 block mb-1"></i>
                    <span>No structural transaction logs linked to this profile container.</span>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/70 border-b border-gray-100 text-[10px] font-semibold uppercase text-gray-400 tracking-wider">
                                <th class="py-3 px-6">Transaction ID</th>
                                <th class="py-3 px-6">Date</th>
                                <th class="py-3 px-6">Plan Tier</th>
                                <th class="py-3 px-6">Amount</th>
                                <th class="py-3 px-6 text-center">Status</th>
                                <th class="py-3 px-6 text-center">Download</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs font-medium text-black divide-y divide-gray-100">
                            <?php foreach ($transactions as $tx): ?>
                                <tr class="hover:bg-gray-50/40 transition-colors">
                                    <td class="py-4 px-6 font-mono text-gray-900 font-bold text-[11px]">
                                        <?php echo !empty($tx['tid']) ? htmlspecialchars($tx['tid']) : 'N/A'; ?>
                                    </td>
                                    <td class="py-4 px-6 text-gray-900 font-semibold min-w-[120px] whitespace-nowrap">
                                        <?php echo date('M d, Y', strtotime($tx['created_at'])); ?>
                                    </td>
                                    <td class="py-4 px-6 font-semibold uppercase text-gray-700">
                                        <?php echo htmlspecialchars($tx['plan']); ?>
                                    </td>
                                    <td class="py-4 px-6 font-bold text-gray-900">
                                        $<?php echo !empty($tx['plan_cost']) ? number_format($tx['plan_cost'], 2) : '0.00'; ?>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <?php if ($tx['status'] === 'succeeded'): ?>
                                            <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-[10px] font-semibold px-2 py-0.5 rounded-md uppercase border border-emerald-100">
                                                <span class="w-1 h-1 rounded-full bg-emerald-600"></span> Paid
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 text-[10px] font-semibold px-2 py-0.5 rounded-md uppercase border border-red-100">
                                                <span class="w-1 h-1 rounded-full bg-red-500"></span> Declined
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <?php if ($tx['status'] === 'succeeded'): ?>
                                            <a href="generate_invoice?tid=<?php echo urlencode($tx['tid']); ?>" target="_blank" class="inline-block bg-[#0072bc] text-white hover:bg-[#005ea3] border border-transparent text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm transition duration-150 tracking-tight" title="Download PDF Invoice">
                                                Download
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-300 text-sm select-none">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <script>
        // NAVBAR SCROLL GLASS EFFECT
        document.addEventListener("DOMContentLoaded", () => {
            const navbar = document.getElementById("mainNavbar");
            if (!navbar) return;
            const handleScroll = () => {
                if (window.scrollY > 20) {
                    navbar.classList.add("navbar-scrolled");
                } else {
                    navbar.classList.remove("navbar-scrolled");
                }
            };
            window.addEventListener("scroll", handleScroll, { passive: true });
            handleScroll();
        });
    </script></body>

</html>
