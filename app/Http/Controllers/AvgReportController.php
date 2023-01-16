<?php

namespace App\Http\Controllers;

use App\Models\Eloquent\AvgReport;
use App\Services\CrawlerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvgReportController extends Controller
{
    /**
     * @var CrawlerService
     */
    private CrawlerService $crawlerService;

    /**
     * class constructor
     */
    public function __construct(CrawlerService $crawlerService)
    {
        $this->crawlerService = $crawlerService;
    }

    /**
     * @param  Request  $request
     * @return view
     */
    public function index(Request $request)
    {
        $avgReportData = $this->crawlerService->getAvgReports();
        return view('ava_report', compact('avgReportData'));
    }

    /**
     * @param  int  $id
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
