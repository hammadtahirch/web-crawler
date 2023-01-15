<?php

namespace App\Http\Controllers;

use App\Models\Eloquent\Report;
use App\Services\CrawlerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
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
    public function index(Request $request): View
    {
        $reportData = Report::latest()->paginate(5);

        return view('index', compact('reportData'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * @return view
     */
    public function create(): View
    {
        return view('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'url' => 'required|url',
                'pages' => 'required|min:1|max:10',
            ]);
            $this->crawlerService->parsePageByUrl($request->all());

            return redirect()
                ->route('report.index')
                ->with('success', 'Well, that was a smooth ride! All pages have been successfully crawled.');
        } catch (\Exception $exception) {
            logger($exception->getMessage());

            return redirect()
                ->route('report.index')
                ->with('error', 'Well this is embarrassing... something went wrong on our end. Please try again later or contact our support team for assistance.');
        }
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->crawlerService->deleteRecords($id);

            return redirect()
                ->route('report.index')
                ->with('success', 'The selected record has been deleted successfully. Thank you.');
        } catch (\Exception $exception) {
            logger($exception->getMessage());

            return redirect()
                ->route('report.index')
                ->with('error', 'Well this is embarrassing... something went wrong on our end. Please try again later or contact our support team for assistance.');
        }
    }
}
