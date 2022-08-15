<?php

namespace App\Exports;

use App\Models\Report;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ReportExport implements FromQuery,WithHeadings
{
    public function __construct(String $afterdate, String $beforedate)
    {
        $this->afterdate = $afterdate;
        $this->beforedate = $beforedate;
    }

    public function query()
    {
        if($this->afterdate){
            return Report::query()->select('ip','description','vulnerability','datetime','method','payload','url','country','severity')->whereDate('datetime', '>=', $this->afterdate)->whereDate('datetime', '<=', $this->beforedate);
        } else {
            return Report::query()->select('ip','description','vulnerability','datetime','method','payload','url','country','severity');
        }
    }

    public function headings(): array
    {
        return [
            'ip',
            'description',
            'vulnerability',
            'datetime',
            'method',
            'payload',
            'url',
            'country',
            'severity'
        ];
    }
}
