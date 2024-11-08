<?php

$basePath = __DIR__;
$buildDirectory = $basePath . '/build';
$pharPath = $buildDirectory . '/PrisonCore.phar';

if (file_exists($pharPath)) {
    echo 'Phar file already exists, overwriting...' . PHP_EOL;

    try {
        \Phar::unlinkArchive($pharPath);
    } catch (\PharException $e) {
        unlink($pharPath);
    }
}

echo 'Adding files...'  . PHP_EOL;

$start = microtime(true);
$phar = new \Phar($pharPath);

$phar->setSignatureAlgorithm(\Phar::SHA1);
$phar->startBuffering();

$regex = sprintf(
    '/^(?!.*(%s|\.git|\.gitea|phpchecks))(?=.*\.(php|ya?ml)$).*$/i',
    preg_quote($basePath . '/build', '/')
);

echo sprintf('Regex used for inclusion/exclusion: %s\n', $regex)  . PHP_EOL;

$directory = new \RecursiveDirectoryIterator($basePath, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS | \FilesystemIterator::CURRENT_AS_PATHNAME);
$iterator = new \RecursiveIteratorIterator($directory);

$regexIterator = new \RegexIterator($iterator, $regex);

foreach ($regexIterator as $file) {
    echo sprintf('Found file: %s\n', $file) . PHP_EOL;
}

$count = count($phar->buildFromIterator($regexIterator, $basePath));
echo sprintf('Added %d files', $count) . PHP_EOL;

$phar->stopBuffering();

echo sprintf('Done in %.3fs\n', microtime(true) - $start) . PHP_EOL;