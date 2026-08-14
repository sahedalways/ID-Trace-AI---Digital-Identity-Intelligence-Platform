<?php
/**
 * OSINT Universal Intelligence Console — Billing Management
 * File: billing.php
 */
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: signin"); exit; }

$user_id = (int)$_SESSION['user_id'];
$api_key = STRIPE_TEST_SECRET_KEY;

// 1. Fetch Stripe Customer ID
$u_stmt = $pdo->prepare("SELECT stripe_customer_id FROM users WHERE id = ?");
$u_stmt->execute([$user_id]);
$ud = $u_stmt->fetch(PDO::FETCH_ASSOC);
$customer_id = $ud['stripe_customer_id'] ?? die("No Stripe profile found.");

function stripeCoreCall($endpoint, $postData, $apiKey, $method = 'POST') {
    $ch = curl_init("https://api.stripe.com/v1/" . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":");
    if ($method !== 'POST') curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    else { curl_setopt($ch, CURLOPT_POST, true); curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData)); }
    $res = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $res;
}

// 2. Handle POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $pm_id = $_POST['pm_id'];
    if ($_POST['action'] === 'delete') {
        stripeCoreCall("payment_methods/$pm_id/detach", [], $api_key, 'POST');
    } elseif ($_POST['action'] === 'default') {
        stripeCoreCall("customers/$customer_id", ['invoice_settings' => ['default_payment_method' => $pm_id]], $api_key, 'POST');
    }
    header("Location: billing"); exit;
}

// 3. Fetch Data
$cards = stripeCoreCall("payment_methods?customer=$customer_id&type=card", [], $api_key, 'GET');
$setup = stripeCoreCall("setup_intents", ['customer' => $customer_id], $api_key, 'POST');
$cust = stripeCoreCall("customers/$customer_id", [], $api_key, 'GET');
$default_pm = $cust['invoice_settings']['default_payment_method'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Billing Management — Identity Search AI</title>
    <?php include 'head.php'; ?>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body class="bg-gray-50 min-h-screen relative">
    <!-- Sticky Glassmorphic Navbar Container -->
    <header id="mainNavbar" class="sticky top-0 z-50 bg-transparent transition-all duration-300">
        <?php include 'navbar.php'; ?>
    </header>

    <!-- Full-width Background Decorations -->
    <div class="absolute inset-x-0 top-0 -z-10 overflow-hidden" style="min-height: 100vh; background: linear-gradient(180deg, #BFE4FD 0%, #FFFFFF 100%);">
        <div class="blob-1 absolute top-0 left-1/2 w-[900px] h-[900px] bg-[#0072bc]/10 rounded-full blur-3xl opacity-60 -translate-x-1/2 will-change-transform"></div>
        <div class="blob-2 absolute top-24 -left-20 w-96 h-96 bg-[#0072bc]/15 rounded-full blur-3xl will-change-transform"></div>
        <div class="blob-3 absolute bottom-0 right-0 w-96 h-96 bg-[#BFE4FD]/50 rounded-full blur-3xl opacity-70 will-change-transform"></div>
    </div>

    <style>
        @keyframes blobMove1 { 0%, 100% { transform: translateX(-50%) translateY(0); } 25% { transform: translateX(-40%) translateY(-20px); } 50% { transform: translateX(-50%) translateY(-10px); } 75% { transform: translateX(-60%) translateY(10px); } }
        @keyframes blobMove2 { 0%, 100% { transform: translate(0, 0); } 33% { transform: translate(30px, -15px); } 66% { transform: translate(-20px, 20px); } }
        @keyframes blobMove3 { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(-40px, -20px); } }
        .blob-1 { animation: blobMove1 18s ease-in-out infinite; }
        .blob-2 { animation: blobMove2 14s ease-in-out infinite; }
        .blob-3 { animation: blobMove3 16s ease-in-out infinite; }
        .navbar-scrolled { background: linear-gradient(to right, #eef5ff 20%, #0072bc 100%); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); }
        #mainNavbar #siteNavbar { background: transparent !important; border-bottom: none !important; box-shadow: none !important; }
        #siteNavbar a, #siteNavbar button, #siteNavbar .text-black, #siteNavbar .text-slate-400, #siteNavbar .text-\[\#0072bc\] { transition: color 0.35s ease, background-color 0.35s ease, border-color 0.35s ease; }
        #mainNavbar.navbar-scrolled #siteNavbar a:not(#userDropdownMenu a, #mobileDrawer a),
        #mainNavbar.navbar-scrolled #siteNavbar button:not(#userDropdownMenu button, #mobileDrawer button),
        #mainNavbar.navbar-scrolled #siteNavbar .text-black:not(#userDropdownMenu, #mobileDrawer),
        #mainNavbar.navbar-scrolled #siteNavbar .text-slate-400:not(#userDropdownMenu .text-slate-400, #mobileDrawer .text-slate-400),
        #mainNavbar.navbar-scrolled #siteNavbar .text-\[\#0072bc\]:not(#userDropdownMenu .text-\[\#0072bc\], #mobileDrawer .text-\[\#0072bc\]) { color: #ffffff !important; }
        #mainNavbar.navbar-scrolled #siteNavbar a:hover:not(#userDropdownMenu a, #mobileDrawer a),
        #mainNavbar.navbar-scrolled #siteNavbar button:hover:not(#userDropdownMenu button, #mobileDrawer button, #mobileMenuButton) { color: #ffffff !important; }
        #mainNavbar.navbar-scrolled #siteNavbar #mobileMenuButton { color: #ffffff !important; border-color: rgba(255, 255, 255, 0.25); }
        #mainNavbar.navbar-scrolled #siteNavbar #mobileMenuButton:hover,
        #mainNavbar.navbar-scrolled #siteNavbar #userMenuButton:hover { background-color: rgba(255, 255, 255, 0.15); color: #ffffff !important; }
    </style>
    <main class="max-w-xl mx-auto p-6 space-y-8">
        
        <div class="bg-white p-6 rounded-3xl border shadow-sm">
            <h2 class="text-xl font-black mb-6">Payment Methods</h2>
            <?php foreach ($cards['data'] as $pm): ?>
            <div class="flex items-center justify-between p-5 border rounded-2xl mb-4">
                <div>
                    <p class="text-sm font-black capitalize"><?php echo $pm['card']['brand']; ?> ending <?php echo $pm['card']['last4']; ?></p>
                    <?php if ($pm['id'] === $default_pm): ?>
                        <span class="text-[10px] bg-blue-100 text-blue-700 px-2 rounded-full font-bold">DEFAULT</span>
                    <?php endif; ?>
                </div>
                <div class="flex gap-3">
                    <form method="POST" onsubmit="return confirm('Set this card as your default payment method?')">
                        <input type="hidden" name="pm_id" value="<?php echo $pm['id']; ?>">
                        <button name="action" value="default" class="text-xs font-bold text-blue-600 hover:underline">Default</button>
                    </form>
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this card?')">
                        <input type="hidden" name="pm_id" value="<?php echo $pm['id']; ?>">
                        <button name="action" value="delete" class="text-xs font-bold text-red-500 hover:underline">Delete</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="bg-white p-6 rounded-3xl border shadow-sm">
            <h2 class="text-xl font-black mb-4">Add New Card</h2>
            <button id="showStripeBtn" class="w-full bg-slate-900 text-white py-4 rounded-xl font-extrabold">Add New Card</button>
            <div id="stripe-form-wrapper" class="hidden mt-4 pt-4 border-t">
                <div id="setup-element-target" class="mb-4"></div>
                <button id="confirmAddCardBtn" class="w-full bg-green-600 text-white py-4 rounded-xl font-extrabold">Confirm Card</button>
            </div>
            <div id="card-errors" class="text-red-500 text-xs font-bold pt-2"></div>
        </div>
    </main>

    <script>
        const stripe = Stripe("<?php echo STRIPE_TEST_PUBLISHABLE_KEY; ?>");
        let elements;

        document.getElementById('showStripeBtn').onclick = function() {
            this.classList.add('hidden');
            document.getElementById('stripe-form-wrapper').classList.remove('hidden');
            
            elements = stripe.elements({ clientSecret: "<?php echo $setup['client_secret']; ?>", appearance: { theme: 'stripe' } });
            const paymentElement = elements.create('payment');
            paymentElement.mount('#setup-element-target');
        };

        document.getElementById('confirmAddCardBtn').onclick = async () => {
            const { error } = await stripe.confirmSetup({
                elements,
                confirmParams: { return_url: window.location.href }
            });
            if (error) document.getElementById('card-errors').textContent = error.message;
        };
    </script>
    <script>
        // NAVBAR SCROLL GLASS EFFECT
        document.addEventListener("DOMContentLoaded", () => {
            const navbar = document.getElementById("mainNavbar");
            if (!navbar) return;
            const handleScroll = () => {
                if (window.scrollY > 20) {
                    navbar.classList.add("navbar-scrolled");
                } else {
                    navbar.classList.remove("navbar-scrolled");
                }
            };
            window.addEventListener("scroll", handleScroll, { passive: true });
            handleScroll();
        });
    </script></body>
</html>
