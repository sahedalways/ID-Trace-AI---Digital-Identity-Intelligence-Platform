<?php
/**
 * OSINT Universal Intelligence Console — Target View Interface
 * File: view.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. INPUT VALIDATION: Ensure target view token exists
$vid = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : ''; 
if (empty($vid)) {
    die("Error: Target view identifier context missing.");
}

// 2. GUEST REDIRECTION TERMINAL (Replaces direct DB submission)
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guest_email'])) {
    $guestEmail = trim($_POST['guest_email']);
    
    if (filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
        $searchParams = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        parse_str($searchParams, $parsedParams);
        
        unset($parsedParams['id']); 
        $parsedParams['email'] = $guestEmail;
        $parsedParams['return'] = "/view.php?id=" . urlencode($vid);
        
        $redirectUrl = "signin.php?" . http_build_query($parsedParams);
        header("Location: " . $redirectUrl);
        exit;
    } else {
        $error = "Please enter a valid corporate or personal email layout address.";
    }
}

// 3. TARGET DATA RETRIEVAL
$stmt = $pdo->prepare("SELECT * FROM `view` WHERE `vid` = ? LIMIT 1");
$stmt->execute([$vid]);
$target = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$target) {
    die("Error: Target records could not be traced inside the directory grid.");
}

// Check scanning state flag constraint to completely prevent re-running animations
$hasBeenScannedBefore = isset($target['scanned']) && (int)$target['scanned'] === 1;

// 4. USER CONTEXT & SYSTEM ENTITLEMENT RESOLUTION
$isLoggedIn = isset($_SESSION['user_id']);
$userCredits = 0;

if ($isLoggedIn) {
    $userStmt = $pdo->prepare("SELECT `credit` FROM `users` WHERE `id` = ? LIMIT 1");
    $userStmt->execute([(int)$_SESSION['user_id']]);
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
    if ($userData) {
        $userCredits = (int)$userData['credit'];
    }
}

function escapeHtml($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$cleanName = trim($target['name'] ?? '');
$cleanSource = trim($target['source'] ?? '');
$firstLetter = !empty($cleanName) ? strtoupper(substr($cleanName, 0, 1)) : 'U';

// 5. TESTIMONIAL LOADER MATRIX
include_once 'view_review.php';
if (isset($all_reviews) && is_array($all_reviews)) {
    shuffle($all_reviews);
    $random_reviews = array_slice($all_reviews, 0, 4);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo escapeHtml($cleanName); ?> (<?php echo escapeHtml($cleanSource); ?>) - Identity Search AI</title>
    <?php include 'head.php'; ?>
    <style>
        body { 
            background-color: #ffffff !important; 
            color: #111827 !important;
        }
        /* Configured custom elastic step jump transition speeds */
        .progress-bar-fill {
            transition: width 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white">

    <?php include 'navbar.php'; ?>

    <main class="flex-grow max-w-2xl w-full mx-auto px-4 sm:px-6 pt-8 pb-16">
        <div class="w-full space-y-6">
            
            <div class="flex items-center gap-4 bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <div class="relative w-16 h-16 rounded-full overflow-hidden border border-gray-200 bg-gray-50 flex-shrink-0 flex items-center justify-center">
                    <?php if (!empty($target['avatar'])): ?>
                        <img id="targetProfileAvatar" 
                             src="<?php echo (strpos($target['avatar'], 'avatars/') === 0) ? $target['avatar'] : 'proxy.php?url=' . urlencode(base64_encode($target['avatar'])); ?>" 
                             alt="<?php echo escapeHtml($target['name']); ?>" 
                             class="w-full h-full object-cover relative z-10"
                             onerror="this.style.display='none'; document.getElementById('letterFallbackAvatar').classList.remove('hidden');">
                    <?php endif; ?>
                    
                    <div id="letterFallbackAvatar" class="letter-avatar absolute inset-0 w-full h-full bg-emerald-50 text-[#128c7e] font-bold text-2xl flex items-center justify-center uppercase <?php echo !empty($target['avatar']) ? 'hidden' : ''; ?>">
                        <?php echo $firstLetter; ?>
                    </div>
                </div>
                <div class="min-w-0 text-left">
                    <h1 class="text-xl font-bold text-gray-900 truncate tracking-tight"><?php echo escapeHtml($target['name']); ?></h1>
                    <p class="text-xs text-gray-400 font-semibold mt-1 uppercase tracking-wider">
                        Source: <span class="text-[#128c7e] font-bold"><?php echo escapeHtml($cleanSource); ?></span>
                    </p>
                </div>
            </div>

            <div id="scanningPanel" class="<?php echo ($hasBeenScannedBefore || !empty($error)) ? 'hidden' : ''; ?> bg-white rounded-2xl border border-gray-200 shadow-sm p-6 text-center">
                <div class="flex items-center justify-center gap-2 text-gray-800 font-bold text-base mb-4">
                    <div id="scanStatusIconContainer" class="flex items-center justify-center">
                        <div id="scanSpinner" class="w-4 h-4 border-2 border-emerald-100 border-t-[#128c7e] rounded-full animate-spin"></div>
                    </div>
                    <span id="scanStatusText">0 data source found</span>
                </div>

                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden relative mb-4">
                    <div id="scanProgressBar" class="progress-bar-fill bg-[#128c7e] h-full w-0" style="width: 0%;"></div>
                </div>
                <p id="scanPleaseWait" class="text-xs text-black font-semibold mb-4">Please wait, analyzing data packages...</p>

                <div class="flex items-center justify-center gap-1.5 py-1">
                    <div class="flex -space-x-2 overflow-hidden flex-wrap justify-center py-1">
                        <span class="w-7 h-7 rounded-full bg-pink-600 border-2 border-white text-white flex items-center justify-center text-xs font-bold shadow-sm" title="Instagram"><i class="fa-brands fa-instagram"></i></span>
                        <span class="w-7 h-7 rounded-full bg-blue-600 border-2 border-white text-white flex items-center justify-center text-xs font-bold shadow-sm" title="Facebook"><i class="fa-brands fa-facebook-f text-[10px]"></i></span>
                        <span class="w-7 h-7 rounded-full bg-blue-700 border-2 border-white text-white flex items-center justify-center text-xs font-bold shadow-sm" title="LinkedIn"><i class="fa-brands fa-linkedin-in text-[10px]"></i></span>
                        <span class="w-7 h-7 rounded-full bg-slate-900 border-2 border-white text-white flex items-center justify-center text-xs font-bold shadow-sm" title="Twitter / X"><i class="fa-brands fa-x-twitter text-[10px]"></i></span>
                        <span class="w-7 h-7 rounded-full bg-black border-2 border-white text-white flex items-center justify-center text-xs font-bold shadow-sm" title="TikTok"><i class="fa-brands fa-tiktok text-[10px]"></i></span>
                        <span class="w-7 h-7 rounded-full bg-[#128c7e] border-2 border-white text-white flex items-center justify-center text-xs font-bold shadow-sm" title="TrueCaller"><i class="fa-solid fa-phone text-[9px]"></i></span>
                        <span class="w-7 h-7 rounded-full bg-blue-500 border-2 border-white text-white flex items-center justify-center text-xs font-bold shadow-sm" title="Google Logs"><i class="fa-brands fa-google text-[10px]"></i></span>
                        <span class="w-7 h-7 rounded-full bg-red-600 border-2 border-white text-white flex items-center justify-center text-xs font-bold shadow-sm" title="YouTube Directory"><i class="fa-brands fa-youtube text-[10px]"></i></span>
                        <span class="w-7 h-7 rounded-full bg-gray-700 border-2 border-white text-white flex items-center justify-center text-xs font-bold shadow-sm" title="Wikipedia Node"><i class="fa-solid fa-w text-[9px]"></i></span>
                        <span class="w-7 h-7 rounded-full bg-[#ff4500] border-2 border-white text-white flex items-center justify-center text-xs font-bold shadow-sm" title="Reddit Forums"><i class="fa-brands fa-reddit-alien text-[10px]"></i></span>
                        <span class="w-7 h-7 rounded-full bg-slate-800 border-2 border-white text-white flex items-center justify-center text-xs font-bold shadow-sm" title="GitHub Codebases"><i class="fa-brands fa-github text-[11px]"></i></span>
                    </div>
                </div>
            </div>

            <div id="gatedWorkspaceSection" class="<?php echo ($hasBeenScannedBefore || !empty($error)) ? '' : 'hidden opacity-0'; ?> transition-all duration-500 transform translate-y-2">
                
                <?php if (!$isLoggedIn): ?>
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 text-left">
                        <h3 class="text-base font-bold text-gray-900 mb-1">Access Intelligence Report</h3>
                        <p class="text-xs text-black font-semibold mb-4">Enter your email address to continue to secure credit terminal.</p>
                        
                        <?php if (!empty($error)): ?>
                            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs font-semibold">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <form id="guestEmailInboundForm" action="view.php?id=<?php echo escapeHtml($vid); ?>" method="POST" class="space-y-4">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">
                                    <i class="fa-solid fa-envelope text-base"></i>
                                </span>
                                <input type="email" id="guestEmailInputField" name="guest_email" required 
                                       class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-black focus:outline-none focus:ring-2 focus:ring-[#128c7e] focus:border-[#128c7e] transition-all" 
                                       placeholder="name@example.com"
                                       value="<?php echo isset($_POST['guest_email']) ? escapeHtml($_POST['guest_email']) : ''; ?>">
                            </div>

                            <button type="submit" id="guestEmailSubmitBtn" class="w-full bg-[#128c7e] hover:bg-[#0e6f64] text-white font-bold py-3.5 px-4 rounded-xl text-sm shadow-md transition-all tracking-wide flex items-center justify-center gap-2 cursor-pointer">
                                <span id="btnTextNode">Continue</span>
                            </button>
                        </form>
                    </div>

                <?php elseif ($isLoggedIn && $userCredits === 0): ?>
                    <?php include 'view_pricing.php'; ?>

                <?php else: ?>
                    <?php include 'analyze.php'; ?>
                <?php endif; ?>

            </div>

            <?php if (isset($random_reviews) && is_array($random_reviews)): ?>
                <div class="space-y-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between px-1">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-wider text-left">Verified Platform Feedback</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($random_reviews as $review): ?>
                            <div class="flex flex-col justify-between bg-white border border-gray-200 rounded-2xl p-4 text-left shadow-sm hover:border-gray-300 transition-all duration-300">
                                <div class="relative pb-3">
                                    <span class="absolute -top-3 -left-1 text-3xl font-serif text-gray-200 pointer-events-none select-none">“</span>
                                    <p class="text-xs font-semibold text-black leading-relaxed relative z-10 pl-2">
                                        <?php echo htmlspecialchars($review['content']); ?>
                                    </p>
                                </div>
                                
                                <div class="flex items-end justify-between pt-3 border-t border-gray-100 mt-auto">
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 rounded-full bg-slate-950 text-white flex items-center justify-center text-[9px] font-black uppercase tracking-tight shadow-sm">
                                            <?php echo substr($review['name'], 0, 1); ?>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-black text-gray-900 truncate"><?php echo htmlspecialchars($review['name']); ?></h4>
                                            <p class="text-[10px] font-black text-gray-400 truncate max-w-[110px]"><?php echo htmlspecialchars($review['occupation']); ?></p>
                                        </div>
                                    </div>
                                    
                                    <span class="inline-flex items-center gap-1 bg-gray-50 text-slate-700 border border-gray-100 text-[10px] font-bold px-2 py-0.5 rounded-lg tracking-wide shrink-0">
                                        <i class="fa-solid fa-location-dot text-[#128c7e] text-xs"></i>
                                        <?php echo htmlspecialchars($review['country']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <footer class="w-full border-t border-gray-200/60 bg-white/50 backdrop-blur-sm">
        <div class="max-w-4xl w-full mx-auto px-4 sm:px-6 py-6 text-center sm:flex sm:justify-between sm:items-center text-xs font-semibold text-gray-500">
            <p>&copy; 2026 Identity Trace AI. All rights reserved.</p>
            <div class="flex justify-center gap-5 mt-2 sm:mt-0">
                <a href="#" class="hover:text-gray-900 transition-colors">Terms of Service</a>
                <a href="#" class="hover:text-gray-900 transition-colors">Privacy Policy</a>
            </div>
        </div>
    </footer>

    <script>
        // Form Processing Listener Injection
        const emailForm = document.getElementById('guestEmailInboundForm');
        if (emailForm) {
            emailForm.addEventListener('submit', function() {
                const input = document.getElementById('guestEmailInputField');
                const btn = document.getElementById('guestEmailSubmitBtn');
                const txt = document.getElementById('btnTextNode');
                
                if (input && input.value.trim() !== "" && btn && txt) {
                    btn.style.pointerEvents = 'none';
                    btn.opacity = '0.85';
                    txt.innerHTML = `<i class="fa-solid fa-spinner animate-spin text-xs mr-1"></i> Processing...`;
                }
            });
        }

        function updateSocialsValidation() {
            let filledCount = 0;
            document.querySelectorAll('#sBox input[type="url"]').forEach(input => { if(input.value.trim() !== '') filledCount++; });
            const iconEl = document.getElementById('socialsStatusIcon');
            if(iconEl) iconEl.innerHTML = filledCount <= 1 ? '<i class="fa-solid fa-triangle-exclamation text-amber-500 text-base"></i>' : '<i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>';
        }

        document.addEventListener("DOMContentLoaded", () => {
            const progress = document.getElementById("scanProgressBar");
            const statusText = document.getElementById("scanStatusText");
            const scanningPanel = document.getElementById("scanningPanel");
            const gatedWorkspace = document.getElementById("gatedWorkspaceSection");
            const iconContainer = document.getElementById("scanStatusIconContainer");

            if (scanningPanel && !scanningPanel.classList.contains('hidden')) {
                const finalTargetSources = Math.floor(Math.random() * (100 - 50 + 1)) + 50;

                const checkpoints = [
                    { time: 4000,  pct: 25,  src: Math.floor(finalTargetSources * 0.25) },
                    { time: 9000,  pct: 55,  src: Math.floor(finalTargetSources * 0.55) },
                    { time: 14000, pct: 85,  src: Math.floor(finalTargetSources * 0.85) },
                    { time: 20000, pct: 100, src: finalTargetSources }
                ];

                checkpoints.forEach((step) => {
                    setTimeout(() => {
                        if (progress) progress.style.width = step.pct + "%";
                        if (statusText) statusText.textContent = `${step.src} data source found`;

                        if (step.pct === 100) {
                            // Instant completion checkpoint swap: Loader icon shifts into a green solid checkmark
                            if (iconContainer) {
                                iconContainer.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>';
                            }

                            // Optimized interface deployment loop interval down to 500ms bounds
                            setTimeout(() => {
                                fetch('view_scanned.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: new URLSearchParams({ 'id': '<?php echo urlencode($vid); ?>' })
                                })
                                .then(() => {
                                    if (scanningPanel) scanningPanel.classList.add("hidden");
                                    if (gatedWorkspace) {
                                        gatedWorkspace.classList.remove("hidden");
                                        setTimeout(() => {
                                            gatedWorkspace.classList.remove("opacity-0", "translate-y-2");
                                            if (typeof handleGlobalMatrixRecalculation === "function") {
                                                handleGlobalMatrixRecalculation();
                                            }
                                        }, 50);
                                    }
                                })
                                .catch(() => {
                                    if (scanningPanel) scanningPanel.classList.add("hidden");
                                    if (gatedWorkspace) gatedWorkspace.classList.remove("hidden", "opacity-0", "translate-y-2");
                                });
                            }, 500); 
                        }
                    }, step.time);
                });
            }
        });
    </script>
</body>
</html>
