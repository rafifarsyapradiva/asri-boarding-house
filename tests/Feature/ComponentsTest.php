<?php

namespace Tests\Feature;


use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ComponentsTest extends TestCase
{
    #[Test]
    public function it_renders_reservation_status_badge_correctly()
    {
        $view = $this->blade('<x-reservation-status-badge status="lunas" />');

        $view->assertSee('LUNAS');
        $view->assertSee('admin-badge-success');
    }

    #[Test]
    public function it_handles_null_reservation_status_gracefully()
    {
        $view = $this->blade('<x-reservation-status-badge :status="null" />');

        $view->assertSee('N/A');
        $view->assertSee('admin-badge-neutral');
    }

    #[Test]
    public function it_renders_wa_floating_button_with_custom_phone_and_message()
    {
        $view = $this->blade('<x-wa-float-button waNumber="08123456789" message="Tanya kamar VIP" />');

        $view->assertSee('https://wa.me/08123456789?text=Tanya%20kamar%20VIP', false);
        $view->assertSee('Hubungi Admin via WhatsApp');
    }

    #[Test]
    public function it_renders_toast_component_when_session_success_exists()
    {
        session()->flash('success', 'Reservasi Berhasil!');

        $view = $this->blade('<x-toast />');

        $view->assertSee('Reservasi Berhasil!');
        $view->assertSee('Sukses');
        $view->assertSee('bg-emerald-400');
    }

    #[Test]
    public function it_renders_primary_button_with_neo_brutalist_styling()
    {
        $view = $this->blade('<x-primary-button>Simpan Data</x-primary-button>');

        $view->assertSee('Simpan Data');
        $view->assertSee('border-4 border-black');
        $view->assertSee('shadow-[4px_4px_0px_0px_#000000]');
    }
}
