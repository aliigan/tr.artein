<?php
declare(strict_types=1);

// Lightweight updates JSON endpoint
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

try {
    require_once __DIR__ . '/../admin/config/config.php';

    $today = date('Y-m-d');

    // Basic versions based on latest timestamps or counts
    $latestProject = $database->fetchOne("SELECT UNIX_TIMESTAMP(MAX(GREATEST(IFNULL(updated_at, '1970-01-01'), IFNULL(created_at, '1970-01-01')))) AS ts FROM projects")['ts'] ?? 0;
    $latestService = $database->fetchOne("SELECT UNIX_TIMESTAMP(MAX(GREATEST(IFNULL(updated_at, '1970-01-01'), IFNULL(created_at, '1970-01-01')))) AS ts FROM services")['ts'] ?? 0;
    $latestSlider  = $database->fetchOne("SELECT UNIX_TIMESTAMP(MAX(GREATEST(IFNULL(updated_at, '1970-01-01'), IFNULL(created_at, '1970-01-01')))) AS ts FROM sliders")['ts'] ?? 0;
    $latestMedia   = $database->fetchOne("SELECT UNIX_TIMESTAMP(MAX(GREATEST(IFNULL(updated_at, '1970-01-01'), IFNULL(created_at, '1970-01-01')))) AS ts FROM media_files")['ts'] ?? 0;
    $latestAbout   = $database->fetchOne("SELECT UNIX_TIMESTAMP(MAX(GREATEST(IFNULL(updated_at, '1970-01-01'), IFNULL(created_at, '1970-01-01')))) AS ts FROM about_content")['ts'] ?? 0;
    $latestManifesto = $database->fetchOne("SELECT UNIX_TIMESTAMP(MAX(GREATEST(IFNULL(updated_at, '1970-01-01'), IFNULL(created_at, '1970-01-01')))) AS ts FROM manifesto_content")['ts'] ?? 0;
    $latestMsg     = $database->fetchOne("SELECT UNIX_TIMESTAMP(MAX(GREATEST(IFNULL(updated_at, '1970-01-01'), IFNULL(created_at, '1970-01-01')))) AS ts FROM contact_messages")['ts'] ?? 0;

    $unreadMessages = (int)($database->fetchOne("SELECT COUNT(*) AS c FROM contact_messages WHERE is_read = 0")['c'] ?? 0);
    $totalMessages  = (int)($database->fetchOne("SELECT COUNT(*) AS c FROM contact_messages")['c'] ?? 0);

    $todayStats = $database->fetchOne("SELECT page_views, unique_visitors, contact_forms FROM site_stats WHERE stat_date = ?", [$today]) ?? [
        'page_views' => 0,
        'unique_visitors' => 0,
        'contact_forms' => 0
    ];

    $version = max($latestProject, $latestService, $latestSlider, $latestMedia, $latestAbout, $latestManifesto, $latestMsg);

    echo json_encode([
        'success' => true,
        'version' => (int)$version,
        'counts' => [
            'unread_messages' => $unreadMessages,
            'total_messages' => $totalMessages,
            'today' => $todayStats,
        ],
        'timestamps' => [
            'projects' => (int)$latestProject,
            'services' => (int)$latestService,
            'sliders' => (int)$latestSlider,
            'media' => (int)$latestMedia,
            'about' => (int)$latestAbout,
            'manifesto' => (int)$latestManifesto,
            'messages' => (int)$latestMsg,
        ]
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'updates_failed']);
}


