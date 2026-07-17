<?php
include_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['affiliate_id'])) {
    header("Location: affiliate-login.php");
    exit();
}

$affiliate_id = $_SESSION['affiliate_id'];

$success_msg = "";
$error_msg = "";

// Fetch affiliate data
$stmt = $pdo->prepare("SELECT * FROM affiliates WHERE id = ?");
$stmt->execute([$affiliate_id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: affiliate-login.php");
    exit();
}

// Fetch payment profile from separate table
$pay_stmt = $pdo->prepare("SELECT * FROM affiliate_payments WHERE affiliate_id = ?");
$pay_stmt->execute([$affiliate_id]);
$payment = $pay_stmt->fetch();

$payment_filled = (!empty($payment) && !empty($payment['payment_method']) && !empty($payment['payment_info']));

// 1. Profile Update Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_name = trim($_POST['name']);
    $new_mobile = trim($_POST['mobile']);
    $new_country = trim($_POST['country']);

    try {
        $up_stmt = $pdo->prepare("UPDATE affiliates SET name = ?, mobile = ?, country = ? WHERE id = ?");
        $up_stmt->execute([$new_name, $new_mobile, $new_country, $affiliate_id]);
        $_SESSION['affiliate_name'] = $new_name;
        $_SESSION['flash_success'] = "Profile updated successfully!";
        header("Location: affiliate-profile.php");
            exit();
        } catch (PDOException $e) {
            $error_msg = "Update failed. Please try again.";
        }
}

// 2. Payment Update Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_payment'])) {
    $method = $_POST['payment_method'];
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

    if (!empty($info)) {
        try {
            if ($payment_filled) {
                $pay_stmt = $pdo->prepare("UPDATE affiliate_payments SET payment_method = ?, payment_info = ? WHERE affiliate_id = ?");
                $pay_stmt->execute([$method, $info, $affiliate_id]);
            } else {
                $pay_stmt = $pdo->prepare("INSERT INTO affiliate_payments (affiliate_id, payment_method, payment_info) VALUES (?, ?, ?)");
                $pay_stmt->execute([$affiliate_id, $method, $info]);
            }
            $_SESSION['flash_success'] = "Payment info updated successfully!";
            header("Location: affiliate-profile.php");
            exit();
        } catch (PDOException $e) {
            $error_msg = "Database error. Please try again.";
        }
    } else {
        $error_msg = "Please fill in the required payment details.";
    }
}

// 3. Password Change Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    if (!password_verify($old_pass, $user['password'])) {
        $error_msg = "Current password incorrect.";
    } elseif ($new_pass !== $confirm_pass) {
        $error_msg = "Passwords do not match.";
    } elseif (strlen($new_pass) < 6) {
        $error_msg = "Password must be at least 6 characters.";
    } else {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE affiliates SET password = ? WHERE id = ?")->execute([$hashed, $affiliate_id]);
        $success_msg = "Password updated successfully!";
    }
}

if (isset($_SESSION['flash_success'])) {
    $success_msg = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

$method_labels = [
    'payoneer' => 'Payoneer',
    'usdt_bep20' => 'USDT (BEP-20)',
    'bank_transfer' => 'Bank Transfer'
];
$method_icons = [
    'payoneer' => 'fa-solid fa-envelope',
    'usdt_bep20' => 'fa-solid fa-coins',
    'bank_transfer' => 'fa-solid fa-building-columns'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile Settings | Identity Search AI</title>
    <?php include 'head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
    <?php include 'affiliate-navbar.php'; ?>

    <div class="max-w-4xl mx-auto px-4 py-10 space-y-6">

        <?php if (!$payment_filled): ?>
            <div class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center shrink-0">
                        <i class="fa fa-wallet text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-amber-900 font-black text-xs uppercase tracking-tight">Payment Profile Required</h4>
                        <p class="text-amber-700 text-[10px] mt-0.5">Please select a payment method to receive your earnings.</p>
                    </div>
                </div>
                <a href="#payment-sec" class="bg-amber-600 text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-amber-700 transition-all">Setup</a>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Account Details -->
            <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/40 border border-slate-100">
                <h3 class="text-lg font-black mb-6 flex items-center gap-2">
                    <i class="fa fa-user-circle text-blue-600"></i> Account Details
                </h3>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Full Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Email Address</label>
                        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-500 cursor-not-allowed">
                        <p class="text-[9px] text-slate-400 font-bold mt-1 px-1"><i class="fa-solid fa-lock mr-1"></i> Email cannot be changed</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Mobile Number</label>
                        <input type="text" name="mobile" value="<?= htmlspecialchars($user['mobile'] ?? '') ?>" placeholder="+880 1XXXXXXXXX" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Country</label>
                        <input type="text" name="country" value="<?= htmlspecialchars($user['country']) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="bg-slate-50 rounded-xl px-4 py-3 border border-slate-100">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Affiliate ID</span>
                            <span class="text-xs font-mono font-bold text-[#128c7e]"><?= htmlspecialchars($user['aid']) ?></span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</span>
                            <span class="px-3 py-1 <?= $user['status'] === 'active' ? 'bg-emerald-100 text-emerald-700' : ($user['status'] === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') ?> text-[9px] font-black rounded-full uppercase">
                                <?= ucfirst($user['status']) ?>
                            </span>
                        </div>
                    </div>
                    <button type="submit" name="update_profile" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black uppercase text-[11px] tracking-widest shadow-lg hover:bg-slate-800 transition cursor-pointer">Save Profile</button>
                </form>
            </div>

            <!-- Payment Profile -->
            <div id="payment-sec" class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/40 border border-slate-100">
                <h3 class="text-lg font-black mb-6 flex items-center gap-2">
                    <i class="fa fa-wallet text-emerald-500"></i> Payment Profile
                </h3>

                <?php if ($payment_filled): ?>
                    <form method="POST" class="space-y-4" id="paymentForm">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Payment Gateway</label>
                            <select name="payment_method" id="paySelect" onchange="togglePayUI()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                                <option value="">— Select Method —</option>
                                <option value="payoneer" <?= $payment['payment_method'] === 'payoneer' ? 'selected' : '' ?>>Payoneer</option>
                                <option value="usdt_bep20" <?= $payment['payment_method'] === 'usdt_bep20' ? 'selected' : '' ?>>USDT (BEP-20)</option>
                                <option value="bank_transfer" <?= $payment['payment_method'] === 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                            </select>
                        </div>

                        <div id="form_payoneer" class="pay-box <?= $payment['payment_method'] !== 'payoneer' ? 'hidden' : '' ?> space-y-3">
                            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3 flex items-center gap-2.5">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-envelope text-emerald-600 text-sm"></i>
                                </div>
                                <span class="text-[11px] font-bold text-emerald-800">Enter your Payoneer email for quick payouts</span>
                            </div>
                            <?php
                            $current_val = ($payment['payment_method'] === 'payoneer') ? htmlspecialchars($payment['payment_info']) : '';
                            ?>
                            <input type="email" name="payoneer_email" value="<?= $current_val ?>" placeholder="Payoneer Email Address" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div id="form_usdt_bep20" class="pay-box <?= $payment['payment_method'] !== 'usdt_bep20' ? 'hidden' : '' ?> space-y-3">
                            <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 flex items-center gap-2.5">
                                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-coins text-amber-600 text-sm"></i>
                                </div>
                                <span class="text-[11px] font-bold text-amber-800">Send USDT to your BEP-20 wallet address</span>
                            </div>
                            <?php
                            $current_val = ($payment['payment_method'] === 'usdt_bep20') ? htmlspecialchars($payment['payment_info']) : '';
                            ?>
                            <input type="text" name="usdt_address" value="<?= $current_val ?>" placeholder="USDT Wallet Address (BEP-20)" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div id="form_bank_transfer" class="pay-box <?= $payment['payment_method'] !== 'bank_transfer' ? 'hidden' : '' ?> space-y-3">
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 flex items-center gap-2.5">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-building-columns text-blue-600 text-sm"></i>
                                </div>
                                <span class="text-[11px] font-bold text-blue-800">Provide your bank details for wire transfer</span>
                            </div>
                            <?php
                            $bank_data = [];
                            if ($payment['payment_method'] === 'bank_transfer') {
                                $lines = explode(", ", $payment['payment_info']);
                                foreach ($lines as $line) {
                                    $parts = explode(": ", $line, 2);
                                    if (count($parts) === 2) {
                                        $bank_data[trim($parts[0])] = trim($parts[1]);
                                    }
                                }
                            }
                            ?>
                            <input type="text" name="b_bank_name" value="<?= htmlspecialchars($bank_data['Bank Name'] ?? '') ?>" placeholder="Bank Name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                            <input type="text" name="b_name" value="<?= htmlspecialchars($bank_data['Beneficiary Name'] ?? '') ?>" placeholder="Beneficiary Name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                            <input type="text" name="b_acc" value="<?= htmlspecialchars($bank_data['Account Number'] ?? '') ?>" placeholder="Account Number" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                            <input type="text" name="b_routing" value="<?= htmlspecialchars($bank_data['Routing Number'] ?? '') ?>" placeholder="Routing Number (optional)" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                            <input type="text" name="b_swift" value="<?= htmlspecialchars($bank_data['Swift Code'] ?? '') ?>" placeholder="Swift Code" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                            <textarea name="b_address" placeholder="Bank Branch Address" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500"><?= htmlspecialchars($bank_data['Bank Address'] ?? '') ?></textarea>
                        </div>

                        <p class="text-[10px] text-red-500 font-bold px-1 italic hidden" id="paymentNote">
                            <i class="fa fa-info-circle mr-1"></i> Note: You cannot edit payment info after saving.
                        </p>

                        <button type="submit" name="update_payment" id="savePaymentBtn" class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-black uppercase text-[11px] tracking-widest shadow-lg hover:bg-emerald-700 transition cursor-pointer hidden">Update Payment Info</button>
                    </form>

                <?php else: ?>
                    <form method="POST" class="space-y-4" id="paymentForm">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Select Payment Gateway</label>
                            <select name="payment_method" id="paySelect" onchange="togglePayUI()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                                <option value="">— Select Method —</option>
                                <option value="payoneer">Payoneer</option>
                                <option value="usdt_bep20">USDT (BEP-20)</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <!-- Payoneer Form -->
                        <div id="form_payoneer" class="pay-box hidden space-y-3">
                            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3 flex items-center gap-2.5">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-envelope text-emerald-600 text-sm"></i>
                                </div>
                                <span class="text-[11px] font-bold text-emerald-800">Enter your Payoneer email for quick payouts</span>
                            </div>
                            <input type="email" name="payoneer_email" placeholder="Payoneer Email Address" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <!-- USDT BEP-20 Form -->
                        <div id="form_usdt_bep20" class="pay-box hidden space-y-3">
                            <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 flex items-center gap-2.5">
                                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-coins text-amber-600 text-sm"></i>
                                </div>
                                <span class="text-[11px] font-bold text-amber-800">Send USDT to your BEP-20 wallet address</span>
                            </div>
                            <input type="text" name="usdt_address" placeholder="USDT Wallet Address (BEP-20)" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <!-- Bank Transfer Form -->
                        <div id="form_bank_transfer" class="pay-box hidden space-y-3">
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 flex items-center gap-2.5">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-building-columns text-blue-600 text-sm"></i>
                                </div>
                                <span class="text-[11px] font-bold text-blue-800">Provide your bank details for wire transfer</span>
                            </div>
                            <input type="text" name="b_bank_name" placeholder="Bank Name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                            <input type="text" name="b_name" placeholder="Beneficiary Name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                            <input type="text" name="b_acc" placeholder="Account Number" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                            <input type="text" name="b_routing" placeholder="Routing Number (optional)" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                            <input type="text" name="b_swift" placeholder="Swift Code" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                            <textarea name="b_address" placeholder="Bank Branch Address" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                        </div>

                        <p class="text-[10px] text-red-500 font-bold px-1 italic hidden" id="paymentNote">
                            <i class="fa fa-info-circle mr-1"></i> Note: You cannot edit payment info after saving.
                        </p>

                        <button type="submit" name="update_payment" id="savePaymentBtn" class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-black uppercase text-[11px] tracking-widest shadow-lg hover:bg-emerald-700 transition cursor-pointer hidden">Save Payment Info</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/40 border border-slate-100 md:col-span-2">
                <h3 class="text-lg font-black mb-6 flex items-center gap-2">
                    <i class="fa fa-key text-orange-500"></i> Change Password
                </h3>
                <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="password" name="old_pass" placeholder="Current Password" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-orange-500">
                    <input type="password" name="new_pass" placeholder="New Password" required minlength="6" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-orange-500">
                    <input type="password" name="confirm_pass" placeholder="Confirm New Password" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold outline-none focus:ring-2 focus:ring-orange-500">
                    <div class="md:col-span-3">
                        <button type="submit" name="change_password" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black uppercase text-[11px] tracking-widest shadow-lg hover:bg-slate-800 transition cursor-pointer">Update Security</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <?php include 'footer.php'; ?>

    <?php
    $alert_type = $success_msg ? 'success' : ($error_msg ? 'error' : '');
    $alert_message = $success_msg ?: $error_msg;
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
            document.getElementById('paymentNote').classList.remove('hidden');
            document.getElementById('savePaymentBtn').classList.remove('hidden');
        } else {
            document.getElementById('paymentNote').classList.add('hidden');
            document.getElementById('savePaymentBtn').classList.add('hidden');
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const sel = document.getElementById('paySelect');
        if (sel && sel.value) togglePayUI();
    });
    </script>
</body>
</html>