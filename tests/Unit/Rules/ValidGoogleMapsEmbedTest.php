<?php

namespace Tests\Unit\Rules;

use App\Rules\ValidGoogleMapsEmbed;
use Tests\TestCase;

class ValidGoogleMapsEmbedTest extends TestCase
{
    /**
     * Test valid Google Maps embed iframe.
     */
    public function test_valid_google_maps_embed(): void
    {
        $rule = new ValidGoogleMapsEmbed();

        $validIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.6052329388147!2d110.4357388!3d-7.0555541" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>';

        $calledFail = false;
        $failCallback = function ($message) use (&$calledFail) {
            $calledFail = true;
        };

        $rule->validate('google_maps_embed', $validIframe, $failCallback);

        $this->assertFalse($calledFail, 'Valid iframe should pass validation.');
    }

    /**
     * Test invalid iframe format (missing start/end tags).
     */
    public function test_invalid_iframe_format(): void
    {
        $rule = new ValidGoogleMapsEmbed();

        $invalidIframe = 'https://www.google.com/maps/embed';

        $calledFail = false;
        $failCallback = function ($message) use (&$calledFail) {
            $calledFail = true;
            $this->assertStringContainsString('harus diawali dengan <iframe', $message);
        };

        $rule->validate('google_maps_embed', $invalidIframe, $failCallback);

        $this->assertTrue($calledFail, 'Invalid format should fail validation.');
    }

    /**
     * Test disallowed host (not google.com).
     */
    public function test_disallowed_host(): void
    {
        $rule = new ValidGoogleMapsEmbed();

        $invalidIframe = '<iframe src="https://www.attacker.com/maps/embed" width="100%"></iframe>';

        $calledFail = false;
        $failCallback = function ($message) use (&$calledFail) {
            $calledFail = true;
            $this->assertStringContainsString('domain Google resmi', $message);
        };

        $rule->validate('google_maps_embed', $invalidIframe, $failCallback);

        $this->assertTrue($calledFail, 'Disallowed host should fail validation.');
    }

    /**
     * Test disallowed iframe attributes (e.g. onload script).
     */
    public function test_disallowed_attributes(): void
    {
        $rule = new ValidGoogleMapsEmbed();

        $invalidIframe = '<iframe src="https://www.google.com/maps/embed" onload="alert(1)"></iframe>';

        $calledFail = false;
        $failCallback = function ($message) use (&$calledFail) {
            $calledFail = true;
            $this->assertStringContainsString('tidak diperbolehkan', $message);
        };

        $rule->validate('google_maps_embed', $invalidIframe, $failCallback);

        $this->assertTrue($calledFail, 'Iframe with onload attribute should fail validation.');
    }

    /**
     * Test XSS injection after the iframe.
     */
    public function test_xss_injection_after_iframe(): void
    {
        $rule = new ValidGoogleMapsEmbed();

        $invalidIframe = '<iframe src="https://www.google.com/maps/embed"></iframe><script>alert(1)</script><iframe></iframe>';

        $calledFail = false;
        $failCallback = function ($message) use (&$calledFail) {
            $calledFail = true;
        };

        $rule->validate('google_maps_embed', $invalidIframe, $failCallback);

        $this->assertTrue($calledFail, 'Iframe with script tag appended should fail validation.');
    }

    /**
     * Test XSS injection via comments and iframe manipulation.
     */
    public function test_xss_injection_via_comments(): void
    {
        $rule = new ValidGoogleMapsEmbed();

        $invalidIframe = '<iframe src="https://www.google.com/maps/embed"></iframe><script>alert(1)</script><!-- </iframe>';

        $calledFail = false;
        $failCallback = function ($message) use (&$calledFail) {
            $calledFail = true;
        };

        $rule->validate('google_maps_embed', $invalidIframe, $failCallback);

        $this->assertTrue($calledFail, 'Iframe with comment-based XSS payload should fail validation.');
    }

    /**
     * Test non-HTTPS source.
     */
    public function test_non_https_src(): void
    {
        $rule = new ValidGoogleMapsEmbed();

        $invalidIframe = '<iframe src="http://www.google.com/maps/embed"></iframe>';

        $calledFail = false;
        $failCallback = function ($message) use (&$calledFail) {
            $calledFail = true;
            $this->assertStringContainsString('protokol HTTPS', $message);
        };

        $rule->validate('google_maps_embed', $invalidIframe, $failCallback);

        $this->assertTrue($calledFail, 'HTTP source should fail validation.');
    }

    /**
     * Test nested HTML element inside iframe.
     */
    public function test_nested_element_inside_iframe(): void
    {
        $rule = new ValidGoogleMapsEmbed();

        $invalidIframe = '<iframe src="https://www.google.com/maps/embed"><div>Fallback</div></iframe>';

        $calledFail = false;
        $failCallback = function ($message) use (&$calledFail) {
            $calledFail = true;
        };

        $rule->validate('google_maps_embed', $invalidIframe, $failCallback);

        $this->assertTrue($calledFail, 'Iframe containing other HTML elements should fail validation.');
    }
}

