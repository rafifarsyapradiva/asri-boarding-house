<?php

namespace Tests\Unit;

use App\Traits\HandlesReservationSession;
use Illuminate\Http\Request;
use Tests\TestCase;

class HandlesReservationSessionTest extends TestCase
{
    private object $dummy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dummy = new class {
            use HandlesReservationSession;
            
            // Expose protected methods for testing if needed
            public function callStoreReservationSessionState(Request $request): string
            {
                return $this->storeReservationSessionState($request);
            }
        };
    }

    /**
     * Test menyimpan parameter reservasi tipe sewa dan durasi ke dalam session.
     */
    public function test_stores_reservation_parameters_in_session(): void
    {
        $request = Request::create('/reserve', 'POST', [
            'tipe_sewa' => 'harian',
            'durasi' => '3',
        ]);

        $this->dummy->callStoreReservationSessionState($request);

        $this->assertEquals('harian', session('reservasi_tipe_sewa'));
        $this->assertEquals('3', session('reservasi_durasi'));
    }

    /**
     * Test tidak menyimpan parameter reservasi jika input request kosong.
     */
    public function test_does_not_overwrite_session_if_parameters_are_absent(): void
    {
        session(['reservasi_tipe_sewa' => 'bulanan']);
        
        $request = Request::create('/reserve', 'POST'); // Tanpa input

        $this->dummy->callStoreReservationSessionState($request);

        $this->assertEquals('bulanan', session('reservasi_tipe_sewa'));
    }

    /**
     * Test mencatat url.intended jika URL sebelumnya valid, lokal, dan bukan blacklist.
     */
    public function test_saves_intended_url_when_valid_local_and_not_excluded(): void
    {
        $this->from('http://localhost/rooms/kamar-asri-1');
        
        $request = Request::create('/reserve', 'GET');
        $request->headers->set('HOST', 'localhost');

        $this->dummy->callStoreReservationSessionState($request);

        $this->assertEquals('http://localhost/rooms/kamar-asri-1', session('url.intended'));
    }

    /**
     * Test menghindari penyimpanan url.intended jika URL sebelumnya adalah halaman login, register, dll.
     */
    public function test_does_not_save_intended_url_if_excluded(): void
    {
        $excludedUrls = [
            'http://localhost/login',
            'http://localhost/register',
            'http://localhost/forgot-password',
            'http://localhost/reset-password',
            'http://localhost/auth/google',
            'http://localhost/logout',
        ];

        foreach ($excludedUrls as $url) {
            session()->forget('url.intended');
            $this->from($url);

            $request = Request::create('/reserve', 'GET');
            $request->headers->set('HOST', 'localhost');

            $this->dummy->callStoreReservationSessionState($request);

            $this->assertNull(session('url.intended'), "Fails to exclude URL: {$url}");
        }
    }

    /**
     * Test menghindari open redirect dengan tidak mencatat url.intended jika URL eksternal/berbeda domain.
     */
    public function test_does_not_save_intended_url_if_external_domain(): void
    {
        $this->from('https://malicious-website.com/phishing');

        $request = Request::create('/reserve', 'GET');
        $request->headers->set('HOST', 'localhost');

        $this->dummy->callStoreReservationSessionState($request);

        $this->assertNull(session('url.intended'));
    }

    /**
     * Test tidak menimpa url.intended yang sudah ada sebelumnya di session.
     */
    public function test_does_not_overwrite_existing_intended_url(): void
    {
        session(['url.intended' => 'http://localhost/rooms/original-choice']);
        
        $this->from('http://localhost/rooms/new-choice');

        $request = Request::create('/reserve', 'GET');
        $request->headers->set('HOST', 'localhost');

        $this->dummy->callStoreReservationSessionState($request);

        $this->assertEquals('http://localhost/rooms/original-choice', session('url.intended'));
    }

    /**
     * Test memblokir domain prefix spoofing open redirect (misal localhost.attacker.com).
     */
    public function test_blocks_domain_prefix_spoofing_open_redirect(): void
    {
        $this->from('http://localhost.attacker.com/phishing');

        $request = Request::create('/reserve', 'GET');
        $request->headers->set('HOST', 'localhost');

        $this->dummy->callStoreReservationSessionState($request);

        $this->assertNull(session('url.intended'));
    }

    /**
     * Test tidak menyimpan parameter reservasi jika input bernilai string kosong.
     */
    public function test_does_not_overwrite_session_if_parameters_are_empty_string(): void
    {
        session(['reservasi_tipe_sewa' => 'bulanan']);

        $request = Request::create('/reserve', 'POST', [
            'tipe_sewa' => '',
        ]);

        $this->dummy->callStoreReservationSessionState($request);

        $this->assertEquals('bulanan', session('reservasi_tipe_sewa'));
    }

    /**
     * Test mencegah self-referencing url.intended (prevents infinite redirect loop).
     */
    public function test_prevents_self_referencing_intended_url(): void
    {
        $currentUrl = 'http://localhost/reserve';
        $this->from($currentUrl);

        $request = Request::create('/reserve', 'GET');
        $request->headers->set('HOST', 'localhost');

        $this->dummy->callStoreReservationSessionState($request);

        $this->assertNull(session('url.intended'));
    }
}
