<?php
/**
 * Simple PSR-4-like autoloader
 */
spl_autoload_register(function ($class) {
    // Base directory for classes
    $baseDir = __DIR__ . '/../classes/';
    
    // Build the file path
    $file = $baseDir . $class . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    
    return false;
});