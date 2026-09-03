<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileCompletionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileCompletionController extends Controller
{
    /**
     * Show the profile completion form.
     */
    public function showForm(): View|RedirectResponse
    {
        $user = auth()->user();

        // CLEAN CODE: Menggunakan method model isProfileComplete() dan getDashboardRouteName()
        if ($user && $user->isProfileComplete()) {
            return redirect()->route($user->getDashboardRouteName());
        }

        return view('auth.complete-profile');
    }

    /**
     * Store the completed profile information.
     */
    public function store(ProfileCompletionRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        
        // CLEAN LOGIC: Transaksi database untuk integritas pembaruan multi-tabel
        DB::transaction(function () use ($user, $validated) {
            $user->update([
                'no_hp'     => $validated['no_hp'],
                'nik'       => $validated['nik'],
                'nama_wali' => $validated['nama_wali'],
                'no_wali'   => $validated['no_wali'],
            ]);

            // Sinkronisasikan data penting ke model Penyewa jika relasi sudah ada
            if ($user->penyewa) {
                $user->penyewa->update([
                    'nik'       => $validated['nik'],
                    'nama_wali' => $validated['nama_wali'],
                    'no_wali'   => $validated['no_wali'],
                ]);
            }
        });

        // CLEAN ARCHITECTURE: Mengelola URL asal (intended)
        $intended = session()->pull('url.intended');
        if ($intended) {
            $isAdminRoute = Str::contains($intended, '/admin');
            $isActiveTenantRoute = Str::contains($intended, '/penyewa') && !Str::contains($intended, '/penyewa/reservasi');
            
            if (!$isAdminRoute && !$isActiveTenantRoute) {
                if (session()->has('reservasi_tipe_sewa') && session()->has('reservasi_durasi')) {
                    $queryParams = http_build_query([
                        'tipe_sewa' => session()->pull('reservasi_tipe_sewa'),
                        'durasi'    => session()->pull('reservasi_durasi'),
                    ]);
                    $intended .= (str_contains($intended, '?') ? '&' : '?') . $queryParams;
                }

                return redirect()->to($intended)
                    ->with('success', 'Profil Anda telah berhasil dilengkapi!');
            }
        }

        // CLEAN ARCHITECTURE: Arahkan langsung ke dashboard menggunakan model helper
        return redirect()->route($user->getDashboardRouteName())
            ->with('success', 'Profil Anda telah berhasil dilengkapi!');
    }
}
