<?php
/**
 * Identity Search AI — User Target Intelligence Archives Node
 * File: my-report.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Enforcement Matrix Check
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "signin");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

try {
    /**
     * Optimized Cross-Schema Query Loop Matrix with Collation Override Safety
     * Forces the join to align onto unicode_ci to prevent collation mix conflicts between general_ci and unicode_ci.
     */
    $query = "SELECT 
                r.`vid`, 
                r.`status` AS `report_status`, 
                r.`created_at` AS `generated_date`,
                v.`name` AS `target_name`, 
                v.`avatar` AS `target_avatar`, 
                v.`source` AS `target_source`
              FROM `reports` r
              INNER JOIN `view` v ON CONVERT(r.`vid` USING utf8mb4) COLLATE utf8mb4_unicode_ci = v.`vid`
              WHERE r.`uid` = ?
              ORDER BY r.`created_at` DESC";
              
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $generated_reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    die("System Schema Connection Error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Reports — Identity Search AI</title>
    <?php include 'head.php'; ?>
    <style>
        body { background-color: #f9fafb !important; color: #111827 !important; }
        .report-grid-layout {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 1rem;
        }
        @media (max-width: 640px) {
            .report-grid-layout {
                grid-template-columns: 1fr;
                align-items: center;
                text-align: center;
                gap: 1.25rem;
            }
            .report-info-wrapper {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .report-meta-text {
                align-items: center;
                justify-content: center;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#0072bc] selection:text-white bg-slate-50 relative">

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

    <main class="flex-grow max-w-4xl w-full mx-auto px-4 py-8 space-y-6">
        
        <!-- Header Text Matrix -->
        <div class="text-left flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 flex items-center gap-2">
                    🕵️‍♂️ My Reports
                </h1>
                <!-- UPDATED: Description paragraph block removed completely from here -->
            </div>
            <a href="<?php echo BASE_URL; ?>" class="bg-[#0072bc] hover:bg-[#005ea3] text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-colors flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-plus text-[10px]"></i> New Report
            </a>
        </div>

        <!-- Structural Content Workspace Card Grid Wrapper -->
        <div class="space-y-3">
            <?php if (empty($generated_reports)): ?>
                <!-- Clean Empty State Dashboard Interface -->
                <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm max-w-lg mx-auto mt-6">
                    <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-2xl flex items-center justify-center mx-auto border border-gray-100 text-2xl mb-4">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">No Intelligence Records Found</h3>
                    <p class="text-xs text-gray-500 font-medium mt-1 mb-6 max-w-xs mx-auto">You have not requested or generated any system intelligence profiles yet.</p>
                    <a href="<?php echo BASE_URL; ?>" class="inline-flex items-center justify-center gap-2 bg-[#0072bc] hover:bg-[#005ea3] text-white py-3.5 px-8 rounded-xl text-xs font-bold transition shadow-sm cursor-pointer">
                        <i class="fa-solid fa-bolt"></i> Initiate First Deep Scan
                    </a>
                </div>
            <?php else: ?>
                <!-- Iterate Matrix Output Variables Array Rows -->
                <?php foreach ($generated_reports as $report): 
                    $cleanVid = htmlspecialchars($report['vid'], ENT_QUOTES, 'UTF-8');
                    $targetName = !empty($report['target_name']) ? htmlspecialchars($report['target_name']) : 'Unknown Target Identity';
                    $sourceRaw = strtolower(trim($report['target_source'] ?? ''));
                    $statusRaw = strtolower(trim($report['report_status'] ?? 'pending'));

                    // Explicit Platform Icon Mapping for Your Six Defined Sources
                    $sourceIcon = 'fa-solid fa-circle-nodes text-gray-400';
                    if (strpos($sourceRaw, 'facebook') !== false) {
                        $sourceIcon = 'fa-brands fa-facebook text-blue-600';
                    } elseif (strpos($sourceRaw, 'instagram') !== false) {
                        $sourceIcon = 'fa-brands fa-instagram text-pink-600';
                    } elseif (strpos($sourceRaw, 'linkedin') !== false) {
                        $sourceIcon = 'fa-brands fa-linkedin text-blue-700';
                    } elseif (strpos($sourceRaw, 'tiktok') !== false) {
                        $sourceIcon = 'fa-brands fa-tiktok text-gray-900';
                    } elseif (strpos($sourceRaw, 'twitter') !== false || strpos($sourceRaw, 'x.') !== false) {
                        $sourceIcon = 'fa-brands fa-x-twitter text-gray-900';
                    } elseif (strpos($sourceRaw, 'truecaller') !== false) {
                        $sourceIcon = 'fa-solid fa-phone-volume text-sky-500';
                    }

                    // Configuration Rules for Conditional Status Tags and Font Awesome sub-indicators
                    $statusStyles = [
                        'completed'  => ['css' => 'bg-emerald-50 text-emerald-700 border-emerald-100', 'icon' => 'fa-solid fa-circle-check'],
                        'processing' => ['css' => 'bg-amber-50 text-amber-700 border-amber-100', 'icon' => 'fa-solid fa-spinner animate-spin'],
                        'pending'    => ['css' => 'bg-blue-50 text-blue-600 border-blue-100', 'icon' => 'fa-solid fa-clock'],
                        'failed'     => ['css' => 'bg-rose-50 text-rose-600 border-rose-100', 'icon' => 'fa-solid fa-circle-exclamation']
                    ];
                    $currentConfig = $statusStyles[$statusRaw] ?? ['css' => 'bg-gray-50 text-gray-600 border-gray-100', 'icon' => 'fa-solid fa-circle'];
                ?>
                    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm hover:border-gray-300 transition-all report-grid-layout text-left">
                        
                        <!-- Profile Identification Visual Column Block -->
                        <div class="report-info-wrapper flex items-center gap-3.5 min-w-0 w-full">
                            
                            <!-- Avatar Block Engine -->
                            <div class="relative w-12 h-12 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 flex items-center justify-center shrink-0">
                                <?php if (!empty($report['target_avatar'])): ?>
                                    <img src="<?php echo htmlspecialchars($report['target_avatar']); ?>" 
                                         alt="<?php echo $targetName; ?>" 
                                         class="w-full h-full object-cover"
                                         onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                <?php endif; ?>
                                <div class="absolute inset-0 w-full h-full bg-emerald-50 text-[#0072bc] font-bold text-base flex items-center justify-center uppercase <?php echo !empty($report['target_avatar']) ? 'hidden' : ''; ?>">
                                    <?php echo strtoupper(substr($targetName, 0, 1)); ?>
                                </div>
                            </div>

                            <!-- Text Metatag Context Data Fields Block -->
                            <div class="report-meta-text flex flex-col min-w-0 space-y-1 w-full sm:text-left">
                                <div class="flex items-center gap-2 justify-center sm:justify-start">
                                    <span class="text-sm font-bold text-gray-900 truncate tracking-tight"><?php echo $targetName; ?></span>
                                    <!-- Clean inline single-icon tracker flag -->
                                    <i class="<?php echo $sourceIcon; ?> text-sm shrink-0" title="<?php echo htmlspecialchars($report['target_source']); ?>"></i>
                                </div>
                                
                                <div class="text-[11px] text-gray-400 font-medium flex items-center justify-center sm:justify-start">
                                    <i class="fa-solid fa-calendar-day mr-1"></i> 
                                    <?php echo date('M d, Y • h:i A', strtotime($report['generated_date'])); ?>
                                </div>

                                <!-- Status line elements relocated perfectly to its own layout tier underneath the date metrics -->
                                <div class="flex items-center justify-center sm:justify-start pt-0.5">
                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-md border <?php echo $currentConfig['css']; ?>">
                                        <i class="<?php echo $currentConfig['icon']; ?> text-[10px]"></i> <?php echo $statusRaw; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Controls (Clean, content-sized w-auto button on mobile instead of block layout) -->
                        <div class="shrink-0 flex justify-center w-full sm:w-auto">
                            <a href="<?php echo BASE_URL; ?>report?id=<?php echo $cleanVid; ?>" class="inline-flex items-center justify-center bg-[#0072bc] hover:bg-[#005ea3] text-white py-2.5 px-5 rounded-xl text-xs font-bold shadow-sm transition-colors cursor-pointer gap-1.5 border border-transparent whitespace-nowrap">
                                <i class="fa-solid fa-passport text-xs"></i> View Report
                            </a>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </main>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

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
