<?php

namespace App\Models;

use App\Enums\OfferStatus;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'deal_id', 'company_id', 'owner_id', 'parent_id', 'number', 'revision', 'status',
        'issued_at', 'valid_until', 'currency', 'vat_rate', 'subtotal', 'vat_amount', 'total',
        'notes', 'sent_at',
    ];

    protected array $auditExclude = ['subtotal', 'vat_amount', 'sent_at'];

    protected $attributes = [
        'status' => 'draft',
        'revision' => 1,
    ];

    protected function casts(): array
    {
        return [
            'status' => OfferStatus::class,
            'issued_at' => 'date',
            'valid_until' => 'date',
            'sent_at' => 'datetime',
            'vat_rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class)->withTrashed();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'parent_id')->withTrashed();
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(Offer::class, 'parent_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OfferItem::class)->orderBy('position');
    }

    public function scopeSearch(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $query->where(function (Builder $q) use ($term) {
            $q->where('number', 'like', "%{$term}%")
                ->orWhereHas('company', fn (Builder $c) => $c->where('name', 'like', "%{$term}%"))
                ->orWhereHas('deal', fn (Builder $d) => $d->where('title', 'like', "%{$term}%"));
        });
    }

    /** Only drafts can be edited; anything already sent gets a new revision instead. */
    public function isEditable(): bool
    {
        return $this->status === OfferStatus::Draft;
    }

    public function isExpired(): bool
    {
        return $this->valid_until !== null && $this->valid_until->endOfDay()->isPast();
    }
}
