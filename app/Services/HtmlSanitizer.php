<?php

namespace App\Services;

class HtmlSanitizer
{
    /**
     * List of allowed HTML tags
     */
    private static array $allowedTags = [
        'p', 'br', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 
        'strong', 'em', 'u', 'span', 'div', 
        'table', 'thead', 'tbody', 'tr', 'th', 'td', 
        'img', 'ul', 'ol', 'li'
    ];

    /**
     * List of allowed attributes per tag
     */
    private static array $allowedAttributes = [
        '*'     => ['style', 'class'],
        'img'   => ['src', 'alt', 'width', 'height'],
        'table' => ['cellpadding', 'cellspacing', 'border', 'width'],
        'td'    => ['colspan', 'rowspan', 'width', 'align', 'valign'],
        'th'    => ['colspan', 'rowspan', 'width', 'align', 'valign'],
    ];

    /**
     * Sanitize HTML content
     */
    public static function clean(?string $html): string
    {
        if (empty($html)) return '';

        $dom = new \DOMDocument();
        
        // Supress warnings for malformed HTML
        libxml_use_internal_errors(true);
        
        // Load HTML with UTF-8
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        libxml_clear_errors();

        self::sanitizeNode($dom);

        $cleanHtml = $dom->saveHTML();
        
        // Remove the xml encoding header
        $cleanHtml = str_replace('<?xml encoding="utf-8" ?>', '', $cleanHtml);

        return $cleanHtml;
    }

    /**
     * Recursively sanitize DOM nodes
     */
    private static function sanitizeNode(\DOMNode $node): void
    {
        if ($node->hasChildNodes()) {
            $children = [];
            foreach ($node->childNodes as $child) {
                $children[] = $child;
            }
            foreach ($children as $child) {
                self::sanitizeNode($child);
            }
        }

        if ($node->nodeType === XML_ELEMENT_NODE) {
            $tagName = strtolower($node->nodeName);

            // 1. Remove disallowed tags
            if (!in_array($tagName, self::$allowedTags)) {
                $fragment = $node->ownerDocument->createDocumentFragment();
                while ($node->childNodes->length > 0) {
                    $fragment->appendChild($node->childNodes->item(0));
                }
                $node->parentNode->replaceChild($fragment, $node);
                return;
            }

            // 2. Remove disallowed attributes
            $allowedAttrs = array_merge(
                self::$allowedAttributes['*'] ?? [],
                self::$allowedAttributes[$tagName] ?? []
            );

            $attrsToRemove = [];
            foreach ($node->attributes as $attr) {
                $attrName = strtolower($attr->nodeName);
                
                // Block 'on*' attributes (event handlers)
                if (str_starts_with($attrName, 'on')) {
                    $attrsToRemove[] = $attrName;
                    continue;
                }

                if (!in_array($attrName, $allowedAttrs)) {
                    $attrsToRemove[] = $attrName;
                    continue;
                }

                // 3. Sanitize attribute values (especially 'style' and 'src')
                $attrValue = $attr->nodeValue;
                
                if ($attrName === 'src' || $attrName === 'href') {
                    // Only allow data: URIs (for images) or relative/safe absolute paths
                    if (str_starts_with(strtolower(trim($attrValue)), 'javascript:')) {
                        $attrsToRemove[] = $attrName;
                    }
                }

                if ($attrName === 'style') {
                    // Block dangerous CSS properties
                    if (preg_match('/(expression|javascript|behaviour|vml|script)/i', $attrValue)) {
                        $attrsToRemove[] = $attrName;
                    }
                }
            }

            foreach ($attrsToRemove as $attrName) {
                $node->removeAttribute($attrName);
            }
        }
    }
}
