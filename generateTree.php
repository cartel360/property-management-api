<?php

// List of common files and folders we want to exclude
$excludeDirs = [
    'vendor',
    'node_modules',
    '.git',
    'storage',
    'public/storage',
    'database/cache',
    'tests',
    '.idea',
    '.vscode',
    'docker',
    'build',
    'node_modules',
    'bower_components',
    'Modules',
    'stubs'
];

// Function to generate a folder-only tree structure for Laravel project
function generateLaravelFolderTree($dir, $prefix = '')
{
    global $excludeDirs;

    // Open the directory
    $files = scandir($dir);
    foreach ($files as $index => $file) {
        // Skip current and parent directory
        if ($file == '.' || $file == '..') continue;

        $path = $dir . DIRECTORY_SEPARATOR . $file;

        // Skip directories that are in the exclude list
        if (in_array($file, $excludeDirs) || !is_dir($path)) continue;

        // Print the folder
        echo $prefix . "├── " . $file . "/\n";

        // Recursively process subdirectories
        generateLaravelFolderTree($path, $prefix . "│   ");
    }
}

// Set the base directory path (adjust this to your project root)
$baseDir = __DIR__; // Current directory, or specify the root directory of your Laravel project

// Generate tree structure from base directory
echo $baseDir . "/\n";
generateLaravelFolderTree($baseDir, "");
