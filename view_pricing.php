<?php
/**
 * OSINT Universal Intelligence Console — Embedded Credit Purchase Component
 * File: view_pricing.php
 */
require_once 'config.php';

$vid = $vid ?? (isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '');

try {
    $stmt = $pdo->query("SELECT * FROM `plans` WHERE `status` = 1 ORDER BY `sort_order` ASC");
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    echo "<p class='text-xs text-red-500 font-bold p-4'>System Schema Connection Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    return;
}

// Design mappings matching your exact structural database keys
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

<style>
    /* Ultra-clean custom radio input styles matching brand specifications */
    .custom-radio-input:checked + .plan-card-wrapper {
        border-color: #0072bc !important;
        background-color: #ffffff;
    }
    .custom-radio-input:checked + .plan-card-wrapper .radio-outer-dot {
        border-color: #0072bc;
    }
    .custom-radio-input:checked + .plan-card-wrapper .radio-outer-dot::after {
        content: '';
        display: block;
        width: 10px;
        height: 10px;
        background: #0072bc;
        border-radius: 50%;
    }

    /* Premium floating "Most Popular" badge */
    .mp-badge {
        overflow: hidden;
        animation: mpGlow 2.8s ease-in-out infinite;
    }
    .mp-badge .mp-shine {
        position: absolute;
        inset: 0;
        overflow: hidden;
        border-radius: 9999px;
    }
    .mp-badge .mp-shine::after {
        content: '';
        position: absolute;
        top: -4px;
        left: 0;
        height: calc(100% + 8px);
        width: 45%;
        background: linear-gradient(105deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.55) 50%, rgba(255,255,255,0) 100%);
        transform: skewX(-20deg);
        animation: mpShine 2.8s ease-in-out infinite;
    }
    @keyframes mpShine {
        0% { transform: translateX(-140%) skewX(-20deg); }
        60%, 100% { transform: translateX(320%) skewX(-20deg); }
    }
    @keyframes mpGlow {
        0%, 100% { box-shadow: 0 8px 20px -6px rgba(245, 158, 11, 0.6), 0 0 0 2px rgba(255,255,255,0.7); }
        50% { box-shadow: 0 10px 28px -4px rgba(244, 63, 94, 0.75), 0 0 0 2px rgba(255,255,255,0.7); }
    }

    /* Premium card shine sweep */
    .mp-card-shine { animation: mpCardShine 4.5s ease-in-out infinite; }
    @keyframes mpCardShine {
        0%, 55% { transform: translateX(-160%) skewX(-12deg); }
        85%, 100% { transform: translateX(420%) skewX(-12deg); }
    }
</style>

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm text-left">
    
    <div class="p-5 sm:p-6 space-y-5">
        <div>
            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Choose Your Plan</h3>
        </div>

        <form id="embeddedBillingForm" action="<?php echo BASE_URL; ?>checkout" method="GET" class="space-y-4" onsubmit="triggerEmbedCtaLoadingState(this)">
            
            <?php if (!empty($vid)): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($vid, ENT_QUOTES, 'UTF-8'); ?>">
            <?php endif; ?>
            
            <div class="space-y-7">
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
                            id="embed_plan_<?php echo $code; ?>" 
                            value="<?php echo htmlspecialchars($code); ?>" 
                            class="hidden custom-radio-input"
                            <?php echo $isFirst ? 'checked' : ''; ?>
                        >
                        
                        <label for="embed_plan_<?php echo $code; ?>" class="plan-card-wrapper border rounded-xl flex flex-col cursor-pointer transition-all bg-white block relative select-none space-y-3<?php echo !empty($meta['popular']) ? ' p-5 border-2 border-[#0072bc] ring-4 ring-[#0072bc]/15 bg-gradient-to-b from-blue-100/80 via-white to-white shadow-[0_14px_45px_-10px_rgba(0,114,188,0.45)] scale-[1.04] z-10 mp-card' : ' p-4 border-gray-200 hover:border-gray-300'; ?>">
                            <?php if (!empty($meta['popular'])): ?>
                            <span class="pointer-events-none absolute inset-0 rounded-xl overflow-hidden z-0" aria-hidden="true">
                                <span class="mp-card-shine absolute inset-y-0 w-1/2 bg-gradient-to-r from-transparent via-white/30 to-transparent"></span>
                            </span>
                            <?php endif; ?>
                            
                            <div class="flex items-center justify-between gap-4 w-full">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="radio-outer-dot w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center flex-shrink-0 transition-colors bg-white"></div>
                                    
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-[15px] font-bold text-gray-900 tracking-tight"><?php echo htmlspecialchars($title_string); ?></span>
                                            <?php if (!empty($meta['popular'])): ?>
                                            <span class="mp-badge absolute -top-3 right-4 z-20 inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-amber-400 via-orange-400 to-rose-500 px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-widest text-white">
                                                <span class="mp-shine"></span>
                                                <i class="fa-solid fa-crown text-[10px] relative"></i>
                                                <span class="relative"><?php echo $meta['badge']; ?></span>
                                            </span>
                                            <?php elseif (!empty($meta['badge'])): ?>
                                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full <?php echo $meta['badge_class'] ?? 'bg-[#ef4444]'; ?> text-white whitespace-nowrap">
                                                <?php echo $meta['badge']; ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-xs text-black font-medium mt-0.5">
                                            <span class="text-gray-400 line-through font-normal">$<?php echo $calculated_original_price; ?></span> 
                                            <span class="text-gray-700 font-semibold">$<?php echo number_format($plan['price'], 2); ?> <?php echo htmlspecialchars($meta['billing_text']); ?></span>
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
                                <span>Included: 1-Year ChatZara Premium Free <span class="font-medium text-emerald-700/80">(Value $60)</span></span>
                            </div>

                        </label>
                    </div>
                <?php 
                    $isFirst = false;
                endforeach; 
                ?>
            </div>

            <div class="pt-2">
                <button type="submit" id="embedSubmitCtaBtn" class="w-full bg-[#0072bc] hover:bg-[#005ea3] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#0072bc] text-white py-4 px-4 rounded-xl text-[15px] font-bold transition shadow-sm tracking-wide flex items-center justify-center gap-2 cursor-pointer">
                    <span id="embedBtnTextLabel">Select a plan</span>
                </button>
            </div>
        </form>

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

<script>
    function triggerEmbedCtaLoadingState(formEl) {
        const btn = document.getElementById('embedSubmitCtaBtn');
        const label = document.getElementById('embedBtnTextLabel');

        if (btn && label) {
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');
            label.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1"></i> Processing...';
        }
    }
</script>