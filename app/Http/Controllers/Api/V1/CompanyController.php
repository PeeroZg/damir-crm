<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CompanyStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $companies = Company::query()
            ->search($request->query('q'))
            ->when(CompanyStatus::tryFrom((string) $request->query('status')), fn (Builder $q, $s) => $q->where('status', $s))
            ->with('owner')
            ->withCount('deals')
            ->orderBy('name')
            ->paginate(min($request->integer('per_page', 25), 100));

        return CompanyResource::collection($companies);
    }

    public function show(Company $company): CompanyResource
    {
        return new CompanyResource($company->load(['owner', 'contacts'])->loadCount('deals'));
    }
}
