<?php
/**
 * OSINT Universal Intelligence Console — Credit Purchase Interface
 * File: buy-credit.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Pull active plans directly out of your database architecture mapping matrix
    $stmt = $pdo->query("SELECT * FROM `plans` WHERE `status` = 1 ORDER BY `sort_order` ASC");
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    die("System Schema Connection Error: " . htmlspecialchars($e->getMessage()));
}

// Configuration containing only strict design traits matching your layout rules
$plan_design_meta = [
    'm12' => [
        'badge' => 'Save 68%',
        'billing_text' => 'billed monthly',
        'badge_class' => 'bg-[#ef4444]',
    ],
    'm5' => [
        'badge' => 'Most Popular',
        'billing_text' => 'billed monthly',
        'badge_class' => 'bg-gradient-to-r from-[#0072bc] to-blue-600',
        'popular' => true,
    ],
    'm3' => [
        'badge' => 'Save 47%',
        'billing_text' => 'billed monthly',
        'badge_class' => 'bg-[#ef4444]',
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pricing - Identity Search AI</title>
    <?php include 'head.php'; ?>
    <style>
        body { background-color: #f9fafb !important; color: #111827 !important; }

        /* Ultra-clean custom radio input styles matching brand matrix */
        .custom-radio:checked + .plan-card {
            border-color: #0072bc !important;
            background-color: #ffffff;
        }
        .custom-radio:checked + .plan-card .radio-circle {
            border-color: #0072bc;
        }
        .custom-radio:checked + .plan-card .radio-circle::after {
            content: '';
            display: block;
            width: 10px;
            height: 10px;
            background: #0072bc;
            border-radius: 50%;
        }

        @keyframes blobMove1 {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            25% { transform: translateX(-40%) translateY(-20px); }
            50% { transform: translateX(-50%) translateY(-10px); }
            75% { transform: translateX(-60%) translateY(10px); }
        }
        @keyframes blobMove2 {
            0%, 100% { transform: translate(0, 0); }
            33% { transform: translate(30px, -15px); }
            66% { transform: translate(-20px, 20px); }
        }
        @keyframes blobMove3 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-40px, -20px); }
        }
        .blob-1 { animation: blobMove1 18s ease-in-out infinite; }
        .blob-2 { animation: blobMove2 14s ease-in-out infinite; }
        .blob-3 { animation: blobMove3 16s ease-in-out infinite; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .onload-anim { animation: fadeInUp 0.8s ease-out forwards; opacity: 0; }
        .onload-delay-100 { animation-delay: 100ms; }
        .onload-delay-200 { animation-delay: 200ms; }
        .onload-delay-300 { animation-delay: 300ms; }

        .navbar-scrolled {
            background: linear-gradient(to right, #eef5ff 20%, #0072bc 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        #mainNavbar #siteNavbar {
            background: transparent !important;
            border-bottom: none !important;
            box-shadow: none !important;
        }
        #siteNavbar a,
        #siteNavbar button,
        #siteNavbar .text-black,
        #siteNavbar .text-slate-400,
        #siteNavbar .text-\[\#0072bc\] {
            transition: color 0.35s ease, background-color 0.35s ease, border-color 0.35s ease;
        }
        #mainNavbar.navbar-scrolled #siteNavbar a:not(#userDropdownMenu a, #mobileDrawer a),
        #mainNavbar.navbar-scrolled #siteNavbar button:not(#userDropdownMenu button, #mobileDrawer button),
        #mainNavbar.navbar-scrolled #siteNavbar .text-black:not(#userDropdownMenu, #mobileDrawer),
        #mainNavbar.navbar-scrolled #siteNavbar .text-slate-400:not(#userDropdownMenu .text-slate-400, #mobileDrawer .text-slate-400),
        #mainNavbar.navbar-scrolled #siteNavbar .text-\[\#0072bc\]:not(#userDropdownMenu .text-\[\#0072bc\], #mobileDrawer .text-\[\#0072bc\]) {
            color: #ffffff !important;
        }
        #mainNavbar.navbar-scrolled #siteNavbar a:hover:not(#userDropdownMenu a, #mobileDrawer a),
        #mainNavbar.navbar-scrolled #siteNavbar button:hover:not(#userDropdownMenu button, #mobileDrawer button, #mobileMenuButton) {
            color: #ffffff !important;
        }
        #mainNavbar.navbar-scrolled #siteNavbar #mobileMenuButton {
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.25);
        }
        #mainNavbar.navbar-scrolled #siteNavbar #mobileMenuButton:hover,
        #mainNavbar.navbar-scrolled #siteNavbar #userMenuButton:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff !important;
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

    <main class="relative flex-grow max-w-md w-full mx-auto px-4 py-10 flex items-center justify-center">

        <!-- Standardized Premium Container Box -->
        <div class="bg-white/80 backdrop-blur border border-gray-200 rounded-3xl overflow-hidden shadow-xl ring-1 ring-black/5 text-left w-full onload-anim onload-delay-200">

            <div class="p-5 sm:p-6 space-y-5">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Choose Your Plan</h2>
                </div>

                <form id="billingForm" action="<?php echo BASE_URL; ?>checkout" method="GET" class="space-y-4" onsubmit="triggerCtaLoadingState(this)">
                    
                    <div class="space-y-3">
                        <?php 
                        $isFirst = true;
                        foreach ($plans as $plan): 
                            $code = $plan['name'];
                            $meta = $plan_design_meta[$code] ?? null;
                            if (!$meta) continue;

                            $paid_credit = (int)$plan['credit'];
                            $free_credit = (int)$plan['free_credit'];
                            
                            $calculated_original_price = ($paid_credit > 0) ? $paid_credit * 25 : 25; 
                            $perReportPrice = ($paid_credit > 0) ? round($plan['price'] / $paid_credit, 2) : 0;

                            if ($free_credit > 0) {
                                $title_string = "{$paid_credit} reports + {$free_credit} free";
                            } else {
                                $title_string = "{$paid_credit} reports";
                            }
                        ?>
                            <div class="relative">
                                <input 
                                    type="radio" 
                                    name="plan" 
                                    id="plan_<?php echo $code; ?>" 
                                    value="<?php echo htmlspecialchars($code); ?>" 
                                    class="hidden custom-radio"
                                    <?php echo $isFirst ? 'checked' : ''; ?>
                                >
                                
                                <label for="plan_<?php echo $code; ?>" class="plan-card border border-gray-200 rounded-xl p-4 flex flex-col cursor-pointer hover:border-gray-300 transition-all bg-white block relative select-none space-y-3<?php echo !empty($meta['popular']) ? ' ring-2 ring-[#0072bc]/40 border-[#0072bc]' : ''; ?>">
                                    
                                    <div class="flex items-center justify-between gap-4 w-full">
                                        <div class="flex items-center gap-3.5 min-w-0">
                                            <!-- Minimal Clean Radio Check Circle -->
                                            <div class="radio-circle w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center flex-shrink-0 transition-colors bg-white"></div>
                                            
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="text-[15px] font-bold text-gray-900 tracking-tight"><?php echo htmlspecialchars($title_string); ?></span>
                                                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full <?php echo $meta['badge_class'] ?? 'bg-[#ef4444]'; ?> text-white whitespace-nowrap">
                                                        <?php echo $meta['badge']; ?>
                                                    </span>
                                                </div>
                                                <div class="text-xs text-black font-medium mt-0.5">
                                                    <span class="text-gray-400 line-through font-normal">$<?php echo $calculated_original_price; ?></span> 
                                                    <span class="font-semibold text-gray-700">$<?php echo number_format($plan['price'], 0); ?> <?php echo htmlspecialchars($meta['billing_text']); ?></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right shrink-0 pl-2">
                                            <div class="text-lg font-bold text-gray-900">$<?php echo number_format($perReportPrice, 0); ?></div>
                                            <div class="text-[10px] font-semibold text-gray-400 mt-0.5">per report</div>
                                        </div>
                                    </div>

                                    <!-- BUNDLED PREMIUM INTEGRATION OFFER HIGHLIGHT BOX -->
                                    <div class="bg-blue-50 border border-blue-100 rounded-lg px-3 py-2 flex items-center gap-2 text-[11px] font-bold text-[#0072bc] w-full">
                                        <i class="fa-solid fa-gift text-xs shrink-0"></i>
                                        <span>Included: 1-Year ChatZara Premium Free <span class="font-medium text-blue-700/80">(Value $60)</span></span>
                                    </div>

                                </label>
                            </div>
                        <?php 
                            $isFirst = false;
                        endforeach; 
                        ?>
                    </div>

                    <!-- Submit Button Call Action Area -->
                    <div class="pt-2">
                        <button type="submit" id="submitCtaBtn" class="w-full bg-[#0072bc] hover:bg-[#005ea3] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#0072bc] text-white py-4 px-4 rounded-xl text-[15px] font-bold transition shadow-sm tracking-wide flex items-center justify-center gap-2 cursor-pointer">
                            <span id="btnTextLabel">Select a plan</span>
                        </button>
                    </div>
                </form>

                <!-- Professional Footer Safety Guarantees Line Grid -->
                <div class="pt-2 space-y-2.5 text-center text-xs font-semibold text-black flex flex-col items-center">
                    <div class="flex items-center justify-center gap-1.5 text-gray-500 font-medium">
                        <i class="fa-solid fa-circle-check text-sm text-gray-400"></i> Cancel anytime, for any reason
                    </div>
                    <div class="inline-flex items-center justify-center gap-1.5 bg-[#0072bc]/10 text-[#0072bc] px-4 py-1.5 rounded-full text-xs font-bold border border-[#0072bc]/20">
                        <i class="fa-solid fa-shield-halved text-xs"></i> 30-day money-back guarantee
                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- INJECT SEPARATE GLOBAL FOOTER COMPONENT -->
    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

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

        function triggerCtaLoadingState(formEl) {
            const btn = document.getElementById('submitCtaBtn');
            const label = document.getElementById('btnTextLabel');

            if (btn && label) {
                btn.disabled = true;
                btn.classList.add('opacity-70', 'cursor-not-allowed');
                label.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1"></i> Processing...';
            }
        }
    </script>
</body>
</html>
