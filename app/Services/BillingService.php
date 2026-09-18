<?php

namespace App\Services;

use App\Models\Penyewa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use App\Events\TagihanDibuat;
use App\Events\PembayaranBerhasil;
use App\Events\ReminderPenyewa;
use App\Events\DendaDikenakan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BillingService
{
    /**
     * Rate denda flat rate 5%
     */
    public const DENDA_RATE = 0.05;

    /**
     * Mengorkestrasi pembentukan tagihan bulanan secara otomatis bagi penyewa aktif.
     */
    public function generateTagihanBulanan(): void
    {
        try {
            $now = Carbon::now();
            $periodeBulan = $now->month;
            $periodeTahun = $now->year;
            
            // Tanggal tagihan diatur selalu tanggal 1 bulan berjalan
            $tanggalTagihan = Carbon::create($periodeTahun, $periodeBulan, 1)->toDateString();
            // Tanggal jatuh tempo selalu tanggal 10 bulan berjalan (grace period terikat 10 hari)
            $tanggalJatuhTempo = Carbon::create($periodeTahun, $periodeBulan, 10)->toDateString();

            // Optimasi pemrosesan data massal menggunakan chunking
            Penyewa::where('status', 'aktif')
                ->where('tipe_sewa', 'bulanan')
                ->whereNotIn('tipe_sewa', ['harian', 'mingguan'])
                ->where('tanggal_masuk', '<=', $tanggalTagihan)
                ->with(['kamar'])
                ->chunkById(100, function ($penyewaList) use ($now, $periodeBulan, $periodeTahun, $tanggalTagihan, $tanggalJatuhTempo) {
                    foreach ($penyewaList as $penyewa) {
                        try {
                            $createdTagihan = null;
                            try {
                                DB::transaction(function () use ($penyewa, $now, $periodeBulan, $periodeTahun, $tanggalTagihan, $tanggalJatuhTempo, &$createdTagihan) {
                                    $orderId = "TGH-{$penyewa->id}-" . $now->format('Ym');
                                    $nominalPokok = ($penyewa->harga_sewa !== null && (float)$penyewa->harga_sewa > 0)
                                        ? $penyewa->harga_sewa
                                        : ($penyewa->kamar ? $penyewa->kamar->harga_bulan : 0);

                                    // Gunakan firstOrCreate untuk antiduplikasi data
                                    $tagihan = Tagihan::firstOrCreate([
                                        'penyewa_id' => $penyewa->id,
                                        'periode_bulan' => $periodeBulan,
                                        'periode_tahun' => $periodeTahun,
                                    ], [
                                        'order_id' => $orderId,
                                        'tanggal_tagihan' => $tanggalTagihan,
                                        'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                                        'nominal_pokok' => $nominalPokok,
                                        'nominal_total' => $nominalPokok,
                                        'status' => 'pending',
                                    ]);

                                    if ($tagihan->wasRecentlyCreated) {
                                        $createdTagihan = $tagihan;
                                    }
                                });
                            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                                Log::info("Tagihan bulanan untuk Penyewa ID {$penyewa->id} sudah dibuat oleh proses lain (unique constraint).");
                            }

                            if ($createdTagihan) {
                                event(new TagihanDibuat($createdTagihan));
                            }
                        } catch (\Exception $e) {
                            Log::error("Gagal membuat tagihan bulanan untuk Penyewa ID {$penyewa->id}: " . $e->getMessage());
                        }
                    }
                });
        } catch (\Exception $e) {
            Log::error("Gagal menjalankan generateTagihanBulanan: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Memproses keterlambatan pembayaran tagihan.
     */
    public function prosesKeterlambatan(): void
    {
        try {
            $hariIni = Carbon::today();

            // Optimasi chunking untuk mencegah penumpukan memori (memory leak) pada data massal
            Tagihan::whereIn('status', ['pending', 'terlambat'])
                ->where('tanggal_jatuh_tempo', '<', $hariIni)
                ->chunkById(100, function ($overdueTagihan) use ($hariIni) {
                    foreach ($overdueTagihan as $tagihan) {
                        try {
                            $eventsToFire = [];
                            DB::transaction(function () use ($tagihan, $hariIni, &$eventsToFire) {
                                // Kunci data secara eksklusif menggunakan lockForUpdate()
                                $tagihanLocked = Tagihan::where('id', $tagihan->id)->lockForUpdate()->first();

                                if (!$tagihanLocked || !in_array($tagihanLocked->status, ['pending', 'terlambat'])) {
                                    return;
                                }

                                // Cek apakah sudah melewati bulan periode tagihan berjalan (aman dari month overflow Carbon)
                                $isBulanBerikutnya = ($hariIni->year > $tagihanLocked->periode_tahun) ||
                                    ($hariIni->year === $tagihanLocked->periode_tahun && $hariIni->month > $tagihanLocked->periode_bulan);

                                if ($isBulanBerikutnya) {
                                    // Proteksi Idempotensi: Kunci gerbang nominal_denda == 0
                                    if ($tagihanLocked->nominal_denda == 0) {
                                        $nominalDenda = $tagihanLocked->nominal_pokok * self::DENDA_RATE;
                                        $tagihanLocked->nominal_denda = $nominalDenda;
                                        $tagihanLocked->nominal_total += $nominalDenda;
                                        $tagihanLocked->status = 'terlambat';

                                        // Pastikan bulan_keterlambatan minimal bernilai 3 agar NotifikasiService mengirim notifikasi denda
                                        if ($tagihanLocked->bulan_keterlambatan < 3) {
                                            $tagihanLocked->bulan_keterlambatan = 3;
                                        }

                                        $tambahanKeterangan = "Denda keterlambatan flat 5% dikenakan karena melampaui bulan periode berjalan.";
                                        $tagihanLocked->keterangan = $tagihanLocked->keterangan
                                            ? $tagihanLocked->keterangan . ' ' . $tambahanKeterangan
                                            : $tambahanKeterangan;

                                        $tagihanLocked->save();

                                        // Sinkronisasi ke objek asli
                                        $tagihan->fill($tagihanLocked->toArray());

                                        $eventsToFire[] = new DendaDikenakan($tagihan);
                                    } else {
                                        // Jika denda sudah dikenakan, pastikan status tetap 'terlambat' di DB
                                        if ($tagihanLocked->status !== 'terlambat') {
                                            $tagihanLocked->status = 'terlambat';
                                            $tagihanLocked->save();
                                        }
                                        $eventsToFire[] = new ReminderPenyewa($tagihanLocked);
                                    }
                                } else {
                                    // Masih berada di bulan berjalan yang sama (Masa Keringanan): Bebas denda, hanya trigger reminder
                                    if ($tagihanLocked->bulan_keterlambatan === 0) {
                                        $tagihanLocked->bulan_keterlambatan = 1;
                                    }
                                    $tagihanLocked->status = 'terlambat';
                                    $tagihanLocked->save();

                                    $tagihan->fill($tagihanLocked->toArray());
                                    $eventsToFire[] = new ReminderPenyewa($tagihan);
                                }
                            });

                            // Pemicuan event di luar database transaction untuk mencegah rollback berantai
                            foreach ($eventsToFire as $evt) {
                                event($evt);
                            }
                        } catch (\Exception $e) {
                            Log::error("Gagal memproses keterlambatan untuk Tagihan ID {$tagihan->id}: " . $e->getMessage());
                        }
                    }
                });
        } catch (\Exception $e) {
            Log::error("Gagal menjalankan prosesKeterlambatan: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Menerapkan denda flat 5% secara langsung (idempotent & atomik).
     */
    public function terapkanDendaDirect(Tagihan $tagihan): void
    {
        try {
            DB::transaction(function () use ($tagihan) {
                // Lock data menggunakan lockForUpdate untuk mengantisipasi race condition
                $tagihanLocked = Tagihan::where('id', $tagihan->id)->lockForUpdate()->first();

                if ($tagihanLocked && in_array($tagihanLocked->status, ['pending', 'terlambat']) && $tagihanLocked->nominal_denda == 0) {
                    $nominalDenda = $tagihanLocked->nominal_pokok * self::DENDA_RATE;
                    $tagihanLocked->nominal_denda = $nominalDenda;
                    $tagihanLocked->nominal_total += $nominalDenda;

                    // Pastikan bulan_keterlambatan minimal bernilai 3 agar NotifikasiService mengirim notifikasi denda
                    if ($tagihanLocked->bulan_keterlambatan < 3) {
                        $tagihanLocked->bulan_keterlambatan = 3;
                    }

                    // Perbarui kolom keterangan secara atomik
                    $tambahanKeterangan = "Denda keterlambatan flat 5% dikenakan karena melampaui bulan periode berjalan.";
                    $tagihanLocked->keterangan = $tagihanLocked->keterangan
                        ? $tagihanLocked->keterangan . ' ' . $tambahanKeterangan
                        : $tambahanKeterangan;

                    $tagihanLocked->save();

                    // Sinkronisasi data ke objek tagihan asli
                    $tagihan->fill($tagihanLocked->toArray());

                    event(new DendaDikenakan($tagihan));
                }
            });
        } catch (\Exception $e) {
            Log::error("Gagal menerapkan denda direct untuk Tagihan ID {$tagihan->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Inject sisa DP ke tagihan bulan pertama calon penyewa (Koreksi Finansial v2.1).
     */
    public function injectSisaDp(Penyewa $penyewa, float $nominalSisa): void
    {
        $tanggalMasuk = Carbon::parse($penyewa->tanggal_masuk);
        $bulanMulai = $tanggalMasuk->month;
        $tahunMulai = $tanggalMasuk->year;

        // Hitung jatuh tempo dinamis khusus pelunasan Sisa DP
        $waktuKedatangan = $tanggalMasuk->copy()->startOfDay();
        $waktuToleransi24Jam = now()->addDay()->startOfDay();
        
        $jatuhTempoSisaDp = $waktuKedatangan->greaterThan($waktuToleransi24Jam) 
                            ? $waktuKedatangan 
                            : $waktuToleransi24Jam;

        // firstOrCreate mencegah duplikasi jika admin klik konfirmasi dua kali
        $tagihan = Tagihan::firstOrCreate(
            ['penyewa_id' => $penyewa->id, 'periode_bulan' => $bulanMulai, 'periode_tahun' => $tahunMulai],
            [
                'order_id'            => sprintf('TGH-%d-%04d%02d', $penyewa->id, $tahunMulai, $bulanMulai),
                'tanggal_tagihan'     => Carbon::create($tahunMulai, $bulanMulai, 1),
                'tanggal_jatuh_tempo' => $jatuhTempoSisaDp,
                'nominal_pokok'       => $nominalSisa, // Set pokok sebesar sisa kewajiban (70%) agar sinkron dengan total untuk Midtrans & Denda
                'nominal_denda'       => 0,
                'nominal_total'       => $nominalSisa,
                'keterangan'          => sprintf(
                    'Tagihan bulan pertama (Skema DP Reservasi). Sisa wajib dilunasi: Rp %s.',
                    number_format($nominalSisa, 0, ',', '.')
                ),
                'status'              => 'pending',
            ]
        );

        // Antisipasi dua kondisi:
        // (A) Tagihan sudah dibuat scheduler sebelum admin konfirmasi → update nominal
        // (B) Tagihan dari masa sewa SEBELUMNYA (penyewa checkout lalu reservasi ulang) → reset penuh ke pending
        if (!$tagihan->wasRecentlyCreated) {
            $tagihan->update([
                'nominal_pokok'     => $nominalSisa,
                'nominal_denda'     => 0,
                'nominal_total'     => $nominalSisa,
                'status'            => 'pending',         // WAJIB: reset dari 'lunas' lama jika ini tagihan lama
                'tanggal_bayar'     => null,              // Hapus tanggal bayar lama agar tidak membingungkan
                'metode_pembayaran' => null,              // Hapus metode pembayaran lama
                'keterangan'        => sprintf(
                    'Tagihan bulan pertama (Penyesuaian DP). Sisa kewajiban: Rp %s.',
                    number_format($nominalSisa, 0, ',', '.')
                ),
            ]);

            // Hapus record pembayaran lama dari masa sewa sebelumnya agar nota tidak mengacu ke transaksi lama
            $tagihan->pembayaran()->delete();
        }

        if ($tagihan->wasRecentlyCreated) {
            event(new TagihanDibuat($tagihan));
        }
    }

    /**
     * Inject tagihan lunas penuh (Full Payment) calon penyewa.
     */
    public function injectLunasPenuh(Penyewa $penyewa, Reservasi $reservasi, ?int $adminId = null): void
    {
        $tanggalMasuk = Carbon::parse($penyewa->tanggal_masuk);
        $bulanMulai = $tanggalMasuk->month;
        $tahunMulai = $tanggalMasuk->year;

        DB::transaction(function () use ($penyewa, $reservasi, $bulanMulai, $tahunMulai, $adminId) {
            // Cek jika tagihan sudah terlanjur dibuat oleh scheduler
            $tagihan = Tagihan::where([
                'penyewa_id' => $penyewa->id,
                'periode_bulan' => $bulanMulai,
                'periode_tahun' => $tahunMulai
            ])->first();

            if ($tagihan) {
                $tagihan->update([
                    'nominal_pokok' => $penyewa->harga_sewa ?? ($penyewa->kamar ? $penyewa->kamar->harga_bulan : 0),
                    'nominal_denda' => 0,
                    'nominal_total' => $reservasi->total_harga,
                    'status' => 'lunas',
                    'metode_pembayaran' => $reservasi->metode_pembayaran,
                    'keterangan' => 'Tagihan bulan pertama lunas via Reservasi Online (Full Payment).',
                ]);
            } else {
                // [1] Buat Tagihan dengan status langsung 'lunas'
                $tagihan = Tagihan::create([
                    'penyewa_id'          => $penyewa->id,
                    'order_id'            => sprintf('TGH-%d-%04d%02d', $penyewa->id, $tahunMulai, $bulanMulai),
                    'periode_bulan'       => $bulanMulai,
                    'periode_tahun'       => $tahunMulai,
                    'tanggal_tagihan'     => Carbon::create($tahunMulai, $bulanMulai, 1),
                    'tanggal_jatuh_tempo' => Carbon::create($tahunMulai, $bulanMulai, 10),
                    'nominal_pokok'       => $penyewa->harga_sewa ?? ($penyewa->kamar ? $penyewa->kamar->harga_bulan : 0),
                    'nominal_denda'       => 0,
                    'nominal_total'       => $reservasi->total_harga,
                    'bulan_keterlambatan' => 0,
                    'status'              => 'lunas',
                    'metode_pembayaran'   => $reservasi->metode_pembayaran,
                    'keterangan'          => 'Tagihan bulan pertama lunas via Reservasi Online (Full Payment).',
                ]);
            }

            // [2] Buat record Pembayaran agar nota PDF dapat dicetak oleh PdfNotaService
            $pembayaran = Pembayaran::create([
                'tagihan_id'       => $tagihan->id,
                'transaction_id'   => $reservasi->transaction_id ?? sprintf('PAY-RSV-%d-%d', $reservasi->id, time()),
                'payment_type'     => $reservasi->metode_pembayaran === 'midtrans' ? 'midtrans' : 'cash',
                'nominal'          => $reservasi->total_harga,
                'status_midtrans'  => $reservasi->metode_pembayaran === 'midtrans' ? 'settlement' : 'cash_confirmed',
                'dikonfirmasi_oleh' => $reservasi->metode_pembayaran === 'cash' ? $adminId : null,
                'tanggal_bayar'    => $reservasi->tanggal_konfirmasi ?? Carbon::now(),
            ]);

            // [3] Trigger event → Dompdf cetak PDF nota + kirim WA/Email konfirmasi
            event(new PembayaranBerhasil($pembayaran));
        });
    }

    /**
     * Inject tagihan lunas penuh (Full Payment) untuk pendaftaran manual penyewa baru.
     */
    public function injectManualPenyewaLunas(Penyewa $penyewa, ?int $adminId = null): void
    {
        $tanggalMasuk = Carbon::parse($penyewa->tanggal_masuk);
        $bulanMulai = $tanggalMasuk->month;
        $tahunMulai = $tanggalMasuk->year;

        DB::transaction(function () use ($penyewa, $bulanMulai, $tahunMulai, $adminId) {
            $nominalPokok = $penyewa->harga_sewa ?? ($penyewa->kamar ? $penyewa->kamar->harga_bulan : 0);
            $deposit = $penyewa->deposit ?? 0;
            $nominalTotal = $nominalPokok + $deposit;

            // Cek jika tagihan sudah terlanjur dibuat oleh scheduler
            $tagihan = Tagihan::where([
                'penyewa_id' => $penyewa->id,
                'periode_bulan' => $bulanMulai,
                'periode_tahun' => $tahunMulai
            ])->first();

            if ($tagihan) {
                $tagihan->update([
                    'nominal_pokok' => $nominalPokok,
                    'nominal_denda' => 0,
                    'nominal_total' => $nominalTotal,
                    'status' => 'lunas',
                    'metode_pembayaran' => 'cash',
                    'keterangan' => sprintf(
                        'Tagihan awal lunas via Pendaftaran Manual WhatsApp (Sewa: Rp %s, Deposit: Rp %s).',
                        number_format($nominalPokok, 0, ',', '.'),
                        number_format($deposit, 0, ',', '.')
                    ),
                ]);
            } else {
                // [1] Buat Tagihan dengan status langsung 'lunas'
                $tagihan = Tagihan::create([
                    'penyewa_id'          => $penyewa->id,
                    'order_id'            => sprintf('TGH-MNL-%d-%04d%02d', $penyewa->id, $tahunMulai, $bulanMulai),
                    'periode_bulan'       => $bulanMulai,
                    'periode_tahun'       => $tahunMulai,
                    'tanggal_tagihan'     => Carbon::create($tahunMulai, $bulanMulai, 1),
                    'tanggal_jatuh_tempo' => Carbon::create($tahunMulai, $bulanMulai, 10),
                    'nominal_pokok'       => $nominalPokok,
                    'nominal_denda'       => 0,
                    'nominal_total'       => $nominalTotal,
                    'bulan_keterlambatan' => 0,
                    'status'              => 'lunas',
                    'metode_pembayaran'   => 'cash',
                    'keterangan'          => sprintf(
                        'Tagihan awal lunas via Pendaftaran Manual WhatsApp (Sewa: Rp %s, Deposit: Rp %s).',
                        number_format($nominalPokok, 0, ',', '.'),
                        number_format($deposit, 0, ',', '.')
                    ),
                ]);
            }

            // [2] Buat record Pembayaran agar nota PDF dapat dicetak oleh PdfNotaService
            $pembayaran = Pembayaran::create([
                'tagihan_id'       => $tagihan->id,
                'transaction_id'   => sprintf('PAY-MNL-%d-%d', $penyewa->id, time()),
                'payment_type'     => 'cash',
                'nominal'          => $nominalTotal,
                'status_midtrans'  => 'cash_confirmed',
                'dikonfirmasi_oleh' => $adminId,
                'tanggal_bayar'    => Carbon::now(),
            ]);

            // [3] Trigger event → Dompdf cetak PDF nota + kirim WA/Email konfirmasi
            event(new \App\Events\PembayaranCashDikonfirmasi($pembayaran));
        });
    }

    /**
     * Perpanjangan kontrak manual untuk penyewa harian/mingguan yang overdue.
     */
    public function perpanjangKontrakManual(Penyewa $penyewa, int $durasiTambahan, string $tipeSewa): Tagihan
    {
        return DB::transaction(function () use ($penyewa, $durasiTambahan, $tipeSewa) {
            // Validasi Strict Unit Matching
            if ($tipeSewa !== $penyewa->tipe_sewa) {
                throw new \InvalidArgumentException('Unit perpanjangan harus sama dengan tipe sewa awal penyewa.');
            }

            if ($penyewa->status !== 'aktif' || !in_array($penyewa->tipe_sewa, ['harian', 'mingguan'])) {
                throw new \InvalidArgumentException('Hanya penyewa harian/mingguan aktif yang dapat diperpanjang secara manual.');
            }

            $kamar = $penyewa->kamar;
            if (!$kamar) {
                throw new \Exception('Penyewa tidak memiliki kamar yang valid.');
            }

            // Tentukan tanggal mulai perpanjangan: tanggal keluar seharusnya yang lama
            $tanggalMulaiPerpanjangan = $penyewa->tanggal_keluar_seharusnya ?? $penyewa->tanggal_masuk;

            // Hitung tanggal keluar baru
            $tanggalKeluarBaru = Carbon::parse($tanggalMulaiPerpanjangan);
            if ($tipeSewa === 'harian') {
                $tanggalKeluarBaru->addDays($durasiTambahan);
                $tarifPerUnit = $kamar->harga_harian ?? ($kamar->harga_bulan / 30);
            } else {
                $tanggalKeluarBaru->addWeeks($durasiTambahan);
                $tarifPerUnit = $kamar->harga_mingguan ?? ($kamar->harga_bulan / 4);
            }

            $nominalTagihan = ceil($tarifPerUnit * $durasiTambahan);

            // Update info penyewa (akumulasikan durasi dan ubah tanggal_keluar_seharusnya)
            $penyewa->durasi = $penyewa->durasi + $durasiTambahan;
            $penyewa->tanggal_keluar_seharusnya = $tanggalKeluarBaru->toDateString();
            $penyewa->save();

            // Buat tagihan baru untuk masa perpanjangan
            $now = Carbon::now();
            $orderId = "TGH-EXT-{$penyewa->id}-" . $now->format('YmdHis');

            $tagihan = Tagihan::create([
                'penyewa_id' => $penyewa->id,
                'order_id' => $orderId,
                'periode_bulan' => $now->month,
                'periode_tahun' => $now->year,
                'tanggal_tagihan' => $now->toDateString(),
                'tanggal_jatuh_tempo' => $now->copy()->addDays(2)->toDateString(), // Tempo cepat untuk perpanjangan harian/mingguan
                'nominal_pokok' => $nominalTagihan,
                'nominal_total' => $nominalTagihan,
                'status' => 'pending',
                'keterangan' => "Tagihan Perpanjangan Manual ({$durasiTambahan} {$tipeSewa}) untuk Kamar {$kamar->nomor_kamar}."
            ]);

            event(new TagihanDibuat($tagihan));

            return $tagihan;
        });
    }
}
