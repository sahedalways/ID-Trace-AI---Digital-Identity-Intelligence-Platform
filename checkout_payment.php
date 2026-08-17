<?php

/**
 * OSINT Universal Intelligence Console
 * File: checkout_payment.php
 */
?>

<div class="space-y-3">

    <div class="flex items-center gap-2">

        <span class="bg-[#0072bc] text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
            3
        </span>

        <h3 class="text-base font-bold text-gray-900 tracking-tight">
            Select Payment Method
        </h3>

    </div>


    <div class="border border-gray-200 rounded-2xl p-4 bg-white shadow-sm space-y-4">

        <!-- Payment method buttons -->

        <div class="flex flex-col gap-2 w-full">

            <button
                type="button"
                id="selectCardTab"
                class="w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-white bg-[#0072bc] border-2 border-[#0072bc]">

                <i
                    id="cardTabIcon"
                    class="fa-solid fa-credit-card text-white"></i>

                Credit or debit card

            </button>


            <button
                type="button"
                id="selectWalletTab"
                class="w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-slate-600 bg-emerald-50/40 border border-[#0072bc]"
                disabled
                style="display:none;">

                <i
                    id="walletTabIcon"
                    class="fa-solid fa-wallet text-[#0072bc]"></i>

                <span id="walletTabLabel">
                    Digital Wallet
                </span>

            </button>

        </div>


        <!-- Card fields -->

        <div
            id="cardElementsFieldsBlock"
            class="space-y-4 pt-1 relative"
            style="min-height:160px;">

            <div
                id="stripeLoadingIndicator"
                class="absolute inset-0 flex items-center justify-center bg-white/80 rounded-xl z-10">

                <div class="flex items-center gap-2 text-sm text-gray-400 font-medium">

                    <i class="fa-solid fa-spinner animate-spin"></i>

                    Loading card form...

                </div>

            </div>


            <!-- Cardholder -->

            <div class="space-y-1">

                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">
                    Cardholder Name
                </label>

                <input
                    type="text"
                    id="step3_cardholder_name"
                    class="w-full border border-gray-200 rounded-xl bg-white px-4 py-3.5 text-sm font-semibold text-gray-900"
                    placeholder="Name on card"
                    value="<?php echo htmlspecialchars($saved_name ?? ''); ?>"
                    autocomplete="cc-name">

            </div>


            <!-- Card number -->

            <div class="space-y-1">

                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">
                    Card Number
                </label>

                <div
                    id="stripeCardNumberWrapper"
                    class="w-full border border-gray-200 rounded-xl bg-white">

                    <div
                        id="stripeCardNumberTarget"
                        class="w-full p-3.5"></div>

                </div>

            </div>


            <!-- Expiry / CVC -->

            <div class="grid grid-cols-2 gap-3">

                <div class="space-y-1">

                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">
                        Expiry Date
                    </label>

                    <div
                        id="stripeCardExpiryTarget"
                        class="stripe-container-input"></div>

                </div>


                <div class="space-y-1">

                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">
                        CVV / CVC
                    </label>

                    <div
                        id="stripeCardCvcTarget"
                        class="stripe-container-input"></div>

                </div>

            </div>

        </div>


        <!-- Express wallet -->

        <div
            id="stripePaymentRequestExpressTarget"
            class="hidden pt-1 w-full rounded-xl overflow-hidden"></div>


        <!-- Error -->

        <div
            id="card-errors"
            role="alert"
            class="text-xs font-semibold text-red-500 pt-1 px-1"></div>

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

        /*
        |--------------------------------------------------------------------------
        | DOM
        |--------------------------------------------------------------------------
        */

        const form =
            document.getElementById('paymentExecutionForm');

        const submitBtn =
            document.getElementById('submitPaymentBtn');

        const btnText =
            document.getElementById('btnText');

        const errorConsole =
            document.getElementById('card-errors');

        const loadingIndicator =
            document.getElementById('stripeLoadingIndicator');

        const cardTab =
            document.getElementById('selectCardTab');

        const cardTabIcon =
            document.getElementById('cardTabIcon');

        const walletTab =
            document.getElementById('selectWalletTab');

        const walletTabIcon =
            document.getElementById('walletTabIcon');

        const walletTabLabel =
            document.getElementById('walletTabLabel');

        const cardFieldsBlock =
            document.getElementById('cardElementsFieldsBlock');

        const expressButtonBlock =
            document.getElementById(
                'stripePaymentRequestExpressTarget'
            );

        const cardNumberWrapper =
            document.getElementById(
                'stripeCardNumberWrapper'
            );

        const step3Name =
            document.getElementById(
                'step3_cardholder_name'
            );


        /*
        |--------------------------------------------------------------------------
        | Stripe values
        |--------------------------------------------------------------------------
        |
        | json_encode prevents broken JS / XSS if a value contains quotes.
        |
        */

        const clientSecret =
            <?php echo json_encode(
                trim($client_secret ?? ''),
                JSON_HEX_TAG |
                    JSON_HEX_APOS |
                    JSON_HEX_AMP |
                    JSON_HEX_QUOT
            ); ?>;

        const publishableKey =
            <?php echo json_encode(
                trim($pub_key ?? ''),
                JSON_HEX_TAG |
                    JSON_HEX_APOS |
                    JSON_HEX_AMP |
                    JSON_HEX_QUOT
            ); ?>;

        const checkoutEmail =
            <?php echo json_encode(
                trim($checkout_email ?? ''),
                JSON_HEX_TAG |
                    JSON_HEX_APOS |
                    JSON_HEX_AMP |
                    JSON_HEX_QUOT
            ); ?>;

        const successBase =
            <?php echo json_encode(
                BASE_URL . 'success',
                JSON_HEX_TAG |
                    JSON_HEX_APOS |
                    JSON_HEX_AMP |
                    JSON_HEX_QUOT
            ); ?>;

        const planName =
            <?php echo json_encode(
                $plan_name ?? '',
                JSON_HEX_TAG |
                    JSON_HEX_APOS |
                    JSON_HEX_AMP |
                    JSON_HEX_QUOT
            ); ?>;

        const vendorId =
            <?php echo json_encode(
                $vid ?? '',
                JSON_HEX_TAG |
                    JSON_HEX_APOS |
                    JSON_HEX_AMP |
                    JSON_HEX_QUOT
            ); ?>;

        const walletAmount =
            <?php echo (int) round(
                ((float) ($plan['price'] ?? 0)) * 100
            ); ?>;


        /*
        |--------------------------------------------------------------------------
        | State
        |--------------------------------------------------------------------------
        */

        let currentPaymentMethodMode = 'card';

        let stripe = null;
        let elements = null;

        let cardNumberElement = null;
        let cardExpiryElement = null;
        let cardCvcElement = null;

        let paymentRequest = null;
        let paymentRequestButton = null;


        /*
        |--------------------------------------------------------------------------
        | UI
        |--------------------------------------------------------------------------
        */

        function hideLoading() {

            if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
            }

        }


        function showError(message) {

            hideLoading();

            if (errorConsole) {
                errorConsole.textContent = message;
            }

        }


        function clearError() {

            if (errorConsole) {
                errorConsole.textContent = '';
            }

        }


        function setButtonLoading(loading) {

            if (!submitBtn || !btnText) {
                return;
            }

            submitBtn.disabled = loading;

            if (loading) {

                submitBtn.classList.add(
                    'opacity-70',
                    'cursor-not-allowed'
                );

                btnText.innerHTML =
                    '<i class="fa-solid fa-spinner animate-spin mr-1"></i>' +
                    ' Authorizing transaction...';

            } else {

                submitBtn.classList.remove(
                    'opacity-70',
                    'cursor-not-allowed'
                );

                btnText.textContent =
                    'Complete Checkout';

            }

        }


        function activateCardTab() {

            currentPaymentMethodMode = 'card';

            cardTab.className =
                'w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-white bg-[#0072bc] border-2 border-[#0072bc]';

            cardTabIcon.className =
                'fa-solid fa-credit-card text-white';

            walletTab.className =
                'w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-slate-600 bg-emerald-50/40 border border-[#0072bc]';

            if (walletTabLabel.textContent === 'Apple Pay') {

                walletTabIcon.className =
                    'fa-brands fa-apple text-base text-[#0072bc]';

            } else {

                walletTabIcon.className =
                    'fa-brands fa-google text-xs text-[#0072bc]';

            }

            cardFieldsBlock.classList.remove('hidden');

            expressButtonBlock.classList.add('hidden');

            submitBtn.classList.remove('hidden');

            clearError();
        }


        function activateWalletTab() {

            if (!validateBillingDetails()) {
                return;
            }

            currentPaymentMethodMode = 'wallet';

            walletTab.className =
                'w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-white bg-[#0072bc] border-2 border-[#0072bc]';

            if (walletTabLabel.textContent === 'Apple Pay') {

                walletTabIcon.className =
                    'fa-brands fa-apple text-base text-white';

            } else {

                walletTabIcon.className =
                    'fa-brands fa-google text-xs text-white';

            }

            cardTab.className =
                'w-full px-4 py-3.5 rounded-xl flex items-center justify-center gap-2.5 font-bold text-sm text-slate-600 bg-emerald-50/40 border border-[#0072bc]';

            cardTabIcon.className =
                'fa-solid fa-credit-card text-[#0072bc]';

            cardFieldsBlock.classList.add('hidden');

            expressButtonBlock.classList.remove('hidden');

            submitBtn.classList.add('hidden');

            clearError();
        }


        cardTab.addEventListener(
            'click',
            activateCardTab
        );

        walletTab.addEventListener(
            'click',
            activateWalletTab
        );


        /*
        |--------------------------------------------------------------------------
        | Billing validation
        |--------------------------------------------------------------------------
        */

        function validateBillingDetails() {

            clearError();

            const name =
                step3Name ?
                step3Name.value.trim() :
                '';

            const country =
                document.getElementById(
                    'billing_country'
                );

            const street =
                document.getElementById(
                    'billing_street'
                );

            const zip =
                document.getElementById(
                    'billing_zip'
                );


            if (!name) {

                showError(
                    'Please enter the cardholder name.'
                );

                if (step3Name) {
                    step3Name.focus();
                }

                return false;
            }


            if (!country || !country.value) {

                showError(
                    'Please select your billing country.'
                );

                country?.focus();

                return false;
            }


            if (!street || !street.value.trim()) {

                showError(
                    'Please enter your billing street address.'
                );

                street?.focus();

                return false;
            }


            if (!zip || !zip.value.trim()) {

                showError(
                    'Please enter your billing ZIP / postal code.'
                );

                zip?.focus();

                return false;
            }


            return true;
        }


        function validateTerms() {

            const checkbox =
                document.getElementById('accept_terms');

            if (!checkbox) {
                return true;
            }

            if (!checkbox.checked) {

                showError(
                    'Please accept the Terms and Conditions and Privacy Policy to continue.'
                );

                checkbox.focus();

                return false;
            }

            return true;
        }


        /*
        |--------------------------------------------------------------------------
        | Stripe errors
        |--------------------------------------------------------------------------
        */

        function formatStripeError(error) {

            if (!error) {
                return 'Payment failed. Please try again.';
            }

            if (
                error.type === 'card_error' &&
                error.code === 'card_declined'
            ) {

                if (
                    error.decline_code === 'fraudulent' ||
                    error.decline_code === 'generic_decline' ||
                    error.decline_code === 'do_not_honor' ||
                    error.decline_code === 'not_permitted'
                ) {

                    return 'This transaction was declined by the payment security system or your card issuer. Please try another card or contact your bank.';
                }

                return 'Your card was declined by the card issuer. Please try another card or contact your bank.';
            }


            if (
                error.code === 'authentication_required'
            ) {

                return 'Your bank requires additional authentication. Please complete the verification.';
            }


            return (
                error.message ||
                'Payment failed. Please try again.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Success redirect
        |--------------------------------------------------------------------------
        */

        function redirectAfterSuccess(paymentIntentId) {

            const country =
                document.getElementById(
                    'billing_country'
                ).value;

            const street =
                document.getElementById(
                    'billing_street'
                ).value.trim();

            const zip =
                document.getElementById(
                    'billing_zip'
                ).value.trim();

            const name =
                step3Name.value.trim();


            const params = new URLSearchParams();

            params.set(
                'payment_intent',
                paymentIntentId
            );

            params.set(
                'plan',
                planName
            );

            if (vendorId) {
                params.set(
                    'id',
                    vendorId
                );
            }

            params.set(
                'c_name',
                name
            );

            params.set(
                'c_country',
                country
            );

            params.set(
                'c_street',
                street
            );

            params.set(
                'c_zip',
                zip
            );


            window.location.href =
                successBase + '?' + params.toString();
        }


        /*
        |--------------------------------------------------------------------------
        | Initialize Stripe
        |--------------------------------------------------------------------------
        */

        if (!publishableKey) {

            showError(
                'Payment system configuration error. Please contact support.'
            );

            return;
        }


        if (!clientSecret) {

            showError(
                'Unable to initialize payment. Please refresh the page and try again.'
            );

            return;
        }


        if (
            typeof Stripe === 'undefined'
        ) {

            showError(
                'Stripe failed to load. Please refresh the page.'
            );

            return;
        }


        try {

            stripe = Stripe(
                publishableKey
            );

        } catch (error) {

            showError(
                'Payment system failed to initialize.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Stripe Elements
        |--------------------------------------------------------------------------
        */

        try {

            elements = stripe.elements({
                clientSecret: clientSecret,
            });


            const elementStyle = {

                base: {

                    color: '#111827',

                    fontWeight: '600',

                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',

                    fontSize: '14px',

                    fontSmoothing: 'antialiased',

                    '::placeholder': {
                        color: '#9ca3af'
                    },

                    ':-webkit-autofill': {
                        color: '#111827'
                    }

                },

                invalid: {

                    color: '#ef4444',

                    iconColor: '#ef4444'

                }

            };


            cardNumberElement =
                elements.create(
                    'cardNumber', {
                        style: elementStyle,
                        showIcon: true
                    }
                );


            cardExpiryElement =
                elements.create(
                    'cardExpiry', {
                        style: elementStyle
                    }
                );


            cardCvcElement =
                elements.create(
                    'cardCvc', {
                        style: elementStyle
                    }
                );


            cardNumberElement.mount(
                '#stripeCardNumberTarget'
            );

            cardExpiryElement.mount(
                '#stripeCardExpiryTarget'
            );

            cardCvcElement.mount(
                '#stripeCardCvcTarget'
            );


            hideLoading();


            /*
            |--------------------------------------------------------------------------
            | Focus
            |--------------------------------------------------------------------------
            */

            cardNumberElement.on(
                'focus',
                function() {

                    cardNumberWrapper.classList.add(
                        'border-[#0072bc]',
                        'ring-1',
                        'ring-[#0072bc]'
                    );

                }
            );


            cardNumberElement.on(
                'blur',
                function() {

                    cardNumberWrapper.classList.remove(
                        'border-[#0072bc]',
                        'ring-1',
                        'ring-[#0072bc]'
                    );

                }
            );


            [
                cardExpiryElement,
                cardCvcElement
            ].forEach(function(element) {

                element.on(
                    'focus',
                    function() {

                        element._target = element._target ||
                            document.querySelector(
                                element === cardExpiryElement ?
                                '#stripeCardExpiryTarget' :
                                '#stripeCardCvcTarget'
                            );

                        element._target.classList.add(
                            'stripe-container-input--focus'
                        );

                    }
                );


                element.on(
                    'blur',
                    function() {

                        if (element._target) {

                            element._target.classList.remove(
                                'stripe-container-input--focus'
                            );

                        }

                    }
                );

            });


            /*
            |--------------------------------------------------------------------------
            | Card validation
            |--------------------------------------------------------------------------
            */

            [
                cardNumberElement,
                cardExpiryElement,
                cardCvcElement
            ].forEach(function(element) {

                element.on(
                    'change',
                    function(event) {

                        if (event.error) {

                            errorConsole.textContent =
                                event.error.message;

                        } else {

                            errorConsole.textContent = '';

                        }

                    }
                );

            });


        } catch (error) {

            console.error(
                'Stripe Elements Error:',
                error
            );

            showError(
                'Card form failed to load. Please refresh the page.'
            );

            submitBtn.disabled = true;

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Apple Pay / Google Pay
        |--------------------------------------------------------------------------
        */

        try {

            paymentRequest =
                stripe.paymentRequest({

                    country: 'US',

                    currency: 'usd',

                    total: {

                        label: 'Identity Search AI Subscription',

                        amount: walletAmount

                    },

                    requestPayerName: true,

                    requestPayerEmail: true

                });


            paymentRequest
                .canMakePayment()
                .then(function(result) {

                    if (!result) {

                        walletTab.style.display =
                            'none';

                        return;
                    }


                    paymentRequestButton =
                        elements.create(
                            'paymentRequestButton', {

                                paymentRequest: paymentRequest,

                                style: {

                                    paymentRequestButton: {

                                        theme: 'dark',

                                        height: '52px',

                                        type: 'subscribe'

                                    }

                                }

                            }
                        );


                    paymentRequestButton.mount(
                        '#stripePaymentRequestExpressTarget'
                    );


                    walletTab.disabled = false;

                    walletTab.style.display =
                        'flex';


                    if (result.applePay) {

                        walletTabLabel.textContent =
                            'Apple Pay';

                        walletTabIcon.className =
                            'fa-brands fa-apple text-base text-[#0072bc]';

                    } else {

                        walletTabLabel.textContent =
                            'Google Pay';

                        walletTabIcon.className =
                            'fa-brands fa-google text-xs text-[#0072bc]';

                    }

                })
                .catch(function() {

                    walletTab.style.display =
                        'none';

                });


            /*
            |--------------------------------------------------------------------------
            | Wallet payment
            |--------------------------------------------------------------------------
            */

            paymentRequest.on(
                'paymentmethod',
                async function(event) {

                    try {

                        if (!validateBillingDetails()) {

                            event.complete('fail');

                            return;
                        }


                        if (!validateTerms()) {

                            event.complete('fail');

                            return;
                        }


                        const name =
                            step3Name.value.trim();

                        const country =
                            document.getElementById(
                                'billing_country'
                            ).value;

                        const street =
                            document.getElementById(
                                'billing_street'
                            ).value.trim();

                        const zip =
                            document.getElementById(
                                'billing_zip'
                            ).value.trim();


                        /*
                        |--------------------------------------------------------------------------
                        | Confirm subscription PaymentIntent
                        |--------------------------------------------------------------------------
                        */

                        const result =
                            await stripe.confirmCardPayment(

                                clientSecret,

                                {

                                    payment_method: event.paymentMethod.id,

                                    receipt_email: checkoutEmail,

                                    billing_details: {

                                        name: name,

                                        email: checkoutEmail,

                                        address: {

                                            line1: street,

                                            postal_code: zip,

                                            country: country

                                        }

                                    }

                                },

                                {
                                    handleActions: false
                                }

                            );


                        if (result.error) {

                            event.complete('fail');

                            showError(
                                formatStripeError(
                                    result.error
                                )
                            );

                            return;
                        }


                        event.complete('success');


                        /*
                        |--------------------------------------------------------------------------
                        | 3DS
                        |--------------------------------------------------------------------------
                        */

                        if (
                            result.paymentIntent &&
                            result.paymentIntent.status ===
                            'requires_action'
                        ) {

                            const actionResult =
                                await stripe.confirmCardPayment(
                                    clientSecret
                                );


                            if (actionResult.error) {

                                showError(
                                    formatStripeError(
                                        actionResult.error
                                    )
                                );

                                return;
                            }


                            if (
                                actionResult.paymentIntent &&
                                actionResult.paymentIntent.status ===
                                'succeeded'
                            ) {

                                redirectAfterSuccess(
                                    actionResult.paymentIntent.id
                                );

                            }

                            return;
                        }


                        if (
                            result.paymentIntent &&
                            result.paymentIntent.status ===
                            'succeeded'
                        ) {

                            redirectAfterSuccess(
                                result.paymentIntent.id
                            );

                            return;
                        }


                        showError(
                            'Payment requires additional verification. Please try again.'
                        );

                    } catch (error) {

                        event.complete('fail');

                        console.error(
                            'Wallet payment error:',
                            error
                        );

                        showError(
                            'Unable to complete wallet payment. Please try again.'
                        );

                    }

                }
            );

        } catch (error) {

            walletTab.style.display =
                'none';

        }


        /*
        |--------------------------------------------------------------------------
        | Normal card payment
        |--------------------------------------------------------------------------
        */

        form.addEventListener(
            'submit',
            async function(event) {

                event.preventDefault();


                if (
                    currentPaymentMethodMode !== 'card'
                ) {
                    return;
                }


                clearError();


                if (!validateTerms()) {
                    return;
                }


                if (!validateBillingDetails()) {
                    return;
                }


                if (
                    !cardNumberElement ||
                    !cardExpiryElement ||
                    !cardCvcElement
                ) {

                    showError(
                        'Card form is not ready. Please refresh the page.'
                    );

                    return;
                }


                setButtonLoading(true);


                const name =
                    step3Name.value.trim();

                const country =
                    document.getElementById(
                        'billing_country'
                    ).value;

                const street =
                    document.getElementById(
                        'billing_street'
                    ).value.trim();

                const zip =
                    document.getElementById(
                        'billing_zip'
                    ).value.trim();


                try {

                    /*
                    |--------------------------------------------------------------------------
                    | Confirm the PaymentIntent belonging to subscription invoice
                    |--------------------------------------------------------------------------
                    */

                    const result =
                        await stripe.confirmCardPayment(

                            clientSecret,

                            {

                                payment_method: {

                                    card: cardNumberElement,

                                    billing_details: {

                                        name: name,

                                        email: checkoutEmail,

                                        address: {

                                            line1: street,

                                            postal_code: zip,

                                            country: country

                                        }

                                    }

                                },

                                receipt_email: checkoutEmail

                            }

                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Error
                    |--------------------------------------------------------------------------
                    */

                    if (result.error) {

                        console.error(
                            'Stripe Payment Error:',
                            result.error
                        );

                        showError(
                            formatStripeError(
                                result.error
                            )
                        );

                        setButtonLoading(false);

                        return;
                    }


                    const paymentIntent =
                        result.paymentIntent;


                    /*
                    |--------------------------------------------------------------------------
                    | Success
                    |--------------------------------------------------------------------------
                    */

                    if (
                        paymentIntent &&
                        paymentIntent.status === 'succeeded'
                    ) {

                        btnText.innerHTML =
                            '<i class="fa-solid fa-spinner animate-spin mr-1"></i>' +
                            ' Verifying authorization...';


                        redirectAfterSuccess(
                            paymentIntent.id
                        );

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Additional action
                    |--------------------------------------------------------------------------
                    */

                    if (
                        paymentIntent &&
                        paymentIntent.status ===
                        'requires_action'
                    ) {

                        const actionResult =
                            await stripe.confirmCardPayment(
                                clientSecret
                            );


                        if (actionResult.error) {

                            showError(
                                formatStripeError(
                                    actionResult.error
                                )
                            );

                            setButtonLoading(false);

                            return;
                        }


                        if (
                            actionResult.paymentIntent &&
                            actionResult.paymentIntent.status ===
                            'succeeded'
                        ) {

                            redirectAfterSuccess(
                                actionResult.paymentIntent.id
                            );

                            return;
                        }

                    }


                    showError(
                        'Payment could not be completed. Please try again.'
                    );

                    setButtonLoading(false);


                } catch (error) {

                    console.error(
                        'Checkout exception:',
                        error
                    );

                    showError(
                        'An unexpected payment error occurred. Please try again.'
                    );

                    setButtonLoading(false);

                }

            }
        );

    });
</script>