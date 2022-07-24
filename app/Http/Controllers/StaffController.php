<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class StaffController extends Controller
{

    use HasRoles;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user()->hasRole('admin')) {

            //get all users with staff role
            $staffList = User::role('staff')->with('roles')->with('profile')->select('id','name','email','username', 'phone_number')->get();

            //get the list of Departments
            $deptsDropdown = getUserDepartmentsDropdown(user()->id);

            //get a list of assignable roles
            $staffRoles = Role::whereNotIn('name', ['admin','developer','student','applicant','hod','dean','exam_officer','reg_officer','staff'])->get()->pluck('name','name');


            return view('admin.view-all-staff-list',compact('staffList','deptsDropdown','staffRoles' ));


        }else{

            return redirect(route('home')->with('error',"You do not have rights to view this page, contact ICT"));
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
        if (user()->hasRole('admin')) {

            $this->validate($request, [
                'department_id'=>'required',
                'name'=>'required',
                'usrname'=>'required',
                'email'=>'required',
                'staff_roles'=>'required',
                'gsm_number'=>'required',

            ]);

            //make entries into the user table
            $data = [
                'name'=> $request->name,
                'username'=> $request->usrname,
                'email'=> $request->email,
                'phone_number'=> $request->gsm_number,
                'password' => Hash::make($request->usrname),
                'email_verified_at' => now()
            ];

            $user = User::updateOrCreate(['email' => $request->email, 'username'=>$request->username], $data);

            //asign staff role to the user
            $user->assignRole('staff');

            //assign other roles to the user
            foreach ($request->staff_roles as $role) {
                $user->assignRole($role);
            }

            //create a UserProfile Model for the user
            $userProfile = UserProfile::updateOrCreate(['user_id'=>$user->id, 'department_id'=>$request->department_id],[
                'user_id'=>$user->id,
                'department_id'=>$request->department_id
            ]);
        }

        return back()->with('success', "User Created Successfully");
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
        if (user()->hasRole('admin') && $request->uid =='') {
            $this->validate($request, [
                'department_id'=>'required',
                'name'=>'required',
                'usrname'=>'required',
                'email'=>'required',
                'staff_roles'=>'required',
                'gsm_number'=>'required',
                'psswrd'=>'present',
                'userId'=>'required',

            ]);

            //find the user
            $editedUser = User::find($request->userId);

            //edit parameters
            $editedUser->name = $request->name;
            $editedUser->username = $request->usrname;
            $editedUser->email = $request->email;
            $editedUser->phone_number = $request->gsm_number;
            $editedUser->save();



            //find the profile and Update or Create One if not found
            $data =[
                'department_id' => $request->department_id,
                'user_id' =>$editedUser->id
            ];

            $editProfile = UserProfile::updateOrCreate(['department_id'=>$request->department_id, 'user_id'=>$request->userId], $data);

            //remove roles
            $editedUser->roles()->detach();
            $editedUser->assignRole('staff');

            if ($editedUser->id < 6) {
                $editedUser->assignRole('admin');
            }

            foreach ($request->staff_roles as $role) {
                $editedUser->assignRole($role);
            }

            return back()->with('success','User Modified Successfully!!!');
        }else{
            return back()->with('error', 'Error!!! Something went wrong');
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
        return back()->with('error', "Delete of User not allowed, Contact ICT");
    }
}
