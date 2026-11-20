<?php

namespace App\Http\Controllers;

use App\Enums\CompanyStatus;
use App\Enums\DealStage;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\User;
use App\Support\Csv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanyController extends Controller
{
    private const SORTABLE = ['name', 'city', 'status', 'created_at', 'deals_count'];

    public function index(Request $request): View
    {
        $companies = $this->filteredQuery($request)
            ->with('owner')
            ->withCount(['contacts', 'deals'])
            ->withSum(['deals as won_value' => fn (Builder $q) => $q->where('stage', DealStage::Won)], 'value')
            ->paginate(15)
            ->withQueryString();

        return view('companies.index', [
            'companies' => $companies,
            'statuses' => CompanyStatus::cases(),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function kanban(Request $request): View
    {
        $companies = Company::query()
            ->search($request->query('q'))
            ->with('owner')
            ->withCount('deals')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Company $c) => $c->status->value);

        return view('companies.kanban', [
            'columns' => CompanyStatus::cases(),
            'companies' => $companies,
        ]);
    }

    public function create(): View
    {
        return view('companies.create', [
            'company' => new Company(['status' => CompanyStatus::Lead, 'owner_id' => auth()->id()]),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(CompanyRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['owner_id'] ??= $request->user()->id;

        $company = Company::create($data);

        return redirect()->route('companies.show', $company)
            ->with('success', __('Company created.'));
    }

    public function show(Company $company): View
    {
        $company->load([
            'owner',
            'contacts' => fn ($q) => $q->orderBy('last_name'),
            'deals' => fn ($q) => $q->with('owner')->latest(),
            'offers' => fn ($q) => $q->with('deal')->latest('id'),
            'activities' => fn ($q) => $q->with(['author', 'assignee']),
            'auditLogs' => fn ($q) => $q->with('user')->limit(20),
        ]);

        return view('companies.show', [
            'company' => $company,
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function edit(Company $company): View
    {
        return view('companies.edit', [
            'company' => $company,
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function update(CompanyRequest $request, Company $company): RedirectResponse
    {
        $this->authorize('update', $company);

        $company->update($request->validated());

        return redirect()->route('companies.show', $company)
            ->with('success', __('Company updated.'));
    }

    public function destroy(Company $company): RedirectResponse
    {
        $this->authorize('delete', $company);

        if ($company->deals()->open()->exists()) {
            return back()->with('error', __('This company still has open deals. Close or move them first.'));
        }

        $company->delete();

        return redirect()->route('companies.index')->with('success', __('Company deleted.'));
    }

    public function updateStatus(Request $request, Company $company): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $company);

        $data = $request->validate(['status' => ['required', Rule::enum(CompanyStatus::class)]]);
        $company->update($data);

        return $request->expectsJson()
            ? response()->json([
                'message' => __('Status updated.'),
                'status' => $company->status->value,
                'toast' => ['title' => __('Status updated'), 'body' => __(':deal moved to :stage.', ['deal' => $company->name, 'stage' => $company->status->label()])],
            ])
            : back()->with('success', __('Status updated.'));
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filteredQuery($request)->with('owner')->withCount('deals')->lazy()->map(fn (Company $c) => [
            $c->name, $c->oib, $c->status->label(), $c->industry, $c->email, $c->phone,
            $c->website, $c->address, $c->city, $c->owner?->name, $c->deals_count, $c->created_at->toDateString(),
        ]);

        return Csv::download('companies-'.now()->format('Y-m-d').'.csv', [
            __('Name'), 'OIB', __('Status'), __('Industry'), __('Email'), __('Phone'),
            __('Website'), __('Address'), __('City'), __('Owner'), __('Deals'), __('Created'),
        ], $rows);
    }

    private function filteredQuery(Request $request): Builder
    {
        $sort = in_array($request->query('sort'), self::SORTABLE, true) ? $request->query('sort') : 'name';
        $direction = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        return Company::query()
            ->search($request->query('q'))
            ->when(CompanyStatus::tryFrom((string) $request->query('status')), fn (Builder $q, $status) => $q->where('status', $status))
            ->when($request->query('owner') === 'me', fn (Builder $q) => $q->where('owner_id', $request->user()->id))
            ->when(is_numeric($request->query('owner')), fn (Builder $q) => $q->where('owner_id', (int) $request->query('owner')))
            ->orderBy($sort, $direction)
            ->orderBy('id');
    }
}
