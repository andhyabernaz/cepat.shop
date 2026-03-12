<?php

namespace Tests\Feature;

use Tests\TestCase;

class RouteCanonicalizationTest extends TestCase
{
    public function test_url_generator_does_not_force_auto_cepat_prefix_on_http_requests(): void
    {
        config()->set('app.url', 'http://localhost/auto/cepat');

        $this->get('/install')->assertOk();

        $generated = url('/expected');
        $this->assertStringNotContainsString('/auto/cepat', $generated);
        $this->assertStringEndsWith('/expected', $generated);
    }

    public function test_auto_cepat_path_is_not_served_by_laravel(): void
    {
        $this->get('http://localhost/auto/cepat')->assertNotFound();
        $this->get('http://localhost/auto/cepat/anything')->assertNotFound();
    }
}
