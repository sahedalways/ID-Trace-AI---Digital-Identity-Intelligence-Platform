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
    $stmt = $pdo->query("SELECT * FROM `plans` ORDER BY `price` ASC");
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    die("System Schema Connection Error: " . htmlspecialchars($e->getMessage()));
}

// Configuration containing only strict design traits matching your layout rules
$plan_design_meta = [
    'm1' => [
        'badge' => 'Save 52%',
        'billing_text' => 'billed monthly',
    ],
    'q3' => [
        'badge' => 'Save 56%',
        'billing_text' => 'billed every 3 months',
    ],
    'b6' => [
        'badge' => 'Save 64%',
        'billing_text' => 'billed every 6 months',
    ],
    'y12' => [
        'badge' => 'Save 68%',
        'billing_text' => 'billed annually',
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
        
        /* Ultra-clean custom radio input styles matching WhatsApp Green branding matrix */
        .custom-radio:checked + .plan-card {
            border-color: #128c7e !important;
            background-color: #ffffff;
        }
        .custom-radio:checked + .plan-card .radio-circle {
            border-color: #128c7e;
        }
        .custom-radio:checked + .plan-card .radio-circle::after {
            content: '';
            display: block;
            width: 10px;
            height: 10px;
            background: #128c7e;
            border-radius: 50%;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white">

    <?php include 'navbar.php'; ?>

    <main class="flex-grow max-w-md w-full mx-auto px-4 py-8 flex items-center justify-center">
        
        <!-- Standardized Premium Container Box -->
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm text-left w-full">

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
                                
                                <label for="plan_<?php echo $code; ?>" class="plan-card border border-gray-200 rounded-xl p-4 flex flex-col cursor-pointer hover:border-gray-300 transition-all bg-white block relative select-none space-y-3">
                                    
                                    <div class="flex items-center justify-between gap-4 w-full">
                                        <div class="flex items-center gap-3.5 min-w-0">
                                            <!-- Minimal Clean Radio Check Circle -->
                                            <div class="radio-circle w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center flex-shrink-0 transition-colors bg-white"></div>
                                            
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="text-[15px] font-bold text-gray-900 tracking-tight"><?php echo htmlspecialchars($title_string); ?></span>
                                                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-[#ef4444] text-white whitespace-nowrap">
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
                                    <div class="bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2 flex items-center gap-2 text-[11px] font-bold text-[#128c7e] w-full">
                                        <i class="fa-solid fa-gift text-xs shrink-0"></i>
                                        <span>Included: 1-Year ChatZara Premium Free <span class="font-medium text-emerald-700/80">(Value $60)</span></span>
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
                        <button type="submit" id="submitCtaBtn" class="w-full bg-[#128c7e] hover:bg-[#0e6f64] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#128c7e] text-white py-4 px-4 rounded-xl text-[15px] font-bold transition shadow-sm tracking-wide flex items-center justify-center gap-2 cursor-pointer">
                            <span id="btnTextLabel">Select a plan</span>
                        </button>
                    </div>
                </form>

                <!-- Professional Footer Safety Guarantees Line Grid -->
                <div class="pt-2 space-y-2.5 text-center text-xs font-semibold text-black flex flex-col items-center">
                    <div class="flex items-center justify-center gap-1.5 text-gray-500 font-medium">
                        <i class="fa-solid fa-circle-check text-sm text-gray-400"></i> Cancel anytime, for any reason
                    </div>
                    <div class="inline-flex items-center justify-center gap-1.5 bg-[#f0fdf4] text-[#16a34a] px-4 py-1.5 rounded-full text-xs font-bold border border-[#bbf7d0]/40">
                        <i class="fa-solid fa-shield-halved text-xs"></i> 30-day money-back guarantee
                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- INJECT SEPARATE GLOBAL FOOTER COMPONENT -->
    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

    <script>
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