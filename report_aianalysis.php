<?php
/**
 * OSINT Universal Intelligence Console — Cognitive Portrayal Layer Submodule
 * File: report_aianalysis.php
 * Style Profile: Premium Whitebridge Multi-Sector Corporate Intel Layout
 */

if (!defined('BASE_URL') || !isset($report) || !isset($target)) {
    die("Error: Direct access restriction layer violated.");
}

$ai = [];
if (!empty($report['ai_analysis'])) {
    $ai = json_decode($report['ai_analysis'], true);
}

/**
 * Dynamically resolves internal localized uploads vs external proxy links
 * Optimized for trailing-slash BASE_URL configurations
 */
function resolveReportImage($path) {
    if (empty($path)) return '';
    if (strpos($path, 'http') === 0) {
        return 'proxy.php?url=' . urlencode(base64_encode($path));
    }
    return BASE_URL . ltrim($path, '/');
}

/**
 * Helper function to safely parse and display semicolon-separated fields neatly
 */
function renderSemicolonField($rawData) {
    if (empty($rawData)) return '';
    $items = array_filter(array_map('trim', explode(';', $rawData)));
    if (count($items) <= 1) {
        return '<span class="font-normal text-black text-right break-words max-w-[200px] sm:max-w-md select-all text-sm">' . escapeHtml($rawData) . '</span>';
    }
    
    $html = '<div class="flex flex-wrap justify-end gap-x-2 gap-y-1 pt-0.5 max-w-[200px] sm:max-w-md">';
    foreach ($items as $item) {
        $html .= '<span class="font-normal text-black bg-slate-50 border border-slate-200 px-2.5 py-0.5 rounded-md text-sm font-mono select-all tracking-tight print:py-0 print:border-none print:bg-transparent">' . escapeHtml($item) . '</span>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Custom renderer for cleanly formatting locations and websites with inline flex wrapping and leading icon strings
 * FIXED: Removed w-full from the inner element row loop to prevent wide icon gaps on multi-line text wrapping on mobile viewports.
 */
function renderIconicSemicolonField($rawData, $iconClass) {
    if (empty($rawData)) return '';
    $items = array_filter(array_map('trim', explode(';', $rawData)));
    
    $html = '<div class="flex flex-col items-end gap-1.5 text-right max-w-[200px] sm:max-w-md">';
    foreach ($items as $item) {
        $html .= '<span class="font-normal text-black text-sm flex items-start justify-end gap-1.5 select-all break-words">';
        $html .= '<i class="' . $iconClass . ' text-black text-[11px] mt-1 shrink-0"></i>';
        $html .= '<span class="block text-right break-words">' . escapeHtml($item) . '</span>';
        $html .= '</span>';
    }
    $html .= '</div>';
    return $html;
}
?>

<!-- =========================================================================
     PRINT WORKFLOW COMPATIBILITY OVERLAYS (STRICT GAP REDUCTION)
     ========================================================================= -->
<style>
    .base-text { font-size: 0.875rem !important; line-height: 1.625 !important; color: rgba(0, 0, 0, 0.85) !important; font-weight: 400 !important; }

    @media print {
        .page-break-prevent { 
            page-break-inside: avoid !important; 
            break-inside: avoid-page !important; 
        }
        .space-y-12 > * + * { margin-top: 1rem !important; }
        .pt-4 { padding-top: 0.25rem !important; }
        .py-7 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
        .p-5 { padding: 0.65rem !important; }
        .p-4 { padding: 0.5rem !important; }
        .space-y-3.5 > * + * { margin-top: 0.4rem !important; }
        .space-y-4 > * + * { margin-top: 0.4rem !important; }
        .gap-4 { gap: 0.4rem !important; }
        .gap-6 { gap: 0.5rem !important; }
        .my-2 { margin-top: 0.25rem !important; margin-bottom: 0.25rem !important; }
        
        body, html { background: #ffffff !important; color: #000000 !important; }
        .bg-slate-50\/50, .bg-slate-50, .bg-slate-50\/60 { background-color: #f8fafc !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .bg-white { background-color: #ffffff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .border { border: 1px solid #e2e8f0 !important; }
        .shadow-2sm, .shadow-3sm, .shadow-sm { filter: none !important; box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        
        .grid { display: grid !important; gap: 0.4rem !important; page-break-inside: auto !important; break-inside: auto !important; }
        .grid > div { page-break-inside: avoid !important; break-inside: avoid-page !important; }
    }
</style>

<!-- =========================================================================
     ID TRACE SCORE MODULE BLOCK
     ========================================================================= -->
<?php if (isset($ai['id_trace_score'])): ?>
    <div class="action-card-node text-center py-7 relative overflow-hidden bg-white border border-slate-200/90 rounded-3xl my-2 print:py-4 page-break-prevent shadow-sm">
        <div class="relative z-10 space-y-1">
            <div class="text-5xl font-bold text-black tracking-tight print:text-4xl">
                <?= (int)$ai['id_trace_score'] ?><span class="text-xl text-black/50 font-medium tracking-normal"> / 100</span>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-black/60 block">ID Trace Score</span>
        </div>
    </div>
<?php endif; ?>

<!-- =========================================================================
     WHITEBRIDGE CORE CORPORATE REPORTING SCHEMAS
     ========================================================================= -->
<div class="space-y-12 text-left pt-4 print:space-y-4">
    
    <!-- 1. OVERVIEW SUMMARY -->
    <?php if (!empty($ai['personal_info']['overview'])): ?>
    <div class="space-y-3 page-break-prevent">
        <h3 class="text-xs font-bold uppercase tracking-widest text-black border-b border-black/10 pb-1.5">Overview</h3>
        <p class="base-text">
            <?= htmlspecialchars($ai['personal_info']['overview']) ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- 2. ABOUT METRICS CARD -->
    <?php 
    $aboutData = $ai['personal_info']['about'] ?? [];
    if (!empty($aboutData['full_name']) || !empty($aboutData['gender']) || !empty($aboutData['location']) || !empty($aboutData['mobile_number']) || !empty($aboutData['email']) || !empty($aboutData['website'])): 
    ?>
    <div class="space-y-3 page-break-prevent">
        <h3 class="text-xs font-bold uppercase tracking-widest text-black border-b border-black/10 pb-1.5">About</h3>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 space-y-3.5 shadow-2sm max-w-xl print:rounded-xl">
            <?php if (!empty($aboutData['full_name'])): ?>
                <div class="flex justify-between items-start py-0.5 border-b border-slate-100 gap-4"><span class="text-black font-semibold tracking-wide pt-0.5 shrink-0 text-sm">Full Name</span><?= renderSemicolonField($aboutData['full_name']) ?></div>
            <?php endif; ?>
            <?php if (!empty($aboutData['gender'])): ?>
                <div class="flex justify-between items-start py-0.5 border-b border-slate-100 gap-4"><span class="text-black font-semibold tracking-wide shrink-0 text-sm">Gender</span><span class="font-normal text-black text-right text-sm"><?= escapeHtml($aboutData['gender']) ?></span></div>
            <?php endif; ?>
            <?php if (!empty($aboutData['location'])): ?>
                <div class="flex justify-between items-start py-0.5 border-b border-slate-100 gap-4"><span class="text-black font-semibold tracking-wide pt-0.5 shrink-0 text-sm">Location</span><?= renderIconicSemicolonField($aboutData['location'], 'fa-solid fa-location-dot') ?></div>
            <?php endif; ?>
            <?php if (!empty($aboutData['mobile_number'])): ?>
                <div class="flex justify-between items-start py-0.5 border-b border-slate-100 gap-4"><span class="text-black font-semibold tracking-wide pt-0.5 shrink-0 text-sm">Mobile Number</span><?= renderSemicolonField($aboutData['mobile_number']) ?></div>
            <?php endif; ?>
            <?php if (!empty($aboutData['email'])): ?>
                <div class="flex justify-between items-start py-0.5 border-b border-slate-100 gap-4"><span class="text-black font-semibold tracking-wide pt-0.5 shrink-0 text-sm">Email</span><?= renderSemicolonField($aboutData['email']) ?></div>
            <?php endif; ?>
            <?php if (!empty($aboutData['website'])): ?>
                <div class="flex justify-between items-start py-0.5 gap-4"><span class="text-black font-semibold tracking-wide pt-0.5 shrink-0 text-sm">Website</span><?= renderIconicSemicolonField($aboutData['website'], 'fa-solid fa-earth-americas') ?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 3. BEHAVIOR & INTERACTION INSIGHTS -->
    <?php if (!empty($ai['behavior_interaction_insights']['overview'])): ?>
    <div class="space-y-3 page-break-prevent">
        <h3 class="text-xs font-bold uppercase tracking-widest text-black border-b border-black/10 pb-1.5">Behavior & Interaction Insights</h3>
        <p class="base-text">
            <?= htmlspecialchars($ai['behavior_interaction_insights']['overview']) ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- 4. INTERACTION GUIDELINES -->
    <?php 
    $guidelines = $ai['interaction_guidelines'] ?? [];
    $hasHighlights = !empty($guidelines['topics_to_highlight']);
    $hasAvoids = !empty($guidelines['topics_to_avoid']);
    if ($hasHighlights || $hasAvoids): 
    ?>
    <div class="space-y-4 page-break-prevent">
        <h3 class="text-xs font-bold uppercase tracking-widest text-black border-b border-black/10 pb-1.5">Interaction Guidelines</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php if ($hasHighlights): ?>
            <div class="space-y-2.5">
                <h4 class="font-bold text-black uppercase text-[10px] tracking-wider flex items-center gap-1.5"><i class="fa-solid fa-square-check text-black"></i> Possible Topics for Discussion</h4>
                <div class="space-y-2">
                    <?php foreach ($guidelines['topics_to_highlight'] as $topic): ?>
                        <div class="bg-slate-50/60 border border-slate-100 rounded-xl p-3.5 space-y-1 shadow-3sm print:rounded-lg">
                            <div class="font-bold text-black flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-check text-black text-[11px]"></i> <?= htmlspecialchars($topic['headline'] ?? '') ?>
                            </div>
                            <p class="text-black/85 font-normal pl-4 leading-relaxed text-sm"><?= htmlspecialchars($topic['details'] ?? '') ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($hasAvoids): ?>
            <div class="space-y-2.5">
                <h4 class="font-bold text-black uppercase text-[10px] tracking-wider flex items-center gap-1.5"><i class="fa-solid fa-square-minus text-black"></i> Possible Topics to Avoid</h4>
                <div class="space-y-2">
                    <?php foreach ($guidelines['topics_to_avoid'] as $topic): ?>
                        <div class="bg-slate-50/60 border border-slate-100 rounded-xl p-3.5 space-y-1 shadow-3sm print:rounded-lg">
                            <div class="font-bold text-black flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-xmark text-black text-[11px]"></i> <?= htmlspecialchars($topic['headline'] ?? '') ?>
                            </div>
                            <p class="text-black/85 font-normal pl-4 leading-relaxed text-sm"><?= htmlspecialchars($topic['details'] ?? '') ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 5. OBSERVED PUBLIC BEHAVIOURS -->
    <?php if (!empty($ai['observed_public_behaviours'])): ?>
    <div class="space-y-3 page-break-prevent">
        <h3 class="text-xs font-bold uppercase tracking-widest text-black border-b border-black/10 pb-1.5">Observed Public Behaviours</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ($ai['observed_public_behaviours'] as $behaviour): ?>
                <div class="bg-white border border-slate-200/60 shadow-2sm rounded-xl p-4 space-y-1 print:rounded-lg">
                    <div class="font-bold text-black text-sm tracking-tight"><?= htmlspecialchars($behaviour['headline'] ?? '') ?></div>
                    <p class="text-black/85 font-normal leading-relaxed text-sm"><?= htmlspecialchars($behaviour['description'] ?? '') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 6. PREFERENCES -->
    <?php if (!empty($ai['preferences'])): ?>
    <div class="space-y-3 page-break-prevent">
        <h3 class="text-xs font-bold uppercase tracking-widest text-black border-b border-black/10 pb-1.5">Preferences</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ($ai['preferences'] as $pref): ?>
                <div class="bg-slate-50/50 border border-slate-100 rounded-xl p-4 space-y-1 print:rounded-lg">
                    <div class="font-bold text-black text-sm tracking-tight"><?= htmlspecialchars($pref['headline'] ?? '') ?></div>
                    <p class="text-black/85 font-normal leading-relaxed text-sm"><?= htmlspecialchars($pref['description'] ?? '') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 7. EDUCATION -->
    <?php if (!empty($ai['education'])): ?>
    <div class="space-y-3 page-break-prevent">
        <h3 class="text-xs font-bold uppercase tracking-widest text-black border-b border-black/10 pb-1.5">Education</h3>
        <div class="space-y-3">
            <?php foreach ($ai['education'] as $edu): ?>
                <div class="bg-white border border-slate-200/60 shadow-2sm rounded-xl p-4 flex justify-between items-start gap-4 print:rounded-lg">
                    <div class="space-y-0.5">
                        <div class="font-bold text-black text-sm"><?= htmlspecialchars($edu['institution'] ?? '') ?></div>
                        <div class="text-black/85 font-normal text-sm"><?= htmlspecialchars($edu['degree'] ?? '') ?></div>
                    </div>
                    <?php if (!empty($edu['timeline_years'])): ?>
                        <span class="bg-slate-50 text-black border border-slate-200 px-2.5 py-0.5 font-semibold rounded text-xs shrink-0 print:py-0"><?= htmlspecialchars($edu['timeline_years']) ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 7.5 CAREER OVERVIEW MATRIX -->
    <?php 
    $career = $ai['career'] ?? [];
    if (!empty($career['summary']) || !empty($career['history'])): 
    ?>
    <div class="space-y-4 page-break-prevent">
        <h3 class="text-xs font-bold uppercase tracking-widest text-black border-b border-black/10 pb-1.5">Career</h3>
        <?php if (!empty($career['summary'])): ?>
            <p class="base-text"><?= htmlspecialchars($career['summary']) ?></p>
        <?php endif; ?>
        <?php if (!empty($career['history'])): ?>
            <div class="space-y-3 pt-1">
                <?php foreach ($career['history'] as $job): ?>
                    <div class="border border-slate-200/60 bg-white rounded-xl p-4 space-y-1 shadow-2sm print:rounded-lg">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <div class="font-bold text-black text-sm"><?= htmlspecialchars($job['title'] ?? '') ?></div>
                            <span class="text-xs text-black/60 font-normal"><?= htmlspecialchars($job['date_range'] ?? '') ?></span>
                        </div>
                        <div class="text-black font-semibold text-sm"><?= htmlspecialchars($job['company'] ?? '') ?></div>
                        <?php if (!empty($job['description'])): ?>
                            <p class="text-black/85 leading-relaxed font-normal pt-1 border-t border-dashed border-slate-100 mt-1.5 text-sm"><?= htmlspecialchars($job['description']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- 8. EVENTS & MEDIA -->
    <?php if (!empty($ai['events_and_media'])): ?>
    <div class="space-y-4 page-break-prevent">
        <h3 class="text-xs font-bold uppercase tracking-widest text-black border-b border-black/10 pb-1.5">Events & Media</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($ai['events_and_media'] as $event): ?>
                <div class="bg-white border border-slate-200/60 rounded-2xl overflow-hidden flex flex-col justify-between shadow-2sm print:rounded-xl">
                    <?php if (!empty($event['image_url'])): ?>
                        <div class="w-full bg-slate-50 border-b border-slate-100 overflow-hidden flex items-center justify-center p-1">
                            <img src="<?= resolveReportImage($event['image_url']) ?>" class="w-full h-auto object-contain rounded-xl">
                        </div>
                    <?php endif; ?>
                    <div class="p-4 space-y-2 flex-grow flex flex-col justify-between">
                        <div class="space-y-1">
                            <div class="flex items-start justify-between gap-4">
                                <h4 class="font-bold text-black text-sm tracking-tight leading-snug"><?= htmlspecialchars($event['title'] ?? '') ?></h4>
                                <span class="text-xs bg-slate-50 text-black border border-slate-200 font-medium px-2 py-0.5 rounded shrink-0 mt-0.5 print:py-0"><?= htmlspecialchars($event['date'] ?? '') ?></span>
                            </div>
                            <p class="text-black/85 font-normal leading-relaxed pt-1 text-sm"><?= htmlspecialchars($event['description'] ?? '') ?></p>
                        </div>
                        <div class="pt-1.5 border-t border-dashed border-slate-100 text-xs font-normal text-black/60 flex items-center justify-between">
                            <span>Source</span>
                            <span class="text-black font-semibold"><?= htmlspecialchars($event['content_source'] ?? 'Registry Network') ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 9. MEDIA CHECK -->
    <?php if (!empty($ai['media_check'])): ?>
    <div class="space-y-4 page-break-prevent">
        <h3 class="text-xs font-bold uppercase tracking-widest text-black border-b border-black/10 pb-1.5">Media Check</h3>
        <div class="space-y-3 max-w-xl pt-1">
            <?php foreach ($ai['media_check'] as $key => $checkStatement): 
                $casedHeading = str_replace('_', ' ', $key);
                $finalStatement = (empty($checkStatement) || strtolower($checkStatement) === 'none') ? "We didn't find any records that the person " . htmlspecialchars(strtolower($casedHeading)) . "." : $checkStatement;
            ?>
                <div class="bg-white border border-slate-200/60 rounded-xl p-4 flex items-center gap-4 shadow-3sm text-left print:rounded-lg">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center text-white text-xs shrink-0 select-none print:w-6 print:h-6">
                        <i class="fa-solid fa-check text-xs stroke-[2px]"></i>
                    </div>
                    <div class="space-y-0.5">
                        <div class="font-bold text-black text-sm capitalize tracking-tight">
                            <?= htmlspecialchars($casedHeading) ?>
                        </div>
                        <p class="text-black/60 font-normal text-sm leading-relaxed"><?= htmlspecialchars($finalStatement) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 10. SOCIAL MEDIA ANALYTICS DECK & BREAKDOWN -->
    <?php if (!empty($ai['social_media'])): $sm = $ai['social_media']; ?>
    <div class="space-y-4 page-break-prevent">
        <h3 class="text-xs font-bold uppercase tracking-widest text-black border-b border-black/10 pb-1.5">Social Media</h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200/70 shadow-2sm rounded-xl p-4 flex flex-col justify-between min-h-[90px] text-left print:min-h-[65px]">
                <div class="mt-1 space-y-1">
                    <span class="text-sm font-bold text-black tracking-wide flex items-center gap-1.5 w-full"><i class="fa-solid fa-users text-black text-[12px]"></i> Total Followers</span>
                    <div class="text-sm font-normal text-black/85 tracking-tight"><?= htmlspecialchars($sm['total_followers'] ?? '0') ?></div>
                </div>
            </div>
            <div class="bg-white border border-slate-200/70 shadow-2sm rounded-xl p-4 flex flex-col justify-between min-h-[90px] text-left print:min-h-[65px]">
                <div class="mt-1 space-y-1">
                    <span class="text-sm font-bold text-black tracking-wide flex items-center gap-1.5 w-full"><i class="fa-regular fa-thumbs-up text-black text-[12px]"></i> Like Average</span>
                    <div class="text-sm font-normal text-black/85 tracking-tight"><?= htmlspecialchars($sm['like_average'] ?? '0') ?></div>
                </div>
            </div>
            <div class="bg-white border border-slate-200/70 shadow-2sm rounded-xl p-4 flex flex-col justify-between min-h-[90px] text-left print:min-h-[65px]">
                <div class="mt-1 space-y-1">
                    <span class="text-sm font-bold text-black tracking-wide flex items-center gap-1.5 w-full"><i class="fa-regular fa-comment text-black text-[12px]"></i> Comment Average</span>
                    <div class="text-sm font-normal text-black/85 tracking-tight"><?= htmlspecialchars($sm['comment_average'] ?? '0') ?></div>
                </div>
            </div>
            <div class="bg-white border border-slate-200/70 shadow-2sm rounded-xl p-4 flex flex-col justify-between min-h-[90px] text-left print:min-h-[65px]">
                <div class="mt-1 space-y-1">
                    <span class="text-sm font-bold text-black tracking-wide flex items-center gap-1.5 w-full"><i class="fa-regular fa-face-smile text-black text-[12px]"></i> Engagement Rate</span>
                    <div class="text-sm font-normal text-black/85 tracking-tight"><?= htmlspecialchars($sm['engagement_rate'] ?? '0%') ?></div>
                </div>
            </div>
            <div class="bg-white border border-slate-200/70 shadow-2sm rounded-xl p-4 flex flex-col justify-between min-h-[90px] text-left col-span-2 md:col-span-1 print:min-h-[65px]">
                <div class="mt-1 space-y-1">
                    <span class="text-sm font-bold text-black tracking-wide flex items-center gap-1.5 w-full"><i class="fa-solid fa-briefcase text-black text-[12px]"></i> Industry</span>
                    <div class="text-sm font-normal text-black/85 leading-normal tracking-tight"><?= htmlspecialchars($sm['primary_industry'] ?? 'N/A') ?></div>
                </div>
            </div>
            <div class="bg-white border border-slate-200/70 shadow-2sm rounded-xl p-4 flex flex-col justify-between min-h-[90px] text-left col-span-2 md:col-span-1 print:min-h-[65px]">
                <div class="mt-1 space-y-1">
                    <span class="text-sm font-bold text-black tracking-wide flex items-center gap-1.5 w-full"><i class="fa-solid fa-bullseye text-black text-[12px]"></i> Target Audience</span>
                    <div class="text-sm font-normal text-black/85 leading-normal tracking-tight"><?= htmlspecialchars($sm['target_audience_demographics'] ?? 'N/A') ?></div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($sm['platform_breakdown'])): ?>
        <div class="mt-2 pt-1 print:mt-1">
            <div class="bg-white border border-slate-200/70 shadow-2sm rounded-2xl p-4 text-left font-normal text-black space-y-2 print:p-3 print:rounded-xl">
                <span class="text-xs font-bold uppercase tracking-widest text-black block">Social Media Breakdown</span>
                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm print:gap-x-4">
                    <?php foreach ($sm['platform_breakdown'] as $plat => $pct): if (empty($pct) || $pct === '0%') continue; 
                        $iconClass = 'fa-solid fa-link text-black/40';
                        $iconColor = 'text-black';
                        $lowPlat = strtolower($plat);
                        if ($lowPlat === 'facebook') { $iconClass = 'fa-brands fa-facebook'; $iconColor = 'text-blue-600'; }
                        elseif ($lowPlat === 'instagram') { $iconClass = 'fa-brands fa-instagram'; $iconColor = 'text-pink-600'; }
                        elseif ($lowPlat === 'linkedin') { $iconClass = 'fa-brands fa-linkedin'; $iconColor = 'text-blue-800'; }
                        elseif ($lowPlat === 'twitter' || $lowPlat === 'x') { $iconClass = 'fa-brands fa-x-twitter'; $iconColor = 'text-black'; }
                        elseif ($lowPlat === 'tiktok') { $iconClass = 'fa-brands fa-tiktok'; $iconColor = 'text-black'; }
                    ?>
                        <div class="flex items-center gap-1.5 font-normal text-black/85">
                            <i class="<?= $iconClass ?> <?= $iconColor ?> text-base print:hidden"></i>
                            <span class="capitalize text-black font-semibold"><?= htmlspecialchars($plat) ?></span>
                            <span class="text-black/60 font-mono font-medium">(<?= htmlspecialchars($pct) ?>)</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- 11. TOP ENGAGEMENT POST -->
    <?php if (!empty($ai['top_post']['title']) || !empty($ai['top_post']['description'])): $tp = $ai['top_post']; ?>
    <div class="space-y-3 page-break-prevent">
        <h3 class="text-xs font-bold uppercase tracking-widest text-black border-b border-black/10 pb-1.5">Top Post</h3>
        <div class="bg-white border border-slate-200/80 shadow-2sm rounded-2xl overflow-hidden max-w-md mx-auto sm:mx-0 text-left print:rounded-xl">
            <?php if (!empty($tp['image_url'])): ?>
                <div class="w-full bg-slate-50 border-b border-slate-100 flex items-center justify-center p-1">
                    <img src="<?= resolveReportImage($tp['image_url']) ?>" class="w-full h-auto object-contain rounded-xl">
                </div>
            <?php endif; ?>
            <div class="p-4 space-y-3">
                <div class="space-y-1">
                    <div class="flex items-start justify-between gap-4">
                        <h4 class="font-bold text-black text-sm tracking-tight leading-snug"><?= htmlspecialchars($tp['title'] ?? 'Highest Reaction Entry') ?></h4>
                        <span class="text-xs bg-slate-50 text-black border border-slate-200 font-medium px-2 py-0.5 rounded shrink-0 mt-0.5 print:py-0"><?= htmlspecialchars($tp['date'] ?? '') ?></span>
                    </div>
                    <p class="text-black/85 font-normal leading-relaxed text-sm pt-1"><?= htmlspecialchars($tp['description'] ?? '') ?></p>
                </div>
                <div class="pt-2 border-t border-dashed border-slate-100 flex items-center justify-between text-xs font-normal uppercase">
                    <div class="flex items-center gap-3 text-black/60">
                        <span><i class="fa-regular fa-thumbs-up mr-1 text-black/40"></i><?= (int)($tp['likes'] ?? 0) ?></span>
                        <span><i class="fa-regular fa-comment mr-1 text-black/40"></i><?= (int)($tp['comments'] ?? 0) ?></span>
                    </div>
                    <span class="text-black font-semibold"><?= htmlspecialchars($tp['source_platform'] ?? 'Social Platform') ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- 12. RECENT POSTS TRACKS -->
    <?php if (!empty($ai['recent_posts'])): ?>
    <div class="space-y-4 page-break-prevent">
        <h3 class="text-xs font-bold uppercase tracking-widest text-black border-b border-black/10 pb-1.5">Recent Posts</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($ai['recent_posts'] as $post): ?>
                <div class="bg-white border border-slate-200/60 rounded-2xl overflow-hidden flex flex-col justify-between shadow-3sm print:rounded-xl">
                    <?php if (!empty($post['image_url'])): ?>
                        <div class="w-full bg-slate-50 border-b border-slate-100 flex items-center justify-center p-1">
                            <img src="<?= resolveReportImage($post['image_url']) ?>" class="w-full h-auto object-contain rounded-xl">
                        </div>
                    <?php endif; ?>
                    <div class="p-4 space-y-3 flex-grow flex flex-col justify-between">
                        <div class="space-y-1">
                            <div class="flex items-start justify-between gap-4">
                                <h4 class="font-bold text-black text-sm tracking-tight leading-snug"><?= htmlspecialchars($post['title'] ?? 'Platform Publication') ?></h4>
                                <span class="text-xs bg-slate-50 text-black border border-slate-200 font-medium px-2 py-0.5 rounded shrink-0 mt-0.5 print:py-0"><?= htmlspecialchars($post['date'] ?? '') ?></span>
                            </div>
                            <p class="text-black/85 font-normal leading-relaxed pt-1 text-sm"><?= htmlspecialchars($post['description'] ?? '') ?></p>
                        </div>
                        <div class="pt-2 border-t border-dashed border-slate-100 flex items-center justify-between text-xs font-normal uppercase tracking-wider">
                            <div class="flex items-center gap-2.5 text-black/60">
                                <span><i class="fa-regular fa-thumbs-up mr-0.5 text-black/40"></i> <?= (int)($post['likes'] ?? 0) ?></span>
                                <span><i class="fa-regular fa-comment mr-0.5 text-black/40"></i> <?= (int)($post['comments'] ?? 0) ?></span>
                            </div>
                            <span class="text-black font-semibold"><?= htmlspecialchars($post['source_platform'] ?? 'Platform Feed') ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 13. BRAND SAFETY ANALYSIS GRID -->
    <?php if (!empty($ai['brand_safety']['social_context_check'])): ?>
    <div class="space-y-4 page-break-prevent">
        <h3 class="text-xs font-bold uppercase tracking-widest text-black border-b border-black/10 pb-1.5">Brand Safety Analysis</h3>
        
        <div class="space-y-3 max-w-xl pt-1">
            <?php foreach ($ai['brand_safety']['social_context_check'] as $riskKey => $riskDetail): 
                $finalRiskStatement = (empty($riskDetail) || strtolower($riskDetail) === 'none') ? 'No content detected.' : $riskDetail;
            ?>
                <div class="bg-white border border-slate-200/60 rounded-xl p-4 flex items-center gap-4 shadow-3sm text-left print:rounded-lg">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center text-white text-xs shrink-0 select-none print:w-6 print:h-6">
                        <i class="fa-solid fa-check text-xs stroke-[2px]"></i>
                    </div>
                    <div class="space-y-0.5">
                        <div class="font-bold text-black text-sm capitalize tracking-tight">
                            <?= htmlspecialchars(str_replace('_', ' ', $riskKey)) ?> <i class="fa-regular fa-circle-question text-[10px] text-black/30 ml-0.5 print:hidden" title="Compliance verification parameters matrix mapping"></i>
                        </div>
                        <p class="text-black/60 font-normal text-sm leading-relaxed"><?= htmlspecialchars($finalRiskStatement) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>
