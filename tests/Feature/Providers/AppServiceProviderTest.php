<?php

namespace Tests\Feature\Providers;

use Tests\TestCase;
use App\Services\PdfGeneratorInterface;
use App\Services\DompdfGenerator;
use App\Providers\AppServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;

class AppServiceProviderTest extends TestCase
{
    public function test_pdf_generator_interface_is_bound_to_dompdf_generator(): void
    {
        $instance = $this->app->make(PdfGeneratorInterface::class);

        $this->assertInstanceOf(DompdfGenerator::class, $instance);
    }

    public function test_guest_chat_rate_limiter_allows_unlimited_for_admin(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);
        $request = Request::create('/api/chat', 'POST');
        $request->setUserResolver(fn () => $admin);

        $limiterCallback = RateLimiter::limiter(AppServiceProvider::GUEST_CHAT_LIMITER);
        $result = $limiterCallback($request);

        $this->assertInstanceOf(Limit::class, $result);
        $this->assertEquals(PHP_INT_MAX, $result->maxAttempts);
    }

    public function test_guest_chat_rate_limiter_limits_guest_user(): void
    {
        $request = Request::create('/api/chat', 'POST', [], ['guest_chat_token' => 'sample-token-123']);

        $limiterCallback = RateLimiter::limiter(AppServiceProvider::GUEST_CHAT_LIMITER);
        $result = $limiterCallback($request);

        $this->assertInstanceOf(Limit::class, $result);
        $this->assertEquals(30, $result->maxAttempts);
        $this->assertEquals(hash('sha256', 'sample-token-123'), $result->key);
    }
}
