<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Events\DendaDikenakan;
use App\Events\KeluhanDibuat;
use App\Events\KeluhanDitanggapi;
use App\Events\NotifikasiWali;
use App\Events\PembayaranBerhasil;
use App\Events\PembayaranCashDikonfirmasi;
use App\Events\ReminderPenyewa;
use App\Events\ReservasiDibayar;
use App\Events\ReservasiDibuat;
use App\Events\ReservasiDikonfirmasi;
use App\Events\TagihanDibuat;
use App\Models\Keluhan;
use App\Models\Pembayaran;
use App\Models\Penyewa;
use App\Models\Reservasi;
use App\Models\Tagihan;
use Error;
use Tests\TestCase;

class EventsTest extends TestCase
{
    public function test_all_events_instantiate_correctly_with_payload(): void
    {
        $tagihan = new Tagihan();
        $keluhan = new Keluhan();
        $pembayaran = new Pembayaran();
        $reservasi = new Reservasi();
        $penyewa = new Penyewa();

        $dendaDikenakan = new DendaDikenakan($tagihan);
        $this->assertSame($tagihan, $dendaDikenakan->tagihan);

        $keluhanDibuat = new KeluhanDibuat($keluhan);
        $this->assertSame($keluhan, $keluhanDibuat->keluhan);

        $keluhanDitanggapi = new KeluhanDitanggapi($keluhan);
        $this->assertSame($keluhan, $keluhanDitanggapi->keluhan);

        $notifikasiWali = new NotifikasiWali($tagihan);
        $this->assertSame($tagihan, $notifikasiWali->tagihan);

        $pembayaranBerhasil = new PembayaranBerhasil($pembayaran);
        $this->assertSame($pembayaran, $pembayaranBerhasil->pembayaran);

        $pembayaranCash = new PembayaranCashDikonfirmasi($pembayaran);
        $this->assertSame($pembayaran, $pembayaranCash->pembayaran);

        $reminderPenyewa = new ReminderPenyewa($tagihan);
        $this->assertSame($tagihan, $reminderPenyewa->tagihan);

        $reservasiDibayar = new ReservasiDibayar($reservasi);
        $this->assertSame($reservasi, $reservasiDibayar->reservasi);

        $reservasiDibuat = new ReservasiDibuat($reservasi);
        $this->assertSame($reservasi, $reservasiDibuat->reservasi);

        $reservasiDikonfirmasi = new ReservasiDikonfirmasi($reservasi, $penyewa);
        $this->assertSame($reservasi, $reservasiDikonfirmasi->reservasi);
        $this->assertSame($penyewa, $reservasiDikonfirmasi->penyewa);

        $tagihanDibuat = new TagihanDibuat($tagihan);
        $this->assertSame($tagihan, $tagihanDibuat->tagihan);
    }

    public function test_event_properties_are_readonly_and_cannot_be_mutated(): void
    {
        $tagihan = new Tagihan();
        $event = new DendaDikenakan($tagihan);

        $this->expectException(Error::class);

        /** @phpstan-ignore-next-line */
        $event->tagihan = new Tagihan();
    }

    public function test_reservasi_dikonfirmasi_get_penyewa_returns_explicit_penyewa(): void
    {
        $reservasi = new Reservasi();
        $penyewaEksplisit = new Penyewa(['nama' => 'Budi']);

        $event = new ReservasiDikonfirmasi($reservasi, $penyewaEksplisit);

        $this->assertSame($penyewaEksplisit, $event->getPenyewa());
    }

    public function test_reservasi_dikonfirmasi_get_penyewa_falls_back_to_relation(): void
    {
        $penyewaRelasi = new Penyewa(['nama' => 'Andi']);
        $reservasi = new Reservasi();
        $reservasi->setRelation('penyewa', $penyewaRelasi);

        $event = new ReservasiDikonfirmasi($reservasi, null);

        $this->assertSame($penyewaRelasi, $event->getPenyewa());
    }
}
