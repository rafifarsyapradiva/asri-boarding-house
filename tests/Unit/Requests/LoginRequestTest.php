<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\Auth\LoginRequest;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    /**
     * Test that the request has the correct authorization.
     */
    public function test_authorize_returns_true(): void
    {
        $request = new LoginRequest();
        $this->assertTrue($request->authorize());
    }

    /**
     * Test validation rules definition.
     */
    public function test_rules_returns_correct_rules(): void
    {
        $request = new LoginRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);

        $this->assertEquals(['required', 'string', 'email'], $rules['email']);
        $this->assertEquals(['required', 'string'], $rules['password']);
    }

    /**
     * Test email sanitization during request preparation.
     */
    public function test_email_sanitization_in_prepare_for_validation(): void
    {
        $request = new LoginRequest();
        
        $reflection = new \ReflectionClass(LoginRequest::class);
        $prepareForValidation = $reflection->getMethod('prepareForValidation');
        $prepareForValidation->setAccessible(true);

        $request->merge([
            'email' => '  USER@Example.Com  ',
            'password' => 'password123',
        ]);

        $prepareForValidation->invoke($request);

        $this->assertEquals('user@example.com', $request->input('email'));
    }

    /**
     * Test that throttleKey correctly handles lowercase conversion,
     * trim, and falls back to a safe default IP when the IP is null.
     */
    public function test_throttle_key_generation(): void
    {
        $request = new LoginRequest();
        
        $request->merge([
            'email' => '  USER@Example.Com  ',
        ]);

        // When request IP is not mocked (resolves to null in tests), it should use '127.0.0.1' fallback.
        $key = $request->throttleKey();
        
        $this->assertEquals('user@example.com|127.0.0.1', $key);
    }
}
