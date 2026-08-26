<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function index()
    {
        $deals = Deal::with('company', 'contact')->latest()->paginate(15);
        return view('deals.index', compact('deals'));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $contacts = Contact::with('company')->orderBy('first_name')->get();
        return view('deals.create', compact('companies', 'contacts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'nullable|numeric|min:0',
            'status' => 'required|in:lead,kvalificiran,ponuda_poslana,pregovori,dobiven,izgubljen',
        ]);

        Deal::create($validated);

        return redirect()->route('deals.index')->with('success', 'Deal je uspješno dodan.');
    }

    public function show(Deal $deal)
    {
        $deal->load('company', 'contact');
        return view('deals.show', compact('deal'));
    }

    public function edit(Deal $deal)
    {
        $companies = Company::orderBy('name')->get();
        $contacts = Contact::with('company')->orderBy('first_name')->get();
        return view('deals.edit', compact('deal', 'companies', 'contacts'));
    }

    public function update(Request $request, Deal $deal)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'nullable|numeric|min:0',
            'status' => 'required|in:lead,kvalificiran,ponuda_poslana,pregovori,dobiven,izgubljen',
            'lost_reason' => 'nullable|string|max:255',
        ]);

        if (in_array($validated['status'], ['dobiven', 'izgubljen']) && !$deal->closed_at) {
            $validated['closed_at'] = now();
        }

        $deal->update($validated);

        return redirect()->route('deals.index')->with('success', 'Deal je uspješno ažuriran.');
    }

    public function destroy(Deal $deal)
    {
        $deal->delete();
        return redirect()->route('deals.index')->with('success', 'Deal je obrisan.');
    }

    public function kanban()
    {
        $deals = Deal::with('company')->orderBy('title')->get();
        return view('deals.kanban', compact('deals'));
    }

    public function updateStatus(Request $request, Deal $deal)
    {
        $request->validate(['status' => 'required|in:lead,kvalificiran,ponuda_poslana,pregovori,dobiven,izgubljen']);

        $data = ['status' => $request->status];
        if (in_array($request->status, ['dobiven', 'izgubljen']) && !$deal->closed_at) {
            $data['closed_at'] = now();
        }

        $deal->update($data);

        return redirect()->route('deals.kanban');
    }
}
