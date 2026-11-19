<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiToken extends Model
{
    protected $fillable = ['name', 'token', 'last_used_at', 'expires_at'];

    protected $hidden = ['token'];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function hash(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }

    /** Resolve a "Bearer <id>|<secret>" token, or null when invalid/expired. */
    public static function findValid(?string $plainToken): ?self
    {
        if (! $plainToken || ! str_contains($plainToken, '|')) {
            return null;
        }

        [$id, $secret] = explode('|', $plainToken, 2);

        $token = static::query()->with('user')->find((int) $id);

        if (! $token || ! hash_equals($token->token, static::hash($secret))) {
            return null;
        }

        if ($token->expires_at !== null && $token->expires_at->isPast()) {
            return null;
        }

        return $token;
    }
}
