<?php

namespace App\Models\Repository;

use App\Models\Eloquent\Report;

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
     * @param  int  $id
     * @return string
     */
    public function destroyReport(int $id): void
    {
        Report::find($id)->delete();
    }
}
