<?php

namespace App\Services;

use App\Helpers\PhoneNumberHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    public function formatNomor(?string $nomor): string
    {
        return PhoneNumberHelper::formatInternational($nomor);
    }

    /**
     * Kirim pesan WhatsApp menggunakan Fonnte API.
     */
    public function kirimPesan(?string $nomor, string $pesan): bool
    {
        if (empty($nomor)) {
            Log::warning('Fonnte: target phone number is empty.');
            return false;
        }

        $nomor = $this->formatNomor($nomor);

        if (empty($nomor)) {
            Log::warning('Fonnte: target phone number format yielded empty.');
            return false;
        }

        if (!app()->environment('testing') && (config('fonnte.token') === 'fonnte-token' || empty(config('fonnte.token')))) {
            Log::info('--- [FONNTE DRY RUN / LOCAL LOG] ---', [
                'target'  => $nomor,
                'message' => $pesan,
            ]);

            return true;
        }

        // 3. Kirim HTTP POST request ke endpoint Fonnte dengan timeout pengaman
        try {
            $response = Http::timeout(5)
                ->connectTimeout(3)
                ->withHeaders([
                    'Authorization' => config('fonnte.token'),
                ])->post('https://api.fonnte.com/send', [
                    'target'      => $nomor,
                    'message'     => $pesan,
                    'countryCode' => '62',
                    'delay'       => '2',
                ]);

            $responseData = $response->json();
            $statusInJson = $responseData['status'] ?? false;
            $isFonnteStatusSuccess = ($statusInJson === true || $statusInJson === 'true');

            $sukses = $response->successful() && $isFonnteStatusSuccess;

            if (!$sukses) {
                Log::warning('Fonnte gagal kirim', [
                    'nomor' => $nomor,
                    'response' => $response->body(),
                ]);
            }

            return $sukses;
        } catch (\Throwable $e) {
            Log::error('Fonnte request exception', [
                'nomor' => $nomor,
                'error' => $e->getMessage(),
            ]);
            if (app()->environment('testing')) {
                throw $e;
            }
            return false;
        }
    }
}
