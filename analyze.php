<?php
/**
 * OSINT Universal Intelligence Console — Target Optimization Component
 * File: analyze.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Context Enforcement
$vid = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : ''; 
if (empty($vid)) {
    die("Error: Target view identifier context missing.");
}

$isLoggedIn = isset($_SESSION['user_id']);
if (!$isLoggedIn) {
    die("Error: Authorization token missing.");
}

// 2. Data Hook Retrieval
$stmt = $pdo->prepare("SELECT * FROM `view` WHERE `vid` = ? LIMIT 1");
$stmt->execute([$vid]);
$target = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$target) {
    die("Error: Target records could not be traced inside the directory grid.");
}

// 3. User Credits Validation
$userStmt = $pdo->prepare("SELECT `credit` FROM `users` WHERE `id` = ? LIMIT 1");
$userStmt->execute([(int)$_SESSION['user_id']]);
$userData = $userStmt->fetch(PDO::FETCH_ASSOC);
$userCredits = $userData ? (int)$userData['credit'] : 0;

if ($userCredits === 0) {
    include 'view_pricing.php';
    exit;
}

/**
 * Downloads an external image from a CDN/URL and saves it locally.
 * @param string $url The external image absolute URL.
 * @return string|null The new local file path relative to root, or null on failure.
 */
function downloadExternalCdnImage($url) {
    if (empty($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
        return null;
    }

    $uploadDir = 'avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) OSINT-Core/1.0');
    
    $imageData = curl_exec($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    if (empty($imageData)) {
        return null;
    }

    $extensionMap = [
        'image/jpeg' => 'jpg',
        'image/jpg'  => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif'
    ];

    $ext = $extensionMap[$contentType] ?? 'jpg';
    
    $localFilename = bin2hex(random_bytes(16)) . '.' . $ext;
    $localPath = $uploadDir . $localFilename;

    if (file_put_contents($localPath, $imageData)) {
        return $localPath;
    }

    return null;
}

// --- OPTIMIZATION WRITEBACK ROUTINE ---
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_optimize'])) {
    $checkReport = $pdo->prepare("SELECT `status` FROM `reports` WHERE `vid` = ? LIMIT 1");
    $checkReport->execute([$vid]);
    if ($checkReport->fetch()) {
        header("Location: report.php?id=" . urlencode($vid));
        exit;
    }

    $updatedName = trim($_POST['target_name'] ?? '');
    $updatedEmail = trim($_POST['input_email'] ?? '');
    $socialUrlsArray = $_POST['social_urls'] ?? [];

    $cleanedSocials = array_filter(array_map('trim', $socialUrlsArray));
    $socialUrlsString = implode(',', $cleanedSocials);

    // If submitted with an invalid format, clean it out instead of throwing an error or saving it
    if (!empty($updatedEmail) && !filter_var($updatedEmail, FILTER_VALIDATE_EMAIL)) {
        $updatedEmail = '';
    }

    $finalAvatarPath = $target['avatar'];

    if (isset($_FILES['new_avatar']) && $_FILES['new_avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'avatars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $ext = strtolower(pathinfo($_FILES['new_avatar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $dest = $uploadDir . bin2hex(random_bytes(16)) . '.' . $ext;
            if (move_uploaded_file($_FILES['new_avatar']['tmp_name'], $dest)) {
                $finalAvatarPath = $dest;
            }
        } else {
            $error = "Invalid image extension.";
        }
    } 
    elseif (!empty($finalAvatarPath) && (strpos($finalAvatarPath, 'http://') === 0 || strpos($finalAvatarPath, 'https://') === 0)) {
        $localSavedPath = downloadExternalCdnImage($finalAvatarPath);
        if ($localSavedPath) {
            $finalAvatarPath = $localSavedPath;
        }
    }

    if (empty($finalAvatarPath)) {
        $error = "A valid profile image is required.";
    }

    if (empty($updatedName)) {
        $error = "Target name variable field cannot be left blank.";
    }

    if (empty($cleanedSocials)) {
        $error = "At least one cross-platform network profile URL is mandatory to execute cross-matching maps.";
    }

    if (empty($error)) {
        $pdo->prepare("UPDATE `view` SET `name` = ?, `input_email` = ?, `social_urls` = ?, `avatar` = ? WHERE `vid` = ?")
            ->execute([$updatedName, $updatedEmail, $socialUrlsString, $finalAvatarPath, $vid]);

        // Capture user operational session trace into your explicit report 'uid' row columns
        $pdo->prepare("INSERT INTO `reports` (`vid`, `uid`, `status`, `raw_profile`, `raw_post`, `raw_following`, `raw_reverse_data`, `ai_analysis`) VALUES (?, ?, 'pending', NULL, NULL, NULL, NULL, NULL) ON DUPLICATE KEY UPDATE `uid` = VALUES(`uid`), `status` = 'pending'")
            ->execute([$vid, (int)$_SESSION['user_id']]);

        $pdo->prepare("UPDATE `users` SET `credit` = GREATEST(0, `credit` - 1) WHERE `id` = ?")->execute([(int)$_SESSION['user_id']]);

        header("Location: report.php?id=" . urlencode($vid));
        exit;
    }
}

// Layout helper calculations
$existingSocials = array_filter(explode(',', $target['social_urls'] ?? ''));
$primaryUrl = trim($target['url'] ?? '');
$needsAutoPopulatedField = empty($existingSocials) && empty($primaryUrl);

$wordCount = str_word_count(trim($target['name'] ?? ''));
$nameParts = explode(' ', trim($target['name'] ?? ''));
$firstNamePlaceholder = strtolower(preg_replace("/[^A-Za-z0-9]/", "", $nameParts[0] ?? 'target'));
if(empty($firstNamePlaceholder)) $firstNamePlaceholder = 'target';
?>

<div class="bg-white border border-gray-200 rounded-2xl p-6 text-left shadow-sm space-y-6">
    
    <div class="space-y-1.5">
        <div class="flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wide px-0.5">
            <span>Dossier Integrity Check</span>
            <span id="integrityScoreLabel" class="text-[#128c7e] font-extrabold text-xs">0%</span>
        </div>
        <div class="grid grid-cols-4 gap-1.5 w-full">
            <div id="barSegment1" class="h-1.5 rounded-full bg-gray-100 transition-colors duration-300"></div>
            <div id="barSegment2" class="h-1.5 rounded-full bg-gray-100 transition-colors duration-300"></div>
            <div id="barSegment3" class="h-1.5 rounded-full bg-gray-100 transition-colors duration-300"></div>
            <div id="barSegment4" class="h-1.5 rounded-full bg-gray-100 transition-colors duration-300"></div>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="p-3.5 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs font-semibold"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="analyze.php?id=<?php echo htmlspecialchars($vid); ?>" method="POST" enctype="multipart/form-data" id="optimizationForm" onsubmit="triggerButtonLoadingState(this)">
        <input type="hidden" name="action_optimize" value="1">
        <div class="space-y-6">

            <div class="space-y-2">
                <div class="flex items-center gap-1.5">
                    <h3 class="text-gray-900 text-sm font-bold tracking-tight">1. Enter Full Name</h3>
                    <span id="nameStatusIcon" class="shrink-0 flex items-center"></span>
                </div>
                <p class="text-xs text-black font-semibold leading-relaxed">Enter real full name to generate accurate result. Do not use nickname.</p>
                <input type="text" id="targetNameInput" name="target_name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-black focus:outline-none focus:ring-2 focus:ring-[#128c7e] focus:border-[#128c7e] focus:bg-white transition-all" value="<?= htmlspecialchars($target['name'] ?? '') ?>" autocomplete="off">
            </div>

            <div class="space-y-2">
                <div class="flex items-center gap-1.5">
                    <h3 class="text-sm font-bold text-gray-900 tracking-tight">2. Best Profile Photo</h3>
                    <span id="avatarStatusIcon" class="shrink-0 flex items-center"></span>
                </div>
                <p class="text-xs text-black font-semibold leading-relaxed">Upload a better photo (if you have one) that clearly shows the face. It will improve report accuracy.</p>
                <div id="avatarAlertBox" class="<?= empty($target['avatar']) ? '' : 'hidden' ?> pt-0.5">
                    <p class="text-[11px] p-2.5 bg-red-50 text-red-800 rounded-xl border border-red-100 font-semibold">A profile picture is required to compute accurate biometric reports.</p>
                </div>
                <div class="flex items-center gap-4 bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <div class="relative w-12 h-12 shrink-0">
                        <img id="avatarPreview" src="<?= (!empty($target['avatar'])) ? ((strpos($target['avatar'], 'avatars/') === 0) ? $target['avatar'] : 'proxy.php?url=' . urlencode(base64_encode($target['avatar']))) : '' ?>" class="<?= empty($target['avatar']) ? 'hidden' : '' ?> w-full h-full rounded-xl object-cover border border-gray-200 bg-white">
                        <div id="avatarPlaceholderIcon" class="<?= !empty($target['avatar']) ? 'hidden' : '' ?> w-full h-full bg-gray-200 rounded-xl flex items-center justify-center text-gray-400"><i class="fa-solid fa-user text-lg"></i></div>
                    </div>
                    <div class="grow">
                        <input type="file" id="avatarInput" name="new_avatar" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-[#128c7e] hover:file:bg-emerald-100 cursor-pointer">
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center gap-1.5">
                    <h3 class="text-sm font-bold text-gray-900 tracking-tight">3. Best Email Address</h3>
                    <span id="emailStatusIcon" class="shrink-0 flex items-center"></span>
                </div>
                <p class="text-xs text-black font-semibold leading-relaxed">Enter an email address that this person frequently uses. It will greatly improve report accuracy.</p>
                <input type="email" id="targetEmailInput" name="input_email" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-black focus:outline-none focus:ring-2 focus:ring-[#128c7e] focus:border-[#128c7e] focus:bg-white transition-all" placeholder="<?= htmlspecialchars($firstNamePlaceholder) ?>@email.com" value="<?= htmlspecialchars($target['input_email'] ?? '') ?>" autocomplete="off">
            </div>

            <div class="space-y-3">
                <div class="flex items-center gap-1.5">
                    <h3 class="text-sm font-bold text-gray-900 tracking-tight">4. Best Social Media URLs</h3>
                    <span id="socialsStatusIcon" class="shrink-0 flex items-center"></span>
                </div>
                <p class="text-xs text-black font-semibold leading-relaxed">Enter more social profile links (Facebook, Instagram, LinkedIn, TikTok, Twitter etc) that the same person belongs to. This will greatly impact report accuracy.</p>
                
                <div id="sBox" class="space-y-2.5">
                    <?php if (!empty($primaryUrl)): ?>
                        <input type="url" name="social_urls[]" readonly class="w-full px-4 py-3 bg-slate-50 text-gray-900 border border-gray-200 rounded-xl text-xs font-mono cursor-not-allowed font-medium" value="<?= htmlspecialchars($primaryUrl) ?>">
                    <?php endif; ?>

                    <?php foreach ($existingSocials as $sUrl): if (trim($sUrl) === $primaryUrl || empty(trim($sUrl))) continue; ?>
                        <div class="flex gap-2 items-center row-s">
                            <input type="url" name="social_urls[]" class="grow px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-mono text-black font-semibold focus:outline-none focus:ring-2 focus:ring-[#128c7e] focus:border-[#128c7e] focus:bg-white" value="<?= htmlspecialchars($sUrl) ?>">
                            <button type="button" onclick="this.parentNode.remove(); handleGlobalMatrixRecalculation();" class="p-2.5 text-red-500 hover:bg-red-50 rounded-xl transition-all cursor-pointer"><i class="fa-solid fa-trash-can text-sm"></i></button>
                        </div>
                    <?php endforeach; ?>

                    <?php if ($needsAutoPopulatedField): ?>
                        <div class="flex gap-2 items-center row-s">
                            <input type="url" name="social_urls[]" placeholder="https://socialmedia.com/handle" class="grow px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-mono text-black font-semibold focus:outline-none focus:ring-2 focus:ring-[#128c7e] focus:border-[#128c7e] focus:bg-white">
                            <button type="button" onclick="this.parentNode.remove(); handleGlobalMatrixRecalculation();" class="p-2.5 text-red-500 hover:bg-red-50 rounded-xl transition-all cursor-pointer"><i class="fa-solid fa-trash-can text-sm"></i></button>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="button" id="addS" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#128c7e] hover:text-[#0e6f64] transition-all cursor-pointer"><i class="fa-solid fa-square-plus text-sm"></i> Add more social profile url</button>
            </div>

            <button type="submit" id="submitReportGenerationBtn" class="w-full bg-[#128c7e] hover:bg-[#0e6f64] disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-[#128c7e] text-white font-bold py-4 rounded-xl text-sm transition-all shadow-md tracking-wide flex items-center justify-center gap-2 cursor-pointer">
                <span id="btnLabelText">Generate Report</span>
            </button>
        </div>
    </form>
</div>

<script>
    function triggerButtonLoadingState(formElement) {
        const targetBtn = document.getElementById('submitReportGenerationBtn');
        const labelText = document.getElementById('btnLabelText');
        
        if (targetBtn && labelText) {
            targetBtn.disabled = true;
            targetBtn.classList.add('opacity-85', 'cursor-not-allowed');
            labelText.textContent = "Processing...";
        }
    }

    function handleGlobalMatrixRecalculation() {
        const nameInput = document.getElementById('targetNameInput');
        const emailInput = document.getElementById('targetEmailInput');
        const avatarPreview = document.getElementById('avatarPreview');
        const generateBtn = document.getElementById('submitReportGenerationBtn');
        
        // 1. Calculate Name Score State (Requires 2 words for Green Tick check)
        const nameValid = nameInput && nameInput.value.trim().split(/\s+/).filter(w => w.length > 0).length >= 2;
        const nameStatusIcon = document.getElementById('nameStatusIcon');
        if (nameStatusIcon) nameStatusIcon.innerHTML = nameValid ? '<i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>' : '<i class="fa-solid fa-triangle-exclamation text-amber-500 text-sm"></i>';

        // 2. Calculate Avatar Score State
        const hasAvatar = avatarPreview && !avatarPreview.classList.contains('hidden') && avatarPreview.getAttribute('src') !== '';
        const avatarStatusIcon = document.getElementById('avatarStatusIcon');
        const avatarAlertBox = document.getElementById('avatarAlertBox');
        if (avatarStatusIcon) avatarStatusIcon.innerHTML = hasAvatar ? '<i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>' : '<i class="fa-solid fa-triangle-exclamation text-amber-500 text-sm"></i>';
        if (avatarAlertBox) avatarAlertBox.classList.toggle('hidden', hasAvatar);

        // 3. Calculate Email Score State & Dynamic Custom Layout Warnings
        const emailValue = emailInput ? emailInput.value.trim() : '';
        const emailValid = emailValue !== '' && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue);
        const emailStatusIcon = document.getElementById('emailStatusIcon');
        if (emailStatusIcon) {
            emailStatusIcon.innerHTML = emailValid ? '<i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>' : '<i class="fa-solid fa-triangle-exclamation text-amber-500 text-sm"></i>';
        }

        // Apply red layout frame rules if text exists but fails validation pattern bounds
        if (emailInput) {
            if (emailValue !== '' && !emailValid) {
                emailInput.classList.remove('bg-gray-50', 'border-gray-200', 'focus:ring-[#128c7e]', 'focus:border-[#128c7e]');
                emailInput.classList.add('bg-red-50/30', 'border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
            } else {
                emailInput.classList.remove('bg-red-50/30', 'border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                emailInput.classList.add('bg-gray-50', 'border-gray-200', 'focus:ring-[#128c7e]', 'focus:border-[#128c7e]');
            }
        }

        // 4. Calculate Social Handles Score State
        let totalSocialUrlsPopulated = 0;
        document.querySelectorAll('#sBox input[type="url"]').forEach(input => { 
            if (input.value.trim() !== '') totalSocialUrlsPopulated++; 
        });
        
        const greenTickSocialsValid = totalSocialUrlsPopulated >= 2;
        const socialsStatusIcon = document.getElementById('socialsStatusIcon');
        if (socialsStatusIcon) socialsStatusIcon.innerHTML = greenTickSocialsValid ? '<i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>' : '<i class="fa-solid fa-triangle-exclamation text-amber-500 text-sm"></i>';

        // --- INTERLOCK ACTIVATION RULES ---
        // Button unlocks immediately when name data exists and at least 1 social media link is populated
        const hasAnyNameData = nameInput && nameInput.value.trim().length > 0;
        const operationalSocialsValid = totalSocialUrlsPopulated >= 1;

        if (generateBtn && !generateBtn.classList.contains('cursor-not-allowed')) {
            generateBtn.disabled = !(hasAnyNameData && operationalSocialsValid);
        }

        // --- MAP THE RESULTS SELECTION TO PROGRESS BAR COLORS ---
        const states = [nameValid, hasAvatar, emailValid, greenTickSocialsValid];
        let trueCount = states.filter(Boolean).length;
        
        document.getElementById('integrityScoreLabel').textContent = (trueCount * 25) + "%";

        for (let i = 1; i <= 4; i++) {
            const currentSegment = document.getElementById('barSegment' + i);
            if (currentSegment) {
                if (i <= trueCount) {
                    currentSegment.className = "h-1.5 rounded-full bg-emerald-500 transition-colors duration-300";
                } else {
                    currentSegment.className = "h-1.5 rounded-full bg-amber-500 transition-colors duration-300";
                }
            }
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        handleGlobalMatrixRecalculation();

        const nameField = document.getElementById('targetNameInput');
        if (nameField) nameField.addEventListener('input', handleGlobalMatrixRecalculation);

        const emailField = document.getElementById('targetEmailInput');
        if (emailField) emailField.addEventListener('input', handleGlobalMatrixRecalculation);

        const sBoxContainer = document.getElementById('sBox');
        if (sBoxContainer) {
            sBoxContainer.addEventListener('input', (e) => {
                if (e.target && e.target.type === 'url') {
                    handleGlobalMatrixRecalculation();
                }
            });
        }

        const uploadInput = document.getElementById('avatarInput');
        if (uploadInput) {
            uploadInput.addEventListener('change', function() {
                if (this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('avatarPreview');
                        const placeholder = document.getElementById('avatarPlaceholderIcon');
                        if (preview) { preview.src = e.target.result; preview.classList.remove('hidden'); }
                        if (placeholder) placeholder.classList.add('hidden');
                        handleGlobalMatrixRecalculation();
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        const addVectorBtn = document.getElementById("addS");
        if (addVectorBtn) {
            addVectorBtn.addEventListener("click", () => {
                const container = document.createElement("div"); 
                container.className = "flex gap-2 items-center row-s opacity-0 transition-all duration-300 transform -translate-y-1";
                container.innerHTML = `<input type="url" name="social_urls[]" placeholder="https://socialmedia.com/handle" class="grow px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-mono text-black font-semibold focus:outline-none focus:ring-2 focus:ring-[#128c7e] focus:border-[#128c7e] focus:bg-white"><button type="button" onclick="this.parentNode.remove(); handleGlobalMatrixRecalculation();" class="p-2.5 text-red-500 hover:bg-red-50 rounded-xl transition-all cursor-pointer"><i class="fa-solid fa-trash-can text-sm"></i></button>`;
                
                document.getElementById("sBox").appendChild(container);
                setTimeout(() => container.classList.remove("opacity-0", "-translate-y-1"), 30);
                handleGlobalMatrixRecalculation();
            });
        }
    });
</script>
