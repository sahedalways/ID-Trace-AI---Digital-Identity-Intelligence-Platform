<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { http_response_code(403); exit('Unauthorized'); }

$affiliateId = (int)($_GET['affiliate_id'] ?? 0);
$pending     = (float)($_GET['amount'] ?? 0);
$paid        = (float)($_GET['paid'] ?? 0);

if (!$affiliateId) { echo '<p class="text-xs text-gray-400 text-center py-4">Invalid affiliate.</p>'; exit; }

$aff = $pdo->prepare("SELECT `id`, `aid`, `name`, `email`, `balance`, `withdraw` FROM `affiliates` WHERE `id` = ? LIMIT 1");
$aff->execute([$affiliateId]);
$aff = $aff->fetch(PDO::FETCH_ASSOC);

$pay = $pdo->prepare("SELECT `payment_method`, `payment_info` FROM `affiliate_payments` WHERE `affiliate_id` = ? LIMIT 1");
$pay->execute([$affiliateId]);
$pay = $pay->fetch(PDO::FETCH_ASSOC);

$methodLabels = ['payoneer' => 'Payoneer', 'usdt_bep20' => 'USDT (BEP20)', 'bank_transfer' => 'Bank Transfer'];
$methodIcons  = ['payoneer' => 'fa-wallet', 'usdt_bep20' => 'fa-coins', 'bank_transfer' => 'fa-building-columns'];
$methodColors = ['payoneer' => 'text-indigo-600 bg-indigo-50', 'usdt_bep20' => 'text-emerald-600 bg-emerald-50', 'bank_transfer' => 'text-blue-600 bg-blue-50'];

$method = $pay['payment_method'] ?? null;
$info   = $pay['payment_info'] ?? '—';
?>

<div class="space-y-4">
    <!-- Affiliate Header -->
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-700 font-black text-sm">
            <?= strtoupper(substr($aff['aid'] ?? '?', 0, 2)) ?>
        </div>
        <div>
            <div class="text-xs font-bold text-gray-900"><?= htmlspecialchars($aff['name'] ?? 'N/A') ?></div>
            <div class="text-[10px] text-gray-400 font-mono"><?= htmlspecialchars($aff['aid'] ?? '') ?></div>
        </div>
    </div>

    <!-- Amount Summary -->
    <div class="grid grid-cols-3 gap-2">
        <div class="bg-white border border-gray-200 rounded-xl p-3 text-center">
            <div class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Request</div>
            <div class="text-sm font-black text-gray-900 font-mono">$<?= number_format($pending + $paid, 2) ?></div>
        </div>
        <div class="bg-white border border-emerald-200 rounded-xl p-3 text-center">
            <div class="text-[9px] font-bold text-emerald-500 uppercase tracking-wider">Paid</div>
            <div class="text-sm font-black text-emerald-600 font-mono">$<?= number_format($paid, 2) ?></div>
        </div>
        <div class="bg-white border border-amber-200 rounded-xl p-3 text-center">
            <div class="text-[9px] font-bold text-amber-500 uppercase tracking-wider">Pending</div>
            <div class="text-sm font-black text-amber-600 font-mono">$<?= number_format($pending, 2) ?></div>
        </div>
    </div>

    <!-- Currency -->
    <div class="bg-white border border-gray-200 rounded-xl p-3 flex items-center justify-between">
        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Currency</span>
        <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5"><span class="text-base">🇺🇸</span> USD</span>
    </div>

    <!-- Payment Method -->
    <div class="bg-white border border-gray-200 rounded-xl p-3">
        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Payment Method</div>
        <?php if ($method): ?>
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center text-[10px] font-bold <?= $methodColors[$method] ?? 'text-gray-600 bg-gray-50' ?> px-2 py-0.5 rounded-md gap-1">
                    <i class="fa-solid <?= $methodIcons[$method] ?? 'fa-money-bill' ?> text-[8px]"></i>
                    <?= $methodLabels[$method] ?? ucfirst(str_replace('_', ' ', $method)) ?>
                </span>
            </div>
            <div class="text-[11px] text-gray-700 font-mono bg-gray-50 rounded-lg p-2.5 break-all">
                <?php
                    if ($method === 'bank_transfer') {
                        $parts = array_map('trim', explode(',', $info));
                        foreach ($parts as $part) {
                            $kv = explode(':', $part, 2);
                            if (count($kv) === 2) {
                                echo '<div class="flex items-start gap-2 mb-1"><span class="text-[9px] font-bold text-gray-400 uppercase whitespace-nowrap">' . htmlspecialchars(trim($kv[0])) . ':</span><span class="text-gray-700">' . htmlspecialchars(trim($kv[1])) . '</span></div>';
                            } else {
                                echo '<div class="text-gray-700">' . htmlspecialchars($part) . '</div>';
                            }
                        }
                    } else {
                        echo htmlspecialchars($info);
                    }
                ?>
            </div>
        <?php else: ?>
            <div class="text-[11px] text-gray-400 italic">No payment method configured</div>
        <?php endif; ?>
    </div>
</div>