<?php

namespace App\Models\Repository;

use App\Models\Eloquent\AvgReport;
use App\Models\Eloquent\Report;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportRepository
{
    /**
     * this function helps to save report in db
     *
     * @param  array  $param contains the report data to be saved
     * @return Report
     */
    public function saveReport(array $param): Report
    {
        $param['email'] = session('email');
        $reportObject = Report::updateOrCreate(
            [
                'page_link' => $param['page_link'],
            ],
            $param
        );
        if ($reportObject->id > 0) {
            return $reportObject;
        }
    }

    /**
     * Save average report data to database
     *
     * @param  array  $param contains the avg report data to be saved
     * @return AvgReport
     */
    public function saveAvgReport(array $param): AvgReport
    {
        $param['email'] = session('email');
        $reportObject = AvgReport::updateOrCreate(
            [
                'site_link' => $param['site_link'],
            ],
            $param
        );
        if ($reportObject->id > 0) {
            return $reportObject;
        }
    }

    /**
     * this function helps to remove report from db.
     *
     * @param  int  $id use for lookup.
     * @return void
     */
    public function destroyReport(int $id): void
    {
        Report::find($id)->delete();
    }

    /**
     * this function helps to remove ave report data from db
     *
     * @param  int  $id use for lookup.
     * @return void
     */
    public function destroyAvgReport(int $id): void
    {
        AvgReport::find($id)->delete();
    }

    /**
     * Helps to get report from db
     *
     * @return LengthAwarePaginator
     */
    public function getReports(): mixed
    {
        return Report::latest()
            ->where(['email' => session('email')])
            ->paginate(10);
    }

    /**
     * Gets avg report data from db table.
     *
     * @return LengthAwarePaginator
     */
    public function getAvgReports(): mixed
    {
        return AvgReport::latest()
            ->where(['email' => session('email')])
            ->paginate(10);
    }

    /**
     * Removes all the report and avg report data and session.
     *
     * @return void
     */
    public function clearOutDataAndSession()
    {
        Report::where(['email' => session('email')])->delete();
        AvgReport::where(['email' => session('email')])->delete();
        session()->forget('email');
    }
}
