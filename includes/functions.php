<?php
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function formatDate($date, $format = 'd.m.Y') {
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return '';
    }
    return date($format, $timestamp);
}

function renderStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 10; $i++) {
        $stars .= $i <= $rating ? '★' : '☆';
    }
    return $stars;
}

function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}