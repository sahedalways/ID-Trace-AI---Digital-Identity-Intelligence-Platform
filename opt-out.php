<?php
/**
 * File: opt-out.php
 * User data opt-out / removal request page.
 */
require_once 'config.php';
require_once 'mailer.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$session_email = !empty($_SESSION['user_email']) ? trim($_SESSION['user_email']) : '';
$success_message = $_SESSION['optout_flash_success'] ?? '';
$error_message   = $_SESSION['optout_flash_error'] ?? '';
unset($_SESSION['optout_flash_success'], $_SESSION['optout_flash_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_name    = trim($_POST['optout_name'] ?? '');
    $input_email   = trim($_POST['optout_email'] ?? '');
    $input_detail  = trim($_POST['optout_detail'] ?? '');

    if (empty($input_name) || empty($input_email)) {
        $_SESSION['optout_flash_error'] = "Name and email are required.";
        header("Location: opt-out.php");
        exit;
    } elseif (!filter_var($input_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['optout_flash_error'] = "Invalid email address.";
        header("Location: opt-out.php");
        exit;
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO `opt_out_requests` (`name`, `email`, `detail`, `status`, `created_at`) VALUES (?, ?, ?, 'pending', NOW())");
            $stmt->execute([$input_name, $input_email, $input_detail ?: null]);

            $escapedName  = htmlspecialchars($input_name, ENT_QUOTES, 'UTF-8');
            $escapedEmail = htmlspecialchars($input_email, ENT_QUOTES, 'UTF-8');
            $escapedDetail = htmlspecialchars($input_detail ?: 'No additional details provided.', ENT_QUOTES, 'UTF-8');
            $htmlBody = "
                <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:30px;'>
                    <h2 style='color:#128c7e;'>Opt-Out Request Received</h2>
                    <p>We have received your data removal request. Our team will review and process it shortly.</p>
                    <div style='background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin:20px 0;'>
                        <p><strong>Name:</strong> {$escapedName}</p>
                        <p><strong>Email:</strong> {$escapedEmail}</p>
                        <p><strong>Details:</strong> {$escapedDetail}</p>
                    </div>
                    <p style='color:#64748b;font-size:13px;'>If you have questions, contact us at support@identitysearch.ai</p>
                </div>";
            @sendTransactionalMail($input_email, "Opt-Out Request Confirmation", $htmlBody);

            $_SESSION['optout_flash_success'] = "Your opt-out request has been submitted successfully. We will process it within 30 days.";
            header("Location: opt-out.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['optout_flash_error'] = "Something went wrong. Please try again later.";
            header("Location: opt-out.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Opt-Out — Identity Search AI</title>
    <?php include 'head.php'; ?>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white bg-slate-50">

    <?php include 'navbar.php'; ?>

    <main class="flex-grow max-w-xl w-full mx-auto px-4 sm:px-6 pt-12 pb-16">
        <div class="space-y-6">

            <div class="text-center space-y-2">
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Opt-Out Request</h1>
                <p class="text-base text-black font-semibold max-w-sm mx-auto leading-relaxed">
                    Want your data removed from our platform? Submit a request below and we will process it within 30 days.
                </p>
            </div>

            <?php
            $alert_type = !empty($success_message) ? 'success' : (!empty($error_message) ? 'error' : '');
            $alert_message = $success_message ?: $error_message;
            ?>
            <?php include 'alert-modal.php'; ?>

            <form action="opt-out.php" method="POST" class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xl space-y-5 text-left">

                <div class="space-y-1.5">
                    <label for="optout_name" class="text-xs font-black uppercase text-gray-400 tracking-wider">Full Name</label>
                    <input type="text" name="optout_name" id="optout_name" placeholder="Enter your full name"
                        class="w-full bg-slate-50 border border-gray-200 rounded-xl px-4 py-3 text-base text-black font-semibold outline-none focus:border-[#128c7e] focus:bg-white transition" required>
                </div>

                <div class="space-y-1.5">
                    <label for="optout_email" class="text-xs font-black uppercase text-gray-400 tracking-wider">Email Address</label>
                    <input type="email" name="optout_email" id="optout_email" value="<?= htmlspecialchars($session_email) ?>" placeholder="name@example.com"
                        class="w-full bg-slate-50 border border-gray-200 rounded-xl px-4 py-3 text-base text-black font-semibold outline-none focus:border-[#128c7e] focus:bg-white transition" required>
                </div>

                <div class="space-y-1.5">
                    <label for="optout_detail" class="text-xs font-black uppercase text-gray-400 tracking-wider">Additional Details (Optional)</label>
                    <textarea name="optout_detail" id="optout_detail" rows="4" placeholder="Any specific information you'd like us to know..."
                        class="w-full bg-slate-50 border border-gray-200 rounded-xl px-4 py-3 text-base text-black font-semibold outline-none focus:border-[#128c7e] focus:bg-white transition resize-none"></textarea>
                </div>

                <button type="submit" class="w-full bg-[#128c7e] hover:bg-[#0e6f64] active:scale-[0.99] text-white py-4 rounded-xl text-base font-bold transition-all flex items-center justify-center gap-2 shadow-md shadow-emerald-100 cursor-pointer mt-2">
                    <i class="fa-solid fa-user-slash text-sm shrink-0"></i>
                    Submit Opt-Out Request
                </button>

            </form>

            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-3">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-[#128c7e]"></i> What happens next?
                </h3>
                <ul class="space-y-2 text-sm text-gray-600 font-medium">
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-[#128c7e] mt-1 text-xs"></i> We will review your request within 5 business days.</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-[#128c7e] mt-1 text-xs"></i> Your data will be removed within 30 days of verification.</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-[#128c7e] mt-1 text-xs"></i> You will receive an email confirmation once the process is complete.</li>
                </ul>
            </div>

        </div>
    </main>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

</body>
</html>
