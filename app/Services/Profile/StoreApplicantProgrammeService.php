<?php

namespace App\Services\Profile;

use App\Models\UserProfile;
use App\Models\UserQualification;
use App\Services\BaseService;
use App\Services\ServiceInterface;

class StoreApplicantProgrammeService extends BaseService implements ServiceInterface
{
    public $data;
    public $user;

    public function __construct(array $data, $user)
    {
        $this->data = $data;
        $this->user = $user;
    }

    public function run()
    {

        $data = $this->data;
        return  UserProfile::updateOrCreate(['user_id' => $this->user->id], [
            'applicant_program' => $data['programme'] ?? $this->user->profile->applicant_program ?? null,
            'is_serving_officer' => $data['service_status'] ?? $this->user->profile->is_serving_officer ?? "0",
            'service_number' => $data['service_number'] ?? $this->user->profile->service_number ?? null,
            'service_rank' => $data['service_rank'] ?? $this->user->profile->service_rank ?? null,
            'user_id' => $this->user->id
        ]);
    }
}
