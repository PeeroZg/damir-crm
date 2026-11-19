<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

/**
 * Records created / updated / deleted / restored events into audit_logs,
 * including a field-level diff for updates.
 *
 * @mixin Model
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn (Model $model) => $model->writeAudit('created'));

        static::updated(function (Model $model) {
            $diff = $model->auditDiff();

            if ($diff !== []) {
                $model->writeAudit('updated', $diff);
            }
        });

        static::deleted(function (Model $model) {
            // A soft delete fires "updated" for deleted_at first; we only want one clear entry.
            $model->writeAudit('deleted');
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(fn (Model $model) => $model->writeAudit('restored'));
        }
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest('created_at')->latest('id');
    }

    /** Run a callback without writing audit entries for any model (used by seeders and imports). */
    public static function withoutAuditing(callable $callback): mixed
    {
        $previous = AuditLog::$recording;
        AuditLog::$recording = false;

        try {
            return $callback();
        } finally {
            AuditLog::$recording = $previous;
        }
    }

    /** @return array<string, array{old: mixed, new: mixed}> */
    protected function auditDiff(): array
    {
        $ignored = array_merge(
            ['created_at', 'updated_at', 'deleted_at'],
            $this->hidden,
            property_exists($this, 'auditExclude') ? $this->auditExclude : [],
        );

        $diff = [];

        foreach ($this->getChanges() as $field => $new) {
            if (in_array($field, $ignored, true)) {
                continue;
            }

            $diff[$field] = [
                'old' => $this->normalizeAuditValue($this->getPrevious()[$field] ?? null),
                'new' => $this->normalizeAuditValue($new),
            ];
        }

        return $diff;
    }

    protected function writeAudit(string $event, ?array $changes = null): void
    {
        if (! AuditLog::$recording) {
            return;
        }

        AuditLog::create([
            'auditable_type' => $this->getMorphClass(),
            'auditable_id' => $this->getKey(),
            'user_id' => Auth::id(),
            'event' => $event,
            'diff' => $changes,
            'created_at' => now(),
        ]);
    }

    private function normalizeAuditValue(mixed $value): mixed
    {
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return $value;
    }
}
