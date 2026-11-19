<?php

namespace App\Http\Controllers;

use App\Enums\ContactStatus;
use App\Http\Requests\ContactRequest;
use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use App\Support\Csv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactController extends Controller
{
    private const SORTABLE = ['last_name', 'status', 'created_at'];

    public function index(Request $request): View
    {
        return view('contacts.index', [
            'contacts' => $this->filteredQuery($request)->with('company')->paginate(15)->withQueryString(),
            'statuses' => ContactStatus::cases(),
        ]);
    }

    public function kanban(Request $request): View
    {
        $contacts = Contact::query()
            ->search($request->query('q'))
            ->with('company')
            ->orderBy('last_name')
            ->get()
            ->groupBy(fn (Contact $c) => $c->status->value);

        return view('contacts.kanban', [
            'columns' => ContactStatus::cases(),
            'contacts' => $contacts,
        ]);
    }

    public function create(Request $request): View
    {
        return view('contacts.create', [
            'contact' => new Contact([
                'status' => ContactStatus::New,
                'company_id' => $request->integer('company_id') ?: null,
            ]),
            'companies' => Company::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $contact = Contact::create($request->validated());

        return redirect()->route('contacts.show', $contact)->with('success', __('Contact created.'));
    }

    public function show(Contact $contact): View
    {
        $contact->load([
            'company',
            'deals' => fn ($q) => $q->with('owner')->latest(),
            'activities' => fn ($q) => $q->with(['author', 'assignee']),
            'auditLogs' => fn ($q) => $q->with('user')->limit(20),
        ]);

        return view('contacts.show', [
            'contact' => $contact,
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function edit(Contact $contact): View
    {
        return view('contacts.edit', [
            'contact' => $contact,
            'companies' => Company::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(ContactRequest $request, Contact $contact): RedirectResponse
    {
        $this->authorize('update', $contact);

        $contact->update($request->validated());

        return redirect()->route('contacts.show', $contact)->with('success', __('Contact updated.'));
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $this->authorize('delete', $contact);

        $contact->delete();

        return redirect()->route('contacts.index')->with('success', __('Contact deleted.'));
    }

    public function updateStatus(Request $request, Contact $contact): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $contact);

        $data = $request->validate(['status' => ['required', Rule::enum(ContactStatus::class)]]);
        $contact->update($data);

        return $request->expectsJson()
            ? response()->json(['message' => __('Status updated.'), 'status' => $contact->status->value])
            : back()->with('success', __('Status updated.'));
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filteredQuery($request)->with('company')->lazy()->map(fn (Contact $c) => [
            $c->first_name, $c->last_name, $c->company?->name, $c->position,
            $c->email, $c->phone, $c->status->label(), $c->created_at->toDateString(),
        ]);

        return Csv::download('contacts-'.now()->format('Y-m-d').'.csv', [
            __('First name'), __('Last name'), __('Company'), __('Position'),
            __('Email'), __('Phone'), __('Status'), __('Created'),
        ], $rows);
    }

    private function filteredQuery(Request $request): Builder
    {
        $sort = in_array($request->query('sort'), self::SORTABLE, true) ? $request->query('sort') : 'last_name';
        $direction = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        return Contact::query()
            ->search($request->query('q'))
            ->when(ContactStatus::tryFrom((string) $request->query('status')), fn (Builder $q, $status) => $q->where('status', $status))
            ->when($request->integer('company'), fn (Builder $q, $id) => $q->where('company_id', $id))
            ->orderBy($sort, $direction)
            ->orderBy('first_name')
            ->orderBy('id');
    }
}
