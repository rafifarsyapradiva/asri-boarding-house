<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Fasilitas;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FasilitasTest extends TestCase
{
    use RefreshDatabase;

    public function test_emoji_returns_correct_symbol_for_known_icons()
    {
        $fas1 = Fasilitas::create([
            'nama' => 'WiFi Gratis',
            'ikon' => 'wifi',
            'deskripsi' => 'WiFi cepat',
            'is_active' => true
        ]);

        $fas2 = Fasilitas::create([
            'nama' => 'AC Hemat Energi',
            'ikon' => 'snowflake',
            'deskripsi' => 'AC dingin',
            'is_active' => true
        ]);

        $fas3 = Fasilitas::create([
            'nama' => 'Listrik Token',
            'ikon' => 'bolt',
            'deskripsi' => 'Listrik mandiri',
            'is_active' => true
        ]);

        $this->assertEquals('📶', $fas1->emoji);
        $this->assertEquals('❄️', $fas2->emoji);
        $this->assertEquals('⚡', $fas3->emoji);
    }

    public function test_emoji_returns_fallback_symbol_for_unknown_icons()
    {
        $fas = Fasilitas::create([
            'nama' => 'Fasilitas Misterius',
            'ikon' => 'unknown-icon-name',
            'deskripsi' => 'Misteri',
            'is_active' => true
        ]);

        $this->assertEquals('🏠', $fas->emoji);
    }
}
