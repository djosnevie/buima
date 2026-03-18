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
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

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
            // Check if we are trying to free an occupied table
            if ($table->statut === 'occupee') {
                $hasPendingOrders = \App\Models\Commande::where('table_id', $table->id)
                    ->whereIn('statut', ['en_attente', 'servie'])
                    ->exists();
                
                if ($hasPendingOrders) {
                    session()->flash('error', 'Impossible de libérer cette table : une commande est en cours (en attente ou servie).');
                    return;
                }
            }

            // Toggle between libre and occupee
            $table->statut = $table->statut === 'libre' ? 'occupee' : 'libre';
            $table->save();
            session()->flash('success', 'Statut de la table mis à jour.');
        }
    }

    public function render()
    {
        $query = Table::query();

        if (auth()->user()->isManager()) {
            $contextSiteId = session('manager_view_site_id');
            if ($contextSiteId) {
                $query->where('etablissement_id', $contextSiteId);
            } else {
                $query->whereIn('etablissement_id', auth()->user()->getAccessibleEtablissementIds());
            }
        } else {
            $query->where('etablissement_id', auth()->user()->etablissement_id);
        }

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
