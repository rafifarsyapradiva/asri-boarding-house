<?php

namespace Tests\Unit\Composers;

use Tests\TestCase;
use App\Http\View\Composers\LayoutSettingComposer;
use App\Models\Setting;
use Illuminate\View\View;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class LayoutSettingComposerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        LayoutSettingComposer::resetCache();
    }

    public function test_it_injects_setting_data_into_view(): void
    {
        // Arrange
        Setting::create(['key' => 'contact_whatsapp', 'value' => '08123456789']);
        Setting::create(['key' => 'logo_text', 'value' => 'Kost Asri Test']);

        $viewMock = Mockery::mock(View::class);
        $viewMock->shouldReceive('with')
            ->once()
            ->with(Mockery::on(function ($data) {
                return isset($data['waNumber']) && $data['waNumber'] === '628123456789'
                    && isset($data['logoText']) && $data['logoText'] === 'Kost Asri Test'
                    && isset($data['logoIcon']);
            }));

        $composer = new LayoutSettingComposer();

        // Act
        $composer->compose($viewMock);

        // Assert
        $this->assertTrue(true);
    }
}
