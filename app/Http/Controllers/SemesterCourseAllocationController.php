<?php

namespace App\Http\Controllers;

use App\Models\CourseAllocationItems;
use App\Models\CourseAllocationMonitor;
use App\Models\CurriculumItem;
use App\Models\Department;
use App\Models\RegMonitorItems;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Spatie\Permission\Traits\HasRoles;

class SemesterCourseAllocationController extends Controller
{
    use HasRoles;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user()->hasRole('hod')) {

            $depts = Department::where('hod_id', user()->id)->get()->pluck('id');

            //return count($curriculumCourses);
            $previousAllocations = CourseAllocationMonitor::whereIn('department_id',$depts)->get();

            if (count($previousAllocations)<1) {
                $previousAllocations = false;
            }

            $deptsDropdown = getUserDepartmentsDropdown(user()->id);
            $semesters = getSemestersDropdown();
            $sessions = getSessionsDropdown();


            return view('admin.configs.viewCourseAllocationMonitor', compact('previousAllocations','deptsDropdown', 'semesters', 'sessions'));

        }else {
            return back()->with('error',"Error!!!! This action is for HOD only, Contact HOD");
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'department_id'=>'required',
            'session_id'=>'required',
            'semester'=>'required',
            'uid'=>'required',

        ]);

        //return $request;

        if (user()->hasRole('hod') && getDepartmentDetailById($request->department_id,'hod')==user()->id) {
            $data = [
                'department_id'=> $request->department_id,
                'session_id'=> $request->session_id,
                'semester_id'=> $request->semester,
                'uid'=> $request->uid,
                'created_by' => user()->id
            ];

            CourseAllocationMonitor::upsert($data, $uniqueBy=['department_id', 'session_id', 'semester_id'], $update=[
                'created_by'
            ]);

            return redirect(route('course-allocation.index'))->with('info',"Allocation Intiated Successfully, You may proceed to allocate courses to lecturers");

        }else {

            return back()->with('error', "Error 4001 !!!! , Contact ICT");
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

        $allMonitor = CourseAllocationMonitor::where('uid', $id)->with('allocationItems')->first();

        if (user()->hasRole('hod') && getDepartmentDetailById($allMonitor->department_id,'hod')==user()->id) {
            // You have all the allocations aleady

            //get all lectures
            $lecturers = User::role('lecturer')->get()->pluck('name','id');

            //get all courses for user department
            $semesterCourses = getUserSemesterCoursesDropdown(user()->id);

            //get all courses that have not been allocated
            $userDepts = getAcademicDepts(user()->id);
            $allocatedCoruses = CourseAllocationItems::where('course_id',$allMonitor->id)->get()->pluck('course_id');
            $unallocated = CurriculumItem::join('semester_courses as g','g.id','=','curriculum_items.semester_courses_id')
                                                ->whereIN('g.department_id', $userDepts)
                                                ->whereNotIn('curriculum_items.course_id', $allocatedCoruses)
                                                ->where(['g.activeStatus'=>'1'])
                                                ->get()
                                                ->pluck('semester_courses_id','id');

            $title = "Department of ".getDepartmentDetailById($allMonitor->department_id,'name');

            return view('admin.configs.viewCourseAllocationItems',compact('allMonitor','lecturers','semesterCourses','allocatedCoruses', 'unallocated','title'));

        }else {
            return back()->with('error','Error 40012 !!! Contact ICT');
        }


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

        if (user()->hasRole('hod')) {
            //validate the request
            $this->validate($request, [
                'semester'=>'required',
                'id'=>'required',

            ]);

            //get the Allocation

            //return $request->id;

            $updatedAllocation = CourseAllocationMonitor::where('uid', $request->id)->first();

            //update the semester alone
            $updatedAllocation->semester_id = intval($request->semester);
            $updatedAllocation->save();

            // redirect back with success message

            return back()->with('success', "Records Updated Successfully !!!!");
        }
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



    public function addAllocationItem(Request $request)
    {

        //check if the user has hod priviledges

        if (user()->hasRole('hod')) {
            //validate the request
            $this->validate($request, [
                'MonitorId'=>'required',
                'semester_courses_id'=>'required',
                'staffId'=>'required',
                'gradingRights'=>'required',

            ]);


            //get the Allocation

            $allocationMonitor = CourseAllocationMonitor::where('uid', $request->MonitorId)->first();

            //proceed with createOrUpdate
            $data = [
                'allocation_id' => $allocationMonitor->id,
                'course_id' => $request->semester_courses_id,
                'staff_id' => $request->staffId,
                'can_grade' => $request->gradingRights
            ];
            $allocationItem = CourseAllocationItems::updateOrCreate(['allocation_id' => $allocationMonitor->id,'course_id' => $request->semester_courses_id, 'staff_id' => $request->staffId, ],$data);


            return redirect(route('course-allocation.show',['course_allocation' => $allocationMonitor->uid]));
        }
    }





}
