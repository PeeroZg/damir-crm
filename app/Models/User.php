<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'role', 'locale'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $attributes = [
        'role' => 'sales',
        'locale' => 'en',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    protected function initials(): Attribute
    {
        return Attribute::get(function () {
            $parts = preg_split('/\s+/', trim($this->name)) ?: [];

            return mb_strtoupper(collect($parts)->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode(''));
        });
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /** Seeded demo accounts are protected while the public demo is running. */
    public function isDemoAccount(): bool
    {
        return config('crm.demo.enabled')
            && str_ends_with($this->email, '@'.config('crm.demo.domain'));
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'owner_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'owner_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Activity::class, 'assigned_to');
    }

    public function apiTokens(): HasMany
    {
        return $this->hasMany(ApiToken::class)->latest();
    }

    /**
     * Create a personal API token. The plain text value is returned once and never stored.
     */
    public function createApiToken(string $name, ?\DateTimeInterface $expiresAt = null): string
    {
        $secret = Str::random(40);

        $token = $this->apiTokens()->create([
            'name' => $name,
            'token' => ApiToken::hash($secret),
            'expires_at' => $expiresAt,
        ]);

        return $token->id.'|'.$secret;
    }
}
