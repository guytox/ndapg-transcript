<?php

namespace App\Http\Controllers;

use App\Models\Defferment;
use App\Models\RegMonitor;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Http\Request;

class StudentDeffermentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect(route('defermentMgt.create'))->with('error',"This Module is still Under Construction");
        #get all defferment students

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        # we return a view requesting for matric number
        return view('admin.select-student-for-defferment');
    }

    public function viewStudentDetails(Request $request){

        if (user()->hasRole('admin|dean_pg|dap')) {

        }else{
            return back()->with('error', "Error!!!, You do not have the right priviledges to perform this action");
        }

        $this->validate($request, [

            'd_std'=>'required',
            'c_sess'=>'required',
            'r_sess'=>'required',
            'd_amount'=>'required',

        ]);

        $std = StudentRecord::where('matric', $request->d_std)->first();

        #get the regMonitors for this session
        $thisSessionReg = RegMonitor::join('curricula as c', 'c.id','=','reg_monitors.curricula_id')
                                    ->where('reg_monitors.student_id', $std->id)
                                    ->where('reg_monitors.session_id', '>=', $request->c_sess)
                                    ->select('reg_monitors.*','c.title')
                                    ->get();
        $title = "Preview Student Information before Semester Defferment deferment";
        $beginSess = $request->c_sess;
        $returnSess = $request->r_sess;
        $amt = $request->d_amount;

        return view('admin.previewStudentBeforeDefferment',compact('std','thisSessionReg','title', 'beginSess','returnSess', 'amt'));
        return $request;

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request;
        if (user()->hasRole('admin|vc|dap')) {

            $this->validate($request, [

                'r_sess'=>'required',
                'amt'=>'required',
                'd_sess'=>'required',
                'std_id'=>'required',

            ]);
            #user has the correct role
            #enter deferment records in the table
            $data = [
                'uid' => uniqid('def_'),
                'student_id'=> $request->std_id,
                'd_session' => $request->d_sess,
                'r_session' => $request->r_sess,
                'amount_payable' => convertToKobo($request->amt),
            ];
            $toDeffer = Defferment::updateOrCreate([
                'student_id'=> $request->std_id,
                'd_session' => $request->d_sess,
                'r_session' => $request->r_sess,
            ],$data);

            if ($toDeffer) {
                #deferment record entered successuflly, proceed with the process
                #find the student
                $stdRecord = StudentRecord::find($request->std_id);
                #set in defferment value in student record
                $stdRecord->in_defferment = true;
                $stdRecord->save();
                #remove student role
                $stdUsr = User::find($stdRecord->user_id);
                $stdUsr->removeRole('student');
                #delete registrations affected
                if ($request->regMonitor) {
                    #regMonitors to delete exist
                    foreach ($request->regMonitor as $v) {
                        #find the course reg
                        $regM = RegMonitor::where('uid', $v)->first();
                        if ($regM) {
                            $regM->delete();
                        }
                    }
                }
                #return back to index.
                return redirect(route('defermentMgt.create'))->with('info',"Student Defferment Successful");

            }


        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


}
