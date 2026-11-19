<?php

namespace App\Http\Controllers;

use App\Enums\DealStage;
use App\Enums\OfferStatus;
use App\Http\Requests\OfferRequest;
use App\Models\Deal;
use App\Models\Offer;
use App\Services\OfferService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function __construct(private readonly OfferService $offers) {}

    public function index(Request $request): View
    {
        $offers = Offer::query()
            ->search($request->query('q'))
            ->when(OfferStatus::tryFrom((string) $request->query('status')), fn (Builder $q, $status) => $q->where('status', $status))
            ->when($request->query('owner') === 'me', fn (Builder $q) => $q->where('owner_id', $request->user()->id))
            ->with(['company', 'deal', 'owner'])
            ->latest('issued_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('offers.index', [
            'offers' => $offers,
            'statuses' => OfferStatus::cases(),
        ]);
    }

    public function kanban(Request $request): View
    {
        $offers = Offer::query()
            ->search($request->query('q'))
            ->with(['company', 'deal'])
            ->latest('issued_at')
            ->get()
            ->groupBy(fn (Offer $o) => $o->status->value);

        return view('offers.kanban', [
            'columns' => OfferStatus::cases(),
            'offers' => $offers,
        ]);
    }

    public function create(Request $request): View
    {
        $deal = $request->filled('deal_id') ? Deal::with('company')->findOrFail($request->integer('deal_id')) : null;

        if ($deal) {
            $this->authorize('create', [Offer::class, $deal]);
        }

        return view('offers.create', [
            'offer' => new Offer([
                'deal_id' => $deal?->id,
                'issued_at' => now(),
                'valid_until' => now()->addDays(config('crm.offer_validity_days')),
                'vat_rate' => config('crm.vat_rate'),
            ]),
            'items' => [['description' => $deal?->title ?? '', 'quantity' => 1, 'unit' => 'pcs', 'unit_price' => (float) ($deal?->value ?? 0), 'discount_percent' => 0]],
            'deals' => $this->dealOptions($request, $deal?->id),
        ]);
    }

    public function store(OfferRequest $request): RedirectResponse
    {
        $deal = Deal::findOrFail($request->integer('deal_id'));
        $this->authorize('create', [Offer::class, $deal]);

        $offer = $this->offers->create($deal, $request->user(), $request->validated(), $request->validated('items'));

        return redirect()->route('offers.show', $offer)->with('success', __('Offer :number created.', ['number' => $offer->number]));
    }

    public function show(Offer $offer): View
    {
        $offer->load(['company', 'deal.contact', 'owner', 'items', 'parent', 'revisions', 'auditLogs.user']);

        return view('offers.show', [
            'offer' => $offer,
            'statuses' => OfferStatus::cases(),
        ]);
    }

    public function edit(Offer $offer): View|RedirectResponse
    {
        if (! $offer->isEditable()) {
            return redirect()->route('offers.show', $offer)
                ->with('error', __('Only draft offers can be edited. Create a new revision instead.'));
        }

        $this->authorize('update', $offer);

        return view('offers.edit', [
            'offer' => $offer,
            'items' => $offer->items->map->only(['description', 'quantity', 'unit', 'unit_price', 'discount_percent'])->all(),
        ]);
    }

    public function update(OfferRequest $request, Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);

        $this->offers->update($offer, $request->validated(), $request->validated('items'));

        return redirect()->route('offers.show', $offer)->with('success', __('Offer updated.'));
    }

    public function destroy(Offer $offer): RedirectResponse
    {
        $this->authorize('delete', $offer);

        $offer->delete();

        return redirect()->route('offers.index')->with('success', __('Offer deleted.'));
    }

    public function updateStatus(Request $request, Offer $offer): JsonResponse|RedirectResponse
    {
        $this->authorize('changeStatus', $offer);

        $data = $request->validate(['status' => ['required', Rule::enum(OfferStatus::class)]]);
        $this->offers->changeStatus($offer, OfferStatus::from($data['status']));

        $message = $offer->status === OfferStatus::Accepted && $offer->deal?->stage === DealStage::Won
            ? __('Offer accepted — the deal is marked as won.')
            : __('Status updated.');

        return $request->expectsJson()
            ? response()->json(['message' => $message, 'status' => $offer->status->value])
            : back()->with('success', $message);
    }

    public function revise(Request $request, Offer $offer): RedirectResponse
    {
        $this->authorize('revise', $offer);

        $revision = $this->offers->createRevision($offer->load('items'), $request->user());

        return redirect()->route('offers.edit', $revision)
            ->with('success', __('Revision :number created. Adjust it and send it again.', ['number' => $revision->number]));
    }

    public function pdf(Request $request, Offer $offer): Response
    {
        if (in_array($request->query('lang'), array_keys(config('crm.locales')), true)) {
            app()->setLocale($request->query('lang'));
        }

        $offer->load(['company', 'deal.contact', 'owner', 'items']);

        return Pdf::loadView('offers.pdf', ['offer' => $offer, 'seller' => config('crm.seller')])
            ->setPaper('a4')
            ->download($offer->number.'.pdf');
    }

    /** Deals the current user may prepare offers for (all deals for admins). */
    private function dealOptions(Request $request, ?int $includeDealId = null)
    {
        $user = $request->user();

        return Deal::query()
            ->with('company')
            ->when(! $user->isAdmin(), fn (Builder $q) => $q->where('owner_id', $user->id))
            ->where(fn (Builder $q) => $q->open()->when($includeDealId, fn (Builder $q) => $q->orWhere('id', $includeDealId)))
            ->orderBy('title')
            ->get();
    }
}
