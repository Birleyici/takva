<?php

namespace Tests\Unit;

use App\Services\ArticleContentNormalizer;
use PHPUnit\Framework\TestCase;

class ArticleContentNormalizerTest extends TestCase
{
    public function test_it_links_inline_bracket_references_to_matching_sources(): void
    {
        $normalizer = new ArticleContentNormalizer();

        $content = '<p>Metin ici atif [1] ve ikinci atif [2].</p>'
            .'<p>[1] Birinci kaynak metni</p>'
            .'<p>[2] Ikinci kaynak metni</p>';

        $normalized = $normalizer->normalize($content);

        $this->assertIsString($normalized);
        $this->assertStringContainsString('id="citation-ref-1-1"', $normalized);
        $this->assertStringContainsString('href="#source-ref-1"', $normalized);
        $this->assertStringContainsString('id="source-ref-1"', $normalized);
        $this->assertStringContainsString('href="#citation-ref-1-1"', $normalized);
        $this->assertStringContainsString('id="citation-ref-2-1"', $normalized);
        $this->assertStringContainsString('href="#source-ref-2"', $normalized);
    }

    public function test_it_keeps_unmatched_bracket_references_as_plain_text(): void
    {
        $normalizer = new ArticleContentNormalizer();

        $content = '<p>Bu [7] atfi kaynaksiz.</p><p>[1] Sadece birinci kaynak var.</p>';
        $normalized = $normalizer->normalize($content);

        $this->assertIsString($normalized);
        $this->assertStringNotContainsString('citation-ref-7-1', $normalized);
        $this->assertStringContainsString('[7]', $normalized);
    }
}
