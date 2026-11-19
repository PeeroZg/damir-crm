<?php

namespace App\Providers;

use App\Models\ApiToken;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Catch N+1 queries and silently discarded attributes during development and tests.
        Model::shouldBeStrict(! $this->app->isProduction());

        // Store short, stable aliases instead of class names in polymorphic columns.
        Relation::enforceMorphMap([
            'company' => Company::class,
            'contact' => Contact::class,
            'deal' => Deal::class,
            'offer' => Offer::class,
            'user' => User::class,
        ]);

        Paginator::defaultView('components.pagination');

        $this->configureApiAuthentication();
    }

    /**
     * A small personal-access-token guard for the REST API ("Authorization: Bearer <id>|<secret>").
     * Tokens are stored as SHA-256 hashes and can be revoked from the profile page.
     */
    private function configureApiAuthentication(): void
    {
        Auth::viaRequest('api-token', function (Request $request) {
            $token = ApiToken::findValid($request->bearerToken());

            if (! $token) {
                return null;
            }

            // Avoid a write on every request: refresh "last used" at most once a minute.
            if (! $token->last_used_at || $token->last_used_at->lt(now()->subMinute())) {
                $token->forceFill(['last_used_at' => now()])->saveQuietly();
            }

            return $token->user;
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
