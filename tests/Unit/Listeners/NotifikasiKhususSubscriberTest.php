<?php

namespace Tests\Unit\Listeners;

use App\Events\KeluhanDibuat;
use App\Events\ReservasiDibuat;
use App\Listeners\NotifikasiKhususSubscriber;
use App\Models\Keluhan;
use App\Models\NotifikasiKhusus;
use App\Models\Reservasi;
use Illuminate\Auth\Events\Registered;
use Illuminate\Events\Dispatcher;

use Tests\TestCase;

class NotifikasiKhususSubscriberTest extends TestCase
{
    public function test_subscribe_mendaftarkan_semua_event_handlers(): void
    {
        $subscriber = new NotifikasiKhususSubscriber();
        $dispatcher = new Dispatcher();

        $events = $subscriber->subscribe($dispatcher);

        $this->assertArrayHasKey(Registered::class, $events);
        $this->assertArrayHasKey(ReservasiDibuat::class, $events);
        $this->assertArrayHasKey(KeluhanDibuat::class, $events);
        $this->assertEquals('handleKeluhanDibuat', $events[KeluhanDibuat::class]);
    }

    public function test_constants_kategori_terdefinisi_dengan_benar(): void
    {
        $this->assertEquals('reservasi', NotifikasiKhususSubscriber::CAT_RESERVASI);
        $this->assertEquals('tagihan', NotifikasiKhususSubscriber::CAT_TAGIHAN);
        $this->assertEquals('keluhan', NotifikasiKhususSubscriber::CAT_KELUHAN);
        $this->assertEquals('admin', NotifikasiKhususSubscriber::CAT_ADMIN);
    }
}
