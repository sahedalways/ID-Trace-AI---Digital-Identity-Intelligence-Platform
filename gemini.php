<?php
/**
 * OSINT Universal Intelligence Console — Cognitive Matrix Synthesis Core
 * File: gemini.php
 * Context: Runs sequentially inside process.php context loop mapping or standalone dispatch / report.php inline scope
 */

require_once 'config.php';

// Ensure the endpoint correctly communicates runtime statuses if triggered via AJAX directly
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !headers_sent()) {
    header('Content-Type: application/json; charset=UTF-8');
}

// Inherit target identifier parameters from parental orchestrator scope or request payload variables
$vid = isset($vid) ? trim($vid) : (isset($_POST['id']) ? trim($_POST['id']) : '');

if (empty($vid)) {
    $errResponse = ['success' => false, 'error' => 'Missing workspace identity token parameters inside payload channels.'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode($errResponse);
        exit;
    }
    error_log("Gemini Synthesis Core Error: " . $errResponse['error']);
    return;
}

try {
    // =========================================================================
    // STEP 1: CONSOLIDATE DISCOVERED PRIMARY FOOTPRINTS & RAW API TARGET FIELDS
    // =========================================================================
    $viewStmt = $pdo->prepare("SELECT `name`, `input_email`, `avatar`, `url`, `social_urls` FROM `view` WHERE `vid` = ? LIMIT 1");
    $viewData = $viewStmt->execute([$vid]) ? $viewStmt->fetch(PDO::FETCH_ASSOC) : null;

    if (!$viewData) {
        throw new Exception("Operational workflow disruption: Master record missing from directory layout lookup parameters.");
    }

    $reportStmt = $pdo->prepare("SELECT * FROM `reports` WHERE `vid` = ? LIMIT 1");
    $reportData = $reportStmt->execute([$vid]) ? $reportStmt->fetch(PDO::FETCH_ASSOC) : null;

    if (!$reportData) {
        throw new Exception("Operational workflow disruption: Report target tracking matrix entity could not be traced.");
    }

    $primaryInputs = [
        "recorded_primary_name"  => trim($viewData['name'] ?? ''),
        "recorded_primary_email" => trim($viewData['input_email'] ?? ''),
        "recorded_avatar_path"   => trim($viewData['avatar'] ?? ''),
        "primary_discovery_url"  => trim($viewData['url'] ?? ''),
        "secondary_social_urls"  => array_filter(array_map('trim', explode(',', $viewData['social_urls'] ?? '')))
    ];

    $rawScrapedFootprints = [];
    $dataColumnMap = [
        'raw_profile'      => 'primary_profile_raw',
        'raw_post'         => 'activity_feed_posts_raw',
        'raw_following'    => 'social_following_network_raw',
        'raw_reverse_data' => 'google_lens_reverse_image_raw',
        'raw_email_data'   => 'skip_trace_breach_indices_raw'
    ];

    foreach ($dataColumnMap as $dbColumn => $labelKey) {
        if (!empty($reportData[$dbColumn])) {
            $decoded = json_decode($reportData[$dbColumn], true);
            if (!empty($decoded)) {
                $rawScrapedFootprints[$labelKey] = $decoded;
            }
        }
    }

    // =========================================================================
    // PARSING SYSTEM PRESERVING EXTANT LOCAL PARAMETER KEYS
    // =========================================================================
    if (!empty($rawScrapedFootprints['activity_feed_posts_raw'])) {
        foreach ($rawScrapedFootprints['activity_feed_posts_raw'] as $platformKey => &$postsDataBlock) {
            if (is_array($postsDataBlock)) {
                foreach ($postsDataBlock as &$postItem) {
                    if (is_array($postItem)) {
                        foreach ($postItem as $key => &$value) {
                            if (is_string($value)) {
                                $trimmedValue = trim($value);
                                if (strpos($trimmedValue, 'uploads/') === 0) {
                                    continue; 
                                } 
                                elseif (strpos($trimmedValue, 'http') === 0 && (strpos($trimmedValue, 'cdninstagram.com') !== false || strpos($trimmedValue, 'fbcdn.net') !== false || strpos($trimmedValue, 'akamaihd.net') !== false)) {
                                    $value = '[CDN_LINK_REMOVED]';
                                }
                            }
                        }
                        unset($value);
                    }
                }
                unset($postItem);
            }
        }
        unset($postsDataBlock);
    }

    $masterCompiledContext = [
        "target_identity_vid"     => $vid,
        "operator_entered_fields" => $primaryInputs,
        "scraped_intelligence"    => $rawScrapedFootprints
    ];

    $minifiedContextJson = json_encode($masterCompiledContext, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // =========================================================================
    // STEP 2: ORCHESTRATE COGNITIVE PROMPT PIPELINE DESIGN MATRICES
    // =========================================================================
    $geminiApiKey = GEMINI_API_KEY; 
    
    $modelsLineupOrder = [
        "gemini-3.5-flash",
        "gemini-2.5-flash",
        "gemini-3-flash-preview"
    ];

    $geminiPrompt = "Analyze this fully consolidated OSINT intelligence dossier data string. Synthesize all connections cross-platform and compile a high-fidelity profile assessment dossier mapping findings exactly into the structural blueprint schema template provided.\n\n";
    $geminiPrompt .= "CONSOLIDATED INTERCEPTED DATA COMPILATION:\n" . $minifiedContextJson . "\n\n";
    $geminiPrompt .= "Audit all digital parameters closely. Fill out behavioral profiling parameters using deductive analysis.";

    $systemInstruction = "You are an elite, corporate-grade OSINT investigative profiler AI agent. Your mission is to analyze all raw digital footprint logs, cross-platform posts, and metadata metrics provided, and return a comprehensive, highly-structured JSON dossier report matching the premium Whitebridge Corporate intelligence layout style.\n\n"
                       . "CRITICAL NARRATIVE DENSITY EXPANSION & IDENTITY ANALYSIS RULES:\n"
                       . "1. 'overview': You MUST structure this summary paragraph systematically. At the very beginning of the overview text string, analyze the discovered target names (found in the 'about' matrix). Provide an explicit etymological breakdown mapping out which language group the name originated from, along with its specific literal meanings or cultural roots. Once this name origin profile is established, immediately continue with the rest of the target behavior metrics, location context, and traits as previously instructed.\n"
                       . "2. For the important report blocks — including 'topics_to_highlight', 'topics_to_avoid', 'observed_public_behaviours', 'preferences', and 'events_and_media' — you MUST aggressively extract every possible hint, behavior, or clue from the raw data. Elaborate deeply and generate highly detailed text definitions to make the report as comprehensive and long as possible.\n\n"
                       . "CRITICAL ABOUT DATACARD DELIMITATION & GEOGRAPHICAL SYNTHESIS CONSTRAINTS:\n"
                       . "1. Extract all trace variations for identity contact indicators. You MUST include properties for: 'full_name', 'gender', 'location', 'mobile_number', 'email', and 'website'.\n"
                       . "2. 'location' Synthesis Normalization Rule: You MUST process all raw physical location addresses, footprints, and coordinates found across the log files using deductive analysis. Do not present localized string fragments or multi-city lists within the same nation (e.g., avoid listing multiple entries like 'Dhaka; Pabna; Rajshahi'). Instead, consolidate entries into exactly ONE primary geographic location entity per distinct country. For each resolved country anchor, extrapolate and output the parameters cleanly in a 'City, State, Country' format. Separate distinct international country boundaries using a semicolon (e.g., 'Dhaka, Dhaka Division, Bangladesh; Mumbai, Maharashtra, India').\n"
                       . "3. 'website' Exclusion Constraint: You MUST extract independent personal portfolios, corporate domains, or custom project websites belonging to the target. Under no circumstances should you fill this property with raw social media network profile URLs (e.g., do not show Facebook, Instagram, LinkedIn, or Twitter/X profile links inside the 'website' block). If only social platform links are discovered without distinct independent URLs, you MUST explicitly set the 'website' field to null.\n"
                       . "4. If multiple separate phone numbers, email addresses, non-social websites, or name variations exist, string-concatenate them together inside their respective property values, separating each unique item cleanly with a semicolon. Keep 'gender' cased exactly as 'Male' or 'Female'. Set any field to null explicitly if completely untraceable.\n\n"
                       . "CRITICAL COMPLIANCE & BRAND SAFETY DYNAMIC VALUE ASSIGNMENT RULES:\n"
                       . "For every key element evaluated inside both the 'media_check' and 'brand_safety.social_context_check' blocks:\n"
                       . "- If no adverse records, context indicators, or tracking flags are discovered, you MUST output exactly the string value \"none\".\n"
                       . "- If explicit flag logs, warning matching content text elements, or adverse traces are detected, you MUST output a detailed, natural language text summary explicitly describing the finding data (e.g., do NOT write the literal word 'data' as a placeholder; instead, summarize the actual behavioral context or media trace information found).\n\n"
                       . "CRITICAL RECENT_POSTS COMPLETE TIMELINE UNROLLING & CROSS-SOURCE MERGING RULES:\n"
                       . "1. 'recent_posts' Exhaustive Extraction Guardrail: You MUST fully unroll and extract ALL possible post components available in 'scraped_intelligence'. If no posts exist or post logs are empty, you MUST return an empty array `[]` for this field instead of failing. Never selectively omit, truncate, summarize away, or selectively filter posts based on their platform type or data size constraints. Every single message block, timeline action, or public feed item must be systematically appended to the array.\n"
                       . "2. Cross-Platform Source Merging: If the exact same content text or message signature is found repeated across multiple social media platforms (e.g., cross-posted matching content items on both Facebook and Instagram), you MUST combine them into a single entry item to prevent duplication, and list their joint networks inside the 'source_platform' field formatted with a forward slash separating them (e.g., \"Facebook/Instagram\" or \"Instagram/LinkedIn\"). Otherwise, provide the single source name normally.\n"
                       . "3. Missing Media Resilience: Never skip or filter out text posts simply because they are missing images, missing upload references, or contain stripped '[CDN_LINK_REMOVED]' entries. For items without an image path, you must preserve the text content cleanly, keep the post inside the feed tracking array, and map the 'image_url' property value explicitly to null.\n"
                       . "4. Reverse-Chronological Order: Sort the entire consolidated unified feed tracking array strictly in reverse chronological order (most recent first). Translate alternative language elements (e.g., Bengali, Hindi, etc.) accurately into clean English prose without appending any extra synthetic commentary or interpretations.\n"
                       . "5. 'events_and_media': Isolate exactly the top 4 to 5 unique core behavioral themes or lifestyle traits from the user's timeline. Map each to its corresponding local 'uploads/' string if available. The title and narrative description blocks inside this array must be completely AI-generated, highly detailed, and deeply expanded to maximize text volume (following the premium design profile layout conventions shown in 1000301384.png).\n"
                       . "6. 'top_post': Identify and isolate the single post object containing the highest overall traceable engagement metrics calculated by raw volume summation (Likes + Comments). If no original posts or activities are found in the payload dataset, map this object completely to an empty object wrapper `{}` or set its internal details to null fields.\n\n"
                       . "CRITICAL SOCIAL MEDIA PERCENTAGE BREAKDOWN RULES:\n"
                       . "1. For the 'platform_breakdown' object metrics parameters, you MUST dynamically calculate the explicit percentage ratio weight distributions based rendered solely on the count volume metrics of active unique posts parsed across the platforms.\n"
                       . "2. The strings passed to properties ('facebook', 'instagram', 'linkedin', 'twitter', 'tiktok') MUST feature cleanly formatted percentage notation suffix signatures containing only digits and the percentage symbol (e.g., \"60%\").\n"
                       . "3. The complete consolidated combination summation weights of all platform parameters MUST add up to exactly 100% altogether. If only one singular network data platform context layer is supplied within the data pool payloads (e.g., Instagram posts exclusively), you MUST explicitly assign that matching active node a full weight valuation parameter string equal exactly to \"100%\", mapping all remaining missing or unrepresented network field parameters cleanly to \"0%\". If no post data platforms are found at all, map all platform keys cleanly to \"0%\".\n\n"
                       . "CRITICAL IMAGE RESOLUTION MATRIX CORE TRUTH:\n"
                       . "1. Inspect each individual post data object. If any property parameter key contains a path string starting exactly with 'uploads/', you MUST assign that exact string to the output 'image_url' property inside events_and_media, top_post, and recent_posts.\n"
                       . "2. Never map external CDN URLs to image fields. If a post has no parameter value starting with 'uploads/', or it only contains '[CDN_LINK_REMOVED]' strings, you MUST set the output 'image_url' to null explicitly.\n\n"
                       . "CRITICAL PRE-EVALUATION THRESHOLD RULE:\n"
                       . "You MUST proceed to construct the full dossier report regardless of the volume or count of unique social media post items available in 'scraped_intelligence'. There is no post threshold requirement to compile a dossier. Extract every available clue, indicator, and tracking fragment across all data points to generate the full dossier sections deeply. The top-level property \"status\" MUST always be set to \"success\". If no posts are discovered, set 'top_post' to null or empty parameters and leave the 'recent_posts' array empty `[]` while fully synthesizing all other profile fields.\n\n"
                       . "You must return a response strictly following this minified JSON structure schema framework without deviations:\n"
                       . "{\n"
                       . "  \"status\": \"success\",\n"
                       . "  \"personal_info\": {\n"
                       . "    \"overview\": \"string featuring name origin analysis first, followed by baseline background context summaries\",\n"
                       . "    \"about\": {\n"
                       . "      \"full_name\": \"string separated by semicolons if multiple variations exist, or null\",\n"
                       . "      \"gender\": \"string cased exactly as Male or Female, or null\",\n"
                       . "      \"location\": \"string normalized with one anchor entry per country separated by semicolons, or null\",\n"
                       . "      \"mobile_number\": \"string separated by semicolons if multiple parameters exist, or null\",\n"
                       . "      \"email\": \"string separated by semicolons if multiple fields exist, or null\",\n"
                       . "      \"website\": \"string separated by semicolons if multiple targets exist, or null\"\n"
                       . "    }\n"
                       . "  },\n"
                       . "  \"behavior_interaction_insights\": {\n"
                       . "    \"overview\": \"string\"\n"
                       . "  },\n"
                       . "  \"interaction_guidelines\": {\n"
                       . "    \"topics_to_highlight\": [\n"
                       . "      { \"headline\": \"string\", \"details\": \"string\" }\n"
                       . "    ],\n"
                       . "    \"topics_to_avoid\": [\n"
                       . "      { \"headline\": \"string\", \"details\": \"string\" }\n"
                       . "    ]\n"
                       . "  },\n"
                       . "  \"observed_public_behaviours\": [\n"
                       . "    { \"headline\": \"string\", \"description\": \"string\" }\n"
                       . "  ],\n"
                       . "  \"preferences\": [\n"
                       . "    { \"headline\": \"string\", \"description\": \"string\" }\n"
                       . "  ],\n"
                       . "  \"education\": [\n"
                       . "    { \"institution\": \"string\", \"degree\": \"string\", \"timeline_years\": \"string\" }\n"
                       . "  ] or null,\n"
                       . "  \"career\": {\n"
                       . "    \"summary\": \"string\",\n"
                       . "    \"history\": [\n"
                       . "      { \"title\": \"string\", \"company\": \"string\", \"date_range\": \"string\", \"description\": \"string\" }\n"
                       . "    ]\n"
                       . "  } or null,\n"
                       . "  \"events_and_media\": [\n"
                       . "    { \"image_url\": \"string or null\", \"title\": \"string\", \"date\": \"string\", \"description\": \"string\", \"content_source\": \"string\" }\n"
                       . "  ],\n"
                       . "  \"media_check\": {\n"
                       . "    \"grave_crime\": \"none or finding summary details narrative\",\n"
                       . "    \"violation_of_public_service_law\": \"none or finding summary details narrative\",\n"
                       . "    \"deliberate_offence\": \"none or finding summary details narrative\",\n"
                       . "    \"corruption_offence\": \"none or finding summary details narrative\",\n"
                       . "    \"substance_abuse\": \"none or finding summary details narrative\",\n"
                       . "    \"public_procurement_violation\": \"none or finding summary details narrative\",\n"
                       . "    \"politicians_code_violation\": \"none or finding summary details narrative\",\n"
                       . "    \"civil_servants_code_violation\": \"none or finding summary details narrative\",\n"
                       . "    \"scandal\": \"none or finding summary details narrative\",\n"
                       . "    \"needs_attention\": \"none or finding summary details narrative\"\n"
                       . "  },\n"
                       . "  \"social_media\": {\n"
                       . "    \"total_followers\": \"string\",\n"
                       . "    \"like_average\": \"string\",\n"
                       . "    \"comment_average\": \"string\",\n"
                       . "    \"engagement_rate\": \"string\",\n"
                       . "    \"primary_industry\": \"string\",\n"
                       . "    \"target_audience_demographics\": \"string\",\n"
                       . "    \"platform_breakdown\": { \"facebook\": \"string\", \"instagram\": \"string\", \"linkedin\": \"string\", \"twitter\": \"string\", \"tiktok\": \"string\" }\n"
                       . "  },\n"
                       . "  \"top_post\": {\n"
                       . "    \"title\": \"string\",\n"
                       . "    \"date\": \"string\",\n"
                       . "    \"description\": \"string\",\n"
                       . "    \"likes\": 0,\n"
                       . "    \"comments\": 0,\n"
                       . "    \"source_platform\": \"string\",\n"
                       . "    \"image_url\": \"string or null\"\n"
                       . "  } or null,\n"
                       . "  \"recent_posts\": [\n"
                       . "    { \"title\": \"string\", \"date\": \"string\", \"description\": \"string\", \"likes\": 0,\n"
                       . "      \"comments\": 0, \"source_platform\": \"string\", \"image_url\": \"string or null\" }\n"
                       . "  ],\n"
                       . "  \"brand_safety\": {\n"
                       . "    \"social_context_check\": {\n"
                       . "      \"sensitive_content\": \"none or finding summary details narrative\",\n"
                       . "      \"alcohol\": \"none or finding summary details narrative\",\n"
                       . "      \"crime_related_content\": \"none or finding summary details narrative\",\n"
                       . "      \"offensive_content\": \"none or finding summary details narrative\",\n"
                       . "      \"toxic_content\": \"none or finding summary details narrative\"\n"
                       . "    }\n"
                       . "  },\n"
                       . "  \"id_trace_score\": 75\n"
                       . "}";

    $geminiPayload = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $geminiPrompt]
                ]
            ]
        ],
        "systemInstruction" => [
            "parts" => [
                ["text" => $systemInstruction]
            ]
        ],
        "generationConfig" => [
            "responseMimeType" => "application/json"
        ]
    ];

    // =========================================================================
    // STEP 3: EXECUTE SECURE DISPATCH TO INFERENCE ENDPOINT SYSTEM WITH RETRIES
    // =========================================================================
    $geminiResponse = null;
    $geminiHttpCode = 0;
    $successfulExecution = false;
    $aiAnalysisOutputString = '';

    foreach ($modelsLineupOrder as $index => $modelCandidate) {
        
        $geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/" . $modelCandidate . ":generateContent?key=" . $geminiApiKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $geminiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($geminiPayload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 120); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $geminiResponse = curl_exec($ch);
        $geminiHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($geminiHttpCode === 200 && !empty($geminiResponse)) {
            $geminiResultArray = json_decode($geminiResponse, true);
            $aiAnalysisOutputString = $geminiResultArray['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            if (!empty($aiAnalysisOutputString) && json_decode($aiAnalysisOutputString) !== null) {
                $successfulExecution = true;
                break;
            }
        }
        
        error_log("Gemini Vector Route [{$modelCandidate}] drop-out with code {$geminiHttpCode}. Stepping to fallback chain.");
        sleep(2);
    }

    if (!$successfulExecution) {
        throw new Exception("All models in the high-availability pipeline failed execution sequences or timed out.");
    }

    // =========================================================================
    // STEP 4: PERSIST AI ANALYSIS LAYERS ON SUCCESSFUL PROJECTION RUNS
    // =========================================================================
    $cleanJsonArray = json_decode($aiAnalysisOutputString, true);
    $cleanJsonString = json_encode($cleanJsonArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $finalStatement = $pdo->prepare("UPDATE `reports` SET `ai_analysis` = ?, `updated_at` = NOW() WHERE `vid` = ?");
    $finalStatement->execute([$cleanJsonString, $vid]);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode(['success' => true, 'status' => 'success', 'message' => 'Cognitive data extraction sequence completed successfully.']);
        exit;
    }

} catch (Exception $e) {
    error_log("OSINT Gemini Matrix Synthesis Critical Crash [VID: {$vid}]: " . $e->getMessage());

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
    return;
}