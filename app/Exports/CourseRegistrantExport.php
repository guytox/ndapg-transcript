<?php

namespace App\Exports;

use App\Models\RegMonitorItems;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CourseRegistrantExport implements FromCollection, WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    private $sessionId;
    private $semesterId;
    private $courseCode;

    public function __construct($sessionId, $semesterId, $courseCode)
    {
        $this->sessionId = $sessionId;
        $this->semesterId = $semesterId;
        $this->courseCode = $courseCode;
    }

    public function collection()
    {
        //$registrants[] = ['matric','name','ca1','ca2','ca3','ca4','exam'];

        $regs = RegMonitorItems::join('student_records as r','r.id','=','reg_monitor_items.student_id')
                                        ->join('users as u','u.id','=','r.user_id')
                                        ->join('study_levels as l','l.id','=','u.current_level')
                                        ->where(['course_id'=>$this->courseCode, 'session_id'=>$this->sessionId,'semester_id'=>$this->semesterId])
                                        ->select('u.name', 'r.matric','l.level','ca1','ca2','ca3','ca4','exam')
                                        ->get();


        $registrants = new Collection();

        foreach ($regs as $v) {
            $registrants->push((object)[
                'name' => $v->name,
                'matric' => $v->matric,
                'ca1' => convertToNaira($v->ca1),
                'ca2' => convertToNaira($v->ca2),
                'ca3' => convertToNaira($v->ca3),
                'ca4' => convertToNaira($v->ca4),
                'exam' => convertToNaira($v->exam),

            ]);
        }


        return $registrants;
    }

    public function headings(): array{

        return ['name','matricno','level','ca1','ca2','ca3','ca4','exam'];

    }
}
