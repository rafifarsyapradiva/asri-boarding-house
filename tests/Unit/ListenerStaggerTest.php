<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Listeners\HandleTagihanDibuat;
use App\Events\TagihanDibuat;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Carbon;

class ListenerStaggerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        HandleTagihanDibuat::resetStaggerDelay();
    }

    public function test_stagger_delay_increments()
    {
        Queue::fake();

        $listener = new HandleTagihanDibuat();
        $tagihan = new Tagihan();

        // Panggil pertama -> delay 0s
        $listener->handle(new TagihanDibuat($tagihan));
        
        // Panggil kedua -> delay 3s
        $listener->handle(new TagihanDibuat($tagihan));

        // Karena static variable, nilai delaySeconds internal bertambah ke 6
        $this->assertEquals(6, $this->getPrivateProperty($listener, 'delaySeconds'));
    }

    public function test_stagger_delay_resets_after_threshold()
    {
        Queue::fake();
        Carbon::setTestNow(now());

        $listener = new HandleTagihanDibuat();
        $tagihan = new Tagihan();

        // Panggil pertama -> delay 0s
        $listener->handle(new TagihanDibuat($tagihan));

        // Maju 6 detik ke depan (melebihi threshold default 5 detik)
        $this->travel(6)->seconds();

        // Panggil kedua -> harus direset kembali ke 0s, dan bertambah ke 3s untuk selanjutnya
        $listener->handle(new TagihanDibuat($tagihan));
        $this->assertEquals(3, $this->getPrivateProperty($listener, 'delaySeconds'));
    }

    public function test_reset_stagger_delay_explicitly()
    {
        $listener = new HandleTagihanDibuat();
        
        // Simulasikan kenaikan delay
        $this->callPrivateMethod($listener, 'getStaggerDelay');
        $this->assertEquals(3, $this->getPrivateProperty($listener, 'delaySeconds'));

        // Reset delay secara manual
        HandleTagihanDibuat::resetStaggerDelay();
        $this->assertEquals(0, $this->getPrivateProperty($listener, 'delaySeconds'));
    }

    public function test_respects_custom_stagger_properties_if_overridden()
    {
        // Buat kelas dinamis untuk menguji properti override
        $customStagger = new class {
            use \App\Listeners\Traits\StaggersJobs;
            protected int $staggerStep = 5;
            protected int $staggerMaxDelay = 10;
            protected float $staggerResetThreshold = 10.0;

            public function getStaggerDelayPublic(): int
            {
                return $this->getStaggerDelay();
            }
        };

        $customStagger::resetStaggerDelay();

        // Tes Step
        $this->assertEquals(0, $customStagger->getStaggerDelayPublic());
        $this->assertEquals(5, $customStagger->getStaggerDelayPublic());
        $this->assertEquals(10, $customStagger->getStaggerDelayPublic());
        $this->assertEquals(10, $customStagger->getStaggerDelayPublic()); // Capped

        // Tes Reset Threshold (maju 11 detik)
        Carbon::setTestNow(now());
        $customStagger->getStaggerDelayPublic();
        $this->travel(11)->seconds();
        $this->assertEquals(0, $customStagger->getStaggerDelayPublic());
    }

    public function test_stagger_delay_is_isolated_between_different_listeners()
    {
        $listenerA = new class {
            use \App\Listeners\Traits\StaggersJobs;
            public function getDelay(): int { return $this->getStaggerDelay(); }
        };
        $listenerB = new class {
            use \App\Listeners\Traits\StaggersJobs;
            public function getDelay(): int { return $this->getStaggerDelay(); }
        };

        $listenerA::resetStaggerDelay();
        $listenerB::resetStaggerDelay();

        // Pemanggilan Listener A
        $this->assertEquals(0, $listenerA->getDelay());
        $this->assertEquals(3, $listenerA->getDelay());

        // Listener B harus mulai dari 0s (terisolasi lewat Late Static Binding)
        $this->assertEquals(0, $listenerB->getDelay());
        $this->assertEquals(3, $listenerB->getDelay());
    }

    public function test_respects_queue_stagger_config_values()
    {
        config(['queue.stagger.step' => 10]);
        config(['queue.stagger.max_delay' => 20]);

        $configStagger = new class {
            use \App\Listeners\Traits\StaggersJobs;
            public function getDelay(): int { return $this->getStaggerDelay(); }
        };

        $configStagger::resetStaggerDelay();

        $this->assertEquals(0, $configStagger->getDelay());
        $this->assertEquals(10, $configStagger->getDelay());
        $this->assertEquals(20, $configStagger->getDelay());
        $this->assertEquals(20, $configStagger->getDelay()); // Capped pada max_delay 20
    }

    protected function getPrivateProperty($object, $propertyName)
    {
        $reflector = new \ReflectionClass($object);
        $property = $reflector->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    protected function callPrivateMethod($object, $methodName, array $parameters = [])
    {
        $reflector = new \ReflectionClass($object);
        $method = $reflector->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
