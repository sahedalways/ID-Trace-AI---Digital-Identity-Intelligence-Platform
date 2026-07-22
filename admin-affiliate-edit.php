<?php
/**
 * File: admin-affiliate-edit.php
 * Admin page to edit affiliate profile details, reset password, and manage status.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

$affId = (int)($_GET['id'] ?? 0);
if (!$affId) {
    header("Location: admin-affiliates.php");
    exit;
}

$success_msg = "";
$error_msg = "";

try {
    $stmt = $pdo->prepare("SELECT * FROM `affiliates` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$affId]);
    $affiliate = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$affiliate) {
        header("Location: admin-affiliates.php");
        exit;
    }

    $payStmt = $pdo->prepare("SELECT * FROM `affiliate_payments` WHERE `affiliate_id` = ? LIMIT 1");
    $payStmt->execute([$affId]);
    $payment = $payStmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Admin Affiliate Edit Error: " . $e->getMessage());
    die("Error loading affiliate.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['form_action'] ?? '';

    if ($act === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $status = $_POST['status'] ?? $affiliate['status'];
        $contact = trim($_POST['contact'] ?? '');
        $experience_level = trim($_POST['experience_level'] ?? '');
        $traffic_source = trim($_POST['traffic_source'] ?? '');
        $past_experience = trim($_POST['past_experience'] ?? '');

        if (empty($name) || empty($email)) {
            $error_msg = "Name and email are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = "Invalid email address.";
        } else {
            $checkStmt = $pdo->prepare("SELECT id FROM `affiliates` WHERE `email` = ? AND `id` != ? LIMIT 1");
            $checkStmt->execute([$email, $affId]);
            if ($checkStmt->fetch()) {
                $error_msg = "Email address is already in use by another affiliate.";
            } else {
                $upStmt = $pdo->prepare("UPDATE `affiliates` SET `name` = ?, `email` = ?, `mobile` = ?, `country` = ?, `status` = ?, `contact` = ?, `experience_level` = ?, `traffic_source` = ?, `past_experience` = ? WHERE `id` = ?");
                $upStmt->execute([$name, $email, $mobile, $country, $status, $contact, $experience_level, $traffic_source, $past_experience, $affId]);
                $_SESSION['flash_success'] = "Affiliate profile updated successfully.";
                header("Location: admin-affiliate-edit.php?id=$affId");
                exit;
            }
        }
    }

    if ($act === 'reset_password') {
        $newPass = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($newPass) < 6) {
            $error_msg = "Password must be at least 6 characters.";
        } elseif ($newPass !== $confirm) {
            $error_msg = "Passwords do not match.";
        } else {
            $hashed = password_hash($newPass, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE `affiliates` SET `password` = ? WHERE `id` = ?")->execute([$hashed, $affId]);
            $_SESSION['flash_success'] = "Password has been reset successfully.";
            header("Location: admin-affiliate-edit.php?id=$affId");
            exit;
        }
    }

    if ($act === 'update_payment') {
        $method = $_POST['payment_method'] ?? '';
        $info = "";

        if ($method === 'payoneer') {
            $info = trim($_POST['payoneer_email'] ?? '');
        } elseif ($method === 'usdt_bep20') {
            $info = trim($_POST['usdt_address'] ?? '');
        } elseif ($method === 'bank_transfer') {
            $bank_fields = [
                "Bank Name" => trim($_POST['b_bank_name'] ?? ''),
                "Beneficiary Name" => trim($_POST['b_name'] ?? ''),
                "Account Number" => trim($_POST['b_acc'] ?? ''),
                "Routing Number" => trim($_POST['b_routing'] ?? ''),
                "Swift Code" => trim($_POST['b_swift'] ?? ''),
                "Bank Address" => trim($_POST['b_address'] ?? '')
            ];
            $parts = [];
            foreach ($bank_fields as $label => $value) {
                if (!empty($value)) $parts[] = $label . ": " . $value;
            }
            $info = implode(", ", $parts);
        }

        if (empty($method) || empty($info)) {
            $error_msg = "Payment method and details are required.";
        } else {
            if ($payment) {
                $pdo->prepare("UPDATE `affiliate_payments` SET `payment_method` = ?, `payment_info` = ? WHERE `affiliate_id` = ?")->execute([$method, $info, $affId]);
            } else {
                $pdo->prepare("INSERT INTO `affiliate_payments` (`affiliate_id`, `payment_method`, `payment_info`) VALUES (?, ?, ?)")->execute([$affId, $method, $info]);
            }
            $_SESSION['flash_success'] = "Payment info updated.";
            header("Location: admin-affiliate-edit.php?id=$affId");
            exit;
        }
    }
}

if (isset($_SESSION['flash_success'])) {
    $success_msg = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

$methodLabels = [
    'payoneer' => 'Payoneer',
    'usdt_bep20' => 'USDT (BEP-20)',
    'bank_transfer' => 'Bank Transfer'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Affiliate #<?= str_pad($affiliate['id'], 3, '0', STR_PAD_LEFT) ?> — Admin Panel</title>
    <?php include 'admin-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased bg-[#f8fafc]">

    <?php include 'admin-sidebar.php'; ?>
    <?php include 'admin-navbar.php'; ?>

    <div id="sidebarContent" class="lg:ml-64 pt-16 min-h-screen">
        <main class="p-4 sm:p-6 space-y-6 max-w-4xl">

            <div class="flex items-center gap-3">
                <a href="admin-affiliate-view.php?id=<?= $affId ?>" class="text-gray-400 hover:text-gray-900 transition">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-gray-900">Edit Affiliate #<?= str_pad($affiliate['id'], 3, '0', STR_PAD_LEFT) ?></h1>
                    <p class="text-xs text-gray-400">Update profile, payment info, and security credentials.</p>
                </div>
            </div>

            <!-- Profile Edit -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-5 flex items-center gap-1.5">
                    <i class="fa-solid fa-user-pen text-indigo-600"></i> Profile Information
                </h3>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="form_action" value="update_profile">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Full Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($affiliate['name']) ?>" required
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Email Address</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($affiliate['email']) ?>" required
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Telegram Username</label>
                            <input type="text" name="contact" value="<?= htmlspecialchars($affiliate['contact'] ?? '') ?>" placeholder="@username"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Mobile</label>
                            <input type="text" name="mobile" value="<?= htmlspecialchars($affiliate['mobile'] ?? '') ?>"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Country</label>
                            <input type="text" name="country" value="<?= htmlspecialchars($affiliate['country'] ?? '') ?>"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Status</label>
                            <select name="status" class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900 cursor-pointer">
                                <option value="active" <?= $affiliate['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="pending" <?= $affiliate['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="banned" <?= $affiliate['status'] === 'banned' ? 'selected' : '' ?>>Banned</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Experience Level</label>
                            <select name="experience_level" class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900 cursor-pointer">
                                <option value="New Affiliate" <?= ($affiliate['experience_level'] ?? '') === 'New Affiliate' ? 'selected' : '' ?>>New Affiliate</option>
                                <option value="Have some experience" <?= ($affiliate['experience_level'] ?? '') === 'Have some experience' ? 'selected' : '' ?>>Have some experience</option>
                                <option value="Expert" <?= ($affiliate['experience_level'] ?? '') === 'Expert' ? 'selected' : '' ?>>Expert</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Affiliate ID</label>
                            <input type="text" value="<?= htmlspecialchars($affiliate['aid']) ?>" readonly
                                class="w-full text-sm px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl font-mono font-bold text-gray-500 cursor-not-allowed">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Traffic Source & Promotional Strategy</label>
                        <textarea name="traffic_source" rows="2" placeholder="Describe traffic sources and promotional strategy..."
                            class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900"><?= htmlspecialchars($affiliate['traffic_source'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Past Affiliate Experience</label>
                        <textarea name="past_experience" rows="2" placeholder="Previous affiliate network experience..."
                            class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900"><?= htmlspecialchars($affiliate['past_experience'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2.5 px-6 rounded-xl transition cursor-pointer">
                        Save Changes
                    </button>
                </form>
            </div>

            <!-- Payment Profile -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-5 flex items-center gap-1.5">
                    <i class="fa-solid fa-wallet text-emerald-600"></i> Payment Profile
                </h3>
                <form method="POST" class="space-y-4" id="paymentForm">
                    <input type="hidden" name="form_action" value="update_payment">

                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Payment Gateway</label>
                        <select name="payment_method" id="paySelect" onchange="togglePayUI()" class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900 cursor-pointer">
                            <option value="">— Select Method —</option>
                            <option value="payoneer" <?= ($payment['payment_method'] ?? '') === 'payoneer' ? 'selected' : '' ?>>Payoneer</option>
                            <option value="usdt_bep20" <?= ($payment['payment_method'] ?? '') === 'usdt_bep20' ? 'selected' : '' ?>>USDT (BEP-20)</option>
                            <option value="bank_transfer" <?= ($payment['payment_method'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                        </select>
                    </div>

                    <?php
                    $bank_data = [];
                    if (($payment['payment_method'] ?? '') === 'bank_transfer') {
                        $lines = explode(", ", $payment['payment_info'] ?? '');
                        foreach ($lines as $line) {
                            $parts = explode(": ", $line, 2);
                            if (count($parts) === 2) {
                                $bank_data[trim($parts[0])] = trim($parts[1]);
                            }
                        }
                    }
                    ?>

                    <div id="form_payoneer" class="pay-box <?= ($payment['payment_method'] ?? '') !== 'payoneer' ? 'hidden' : '' ?> space-y-3">
                        <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3 flex items-center gap-2.5">
                            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-envelope text-emerald-600 text-sm"></i>
                            </div>
                            <span class="text-[11px] font-bold text-emerald-800">Enter Payoneer email for quick payouts</span>
                        </div>
                        <input type="email" name="payoneer_email" value="<?= ($payment['payment_method'] ?? '') === 'payoneer' ? htmlspecialchars($payment['payment_info']) : '' ?>" placeholder="Payoneer Email Address"
                            class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                    </div>

                    <div id="form_usdt_bep20" class="pay-box <?= ($payment['payment_method'] ?? '') !== 'usdt_bep20' ? 'hidden' : '' ?> space-y-3">
                        <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 flex items-center gap-2.5">
                            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-coins text-amber-600 text-sm"></i>
                            </div>
                            <span class="text-[11px] font-bold text-amber-800">Send USDT to your BEP-20 wallet address</span>
                        </div>
                        <input type="text" name="usdt_address" value="<?= ($payment['payment_method'] ?? '') === 'usdt_bep20' ? htmlspecialchars($payment['payment_info']) : '' ?>" placeholder="USDT Wallet Address (BEP-20)"
                            class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                    </div>

                    <div id="form_bank_transfer" class="pay-box <?= ($payment['payment_method'] ?? '') !== 'bank_transfer' ? 'hidden' : '' ?> space-y-3">
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 flex items-center gap-2.5">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-building-columns text-blue-600 text-sm"></i>
                            </div>
                            <span class="text-[11px] font-bold text-blue-800">Provide bank details for wire transfer</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <input type="text" name="b_bank_name" value="<?= htmlspecialchars($bank_data['Bank Name'] ?? '') ?>" placeholder="Bank Name"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                            <input type="text" name="b_name" value="<?= htmlspecialchars($bank_data['Beneficiary Name'] ?? '') ?>" placeholder="Beneficiary Name"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                            <input type="text" name="b_acc" value="<?= htmlspecialchars($bank_data['Account Number'] ?? '') ?>" placeholder="Account Number"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                            <input type="text" name="b_routing" value="<?= htmlspecialchars($bank_data['Routing Number'] ?? '') ?>" placeholder="Routing Number (optional)"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                            <input type="text" name="b_swift" value="<?= htmlspecialchars($bank_data['Swift Code'] ?? '') ?>" placeholder="Swift Code"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                        </div>
                        <textarea name="b_address" placeholder="Bank Branch Address" rows="2"
                            class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900"><?= htmlspecialchars($bank_data['Bank Address'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 px-6 rounded-xl transition cursor-pointer">
                        Update Payment Info
                    </button>
                </form>
            </div>

            <!-- Reset Password -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-5 flex items-center gap-1.5">
                    <i class="fa-solid fa-key text-amber-600"></i> Reset Password
                </h3>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="form_action" value="reset_password">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">New Password</label>
                            <input type="password" name="new_password" required minlength="6" placeholder="Minimum 6 characters"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-amber-500 transition font-semibold text-gray-900">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Confirm Password</label>
                            <input type="password" name="confirm_password" required minlength="6" placeholder="Repeat password"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-amber-500 transition font-semibold text-gray-900">
                        </div>
                    </div>

                    <button type="submit" onclick="return confirm('Are you sure you want to reset this affiliate\'s password?')" class="bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs py-2.5 px-6 rounded-xl transition cursor-pointer">
                        Reset Password
                    </button>
                </form>
            </div>

        </main>
    </div>

    <?php
    $alert_type = !empty($error_msg) ? 'error' : (!empty($success_msg) ? 'success' : '');
    $alert_message = !empty($error_msg) ? $error_msg : $success_msg;
    ?>
    <?php include 'alert-modal.php'; ?>

    <script>
    function togglePayUI() {
        const val = document.getElementById('paySelect').value;
        document.querySelectorAll('.pay-box').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.pay-box input, .pay-box textarea').forEach(i => { i.required = false; });
        if (val) {
            const target = document.getElementById('form_' + val);
            if (target) {
                target.classList.remove('hidden');
                target.querySelectorAll('input, textarea').forEach(i => i.required = true);
            }
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const sel = document.getElementById('paySelect');
        if (sel && sel.value) togglePayUI();
    });
    </script>

</body>
</html>
