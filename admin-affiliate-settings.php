<?php
/**
 * File: admin-affiliate-settings.php
 * Admin page to manage global affiliate bonus settings.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login");
    exit;
}

$success_msg = "";
$error_msg = "";

try {
    $settings = [];
    $stmt = $pdo->query("SELECT `setting_key`, `setting_value` FROM `affiliate_settings`");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    error_log("Admin Affiliate Settings Error: " . $e->getMessage());
    $error_msg = "Error loading settings.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bonus_amount = trim($_POST['global_bonus_amount'] ?? '');
    $bonus_type = $_POST['global_bonus_type'] ?? 'recursion';

    if (!is_numeric($bonus_amount) || (float)$bonus_amount < 0) {
        $error_msg = "Bonus amount must be a positive number.";
    } elseif (!in_array($bonus_type, ['recursion', 'fixed'])) {
        $error_msg = "Invalid bonus type.";
    } else {
        try {
            $upStmt = $pdo->prepare("UPDATE `affiliate_settings` SET `setting_value` = ? WHERE `setting_key` = 'global_bonus_amount'");
            $upStmt->execute([number_format((float)$bonus_amount, 2, '.', '')]);

            $upStmt2 = $pdo->prepare("UPDATE `affiliate_settings` SET `setting_value` = ? WHERE `setting_key` = 'global_bonus_type'");
            $upStmt2->execute([$bonus_type]);

            $settings['global_bonus_amount'] = number_format((float)$bonus_amount, 2, '.', '');
            $settings['global_bonus_type'] = $bonus_type;

            $_SESSION['flash_success'] = "Global affiliate bonus settings updated successfully.";
            header("Location: admin-affiliate-settings");
            exit;
        } catch (PDOException $e) {
            error_log("Admin Affiliate Settings Update Error: " . $e->getMessage());
            $error_msg = "Failed to update settings.";
        }
    }
}

if (isset($_SESSION['flash_success'])) {
    $success_msg = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Affiliate Settings — Admin Panel</title>
    <?php include 'admin-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased bg-[#f8fafc]">

    <?php include 'admin-sidebar.php'; ?>
    <?php include 'admin-navbar.php'; ?>

    <div id="sidebarContent" class="lg:ml-64 pt-16 min-h-screen">
        <main class="p-4 sm:p-6 space-y-6 max-w-3xl">

            <div>
                <h1 class="text-xl font-extrabold tracking-tight text-gray-900">Affiliate Bonus Settings</h1>
                <p class="text-xs text-gray-400">Configure the default referral bonus amount and type for all affiliates. Individual affiliates can override these settings.</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-5 flex items-center gap-1.5">
                    <i class="fa-solid fa-globe text-indigo-600"></i> Global Default Settings
                </h3>
                <form method="POST" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Bonus Amount (USD)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">$</span>
                                <input type="number" name="global_bonus_amount" step="0.01" min="0" value="<?= htmlspecialchars($settings['global_bonus_amount'] ?? '32.00') ?>" required
                                    class="w-full text-sm pl-8 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Flat amount paid per referral</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Bonus Type</label>
                            <div class="flex gap-3 mt-1">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="global_bonus_type" value="recursion" <?= ($settings['global_bonus_type'] ?? 'recursion') === 'recursion' ? 'checked' : '' ?> class="peer hidden">
                                    <div class="border-2 border-gray-200 rounded-xl p-3 text-center peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition">
                                        <i class="fa-solid fa-rotate text-indigo-600 text-lg mb-1"></i>
                                        <div class="text-xs font-bold text-gray-900">Recursion</div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">Recurring monthly</div>
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="global_bonus_type" value="fixed" <?= ($settings['global_bonus_type'] ?? 'recursion') === 'fixed' ? 'checked' : '' ?> class="peer hidden">
                                    <div class="border-2 border-gray-200 rounded-xl p-3 text-center peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition">
                                        <i class="fa-solid fa-lock text-emerald-600 text-lg mb-1"></i>
                                        <div class="text-xs font-bold text-gray-900">Fixed</div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">One-time on registration</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2.5 px-6 rounded-xl transition cursor-pointer">
                        Save Global Settings
                    </button>
                </form>
            </div>

            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-circle-info text-indigo-600 text-sm"></i>
                    </div>
                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-indigo-900">How Bonus Settings Work</h4>
                        <ul class="text-[11px] text-indigo-700 space-y-1.5 font-medium">
                            <li class="flex items-start gap-2">
                                <i class="fa-solid fa-check text-indigo-500 text-[8px] mt-1 flex-shrink-0"></i>
                                <span><strong>Recursion:</strong> Affiliate gets the bonus on first registration AND every monthly renewal while the referred user stays subscribed.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fa-solid fa-check text-indigo-500 text-[8px] mt-1 flex-shrink-0"></i>
                                <span><strong>Fixed:</strong> Affiliate gets the bonus only once when a user registers using their referral URL. No recurring payments.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fa-solid fa-check text-indigo-500 text-[8px] mt-1 flex-shrink-0"></i>
                                <span>Individual affiliates can override these global settings from the <strong>Edit Affiliate</strong> page.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <?php
    $alert_type = !empty($error_msg) ? 'error' : (!empty($success_msg) ? 'success' : '');
    $alert_message = !empty($error_msg) ? $error_msg : $success_msg;
    ?>
    <?php include 'alert-modal.php'; ?>

</body>
</html>
