<?php

require dirname(__FILE__) . '\php-cs-fixer\Fixer\Whitespace\AddSpacesInsideParenthesisFixer.php';

return PhpCsFixer\Config::create()
    ->registerCustomFixers([
        new Hogash\Fixer\Whitespace\AddSpacesInsideParenthesisFixer()
    ])
    ->setRules(array(
		'array_syntax' => array('syntax' => 'short'),
        'no_spaces_inside_parenthesis' => false,
        'no_spaces_after_function_name' => true,
        'no_whitespace_in_blank_line' => true,
        'no_trailing_whitespace' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_whitespace_before_comma_in_array' => true,
        'space_after_semicolon' => [
            'remove_in_empty_for_expressions' => true
        ],
        'not_operator_with_space' => true,
        'object_operator_without_whitespace' => true,
        'indentation_type' => false,
        'concat_space' => [
            'spacing' => 'one'
        ],
        'no_closing_tag' => true,
        'array_indentation' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'trailing_comma_in_multiline_array' => true,
        'whitespace_after_comma_in_array' => true,
        'align_multiline_comment' => true,
        'blank_line_after_opening_tag' => true,
		'single_quote' => true,
		'binary_operator_spaces' => [
			'align_double_arrow' => true,
			'align_equals' => true
		],
        'no_spaces_around_offset' => [
            'positions' => [
                'inside'
            ]
        ],
        'method_argument_space' => [
			'ensure_fully_multiline' => true,
			'keep_multiple_spaces_after_comma' => false,
			'after_heredoc' => false,
			'on_multiline' => 'ensure_fully_multiline'
		],
        'braces' => [
            'allow_single_line_closure' => false,
            'position_after_functions_and_oop_constructs' => 'same'
        ],
        'line_ending' => true,
        'linebreak_after_opening_tag' => true,
        'single_blank_line_at_eof' => true,
        'single_class_element_per_statement' => true,
        'full_opening_tag' => true,
        // PHP DOC
        'doctrine_annotation_spaces' => [
            'after_argument_assignments' => true,
            'after_array_assignments_colon' => true,
            'after_array_assignments_equals' => true,
            'around_argument_assignments' => true,
            'around_array_assignments' => true,
            'around_commas' => true,
            'around_parentheses' => true,
            'before_argument_assignments' => true,
            'before_array_assignments_colon' => true,
            'before_array_assignments_equals' => true
        ],
        'doctrine_annotation_indentation' => true,
        'doctrine_annotation_array_assignment' => [
            'operator' => ':',
        ],
        'align_multiline_comment' => [
            'comment_type' => 'phpdocs_like'
        ],
        'no_blank_lines_after_phpdoc' => true,
        'no_trailing_whitespace_in_comment' => true,
        'phpdoc_add_missing_param_annotation' => [
            'only_untyped' => false
        ],
        'phpdoc_align' => [
            'align' => 'vertical'
        ],
        'phpdoc_annotation_without_dot' => true, // todo
        'phpdoc_indent' => true,
        'phpdoc_no_alias_tag' => true,
        'phpdoc_order' => true,
        'phpdoc_separation' => true,
        'phpdoc_to_comment' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
        'Hogash/add_spaces_inside_parenthesis' => true,
    ))
    ->setIndent("\t")
    ->setLineEnding("\n");