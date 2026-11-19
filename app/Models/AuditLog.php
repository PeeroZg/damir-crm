<?php

namespace App\Models;

use App\Enums\CompanyStatus;
use App\Enums\ContactStatus;
use App\Enums\DealStage;
use App\Enums\OfferStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    public const UPDATED_AT = null;

    /** Global switch used by Auditable::withoutAuditing(). */
    public static bool $recording = true;

    protected $fillable = ['auditable_type', 'auditable_id', 'user_id', 'event', 'diff', 'created_at'];

    protected function casts(): array
    {
        return [
            'diff' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Human readable label of the model type, e.g. "Deal". */
    public function subjectLabel(): string
    {
        return __(ucfirst($this->auditable_type));
    }

    public function subjectTitle(): string
    {
        $model = $this->auditable;

        return match (true) {
            $model instanceof Company => $model->name,
            $model instanceof Contact => $model->full_name,
            $model instanceof Deal => $model->title,
            $model instanceof Offer => $model->number,
            default => '#'.$this->auditable_id,
        };
    }

    public function subjectUrl(): ?string
    {
        if (! $this->auditable || $this->auditable->trashed()) {
            return null;
        }

        return route(str($this->auditable_type)->plural().'.show', $this->auditable_id);
    }

    public function eventLabel(): string
    {
        if ($this->event === 'updated' && $this->isStatusChange()) {
            return __('moved');
        }

        return match ($this->event) {
            'created' => __('created'),
            'updated' => __('updated'),
            'deleted' => __('deleted'),
            'restored' => __('restored'),
            default => $this->event,
        };
    }

    public function isStatusChange(): bool
    {
        $fields = array_keys($this->diff ?? []);

        return $fields !== [] && array_diff($fields, ['stage', 'status', 'lost_reason', 'value']) === []
            && (in_array('stage', $fields, true) || in_array('status', $fields, true));
    }

    /**
     * @return list<array{field: string, old: string, new: string}>
     */
    public function formattedChanges(): array
    {
        return collect($this->diff ?? [])->map(fn (array $change, string $field) => [
            'field' => __(ucfirst(str_replace(['_id', '_'], ['', ' '], $field))),
            'old' => $this->formatValue($field, $change['old'] ?? null),
            'new' => $this->formatValue($field, $change['new'] ?? null),
        ])->values()->all();
    }

    private function formatValue(string $field, mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        $enum = match (true) {
            $field === 'stage' => DealStage::class,
            $field === 'status' && $this->auditable_type === 'company' => CompanyStatus::class,
            $field === 'status' && $this->auditable_type === 'contact' => ContactStatus::class,
            $field === 'status' && $this->auditable_type === 'offer' => OfferStatus::class,
            default => null,
        };

        if ($enum && ($case = $enum::tryFrom((string) $value))) {
            return $case->label();
        }

        if (in_array($field, ['value', 'total'], true)) {
            return money($value);
        }

        return str((string) $value)->limit(60)->toString();
    }
}
