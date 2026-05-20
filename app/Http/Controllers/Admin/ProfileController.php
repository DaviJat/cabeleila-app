<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Profile\ProfileUpdateRequest;
use App\Http\Requests\Profile\ProfileDestroyRequest;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     *
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Profile/Index');
    }

    /**
     * Update the user's profile information.
     *
     * @param ProfileUpdateRequest $request
     * @return RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        $request->user()->save();

        return Redirect::route('profile.index');
    }

    /**
     * Delete the user's account.
     *
     * @param ProfileDestroyRequest $request
     * @return RedirectResponse
     */
    public function destroy(ProfileDestroyRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Logout before deleting the account to prevent session-related errors
        Auth::logout();

        $user->delete();

        // Invalidate the session and regenerate the CSRF token for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/login');
    }
}
