/**
 * Identity Search AI — Client Authentication Flow Engine
 * File: auth_ui_flow.js
 */
document.addEventListener('DOMContentLoaded', function () {
    const feedback = document.getElementById('runtimeFeedbackMsg');
    const identityBlock = document.getElementById('stageIdentityBlock');
    const verifyBlock = document.getElementById('stageVerificationBlock');
    const nameBlock = document.getElementById('stageNameCollectionBlock');

    const emailForm = document.getElementById('emailSubmissionForm');
    const otpForm = document.getElementById('otpVerificationForm');
    const nameForm = document.getElementById('nameCollectionForm');

    const emailField = document.getElementById('emailField');
    const nameField = document.getElementById('nameField');

    const emailSubmit = document.getElementById('emailSubmitBtn');
    const otpSubmit = document.getElementById('otpSubmitBtn');
    const nameSubmit = document.getElementById('nameSubmitBtn');

    const emailMirror = document.getElementById('targetEmailMirror');
    const resendBtn = document.getElementById('resendOtpBtn');
    const otpBoxes = document.querySelectorAll('#otpInputsCluster .otp-box');

    /**
     * URL PARAMETER NORMALIZATION ENGINE
     * Safely appends query components without creating duplicate '?' operators.
     * Converts '?return=/path' context parameters into a clean '&return=/path' connection.
     */
    function getCleanAuthUrl(action) {
        const searchParams = window.location.search;
        if (!searchParams) {
            return `signin.php?action=${action}`;
        }
        // Trim the leading '?' from window.location.search and bind it securely using '&'
        return `signin.php?action=${action}&` + searchParams.substring(1);
    }

    function showFeedback(text, isError = true) {
        feedback.textContent = text;
        feedback.className = isError
            ? 'block mb-5 p-3.5 rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm font-semibold text-center fade-in-up'
            : 'block mb-5 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm font-semibold text-center fade-in-up';
    }

    function clearFeedback() {
        feedback.className = 'hidden';
        feedback.textContent = '';
    }

    // Task 1: Submitting Email & Triggering OTP Generation
    emailForm.addEventListener('submit', function (e) {
        e.preventDefault();
        clearFeedback();

        const emailVal = emailField.value.trim();
        if (!emailVal) return;

        emailSubmit.disabled = true;
        emailSubmit.innerHTML = `<div class="w-5 h-5 rounded-full border-2 border-white/20 border-t-white animate-spin"></div> <span>Sending code...</span>`;

        // Utilizes normalized link structure string to safely pass verification parameters
        fetch(getCleanAuthUrl('send_otp'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: emailVal }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.status === 'success') {
                    emailMirror.textContent = emailVal;
                    identityBlock.classList.add('hidden');
                    verifyBlock.classList.remove('hidden');
                    setTimeout(() => otpBoxes[0].focus(), 150);
                } else {
                    showFeedback(data.message, true);
                    emailSubmit.disabled = false;
                    emailSubmit.innerHTML = 'Continue with email';
                }
            })
            .catch(() => {
                showFeedback(
                    'An infrastructure execution breakdown occurred. Please try again later.',
                    true
                );
                emailSubmit.disabled = false;
                emailSubmit.innerHTML = 'Continue with email';
            });
    });

    // Task 2: Handling Auto-focus navigation jumps on interaction
    otpBoxes.forEach((box, index) => {
        box.addEventListener('input', (e) => {
            const val = e.target.value;
            if (val.length > 1) {
                e.target.value = val.charAt(0);
            }
            if (e.target.value !== '' && index < otpBoxes.length - 1) {
                otpBoxes[index + 1].removeAttribute('disabled');
                otpBoxes[index + 1].focus();
            }
        });

        box.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace') {
                if (e.target.value === '' && index > 0) {
                    otpBoxes[index - 1].focus();
                    otpBoxes[index].setAttribute('disabled', true);
                    otpBoxes[index - 1].value = '';
                } else {
                    e.target.value = '';
                }
            }
        });
    });

    // Auto-fill full code sequence if copied directly onto clipboard memory
    otpBoxes[0].addEventListener('paste', (e) => {
        e.preventDefault();
        const cleanData = (e.clipboardData || window.clipboardData).getData('text').trim();
        if (/^\d{6}$/.test(cleanData)) {
            otpBoxes.forEach((box, idx) => {
                box.removeAttribute('disabled');
                box.value = cleanData.charAt(idx);
            });
            otpBoxes[otpBoxes.length - 1].focus();
        }
    });

    // Task 3: Verifying Input Code Match Checking Routine
    otpForm.addEventListener('submit', function (e) {
        e.preventDefault();
        clearFeedback();

        let combinedToken = '';
        otpBoxes.forEach((box) => (combinedToken += box.value));

        if (combinedToken.length !== 6) {
            showFeedback('Please supply the complete 6-digit verification code string.', true);
            return;
        }

        otpSubmit.disabled = true;
        otpSubmit.innerHTML = `<div class="w-5 h-5 rounded-full border-2 border-white/20 border-t-white animate-spin"></div>`;

        // Utilizes normalized path string target parameters to execute transaction operations cleanly
        fetch(getCleanAuthUrl('verify_otp'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ otp: combinedToken }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.status === 'success') {
                    showFeedback('Authorization confirmed. Transferring session metrics...', false);
                    window.location.href = data.redirect; // Executes complete browser path state reset transition
                } else if (data.status === 'require_name') {
                    // NEW USER ROUTE: Transition interface views down to name acquisition elements smoothly
                    verifyBlock.classList.add('hidden');
                    nameBlock.classList.remove('hidden');
                    setTimeout(() => nameField.focus(), 150);
                } else {
                    showFeedback(data.message, true);
                    otpSubmit.disabled = false;
                    otpSubmit.innerHTML = 'Verify code';
                }
            })
            .catch(() => {
                showFeedback('An unexpected response transaction crash occurred.', true);
                otpSubmit.disabled = false;
                otpSubmit.innerHTML = 'Verify code';
            });
    });

    // Task 4: Managing Token Refresh Dispatches
    resendBtn.addEventListener('click', function () {
        clearFeedback();
        resendBtn.disabled = true;
        resendBtn.textContent = 'Dispatched...';

        // Utilizes clean query router parameter normalization patterns for resend updates
        fetch(getCleanAuthUrl('send_otp'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: emailField.value.trim() }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.status === 'success') {
                    showFeedback('A fresh authentication token hash has been dispatched.', false);
                } else {
                    showFeedback(data.message, true);
                }
                resendBtn.disabled = false;
                resendBtn.textContent = 'Resend code';
            })
            .catch(() => {
                showFeedback(
                    'Failed to request an alternate validation token resource reset.',
                    true
                );
                resendBtn.disabled = false;
                resendBtn.textContent = 'Resend code';
            });
    });

    // Task 5: NEW INTERCEPT — Submitting Name Parameters to Finalize Account
    nameForm.addEventListener('submit', function (e) {
        e.preventDefault();
        clearFeedback();

        const nameVal = nameField.value.trim();
        if (nameVal.length < 2) {
            showFeedback('Please enter a valid full name to continue.', true);
            return;
        }

        nameSubmit.disabled = true;
        nameSubmit.innerHTML = `<div class="w-5 h-5 rounded-full border-2 border-white/20 border-t-white animate-spin"></div> <span>Finalizing registration...</span>`;

        fetch(getCleanAuthUrl('complete_signup'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name: nameVal }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.status === 'success') {
                    showFeedback('Account set up complete! Launching ecosystem access...', false);
                    window.location.href = data.redirect;
                } else {
                    showFeedback(data.message, true);
                    nameSubmit.disabled = false;
                    nameSubmit.innerHTML = 'Complete Registration';
                }
            })
            .catch(() => {
                showFeedback('An unexpected interface engine exception happened.', true);
                nameSubmit.disabled = false;
                nameSubmit.innerHTML = 'Complete Registration';
            });
    });
});
