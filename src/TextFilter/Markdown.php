<?php

namespace Jovis\TextFilter;

class CMarkdown
{
    /**
     * Format text according to Markdown syntax.
     *
     * @param string $text the text that should be formatted.
     *
     * @return string as the formatted html-text.
     *
     * @link http://dbwebb.se/coachen/skriv-for-webben-med-markdown-och-formattera-till-html-med-php
     */
    public function markdown($text)
    {
        return \Michelf\MarkdownExtra::defaultTransform($text);
    }
