<?php

namespace App\Services;

use App\Enums\DealStage;
use App\Enums\OfferStatus;
use App\Models\Activity;
use App\Models\AuditLog;
use App\Models\Deal;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Numbers shown on the dashboard. Optionally scoped to one sales rep ("my pipeline").
 */
class DashboardMetrics
{
    public function __construct(private readonly ?User $owner = null) {}

    public static function for(?User $owner): self
    {
        return new self($owner);
    }

    /** @return array<string, float|int|null> */
    public function kpis(): array
    {
        $open = $this->deals()->open()->get(['value', 'stage']);
        $closedThisYear = $this->deals()
            ->whereIn('stage', [DealStage::Won, DealStage::Lost])
            ->where('closed_at', '>=', now()->startOfYear())
            ->get(['value', 'stage']);

        $won = $closedThisYear->where('stage', DealStage::Won);
        $lostCount = $closedThisYear->where('stage', DealStage::Lost)->count();
        $decided = $won->count() + $lostCount;

        return [
            'open_value' => (float) $open->sum('value'),
            'open_count' => $open->count(),
            'weighted_value' => (float) $open->sum(fn (Deal $d) => $d->weightedValue()),
            'won_value_ytd' => (float) $won->sum('value'),
            'won_count_ytd' => $won->count(),
            'win_rate' => $decided > 0 ? round($won->count() / $decided * 100) : null,
            'avg_won' => $won->count() > 0 ? round((float) $won->avg('value'), 2) : null,
            'offers_pending' => $this->offers()->where('status', OfferStatus::Sent)->count(),
            'offers_pending_value' => (float) $this->offers()->where('status', OfferStatus::Sent)->sum('total'),
        ];
    }

    /**
     * @return Collection<int, array{stage: DealStage, count: int, value: float}>
     */
    public function pipelineByStage(): Collection
    {
        $rows = $this->deals()
            ->selectRaw('stage, COUNT(*) as aggregate_count, COALESCE(SUM(value), 0) as aggregate_value')
            ->groupBy('stage')
            ->toBase()
            ->get()
            ->keyBy('stage');

        return collect(DealStage::cases())->map(fn (DealStage $stage) => [
            'stage' => $stage,
            'count' => (int) ($rows[$stage->value]->aggregate_count ?? 0),
            'value' => (float) ($rows[$stage->value]->aggregate_value ?? 0),
        ]);
    }

    /**
     * Won revenue per month. Grouped in PHP so it works the same on MySQL and SQLite.
     *
     * @return Collection<int, array{month: Carbon, value: float, count: int}>
     */
    public function wonByMonth(int $months = 6): Collection
    {
        $start = now()->startOfMonth()->subMonths($months - 1);

        $deals = $this->deals()
            ->where('stage', DealStage::Won)
            ->where('closed_at', '>=', $start)
            ->get(['value', 'closed_at']);

        return collect(range(0, $months - 1))->map(function (int $i) use ($start, $deals) {
            $month = $start->copy()->addMonths($i);
            $inMonth = $deals->filter(fn (Deal $d) => $d->closed_at->isSameMonth($month));

            return [
                'month' => $month,
                'value' => (float) $inMonth->sum('value'),
                'count' => $inMonth->count(),
            ];
        });
    }

    /** @return Collection<int, Activity> */
    public function upcomingTasks(User $user, int $limit = 6): Collection
    {
        return Activity::query()
            ->tasks()
            ->open()
            ->where('assigned_to', $user->id)
            ->with('subject')
            ->orderByRaw('due_at IS NULL')
            ->orderBy('due_at')
            ->limit($limit)
            ->get();
    }

    /** @return Collection<int, Deal> */
    public function closingSoon(int $limit = 5): Collection
    {
        return $this->deals()
            ->open()
            ->whereNotNull('expected_close_date')
            ->with('company')
            ->orderBy('expected_close_date')
            ->limit($limit)
            ->get();
    }

    /** @return Collection<int, AuditLog> */
    public function recentActivity(int $limit = 8): Collection
    {
        return AuditLog::query()
            ->with(['user', 'auditable'])
            ->when($this->owner, fn (Builder $q) => $q->where('user_id', $this->owner->id))
            ->latest('created_at')
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    private function deals(): Builder
    {
        return Deal::query()->when($this->owner, fn (Builder $q) => $q->where('owner_id', $this->owner->id));
    }

    private function offers(): Builder
    {
        return Offer::query()->when($this->owner, fn (Builder $q) => $q->where('owner_id', $this->owner->id));
    }
}
