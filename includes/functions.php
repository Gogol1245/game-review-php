<?php
/**
 * Helper functions for the application
 */

/**
 * Sanitize output for HTML display
 */
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format date to Slovak locale
 */
function formatDate(string $date, string $format = 'd.m.Y'): string {
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return '';
    }
    
    $months = [
        1 => 'január', 2 => 'február', 3 => 'marec',
        4 => 'apríl', 5 => 'máj', 6 => 'jún',
        7 => 'júl', 8 => 'august', 9 => 'september',
        10 => 'október', 11 => 'november', 12 => 'december'
    ];
    
    switch ($format) {
        case 'long':
            return date('j.', $timestamp) . ' ' . $months[(int)date('n', $timestamp)] . ' ' . date('Y', $timestamp);
        case 'datetime':
            return date('d.m.Y H:i', $timestamp);
        case 'd.m.Y':
        default:
            return date('d.m.Y', $timestamp);
    }
}

/**
 * Truncate text to specified length
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Generate star rating HTML
 */
function renderStars(int $rating): string {
    $stars = '';
    for ($i = 1; $i <= 10; $i++) {
        $stars .= $i <= $rating ? '★' : '☆';
    }
    return $stars;
}

/**
 * Validate URL
 */
function isValidUrl(string $url): bool {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Generate CSRF token
 */
function generateCsrfToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken(string $token): bool {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    unset($_SESSION['csrf_token']);
    return true;
}

/**
 * Redirect to URL
 */
function redirect(string $url): void {
    header("Location: {$url}");
    exit;
}

/**
 * Get current URL
 */
function currentUrl(): string {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
           "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
}

/**
 * Get base URL
 */
function baseUrl(): string {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
           "://{$_SERVER['HTTP_HOST']}";
}

/**
 * Check if request is AJAX
 */
function isAjaxRequest(): bool {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get flash message
 */
function getFlashMessage(): ?array {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Set flash message
 */
function setFlashMessage(string $type, string $message): void {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}