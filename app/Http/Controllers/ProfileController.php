<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules; // Nodig voor Rules\Password::defaults()

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
            'email' => 'required|email|max:255|unique:users,email,' . $request->user()->id, // Zorg dat email uniek is, behalve voor de huidige gebruiker
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
        // current_password is een ingebouwde Laravel validatieregel die controleert of het ingevoerde wachtwoord
        // overeenkomt met het gehashte wachtwoord van de ingelogde gebruiker.
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
            'profile' => ['required', 'image', 'max:2048'], // Max 2MB
        ]);

        $user = $request->user();

        // Verwijder oude profielfoto als deze bestaat
        if ($user->profile) {
            // "storage/" prefix verwijderen omdat Storage::disk('public') al in de 'public' directory werkt
            $oldPath = str_replace('storage/', '', $user->profile);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // Sla de nieuwe foto op
        $path = $request->file('profile')->store('profile_photos', 'public');
        $user->profile = 'storage/' . $path; // Sla het pad op in de database
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'Profielfoto geüpload.');
    }

    /**
     * Verwijder account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'], // Controleer of huidig wachtwoord correct is
        ]);

        $user = $request->user();

        Auth::logout(); // Log de gebruiker uit
        $user->delete(); // Verwijder de gebruiker uit de database

        $request->session()->invalidate(); // Ongeldige sessie
        $request->session()->regenerateToken(); // Genereer nieuwe CSRF token

        return redirect('/')->with('status', 'Account verwijderd.');
    }
}
