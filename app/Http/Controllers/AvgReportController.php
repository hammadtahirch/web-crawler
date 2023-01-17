<?php

namespace App\Http\Controllers;
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
     * use to get all ave report data
     *
     * @return View|RedirectResponse
     */
    public function index():View|RedirectResponse
    {
        if(!session('email')){
            $this->crawlerService->deleteDataAndSession();
            return redirect()->route('report.create');
        }
        $avgReportData = $this->crawlerService->getAvgReports();
        return view('ava_report', compact('avgReportData'));
    }

    /**
     * this function helps to delete avg report using id
     *
     * @param  int  $id use for lookup
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
