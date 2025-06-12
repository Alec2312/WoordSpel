<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    /**
     * Toon profielpagina met user-variabele.
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Update naam en e-mail.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $user = $request->user();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('status', 'Profiel bijgewerkt.');
    }

    /**
     * Update wachtwoord.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('status', 'Wachtwoord gewijzigd.');
    }

    /**
     * Profielfoto uploaden/vervangen.
     */
    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile' => ['required', 'image', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user->profile) {
            $old = str_replace('storage/', '', $user->profile);
            if (Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
        }

        $path = $request->file('profile')->store('profile_photos', 'public');
        $user->profile = 'storage/' . $path;
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'Profielfoto geüpload.');
    }

    /**
     * Verwijder account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Account verwijderd.');
    }
}
