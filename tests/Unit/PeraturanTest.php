<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Peraturan;

class PeraturanTest extends TestCase
{
    public function test_badge_color_class_returns_correct_css_class_for_known_icons()
    {
        $peraturan1 = new Peraturan(['ikon' => 'sparkles']);
        $peraturan2 = new Peraturan(['ikon' => 'bolt']);
        $peraturan3 = new Peraturan(['ikon' => 'computer-desktop']);
        $peraturan4 = new Peraturan(['ikon' => 'moon']);

        $this->assertStringContainsString('bg-indigo-300', $peraturan1->badge_color_class);
        $this->assertStringContainsString('bg-amber-300', $peraturan2->badge_color_class);
        $this->assertStringContainsString('bg-blue-300', $peraturan3->badge_color_class);
        $this->assertStringContainsString('bg-purple-300', $peraturan4->badge_color_class);
    }

    public function test_badge_color_class_returns_fallback_class_for_unknown_icons()
    {
        $peraturan = new Peraturan(['ikon' => 'non-existent-icon']);

        $this->assertStringContainsString('bg-slate-300', $peraturan->badge_color_class);
    }

    public function test_ikon_label_formats_hyphenated_slugs_to_readable_text()
    {
        $peraturan1 = new Peraturan(['ikon' => 'computer-desktop']);
        $peraturan2 = new Peraturan(['ikon' => 'exclamation-triangle']);

        $this->assertEquals('computer desktop', $peraturan1->ikon_label);
        $this->assertEquals('exclamation triangle', $peraturan2->ikon_label);
    }
}
