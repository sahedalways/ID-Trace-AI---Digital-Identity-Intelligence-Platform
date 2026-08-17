<?php
/**
 * OSINT Universal Intelligence Console — Unified Payment Module Component
 * File: checkout_payment.php
 */
?>
<div class="space-y-3">
    <div class="flex items-center gap-2">
        <span class="bg-[#0072bc] text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">3</span>
        <h3 class="text-base font-bold text-gray-900 tracking-tight">Select Payment Method</h3>
    </div>

    <div class="border border-gray-200 rounded-2xl p-4 bg-white shadow-sm space-y-4">
        
        <div class="flex flex-col gap-2 w-full">
            <button type="button" id="selectCardTab" class="w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-white bg-[#0072bc] border-2 border-[#0072bc] transition-all duration-200">
                <i id="cardTabIcon" class="fa-solid fa-credit-card text-white"></i> Credit or debit card
            </button>
            <button type="button" id="selectWalletTab" class="w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-slate-600 bg-emerald-50/40 border border-[#0072bc] hover:bg-emerald-100/60 transition-all duration-200" disabled>
                <i id="walletTabIcon" class="fa-solid fa-wallet text-[#0072bc]"></i> <span id="walletTabLabel">Digital Wallet</span>
            </button>
        </div>

        <div id="cardElementsFieldsBlock" class="space-y-4 pt-1 relative" style="min-height: 160px;">
            <div id="stripeLoadingIndicator" class="absolute inset-0 flex items-center justify-center bg-white/80 rounded-xl z-10">
                <div class="flex items-center gap-2 text-sm text-gray-400 font-medium">
                    <i class="fa-solid fa-spinner animate-spin"></i> Loading card form...
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Cardholder Name</label>
                <input type="text" id="step3_cardholder_name" class="w-full border border-gray-200 rounded-xl bg-white px-4 py-3.5 text-sm font-semibold text-gray-900 focus:outline-none focus:border-[#0072bc] focus:ring-1 focus:ring-[#0072bc] transition-all duration-200" placeholder="Name on card" value="<?php echo htmlspecialchars($saved_name); ?>" autocomplete="off">
            </div>
            <div class="space-y-1">
                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Card Number</label>
                <div class="w-full border border-gray-200 rounded-xl bg-white transition-all duration-200" id="stripeCardNumberWrapper">
                    <div id="stripeCardNumberTarget" class="w-full p-3.5 bg-transparent focus:outline-none"></div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Expiry Date</label>
                    <div id="stripeCardExpiryTarget" class="stripe-container-input"></div>
                </div>
                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">CVV / CVC</label>
                    <div id="stripeCardCvcTarget" class="stripe-container-input"></div>
                </div>
            </div>
        </div>

        <div id="stripePaymentRequestExpressTarget" class="hidden pt-1 w-full rounded-xl overflow-hidden"></div>

        <div id="card-errors" role="alert" class="text-xs font-semibold text-red-500 pt-1 px-1"></div>
    </div>
</div>

<style>
    #stripeCardNumberTarget iframe,
    #stripeCardExpiryTarget iframe,
    #stripeCardCvcTarget iframe {
        pointer-events: auto !important;
        opacity: 1 !important;
        min-height: 20px;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('paymentExecutionForm');
        const submitBtn = document.getElementById('submitPaymentBtn');
        const btnText = document.getElementById('btnText');
        const errorConsole = document.getElementById('card-errors');
        const loadingIndicator = document.getElementById('stripeLoadingIndicator');
        
        const cardTab = document.getElementById('selectCardTab');
        const cardTabIcon = document.getElementById('cardTabIcon');
        
        const walletTab = document.getElementById('selectWalletTab');
        const walletTabIcon = document.getElementById('walletTabIcon');
        const walletTabLabel = document.getElementById('walletTabLabel');
        
        const cardFieldsBlock = document.getElementById('cardElementsFieldsBlock');
        const expressButtonBlock = document.getElementById('stripePaymentRequestExpressTarget');
        const cardNumberWrapper = document.getElementById('stripeCardNumberWrapper');

        const step2Name = document.getElementById('cardholder_name');
        const step3Name = document.getElementById('step3_cardholder_name');

        let currentPaymentMethodMode = 'card';
        let stripeReady = false;

        if (step2Name && step3Name) {
            step2Name.addEventListener('input', function() {
                step3Name.value = this.value;
            });
            step3Name.addEventListener('input', function() {
                step2Name.value = this.value;
            });
            if (step2Name.value) step3Name.value = step2Name.value;
        }

        function activateCardTab() {
            currentPaymentMethodMode = 'card';
            cardTab.className = "w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-white bg-[#0072bc] border-2 border-[#0072bc] transition-all duration-200";
            cardTabIcon.className = "fa-solid fa-credit-card text-white";
            walletTab.className = "w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-slate-600 bg-emerald-50/40 border border-[#0072bc] hover:bg-emerald-100/60 transition-all duration-200";
            if (walletTabLabel.textContent === "Apple Pay") {
                walletTabIcon.className = "fa-brands fa-apple text-base text-[#0072bc]";
            } else {
                walletTabIcon.className = "fa-brands fa-google text-xs text-[#0072bc]";
            }
            cardFieldsBlock.classList.remove('hidden');
            expressButtonBlock.classList.add('hidden');
            submitBtn.classList.remove('hidden');
            errorConsole.textContent = '';
        }

        function activateWalletTab() {
            if (!validateBillingDetailsFormBlock()) return;
            currentPaymentMethodMode = 'wallet';
            walletTab.className = "w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-white bg-[#0072bc] border-2 border-[#0072bc] transition-all duration-200";
            if (walletTabLabel.textContent === "Apple Pay") {
                walletTabIcon.className = "fa-brands fa-apple text-base text-white";
            } else {
                walletTabIcon.className = "fa-brands fa-google text-xs text-white";
            }
            cardTab.className = "w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-slate-600 bg-emerald-50/40 border border-[#0072bc] hover:bg-emerald-100/60 transition-all duration-200";
            cardTabIcon.className = "fa-solid fa-credit-card text-[#0072bc]";
            cardFieldsBlock.classList.add('hidden');
            expressButtonBlock.classList.remove('hidden');
            submitBtn.classList.add('hidden');
            errorConsole.textContent = '';
        }

        cardTab.addEventListener('click', activateCardTab);
        walletTab.addEventListener('click', activateWalletTab);

        function validateBillingDetailsFormBlock() {
            const step3NameVal = step3Name ? step3Name.value.trim() : '';
            const step2NameVal = step2Name ? step2Name.value.trim() : '';
            const nameVal = step3NameVal || step2NameVal;
            
            const requiredFields = [
                { id: 'billing_country', el: document.getElementById('billing_country') },
                { id: 'billing_street', el: document.getElementById('billing_street') },
                { id: 'billing_zip', el: document.getElementById('billing_zip') }
            ];

            if (!nameVal) {
                errorConsole.textContent = "Please enter the cardholder name in Step 2 or Step 3.";
                if (step3Name) {
                    step3Name.focus();
                    step3Name.classList.add('border-red-400');
                    setTimeout(() => step3Name.classList.remove('border-red-400'), 3000);
                }
                return false;
            }

            for (let field of requiredFields) {
                if (!field.el || !field.el.value.trim()) {
                    errorConsole.textContent = "Please fill out all billing details (country, address, ZIP) in Step 2 before selecting this payment option.";
                    if (field.el) {
                        field.el.focus();
                        field.el.classList.add('border-red-400');
                        setTimeout(() => field.el.classList.remove('border-red-400'), 3000);
                    }
                    return false;
                }
            }
            return true;
        }

        function getCardholderName() {
            const step3Val = step3Name ? step3Name.value.trim() : '';
            const step2Val = step2Name ? step2Name.value.trim() : '';
            return step3Val || step2Val;
        }

        function acceptTermsValidation() {
            const termsCheckbox = document.getElementById('accept_terms');
            if (!termsCheckbox) return true;
            if (!termsCheckbox.checked) {
                errorConsole.textContent = "Please accept the Terms and Conditions and Privacy Policy to continue.";
                termsCheckbox.focus();
                return false;
            }
            return true;
        }

        function formatStripeError(error) {
            if (!error) return "An unexpected error occurred. Please refresh and try again.";
            const code = error.code;
            const declineCode = error.decline_code;
            if (code === 'card_declined' && (declineCode === 'generic_decline' || declineCode === 'fraudulent' || declineCode === 'not_permitted' || declineCode === 'do_not_honor')) {
                return "This transaction was declined by our payment security system or your card issuer. Please try another card or contact your bank for assistance.";
            }
            if (code === 'card_declined') {
                return "Your card was declined by the issuer. Please check the card details or use a different payment method.";
            }
            return error.message || "An unexpected error occurred. Please refresh and try again.";
        }

        function hideLoading() {
            if (loadingIndicator) loadingIndicator.style.display = 'none';
        }

        function showError(msg) {
            hideLoading();
            errorConsole.textContent = msg;
        }

        const clientSecret = "<?php echo trim($client_secret); ?>";
        const pubKey = "<?php echo trim($pub_key); ?>";

        if (!pubKey) {
            showError("Payment system configuration error. Please contact support.");
            return;
        }

        if (!clientSecret) {
            showError("Unable to initialize payment. Please refresh the page and try again.");
            return;
        }

        try {
            var stripe = Stripe(pubKey);
        } catch (e) {
            showError("Payment system failed to load. Please refresh the page.");
            return;
        }

        const baseElementsStylesOptions = {
            style: {
                base: {
                    color: '#111827',
                    fontWeight: '600',
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
                    fontSize: '14px',
                    fontSmoothing: 'antialiased',
                    '::placeholder': { color: '#9ca3af' },
                    ':-webkit-autofill': { color: '#111827' },
                },
                invalid: { color: '#ef4444', iconColor: '#ef4444' }
            }
        };

        var elements, cardNumberElement, cardExpiryElement, cardCvcElement;
        var requestInstance, requestButtonWidget;

        try {
            elements = stripe.elements({ clientSecret: clientSecret });

            cardNumberElement = elements.create('cardNumber', {
                style: baseElementsStylesOptions.style,
                showIcon: true
            });
            cardNumberElement.mount('#stripeCardNumberTarget');

            cardExpiryElement = elements.create('cardExpiry', baseElementsStylesOptions);
            cardExpiryElement.mount('#stripeCardExpiryTarget');

            cardCvcElement = elements.create('cardCvc', baseElementsStylesOptions);
            cardCvcElement.mount('#stripeCardCvcTarget');

            hideLoading();
            stripeReady = true;

            cardNumberElement.on('focus', () => {
                cardNumberWrapper.classList.add('border-[#0072bc]', 'ring-1', 'ring-[#0072bc]');
            });
            cardNumberElement.on('blur', () => {
                cardNumberWrapper.classList.remove('border-[#0072bc]', 'ring-1', 'ring-[#0072bc]');
            });

            [
                { el: cardExpiryElement, id: '#stripeCardExpiryTarget' },
                { el: cardCvcElement, id: '#stripeCardCvcTarget' }
            ].forEach(item => {
                const wrapper = document.querySelector(item.id);
                item.el.on('focus', () => wrapper.classList.add('stripe-container-input--focus'));
                item.el.on('blur', () => wrapper.classList.remove('stripe-container-input--focus'));
            });

            [cardNumberElement, cardExpiryElement, cardCvcElement].forEach(el => {
                el.on('change', (e) => {
                    if (e.error) errorConsole.textContent = e.error.message;
                    else if (!e.complete) errorConsole.textContent = '';
                });
            });

            submitBtn.classList.remove('hidden');
        } catch (e) {
            showError("Card form failed to load. Please refresh the page.");
            submitBtn.classList.add('hidden');
            return;
        }

        try {
            requestInstance = stripe.paymentRequest({
                country: 'US',
                currency: 'usd',
                total: { label: 'Subscription Payment Plan', amount: Math.round(<?php echo (float)$plan['price']; ?> * 100) },
                requestPayerName: true,
                requestPayerEmail: true,
            });

            requestInstance.canMakePayment().then(function(result) {
                if (result) {
                    requestButtonWidget = elements.create('paymentRequestButton', {
                        paymentRequest: requestInstance,
                        style: {
                            paymentRequestButton: {
                                theme: 'dark',
                                height: '52px',
                                type: 'subscribe'
                            }
                        }
                    });
                    requestButtonWidget.mount('#stripePaymentRequestExpressTarget');
                    walletTab.disabled = false;
                    walletTab.style.display = '';

                    if (result.applePay) {
                        walletTabLabel.textContent = "Apple Pay";
                        walletTabIcon.className = "fa-brands fa-apple text-base text-[#0072bc]";
                    } else {
                        walletTabLabel.textContent = "Google Pay";
                        walletTabIcon.className = "fa-brands fa-google text-xs text-[#0072bc]";
                    }
                } else {
                    walletTab.disabled = true;
                    walletTab.style.display = 'none';
                }
            }).catch(function() {
                walletTab.disabled = true;
                walletTab.style.display = 'none';
            });

            requestInstance.on('paymentmethod', async (ev) => {
                if (!validateBillingDetailsFormBlock()) {
                    ev.complete('fail');
                    return;
                }
                if (!acceptTermsValidation()) {
                    ev.complete('fail');
                    return;
                }

                const cardName = getCardholderName();
                const country = document.getElementById('billing_country').value;
                const street = document.getElementById('billing_street').value;
                const zip = document.getElementById('billing_zip').value;
                const email = "<?php echo addslashes($checkout_email); ?>";
                const successBase = "<?php echo BASE_URL; ?>success";

                const { error, paymentIntent } = await stripe.confirmCardPayment(
                    clientSecret,
                    {
                        payment_method: ev.paymentMethod.id,
                        receipt_email: email,
                        billing_details: {
                            name: cardName,
                            email: email,
                            address: { line1: street, postal_code: zip, country: country }
                        }
                    },
                    { handleActions: false }
                );

                if (error) {
                    ev.complete('fail');
                    errorConsole.textContent = formatStripeError(error);
                } else {
                    ev.complete('success');
                    if (paymentIntent.status === "requires_action") {
                        const { error: actError, paymentIntent: actIntent } = await stripe.confirmCardPayment(clientSecret);
                        if (actError) {
                            errorConsole.textContent = formatStripeError(actError);
                        } else if (actIntent.status === "succeeded") {
                            window.location.href = successBase + "?payment_intent=" + encodeURIComponent(actIntent.id) + "&plan=<?php echo urlencode($plan_name); ?>&id=<?php echo urlencode($vid); ?>&c_name=" + encodeURIComponent(cardName) + "&c_country=" + encodeURIComponent(country) + "&c_street=" + encodeURIComponent(street) + "&c_zip=" + encodeURIComponent(zip);
                        }
                    } else if (paymentIntent.status === "succeeded") {
                        window.location.href = successBase + "?payment_intent=" + encodeURIComponent(paymentIntent.id) + "&plan=<?php echo urlencode($plan_name); ?>&id=<?php echo urlencode($vid); ?>&c_name=" + encodeURIComponent(cardName) + "&c_country=" + encodeURIComponent(country) + "&c_street=" + encodeURIComponent(street) + "&c_zip=" + encodeURIComponent(zip);
                    }
                }
            });
        } catch (e) {
            walletTab.disabled = true;
            walletTab.style.display = 'none';
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (currentPaymentMethodMode !== 'card') return;

            if (!acceptTermsValidation()) return;
            if (!validateBillingDetailsFormBlock()) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                btnText.textContent = "Complete Checkout";
                return;
            }

            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
            btnText.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1"></i> Authorizing transaction...';

            const cardName = getCardholderName();
            const country = document.getElementById('billing_country').value;
            const street = document.getElementById('billing_street').value;
            const zip = document.getElementById('billing_zip').value;
            const successBase = "<?php echo BASE_URL; ?>success";

            const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: cardNumberElement,
                    billing_details: {
                        name: cardName,
                        email: "<?php echo addslashes($checkout_email); ?>",
                        address: { line1: street, postal_code: zip, country: country }
                    }
                },
                receipt_email: "<?php echo addslashes($checkout_email); ?>"
            });

            if (error) {
                errorConsole.textContent = formatStripeError(error);
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                btnText.textContent = "Complete Checkout";
            } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                btnText.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1"></i> Verifying authorization...';
                window.location.href = successBase + "?payment_intent=" + encodeURIComponent(paymentIntent.id) + "&plan=<?php echo urlencode($plan_name); ?>&id=<?php echo urlencode($vid); ?>&c_name=" + encodeURIComponent(cardName) + "&c_country=" + encodeURIComponent(country) + "&c_street=" + encodeURIComponent(street) + "&c_zip=" + encodeURIComponent(zip);
            } else {
                errorConsole.textContent = "An unexpected error occurred. Please refresh and try again.";
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                btnText.textContent = "Complete Checkout";
            }
        });
    });
</script>
