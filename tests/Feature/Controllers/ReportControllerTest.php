<?php

namespace Tests\Feature\Controllers;

use App\Services\CrawlerService;
use Mockery\MockInterface;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    /**
     * Test function for checking the status of different routes for report
     *
     * @return void
     */
    public function testReportRoutes(): void
    {
        // Mocking the instance of CrawlerService
        $this->instance(
            CrawlerService::class,
            \Mockery::mock(CrawlerService::class, function (MockInterface $mock) {
                $mock->shouldReceive('getReports')->andReturn([]);
                $mock->shouldReceive('deleteRecords');
                $mock->shouldReceive('parsePageByUrl');
                $mock->shouldReceive('deleteDataAndSession');
            })
        );

        // Make GET request to the report route
        $responseIndex = $this->call('GET', 'report');
        // Make GET request to the report/create route
        $responseCreate = $this->call('GET', 'report/create');
        // Make POST request to the report route
        $responseStore = $this->call('POST', 'report');
        // Make DELETE request to the report/1 route
        $responseDestroy = $this->call('DELETE', 'report/1');
        // Make GET request to the delete_session_data route
        $responseClearSession = $this->call('GET', 'delete_session_data');

        // Assert that the status of the response to the report route is 302
        $this->assertEquals($responseIndex->status(), 302);
        // Assert that the status of the response to the report/create route is 200
        $this->assertEquals($responseCreate->status(), 200);
        // Assert that the status of the response to the report route is 302 after POST request
        $this->assertEquals($responseStore->status(), 302);
        // Assert that the status of the response to the report/1 route is 302 after DELETE request
        $this->assertEquals($responseDestroy->status(), 302);
        // Assert that the status of the response to the delete_session_data route is 302
        $this->assertEquals($responseClearSession->status(), 302);
    }
}
