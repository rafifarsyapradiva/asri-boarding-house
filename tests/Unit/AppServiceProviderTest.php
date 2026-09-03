<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Providers\AppServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

class AppServiceProviderTest extends TestCase
{
    private ?\Closure $limiterClosure;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->limiterClosure = RateLimiter::limiter(AppServiceProvider::GUEST_CHAT_LIMITER);
        $this->assertNotNull($this->limiterClosure, 'Rate limiter guest_chat_limiter should be registered.');
    }

    /**
     * Test admin user gets Limit::none()
     */
    public function test_admin_is_exempt_from_rate_limiting(): void
    {
        $admin = new User([
            'role' => User::ROLE_ADMIN,
        ]);

        $request = Request::create('/chat', 'POST');
        $request->setUserResolver(fn() => $admin);

        $limit = ($this->limiterClosure)($request);

        $this->assertInstanceOf(\Illuminate\Cache\RateLimiting\Unlimited::class, $limit);
    }

    /**
     * Test rate limit key generated from guest_chat_token cookie
     */
    public function test_rate_key_generated_from_cookie_token(): void
    {
        $request = Request::create('/chat', 'POST', [], ['guest_chat_token' => 'my-secret-cookie']);

        $limit = ($this->limiterClosure)($request);

        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertEquals(30, $limit->maxAttempts);
        $this->assertEquals(hash('sha256', 'my-secret-cookie'), $limit->key);
    }

    /**
     * Test rate limit key generated from X-Guest-Chat-Token header
     */
    public function test_rate_key_generated_from_header_token(): void
    {
        $request = Request::create('/chat', 'POST');
        $request->headers->set('X-Guest-Chat-Token', 'my-secret-header');

        $limit = ($this->limiterClosure)($request);

        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertEquals(30, $limit->maxAttempts);
        $this->assertEquals(hash('sha256', 'my-secret-header'), $limit->key);
    }

    /**
     * Test rate limit key generated from session_token input parameter
     */
    public function test_rate_key_generated_from_input_token(): void
    {
        $request = Request::create('/chat', 'POST', ['session_token' => 'my-secret-input']);

        $limit = ($this->limiterClosure)($request);

        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertEquals(30, $limit->maxAttempts);
        $this->assertEquals(hash('sha256', 'my-secret-input'), $limit->key);
    }

    /**
     * Test rate limit fallback to client IP when no token is present
     */
    public function test_rate_key_falls_back_to_client_ip_when_no_token(): void
    {
        $request = Request::create('/chat', 'POST');
        $request->server->set('REMOTE_ADDR', '203.0.113.1');

        $limit = ($this->limiterClosure)($request);

        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertEquals(30, $limit->maxAttempts);
        $this->assertEquals('203.0.113.1', $limit->key);
    }

    /**
     * Test safety check: array input in session_token parameter falls back to client IP and doesn't crash
     */
    public function test_array_input_falls_back_to_client_ip_without_crashing(): void
    {
        $request = Request::create('/chat', 'POST', ['session_token' => ['attack']]);
        $request->server->set('REMOTE_ADDR', '203.0.113.1');

        $limit = ($this->limiterClosure)($request);

        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertEquals(30, $limit->maxAttempts);
        $this->assertEquals('203.0.113.1', $limit->key);
    }

    /**
     * Test CLI/Null IP fallback
     */
    public function test_rate_key_falls_back_to_localhost_ip_when_ip_is_null(): void
    {
        $request = Request::create('/chat', 'POST');
        $request->server->remove('REMOTE_ADDR');

        $limit = ($this->limiterClosure)($request);

        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertEquals(30, $limit->maxAttempts);
        $this->assertEquals('127.0.0.1', $limit->key);
    }

    /**
     * Test rate limit uses config value if defined
     */
    public function test_rate_limit_uses_config_value(): void
    {
        config(['services.chat.guest_limit' => 10]);

        $request = Request::create('/chat', 'POST', [], ['guest_chat_token' => 'custom-config-token']);

        $limit = ($this->limiterClosure)($request);

        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertEquals(10, $limit->maxAttempts);
    }
}
