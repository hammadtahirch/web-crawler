<?php

namespace App\Http\Controllers;

use App\Services\CrawlerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
     * this function helps to get report data.
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function index(Request $request): View|RedirectResponse
    {
        if(!session('email')){
            $this->crawlerService->deleteDataAndSession();
            return redirect()->route('report.create');
        }
        $reportData = $this->crawlerService->getReports();
        return view('index', compact('reportData'));
    }

    /**
     * this function helps to load the create view
     *
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
            $validator = Validator::make(
                $request->all(), [
                'url' => 'required|url',
                'email' => 'required|email',
                'pages' => 'required|gt:0|lt:10'
                ],[
                    'url.required'=>"Please provide your website URL, we promise it's worth the click!",
                    'url.url'=>"It's not a proper URL, it's like a wrong turn, let's make sure we are on the right path",
                    'email.required'=>"Please provide your email, we promise it's worth the click!",
                    'email.url'=>"It's not a proper email, it's like a wrong turn, let's make sure we are on the right path",
                    'pages.required'=>"It looks like we hit a dead end, let's double check the pages number and try again",
                    'pages.gt'=>"Slow down tiger, let's stick to 1 pages or more.",
                    'pages.lt'=>"Slow down tiger, let's stick to 10 pages or less.",
                ]
            );
            if(!session("email")){
                session(["email"=>$request->input('email')]);
            }
            if ($validator->fails()) {
                return redirect()
                    ->route('report.create')
                    ->withErrors($validator->errors());

            }
            $this->crawlerService->parsePageByUrl($request->all());

            return redirect()
                ->route('report.index')
                ->with('success', 'Well, that was a smooth ride! All pages have been successfully crawled.');
        }  catch (\Exception $exception) {
            logger($exception->getMessage());

            return redirect()
                ->route('report.create')
                ->with('error', 'Well this is embarrassing... something went wrong on our end. Please try again later or contact our support team for assistance.');
        }
    }

    /**
     * this function helps to delete records from report table.
     *
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

    /**
     * this function helps to remove all reports data and sessions
     *
     * @return RedirectResponse
     */
    public function deleteDataAndSession():RedirectResponse
    {
        $this->crawlerService->deleteDataAndSession();
        return redirect()->route('report.create');
    }
}
