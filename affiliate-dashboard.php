<?php
/**
 * File: affiliate-dashboard.php
 * Main Analytics Summary Panel Matrix for Activated Affiliate Partners.
 * Displays financial metrics balances, referral status flags, and updated clean go.php tracking links.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Enforce strict authentication wall intercept limits
if (!isset($_SESSION['affiliate_id'])) {
    header("Location: affiliate-login.php");
    exit;
}

$affiliateId = $_SESSION['affiliate_id'];

try {
    // 2. Fetch fresh, live financial metadata numbers from database
    $stmt = $pdo->prepare("SELECT * FROM `affiliates` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$affiliateId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Safe fallback logout sequence if user entry vanished from core tables
        session_destroy();
        header("Location: affiliate-login.php");
        exit;
    }

    // 3. Dynamically craft affiliate tracking link targeting go.php?id=[aid]
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "https://";
    $referralLink = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "go.php?id=" . $user['aid'];

    // 4. AGGREGATE LIVE NETWORK PERFORMANCE METRICS FROM NEW MATRIX LAYOUT
    // Total Raw Clicks routing through this partner node
    $clicks_stmt = $pdo->prepare("SELECT COUNT(*) FROM `clicks` WHERE `affid` = ?");
    $clicks_stmt->execute([$affiliateId]);
    $totalClicks = (int)$clicks_stmt->fetchColumn();

    // Total Conversions where user has checked out successfully (directly tracking via affid)
    $conv_stmt = $pdo->prepare("SELECT COUNT(*) FROM `conversions` WHERE `affid` = ?");
    $conv_stmt->execute([$affiliateId]);
    $totalConversions = (int)$conv_stmt->fetchColumn();

    // Total Recurring Subscriptions tracked across subsequent billing cycle events
    $rec_stmt = $pdo->prepare("SELECT COUNT(*) FROM `recurring` WHERE `affid` = ?");
    $rec_stmt->execute([$affiliateId]);
    $totalRecurring = (int)$rec_stmt->fetchColumn();

    // Total Chargebacks matching this affiliate ID where dispute_status equals 1 inside transactions table
    $dispute_stmt = $pdo->prepare("
        SELECT COUNT(*) FROM `transactions` 
        WHERE `dispute_status` = 1 
        AND `uid` IN (SELECT `uid` FROM `conversions` WHERE `affid` = ?)
    ");
    $dispute_stmt->execute([$affiliateId]);
    $totalChargebacks = (int)$dispute_stmt->fetchColumn();

} catch (PDOException $e) {
    error_log("Affiliate Dashboard Loading Error: " . $e->getMessage());
    die("An error occurred loading the account panel matrix layers.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Partner Metrics Panel — PartnerTerminal</title>
    <?php include 'affiliate-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-between">

    <?php include 'affiliate-navbar.php'; ?>

    <main class="max-w-[1650px] w-full mx-auto px-4 sm:px-6 pt-8 pb-16 grow space-y-6">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white border border-gray-200 p-6 rounded-2xl shadow-sm gap-4">
            <div class="text-left space-y-0.5">
                <h1 class="text-xl font-extrabold tracking-tight text-gray-900">Partner Intelligence Console</h1>
                <p class="text-xs text-gray-400">Track conversions, traffic links, and residual revenue metrics live.</p>
            </div>
            
            <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 px-2.5 py-1 rounded-md uppercase tracking-wider">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div> Network Node: <?= htmlspecialchars($user['status']) ?>
            </span>
        </div>

        <!-- Filter Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-1">Filter:</span>
            <button onclick="filterStats('lifetime')" data-filter="lifetime" class="filter-btn active bg-gray-900 text-white text-[11px] font-bold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">Lifetime</button>
            <button onclick="filterStats('today')" data-filter="today" class="filter-btn bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-[11px] font-bold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">Today</button>
            <button onclick="filterStats('yesterday')" data-filter="yesterday" class="filter-btn bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-[11px] font-bold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">Yesterday</button>
            <button onclick="filterStats('this_week')" data-filter="this_week" class="filter-btn bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-[11px] font-bold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">This Week</button>
            <button onclick="filterStats('this_month')" data-filter="this_month" class="filter-btn bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-[11px] font-bold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">This Month</button>
            <button onclick="filterStats('last_month')" data-filter="last_month" class="filter-btn bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-[11px] font-bold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">Last Month</button>
            <button onclick="filterStats('this_year')" data-filter="this_year" class="filter-btn bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-[11px] font-bold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">This Year</button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-5">
            
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left relative overflow-hidden flex flex-col justify-between min-h-[120px]">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Available Balance</span>
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm"><i class="fa-solid fa-wallet"></i></div>
                </div>
                <div>
                    <div class="text-2xl font-black text-gray-900 tracking-tight font-mono" id="stat-balance">$<?= number_format($user['balance'], 2) ?></div>
                    <p class="text-[10px] text-gray-400 mt-0.5">Cleared splits ready for payout deployment.</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left relative overflow-hidden flex flex-col justify-between min-h-[120px]">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Withdrawn</span>
                    <div class="w-7 h-7 rounded-lg bg-slate-50 text-slate-500 flex items-center justify-center text-sm"><i class="fa-solid fa-money-bill-wave"></i></div>
                </div>
                <div>
                    <div class="text-2xl font-black text-gray-900 tracking-tight font-mono" id="stat-withdrawn">$<?= number_format($user['withdraw'], 2) ?></div>
                    <p class="text-[10px] text-gray-400 mt-0.5">Accumulated funds dispatched securely.</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left relative overflow-hidden flex flex-col justify-between min-h-[120px]">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Traffic Clicks</span>
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm"><i class="fa-solid fa-mouse-pointer"></i></div>
                </div>
                <div>
                    <div class="text-2xl font-black text-gray-900 tracking-tight font-mono" id="stat-clicks"><?= number_format($totalClicks) ?></div>
                    <p class="text-[10px] text-gray-400 mt-0.5">Raw unique inbound visitor referrals.</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left relative overflow-hidden flex flex-col justify-between min-h-[120px]">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Converted Sales</span>
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm"><i class="fa-solid fa-circle-check"></i></div>
                </div>
                <div>
                    <div class="text-2xl font-black text-emerald-600 tracking-tight font-mono" id="stat-conversions"><?= number_format($totalConversions) ?></div>
                    <p class="text-[10px] text-gray-400 mt-0.5">Successful customer purchases logged.</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left relative overflow-hidden flex flex-col justify-between min-h-[120px]">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Recurring Billings</span>
                    <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-sm"><i class="fa-solid fa-arrows-rotate"></i></div>
                </div>
                <div>
                    <div class="text-2xl font-black text-gray-900 tracking-tight font-mono" id="stat-recurring"><?= number_format($totalRecurring) ?></div>
                    <p class="text-[10px] text-gray-400 mt-0.5">Active continuous 50% split plan loops.</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left relative overflow-hidden flex flex-col justify-between min-h-[120px]">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Chargebacks</span>
                    <div class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-sm"><i class="fa-solid fa-triangle-exclamation"></i></div>
                </div>
                <div>
                    <div class="text-2xl font-black text-red-600 tracking-tight font-mono" id="stat-chargebacks"><?= number_format($totalChargebacks) ?></div>
                    <p class="text-[10px] text-gray-400 mt-0.5">Disputed sales revoked from balance.</p>
                </div>
            </div>

        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm text-left space-y-4">
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5"><i class="fa-solid fa-link text-indigo-600 text-base"></i> Your Unique Promotional Link Anchor</h3>
                <p class="text-xs text-gray-400">Deploy this URL pattern into tracking content grids to lock cookie matrices to referred visitors.</p>
            </div>
            
            <div class="flex items-center gap-2 bg-slate-50 border border-gray-150 rounded-xl p-2 pl-3">
                <i class="fa-solid fa-globe text-slate-400 shrink-0 text-sm"></i>
                <input type="text" id="refLinkInput" readonly value="<?= htmlspecialchars($referralLink) ?>" 
                    class="w-full bg-transparent font-mono text-xs font-semibold text-slate-700 outline-none select-all">
                <button onclick="copyTrackingLink()" id="copyBtn" class="bg-gray-900 hover:bg-gray-800 text-white text-[11px] font-bold px-4 py-2 rounded-lg transition-all shrink-0 cursor-pointer flex items-center gap-1">
                    <i class="fa-solid fa-copy text-xs"></i> Copy Link
                </button>
            </div>

            <div class="flex items-center gap-2">
                <label for="s1Input" class="text-xs font-bold text-gray-500 shrink-0">Sub ID (s1):</label>
                <input type="text" id="s1Input" placeholder="e.g. James Smith" maxlength="64"
                    class="flex-1 bg-slate-50 border border-gray-200 rounded-lg px-3 py-2 font-mono text-xs text-slate-700 outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 transition-all">
            </div>
        </div>

    </main>

    <script>
        const baseRefLink = "<?= htmlspecialchars($referralLink) ?>";
        const refLinkInput = document.getElementById("refLinkInput");
        const s1Input = document.getElementById("s1Input");

        s1Input.addEventListener("input", function() {
            const s1 = this.value.trim();
            refLinkInput.value = s1 ? baseRefLink + "&s1=" + encodeURIComponent(s1) : baseRefLink;
        });

        function copyTrackingLink() {
            const copyBtn = document.getElementById("copyBtn");
            
            refLinkInput.select();
            refLinkInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(refLinkInput.value);
            
            copyBtn.innerHTML = `<i class="fa-solid fa-check text-xs"></i> Copied!`;
            copyBtn.style.background = "#059669";
            
            setTimeout(() => {
                copyBtn.innerHTML = `<i class="fa-solid fa-copy text-xs"></i> Copy Link`;
                copyBtn.style.background = "#111827";
            }, 2500);
        }

        // Dashboard Stats Filter
        function filterStats(filter) {
            // Update active button styles
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-gray-900', 'text-white');
                btn.classList.add('bg-white', 'border', 'border-gray-200', 'text-gray-600');
            });
            const activeBtn = document.querySelector(`[data-filter="${filter}"]`);
            activeBtn.classList.remove('bg-white', 'border', 'border-gray-200', 'text-gray-600');
            activeBtn.classList.add('bg-gray-900', 'text-white');

            // Add pulse animation to cards
            document.querySelectorAll('#stat-withdrawn, #stat-clicks, #stat-conversions, #stat-recurring, #stat-chargebacks').forEach(el => {
                el.style.opacity = '0.4';
            });

            fetch(`affiliate-dashboard-stats.php?filter=${encodeURIComponent(filter)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('stat-withdrawn').textContent = '$' + data.withdrawn;
                        document.getElementById('stat-clicks').textContent = data.clicks;
                        document.getElementById('stat-conversions').textContent = data.conversions;
                        document.getElementById('stat-recurring').textContent = data.recurring;
                        document.getElementById('stat-chargebacks').textContent = data.chargebacks;
                    }
                })
                .catch(err => console.error('Filter error:', err))
                .finally(() => {
                    document.querySelectorAll('#stat-withdrawn, #stat-clicks, #stat-conversions, #stat-recurring, #stat-chargebacks').forEach(el => {
                        el.style.opacity = '1';
                        el.style.transition = 'opacity 0.3s ease';
                    });
                });
        }
    </script>

    <footer class="w-full bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400 font-semibold">
        <div class="flex items-center justify-center gap-2 mb-2">
            <img src="public/logo.png" alt="Identity Search AI Logo" class="h-12 w-auto">
        </div>
        &copy; <?= date('Y'); ?> Identity Search AI Affiliate Portal. All rights reserved.
    </footer>

</body>
</html>
