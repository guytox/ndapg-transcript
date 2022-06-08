<?php

namespace App\Services\Profile;

use App\Models\UserProfile;
use App\Services\BaseService;
use App\Services\ServiceInterface;

class StoreProfileService extends BaseService implements ServiceInterface
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
        return UserProfile::updateOrCreate(['user_id' => $this->user->id],[
            'user_id' => $this->user->id,
            'marital_status' => $this->data['marital_status'] ?? $this->user->profile->marital_status ?? 'single',
            'nationality' => (isset($this->data['nationality']) ? ucfirst($this->data['nationality']): null) ?? $this->user->profile->nationality ?? null,
            'state_id' => $this->data['state_of_origin'] ?? $this->user->profile->state_id ?? null,
            'town' => $this->data['town'] ?? $this->user->profile->town ?? null,
            'gender' => $this->data['gender'] ?? $this->user->profile->gender ?? null,
            'dob' => $this->data['dob'] ?? $this->user->profile->dob ?? null,
            'local_government' => $this->data['local_government'] ?? $this->user->profile->local_government ?? null,
            'permanent_home_address' => $this->data['permanent_home_address'] ?? $this->user->profile->permanent_home_address ?? null,
            'contact_address' => $this->data['contact_address'] ?? $this->user->profile->contact_address ?? null,
            'extra_curricular' => $this->data['extra_curricular'] ?? $this->user->profile->extra_curricular ?? null,
        ]);
    }
}
