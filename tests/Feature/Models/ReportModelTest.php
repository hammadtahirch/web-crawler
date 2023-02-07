<?php

namespace Tests\Feature\Models;

use App\Models\Eloquent\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReportModelTest extends TestCase
{
    /**
     * Use the RefreshDatabase and WithFaker traits
     */
    use RefreshDatabase , WithFaker;

    /**
     * Test function for checking the columns of the reports table
     *
     * @return void
     */
    public function test(): void
    {
        $this->assertTrue(
            Schema::hasColumns('reports', [
                'id',
                'email',
                'page_link',
                'status_code',
                'images_links',
                'internal_links',
                'external_links',
                'page_load_time',
                'word_count',
                'title_length',
                'created_at',
                'updated_at',
            ]), 1);
    }

    /**
     * Test function for checking the functionality of the report model
     *
     * @return void
     */
    public function testReportModelFunctionality(): void
    {
        $report = new Report([
            'email' => $this->faker->email,
            'page_link' => $this->faker->url,
            'status_code' => 200,
            'images_links' => 4,
            'internal_links' => 10,
            'external_links' => 10,
            'page_load_time' => 10,
            'word_count' => 200,
            'title_length' => 11,
            'created_at' => $this->faker->dateTime(now()),
            'updated_at' => $this->faker->dateTime(now()),
        ]);
        $report->save();
        $savedReport = Report::find($report->id);
        $this->assertEquals($report->page_link, $savedReport->page_link);
        $this->assertEquals($report->email, $savedReport->email);
        $this->assertEquals($report->getFillable(), [
            'email',
            'page_link',
            'status_code',
            'images_links',
            'internal_links',
            'external_links',
            'page_load_time',
            'word_count',
            'title_length',
            'created_at',
            'updated_at',
        ]);
    }
}
