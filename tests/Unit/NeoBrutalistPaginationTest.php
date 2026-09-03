<?php

namespace Tests\Unit;


use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class NeoBrutalistPaginationTest extends TestCase
{
    #[Test]
    public function it_renders_length_aware_pagination_on_first_page_correctly()
    {
        $paginator = new LengthAwarePaginator(
            array_fill(0, 30, 'item'),
            30, // total
            10, // per page
            1,  // current page
            ['path' => 'http://localhost/test']
        );

        $rendered = $paginator->render('vendor.pagination.neo-brutalist')->toHtml();

        $this->assertStringContainsString('Showing', $rendered);
        $this->assertStringContainsString('1', $rendered);
        $this->assertStringContainsString('10', $rendered);
        $this->assertStringContainsString('30', $rendered);
        $this->assertStringContainsString('aria-disabled="true"', $rendered);
    }

    #[Test]
    public function it_renders_length_aware_pagination_on_middle_page_correctly()
    {
        $paginator = new LengthAwarePaginator(
            array_fill(0, 30, 'item'),
            30, // total
            10, // per page
            2,  // current page
            ['path' => 'http://localhost/test']
        );

        $rendered = $paginator->render('vendor.pagination.neo-brutalist')->toHtml();

        $this->assertStringContainsString('rel="prev"', $rendered);
        $this->assertStringContainsString('rel="next"', $rendered);
        $this->assertStringContainsString('aria-current="page"', $rendered);
    }

    #[Test]
    public function it_renders_length_aware_pagination_on_last_page_correctly()
    {
        $paginator = new LengthAwarePaginator(
            array_fill(0, 30, 'item'),
            30, // total
            10, // per page
            3,  // current page
            ['path' => 'http://localhost/test']
        );

        $rendered = $paginator->render('vendor.pagination.neo-brutalist')->toHtml();

        $this->assertStringContainsString('rel="prev"', $rendered);
        $this->assertStringContainsString('aria-disabled="true"', $rendered);
    }

    #[Test]
    public function it_renders_simple_paginator_without_errors()
    {
        $paginator = new Paginator(
            array_fill(0, 15, 'item'),
            10,
            2,
            ['path' => 'http://localhost/test']
        );

        $rendered = $paginator->render('vendor.pagination.neo-brutalist')->toHtml();

        $this->assertStringContainsString('Page 2', $rendered);
        $this->assertStringNotContainsString('Showing', $rendered);
    }

    #[Test]
    public function it_does_not_render_nav_when_there_are_no_extra_pages()
    {
        $paginator = new LengthAwarePaginator(
            array_fill(0, 5, 'item'),
            5,
            10,
            1,
            ['path' => 'http://localhost/test']
        );

        $rendered = $paginator->render('vendor.pagination.neo-brutalist')->toHtml();

        $this->assertStringNotContainsString('<nav', $rendered);
    }
}

