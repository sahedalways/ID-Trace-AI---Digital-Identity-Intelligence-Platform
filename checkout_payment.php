<?php
/**
 * OSINT Universal Intelligence Console — Unified Payment Module Component
 * File: checkout_payment.php
 */
?>
<div class="space-y-3">
    <div class="flex items-center gap-2">
        <span class="bg-[#128c7e] text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">3</span>
        <h3 class="text-base font-bold text-gray-900 tracking-tight">Select Payment Method</h3>
    </div>

    <div class="border border-gray-200 rounded-2xl p-4 bg-white shadow-sm space-y-4">
        
        <div class="flex flex-col gap-2 w-full">
            <button type="button" id="selectCardTab" class="w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-white bg-[#128c7e] border-2 border-[#128c7e] transition-all duration-200">
                <i id="cardTabIcon" class="fa-solid fa-credit-card text-white"></i> Credit or debit card
            </button>
            <button type="button" id="selectWalletTab" class="w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-slate-600 bg-emerald-50/40 border border-[#128c7e] hover:bg-emerald-100/60 transition-all duration-200" disabled>
                <i id="walletTabIcon" class="fa-solid fa-wallet text-[#128c7e]"></i> <span id="walletTabLabel">Digital Wallet</span>
            </button>
        </div>

        <div id="cardElementsFieldsBlock" class="space-y-4 pt-1">
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
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Security Code</label>
                    <div id="stripeCardCvcTarget" class="stripe-container-input"></div>
                </div>
            </div>
        </div>

        <div id="stripePaymentRequestExpressTarget" class="hidden pt-1 w-full rounded-xl overflow-hidden"></div>

        <div id="card-errors" role="alert" class="text-xs font-semibold text-red-500 pt-1 px-1"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('paymentExecutionForm');
        const submitBtn = document.getElementById('submitPaymentBtn');
        const btnText = document.getElementById('btnText');
        const errorConsole = document.getElementById('card-errors');
        
        const cardTab = document.getElementById('selectCardTab');
        const cardTabIcon = document.getElementById('cardTabIcon');
        
        const walletTab = document.getElementById('selectWalletTab');
        const walletTabIcon = document.getElementById('walletTabIcon');
        const walletTabLabel = document.getElementById('walletTabLabel');
        
        const cardFieldsBlock = document.getElementById('cardElementsFieldsBlock');
        const expressButtonBlock = document.getElementById('stripePaymentRequestExpressTarget');
        const cardNumberWrapper = document.getElementById('stripeCardNumberWrapper');

        let currentPaymentMethodMode = 'card';

        const stripe = Stripe("<?php echo trim($pub_key); ?>");
        
        const baseElementsStylesOptions = {
            style: {
                base: {
                    color: '#111827',
                    fontWeight: '600',
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
                    fontSize: '14px',
                    fontSmoothing: 'antialiased',
                    '::placeholder': { color: '#9ca3af' },
                },
                invalid: { color: '#ef4444', iconColor: '#ef4444' }
            }
        };

        const elements = stripe.elements({ clientSecret: "<?php echo trim($client_secret); ?>" });

        const cardNumberElement = elements.create('cardNumber', {
            style: baseElementsStylesOptions.style,
            showIcon: true
        });
        cardNumberElement.mount('#stripeCardNumberTarget');

        const cardExpiryElement = elements.create('cardExpiry', baseElementsStylesOptions);
        cardExpiryElement.mount('#stripeCardExpiryTarget');

        const cardCvcElement = elements.create('cardCvc', baseElementsStylesOptions);
        cardCvcElement.mount('#stripeCardCvcTarget');

        cardNumberElement.on('focus', () => {
            cardNumberWrapper.classList.add('border-[#128c7e]', 'ring-1', 'ring-[#128c7e]');
        });
        cardNumberElement.on('blur', () => {
            cardNumberWrapper.classList.remove('border-[#128c7e]', 'ring-1', 'ring-[#128c7e]');
        });

        [
            { el: cardNumberElement, id: '#stripeCardNumberTarget' },
            { el: cardExpiryElement, id: '#stripeCardExpiryTarget' },
            { el: cardCvcElement, id: '#stripeCardCvcTarget' }
        ].forEach(item => {
            if (item.id !== '#stripeCardNumberTarget') {
                const wrapper = document.querySelector(item.id);
                item.el.on('focus', () => wrapper.classList.add('stripe-container-input--focus'));
                item.el.on('blur', () => wrapper.classList.remove('stripe-container-input--focus'));
            }
            item.el.on('change', (e) => {
                if (e.error) errorConsole.textContent = e.error.message;
                else errorConsole.textContent = '';
            });
        });

        const requestInstance = stripe.paymentRequest({
            country: 'US',
            currency: 'usd',
            total: { label: 'Subscription Payment Plan', amount: Math.round(<?php echo (float)$plan['price']; ?> * 100) },
            requestPayerName: true,
            requestPayerEmail: true,
        });

        const requestButtonWidget = elements.create('paymentRequestButton', {
            paymentRequest: requestInstance,
            style: { 
                paymentRequestButton: { 
                    theme: 'dark', 
                    height: '52px', 
                    type: 'subscribe' 
                } 
            }
        });

        requestInstance.canMakePayment().then(function(result) {
            if (result) {
                requestButtonWidget.mount('#stripePaymentRequestExpressTarget');
                walletTab.disabled = false;

                if (result.applePay) {
                    walletTabLabel.textContent = "Apple Pay";
                    walletTabIcon.className = "fa-brands fa-apple text-base text-[#128c7e]";
                } else {
                    walletTabLabel.textContent = "Google Pay";
                    walletTabIcon.className = "fa-brands fa-google text-xs text-[#128c7e]";
                }
            } else {
                walletTab.style.display = 'none';
            }
        });

        function validateBillingDetailsFormBlock() {
            const requiredFieldIds = ['cardholder_name', 'billing_country', 'billing_street', 'billing_zip'];
            errorConsole.textContent = '';
            
            for (let fieldId of requiredFieldIds) {
                const inputElement = document.getElementById(fieldId);
                if (!inputElement || !inputElement.value.trim()) {
                    errorConsole.textContent = "Please fill out all billing details in Step 2 before selecting this payment option.";
                    inputElement.focus();
                    inputElement.classList.add('border-red-400');
                    setTimeout(() => inputElement.classList.remove('border-red-400'), 3000);
                    return false;
                }
            }
            return true;
        }

        cardTab.addEventListener('click', () => {
            currentPaymentMethodMode = 'card';
            
            // Activate Card: Solid Green (Heavy Border)
            cardTab.className = "w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-white bg-[#128c7e] border-2 border-[#128c7e] transition-all duration-200";
            cardTabIcon.className = "fa-solid fa-credit-card text-white";
            
            // Deactivate Wallet: Thin Border Accent
            walletTab.className = "w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-slate-600 bg-emerald-50/40 border border-[#128c7e] hover:bg-emerald-100/60 transition-all duration-200";
            if (walletTabLabel.textContent === "Apple Pay") {
                walletTabIcon.className = "fa-brands fa-apple text-base text-[#128c7e]";
            } else {
                walletTabIcon.className = "fa-brands fa-google text-xs text-[#128c7e]";
            }

            cardFieldsBlock.classList.remove('hidden');
            expressButtonBlock.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        });

        walletTab.addEventListener('click', () => {
            if (!validateBillingDetailsFormBlock()) {
                return; 
            }
            
            currentPaymentMethodMode = 'wallet';
            
            // Activate Wallet: Solid Green (Heavy Border)
            walletTab.className = "w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-white bg-[#128c7e] border-2 border-[#128c7e] transition-all duration-200";
            if (walletTabLabel.textContent === "Apple Pay") {
                walletTabIcon.className = "fa-brands fa-apple text-base text-white";
            } else {
                walletTabIcon.className = "fa-brands fa-google text-xs text-white";
            }
            
            // Deactivate Card: Thin Border Accent
            cardTab.className = "w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-slate-600 bg-emerald-50/40 border border-[#128c7e] hover:bg-emerald-100/60 transition-all duration-200";
            cardTabIcon.className = "fa-solid fa-credit-card text-[#128c7e]";

            cardFieldsBlock.classList.add('hidden');
            expressButtonBlock.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        });

        requestInstance.on('paymentmethod', async (ev) => {
            const cardName = ev.payerName || document.getElementById('cardholder_name').value;
            const country = document.getElementById('billing_country').value || ev.paymentMethod.billing_details.address.country || 'US';
            const street = document.getElementById('billing_street').value || ev.paymentMethod.billing_details.address.line1 || '—';
            const zip = document.getElementById('billing_zip').value || ev.paymentMethod.billing_details.address.postal_code || '—';

            const successUrl = window.location.origin + window.location.pathname.replace('checkout.php', 'success.php') + 
                               `?plan=<?php echo urlencode($plan_name); ?>&id=<?php echo urlencode($vid); ?>&c_name=${encodeURIComponent(cardName)}&c_country=${encodeURIComponent(country)}&c_street=${encodeURIComponent(street)}&c_zip=${encodeURIComponent(zip)}`;

            const { error, paymentIntent } = await stripe.confirmCardPayment(
                "<?php echo trim($client_secret); ?>",
                { payment_method: ev.paymentMethod.id },
                { handleActions: false }
            );

            if (error) {
                ev.complete('fail');
                errorConsole.textContent = error.message;
            } else {
                ev.complete('success');
                if (paymentIntent.status === "requires_action") {
                    const { error: actError, paymentIntent: actIntent } = await stripe.confirmCardPayment("<?php echo trim($client_secret); ?>");
                    if (actError) {
                        errorConsole.textContent = actError.message;
                    } else if (actIntent.status === "succeeded") {
                        window.location.href = successUrl + `&payment_intent=${actIntent.id}`;
                    }
                } else if (paymentIntent.status === "succeeded") {
                    window.location.href = successUrl + `&payment_intent=${paymentIntent.id}`;
                }
            }
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (currentPaymentMethodMode !== 'card') return;

            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
            btnText.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1"></i> Authorizing transaction...';

            const cardName = document.getElementById('cardholder_name').value;
            const country = document.getElementById('billing_country').value;
            const street = document.getElementById('billing_street').value;
            const zip = document.getElementById('billing_zip').value;

            const successUrl = window.location.origin + window.location.pathname.replace('checkout.php', 'success.php') + 
                               `?plan=<?php echo urlencode($plan_name); ?>&id=<?php echo urlencode($vid); ?>&c_name=${encodeURIComponent(cardName)}&c_country=${encodeURIComponent(country)}&c_street=${encodeURIComponent(street)}&c_zip=${encodeURIComponent(zip)}`;

            const { error, paymentIntent } = await stripe.confirmCardPayment("<?php echo trim($client_secret); ?>", {
                payment_method: {
                    card: cardNumberElement,
                    billing_details: {
                        name: cardName,
                        email: "<?php echo addslashes($checkout_email); ?>",
                        address: {
                            line1: street,
                            postal_code: zip,
                            country: country
                        }
                    }
                }
            });

            if (error) {
                errorConsole.textContent = error.message;
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                btnText.textContent = "Complete Checkout";
            } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                btnText.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1"></i> Verifying authorization...';
                window.location.href = successUrl + `&payment_intent=${paymentIntent.id}`;
            } else {
                errorConsole.textContent = "An unexpected error occurred. Please refresh and try again.";
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                btnText.textContent = "Complete Checkout";
            }
        });
    });
</script>
