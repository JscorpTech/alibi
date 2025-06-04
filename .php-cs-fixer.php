<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php')
    ->exclude('vendor')
    ->exclude('storage')
    ->exclude('bootstrap/cache')
    ->exclude('node_modules');

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR2' => true,
    'array_syntax' => ['syntax' => 'short'],
    'binary_operator_spaces' => [
        'default' => 'single_space',
        'operators' => ['=>' => 'align']
    ],
    'blank_line_after_namespace' => true,
    'blank_line_after_opening_tag' => true,
    'blank_line_before_statement' => [
        'statements' => ['return']
    ],
    'braces' => [
        'allow_single_line_closure' => true,
    ],
    'cast_spaces' => true,
    'class_attributes_separation' => [
        'elements' => ['method' => 'one']
    ],
    'concat_space' => [
        'spacing' => 'one'
    ],
    'declare_equal_normalize' => true,
    'function_declaration' => true,
    'include' => true,
    'lowercase_cast' => true,
    'no_extra_blank_lines' => [
        'tokens' => [
            'extra',
            'throw',
            'use',
            'use_trait',
            'curly_brace_block'
        ]
    ],
    'no_spaces_after_function_name' => true,
    'no_spaces_around_offset' => true,
    'no_whitespace_in_blank_line' => true,
    'ordered_imports' => ['sort_algorithm' => 'alpha'],
    'single_quote' => false,
    'ternary_operator_spaces' => true,
    'trailing_comma_in_multiline' => ['elements' => ['arrays']],
    'trim_array_spaces' => true,
    'unary_operator_spaces' => true,
])
    ->setFinder($finder);