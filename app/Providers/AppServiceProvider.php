<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->runningInConsole()) {
            URL::forceRootUrl(config('app.url'));
        }

        $this->normalizeMailConfiguration();
    }

    protected function normalizeMailConfiguration(): void
    {
        $defaultMailer = config('mail.default');

        if (! is_string($defaultMailer) || $defaultMailer === '') {
            return;
        }

        $transport = config("mail.mailers.{$defaultMailer}.transport");
        $url = config("mail.mailers.{$defaultMailer}.url");

        if (is_string($url)) {
            if (trim($url) === '') {
                config(["mail.mailers.{$defaultMailer}.url" => null]);
            } else {
                $parts = @parse_url($url);
                $host = is_array($parts) ? ($parts['host'] ?? null) : null;

                if (! is_string($host) || $host === '') {
                    config(["mail.mailers.{$defaultMailer}.url" => null]);
                    Log::warning('MAIL_URL is set but invalid (missing host). Ignoring MAIL_URL and using MAIL_HOST based configuration.', [
                        'default_mailer' => $defaultMailer,
                        'transport' => $transport,
                    ]);
                }
            }
        }

        if ($transport === 'smtp') {
            $host = config("mail.mailers.{$defaultMailer}.host");

            if (! is_string($host) || trim($host) === '') {
                config(['mail.default' => 'log']);
                Log::error('MAIL_HOST is missing for SMTP. Falling back to log mailer to avoid fatal error.', [
                    'previous_default_mailer' => $defaultMailer,
                    'transport' => $transport,
                ]);
            }
        }
    }
}
