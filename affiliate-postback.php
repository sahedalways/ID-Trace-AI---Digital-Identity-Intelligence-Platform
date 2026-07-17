<?php
/**
 * File: affiliate-postback.php
 * Automated Server-to-Server Postback Tracking Configuration Hub.
 * Saves custom callback end-points for live external tracking attribution loops.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Enforce strict authentication walls
if (!isset($_SESSION['affiliate_id'])) {
    header("Location: affiliate-login.php");
    exit;
}

$affiliateId = (int)$_SESSION['affiliate_id'];

// 2. Handle POST Mutation Engine Rules
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_postback'])) {
    $postback_url = isset($_POST['postback_url']) ? trim($_POST['postback_url']) : '';

    if (empty($postback_url)) {
        // Allow removing the postback URL
        try {
            $update_stmt = $pdo->prepare("UPDATE `affiliates` SET `postback_url` = NULL WHERE `id` = ? LIMIT 1");
            $update_stmt->execute([$affiliateId]);
            $_SESSION['postback_msg'] = "Postback endpoint configuration removed completely.";
            $_SESSION['postback_success'] = true;
        } catch (PDOException $e) {
            error_log("Affiliate Postback Clear Failure: " . $e->getMessage());
            $_SESSION['postback_msg'] = "Operational Break: An error occurred clearing configuration parameters.";
            $_SESSION['postback_success'] = false;
        }
    } else {
        // A. Basic URL validation scheme verification
        if (!filter_var($postback_url, FILTER_VALIDATE_URL)) {
            $_SESSION['postback_msg'] = "Error: Invalid URL string format structure detected.";
            $_SESSION['postback_success'] = false;
        } 
        // B. Enforce attribution parameter existence ([s1] or [s2] tracking tokens)
        elseif (strpos($postback_url, '[s1]') === false && strpos($postback_url, '[s2]') === false) {
            $_SESSION['postback_msg'] = "Error: Tracking configuration missing critical parameters. You must include either [s1] or [s2] within the URL.";
            $_SESSION['postback_success'] = false;
        } 
        // C. Enforce clean bracket nesting wrappers (reject raw naked keys, bad enclosures, or doubled brackets)
        else {
            $has_bad_brackets = false;

            // Check for illegal wrapper enclosures explicitly
            if (preg_match('/\[\[|\]\]|\{[^\}]+\}|\([^\)]+\)/', $postback_url)) {
                $has_bad_brackets = true;
            } else {
                // Parse out values to inspect for naked macros without flagging valid parameter names
                $query_string = parse_url($postback_url, PHP_URL_QUERY);
                if (!empty($query_string)) {
                    parse_str($query_string, $query_params);
                    $valid_system_macros = ['s1', 's2', 'price', 'payout', 'cid', 'tid'];
                    
                    foreach ($query_params as $key => $value) {
                        // If a value matches a macro keyword exactly without its square brackets, it's naked
                        if (in_array(strtolower($value), $valid_system_macros)) {
                            $has_bad_brackets = true;
                            break;
                        }
                    }
                }
            }

            if ($has_bad_brackets) {
                $_SESSION['postback_msg'] = "Error: Improper macro bracket encapsulation layout framework. Ensure variables use single square brackets exactly (e.g., [s1], [cid]) without doubling or alternative wrapper symbols.";
                $_SESSION['postback_success'] = false;
            } else {
                try {
                    $update_stmt = $pdo->prepare("UPDATE `affiliates` SET `postback_url` = ? WHERE `id` = ? LIMIT 1");
                    $update_stmt->execute([$postback_url, $affiliateId]);
                    $_SESSION['postback_msg'] = "Postback endpoint matrix configured and validated successfully.";
                    $_SESSION['postback_success'] = true;
                } catch (PDOException $e) {
                    error_log("Affiliate Postback Save Failure: " . $e->getMessage());
                    $_SESSION['postback_msg'] = "Operational Break: An error occurred saving configuration modifications.";
                    $_SESSION['postback_success'] = false;
                }
            }
        }
    }
    
    header("Location: affiliate-postback.php");
    exit;
}

// 3. Extract flash session alert variables down to local contexts
$status_msg = isset($_SESSION['postback_msg']) ? $_SESSION['postback_msg'] : "";
$status_success = isset($_SESSION['postback_success']) ? $_SESSION['postback_success'] : false;

unset($_SESSION['postback_msg'], $_SESSION['postback_success']);

// 4. Fetch existing configuration parameters for DOM input rendering
try {
    $stmt = $pdo->prepare("SELECT `postback_url` FROM `affiliates` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$affiliateId]);
    $saved_url = $stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Affiliate Postback Read Failure: " . $e->getMessage());
    $saved_url = "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>S2S Postback Integration Terminal — PartnerTerminal</title>
    <?php include 'affiliate-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-between">

    <?php include 'affiliate-navbar.php'; ?>

    <main class="max-w-[1650px] w-full mx-auto px-4 sm:px-6 pt-8 pb-16 grow space-y-6">

        <?php if (!empty($status_msg)): ?>
            <div class="p-4 rounded-xl text-xs font-bold border <?= $status_success ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-600' ?> text-left">
                <i class="fa-solid <?= $status_success ? 'fa-circle-check text-emerald-500' : 'fa-circle-exclamation text-red-500' ?> mr-1.5 text-sm align-middle"></i>
                <?= htmlspecialchars($status_msg) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm text-left space-y-6">
            <div class="space-y-1">
                <h1 class="text-lg font-black text-gray-900 tracking-tight">Server-to-Server (S2S) Postback URL</h1>
                <p class="text-xs text-gray-400 font-medium leading-relaxed">Configure your global automated tracking postback URL. Our platform engine will trigger a live GET request down to this endpoint string whenever a referred conversion occurs.</p>
            </div>

            <form method="POST" action="affiliate-postback.php" class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-gray-400 uppercase tracking-wider">Your Secure Postback Endpoint</label>
                    <input type="text" name="postback_url" placeholder="https://yourdomain.com/postback?clickid=[s2]&payout=[payout]&tx=[tid]" 
                           value="<?= htmlspecialchars($saved_url ?? '') ?>"
                           class="w-full bg-slate-50 border border-gray-200 text-xs font-mono rounded-xl px-4 py-3.5 text-slate-800 outline-none focus:border-indigo-500 focus:bg-white transition-all shadow-inner">
                </div>

                <div class="flex justify-end">
                    <button type="submit" name="save_postback" class="w-full sm:w-auto bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs px-6 py-3 rounded-xl transition-all cursor-pointer shadow-sm flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> Save Postback Link
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm text-left space-y-3">
            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-wider flex items-center gap-1.5"><i class="fa-solid fa-code text-indigo-600 text-sm"></i> Example Integration Mapping Syntax</h3>
            <div class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs overflow-x-auto border select-all border-neutral-800 font-semibold shadow-md leading-relaxed">
                mysite.com/postback.php?clickid=[s2]&value=[price]&rate=[payout]&txid=[tid]
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm text-left space-y-4">
            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-wider border-b pb-2 border-gray-100 flex items-center gap-1.5"><i class="fa-solid fa-tags text-indigo-600 text-sm"></i> Supported System Tracking Tokens</h3>
            
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-bold uppercase tracking-wider text-gray-400 bg-slate-50/20 border-b border-gray-100">
                            <th class="px-4 py-3 w-1/4">Token Matrix Key</th>
                            <th class="px-4 py-3 w-3/4">Attribution Explanation Variable Log</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs font-medium text-slate-700">
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-4 py-3.5 font-mono text-indigo-600 font-bold">[s1]</td>
                            <td class="px-4 py-3.5 text-gray-500 leading-relaxed">Sub ID 1 tracking data payload context string parameter value passed down from your original promotional reference link.</td>
                        </tr>
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-4 py-3.5 font-mono text-indigo-600 font-bold">[s2]</td>
                            <td class="px-4 py-3.5 text-gray-500 leading-relaxed">Sub ID 2 tracking data payload context string parameter value passed down from your original promotional reference link.</td>
                        </tr>
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-4 py-3.5 font-mono text-indigo-600 font-bold">[price]</td>
                            <td class="px-4 py-3.5 text-gray-500 leading-relaxed">Gross purchase value price associated with the specific system data plan tier matching the customer checkout sequence.</td>
                        </tr>
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-4 py-3.5 font-mono text-indigo-600 font-bold">[payout]</td>
                            <td class="px-4 py-3.5 text-gray-500 leading-relaxed">Calculated financial net partner commission split value payout awarded into your account matrix balances.</td>
                        </tr>
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-4 py-3.5 font-mono text-indigo-600 font-bold">[cid]</td>
                            <td class="px-4 py-3.5 text-gray-500 leading-relaxed">Unique internal transaction tracking click token generated directly across the Identity Search platform layer.</td>
                        </tr>
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-4 py-3.5 font-mono text-indigo-600 font-bold">[tid]</td>
                            <td class="px-4 py-3.5 text-gray-500 leading-relaxed">Unique alphanumeric 14-character transaction reference token generated upon a successful checkout event.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <footer class="w-full bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400 font-semibold">
        <div class="flex items-center justify-center gap-2 mb-2">
            <img src="public/logo.png" alt="Identity Search AI Logo" class="h-12 w-auto">
        </div>
        &copy; <?= date('Y'); ?> Identity Search AI Affiliate Portal. All rights reserved.
    </footer>

</body>
</html>
