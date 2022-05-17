<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $date = $request->input('date');

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

        $data = new Report;

        # get by date
        if($date)
        {
            $data = $data->whereDate('datetime', '=', $date);
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
}
