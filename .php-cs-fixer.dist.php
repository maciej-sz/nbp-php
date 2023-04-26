<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('bin')
    ->exclude('stubs')
    ->notName('*DEPRECATED_*')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'array_indentation' => true,
        'combine_consecutive_issets' => true,
        'concat_space' => ['spacing' => 'one'],
        'function_to_constant' => true,
        'implode_call' => true,
        'is_null' => true,
        'method_chaining_indentation' => true,
        'modernize_types_casting' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
        'no_superfluous_elseif' => true,
        'no_useless_else' => true,
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_separation' => false,
        'phpdoc_to_comment' => false,
        'phpdoc_summary' => false,
        'psr_autoloading' => true,
        'return_assignment' => true,
        'yoda_style' => false,
        'no_superfluous_phpdoc_tags' => true,
        'single_line_throw' => false,
    ])
    ->setFinder($finder)
;
