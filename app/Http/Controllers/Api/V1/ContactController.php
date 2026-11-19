<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $contacts = Contact::query()
            ->search($request->query('q'))
            ->when($request->integer('company_id'), fn (Builder $q, $id) => $q->where('company_id', $id))
            ->with('company')
            ->orderBy('last_name')
            ->paginate(min($request->integer('per_page', 25), 100));

        return ContactResource::collection($contacts);
    }
}
