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

    public function test_auto_cepat_path_is_redirected_to_main_domain(): void
    {
        $this->get('http://localhost/auto/cepat')
            ->assertRedirect('https://shop.cepat.digital/');

        $this->get('http://localhost/auto/cepat/anything')
            ->assertRedirect('https://shop.cepat.digital/anything');
    }

    public function test_auto_path_is_redirected_to_main_domain_with_query(): void
    {
        $this->get('http://localhost/auto/sample?foo=bar')
            ->assertRedirect('https://shop.cepat.digital/sample?foo=bar');
    }
}
