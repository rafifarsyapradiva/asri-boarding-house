<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penyewa;
use App\Models\User;
use App\Models\Kamar;
use App\Http\Requests\StorePenyewaRequest;
use App\Http\Requests\UpdatePenyewaRequest;
use App\Services\NotifikasiService;
use App\Services\PdfGeneratorInterface;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class PenyewaController extends Controller
{
    /**
     * Constructor for PenyewaController.
     */
    public function __construct(
        protected PdfGeneratorInterface $pdfGenerator,
        protected \App\Services\AdminPenyewaService $adminPenyewaService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = $this->adminPenyewaService->getPenyewaQuery($request->only(['search', 'status']));
        $penyewa = $query->paginate(10)->withQueryString();

        return view('admin.penyewa.index', compact('penyewa'));
    }

    /**
     * Export list to PDF.
     */
    public function exportPdf(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $penyewaList = $this->adminPenyewaService->getPenyewaQuery($request->only(['search', 'status']))->get();

        $totalDeposit = $penyewaList->sum('deposit');
        $dendaPersen = \App\Models\Setting::get('denda_flat_persen', 5);
        $appName = \App\Models\Setting::get('logo_text', 'Asri Boarding House');

        $pdfOutput = $this->pdfGenerator->generate(
            'pdf.laporan-penyewa',
            compact('penyewaList', 'totalDeposit', 'dendaPersen', 'appName'),
            'A4',
            'landscape'
        );

        $filename = 'laporan-penyewa-' . time() . '.pdf';

        return response()->streamDownload(function () use ($pdfOutput) {
            echo $pdfOutput;
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export list to CSV using memory-efficient streaming.
     */
    public function exportCsv(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = 'laporan-penyewa-' . time() . '.csv';

        return response()->streamDownload(function () use ($request) {
            echo "\xEF\xBB\xBF";

            $handle = fopen('php://output', 'w');

            fputcsv($handle, ["LAPORAN DATA PENYEWA KOST"]);
            fputcsv($handle, ["Tanggal Cetak", now()->format('d-m-Y H:i:s')]);
            fputcsv($handle, ["Filter - Kata Kunci", $request->search ?: 'Semua']);
            fputcsv($handle, ["Filter - Status Keaktifan", $request->status ? ucfirst(strtolower($request->status)) : 'Semua']);
            fputcsv($handle, []);

            fputcsv($handle, [
                "No", 
                "Nama Penyewa", 
                "Email", 
                "No. HP", 
                "Nomor Kamar", 
                "Tipe Kamar", 
                "NIK", 
                "Tipe Sewa", 
                "Durasi", 
                "Tanggal Masuk", 
                "Status", 
                "Deposit (Rp)"
            ]);

            $index = 0;
            $totalDeposit = 0;

            foreach ($this->adminPenyewaService->getPenyewaQuery($request->only(['search', 'status']))->cursor() as $p) {
                $index++;
                $deposit = (int) ($p->deposit ?? 0);
                $totalDeposit += $deposit;

                fputcsv($handle, [
                    $index,
                    $p->user?->nama ?? '-',
                    $p->user?->email ?? '-',
                    $p->user?->no_hp ?? '-',
                    $p->kamar?->nomor_kamar ? 'Kamar ' . $p->kamar?->nomor_kamar : '-',
                    $p->kamar?->tipe ?? '-',
                    $p->nik ?? '-',
                    ucfirst($p->tipe_sewa ?? '-'),
                    $p->durasi ? $p->durasi . ' ' . ($p->tipe_sewa === 'harian' ? 'hari' : ($p->tipe_sewa === 'mingguan' ? 'minggu' : 'bulan')) : '-',
                    $p->tanggal_masuk ? \Carbon\Carbon::parse($p->tanggal_masuk)->format('Y-m-d') : '-',
                    ucfirst($p->status ?? '-'),
                    $deposit,
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, [
                "Total Akumulasi Uang Jaminan", 
                "", "", "", "", "", "", "", "", "", "", 
                $totalDeposit
            ]);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $kamar = Kamar::where('status', 'tersedia')->get();
        return view('admin.penyewa.create', compact('kamar'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePenyewaRequest $request): RedirectResponse
    {
        try {
            $this->adminPenyewaService->registerPenyewa($request->validated(), (int) auth()->id());

            return redirect()->route('admin.penyewa.index')->with('success', 'Penyewa berhasil didaftarkan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal meregistrasi penyewa baru: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            $message = $e instanceof \Illuminate\Database\QueryException
                ? 'Terjadi kesalahan sistem database. Kemungkinan terdapat duplikasi data atau kesalahan format data internal.'
                : $e->getMessage();

            return back()->withInput()->with('error', 'Gagal mendaftarkan penyewa: ' . $message);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Penyewa $penyewa): View
    {
        $penyewa->loadCount('tagihan');
        return view('admin.penyewa.show', compact('penyewa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penyewa $penyewa): View
    {
        $kamar = Kamar::where('status', 'tersedia')->orWhere('id', $penyewa->kamar_id)->get();
        return view('admin.penyewa.edit', compact('penyewa', 'kamar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePenyewaRequest $request, Penyewa $penyewa): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $penyewa) {
                // Update User
                $penyewa->user->update([
                    'nama' => $request->input('nama'),
                    'email' => trim(strtolower($request->input('email'))),
                    'no_hp' => $request->input('no_hp'),
                ]);

                // Jika pindah kamar, update status kamar lama dan baru dengan row lock
                $oldKamarId = $penyewa->kamar_id;
                $newKamarId = $request->input('kamar_id');
                if ($oldKamarId != $newKamarId) {
                    // Prevent deadlock by sorting primary keys before acquiring row locks
                    $ids = [$oldKamarId, $newKamarId];
                    sort($ids);
                    
                    $lockedKamars = Kamar::whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');
                    $oldKamar = $lockedKamars->get($oldKamarId);
                    $newKamar = $lockedKamars->get($newKamarId);
                    
                    if ($newKamar && $newKamar->status !== 'tersedia') {
                        throw new \Exception('Kamar baru yang dipilih tidak tersedia (sudah diisi oleh proses lain).');
                    }
                    
                    if ($oldKamar) {
                        $oldKamar->update(['status' => 'tersedia']);
                    }
                    if ($newKamar) {
                        $newKamar->update(['status' => 'terisi']);
                    }
                }

                // CATATAN: Status penyewa TIDAK bisa diubah lewat form edit.
                // Perubahan status (aktif ↔ nonaktif) hanya dilakukan melalui tombol Checkout
                // agar semua logika bisnis (update kamar, reservasi, keuangan) berjalan dengan benar.
                // Status dipertahankan dari nilai yang ada di database.

                $tanggalMasuk = \Illuminate\Support\Carbon::parse($request->input('tanggal_masuk'));
                $tipeSewa = $request->input('tipe_sewa');
                $durasi = intval($request->input('durasi'));
                if ($tipeSewa === 'harian') {
                    $tanggalKeluarSeharusnya = $tanggalMasuk->copy()->addDays($durasi)->toDateString();
                } elseif ($tipeSewa === 'mingguan') {
                    $tanggalKeluarSeharusnya = $tanggalMasuk->copy()->addWeeks($durasi)->toDateString();
                } else {
                    $tanggalKeluarSeharusnya = $tanggalMasuk->copy()->addMonths($durasi)->toDateString();
                }

                // Update Penyewa — status dan tanggal_keluar TIDAK diubah dari sini
                $penyewa->update([
                    'kamar_id'                  => $newKamarId,
                    'harga_sewa'                => $request->input('harga_sewa'),
                    'nik'                       => $request->input('nik'),
                    'tanggal_masuk'             => $request->input('tanggal_masuk'),
                    'tanggal_keluar_seharusnya' => $tanggalKeluarSeharusnya,
                    'nama_wali'                 => $request->input('nama_wali'),
                    'no_wali'                   => $request->input('no_wali'),
                    'tipe_sewa'                 => $request->input('tipe_sewa'),
                    'durasi'                    => $request->input('durasi'),
                    'deposit'                   => $request->input('deposit'),
                    'catatan'                   => $request->input('catatan'),
                ]);
            });


            return redirect()->route('admin.penyewa.index')->with('success', 'Data penyewa berhasil diperbarui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal memperbarui data penyewa: ' . $e->getMessage(), [
                'penyewa_id' => $penyewa->id,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            $message = $e instanceof \Illuminate\Database\QueryException
                ? 'Terjadi kesalahan sistem database. Kemungkinan terdapat duplikasi data atau kesalahan format data internal.'
                : $e->getMessage();

            return back()->withInput()->with('error', 'Gagal memperbarui data penyewa: ' . $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penyewa $penyewa): RedirectResponse
    {
        if ($penyewa->tagihan()->where('status', '!=', 'lunas')->exists()) {
            return back()->with('error', 'Penyewa tidak dapat dihapus karena memiliki riwayat tagihan.');
        }

        try {
            DB::transaction(function () use ($penyewa) {
                $user = $penyewa->user;
                $timestamp = time();
                
                // Hapus log notifikasi terlebih dahulu agar tidak melanggar foreign key constraint
                \App\Models\LogNotifikasi::where('penyewa_id', $penyewa->id)->delete();

                // Ubah NIK untuk membebaskan unique constraint sebelum di-soft-delete
                $penyewa->update([
                    'nik' => $penyewa->nik . '_deleted_' . $timestamp,
                ]);
                $penyewa->delete();

                if ($user) {
                    // Ubah email, no_hp, & NIK untuk membebaskan unique constraint sebelum di-soft-delete
                    $user->update([
                        'email' => $user->email . '_deleted_' . $timestamp,
                        'no_hp' => $user->no_hp . '_deleted_' . $timestamp,
                        'nik' => $user->nik ? $user->nik . '_deleted_' . $timestamp : null,
                    ]);
                    $user->delete();
                }
            });

            return redirect()->route('admin.penyewa.index')->with('success', 'Penyewa berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menghapus penyewa: ' . $e->getMessage(), [
                'penyewa_id' => $penyewa->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Gagal menghapus penyewa: Terjadi kesalahan sistem saat menghapus data.');
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(int $id): RedirectResponse
    {
        $penyewa = Penyewa::onlyTrashed()->findOrFail($id);
        
        $nik = $penyewa->nik;
        $originalNik = $nik;
        if (str_contains($nik, '_deleted_')) {
            $parts = explode('_deleted_', $nik);
            $originalNik = $parts[0];
        }
        
        // Check NIK collision
        if (Penyewa::where('nik', $originalNik)->exists()) {
            return back()->with('error', 'Gagal memulihkan penyewa. NIK ' . $originalNik . ' sudah digunakan oleh penyewa aktif lain.');
        }
        $penyewa->nik = $originalNik;

        try {
            DB::transaction(function () use ($penyewa) {
                $user = User::onlyTrashed()->find($penyewa->user_id);
                if ($user) {
                    $email = $user->email;
                    $originalEmail = $email;
                    if (str_contains($email, '_deleted_')) {
                        $parts = explode('_deleted_', $email);
                        $originalEmail = $parts[0];
                    }
                    if (User::where('email', $originalEmail)->exists()) {
                        throw new \Exception('Email ' . $originalEmail . ' sudah digunakan oleh pengguna aktif lain.');
                    }
                    $user->email = $originalEmail;
                    
                    $noHp = $user->no_hp;
                    $originalNoHp = $noHp;
                    if (str_contains($noHp, '_deleted_')) {
                        $parts = explode('_deleted_', $noHp);
                        $originalNoHp = $parts[0];
                    }
                    if (User::where('no_hp', $originalNoHp)->exists()) {
                        throw new \Exception('Nomor HP ' . $originalNoHp . ' sudah digunakan oleh pengguna aktif lain.');
                    }
                    $user->no_hp = $originalNoHp;

                    $userNik = $user->nik;
                    $originalUserNik = $userNik;
                    if ($userNik && str_contains($userNik, '_deleted_')) {
                        $parts = explode('_deleted_', $userNik);
                        $originalUserNik = $parts[0];
                    }
                    if ($originalUserNik && User::where('nik', $originalUserNik)->exists()) {
                        throw new \Exception('NIK ' . $originalUserNik . ' sudah digunakan oleh pengguna aktif lain.');
                    }
                    $user->nik = $originalUserNik;
                    
                    $user->restore();
                }
                
                $penyewa->restore();
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memulihkan penyewa: ' . $e->getMessage());
        }

        return redirect()->route('admin.penyewa.index')->with('success', 'Penyewa berhasil dipulihkan.');
    }

    /**
     * Menampilkan form checkout penyewa.
     */
    public function checkoutForm(Penyewa $penyewa): View
    {
        if ($penyewa->status !== 'aktif') {
            return redirect()->route('admin.penyewa.index')->with('error', 'Hanya penyewa aktif yang dapat di-checkout.');
        }

        return view('admin.penyewa.checkout', compact('penyewa'));
    }

    /**
     * Memproses data checkout penyewa dengan validasi potongan deposit dan pencatatan keuangan.
     */
    public function processCheckout(Request $request, Penyewa $penyewa): RedirectResponse
    {
        if ($penyewa->status !== 'aktif') {
            return redirect()->route('admin.penyewa.index')->with('error', 'Hanya penyewa aktif yang dapat di-checkout.');
        }

        // VALIDASI STRICT FINANSIAL: Cegah checkout jika masih ada tagihan pending/terlambat
        $unpaidBillsCount = $penyewa->tagihan()->whereIn('status', ['pending', 'terlambat'])->count();
        if ($unpaidBillsCount > 0) {
            return redirect()->route('admin.penyewa.index')
                ->with('error', 'Penyewa tidak dapat di-checkout karena masih memiliki ' . $unpaidBillsCount . ' tagihan yang belum lunas. Selesaikan semua pembayaran terlebih dahulu.');
        }

        if ($request->has('nominal_potongan')) {
            $request->merge([
                'nominal_potongan' => $this->sanitizeCurrency($request->input('nominal_potongan')),
            ]);
        }

        $request->validate([
            'apakah_ada_kerusakan' => ['required', 'boolean'],
            'nominal_potongan' => [
                'required_if:apakah_ada_kerusakan,1',
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($penyewa) {
                    if ($value > $penyewa->deposit) {
                        $fail('Nominal potongan perbaikan tidak boleh melebihi jaminan deposit awal penyewa (Rp ' . number_format($penyewa->deposit, 0, ',', '.') . ').');
                    }
                }
            ],
            'bukti_nota' => [
                'required_if:apakah_ada_kerusakan,1',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048'
            ],
            'keterangan' => [
                'required_if:apakah_ada_kerusakan,1',
                'nullable',
                'string',
                'max:500'
            ],
        ], [
            'nominal_potongan.required_if' => 'Nominal potongan wajib diisi jika ada kerusakan.',
            'nominal_potongan.numeric' => 'Nominal potongan harus berupa angka.',
            'nominal_potongan.min' => 'Nominal potongan tidak boleh kurang dari 0.',
            'bukti_nota.required_if' => 'Unggahan berkas bukti nota wajib dilampirkan jika ada kerusakan.',
            'bukti_nota.image' => 'Bukti nota harus berupa berkas gambar.',
            'bukti_nota.mimes' => 'Format gambar bukti nota harus berupa jpg, jpeg, atau png.',
            'bukti_nota.max' => 'Ukuran berkas bukti nota maksimal adalah 2MB.',
            'keterangan.required_if' => 'Keterangan detail perbaikan wajib diisi jika ada kerusakan.',
            'keterangan.max' => 'Panjang keterangan maksimal 500 karakter.',
        ]);

        try {
            DB::transaction(function () use ($request, $penyewa) {
                // Kunci baris data penyewa untuk mencegah concurrency race condition
                $lockedPenyewa = Penyewa::where('id', $penyewa->id)->lockForUpdate()->firstOrFail();

                if ($lockedPenyewa->status !== 'aktif') {
                    throw new \Exception('Data penyewa sudah tidak aktif atau di-checkout oleh proses lain.');
                }

                $updateData = [
                    'status' => 'nonaktif',
                ];

                $remainingDeposit = (float) $lockedPenyewa->deposit;

                if ($request->input('apakah_ada_kerusakan') == '1') {
                    $nominalPotongan = (float) $request->input('nominal_potongan');
                    $pathNota = null;

                    if ($request->hasFile('bukti_nota')) {
                        $file = $request->file('bukti_nota');
                        $pathNota = $file->store('nota_pengeluaran', 'public');
                    }

                    $remainingDeposit = max(0, $remainingDeposit - $nominalPotongan);

                    \App\Models\Pengeluaran::create([
                        'nama_pengeluaran' => 'Perbaikan Kerusakan Kamar ' . ($lockedPenyewa->kamar->nomor_kamar ?? '-'),
                        'kategori' => 'maintenance',
                        'nominal' => $nominalPotongan,
                        'tanggal_pengeluaran' => now()->toDateString(),
                        'bukti_nota' => $pathNota,
                        'keterangan' => $request->input('keterangan'),
                    ]);
                }

                // Sisa deposit dikembalikan secara fisik dan dicatat dalam arus kas keluar
                if ($remainingDeposit > 0) {
                    \App\Models\Pengeluaran::create([
                        'nama_pengeluaran' => 'Pengembalian Jaminan Deposit Penyewa: ' . ($lockedPenyewa->user->nama ?? '-'),
                        'kategori' => 'operasional',
                        'nominal' => $remainingDeposit,
                        'tanggal_pengeluaran' => now()->toDateString(),
                        'keterangan' => 'Pengembalian sisa uang jaminan sewa (deposit) setelah dipotong biaya perbaikan kerusakan (jika ada).',
                    ]);
                }

                $updateData['deposit'] = 0; // Deposit diselesaikan (0) setelah dikembalikan / dipotong perbaikan

                $lockedPenyewa->update($updateData);
            });

            return redirect()->route('admin.penyewa.index')->with('success', 'Prosedur checkout penyewa berhasil diselesaikan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal memproses checkout penyewa: ' . $e->getMessage(), [
                'penyewa_id' => $penyewa->id,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->with('error', 'Gagal memproses checkout: ' . $e->getMessage());
        }
    }

    /**
     * Memproses perpanjangan kontrak manual penyewa harian/mingguan.
     */
    public function perpanjang(Request $request, Penyewa $penyewa, \App\Services\BillingService $billingService): RedirectResponse
    {
        $request->validate([
            'durasi_tambahan' => ['required', 'integer', 'min:1'],
            'tipe_sewa' => ['required', 'string', \Illuminate\Validation\Rule::in(['harian', 'mingguan'])],
        ]);

        try {
            $billingService->perpanjangKontrakManual(
                $penyewa,
                (int) $request->input('durasi_tambahan'),
                $request->input('tipe_sewa')
            );

            return redirect()->route('admin.penyewa.index')
                ->with('success', 'Kontrak penyewa berhasil diperpanjang secara manual.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperpanjang kontrak: ' . $e->getMessage());
        }
    }

    /**
     * Memformat string rupiah menjadi nilai desimal numerik.
     */
    private function sanitizeCurrency($value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        if (preg_match('/^\d+\.\d{1,2}$/', $value)) {
            return (float) $value;
        }

        if (str_contains($value, ',')) {
            $cleaned = str_replace('.', '', $value);
            $cleaned = str_replace(',', '.', $cleaned);
            return is_numeric($cleaned) ? (float) $cleaned : $value;
        }

        $cleaned = str_replace('.', '', $value);
        return is_numeric($cleaned) ? (float) $cleaned : $value;
    }
}
