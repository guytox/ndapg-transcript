<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Profile\StoreProfileService;

class ProfileController extends Controller
{
    public function addData()
    {
        return (new StoreProfileService())->run();
    }
}
