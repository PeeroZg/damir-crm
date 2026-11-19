<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Powers the Ctrl+K command palette.
 */
class SearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q'));

        if (mb_strlen($term) < 2) {
            return response()->json(['results' => []]);
        }

        $results = collect()
            ->merge(Company::search($term)->limit(5)->get()->map(fn (Company $c) => [
                'type' => __('Company'),
                'title' => $c->name,
                'subtitle' => collect([$c->city, $c->status->label()])->filter()->implode(' · '),
                'url' => route('companies.show', $c),
            ]))
            ->merge(Contact::search($term)->with('company')->limit(5)->get()->map(fn (Contact $c) => [
                'type' => __('Contact'),
                'title' => $c->full_name,
                'subtitle' => collect([$c->position, $c->company?->name])->filter()->implode(' · '),
                'url' => route('contacts.show', $c),
            ]))
            ->merge(Deal::search($term)->with('company')->limit(5)->get()->map(fn (Deal $d) => [
                'type' => __('Deal'),
                'title' => $d->title,
                'subtitle' => $d->company?->name.' · '.money($d->value).' · '.$d->stage->label(),
                'url' => route('deals.show', $d),
            ]))
            ->merge(Offer::search($term)->with('company')->limit(5)->get()->map(fn (Offer $o) => [
                'type' => __('Offer'),
                'title' => $o->number,
                'subtitle' => $o->company?->name.' · '.money($o->total).' · '.$o->status->label(),
                'url' => route('offers.show', $o),
            ]));

        return response()->json(['results' => $results->values()]);
    }
}
