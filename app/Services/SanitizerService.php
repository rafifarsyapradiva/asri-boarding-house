<?php

namespace App\Services;

use DOMDocument;

class SanitizerService
{
    /**
     * Sanitasi tag iframe Google Maps agar aman dan responsif.
     */
    public static function sanitizeGoogleMapsEmbed(string $value): string
    {
        $trimmed = trim($value);
        if (empty($trimmed)) {
            return '';
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $trimmed, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $iframes = $dom->getElementsByTagName('iframe');
        if ($iframes->length === 1) {
            $iframe = $iframes->item(0);
            $iframe->setAttribute('width', '100%');
            $iframe->setAttribute('height', '100%');
            $iframe->setAttribute('loading', 'lazy');
            $iframe->setAttribute('style', 'border:0;');
            $iframe->setAttribute('class', 'w-full h-full');
            
            $allowed = ['src', 'width', 'height', 'style', 'class', 'allowfullscreen', 'loading', 'referrerpolicy', 'title'];
            $attrsToRemove = [];
            foreach ($iframe->attributes as $attr) {
                if (!in_array(strtolower($attr->nodeName), $allowed, true)) {
                    $attrsToRemove[] = $attr->nodeName;
                }
            }
            foreach ($attrsToRemove as $attrName) {
                $iframe->removeAttribute($attrName);
            }

            return $dom->saveHTML($iframe);
        }

        return $value;
    }
}
