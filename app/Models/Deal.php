<?php

namespace App\Models;

use App\Enums\DealStage;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasActivities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use Auditable, HasActivities, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'contact_id', 'owner_id', 'title', 'description',
        'value', 'stage', 'expected_close_date', 'lost_reason',
    ];

    /** stage_changed_at / closed_at are derived from stage, so the stage diff is enough. */
    protected array $auditExclude = ['stage_changed_at', 'closed_at'];

    protected $attributes = [
        'stage' => 'lead',
    ];

    protected function casts(): array
    {
        return [
            'stage' => DealStage::class,
            'value' => 'decimal:2',
            'expected_close_date' => 'date',
            'stage_changed_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Keep closed_at / stage_changed_at consistent no matter how the stage is changed.
        static::saving(function (Deal $deal) {
            if (! $deal->isDirty('stage') && $deal->exists) {
                return;
            }

            $deal->stage_changed_at = now();

            if ($deal->stage->isClosed()) {
                $deal->closed_at ??= now();
            } else {
                $deal->closed_at = null;
            }

            if ($deal->stage !== DealStage::Lost) {
                $deal->lost_reason = null;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class)->withTrashed();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class)->latest('id');
    }

    public function scopeOpen(Builder $query): void
    {
        $query->whereIn('stage', DealStage::open());
    }

    public function scopeSearch(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhereHas('company', fn (Builder $c) => $c->where('name', 'like', "%{$term}%"));
        });
    }

    public function isOpen(): bool
    {
        return ! $this->stage->isClosed();
    }

    /**
     * Move the deal to another stage. Returns false when nothing changed.
     */
    public function moveTo(DealStage $stage, ?string $lostReason = null): bool
    {
        if ($this->stage === $stage && ($stage !== DealStage::Lost || $lostReason === null)) {
            return false;
        }

        $this->stage = $stage;

        if ($stage === DealStage::Lost) {
            $this->lost_reason = $lostReason;
        }

        return $this->save();
    }

    /**
     * Only ever move forward automatically (e.g. sending an offer must not pull
     * a deal in negotiation back to "proposal").
     */
    public function advanceTo(DealStage $stage): bool
    {
        if (! $this->isOpen() || $this->stage->order() >= $stage->order()) {
            return false;
        }

        return $this->moveTo($stage);
    }

    public function weightedValue(): float
    {
        return round((float) $this->value * $this->stage->probability() / 100, 2);
    }
}
