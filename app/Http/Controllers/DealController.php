<?php

namespace App\Http\Controllers;

use App\Enums\DealStage;
use App\Http\Requests\DealRequest;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\User;
use App\Support\Csv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DealController extends Controller
{
    private const SORTABLE = ['title', 'value', 'stage', 'expected_close_date', 'created_at'];

    public function index(Request $request): View
    {
        return view('deals.index', [
            'deals' => $this->filteredQuery($request)->with(['company', 'owner'])->paginate(15)->withQueryString(),
            'stages' => DealStage::cases(),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function kanban(Request $request): View
    {
        $deals = Deal::query()
            ->search($request->query('q'))
            ->when($request->query('owner') === 'me', fn (Builder $q) => $q->where('owner_id', $request->user()->id))
            ->with(['company', 'owner'])
            ->orderByDesc('value')
            ->get()
            ->groupBy(fn (Deal $d) => $d->stage->value)
            // Closed columns show the most recent deals first (only the latest few are rendered).
            ->map(fn ($group, $stage) => DealStage::from($stage)->isClosed() ? $group->sortByDesc('closed_at')->values() : $group);

        return view('deals.kanban', [
            'columns' => DealStage::cases(),
            'deals' => $deals,
        ]);
    }

    public function create(Request $request): View
    {
        $deal = new Deal([
            'stage' => DealStage::Lead,
            'company_id' => $request->integer('company_id') ?: null,
            'contact_id' => $request->integer('contact_id') ?: null,
            'expected_close_date' => now()->addMonth(),
        ]);

        if ($deal->contact_id && ! $deal->company_id) {
            $deal->company_id = Contact::find($deal->contact_id)?->company_id;
        }

        return view('deals.create', $this->formData($deal));
    }

    public function store(DealRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['owner_id'] = $request->user()->can('reassign', Deal::class) && ! empty($data['owner_id'])
            ? $data['owner_id']
            : $request->user()->id;

        $deal = Deal::create($data);

        return redirect()->route('deals.show', $deal)->with('success', __('Deal created.'));
    }

    public function show(Deal $deal): View
    {
        $deal->load([
            'company',
            'contact',
            'owner',
            'offers' => fn ($q) => $q->with('owner'),
            'activities' => fn ($q) => $q->with(['author', 'assignee']),
            'auditLogs' => fn ($q) => $q->with('user')->limit(20),
        ]);

        return view('deals.show', [
            'deal' => $deal,
            'stages' => DealStage::cases(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function edit(Deal $deal): View
    {
        $this->authorize('update', $deal);

        return view('deals.edit', $this->formData($deal));
    }

    public function update(DealRequest $request, Deal $deal): RedirectResponse
    {
        $this->authorize('update', $deal);

        $data = $request->validated();

        if (! $request->user()->can('reassign', Deal::class) || empty($data['owner_id'])) {
            unset($data['owner_id']);
        }

        $deal->update($data);

        return redirect()->route('deals.show', $deal)->with('success', __('Deal updated.'));
    }

    public function destroy(Deal $deal): RedirectResponse
    {
        $this->authorize('delete', $deal);

        $deal->delete();

        return redirect()->route('deals.index')->with('success', __('Deal deleted.'));
    }

    /** Used by the kanban board (JSON) and the stage buttons on the deal page (redirect). */
    public function updateStage(Request $request, Deal $deal): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $deal);

        $data = $request->validate([
            'stage' => ['required', Rule::enum(DealStage::class)],
            'lost_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $deal->moveTo(DealStage::from($data['stage']), $data['lost_reason'] ?? null);

        if ($request->expectsJson()) {
            $won = $deal->stage === DealStage::Won;

            return response()->json([
                'message' => __('Deal moved to :stage.', ['stage' => $deal->stage->label()]),
                'stage' => $deal->stage->value,
                'toast' => [
                    'type' => $won ? 'won' : 'success',
                    'title' => $won ? __('Deal won — nice work!') : __('Stage updated'),
                    'body' => __(':deal moved to :stage.', ['deal' => $deal->title, 'stage' => $deal->stage->label()]),
                ],
            ]);
        }

        return back()->with('success', __('Deal moved to :stage.', ['stage' => $deal->stage->label()]));
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filteredQuery($request)->with(['company', 'contact', 'owner'])->lazy()->map(fn (Deal $d) => [
            $d->title, $d->company?->name, $d->contact?->full_name, $d->stage->label(),
            $d->value, $d->stage->probability(), $d->expected_close_date?->toDateString(),
            $d->owner?->name, $d->closed_at?->toDateString(), $d->created_at->toDateString(),
        ]);

        return Csv::download('deals-'.now()->format('Y-m-d').'.csv', [
            __('Title'), __('Company'), __('Contact'), __('Stage'), __('Value'), __('Probability').' %',
            __('Expected close'), __('Owner'), __('Closed'), __('Created'),
        ], $rows);
    }

    /** @return array<string, mixed> */
    private function formData(Deal $deal): array
    {
        return [
            'deal' => $deal,
            'companies' => Company::orderBy('name')->get(['id', 'name']),
            'contacts' => Contact::orderBy('last_name')->get(['id', 'company_id', 'first_name', 'last_name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ];
    }

    private function filteredQuery(Request $request): Builder
    {
        $sort = in_array($request->query('sort'), self::SORTABLE, true) ? $request->query('sort') : 'created_at';
        $direction = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        return Deal::query()
            ->search($request->query('q'))
            ->when(DealStage::tryFrom((string) $request->query('stage')), fn (Builder $q, $stage) => $q->where('stage', $stage))
            ->when($request->query('stage') === 'open', fn (Builder $q) => $q->open())
            ->when($request->query('owner') === 'me', fn (Builder $q) => $q->where('owner_id', $request->user()->id))
            ->when(is_numeric($request->query('owner')), fn (Builder $q) => $q->where('owner_id', (int) $request->query('owner')))
            ->when($request->integer('company'), fn (Builder $q, $id) => $q->where('company_id', $id))
            ->orderBy($sort, $direction)
            ->orderByDesc('id');
    }
}
