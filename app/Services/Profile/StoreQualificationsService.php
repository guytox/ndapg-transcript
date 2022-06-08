<?php

namespace App\Services\Profile;

use App\Models\UserQualification;
use App\Services\BaseService;
use App\Services\ServiceInterface;

class StoreQualificationsService extends BaseService implements ServiceInterface
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
        return  UserQualification::updateOrCreate(['user_id' => $this->user->id, 'certificate_type' => $this->data['certificate_type']], [
            'certificate_type' => $data['certificate_type'] ?? $this->user->qualifications->certificate_type ?? null,
            'qualification_obtained' => $data['qualification_obtained'] ?? $this->user->qualifications->qualification_obtained ?? null,
            'year_obtained' => $data['year_obtained'] ?? $this->user->qualifications->year_obtained ?? null,
            'type' => $data['type'] ?? $this->user->qualifications->type ?? 'school',
            'class' => $data['class'] ?? $this->user->qualifications->class ?? null,
            'expiry_date' => $data['expiry_date'] ?? $this->user->qualifications->expiry_date ?? null,
            'certificate_no' => $data['certificate_no'] ?? $this->user->qualifications->certificate_no ?? null,
            'awarding_institution' => $data['awarding_institution'] ?? $this->user->qualifications->awarding_institution ?? null,
            'user_id' => $this->user->id
        ]);
    }
}
