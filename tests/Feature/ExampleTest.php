<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test /tentang-kami route returns a successful response and contains correct text.
     */
    public function test_tentang_kami_returns_successful_response(): void
    {
        $response = $this->get('/tentang-kami');

        $response->assertStatus(200);
        $response->assertSee('PROFIL KOST ASRI');
        $response->assertSee('VISI KAMI');
        $response->assertSee('MISI KAMI');
    }
}
