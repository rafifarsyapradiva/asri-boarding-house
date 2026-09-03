<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Jobs\Traits\QueueConfiguration;

class QueueConfigurationTest extends TestCase
{
    /**
     * Helper to instantiate an anonymous class using the trait.
     */
    private function getJobWithTrait()
    {
        return new class {
            use QueueConfiguration;
        };
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Clear configuration changes between tests
        config(['queue.jobs.tries' => null]);
        config(['queue.jobs.timeout' => null]);
        config(['queue.jobs.backoff' => null]);
    }

    /**
     * Test default values when no config is set.
     */
    public function test_default_values(): void
    {
        $job = $this->getJobWithTrait();

        $this->assertEquals(3, $job->tries());
        $this->assertEquals(60, $job->timeout());
        // Since tests run in 'testing' env, backoff() should return 0 by default.
        $this->assertEquals(0, $job->backoff());
    }

    /**
     * Test configuration can be customized dynamically.
     */
    public function test_customized_configuration(): void
    {
        $job = $this->getJobWithTrait();

        config([
            'queue.jobs.tries' => 5,
            'queue.jobs.timeout' => 90,
        ]);

        $this->assertEquals(5, $job->tries());
        $this->assertEquals(90, $job->timeout());
    }

    /**
     * Test backoff in non-testing environments.
     */
    public function test_backoff_in_non_testing_environment(): void
    {
        $job = $this->getJobWithTrait();
        $originalEnv = app()->environment();
        
        app()['env'] = 'production';
        try {
            $this->assertEquals([30, 60, 120], $job->backoff());

            config(['queue.jobs.backoff' => [10, 20]]);
            $this->assertEquals([10, 20], $job->backoff());
        } finally {
            app()['env'] = $originalEnv;
        }
    }

    /**
     * Test property default value and type.
     */
    public function test_properties(): void
    {
        $job = $this->getJobWithTrait();

        $this->assertTrue($job->deleteWhenMissingModels);
        $this->assertNull($job->afterCommit);
    }
}
