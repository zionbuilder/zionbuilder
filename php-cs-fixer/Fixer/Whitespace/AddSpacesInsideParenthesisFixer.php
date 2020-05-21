<?php

/*
 * This file is part of Hogash custom fixers for PHP CS.
 *
 * (c) Hogash <hello@hogash.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hogash\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\Token;

/**
 * Fixer that will resolve WordPress rule for having 1 space before opening and closing parenthesis.
 *
 * @author Hogash <hello@hogash.com>
 */
final class AddSpacesInsideParenthesisFixer extends AbstractFixer {
	public function getName() {
		return 'Hogash/add_spaces_inside_parenthesis';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDefinition() {
		return new FixerDefinition(
			'There MUST NOT be a space after the opening parenthesis. There MUST NOT be a space before the closing parenthesis.',
			[
				new CodeSample( "<?php\nif (\$a) {\n foo();\n}\n" ),
				new CodeSample(
					"<?php
function foo(\$bar, \$baz)
{
}\n"
				),
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPriority() {
		// must run before FunctionToConstantFixer
		return 2;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isCandidate( Tokens $tokens ) {
		return $tokens->isTokenKindFound( '(' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function applyFix( \SplFileInfo $file, Tokens $tokens ) {
		foreach ( $tokens as $index => $token ) {
			if ( ! $token->equals( '(' ) ) {
				continue;
			}
			$prevIndex = $tokens->getPrevMeaningfulToken( $index );
			// ignore parenthesis for T_ARRAY
			if ( null !== $prevIndex && $tokens[$prevIndex]->isGivenKind( T_ARRAY ) ) {
				continue;
			}
			$endIndex = $tokens->findBlockEnd( Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index );

			// Check for empty comment block
			$is_single_block = $tokens[$index + 1] !== $tokens[$endIndex];

			// add space after opening `(`
			if ( ! $tokens[$tokens->getNextNonWhitespace( $index )]->isComment() && ! $tokens[$index + 1]->isWhitespace() && $is_single_block ) {
				$this->addSpaceAroundToken( $tokens, $index + 1 );
			}
			// add space before closing `)` if it is not `list($a, $b, )` case
			if ( ! $tokens[$tokens->getPrevMeaningfulToken( $endIndex )]->equals( ',' ) && ! $tokens[$endIndex - 1]->isWhitespace() && $is_single_block ) {
				$this->addSpaceAroundToken( $tokens, $endIndex );
			}
		}
	}

	/**
	 * Add spaces from token at a given index.
	 *
	 * @param Tokens $tokens
	 * @param int    $index
	 */
	private function addSpaceAroundToken( Tokens $tokens, $index ) {
		$token = $tokens[$index];

		if ( ! $token->isWhitespace() && false === strpos( $token->getContent(), "\n" ) ) {
			$tokens->insertAt( $index, new Token( [ T_WHITESPACE, ' ' ] ) );
		}
	}
}
