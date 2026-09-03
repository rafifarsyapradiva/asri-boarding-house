<?php

namespace Tests\Feature\Components;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\MessageBag;

class AuthComponentsTest extends TestCase
{
    #[Test]
    public function google_button_renders_with_route_or_fallback_without_error(): void
    {
        $view = $this->blade('<x-auth.google-button />');

        $view->assertSee('Masuk dengan Google');
        $view->assertSee('data-testid="btn-google-oauth"', false);
    }

    #[Test]
    public function google_button_accepts_custom_route_and_attributes(): void
    {
        $view = $this->blade('<x-auth.google-button route="https://example.com/oauth/google" target="_blank" rel="noopener" />');

        $view->assertSee('href="https://example.com/oauth/google"', false);
        $view->assertSee('target="_blank"', false);
        $view->assertSee('rel="noopener"', false);
    }

    #[Test]
    public function password_input_renders_correctly_with_attributes_forwarding(): void
    {
        $view = $this->blade('<x-auth.password-input name="password_confirmation" label="Konfirmasi Kata Sandi" wire:model="confirmPassword" autofocus />');

        $view->assertSee('Konfirmasi Kata Sandi');
        $view->assertSee('name="password_confirmation"', false);
        $view->assertSee('wire:model="confirmPassword"', false);
        $view->assertSee('autofocus', false);
        $view->assertSee('type="password"', false);
    }

    #[Test]
    public function password_input_displays_validation_errors_when_present(): void
    {
        $errors = new ViewErrorBag();
        $messages = new MessageBag(['password' => ['Kata sandi wajib diisi.']]);
        $errors->put('default', $messages);
        view()->share('errors', $errors);

        $view = $this->blade('<x-auth.password-input name="password" />');

        $view->assertSee('Kata sandi wajib diisi.');
    }
}
