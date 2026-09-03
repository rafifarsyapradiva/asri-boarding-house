<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\CekKetersediaanRequest;
use App\Models\CustomerReview;
use App\Models\Faq;
use App\Models\Fasilitas;
use App\Models\Gallery;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Reservasi;
use App\Models\Setting;
use App\Models\WhatsappClick;
use App\Services\ReservasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LandingController extends Controller
{
    // Extracted Constants (Clean Code - Magic Numbers Elimination)
    private const CACHE_TTL = 3600; // 1 jam
    private const DEFAULT_WA_NUMBER = '62895330031313';
    private const LANDING_REVIEW_LIMIT = 6;
    private const LANDING_FAQ_LIMIT = 5;
    private const LANDING_GALLERY_LIMIT = 3;
    private const TESTIMONI_PAGINATION_LIMIT = 10;
    private const KAMAR_PAGINATION_LIMIT = 12;

    /**
     * Dependency Injection via Constructor (Clean Architecture)
     */
    public function __construct(
        protected ReservasiService $reservasiService
    ) {}

    /**
     * Display the landing page with facilities and available rooms.
     */
    public function index(): View
    {
        $fasilitas = $this->getCachedFasilitas();

        $kamarList = Cache::remember('kamar_aktif_landing', self::CACHE_TTL, function () {
            return Kamar::whereIn('status', ['tersedia', 'terisi'])
                ->orderBy('tipe')
                ->orderBy('nomor_kamar')
                ->with('fasilitas')
                ->get();
        });

        $waData = $this->getWhatsappMetadata();
        $reviews = CustomerReview::latest()->take(self::LANDING_REVIEW_LIMIT)->get();
        $faqs = Faq::aktif()->take(self::LANDING_FAQ_LIMIT)->get();
        $galleries = Gallery::aktif()->take(self::LANDING_GALLERY_LIMIT)->get();

        return view('landing.index', array_merge([
            'fasilitas' => $fasilitas,
            'kamarList' => $kamarList,
            'reviews'   => $reviews,
            'faqs'      => $faqs,
            'galleries' => $galleries,
        ], $waData));
    }

    /**
     * Display the facilities page.
     */
    public function fasilitas(): View
    {
        return view('landing.fasilitas', [
            'fasilitas' => $this->getCachedFasilitas(),
        ]);
    }

    /**
     * Display the room catalog page.
     */
    public function kamarList(Request $request): View
    {
        $tipeKamar = $request->input('tipe_kamar', 'all');
        $q = trim((string) $request->input('q', ''));

        $query = Kamar::whereIn('status', ['tersedia', 'terisi'])
            ->with('fasilitas');

        if ($tipeKamar !== 'all') {
            $query->where('tipe', $tipeKamar);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nomor_kamar', 'like', "%{$q}%")
                    ->orWhere('tipe', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        $kamarList = $query->orderBy('tipe')
            ->orderBy('nomor_kamar')
            ->paginate(self::KAMAR_PAGINATION_LIMIT)
            ->withQueryString();

        $waData = $this->getWhatsappMetadata();

        return view('landing.kamar-list', array_merge(['kamarList' => $kamarList], $waData));
    }

    /**
     * Display the booking guide page.
     */
    public function caraBooking(): View
    {
        return view('landing.cara-booking');
    }

    /**
     * Display the customer reviews page.
     */
    public function testimoni(): View
    {
        $reviews = CustomerReview::latest()->paginate(self::TESTIMONI_PAGINATION_LIMIT);
        return view('landing.testimoni', compact('reviews'));
    }

    /**
     * Display details of a specific room.
     */
    public function showKamar(Kamar $kamar): View
    {
        $kamar->load('fasilitas');

        if (!auth()->check() && !session()->has('url.intended')) {
            session()->put('url.intended', url()->full());
        }

        return view('landing.show', compact('kamar'));
    }

    /**
     * AJAX endpoint to calculate dynamic pricing for booking simulation.
     */
    public function hitungHarga(Request $request, Kamar $kamar): JsonResponse
    {
        $data = $request->validate([
            'tipe_sewa' => 'required|string|in:harian,mingguan,bulanan',
            'durasi'    => 'required|integer|min:1',
        ], [
            'tipe_sewa.required' => 'Tipe sewa wajib dipilih.',
            'tipe_sewa.in'       => 'Tipe sewa harus berupa harian, mingguan, atau bulanan.',
            'durasi.required'    => 'Durasi wajib diisi.',
            'durasi.integer'     => 'Durasi harus berupa angka.',
            'durasi.min'         => 'Durasi minimal adalah 1.',
        ]);

        $tipeSewa = $data['tipe_sewa'];
        $durasi = (int) $data['durasi'];

        $min = config("reservasi.min_durasi.{$tipeSewa}", 1);
        $max = config("reservasi.max_durasi.{$tipeSewa}", 999);

        if ($durasi < $min || $durasi > $max) {
            return response()->json([
                'success' => false,
                'message' => "Durasi sewa untuk tipe {$tipeSewa} harus antara {$min} dan {$max}."
            ], 422);
        }

        // Clean Architecture: Gunakan ReservasiService terpusat
        $rincian = $this->reservasiService->hitungHarga($kamar, $tipeSewa, $durasi);

        return response()->json([
            'success'     => true,
            'total_harga' => $rincian['total_harga'],
            'dp_minimal'  => $rincian['nominal_dp'],
        ]);
    }

    /**
     * AJAX endpoint to check availability for all rooms on a specific date range.
     */
    public function cekKetersediaan(CekKetersediaanRequest $request): JsonResponse
    {
        $tanggalMulai = Carbon::parse($request->input('tanggal_masuk'));
        $durasi = (int) $request->input('durasi');
        $tipeSewa = $request->input('tipe_sewa', 'bulanan');

        // Clean Logic: Perbaikan hitung tanggal berdasarkan tipe sewa
        $tanggalSelesai = match ($tipeSewa) {
            'harian'   => $tanggalMulai->copy()->addDays($durasi),
            'mingguan' => $tanggalMulai->copy()->addWeeks($durasi),
            'bulanan'  => $tanggalMulai->copy()->addMonths($durasi),
            default    => $tanggalMulai->copy()->addMonths($durasi),
        };

        $mulaiStr = $tanggalMulai->toDateString();
        $selesaiStr = $tanggalSelesai->toDateString();

        // Delegasi / Query ringan ID Kamar terisi
        $unavailableKamarIds = $this->getUnavailableKamarIds($mulaiStr, $selesaiStr);

        // Hanya select ID dan status dari database (Efisiensi memori)
        $kamarList = Kamar::whereIn('status', ['tersedia', 'terisi'])->get(['id', 'status']);
        $availability = [];

        foreach ($kamarList as $kamar) {
            $isBookedOrOccupied = in_array($kamar->id, $unavailableKamarIds, true);
            $isAvailable = !$isBookedOrOccupied;

            $availability[$kamar->id] = [
                'is_available' => $isAvailable,
                'status'       => $isAvailable ? 'tersedia' : 'terisi',
            ];
        }

        return response()->json([
            'success'      => true,
            'availability' => $availability,
        ]);
    }

    /**
     * Display the About Us page.
     */
    public function tentangKami(): View
    {
        return view('landing.tentang-kami');
    }

    /**
     * Display the FAQ page.
     */
    public function faq(): View
    {
        return view('landing.faq', [
            'faqs' => Faq::aktif()->get(),
        ]);
    }

    /**
     * Display the gallery page.
     */
    public function galeri(): View
    {
        return view('landing.galeri', [
            'galleries' => Gallery::aktif()->get(),
        ]);
    }

    /**
     * AJAX endpoint to track outgoing WhatsApp clicks.
     */
    public function trackWhatsappClick(Request $request): JsonResponse
    {
        $data = $request->validate([
            'source'   => 'required|string|max:50',
            'kamar_id' => 'nullable|integer|exists:kamar,id',
        ]);

        WhatsappClick::create([
            'source'     => $data['source'],
            'kamar_id'   => $data['kamar_id'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp click registered successfully.',
        ]);
    }

    /**
     * Helper DRY: Ambil informasi WhatsApp terpusat.
     */
    private function getWhatsappMetadata(): array
    {
        $rawWa = Setting::get('contact_whatsapp', config('reservasi.admin_wa', self::DEFAULT_WA_NUMBER));
        $cleanWa = Setting::formatWhatsapp($rawWa);

        return [
            'cleanWa' => $cleanWa,
            'waOwner' => $cleanWa,
            'waPesan' => rawurlencode('Halo Admin Asri Boarding House, saya tertarik untuk menanyakan ketersediaan kamar dan informasi lebih lanjut mengenai hunian kost. Terima kasih.'),
        ];
    }

    /**
     * Helper DRY: Data fasilitas aktif dengan cache.
     */
    private function getCachedFasilitas()
    {
        return Cache::remember('fasilitas_aktif_landing', self::CACHE_TTL, function () {
            return Fasilitas::aktif()->get();
        });
    }

    /**
     * Helper Query: Ambil ID kamar yang tidak tersedia pada rentang tanggal.
     */
    private function getUnavailableKamarIds(string $mulaiStr, string $selesaiStr): array
    {
        $booked = Reservasi::query()
            ->aktif()
            ->where('tanggal_mulai', '<=', $selesaiStr)
            ->where('tanggal_selesai', '>=', $mulaiStr)
            ->pluck('kamar_id')
            ->toArray();

        $occupied = Penyewa::query()
            ->where('status', 'aktif')
            ->where('tanggal_masuk', '<=', $selesaiStr)
            ->where(function ($sub) use ($mulaiStr) {
                $sub->where('tanggal_keluar', '>=', $mulaiStr)
                    ->orWhereNull('tanggal_keluar');
            })
            ->pluck('kamar_id')
            ->toArray();

        return array_unique(array_merge($booked, $occupied));
    }
}
