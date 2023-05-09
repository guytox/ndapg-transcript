<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (user()->hasRole('admitted')) {

            return redirect(route('admitted.home'));

        }elseif (user()->hasRole('newstudent')) {
            # return applicant home (this is a placeholder for newly admitted students to take them to their new dashboard)
            return redirect(route('admitted.home'));

        }{

            return view('home');
        }

    }


}
