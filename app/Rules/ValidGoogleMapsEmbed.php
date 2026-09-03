<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use DOMDocument;
use DOMElement;
use DOMText;
use DOMComment;
use DOMXPath;

class ValidGoogleMapsEmbed implements ValidationRule
{
    /**
     * Allowed iframe attributes for styling, layout, and responsiveness.
     */
    private const ALLOWED_ATTRIBUTES = [
        'src',
        'width',
        'height',
        'style',
        'allowfullscreen',
        'loading',
        'referrerpolicy',
        'class',
        'id',
        'title',
        'frameborder'
    ];

    /**
     * Allowed HTML tag names in the parsed document structure.
     */
    private const ALLOWED_TAGS = [
        'html',
        'head',
        'meta',
        'body',
        'iframe'
    ];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('Format Google Maps Embed harus berupa teks.');
            return;
        }

        $trimmed = trim($value);

        // 1. Basic check for start and end tags to prevent simple bypasses
        if (!str_starts_with(strtolower($trimmed), '<iframe') || !str_ends_with(strtolower($trimmed), '</iframe>')) {
            $fail('Format Google Maps Embed harus diawali dengan <iframe dan diakhiri dengan </iframe>.');
            return;
        }

        $dom = new DOMDocument();
        $previousLibxmlState = libxml_use_internal_errors(true);

        try {
            // Wrap in standard HTML structural elements to ensure proper DOM parsing
            $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>' . $trimmed . '</body></html>';
            $loaded = $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            // Check for HTML parser warnings or errors (such as unclosed comments/tags)
            $errors = libxml_get_errors();
            libxml_clear_errors();

            if (!$loaded || count($errors) > 0) {
                $fail('Format HTML iframe tidak valid atau memiliki error parsing.');
                return;
            }

            // 2. Strict DOM Structure and Tag Validation
            if (!$this->validateDomStructure($dom, $fail)) {
                return;
            }

            $iframes = $dom->getElementsByTagName('iframe');
            if ($iframes->length !== 1) {
                $fail('Harus berisi tepat satu tag <iframe>.');
                return;
            }

            $iframe = $iframes->item(0);

            // 3. Prevent nested elements inside iframe
            if (!$this->validateIframeChildren($iframe, $fail)) {
                return;
            }

            // 4. Validate iframe attributes
            if (!$this->validateAttributes($iframe, $fail)) {
                return;
            }

            // 5. Validate the source URL
            $src = $iframe->getAttribute('src');
            if (!$src) {
                $fail('Iframe harus memiliki atribut src.');
                return;
            }

            if (!$this->validateUrl($src, $fail)) {
                return;
            }

        } finally {
            // Restore previous libxml error state to avoid global side effects
            libxml_use_internal_errors($previousLibxmlState);
        }
    }

    /**
     * Validate that only allowed elements, no HTML comments, and no text outside the iframe exist.
     */
    private function validateDomStructure(DOMDocument $dom, Closure $fail): bool
    {
        // Ensure no disallowed elements are present in the entire document
        $allElements = $dom->getElementsByTagName('*');
        foreach ($allElements as $element) {
            $tagName = strtolower($element->nodeName);
            if (!in_array($tagName, self::ALLOWED_TAGS, true)) {
                $fail("Tag HTML '{$element->nodeName}' tidak diperbolehkan.");
                return false;
            }
        }

        $xpath = new DOMXPath($dom);

        // Disallow any HTML comments to prevent potential obfuscation/bypass vectors
        $comments = $xpath->query('//comment()');
        if ($comments->length > 0) {
            $fail('Komentar HTML tidak diperbolehkan.');
            return false;
        }

        // Disallow any non-whitespace text directly outside of structural/iframe elements
        $bodyTexts = $xpath->query('//body/text()');
        foreach ($bodyTexts as $text) {
            if (trim($text->textContent) !== '') {
                $fail('Tidak diperbolehkan ada teks di luar tag iframe.');
                return false;
            }
        }

        return true;
    }

    /**
     * Ensure the iframe does not contain nested elements (e.g., fallback <script> tags).
     */
    private function validateIframeChildren(DOMElement $iframe, Closure $fail): bool
    {
        foreach ($iframe->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $fail('Tag iframe tidak boleh berisi element HTML lain.');
                return false;
            }
        }
        return true;
    }

    /**
     * Validate iframe attributes against an allowlist and block event handlers.
     */
    private function validateAttributes(DOMElement $iframe, Closure $fail): bool
    {
        foreach ($iframe->attributes as $attr) {
            $attrName = strtolower($attr->nodeName);

            if (!in_array($attrName, self::ALLOWED_ATTRIBUTES, true)) {
                $fail("Atribut '{$attr->nodeName}' tidak diperbolehkan pada iframe.");
                return false;
            }
        }

        return true;
    }

    /**
     * Validate the source URL scheme, host, and path structure.
     */
    private function validateUrl(string $src, Closure $fail): bool
    {
        $parsedUrl = parse_url($src);
        if (!$parsedUrl || !isset($parsedUrl['host'])) {
            $fail('Atribut src pada iframe bukan merupakan tautan URL yang valid.');
            return false;
        }

        if (!isset($parsedUrl['scheme']) || strtolower($parsedUrl['scheme']) !== 'https') {
            $fail('Tautan src iframe harus menggunakan protokol HTTPS.');
            return false;
        }

        $host = strtolower($parsedUrl['host']);
        if (!preg_match('/^(.*\.)?google\.(com|co\.id)$/', $host)) {
            $fail('Tautan src iframe harus mengarah ke domain Google resmi (google.com atau google.co.id).');
            return false;
        }

        $path = isset($parsedUrl['path']) ? strtolower($parsedUrl['path']) : '';
        if (!str_starts_with($path, '/maps/')) {
            $fail('Tautan src iframe harus mengarah ke path Google Maps (/maps/...).');
            return false;
        }

        return true;
    }
}
