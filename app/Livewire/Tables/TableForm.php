<?php

namespace App\Livewire\Tables;

use App\Models\Table;
use Livewire\Component;

class TableForm extends Component
{
    public $table;
    public $numero;
    public $capacite;
    public $statut = 'libre';

    public function mount($table = null)
    {
        if ($table) {
            $this->table = Table::find($table);
            $this->numero = $this->table->numero;
            $this->capacite = $this->table->capacite;
            $this->statut = $this->table->statut;
        }
    }

    protected function rules()
    {
        return [
            'numero' => 'required|string|unique:tables,numero,' . ($this->table->id ?? 'NULL'),
            'capacite' => 'required|integer|min:1',
            'statut' => 'required|in:libre,occupee,reservee',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'numero' => $this->numero,
            'capacite' => $this->capacite,
            'statut' => $this->statut,
            'etablissement_id' => auth()->user()->etablissement_id,
        ];

        if ($this->table) {
            $this->table->update($data);
            session()->flash('success', 'Table mise à jour avec succès.');
        } else {
            Table::create($data);
            session()->flash('success', 'Table créée avec succès.');
        }

        return redirect()->route('tables.index');
    }

    public function render()
    {
        return view('livewire.pages.tables.table-form')->layout('layouts.dashboard');
    }
}
