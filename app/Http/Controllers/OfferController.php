<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Deal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::with('deal', 'company')->latest()->paginate(15);
        return view('offers.index', compact('offers'));
    }

    public function create(Request $request)
    {
        $deals = Deal::with('company')->orderBy('title')->get();
        $selectedDealId = $request->query('deal_id');
        return view('offers.create', compact('deals', 'selectedDealId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'amount' => 'nullable|numeric|min:0',
            'valid_until' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'status' => 'required|in:nacrt,poslana,prihvacena,odbijena,istekla',
        ]);

        $deal = Deal::findOrFail($validated['deal_id']);
        $validated['company_id'] = $deal->company_id;

        $nextNumber = Offer::max('id') + 1;
        $validated['offer_number'] = 'PON-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $validated['user_id'] = auth()->id();

        $offer = Offer::create($validated);

        if ($deal->status !== 'dobiven' && $deal->status !== 'izgubljen') {
            $deal->update(['status' => 'ponuda_poslana']);
        }

        return redirect()->route('offers.index')->with('success', 'Ponuda je uspješno kreirana.');
    }

    public function show(Offer $offer)
    {
        $offer->load('deal', 'company');
        return view('offers.show', compact('offer'));
    }

    public function edit(Offer $offer)
    {
        abort_if($offer->user_id !== auth()->id(), 403, 'Nemaš pravo uređivati ovu ponudu.');

        $deals = Deal::with('company')->orderBy('title')->get();
        return view('offers.edit', compact('offer', 'deals'));
    }

    public function update(Request $request, Offer $offer)
    {
        abort_if($offer->user_id !== auth()->id(), 403, 'Nemaš pravo uređivati ovu ponudu.');

        $validated = $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'amount' => 'nullable|numeric|min:0',
            'valid_until' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'status' => 'required|in:nacrt,poslana,prihvacena,odbijena,istekla',
        ]);

        $deal = Deal::findOrFail($validated['deal_id']);
        $validated['company_id'] = $deal->company_id;

        $offer->update($validated);

        return redirect()->route('offers.index')->with('success', 'Ponuda je uspješno ažurirana.');
    }

    public function destroy(Offer $offer)
    {
        abort_if($offer->user_id !== auth()->id(), 403, 'Nemaš pravo obrisati ovu ponudu.');

        $offer->delete();
        return redirect()->route('offers.index')->with('success', 'Ponuda je obrisana.');
    }

    public function kanban()
    {
        $offers = Offer::with('deal', 'company')->orderBy('offer_number')->get();
        return view('offers.kanban', compact('offers'));
    }

    public function updateStatus(Request $request, Offer $offer)
    {
        abort_if($offer->user_id !== auth()->id(), 403, 'Nemaš pravo mijenjati status ove ponude.');

        $request->validate(['status' => 'required|in:nacrt,poslana,prihvacena,odbijena,istekla']);
        $offer->update(['status' => $request->status]);
        return redirect()->route('offers.kanban');
    }

    public function pdf(Offer $offer)
    {
        $offer->load('deal', 'company');
        $pdf = Pdf::loadView('offers.pdf', compact('offer'));
        return $pdf->download($offer->offer_number . '.pdf');
    }
}
