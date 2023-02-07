<?php

namespace Tests\Feature\Controllers;

use App\Services\CrawlerService;
use Mockery\MockInterface;
use Tests\TestCase;

class AvgReportControllerTest extends TestCase
{
    /**
     * Test function for checking the status of different routes for avg report
     *
     * @return void
     */
    public function testAvgReport(): void
    {
        // Mocking the instance of CrawlerService
        $this->instance(
            CrawlerService::class,
            \Mockery::mock(CrawlerService::class, function (MockInterface $mock) {
                $mock->shouldReceive('deleteAvgRecords');
                $mock->shouldReceive('deleteDataAndSession');
                $mock->shouldReceive('getAvgReports')->andReturn([]);
            })
        );

        // Make GET request to the report route
        $responseIndex = $this->call('GET', 'report');
        // Make DELETE request to the report/1 route
        $responseDestroy = $this->call('DELETE', 'report/1');
        // Assert that the status of the response to the report route is 302
        $this->assertEquals($responseIndex->status(), 302);
        // Assert that the status of the response to the report/1 route is 302 after DELETE request
        $this->assertEquals($responseDestroy->status(), 302);
    }
}
