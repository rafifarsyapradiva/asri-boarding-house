<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\Auth\PortalRedirectResolver;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class PortalRedirectResolverTest extends TestCase
{
    use RefreshDatabase;

    private PortalRedirectResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = app(PortalRedirectResolver::class);
    }

    public function test_guest_redirects_to_admin_login_when_accessing_admin_path(): void
    {
        $request = Request::create('/admin/dashboard', 'GET');

        $url = $this->resolver->resolveGuestRedirect($request);

        $this->assertEquals(route('admin.login'), $url);
    }

    public function test_guest_redirects_to_penyewa_login_when_accessing_penyewa_path(): void
    {
        $request = Request::create('/penyewa/dashboard', 'GET');

        $url = $this->resolver->resolveGuestRedirect($request);

        $this->assertEquals(route('penyewa.login'), $url);
    }

    public function test_guest_redirects_to_reservasi_login_for_other_paths(): void
    {
        $request = Request::create('/some-other-path', 'GET');

        $url = $this->resolver->resolveGuestRedirect($request);

        $this->assertEquals(route('reservasi.login'), $url);
    }

    public function test_unauthenticated_request_returns_landing_page(): void
    {
        $request = Request::create('/any-route', 'GET');

        $url = $this->resolver->resolveAuthenticatedRedirect($request);

        $this->assertEquals(route('landing.index'), $url);
    }

    public function test_admin_accessing_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::create([
            'nama' => 'Admin User',
            'email' => 'admin@example.com',
            'no_hp' => '08123456789',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);
        $request = Request::create('/admin/login', 'GET');
        $request->setUserResolver(fn () => $admin);

        $url = $this->resolver->resolveAuthenticatedRedirect($request);

        $this->assertEquals(route('admin.dashboard'), $url);
    }

    public function test_non_admin_accessing_admin_portal_logs_out_and_refreshes_via_injected_auth(): void
    {
        $guardMock = Mockery::mock(StatefulGuard::class);
        $guardMock->shouldReceive('logout')->once();

        $authFactoryMock = Mockery::mock(AuthFactory::class);
        $authFactoryMock->shouldReceive('guard')->andReturn($guardMock);

        $resolver = new PortalRedirectResolver($authFactoryMock);

        $user = User::create([
            'nama' => 'Regular User',
            'email' => 'user@example.com',
            'no_hp' => '08123456789',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PENYEWA,
        ]);

        $request = Request::create('/admin/login', 'GET');
        $request->setLaravelSession(app('session')->driver());
        $request->setUserResolver(fn () => $user);

        $url = $resolver->resolveAuthenticatedRedirect($request);

        $this->assertEquals($request->fullUrl(), $url);
    }
}
