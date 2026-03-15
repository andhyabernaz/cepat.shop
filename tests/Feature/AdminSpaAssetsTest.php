<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminSpaAssetsTest extends TestCase
{
    private string $previousConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousConnection = config('database.default');

        config([
            'installer.installed' => true,
            'database.connections.spa_test' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'database.default' => 'spa_test',
        ]);

        DB::purge('spa_test');
        DB::reconnect('spa_test');
        Cache::flush();

        $this->createSchema();
        $this->seedData();
    }

    protected function tearDown(): void
    {
        config(['database.default' => $this->previousConnection]);
        parent::tearDown();
    }

    public function test_admin_dashboard_returns_spa_shell(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('id=q-app', false);
        $response->assertSee('src=/js/', false);
    }

    public function test_spa_shell_asset_paths_exist_in_public_directory(): void
    {
        $contents = file_get_contents(resource_path('views/app.blade.php'));
        $this->assertIsString($contents);

        $assetPaths = [];

        preg_match_all('/\bsrc=\/(js\/[^"\'>\s]+)/i', $contents, $scriptMatches);
        foreach ($scriptMatches[1] ?? [] as $path) {
            $assetPaths[] = $path;
        }

        preg_match_all('/\bhref=\/(css\/[^"\'>\s]+)/i', $contents, $styleMatches);
        foreach ($styleMatches[1] ?? [] as $path) {
            $assetPaths[] = $path;
        }

        $assetPaths = array_values(array_unique($assetPaths));

        $this->assertNotEmpty($assetPaths);

        foreach ($assetPaths as $relativePath) {
            $this->assertFileExists(public_path($relativePath), "Missing public asset: {$relativePath}");
        }
    }

    private function createSchema(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slogan')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('icon_path')->nullable();
            $table->timestamps();
        });
    }

    private function seedData(): void
    {
        DB::table('stores')->insert([
            'id' => 1,
            'name' => 'Demo Shop',
            'slogan' => null,
            'description' => 'Demo desc',
            'logo_path' => null,
            'icon_path' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

