<?php

$header = <<<EOF
/*
 * This file is part of the Smoke Project.
 *
 * (c) Nils Langner <spamfilter.nils.langner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('vendor')
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    //->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([
        // 'short_array_syntax',
        'ordered_use',
        'strict',
        'strict_param',
        'phpdoc_order',
        'newline_after_open_tag',
        'multiline_spaces_before_semicolon',
        //'header_comment',
        'ereg_to_preg',
        'concat_with_spaces',
        //'align_equals',
        'align_double_arrow',
        'eof_ending'
    ])
    ->finder($finder)
;
