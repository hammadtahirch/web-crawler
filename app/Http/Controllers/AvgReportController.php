<?php

namespace App\Http\Controllers;

use App\Services\CrawlerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AvgReportController extends Controller
{
    /**
     * Property to hold an instance of the `CrawlerService` class.
     *
     * @var CrawlerService
     */
    private CrawlerService $crawlerService;

    /**
     * Constructor to create a new instance of the class and inject an instance of `CrawlerService`.
     *
     * @param CrawlerService $crawlerService Injected instance of the `CrawlerService` class.
     */
    public function __construct(CrawlerService $crawlerService)
    {
        $this->crawlerService = $crawlerService;
    }

    /**
     * Method to retrieve all average report data.
     * If email session is not created, they are redirected to the report creation page.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        if (!session('email')) {
            $this->crawlerService->deleteDataAndSession();
            return redirect()->route('report.create');
        }
        $avgReportData = $this->crawlerService->getAvgReports();

        return view('ava_report', compact('avgReportData'));
    }

    /**
     * Method to delete an average report using its ID.
     *
     * @param  int  $id ID of the report to delete.
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->crawlerService->deleteAvgRecords($id);
            return redirect()
                ->route('avg_report.index')
                ->with('success', 'The selected record has been deleted successfully. Thank you.');
        } catch (\Exception $exception) {
            logger($exception->getMessage());

            return redirect()
                ->route('avg_report.index')
                ->with('error', 'Well this is embarrassing... something went wrong on our end. Please try again later or contact our support team for assistance.');
        }
    }
}
