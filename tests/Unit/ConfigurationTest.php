<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Env;

class ConfigurationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Backup original values to clean up config space dynamically
        $this->originalAppUrl = config('app.url');
        $this->originalGoogleRedirect = config('services.google.redirect');
        $this->originalWaOwner = config('services.wa_owner');
        $this->originalWaOwnerName = config('services.wa_owner_name');
        $this->originalDpPercentage = config('reservasi.dp_percentage');
        $this->originalExpireHours = config('reservasi.expire_hours');
        $this->originalAdminWa = config('reservasi.admin_wa');
        $this->originalQueueBackoff = config('queue.jobs.backoff');
    }

    protected function tearDown(): void
    {
        // Restore original configs
        config(['app.url' => $this->originalAppUrl]);
        config(['services.google.redirect' => $this->originalGoogleRedirect]);
        config(['services.wa_owner' => $this->originalWaOwner]);
        config(['services.wa_owner_name' => $this->originalWaOwnerName]);
        config(['reservasi.dp_percentage' => $this->originalDpPercentage]);
        config(['reservasi.expire_hours' => $this->originalExpireHours]);
        config(['reservasi.admin_wa' => $this->originalAdminWa]);
        config(['queue.jobs.backoff' => $this->originalQueueBackoff]);

        parent::tearDown();
    }

    /**
     * Test Google OAuth redirect URI trims trailing slash.
     */
    public function test_google_redirect_uri_trims_trailing_slash(): void
    {
        $repository = Env::getRepository();
        $originalAppUrl = $repository->get('APP_URL');
        
        try {
            $repository->set('APP_URL', 'http://localhost:8000/');
            $services = require base_path('config/services.php');
            $this->assertEquals('http://localhost:8000/auth/google/callback', $services['google']['redirect']);
        } finally {
            if ($originalAppUrl === null) {
                $repository->clear('APP_URL');
            } else {
                $repository->set('APP_URL', $originalAppUrl);
            }
        }
    }

    /**
     * Test type casting for reservation configurations.
     */
    public function test_reservasi_config_type_casting(): void
    {
        $repository = Env::getRepository();
        $originalDp = $repository->get('RESERVASI_DP_PERCENTAGE');
        $originalExpire = $repository->get('RESERVASI_EXPIRE_HOURS');

        try {
            // Set string values in env repository
            $repository->set('RESERVASI_DP_PERCENTAGE', '0.35');
            $repository->set('RESERVASI_EXPIRE_HOURS', '48');

            $reservasi = require base_path('config/reservasi.php');

            $this->assertIsFloat($reservasi['dp_percentage']);
            $this->assertEquals(0.35, $reservasi['dp_percentage']);

            $this->assertIsInt($reservasi['expire_hours']);
            $this->assertEquals(48, $reservasi['expire_hours']);
        } finally {
            // Restore original env states
            if ($originalDp === null) {
                $repository->clear('RESERVASI_DP_PERCENTAGE');
            } else {
                $repository->set('RESERVASI_DP_PERCENTAGE', $originalDp);
            }

            if ($originalExpire === null) {
                $repository->clear('RESERVASI_EXPIRE_HOURS');
            } else {
                $repository->set('RESERVASI_EXPIRE_HOURS', $originalExpire);
            }
        }
    }

    /**
     * Test queue backoff safely handles empty string settings.
     */
    public function test_queue_backoff_handles_empty_string_safely(): void
    {
        $repository = Env::getRepository();
        $originalEnvBackoff = $repository->get('QUEUE_JOBS_BACKOFF');

        try {
            // Set to empty string
            $repository->set('QUEUE_JOBS_BACKOFF', '');
            $queue = require base_path('config/queue.php');
            $this->assertEquals([30, 60, 120], $queue['jobs']['backoff']);

            // Set to space values
            $repository->set('QUEUE_JOBS_BACKOFF', ' ');
            $queue = require base_path('config/queue.php');
            $this->assertEquals([30, 60, 120], $queue['jobs']['backoff']);

            // Set to standard values
            $repository->set('QUEUE_JOBS_BACKOFF', '10,20,30');
            $queue = require base_path('config/queue.php');
            $this->assertEquals([10, 20, 30], $queue['jobs']['backoff']);
        } finally {
            // Restore original env state
            if ($originalEnvBackoff === null) {
                $repository->clear('QUEUE_JOBS_BACKOFF');
            } else {
                $repository->set('QUEUE_JOBS_BACKOFF', $originalEnvBackoff);
            }
        }
    }

    /**
     * Test default fallback values of WhatsApp owner config are configured.
     */
    public function test_whatsapp_owner_default_configuration(): void
    {
        $repository = Env::getRepository();
        $originalOwner = $repository->get('WA_OWNER_NUMBER');
        $originalName = $repository->get('WA_OWNER_NAME');

        try {
            // Clear WA owner env to force fallback
            $repository->clear('WA_OWNER_NUMBER');
            $repository->clear('WA_OWNER_NAME');

            $services = require base_path('config/services.php');

            // Check fallback works both top-level and nested
            $this->assertEquals('62895330031313', $services['wa_owner']);
            $this->assertEquals('Admin Asri Boarding House', $services['wa_owner_name']);
            $this->assertEquals('62895330031313', $services['whatsapp']['owner_number']);
            $this->assertEquals('Admin Asri Boarding House', $services['whatsapp']['owner_name']);
        } finally {
            if ($originalOwner !== null) {
                $repository->set('WA_OWNER_NUMBER', $originalOwner);
            }
            if ($originalName !== null) {
                $repository->set('WA_OWNER_NAME', $originalName);
            }
        }
    }

    /**
     * Test Midtrans dynamic base and snap URL resolution.
     */
    public function test_midtrans_url_dynamic_resolution(): void
    {
        $repository = Env::getRepository();
        $originalProd = $repository->get('MIDTRANS_IS_PRODUCTION');

        try {
            $repository->set('MIDTRANS_IS_PRODUCTION', 'false');
            $midtrans = require base_path('config/midtrans.php');
            $this->assertFalse($midtrans['is_production']);
            $this->assertStringContainsString('sandbox', $midtrans['base_url']);

            $repository->set('MIDTRANS_IS_PRODUCTION', 'true');
            $midtransProd = require base_path('config/midtrans.php');
            $this->assertTrue($midtransProd['is_production']);
            $this->assertStringNotContainsString('sandbox', $midtransProd['base_url']);
        } finally {
            if ($originalProd !== null) {
                $repository->set('MIDTRANS_IS_PRODUCTION', $originalProd);
            } else {
                $repository->clear('MIDTRANS_IS_PRODUCTION');
            }
        }
    }

    /**
     * Test Fonnte configuration options.
     */
    public function test_fonnte_configuration_defaults(): void
    {
        $fonnte = require base_path('config/fonnte.php');

        $this->assertArrayHasKey('token', $fonnte);
        $this->assertArrayHasKey('url', $fonnte);
        $this->assertIsInt($fonnte['timeout']);
        $this->assertIsInt($fonnte['connect_timeout']);
        $this->assertEquals('https://api.fonnte.com/send', $fonnte['url']);
    }

    /**
     * Test structured settings promo packages.
     */
    public function test_structured_settings_promo_section(): void
    {
        $settings = require base_path('config/settings.php');

        $this->assertArrayHasKey('promo_section', $settings['defaults']);
        $this->assertIsArray($settings['defaults']['promo_section']['packages']);
        $this->assertCount(3, $settings['defaults']['promo_section']['packages']);
    }
}
