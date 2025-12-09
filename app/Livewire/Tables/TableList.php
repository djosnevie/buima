<?php

namespace App\Livewire\Tables;

use App\Models\Table;
use Livewire\Component;
use Livewire\WithPagination;

class TableList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'tous';

    public function setFilter($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function delete($id)
    {
        $table = Table::find($id);
        if ($table) {
            try {
                $table->delete();
                session()->flash('success', 'Table supprimée avec succès.');
            } catch (\Illuminate\Database\QueryException $e) {
                // Check if it's a foreign key constraint error
                if ($e->getCode() == '23000') {
                    session()->flash('error', 'Impossible de supprimer cette table car elle est utilisée dans des commandes.');
                } else {
                    session()->flash('error', 'Une erreur est survenue lors de la suppression.');
                }
            }
        }
    }

    public function toggleStatus($id)
    {
        $table = Table::find($id);
        if ($table) {
            // Toggle between libre and occupee
            $table->statut = $table->statut === 'libre' ? 'occupee' : 'libre';
            $table->save();
            session()->flash('success', 'Statut de la table mis à jour.');
        }
    }

    public function render()
    {
        $query = Table::where('etablissement_id', auth()->user()->etablissement_id);

        if ($this->search) {
            $query->where('numero', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus !== 'tous') {
            $query->where('statut', $this->filterStatus);
        }

        return view('livewire.pages.tables.table-list', [
            'tables' => $query->orderBy('numero')->paginate(14)
        ])->layout('layouts.dashboard');
    }
}
