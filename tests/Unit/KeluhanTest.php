<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Keluhan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KeluhanTest extends TestCase
{
    use RefreshDatabase;

    public function test_kategori_label_accessor_formats_properly()
    {
        $keluhan = new Keluhan(['kategori' => 'fasilitas_bersama']);
        $this->assertEquals('Fasilitas Bersama', $keluhan->kategori_label);

        $keluhan2 = new Keluhan(['kategori' => 'kebersihan']);
        $this->assertEquals('Kebersihan', $keluhan2->kategori_label);
    }

    public function test_status_badge_class_accessor_returns_correct_css_class()
    {
        $keluhanPending = new Keluhan(['status' => 'pending']);
        $keluhanDiproses = new Keluhan(['status' => 'diproses']);
        $keluhanSelesai = new Keluhan(['status' => 'selesai']);

        $this->assertEquals('admin-badge-warning', $keluhanPending->status_badge_class);
        $this->assertEquals('admin-badge-info', $keluhanDiproses->status_badge_class);
        $this->assertEquals('admin-badge-success', $keluhanSelesai->status_badge_class);
    }
}
