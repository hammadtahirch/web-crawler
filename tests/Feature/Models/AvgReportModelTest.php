<?php

namespace Tests\Feature\Models;

use App\Models\Eloquent\AvgReport;
use App\Models\Eloquent\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AvgReportModelTest extends TestCase
{
    /**
     * Use the RefreshDatabase and WithFaker traits
     */
    use RefreshDatabase , WithFaker;

    /**
     * Test function for checking the columns of the avg_reports table
     *
     * @return void
     */
    public function test(): void
    {
        $this->assertTrue(
            Schema::hasColumns('avg_reports', [
                'id',
                'email',
                'site_link',
                'avg_page_load_time',
                'avg_title_length',
                'avg_world_count',
                'crawled_pages',
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
        // Create a new avg report instance
        $avgReport = new AvgReport([
            'email' => $this->faker->email,
            'site_link' => $this->faker->url,
            'avg_page_load_time' => 0.02,
            'avg_title_length' => 200,
            'avg_world_count' => 200,
            'crawled_pages' => 6,
            'created_at' => $this->faker->dateTime(now()),
            'updated_at' => $this->faker->dateTime(now()),
        ]);
        $avgReport->save();
        $savedReport = AvgReport::find($avgReport->id);
        $this->assertEquals($avgReport->crawled_pages, $savedReport->crawled_pages);
        $this->assertEquals($avgReport->email, $savedReport->email);
        $this->assertEquals($avgReport->getFillable(), [
            'email',
            'site_link',
            'avg_page_load_time',
            'avg_title_length',
            'avg_world_count',
            'crawled_pages',
            'created_at',
            'updated_at',
        ]);
    }
}
