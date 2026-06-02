<?php

/**
 * Point d'entrée pour déploiement à la racine (hébergement mutualisé / Laragon).
 * Redirige vers public/index.php tout en servant les assets statiques correctement.
 */

define('LARAVEL_START', microtime(true));

// Servir les fichiers statiques directement (assets Vite, images, etc.)
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Si le fichier existe dans public/, le servir directement
$publicFile = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    // Déterminer le Content-Type
    $ext = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    $mimeTypes = [
        'js'    => 'application/javascript',
        'css'   => 'text/css',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'json'  => 'application/json',
        'map'   => 'application/json',
    ];
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    readfile($publicFile);
    exit;
}

// Tout le reste passe par Laravel via public/index.php
require __DIR__ . '/public/index.php';
