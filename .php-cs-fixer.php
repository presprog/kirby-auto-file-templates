<?php declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
                           ->exclude('panel')
                           ->exclude('vendor')
                           ->in(__DIR__);

$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        '@PSR12' => true,
        'align_multiline_comment' => ['comment_type' => 'phpdocs_like'],
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'single_space', 'operators' => ['=>' => 'align_single_space_minimal', '=' => 'align_single_space_minimal']],
        'blank_line_after_opening_tag' => false,
        'cast_spaces' => ['space' => 'none'],
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'combine_nested_dirname' => true,
        'concat_space' => ['spacing' => 'one'],
        'declare_equal_normalize' => ['space' => 'none'],
        'declare_strict_types' => true,
        'dir_constant' => true,
        'include' => true,
        'logical_operators' => true,
        'lowercase_cast' => true,
        'lowercase_static_reference' => true,
        'magic_constant_casing' => true,
        'magic_method_casing' => true,
        'method_chaining_indentation' => true,
        'modernize_types_casting' => true,
        'multiline_comment_opening_closing' => true,
        'native_function_casing' => true,
        'native_type_declaration_casing' => true,
        'new_with_parentheses' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_leading_namespace_whitespace' => true,
        'no_mixed_echo_print' => ['use' => 'echo'],
        'no_unneeded_control_parentheses' => true,
        'no_unused_imports' => true,
        'no_useless_return' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'ordered_types' => ['sort_algorithm' => 'none', 'null_adjustment' => 'always_last'],
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_indent' => true,
        'phpdoc_scalar' => true,
        'phpdoc_trim' => true,
        'single_space_around_construct' => true,
        'short_scalar_cast' => true,
        'single_line_comment_style' => true,
        'single_quote' => true,
        'ternary_to_null_coalescing' => true,
        'whitespace_after_comma_in_array' => true
    ])
    ->setRiskyAllowed(true)
    ->setIndent("    ")
    ->setFinder($finder);
