<?php

namespace Tests\Unit\Components;

use App\Models\Penyewa;
use App\Models\User;
use App\View\Components\AppLayout;
use App\View\Components\GuestLayout;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LayoutComponentsTest extends TestCase
{
    /**
     * Set the current route name for the request helper.
     */
    protected function setCurrentRouteName(string $name): void
    {
        $request = Request::create('/test-url', 'GET');
        $route = new Route('GET', 'test-url', ['as' => $name]);
        $request->setRouteResolver(fn () => $route);
        $this->app->instance('request', $request);
    }

    /**
     * Test that AppLayout defaults to null override.
     */
    public function test_app_layout_can_be_instantiated_with_default_override(): void
    {
        $component = new AppLayout;
        $this->assertNull($component->brutalist);
    }

    /**
     * Test that AppLayout respects explicit brutalist override.
     */
    public function test_app_layout_respects_explicit_brutalist_override(): void
    {
        $component = new AppLayout(true);
        $this->assertTrue($component->shouldUseBrutalistHeader());

        $component = new AppLayout(false);
        $this->assertFalse($component->shouldUseBrutalistHeader());
    }

    /**
     * Test that AppLayout detects brutalist header for admin routes.
     */
    public function test_app_layout_uses_brutalist_header_for_admin_routes(): void
    {
        $this->setCurrentRouteName('admin.dashboard');

        $component = new AppLayout;
        $this->assertTrue($component->shouldUseBrutalistHeader());
    }

    /**
     * Test that AppLayout detects brutalist header for penyewa routes.
     */
    public function test_app_layout_uses_brutalist_header_for_penyewa_routes(): void
    {
        $this->setCurrentRouteName('penyewa.dashboard');

        $component = new AppLayout;
        $this->assertTrue($component->shouldUseBrutalistHeader());
    }

    /**
     * Test that AppLayout detects brutalist header for non-brutalist routes under normal conditions.
     */
    public function test_app_layout_does_not_use_brutalist_header_for_general_public_routes(): void
    {
        $this->setCurrentRouteName('landing.index');

        $component = new AppLayout;
        $this->assertFalse($component->shouldUseBrutalistHeader());
    }

    /**
     * Test that AppLayout enforces brutalist header when the current user is a tenant (penyewa)
     * and their status is not active, even on general routes.
     */
    public function test_app_layout_enforces_brutalist_header_for_inactive_tenants(): void
    {
        $this->setCurrentRouteName('landing.index');

        // Create tenant user
        $user = new User([
            'role' => 'penyewa',
        ]);

        // Status is null/inactive
        $penyewa = new Penyewa(['status' => 'pending']);
        $user->setRelation('penyewa', $penyewa);

        $this->actingAs($user);

        $component = new AppLayout;
        $this->assertTrue($component->shouldUseBrutalistHeader());
    }

    /**
     * Test that AppLayout handles unauthenticated guest users safely on public routes.
     */
    public function test_app_layout_handles_unauthenticated_guests_on_public_routes(): void
    {
        $this->setCurrentRouteName('landing.index');
        Auth::logout();

        $component = new AppLayout;
        $this->assertFalse($component->shouldUseBrutalistHeader());
    }

    /**
     * Test that GuestLayout can receive size parameters.
     */
    public function test_guest_layout_parameter_handling(): void
    {
        $component = new GuestLayout('lg');
        $this->assertEquals('lg', $component->size);

        $component = new GuestLayout;
        $this->assertEquals('md', $component->size);
    }

    /**
     * Test that GuestLayout falls back to default size 'md' when provided invalid input.
     */
    public function test_guest_layout_falls_back_to_default_for_invalid_size(): void
    {
        $component = new GuestLayout('invalid-size-variant');
        $this->assertEquals('md', $component->size);

        $componentEmpty = new GuestLayout('');
        $this->assertEquals('md', $componentEmpty->size);
    }
}

