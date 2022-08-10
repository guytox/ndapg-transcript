<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request) {
        dd('profile update page');
    }

    public function updateProfile(Request $request){
        $validated = $request->validate([
            'email' => 'required|',
            'phone' => 'required'
        ]);

        dd('profile updated Successfully!!!');
    }
}
