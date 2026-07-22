<?php
/**
 * File: social_fetch.php
 * Endpoint dedicated to extracting profiles and paginated timelines from social networks.
 */
require_once 'config.php';
header('Content-Type: application/json');

$vid = isset($_POST['id']) ? trim($_POST['id']) : '';
if (empty($vid)) {
    echo json_encode(['success' => false, 'error' => 'Missing ID inside target request payload.']);
    exit;
}

try {
    // Force/Keep status state as active processing phase
    $pdo->prepare("UPDATE `reports` SET `status` = 'processing' WHERE `vid` = ?")->execute([$vid]);

    $stmt = $pdo->prepare("SELECT `url` FROM `view` WHERE `vid` = ? LIMIT 1");
    $stmt->execute([$vid]);
    $target = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$target) throw new Exception("Target master profile record missing inside views database.");

    $targetUrl = trim($target['url'] ?? '');
    $instagramUsername = '';

    if (stripos($targetUrl, 'instagram.com') !== false) {
        $parsedUrl = parse_url((strpos($targetUrl, 'http') === 0 ? '' : 'https://') . $targetUrl, PHP_URL_PATH);
        $instagramUsername = trim($parsedUrl, '/');
    } else {
        $instagramUsername = $targetUrl;
    }

    if (empty($instagramUsername)) {
        throw new Exception("Unable to decode clean platform account handle configuration routing.");
    }

    $apiKey = SOCIALFETCH_API_KEY_2;

    function runSocialQuery($url, $apiKey) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "x-api-key: " . $apiKey,
            "Accept: application/json"
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 35);
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($code === 200) ? json_decode($res, true) : null;
    }

    // Extract raw profile configuration metadata matrices
    $profileUrl = "https://api.socialfetch.dev/v1/instagram/profiles/" . urlencode($instagramUsername);
    $rawProfileData = runSocialQuery($profileUrl, $apiKey);

    if (!$rawProfileData) throw new Exception("SocialFetch target profile layer completely dropped matching query execution.");

    // Loop through 3 custom cursor pagination arrays cleanly
    $allPosts = [];
    $nextCursor = '';
    $pagesToFetch = 3;

    for ($page = 1; $page <= $pagesToFetch; $page++) {
        $postsUrl = "https://api.socialfetch.dev/v1/instagram/profiles/" . urlencode($instagramUsername) . "/posts";
        if (!empty($nextCursor)) {
            $postsUrl .= "?cursor=" . urlencode($nextCursor);
        }

        $pageResponse = runSocialQuery($postsUrl, $apiKey);

        if (is_array($pageResponse) && isset($pageResponse['data']['posts'])) {
            $allPosts = array_merge($allPosts, $pageResponse['data']['posts']);
            $nextCursor = $pageResponse['data']['page']['nextCursor'] ?? '';
            if (empty($nextCursor)) break;
        } else {
            break;
        }
    }

    $rawPostsData = [
        "data" => [
            "lookupStatus" => !empty($allPosts) ? "found" : "not_found",
            "posts" => $allPosts
        ]
    ];

    // Clear unneeded payload properties to protect processing constraints
    if (is_array($rawProfileData) && isset($rawProfileData['data'])) {
        unset($rawProfileData['data']['recentProfiles'], $rawProfileData['data']['recentPosts']);
        if (isset($rawProfileData['data']['profile'])) {
            unset($rawProfileData['data']['profile']['avatarUrl'], $rawProfileData['data']['profile']['avatarUrlHd']);
        }
    }

    if (!empty($rawPostsData['data']['posts'])) {
        foreach ($rawPostsData['data']['posts'] as &$post) {
            if (is_array($post)) unset($post['displayUrl']);
        }
        unset($post);
    }

    // FIXED: Keep status string as 'processing' so it respects your database constraints perfectly
    $updateRaw = $pdo->prepare("UPDATE `reports` SET `raw_profile` = ?, `raw_post` = ?, `status` = 'processing' WHERE `vid` = ?");
    $updateRaw->execute([
        json_encode($rawProfileData),
        json_encode($rawPostsData),
        $vid
    ]);

    echo json_encode(['success' => true, 'message' => 'Social fetch matrix stored successfully.']);
    exit;

} catch (Exception $e) {
    $pdo->prepare("UPDATE `reports` SET `status` = 'failed' WHERE `vid` = ?")->execute([$vid]);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}