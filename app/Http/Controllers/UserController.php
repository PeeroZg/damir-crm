<?php

namespace App\Http\Controllers;

use App\Enums\DealStage;
use App\Enums\UserRole;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->withCount(['deals as open_deals_count' => fn (Builder $q) => $q->open()])
            ->withSum(['deals as won_value' => fn (Builder $q) => $q->where('stage', DealStage::Won)], 'value')
            ->orderBy('name')
            ->get();

        return view('users.index', ['users' => $users]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create', ['user' => new User(['role' => UserRole::Sales]), 'roles' => UserRole::cases()]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $user = User::create($request->validated());
        $user->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('users.index')->with('success', __('User :name created.', ['name' => $user->name]));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('users.edit', ['user' => $user, 'roles' => UserRole::cases()]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        // An admin cannot accidentally lock themselves out of user management.
        if ($user->is($request->user())) {
            unset($data['role']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', __('User updated.'));
    }
}
