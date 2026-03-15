<?php

namespace Tests\Feature;

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\User;
use Illuminate\Http\Request;
use Tests\TestCase;

class HomeRedirectTest extends TestCase
{
    public function test_authenticated_user_is_redirected_to_root_home_by_guest_middleware(): void
    {
        $this->actingAs(User::factory()->make());

        $request = Request::create('/auth/login', 'GET');
        $middleware = new RedirectIfAuthenticated();

        $response = $middleware->handle($request, fn () => response('next'));

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(url('/'), $response->headers->get('Location'));
    }

    public function test_guest_request_passes_through_guest_middleware(): void
    {
        $request = Request::create('/auth/login', 'GET');
        $middleware = new RedirectIfAuthenticated();

        $response = $middleware->handle($request, fn () => response('next'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('next', $response->getContent());
    }

    public function test_legacy_home_path_redirects_to_root_home(): void
    {
        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
        $response = $kernel->handle(Request::create('/home', 'GET'));

        $this->assertSame(301, $response->getStatusCode());
        $location = $response->headers->get('Location');
        $this->assertNotNull($location);
        $this->assertTrue($location === '/' || $location === url('/'));
    }
}
