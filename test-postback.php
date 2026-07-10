<?php
/**
 * File: test-postback.php
 * Real-time Server-to-Server (S2S) Postback Testing Utility Console.
 * Fires simulated cURL tracking loops and intercepts structural response packets.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Enforce strict authentication walls
if (!isset($_SESSION['affiliate_id'])) {
    header("Location: affiliate-login.php");
    exit;
}

// 2. Handle POST Testing Action Vector
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fire_test'])) {
    $test_url = isset($_POST['test_url']) ? trim($_POST['test_url']) : '';

    if (empty($test_url) || !filter_var($test_url, FILTER_VALIDATE_URL)) {
        $_SESSION['test_error'] = "Error: Invalid URL string target formatting schema.";
    } else {
        // Initialize high-performance execution loop payload check
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $test_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HEADER, true); // Intercept response headers
        curl_setopt($ch, CURLOPT_USERAGENT, 'IdentityTrace-PostbackEngine/1.2 (Test Hook Matrix)');

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        if (curl_errno($ch)) {
            $_SESSION['test_error'] = "Network Error: " . curl_error($ch);
        } else {
            // Split raw tracking headers down from body payload contents
            $_SESSION['test_http_code'] = $http_code;
            $_SESSION['test_headers']   = trim(substr($response, 0, $header_size));
            $_SESSION['test_body']      = trim(substr($response, $header_size));
        }
        curl_close($ch);
    }

    // Direct redirection pass onto itself clears submission context loops
    header("Location: test-postback.php");
    exit;
}

// 3. Extract execution response states from session caches
$error_msg  = $_SESSION['test_error'] ?? "";
$http_code  = $_SESSION['test_http_code'] ?? "";
$resp_head  = $_SESSION['test_headers'] ?? "";
$resp_body  = $_SESSION['test_body'] ?? "";

// Clean session indicators instantly right after extraction sequence completion
unset($_SESSION['test_error'], $_SESSION['test_http_code'], $_SESSION['test_headers'], $_SESSION['test_body']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Postback Debug Terminal — PartnerTerminal</title>
    <?php include 'affiliate-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-between">

    <?php include 'affiliate-navbar.php'; ?>

    <main class="max-w-4xl w-full mx-auto px-4 sm:px-6 pt-8 pb-16 grow space-y-6">

        <?php if (!empty($error_msg)): ?>
            <div class="p-4 rounded-xl text-xs font-bold border bg-red-50 border-red-200 text-red-600 text-left">
                <i class="fa-solid fa-circle-exclamation text-red-500 mr-1.5 text-sm align-middle"></i>
                <?= htmlspecialchars($error_msg) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm text-left space-y-6">
            <div class="space-y-1">
                <h1 class="text-lg font-black text-gray-900 tracking-tight">Postback Endpoint Live Sandbox</h1>
                <p class="text-xs text-gray-400 font-medium leading-relaxed">Simulate a live partner-triggered routing event. Input your complete endpoint URL pattern string with tracking arguments included to test firewall permissions and server response states.</p>
            </div>

            <form method="POST" action="test-postback.php" class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-gray-400 uppercase tracking-wider">Test Destination URL String</label>
                    <input type="url" name="test_url" required 
                           placeholder="https://mysite.com/postback.php?clickid=test_token_99&value=49.00&rate=24.50" 
                           class="w-full bg-slate-50 border border-gray-200 text-xs font-mono rounded-xl px-4 py-3.5 text-slate-800 outline-none focus:border-indigo-500 focus:bg-white transition-all shadow-inner">
                </div>

                <div class="flex justify-end">
                    <button type="submit" name="fire_test" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-6 py-3 rounded-xl transition-all cursor-pointer shadow-sm flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-bolt text-xs"></i> Fire Test Request Loop
                    </button>
                </div>
            </form>
        </div>

        <?php if ($http_code !== ""): ?>
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm text-left space-y-5">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-terminal text-indigo-600 text-sm"></i> Gateway Transmission Intercept Result
                </h3>
                <span class="inline-block font-mono text-xs font-black px-2.5 py-1 rounded-md <?= (int)$http_code >= 200 && (int)$http_code < 300 ? 'bg-emerald-50 border border-emerald-100 text-emerald-700' : 'bg-amber-50 border border-amber-100 text-amber-700' ?>">
                    HTTP Status: <?= $http_code ?>
                </span>
            </div>

            <div class="space-y-1.5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1"><i class="fa-solid fa-server opacity-60"></i> Server Response Headers Stream</p>
                <pre class="bg-slate-900 text-slate-300 font-mono text-[11px] p-4 rounded-xl overflow-x-auto border border-neutral-800 font-semibold leading-relaxed max-h-48 text-left whitespace-pre-wrap"><?= htmlspecialchars($resp_head) ?></pre>
            </div>

            <div class="space-y-1.5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1"><i class="fa-solid fa-file-code opacity-60"></i> Server Response Body Content String</p>
                <pre class="bg-slate-900 text-indigo-300 font-mono text-[11px] p-4 rounded-xl overflow-x-auto border border-neutral-800 font-semibold leading-relaxed text-left whitespace-pre-wrap"><?= !empty($resp_body) ? htmlspecialchars($resp_body) : '[Empty String Content Vector Returned]' ?></pre>
            </div>
        </div>
        <?php endif; ?>

    </main>

    <footer class="w-full bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400 font-semibold">
        &copy; <?= date('Y'); ?> Identity Search AI Affiliate Portal. Debug Matrix Node.
    </footer>

</body>
</html>
