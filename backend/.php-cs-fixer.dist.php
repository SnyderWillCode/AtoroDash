<?php
require(__DIR__."/storage/packages/autoload.php");

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$header = <<<'HEADER'
This file is part of MythicalClient.
Please view the LICENSE file that was distributed with this source code.

# MythicalSystems License v2.0

## Copyright (c) 2021–2025 MythicalSystems and Cassian Gherman

Breaking any of the following rules will result in a permanent ban from the MythicalSystems community and all of its services.
HEADER;




$finder = (new Finder())
    ->in(__DIR__)
    ->exclude([
        'vendor',
        'node_modules',
        '.github',
        '.vscode',
        '.git',
        'docs',
        'caches',
        'logs',
        'storage',
    ])
    ->notName(['_ide_helper*']);


return (new Config())
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        '@Symfony' => true,
        '@PSR1' => true,
        '@PSR2' => true,
        '@PSR12' => true,
        'align_multiline_comment' => ['comment_type' => 'phpdocs_like'],
        'combine_consecutive_unsets' => true,
        'concat_space' => ['spacing' => 'one'],
        'heredoc_to_nowdoc' => true,
        'no_alias_functions' => true,
        'no_unreachable_default_argument_value' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'length',
        ],
        'phpdoc_align' => [
            'align' => 'left',
            'tags' => [
                'param',
                'property',
                'return',
                'throws',
                'type',
                'var',
            ],
        ],
        'header_comment' => ['header' => $header],
        'phpdoc_order' => true,
        'phpdoc_summary' => true,
        'phpdoc_to_comment' => false,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_no_package' => true,
        'phpdoc_separation' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'none',
        ],
        'phpdoc_var_annotation_correct_order' => true,
        'single_line_throw' => false,
        'single_line_comment_style' => false,
        'random_api_migration' => true,
        'ternary_to_null_coalescing' => true,
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
        'ordered_class_elements' => true,
        'no_useless_else' => true,
        'no_extra_blank_lines' => true,
        'logical_operators' => true,
        'no_unused_imports' => true
]);