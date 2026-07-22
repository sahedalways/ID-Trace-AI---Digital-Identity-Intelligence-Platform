<?php
/**
 * Identity Search AI — Secure Digital Communications Core Gateway
 * File: contact.php
 */
require_once 'config.php';
require_once 'mailer.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Session check to safely pre-fill data handles
$session_uid   = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$preload_name  = !empty($_SESSION['user_name']) ? trim($_SESSION['user_name']) : '';
$preload_email = !empty($_SESSION['user_email']) ? trim($_SESSION['user_email']) : '';

// 2. Fetch flashed PRG status messages from session storage frames
$success_message = $_SESSION['contact_flash_success'] ?? '';
$error_message   = $_SESSION['contact_flash_error'] ?? '';

// Unset immediately so they only display for a single execution pass
unset($_SESSION['contact_flash_success'], $_SESSION['contact_flash_error']);

// 3. Handle outbound message dispatch form metrics securely
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_name    = trim($_POST['contact_name'] ?? '');
    $input_email   = trim($_POST['contact_email'] ?? '');
    $input_subject = trim($_POST['contact_subject'] ?? '');
    $input_body    = trim($_POST['contact_body'] ?? '');

    if (empty($input_name) || empty($input_email) || empty($input_subject) || empty($input_body)) {
        $_SESSION['contact_flash_error'] = "All form matrix entries are required to route your communication.";
        header("Location: " . BASE_URL . "contact");
        exit;
    } elseif (!filter_var($input_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['contact_flash_error'] = "The provided electronic communication address layout remains invalid.";
        header("Location: " . BASE_URL . "contact");
        exit;
    } else {
        try {
            // Commit tracking entry payload dynamically to data layer table
            $stmt = $pdo->prepare("
                INSERT INTO `contact` (`uid`, `name`, `email`, `subject`, `body`, `created_at`)
                VALUES (:uid, :name, :email, :subject, :body, NOW())
            ");
            $stmt->execute([
                ':uid'     => $session_uid,
                ':name'    => $input_name,
                ':email'   => $input_email,
                ':subject' => $input_subject,
                ':body'    => $input_body
            ]);

            // Construct Transactional Email Confirmation Layout with clean thinned font metrics
            $escapedName    = htmlspecialchars($input_name, ENT_QUOTES, 'UTF-8');
            $escapedEmail   = htmlspecialchars($input_email, ENT_QUOTES, 'UTF-8');
            $escapedSubject = htmlspecialchars($input_subject, ENT_QUOTES, 'UTF-8');
            $escapedBody    = nl2br(htmlspecialchars($input_body, ENT_QUOTES, 'UTF-8'));

            $htmlBody = "
                <div style='background-color: #FAFAFA; padding: 24px 12px; font-family: \"Roboto\", -apple-system, BlinkMacSystemFont, sans-serif;'>
                    <div style='max-width: 380px; margin: 0 auto; background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.04);'>

                        <!-- HEADER LOGO STACK -->
                        <div style='padding: 14px 20px; border-bottom: 1px solid #E5E7EB; text-align: center; background-color: #F9FAFB;'>
                            <div style='display: inline-block; vertical-align: middle; text-align: center;'>
                                <img src='https://i.postimg.cc/SQnMm8sh/2313362.png' alt='Identity Search AI Logo' style='width: 28px; height: 28px; display: inline-block; vertical-align: middle; margin-right: 6px; border: 0;'>
                                <span style='font-size: 14px; font-weight: 800; color: #111827; letter-spacing: -0.3px; display: inline-block; vertical-align: middle;'>Identity Search <span style='font-size: 10px; font-weight: 900; background-color: #000000; color: #FFFFFF; padding: 1.5px 5px; border-radius: 3.5px; margin-left: 3px; vertical-align: middle; letter-spacing: 0.5px;'>AI</span></span>
                            </div>
                        </div>

                        <!-- BODY MAIN SECTOR WITH SMALL TEXT LAYOUT -->
                        <div style='padding: 24px 20px; text-align: left;'>
                            <h2 style='font-size: 16px; font-weight: 700; color: #111827; margin-top: 0; margin-bottom: 10px;'>We received your support request</h2>
                            <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-bottom: 18px;'>Thank you for reaching out to Identity Search AI. We have successfully logged your technical data request, and our engineering team will review your inquiry and respond shortly.</p>

                            <div style='background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 10px; padding: 12px; margin-bottom: 18px;'>
                                <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Name:</b> {$escapedName}</div>
                                <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Email:</b> {$escapedEmail}</div>
                                <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Subject:</b> {$escapedSubject}</div>
                                <div style='font-size: 11px; color: #4B5563; line-height: 1.4;'><b style='color: #111827; display: block; margin-bottom: 4px;'>Details:</b><div style='color: #6B7280;'>{$escapedBody}</div></div>
                            </div>
                        </div>

                        <!-- FOOTER BRAND SECTOR -->
                        <div style='padding: 20px; border-top: 1px solid #F3F4F6; background-color: #FAFAFA; text-align: center;'>
                            <div style='display: block; margin-bottom: 8px;'>
                                <span style='font-size: 16px; display: inline-block; vertical-align: middle;'>🕵️‍♂️</span>
                            </div>
                            <p style='font-size: 9px; color: #4B5563; font-weight: 500; margin: 0 0 4px 0;'>&copy; 2026 - Identity Search AI</p>
                            <p style='font-size: 9px; color: #4B5563; font-weight: 400; margin: 0;'>
                                <a href='mailto:support@identitysearch.ai' style='color: #128c7e; text-decoration: none;'>support@identitysearch.ai</a>
                            </p>
                        </div>

                    </div>
                </div>
            ";

            $emailSubject = "We received your support request";
            sendTransactionalMail($input_email, $emailSubject, $htmlBody);

            $_SESSION['contact_flash_success'] = "Message dispatched successfully. A confirmation receipt copy has been sent to your inbox.";

            // Post/Redirect/Get Execution Termination Loop
            header("Location: " . BASE_URL . "contact");
            exit;
        } catch (Exception $dbEx) {
            $_SESSION['contact_flash_error'] = "Operational tracking error: " . $dbEx->getMessage();
            header("Location: " . BASE_URL . "contact");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Support Center — Identity Search AI</title>
    <?php include 'head.php'; ?>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white bg-slate-50">

    <?php include 'navbar.php'; ?>

    <main class="flex-grow max-w-xl w-full mx-auto px-4 sm:px-6 pt-12 pb-16">
        <div class="space-y-6">

            <div class="text-center space-y-2">
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Support Center</h1>
                <p class="text-base text-black font-semibold max-w-sm mx-auto leading-relaxed">
                    Have questions or need technical support? Drop us a message below and we will respond shortly.
                </p>
            </div>

            <!-- STATUS RESPONSE INTERFACES -->
            <?php if (!empty($success_message)): ?>
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3 text-left">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg mt-0.5"></i>
                    <p class="text-sm text-emerald-900 font-bold leading-normal"><?php echo htmlspecialchars($success_message); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3 text-left">
                    <i class="fa-solid fa-circle-exclamation text-red-600 text-lg mt-0.5"></i>
                    <p class="text-sm text-red-900 font-bold leading-normal"><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php endif; ?>

            <!-- CONTACT SUBMISSION GATEWAY -->
            <form id="contactSubmissionForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xl space-y-5 text-left">

                <div class="space-y-1.5">
                    <label for="contact_name" class="text-xs font-black uppercase text-gray-400 tracking-wider">Your Full Name</label>
                    <input
                        type="text"
                        name="contact_name"
                        id="contact_name"
                        value="<?php echo htmlspecialchars($preload_name); ?>"
                        placeholder="Enter your name"
                        class="w-full bg-slate-50 border border-gray-200 rounded-xl px-4 py-3 text-base text-black font-semibold outline-none focus:border-[#128c7e] focus:bg-white transition"
                        required
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="contact_email" class="text-xs font-black uppercase text-gray-400 tracking-wider">Your Email Address</label>
                    <input
                        type="email"
                        name="contact_email"
                        id="contact_email"
                        value="<?php echo htmlspecialchars($preload_email); ?>"
                        placeholder="name@example.com"
                        class="w-full bg-slate-50 border border-gray-200 rounded-xl px-4 py-3 text-base text-black font-semibold outline-none focus:border-[#128c7e] focus:bg-white transition"
                        required
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="contact_subject" class="text-xs font-black uppercase text-gray-400 tracking-wider">Inquiry Subject</label>
                    <input
                        type="text"
                        name="contact_subject"
                        id="contact_subject"
                        placeholder="What are you reaching out about?"
                        class="w-full bg-slate-50 border border-gray-200 rounded-xl px-4 py-3 text-base text-black font-semibold outline-none focus:border-[#128c7e] focus:bg-white transition"
                        required
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="contact_body" class="text-xs font-black uppercase text-gray-400 tracking-wider">Message Details</label>
                    <textarea
                        name="contact_body"
                        id="contact_body"
                        rows="5"
                        placeholder="Type the full details of your message here..."
                        class="w-full bg-slate-50 border border-gray-200 rounded-xl px-4 py-3 text-base text-black font-semibold outline-none focus:border-[#128c7e] focus:bg-white transition resize-none"
                        required
                    ></textarea>
                </div>

                <button type="submit" id="submitContactBtn" class="w-full bg-[#128c7e] hover:bg-[#0e6f64] active:scale-[0.99] text-white py-4 rounded-xl text-base font-bold transition-all flex items-center justify-center gap-2 shadow-md shadow-emerald-100 cursor-pointer mt-2">
                    <i id="btnIconNode" class="fa-solid fa-paper-plane text-sm shrink-0"></i>
                    <span id="btnTextNode">Send Message</span>
                </button>

            </form>
        </div>
    </main>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

    <!-- ANIMATED LOCKOUT FORM BLOCK INTERCEPTOR SCRIPT -->
    <script>
    document.getElementById('contactSubmissionForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('submitContactBtn');
        const iconNode = document.getElementById('btnIconNode');
        const textNode = document.getElementById('btnTextNode');

        if (btn && iconNode && textNode) {
            // Terminate double processing flows instantly
            btn.style.pointerEvents = 'none';
            btn.classList.add('opacity-80');

            // Trigger animation processing layouts
            iconNode.className = "fa-solid fa-spinner animate-spin text-sm shrink-0";
            textNode.textContent = "Processing...";
        }
    });
    </script>
</body>
</html>