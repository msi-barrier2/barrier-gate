<?php

namespace App\Http\Controllers;

use App\Models\LogBarierGate;
use App\Models\RealBarier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;

        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        if (!empty($date_start)  && !empty($date_end)) {
            $report = RealBarier::select('arrival_date')
                ->selectRaw("COUNT(case when type_scenario like '%inbound%' then 1 end) as count_inbounds")
                ->selectRaw("COUNT(case when type_scenario like '%outbound%' then 1 end) as count_outbounds")
                ->selectRaw("COUNT(case when type_scenario like '%others%' then 1 end) as count_others")
                ->selectRaw("(SELECT COUNT(case when status like '%open gate 1%' then 1 end) FROM `log_barier_gates` where  real_bariers.arrival_date = log_barier_gates.arrival_date) as count_gate_1")
                ->selectRaw("(SELECT COUNT(case when status like '%open gate 2%' then 1 end) FROM `log_barier_gates` where  real_bariers.arrival_date = log_barier_gates.arrival_date) as count_gate_2")
                ->groupBy('arrival_date')
                ->whereBetween('arrival_date', ["{$date_start}", "{$date_end}"])
                ->get();
        } else {
            $report = [];
        }

        if ($request->has('download')) {
            $pdf = FacadePdf::loadView('report.pdf', [
                'report' => $report
            ]);
            return $pdf->setPaper('a4', 'potrait')->download('report-pdf.pdf');
        }

        return view('report.index', compact('date_start', 'date_end', 'report'));
    }
}
