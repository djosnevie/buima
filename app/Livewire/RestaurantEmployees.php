<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RestaurantEmployees extends Component
{
    public $isOpen = false;
    public $employees = [];
    public $etablissementName = '';

    protected $listeners = ['open-restaurant-employees' => 'open'];

    public function open($etablissementId = null)
    {
        $targetEtablissementId = $etablissementId ?? Auth::user()->etablissement_id;
        $this->isOpen = true;

        if ($targetEtablissementId) {
            $this->employees = User::where('etablissement_id', $targetEtablissementId)
                ->orderBy('name')
                ->get();

            $etablissement = \App\Models\Etablissement::find($targetEtablissementId);
            $this->etablissementName = $etablissement ? $etablissement->nom : 'Restaurant';
        } else {
            $this->employees = [];
            $this->etablissementName = 'Restaurant';
        }
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.restaurant-employees');
    }
}
