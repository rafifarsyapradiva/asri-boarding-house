<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $isPenyewa = $user->role === User::ROLE_PENYEWA;
        
        $viewName = $isPenyewa ? 'penyewa.kelola-akun' : 'profile.edit';
        $updateRoute = route($isPenyewa ? 'penyewa.profile.update' : 'profile.update');
        $deleteRoute = route($isPenyewa ? 'penyewa.profile.destroy' : 'profile.destroy');

        return view($viewName, [
            'user'        => $user,
            'isDeletable' => $user->isDeletable(), // CLEAN ARCHITECTURE: Menggunakan method model
            'updateRoute' => $updateRoute,
            'deleteRoute' => $deleteRoute,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // CLEAN LOGIC: Menjaga konsistensi data antara User dan Penyewa jika data wali/nik diubah
        DB::transaction(function () use ($user, $validated) {
            $user->fill($validated);
            $user->save();

            if ($user->penyewa) {
                $penyewaData = array_filter([
                    'nik'       => $validated['nik'] ?? null,
                    'nama_wali' => $validated['nama_wali'] ?? null,
                    'no_wali'   => $validated['no_wali'] ?? null,
                ], fn($val) => !is_null($val));

                if (!empty($penyewaData)) {
                    $user->penyewa->update($penyewaData);
                }
            }
        });

        $redirectRoute = $request->route()->named('penyewa.*') 
            ? 'penyewa.profile.edit' 
            : 'profile.edit';

        return redirect()->route($redirectRoute)->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // CLEAN ARCHITECTURE: Pengecekan status hapus akun menggunakan method model User
        if (!$user->isDeletable()) {
            return back()->withErrors([
                'password' => 'Akun Anda tidak dapat dihapus secara mandiri karena memiliki riwayat sewa atau reservasi di Asri Boarding House.'
            ], 'userDeletion');
        }

        Auth::logout();

        $user->anonymizeAndDelete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }
}
