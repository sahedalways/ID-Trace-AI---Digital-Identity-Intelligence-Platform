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
<body class="bg-gray-50 min-h-screen">
    <?php include 'navbar.php'; ?>
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
</body>
</html>