<?php

namespace App\Livewire\Suppliers;

use App\Models\Fournisseur;
use Livewire\Component;

class SupplierForm extends Component
{
    public $supplierId;
    public $nom;
    public $contact;
    public $telephone;
    public $email;
    public $adresse;

    public function mount($id = null)
    {
        if ($id) {
            $supplier = Fournisseur::findOrFail($id);
            $this->supplierId = $supplier->id;
            $this->nom = $supplier->nom;
            $this->contact = $supplier->contact;
            $this->telephone = $supplier->telephone;
            $this->email = $supplier->email;
            $this->adresse = $supplier->adresse;
        }
    }

    protected $rules = [
        'nom' => 'required|string|max:255',
        'contact' => 'nullable|string|max:255',
        'telephone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'adresse' => 'nullable|string',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'etablissement_id' => auth()->user()->etablissement_id,
            'nom' => $this->nom,
            'contact' => $this->contact,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'adresse' => $this->adresse,
        ];

        if ($this->supplierId) {
            Fournisseur::find($this->supplierId)->update($data);
            session()->flash('success', 'Fournisseur mis à jour.');
        } else {
            Fournisseur::create($data);
            session()->flash('success', 'Fournisseur créé.');
        }

        return redirect()->route('suppliers.index');
    }

    public function render()
    {
        return view('livewire.pages.suppliers.supplier-form')->layout('layouts.dashboard');
    }
}
