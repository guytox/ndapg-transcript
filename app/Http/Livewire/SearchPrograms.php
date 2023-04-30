<?php

namespace App\Http\Livewire;

use App\Models\Program;
use Livewire\Component;

class SearchPrograms extends Component
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

            $this->results = Program::where('name','like', "%{$this->query}%")->select('id','name')->orderBy('name','asc')->get()->pluck('name','id');
            // $this->results = Program::where('name','like', "%{$this->query}%")->select('id','name')->orderBy('name','asc')->get();

            //$this->results = DB::table('users')->select('id', DB::raw("CONCAT(users.name,'(',users.username,')') AS full_name", "users.id AS id"))->whereIn('id', $staff)->orderBy('users.name','asc')->get();
        }

        return view('livewire.search-programs');
    }
}
