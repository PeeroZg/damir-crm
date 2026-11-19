<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'tokens' => $request->user()->apiTokens()->get(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Shared demo accounts keep their login e-mail so every visitor can sign in.
        if ($user->isDemoAccount() && $data['email'] !== $user->email) {
            return back()->with('error', __('The e-mail of a demo account cannot be changed.'));
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        $request->session()->put('locale', $user->locale);

        return redirect()->route('profile.edit')->with('success', __('Profile saved.'));
    }

    public function storeToken(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100']]);

        abort_if($request->user()->apiTokens()->count() >= 10, 422, __('You can have at most 10 API tokens.'));

        $plain = $request->user()->createApiToken($data['name']);

        return redirect()->route('profile.edit')
            ->with('new_token', $plain)
            ->with('success', __('API token created. Copy it now — it will not be shown again.'));
    }

    public function destroyToken(Request $request, int $token): RedirectResponse
    {
        $request->user()->apiTokens()->whereKey($token)->firstOrFail()->delete();

        return redirect()->route('profile.edit')->with('success', __('API token revoked.'));
    }
}
