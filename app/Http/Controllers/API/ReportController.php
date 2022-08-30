<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class ReportController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $afterdate = $request->input('afterdate');
        $beforedate = $request->input('beforedate');

        # get by id
        if ($id)
        {
            $data = Report::find($id);

            if ($data)
            {
                return ResponseFormatter::success(
                    $data,
                    'Data berhasil diambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data tidak ada',
                    404
                );
            }
        }

        $data = Report::orderBy('id', 'desc');

        # get by date
        if($afterdate && $beforedate)
        {
            $data = $data->whereDate('datetime', '>=', $afterdate)->whereDate('datetime', '<=', $beforedate);

        }

        return ResponseFormatter::success(
            $data->paginate($limit),
            'Data berhasil diambil'
        );

    }
    
    public function count()
    {
        $total = Report::count();
        return response()->json(['total' => $total]);
    }

    public function thisyear()
    {   

        $report = Report::select(Report::raw("(count(vulnerability)) as total_report"),
                            Report::raw("(DATE_FORMAT(datetime, '%b')) as month"))
                            ->whereYear('datetime', date('Y'))
                            ->groupBy(Report::raw("DATE_FORMAT(datetime, '%b')"))
                            ->get();
        return response()->json(['data' => $report]);
    }

    public function export(Request $request)
	{
        $afterdate = $request->input('afterdate');
        $beforedate = $request->input('beforedate');

        if($afterdate && $beforedate)
        {
            return Excel::download(new ReportExport($afterdate, $beforedate), 'reports-'.$afterdate.'-to-'.$beforedate.'-'.rand(10,1000).'.xlsx');
        } else {
            return Excel::download(new ReportExport("", ""), 'all-reports-'.rand(10,1000).'.xlsx');
        }
		
	}

    public function reportTime(Request $request)
    {
        $date = $request->input('date');

        $times = DB::table('reports')
            ->select(DB::raw('hour(datetime) as hour'), DB::raw('COUNT(id) as count'))
            ->whereDate('datetime', $date)
            ->groupBy(DB::raw('hour(datetime)'))
            ->orderBy('count', 'DESC')
            ->limit(5)
            ->get();

        $array = json_decode($times, true);
        usort($array, function($a, $b) {
            return $a['hour'] <=> $b['hour'];
        });

        return response()->json(['data' => $array]);
    }
}
