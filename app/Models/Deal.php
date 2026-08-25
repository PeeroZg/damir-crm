<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'contact_id', 'title', 'description',
        'value', 'status', 'lost_reason', 'closed_at',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }
}
