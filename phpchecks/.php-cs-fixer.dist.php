<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/../src') // Specify the path to your PHP source code
    ->notPath('*/Tests/*') // Exclude test files
;

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
    'no_unused_imports' => true,
    'single_quote' => true,
    '@PHP80Migration:risky' => true,
])
    ->setFinder($finder);