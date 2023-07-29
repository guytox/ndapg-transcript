<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SearchLecturer extends Component
{
    public $query = '';

    public $results;


    protected $queryString = ['query'];

    public function mount(){
        $this->reset();
    }

    public function render()
    {
        if (strlen($this->query) >1) {

            $staff = User::role('lecturer')->where('name','like', "%{$this->query}%")->orWhere('username','like', "%{$this->query}%")->select('id')->get()->pluck('id');

            $this->results = DB::table('users')->select('id', DB::raw("CONCAT(users.name,'(',users.username,')') AS full_name", "users.id AS id"))->whereIn('id', $staff)->orderBy('users.name','asc')->get();
        }
        return view('livewire.search-lecturer');
    }
}
