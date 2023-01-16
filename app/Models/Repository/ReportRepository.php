<?php

namespace App\Models\Repository;

use App\Models\Eloquent\AvgReport;
use App\Models\Eloquent\Report;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportRepository
{
    /**
     * @param  array  $param
     * @return Report
     */
    public function saveReport(array $param): Report
    {
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
     * @param array $param
     * @return AvgReport
     */
    public function saveAvgReport(array $param): AvgReport
    {
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
     * @param int $id
     * @return void
     */
    public function destroyReport(int $id): void
    {
        Report::find($id)->delete();
    }

    /**
     * @param int $id
     * @return void
     */
    public function destroyAvgReport(int $id): void
    {
        AvgReport::find($id)->delete();
    }

    /**
     * @return LengthAwarePaginator
     */
    public function getReports(): mixed
    {
        return Report::latest()->paginate(10);
    }

    /**
     * @return LengthAwarePaginator
     */
    public function getAvgReports(): mixed
    {
        return AvgReport::latest()->paginate(10);
    }
}
