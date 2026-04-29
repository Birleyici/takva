<?php

namespace App\Services;

use Illuminate\Support\Str;

class ArticleContentNormalizer
{
    /**
     * Normalize stored article HTML so the management editor preserves spacing and references.
     */
    public function normalize(?string $content): ?string
    {
        if ($content === null) {
            return null;
        }

        $normalized = $this->standardizeBreaks($content);
        $normalized = $this->normalizeFootnoteBlocks($normalized);
        $normalized = $this->normalizeFootnoteReferences($normalized);
        $normalized = $this->unwrapGenericDivs($normalized);
        $normalized = $this->trimRedundantBreaks($normalized);

        if ($this->hasRegularParagraph($normalized)) {
            $normalized = $this->tidyExistingParagraphs($normalized);
        } else {
            $normalized = $this->convertBreaksToParagraphs($normalized);
        }

        return $this->tidyHtmlStructure($normalized);
    }

    private function standardizeBreaks(string $content): string
    {
        $normalized = preg_replace('/<br\s*\/?>/i', '<br />', $content);

        return is_string($normalized) ? $normalized : $content;
    }

    private function normalizeFootnoteBlocks(string $content): string
    {
        $pattern = '/<div([^>]*)>(.*?)(?:<\/div>|<\/p>)/is';

        return (string) preg_replace_callback($pattern, function ($matches) {
            $attributes = $matches[1];

            if (!preg_match('/\bid\s*=\s*(["\'])([^"\']*ftn[^"\']*)\1/i', $attributes, $idMatch)) {
                return $matches[0];
            }

            $id = trim($idMatch[2]);
            $number = $this->extractFootnoteNumber($id);
            $body = $this->cleanFootnoteText($matches[2]);

            if ($body === '') {
                return '';
            }

            $referenceKey = $number !== null
                ? $number
                : (Str::slug($id) ?: 'footnote');
            $referenceId = 'ftnref'.$referenceKey;
            $labelText = '['.($number ?? $id).']';
            $labelLink = sprintf(
                '<a href="#%s">%s</a>',
                htmlspecialchars($referenceId, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($labelText, ENT_QUOTES, 'UTF-8')
            );

            return sprintf(
                '<p id="%s" class="footnote-block">%s %s</p>',
                htmlspecialchars($id, ENT_QUOTES, 'UTF-8'),
                $labelLink,
                $body
            );
        }, $content);
    }

    private function normalizeFootnoteReferences(string $content): string
    {
        $pattern = '/<a[^>]*href\s*=\s*(["\'])#_?ftn(\d+)\1[^>]*>(.*?)<\/a>/i';

        return (string) preg_replace_callback($pattern, function ($matches) {
            $number = $matches[2];
            $target = 'ftn'.$number;
            $referenceId = 'ftnref'.$number;
            $label = '['.$number.']';

            return sprintf(
                '<a id="%s" href="#%s">%s</a>',
                htmlspecialchars($referenceId, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($target, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
            );
        }, $content);
    }

    private function unwrapGenericDivs(string $content): string
    {
        $replaced = preg_replace('/<div(?![^>]*ftn)[^>]*>/i', '<br />', $content);
        $replaced = is_string($replaced) ? $replaced : $content;
        $replaced = preg_replace('/<\/div>/i', '<br />', $replaced);

        return is_string($replaced) ? $replaced : $content;
    }

    private function trimRedundantBreaks(string $content): string
    {
        $trimmed = preg_replace('/^(?:\s|<br\s*\/?>|&nbsp;|&#160;)+/i', '', $content);
        $trimmed = is_string($trimmed) ? $trimmed : $content;
        $trimmed = preg_replace('/(?:\s|<br\s*\/?>|&nbsp;|&#160;)+$/i', '', $trimmed);

        return is_string($trimmed) ? $trimmed : $content;
    }

    private function hasRegularParagraph(string $content): bool
    {
        return (bool) preg_match('/<p(?![^>]*footnote-block)[^>]*>/i', $content);
    }

    private function tidyExistingParagraphs(string $content): string
    {
        $cleaned = preg_replace('/<p([^>]*)>\s+/i', '<p$1>', $content);
        $cleaned = is_string($cleaned) ? $cleaned : $content;
        $cleaned = preg_replace('/\s+<\/p>/i', '</p>', $cleaned);

        return is_string($cleaned) ? $cleaned : $content;
    }

    private function convertBreaksToParagraphs(string $content): string
    {
        $trimmed = $this->trimRedundantBreaks($content);

        if ($trimmed === '') {
            return $content;
        }

        $blockPattern = '/(<(?:ul|ol|table|thead|tbody|tr|td|th|tfoot|blockquote|section|article|figure|h[1-6])[^>]*>.*?<\/(?:ul|ol|table|thead|tbody|tr|td|th|tfoot|blockquote|section|article|figure|h[1-6])>|<hr\s*\/?>)/is';
        $segments = preg_split($blockPattern, $trimmed, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        if (!$segments) {
            return '<p>'.$trimmed.'</p>';
        }

        $result = '';

        foreach ($segments as $segment) {
            if (preg_match($blockPattern, $segment)) {
                $result .= $segment;
                continue;
            }

            $result .= $this->wrapInlineSegment($segment);
        }

        return $result !== '' ? $result : $content;
    }

    private function wrapInlineSegment(string $segment): string
    {
        $segment = $this->trimRedundantBreaks($segment);

        if ($segment === '') {
            return '';
        }

        $paragraphBreakPattern = '/(?:\s|&nbsp;|&#160;)*(?:<br\s*\/?>(?:\s|&nbsp;|&#160;)*){2,}/i';
        $parts = preg_split($paragraphBreakPattern, $segment, -1, PREG_SPLIT_NO_EMPTY);

        if (!$parts) {
            return '<p>'.$segment.'</p>';
        }

        $paragraphs = [];

        foreach ($parts as $part) {
            $part = $this->trimRedundantBreaks($part);

            if ($part === '') {
                continue;
            }

            $paragraphs[] = '<p>'.$part.'</p>';
        }

        return implode('', $paragraphs);
    }

    private function cleanFootnoteText(string $text): string
    {
        $text = preg_replace('/<\/?span[^>]*>/i', '', $text);
        $text = preg_replace('/<a[^>]*>(.*?)<\/a>/i', '$1', $text);
        $text = preg_replace('/<br\s*\/?>/i', ' ', $text);
        $text = str_replace(['&nbsp;', '&#160;'], ' ', $text);
        $text = preg_replace('/^\s*\[[^\]]+\]\s*/', '', $text);

        return trim($text);
    }

    private function extractFootnoteNumber(string $value): ?string
    {
        if (preg_match('/(\d+)/', $value, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function tidyHtmlStructure(string $content): string
    {
        $wrapped = '<div>'.$content.'</div>';
        $dom = new \DOMDocument('1.0', 'UTF-8');

        libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML('<?xml encoding="UTF-8">'.$wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        if (!$loaded) {
            return $content;
        }

        $root = $dom->documentElement;

        if (!$root || $root->tagName !== 'div') {
            return $content;
        }

        $xpath = new \DOMXPath($dom);
        $footnoteDivs = $xpath->query('//div[contains(translate(@id, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "ftn")]');

        foreach ($footnoteDivs ?? [] as $div) {
            /** @var \DOMElement $div */
            $paragraph = $dom->createElement('p');

            if ($div->hasAttribute('id')) {
                $paragraph->setAttribute('id', $div->getAttribute('id'));
            }

            $existingClass = trim($div->getAttribute('class'));
            $classes = array_filter(array_map('trim', explode(' ', $existingClass)));
            $classes[] = 'footnote-block';
            $paragraph->setAttribute('class', implode(' ', array_unique($classes)));

            while ($div->firstChild) {
                $paragraph->appendChild($div->firstChild);
            }

            $div->parentNode?->replaceChild($paragraph, $div);
        }

        $footnoteParagraphs = $xpath->query('//*[self::p or self::div][contains(translate(@id, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "ftn") and not(contains(translate(@id, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "ftnref"))]');

        foreach ($footnoteParagraphs ?? [] as $node) {
            /** @var \DOMElement $node */
            $idValue = $node->getAttribute('id');
            if ($idValue === '') {
                continue;
            }

            $classes = array_filter(array_map('trim', explode(' ', (string) $node->getAttribute('class'))));
            if (!in_array('footnote-block', $classes, true)) {
                $classes[] = 'footnote-block';
                $node->setAttribute('class', implode(' ', $classes));
            }

            if (!preg_match('/ftn(\d+)/i', $idValue, $matches)) {
                $referenceKey = Str::slug($idValue) ?: 'footnote';
                $labelValue = $idValue;
            } else {
                $referenceKey = $matches[1];
                $labelValue = $matches[1];
            }

            $referenceId = 'ftnref'.$referenceKey;
            $labelText = '['.$labelValue.']';

            $firstElement = $node->firstChild;
            while ($firstElement && $firstElement->nodeType === XML_TEXT_NODE && trim($firstElement->nodeValue) === '') {
                $firstElement = $firstElement->nextSibling;
            }

            if ($firstElement && $firstElement->nodeType === XML_ELEMENT_NODE && $firstElement->nodeName === 'a') {
                if ($firstElement->getAttribute('href') === '#'.$referenceId) {
                    continue;
                }
                $node->removeChild($firstElement);
            }

            $anchor = $dom->createElement('a', $labelText);
            $anchor->setAttribute('href', '#'.$referenceId);
            $node->insertBefore($anchor, $node->firstChild);
            $node->insertBefore($dom->createTextNode(' '), $anchor->nextSibling);

            $legacyAnchors = $xpath->query('.//a[contains(@href, "_ftnref")]', $node);
            foreach ($legacyAnchors ?? [] as $legacyAnchor) {
                $legacyAnchor->parentNode?->removeChild($legacyAnchor);
            }
        }

        $this->linkBracketReferences($dom, $xpath);

        $html = '';
        foreach ($root->childNodes as $child) {
            $html .= $dom->saveHTML($child);
        }

        return $html ?: $content;
    }

    private function linkBracketReferences(\DOMDocument $dom, \DOMXPath $xpath): void
    {
        $candidateNumbers = $this->extractCandidateReferenceNumbers($xpath);
        if ($candidateNumbers === []) {
            return;
        }

        $sourceTargets = $this->collectSourceTargets($xpath, $candidateNumbers);
        if ($sourceTargets === []) {
            return;
        }

        $this->retargetLegacyReferenceAnchors($xpath, $sourceTargets);
        $citationTargets = $this->linkInlineBracketReferences($dom, $xpath, $sourceTargets);
        $this->appendBackLinksToSources($dom, $sourceTargets, $citationTargets);
    }

    /**
     * @return array<string, true>
     */
    private function extractCandidateReferenceNumbers(\DOMXPath $xpath): array
    {
        $numbers = [];
        $textNodes = $xpath->query('//text()[normalize-space(.) != ""]');

        if (!$textNodes) {
            return $numbers;
        }

        foreach ($textNodes as $textNode) {
            if (!$textNode instanceof \DOMText) {
                continue;
            }

            if ($this->hasAncestorTag($textNode, ['a', 'script', 'style'])) {
                continue;
            }

            if (!preg_match_all('/\[(\d{1,3})\]/', $textNode->nodeValue ?? '', $matches)) {
                continue;
            }

            foreach ($matches[1] as $number) {
                $numbers[$number] = true;
            }
        }

        return $numbers;
    }

    /**
     * @param array<string, true> $candidateNumbers
     * @return array<string, array{id: string, node: \DOMElement}>
     */
    private function collectSourceTargets(\DOMXPath $xpath, array $candidateNumbers): array
    {
        $targets = [];
        $elements = $xpath->query('//*[self::p or self::li or self::div]');

        if (!$elements) {
            return $targets;
        }

        foreach ($elements as $element) {
            if (!$element instanceof \DOMElement) {
                continue;
            }

            $referenceNumber = $this->extractSourceReferenceNumber($element);
            if ($referenceNumber === null || !isset($candidateNumbers[$referenceNumber])) {
                continue;
            }

            if (isset($targets[$referenceNumber])) {
                continue;
            }

            $id = trim($element->getAttribute('id'));
            if ($id === '') {
                $id = 'source-ref-'.$referenceNumber;
                $suffix = 2;

                while ($this->elementIdExists($xpath, $id)) {
                    $id = 'source-ref-'.$referenceNumber.'-'.$suffix;
                    $suffix++;
                }

                $element->setAttribute('id', $id);
            }

            $targets[$referenceNumber] = [
                'id' => $id,
                'node' => $element,
            ];
        }

        return $targets;
    }

    private function extractSourceReferenceNumber(\DOMElement $element): ?string
    {
        $id = trim($element->getAttribute('id'));
        if ($id !== '' && preg_match('/(?:^|[^a-z])ftn(\d+)/i', $id, $matches)) {
            return $matches[1];
        }

        $text = trim($element->textContent ?? '');
        if ($text === '') {
            return null;
        }

        if (preg_match('/^\[(\d{1,3})\]/', $text, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @param array<string, array{id: string, node: \DOMElement}> $sourceTargets
     */
    private function retargetLegacyReferenceAnchors(\DOMXPath $xpath, array $sourceTargets): void
    {
        $anchors = $xpath->query('//a[@id and starts-with(@id, "ftnref")]');

        if (!$anchors) {
            return;
        }

        foreach ($anchors as $anchor) {
            if (!$anchor instanceof \DOMElement) {
                continue;
            }

            $id = $anchor->getAttribute('id');
            if (!preg_match('/ftnref(\d+)/i', $id, $matches)) {
                continue;
            }

            $number = $matches[1];
            if (!isset($sourceTargets[$number])) {
                continue;
            }

            $anchor->setAttribute('href', '#'.$sourceTargets[$number]['id']);
            $this->appendClass($anchor, 'citation-ref');
        }
    }

    /**
     * @param array<string, array{id: string, node: \DOMElement}> $sourceTargets
     * @return array<string, string>
     */
    private function linkInlineBracketReferences(
        \DOMDocument $dom,
        \DOMXPath $xpath,
        array $sourceTargets
    ): array {
        $citationTargets = [];
        $citationCounts = [];
        $sourceNodeIds = [];

        foreach ($sourceTargets as $target) {
            $sourceNodeIds[spl_object_id($target['node'])] = true;
        }

        $nodeList = $xpath->query('//text()[normalize-space(.) != ""]');
        if (!$nodeList) {
            return $citationTargets;
        }

        $textNodes = [];
        foreach ($nodeList as $node) {
            if ($node instanceof \DOMText) {
                $textNodes[] = $node;
            }
        }

        foreach ($textNodes as $textNode) {
            if ($this->hasAncestorTag($textNode, ['a', 'script', 'style'])) {
                continue;
            }

            if ($this->hasAncestorInSet($textNode, $sourceNodeIds)) {
                continue;
            }

            $text = $textNode->nodeValue ?? '';
            if ($text === '' || !preg_match_all('/\[(\d{1,3})\]/', $text, $matches, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            $fragment = $dom->createDocumentFragment();
            $cursor = 0;
            $changed = false;

            foreach ($matches[0] as $index => $fullMatch) {
                $fullText = $fullMatch[0];
                $offset = $fullMatch[1];
                $number = $matches[1][$index][0];

                if (!isset($sourceTargets[$number])) {
                    continue;
                }

                $fragment->appendChild($dom->createTextNode(substr($text, $cursor, $offset - $cursor)));

                $citationCounts[$number] = ($citationCounts[$number] ?? 0) + 1;
                $citationId = 'citation-ref-'.$number.'-'.$citationCounts[$number];

                $anchor = $dom->createElement('a', '['.$number.']');
                $anchor->setAttribute('id', $citationId);
                $anchor->setAttribute('href', '#'.$sourceTargets[$number]['id']);
                $anchor->setAttribute('class', 'citation-ref');
                $fragment->appendChild($anchor);

                if (!isset($citationTargets[$number])) {
                    $citationTargets[$number] = $citationId;
                }

                $cursor = $offset + strlen($fullText);
                $changed = true;
            }

            if (!$changed) {
                continue;
            }

            $fragment->appendChild($dom->createTextNode(substr($text, $cursor)));
            $textNode->parentNode?->replaceChild($fragment, $textNode);
        }

        return $citationTargets;
    }

    /**
     * @param array<string, array{id: string, node: \DOMElement}> $sourceTargets
     * @param array<string, string> $citationTargets
     */
    private function appendBackLinksToSources(
        \DOMDocument $dom,
        array $sourceTargets,
        array $citationTargets
    ): void {
        foreach ($sourceTargets as $number => $target) {
            if (!isset($citationTargets[$number])) {
                continue;
            }

            $sourceNode = $target['node'];
            if ($this->hasBackLink($sourceNode)) {
                continue;
            }

            $backLink = $dom->createElement('a', '[geri]');
            $backLink->setAttribute('href', '#'.$citationTargets[$number]);
            $backLink->setAttribute('class', 'citation-backref');
            $backLink->setAttribute('aria-label', 'Atfa geri dön');

            $sourceNode->appendChild($dom->createTextNode(' '));
            $sourceNode->appendChild($backLink);
        }
    }

    private function hasBackLink(\DOMElement $element): bool
    {
        foreach ($element->getElementsByTagName('a') as $anchor) {
            if (!$anchor instanceof \DOMElement) {
                continue;
            }

            if (str_contains(' '.$anchor->getAttribute('class').' ', ' citation-backref ')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, true> $nodeIdSet
     */
    private function hasAncestorInSet(\DOMNode $node, array $nodeIdSet): bool
    {
        $parent = $node->parentNode;

        while ($parent instanceof \DOMElement) {
            if (isset($nodeIdSet[spl_object_id($parent)])) {
                return true;
            }

            $parent = $parent->parentNode;
        }

        return false;
    }

    /**
     * @param array<int, string> $tagNames
     */
    private function hasAncestorTag(\DOMNode $node, array $tagNames): bool
    {
        $tagLookup = array_fill_keys(array_map('strtolower', $tagNames), true);
        $parent = $node->parentNode;

        while ($parent instanceof \DOMElement) {
            if (isset($tagLookup[strtolower($parent->tagName)])) {
                return true;
            }

            $parent = $parent->parentNode;
        }

        return false;
    }

    private function elementIdExists(\DOMXPath $xpath, string $id): bool
    {
        if (!preg_match('/^[A-Za-z0-9\-_:.]+$/', $id)) {
            return false;
        }

        $found = $xpath->query("//*[@id='{$id}']");

        return $found !== false && $found->length > 0;
    }

    private function appendClass(\DOMElement $element, string $className): void
    {
        $classes = array_filter(
            array_map('trim', explode(' ', (string) $element->getAttribute('class')))
        );

        if (in_array($className, $classes, true)) {
            return;
        }

        $classes[] = $className;
        $element->setAttribute('class', implode(' ', $classes));
    }
}
