<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use App\Events\PembayaranBerhasil;
use App\Events\PembayaranCashDikonfirmasi;
use App\Events\ReservasiDibuat;
use App\Events\ReservasiDibayar;
use App\Events\ReservasiDikonfirmasi;
use App\Events\KeluhanDibuat;
use App\Events\KeluhanDitanggapi;
use App\Listeners\GeneratePdfNotaListener;
use App\Listeners\HandleReservasiDibuat;
use App\Listeners\HandleReservasiDibayar;
use App\Listeners\HandleReservasiDikonfirmasi;
use App\Listeners\KirimNotifikasiKeluhanDibuat;
use App\Listeners\KirimNotifikasiKeluhanDitanggapi;

class EventRegistrationTest extends TestCase
{
    /**
     * Memastikan GeneratePdfNotaListener terdaftar secara manual dengan benar.
     */
    public function test_manual_listeners_are_registered_correctly(): void
    {
        $this->assertHasListener(PembayaranBerhasil::class, GeneratePdfNotaListener::class);
        $this->assertHasListener(PembayaranCashDikonfirmasi::class, GeneratePdfNotaListener::class);
    }

    /**
     * Memastikan listener yang mengandalkan Event Auto-Discovery terdaftar dengan benar.
     */
    public function test_auto_discovered_listeners_are_registered_correctly(): void
    {
        $this->assertHasListener(ReservasiDibuat::class, HandleReservasiDibuat::class);
        $this->assertHasListener(ReservasiDibayar::class, HandleReservasiDibayar::class);
        $this->assertHasListener(ReservasiDikonfirmasi::class, HandleReservasiDikonfirmasi::class);
        $this->assertHasListener(KeluhanDibuat::class, KirimNotifikasiKeluhanDibuat::class);
        $this->assertHasListener(KeluhanDitanggapi::class, KirimNotifikasiKeluhanDitanggapi::class);
    }

    /**
     * Helper asserting that an event has a specific listener class registered.
     */
    private function assertHasListener(string $eventClass, string $listenerClass): void
    {
        $listeners = Event::getListeners($eventClass);
        $found = false;

        foreach ($listeners as $listenerClosure) {
            $reflection = new \ReflectionFunction($listenerClosure);
            $staticVars = $reflection->getStaticVariables();

            if (isset($staticVars['listener'])) {
                $listener = $staticVars['listener'];

                if (is_string($listener) && str_contains($listener, $listenerClass)) {
                    $found = true;
                    break;
                }
                if (is_array($listener) && isset($listener[0]) && $listener[0] === $listenerClass) {
                    $found = true;
                    break;
                }
            }
        }

        $this->assertTrue($found, "Listener {$listenerClass} tidak terdaftar untuk event {$eventClass}");
    }
}
