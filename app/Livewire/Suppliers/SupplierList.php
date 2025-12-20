<?php

namespace App\Livewire\Suppliers;

use App\Models\Fournisseur;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierList extends Component
{
    use WithPagination;

    public $search = '';

    public function delete($id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $supplier = Fournisseur::find($id);
        if ($supplier) {
            try {
                $supplier->delete();
                session()->flash('success', 'Fournisseur supprimé avec succès.');
            } catch (\Exception $e) {
                session()->flash('error', 'Impossible de supprimer ce fournisseur car il est lié à des approvisionnements.');
            }
        }
    }

    public function render()
    {
        $query = Fournisseur::where('etablissement_id', auth()->user()->etablissement_id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nom', 'like', '%' . $this->search . '%')
                    ->orWhere('contact', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.pages.suppliers.supplier-list', [
            'suppliers' => $query->orderBy('nom')->paginate(10)
        ])->layout('layouts.dashboard');
    }
}
