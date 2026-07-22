<?php
/**
 * File: refund.php
 * Billing Cancellation & Refund Policy — exact content from Billing Cancellation and Refund Policy.docx
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Billing Cancellation & Refund Policy — Identity Search AI</title>
    <?php include 'head.php'; ?>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white bg-slate-50">

    <?php include 'navbar.php'; ?>

    <main class="flex-grow max-w-4xl w-full mx-auto px-4 sm:px-6 pt-12 pb-16">
        <div class="space-y-8">

            <div class="text-center space-y-2">
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Subscription, Billing Cancellation and Refund Policy</h1>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest pt-2">Last Updated: July 2026</p>
            </div>

            <div class="space-y-4" id="refundAccordionContainer">

                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">1. Subscription &amp; Billing</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>By selecting a membership plan (Monthly, Quarterly, Semi-Annual, or Annual) on Identity Search AI, you expressly authorize us to charge your payment method immediately for the initial term and automatically on a recurring basis at the start of each renewal period until you cancel.</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li><b>Billing Cycles:</b> Depending on the selected plan, your card will be charged every 30 days ($36/month), every 3 months ($55/quarter), every 6 months ($72/semi-annually), or every 12 months ($96/annually).</li>
                                <li><b>Billing Descriptor:</b> Charges will appear on your bank or credit card statement as "IDENTITYSEARCH.AI".</li>
                                <li><b>Cancellation:</b> You may cancel your subscription at any time to avoid future recurring billing through your account dashboard or by contacting support@identitysearch.ai at least 24 hours prior to your next renewal date.</li>
                                <li><b>30-Day Money-Back Guarantee:</b> If you are not satisfied with our service, you are eligible to request a refund within 30 days of your initial purchase by emailing support@identitysearch.ai.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">2. How do I cancel my account?</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>Identity Search AI provides hassle-free online cancellation in many easy ways:</p>
                            <p>For best results, login to your account.</p>
                            <p>When logged in, visit the "Identity Search AI Account" page. Click "Subscription management" select "Cancel My Subscription."</p>
                            <p>or You may also cancel by emailing support@identitysearch.ai, providing your Email Address and indicating your wish to cancel.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">3. How to cancel app subscription?</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-4">
                            <div>
                                <p class="font-black text-gray-900 mb-2"><i class="fa-brands fa-apple text-gray-800"></i> Subscribed through Apple Store</p>
                                <p>If you purchased your Identity Search AI subscription through the iOS app, your subscription is managed by Apple. Apple requires that you cancel your subscription through your Apple ID account. You can follow these steps to cancel your subscription:</p>
                                <ol class="list-decimal ml-6 mt-2 space-y-1">
                                    <li>On your device, open the Settings app.</li>
                                    <li>Tap your name.</li>
                                    <li>Tap Subscriptions.</li>
                                    <li>Tap the active Identity Search AI subscription.</li>
                                    <li>Tap Cancel Subscription. You might need to scroll down to find the Cancel Subscription button. If there is no Cancel button or you see an expiration message in red text, the subscription is already canceled.</li>
                                </ol>
                                <p class="mt-2">For more information and screenshots of these steps, please see <a href="https://support.apple.com/en-us/118223" target="_blank" class="text-[#128c7e] underline font-bold">https://support.apple.com/en-us/118223</a>. If you need assistance with this process, please contact Apple.</p>
                            </div>
                            <div>
                                <p class="font-black text-gray-900 mb-2"><i class="fa-brands fa-google text-blue-500"></i> Steps to cancel (iPhone or iPad):</p>
                                <ol class="list-decimal ml-6 space-y-1">
                                    <li>Open the Settings app.</li>
                                    <li>Tap your name.</li>
                                    <li>Tap Subscriptions.</li>
                                    <li>Tap the subscription that you want to manage.</li>
                                    <li>Tap Cancel Subscription. If you don't see Cancel, the subscription is already cancelled and won't renew.</li>
                                </ol>
                            </div>
                            <div>
                                <p class="font-black text-gray-900 mb-2">Subscribed through Google Play</p>
                                <p>The user can cancel the subscription by opening the Google Play app, tapping Account, then selecting Subscriptions and finally tapping the Cancel button.</p>
                                <p>When a user cancels the subscription, the user will still have access to the product until the current paid period expires.</p>
                                <p>Uninstalling the app will not automatically stop the subscription. The user must follow the described process to properly cancel the plan.</p>
                            </div>
                            <div>
                                <p class="font-black text-gray-900 mb-2">Steps to cancel (phone or tablet)</p>
                                <ol class="list-decimal ml-6 space-y-1">
                                    <li>On your Android device, go to your subscriptions in Google Play.</li>
                                    <li>Select the subscription you want to cancel.</li>
                                    <li>Tap Cancel subscription.</li>
                                    <li>Follow the instructions.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">4. How can I get a refund?</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>If you are unhappy with our service or the data provided, you are covered by our 30-Day Money-Back Guarantee. You may request a full refund within 30 days of your initial plan purchase by emailing us at support@identitysearch.ai.</p>
                            <p>Please note the following general guidelines regarding our refund policy:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li><b>Initial Purchases:</b> Refund requests submitted within 30 days of the first transaction will be processed promptly.</li>
                                <li><b>Recurring Renewals:</b> Subsequent automatic renewal charges (monthly, quarterly, semi-annual, or annual) are non-refundable once billed, unless requested prior to the renewal date.</li>
                                <li><b>Processing Time:</b> Refunds are processed immediately on our end, but depending on your financial institution, it may take 5 to 10 business days for funds to reflect in your account.</li>
                                <li><b>In-App Purchases:</b> For charges made through the Apple App Store or Google Play, refunds must be requested directly through Apple or Google according to their respective policies.</li>
                            </ul>
                            <p>Also please note the following general guidelines regarding our refund process:</p>
                            <p>Refunds are processed immediately on our end, but depending on your bank or financial institution, it may take up to 10 days for the refund to post to your bank. Feel free to contact us if you have any questions or want to confirm your refund.</p>
                            <p>For charges made through the Apple App Store or Google Play, you must request a refund through Apple or Google. Please see: Apple App Store Refunds or Google Play Refunds.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const container = document.getElementById("refundAccordionContainer");
        if (!container) return;
        container.querySelectorAll(".privacy-content-slider").forEach(panel => {
            panel.style.maxHeight = panel.scrollHeight + "px";
        });
        container.addEventListener("click", (e) => {
            const trigger = e.target.closest(".privacy-toggle-trigger");
            if (!trigger) return;
            const panel = trigger.parentElement.querySelector(".privacy-content-slider");
            const icon  = trigger.querySelector("i");
            if (panel.style.maxHeight === "0px" || panel.style.maxHeight === "") {
                panel.style.maxHeight = panel.scrollHeight + "px";
                panel.style.opacity = "1";
                icon.style.transform = "rotate(180deg)";
            } else {
                panel.style.maxHeight = "0px";
                panel.style.opacity = "0";
                icon.style.transform = "rotate(0deg)";
            }
        });
    });
    </script>
</body>
</html>