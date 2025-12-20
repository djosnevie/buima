<?php

namespace App\Livewire\Caisses;

use App\Models\Caisse;
use Livewire\Component;

class CaisseList extends Component
{
    public $nom;
    public $code;
    public $editingCaisseId;

    protected $rules = [
        'nom' => 'required|string|max:255',
        'code' => 'required|string|max:50|unique:caisses,code',
    ];

    public function save()
    {
        $this->validate($this->editingCaisseId ? [
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:caisses,code,' . $this->editingCaisseId,
        ] : $this->rules);

        $data = [
            'etablissement_id' => auth()->user()->etablissement_id,
            'nom' => $this->nom,
            'code' => $this->code,
        ];

        if ($this->editingCaisseId) {
            Caisse::find($this->editingCaisseId)->update($data);
            session()->flash('success', 'Caisse mise à jour.');
        } else {
            Caisse::create($data);
            session()->flash('success', 'Caisse créée.');
        }

        $this->reset(['nom', 'code', 'editingCaisseId']);
    }

    public function edit($id)
    {
        $caisse = Caisse::findOrFail($id);
        $this->editingCaisseId = $caisse->id;
        $this->nom = $caisse->nom;
        $this->code = $caisse->code;
    }

    public function toggle($id)
    {
        $caisse = Caisse::findOrFail($id);
        $caisse->active = !$caisse->active;
        $caisse->save();
    }

    public function render()
    {
        $query = Caisse::where('etablissement_id', auth()->user()->etablissement_id);

        // If simple user with specific caisse assigned, restrict view
        if (!auth()->user()->isAdmin() && auth()->user()->caisse_id) {
            $query->where('id', auth()->user()->caisse_id);
        }

        return view('livewire.pages.caisses.caisse-list', [
            'caisses' => $query->get()
        ])->layout('layouts.dashboard');
    }
}
