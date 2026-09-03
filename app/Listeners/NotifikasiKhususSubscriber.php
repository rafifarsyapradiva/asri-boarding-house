<?php

namespace App\Listeners;

use App\Events\ReservasiDibuat;
use App\Events\ReservasiDibayar;
use App\Events\ReservasiDikonfirmasi;
use App\Events\PembayaranBerhasil;
use App\Events\PembayaranCashDikonfirmasi;
use App\Events\KeluhanDibuat;
use App\Events\KeluhanDitanggapi;
use App\Events\TagihanDibuat;
use App\Events\DendaDikenakan;
use App\Models\NotifikasiKhusus;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;

class NotifikasiKhususSubscriber implements ShouldQueue
{
    use InteractsWithQueue;

    public bool $afterCommit = true;

    public const CAT_RESERVASI = 'reservasi';
    public const CAT_TAGIHAN = 'tagihan';
    public const CAT_KELUHAN = 'keluhan';
    public const CAT_ADMIN = 'admin';

    /**
     * Helper formatting rupiah seragam (DRY).
     */
    private function formatRupiah(float|int|null $amount): string
    {
        return 'Rp ' . number_format($amount ?? 0, 0, ',', '.');
    }

    /**
     * Mengekstrak actor_id secara aman tanpa melempar exception di luar HTTP request session context
     */
    private function resolveActorId(?int $explicitActorId = null): ?int
    {
        if ($explicitActorId !== null) {
            return $explicitActorId;
        }

        return auth()->check() ? auth()->id() : null;
    }

    /**
     * Tangani event pendaftaran user baru
     */
    public function handleUserRegistered(Registered $event): void
    {
        $user = $event->user;
        NotifikasiKhusus::log(
            self::CAT_RESERVASI,
            'user_terdaftar',
            "Pengguna baru terdaftar: {$user->nama} ({$user->email})",
            $user->only(['id', 'nama', 'email', 'no_hp']),
            $user->id
        );
    }

    /**
     * Tangani event reservasi baru dibuat
     */
    public function handleReservasiDibuat(ReservasiDibuat $event): void
    {
        $reservasi = $event->reservasi;
        $kamarNomor = $reservasi->kamar?->nomor_kamar ?? '-';
        $userNama = $reservasi->user?->nama ?? 'Penyewa';

        NotifikasiKhusus::log(
            self::CAT_RESERVASI,
            'reservasi_baru',
            "Reservasi baru diajukan oleh {$userNama} untuk Kamar {$kamarNomor} (Durasi: {$reservasi->durasi} bulan, Total: " . $this->formatRupiah($reservasi->total_harga) . ")",
            [
                'reservasi_id' => $reservasi->id,
                'kamar_id' => $reservasi->kamar_id,
                'total_harga' => $reservasi->total_harga,
                'durasi' => $reservasi->durasi,
            ],
            $reservasi->user_id
        );
    }

    /**
     * Tangani event reservasi dibayar
     */
    public function handleReservasiDibayar(ReservasiDibayar $event): void
    {
        $reservasi = $event->reservasi;
        $userNama = $reservasi->user?->nama ?? 'Penyewa';
        $metode = $reservasi->metode_pembayaran ?? 'midtrans';

        NotifikasiKhusus::log(
            self::CAT_RESERVASI,
            'reservasi_dibayar',
            "Reservasi ID #{$reservasi->id} telah lunas dibayar via {$metode} oleh {$userNama}",
            [
                'reservasi_id' => $reservasi->id,
                'metode_pembayaran' => $metode,
                'total_harga' => $reservasi->total_harga,
            ],
            $reservasi->user_id
        );
    }

    /**
     * Tangani event reservasi dikonfirmasi admin
     */
    public function handleReservasiDikonfirmasi(ReservasiDikonfirmasi $event): void
    {
        $reservasi = $event->reservasi;
        $userNama = $reservasi->user?->nama ?? 'Penyewa';
        $kamarNomor = $reservasi->kamar?->nomor_kamar ?? '-';
        $actorId = $this->resolveActorId($event->actorId ?? null);

        NotifikasiKhusus::log(
            self::CAT_ADMIN,
            'reservasi_dikonfirmasi',
            "Reservasi ID #{$reservasi->id} dikonfirmasi oleh Admin (Kamar {$kamarNomor} sekarang ditempati oleh {$userNama})",
            [
                'reservasi_id' => $reservasi->id,
                'penyewa_id' => $reservasi->penyewa_id,
                'kamar_id' => $reservasi->kamar_id,
            ],
            $actorId
        );
    }

    /**
     * Tangani event pembayaran tagihan bulanan berhasil via Midtrans
     */
    public function handlePembayaranBerhasil(PembayaranBerhasil $event): void
    {
        $pembayaran = $event->pembayaran;
        $tagihan = $pembayaran->tagihan;
        $penyewa = $tagihan?->penyewa;
        $userNama = $penyewa?->user?->nama ?? 'Penyewa';
        $kamarNomor = $penyewa?->kamar?->nomor_kamar ?? '-';

        NotifikasiKhusus::log(
            self::CAT_TAGIHAN,
            'tagihan_lunas',
            "Tagihan Periode {$tagihan?->periode_bulan}/{$tagihan?->periode_tahun} Kamar {$kamarNomor} ({$userNama}) lunas via Midtrans sebesar " . $this->formatRupiah($pembayaran->nominal),
            [
                'pembayaran_id' => $pembayaran->id,
                'tagihan_id' => $tagihan?->id,
                'penyewa_id' => $penyewa?->id,
                'nominal' => $pembayaran->nominal,
                'payment_type' => $pembayaran->payment_type,
            ],
            $penyewa?->user_id
        );
    }

    /**
     * Tangani event pembayaran tagihan bulanan cash dikonfirmasi
     */
    public function handlePembayaranCashDikonfirmasi(PembayaranCashDikonfirmasi $event): void
    {
        $pembayaran = $event->pembayaran;
        $tagihan = $pembayaran->tagihan;
        $penyewa = $tagihan?->penyewa;
        $userNama = $penyewa?->user?->nama ?? 'Penyewa';
        $kamarNomor = $penyewa?->kamar?->nomor_kamar ?? '-';
        $actorId = $this->resolveActorId($event->actorId ?? null);

        NotifikasiKhusus::log(
            self::CAT_ADMIN,
            'tagihan_lunas',
            "Tagihan Periode {$tagihan?->periode_bulan}/{$tagihan?->periode_tahun} Kamar {$kamarNomor} ({$userNama}) dikonfirmasi lunas secara Cash oleh Admin sebesar " . $this->formatRupiah($tagihan?->nominal_total),
            [
                'tagihan_id' => $tagihan?->id,
                'penyewa_id' => $penyewa?->id,
                'nominal_total' => $tagihan?->nominal_total,
            ],
            $actorId
        );
    }

    /**
     * Tangani event keluhan baru dibuat oleh penyewa
     */
    public function handleKeluhanDibuat(KeluhanDibuat $event): void
    {
        $keluhan = $event->keluhan;
        $userNama = $keluhan->penyewa?->user?->nama ?? 'Penyewa';
        $kamarNomor = $keluhan->penyewa?->kamar?->nomor_kamar ?? '-';

        NotifikasiKhusus::log(
            self::CAT_KELUHAN,
            'keluhan_baru',
            "Keluhan baru dilaporkan oleh {$userNama} (Kamar {$kamarNomor}): \"{$keluhan->judul}\"",
            [
                'keluhan_id' => $keluhan->id,
                'penyewa_id' => $keluhan->penyewa_id,
                'judul' => $keluhan->judul,
            ],
            $keluhan->penyewa?->user_id
        );
    }

    /**
     * Tangani event keluhan ditanggapi admin
     */
    public function handleKeluhanDitanggapi(KeluhanDitanggapi $event): void
    {
        $keluhan = $event->keluhan;
        $userNama = $keluhan->penyewa?->user?->nama ?? 'Penyewa';
        $actorId = $this->resolveActorId($event->actorId ?? null);

        NotifikasiKhusus::log(
            self::CAT_ADMIN,
            'keluhan_ditanggapi',
            "Keluhan ID #{$keluhan->id} dari {$userNama} ditanggapi oleh Admin. Status: " . strtoupper($keluhan->status),
            [
                'keluhan_id' => $keluhan->id,
                'status' => $keluhan->status,
                'tanggapan' => $keluhan->tanggapan,
            ],
            $actorId
        );
    }

    /**
     * Tangani event tagihan baru dibuat
     */
    public function handleTagihanDibuat(TagihanDibuat $event): void
    {
        $tagihan = $event->tagihan;
        $penyewa = $tagihan->penyewa;
        $userNama = $penyewa?->user?->nama ?? 'Penyewa';
        $kamarNomor = $penyewa?->kamar?->nomor_kamar ?? '-';
        $actorId = $this->resolveActorId($event->actorId ?? null);

        NotifikasiKhusus::log(
            self::CAT_ADMIN,
            'tagihan_dibuat',
            "Tagihan baru dibuat untuk Kamar {$kamarNomor} ({$userNama}) Periode {$tagihan->periode_bulan}/{$tagihan->periode_tahun} sebesar " . $this->formatRupiah($tagihan->nominal_total),
            [
                'tagihan_id' => $tagihan->id,
                'penyewa_id' => $penyewa?->id,
                'nominal_total' => $tagihan->nominal_total,
            ],
            $actorId
        );
    }

    /**
     * Tangani event denda dikenakan
     */
    public function handleDendaDikenakan(DendaDikenakan $event): void
    {
        $tagihan = $event->tagihan;
        $penyewa = $tagihan->penyewa;
        $userNama = $penyewa?->user?->nama ?? 'Penyewa';
        $kamarNomor = $penyewa?->kamar?->nomor_kamar ?? '-';
        $actorId = $this->resolveActorId($event->actorId ?? null);

        NotifikasiKhusus::log(
            self::CAT_ADMIN,
            'denda_dikenakan',
            "Denda keterlambatan dikenakan pada Tagihan ID #{$tagihan->id} Kamar {$kamarNomor} ({$userNama}) sebesar " . $this->formatRupiah($tagihan->nominal_denda),
            [
                'tagihan_id' => $tagihan->id,
                'penyewa_id' => $penyewa?->id,
                'nominal_denda' => $tagihan->nominal_denda,
            ],
            $actorId
        );
    }

    /**
     * Daftarkan pendengar untuk subscriber ini
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            Registered::class => 'handleUserRegistered',
            ReservasiDibuat::class => 'handleReservasiDibuat',
            ReservasiDibayar::class => 'handleReservasiDibayar',
            ReservasiDikonfirmasi::class => 'handleReservasiDikonfirmasi',
            PembayaranBerhasil::class => 'handlePembayaranBerhasil',
            PembayaranCashDikonfirmasi::class => 'handlePembayaranCashDikonfirmasi',
            KeluhanDibuat::class => 'handleKeluhanDibuat',
            KeluhanDitanggapi::class => 'handleKeluhanDitanggapi',
            TagihanDibuat::class => 'handleTagihanDibuat',
            DendaDikenakan::class => 'handleDendaDikenakan',
        ];
    }
}

