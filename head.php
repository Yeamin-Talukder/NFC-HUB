<?php
// Shared <head> contents. Set $pageTitle before including.
$pageTitle = isset($pageTitle) ? $pageTitle . ' · NFC Hub' : 'NFC Hub';
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0a0a0b" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#fafafa" media="(prefers-color-scheme: light)">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="icon" href="assets/images/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
