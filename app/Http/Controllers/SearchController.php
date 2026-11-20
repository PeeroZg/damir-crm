<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Powers the Ctrl+K command palette. Results are grouped by type.
 */
class SearchController extends Controller
{
    private const AVATAR_COLORS = ['sky', 'green', 'indigo', 'violet', 'amber', 'rose'];

    public function __invoke(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q'));

        if (mb_strlen($term) < 2) {
            return response()->json(['results' => []]);
        }

        $results = collect()
            ->merge(Company::search($term)->withCount('deals')->limit(5)->get()->map(fn (Company $c) => [
                'group' => __('Companies'),
                'kind' => 'company',
                'icon' => 'building',
                'title' => $c->name,
                'subtitle' => collect([$c->industry, $c->city, trans_choice('{0} No deals|{1} :count deal|[2,*] :count deals', $c->deals_count, ['count' => $c->deals_count])])->filter()->implode(' · '),
                'pill' => ['tone' => $c->status->color(), 'label' => $c->status->label()],
                'url' => route('companies.show', $c),
            ]))
            ->merge(Deal::search($term)->with('owner')->limit(5)->get()->map(fn (Deal $d) => [
                'group' => __('Deals'),
                'kind' => 'deal',
                'icon' => 'briefcase',
                'title' => $d->title,
                'subtitle' => collect([money($d->value), $d->owner?->name])->filter()->implode(' · '),
                'pill' => ['tone' => $d->stage->color(), 'label' => $d->stage->label()],
                'url' => route('deals.show', $d),
            ]))
            ->merge(Contact::search($term)->with('company')->limit(5)->get()->map(fn (Contact $c) => [
                'group' => __('Contacts'),
                'kind' => 'contact',
                'icon' => 'user',
                'initials' => $c->initials,
                'avatar' => self::AVATAR_COLORS[crc32($c->full_name) % count(self::AVATAR_COLORS)],
                'title' => $c->full_name,
                'subtitle' => collect([$c->position, $c->company?->name])->filter()->implode(' · '),
                'url' => route('contacts.show', $c),
            ]))
            ->merge(Offer::search($term)->with('deal')->limit(5)->get()->map(fn (Offer $o) => [
                'group' => __('Offers'),
                'kind' => 'offer',
                'icon' => 'document',
                'title' => $o->number,
                'subtitle' => collect([$o->deal?->title, money($o->total)])->filter()->implode(' · '),
                'pill' => ['tone' => $o->status->color(), 'label' => $o->status->label()],
                'url' => route('offers.show', $o),
            ]));

        return response()->json(['results' => $results->values()]);
    }
}
