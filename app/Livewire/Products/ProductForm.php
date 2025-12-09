<?php

namespace App\Livewire\Products;

use App\Models\Produit;
use App\Models\Categorie;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductForm extends Component
{
    use WithFileUploads;

    public $produit;
    public $nom;
    public $description;
    public $prix_vente;
    public $categorie_id;
    public $image;
    public $newImage;
    public $disponible = true;

    public function mount($produit = null)
    {
        if ($produit) {
            $this->produit = Produit::find($produit);
            $this->nom = $this->produit->nom;
            $this->description = $this->produit->description;
            $this->prix_vente = $this->produit->prix_vente;
            $this->categorie_id = $this->produit->categorie_id;
            $this->image = $this->produit->image;
            $this->disponible = $this->produit->disponible;
        }
    }

    protected function rules()
    {
        return [
            'nom' => 'required|min:3',
            'description' => 'nullable',
            'prix_vente' => 'required|numeric|min:0',
            'categorie_id' => 'nullable|exists:categories,id',
            'newImage' => 'nullable|image|max:1024', // 1MB Max
            'disponible' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nom' => $this->nom,
            'description' => $this->description,
            'prix_vente' => $this->prix_vente,
            'categorie_id' => $this->categorie_id ?: null,
            'disponible' => $this->disponible,
            'etablissement_id' => auth()->user()->etablissement_id,
        ];

        if ($this->newImage) {
            $data['image'] = $this->newImage->store('products', 'public');
        }

        if ($this->produit) {
            $this->produit->update($data);
            session()->flash('success', 'Produit mis à jour avec succès.');
        } else {
            Produit::create($data);
            session()->flash('success', 'Produit créé avec succès.');
        }

        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.pages.products.product-form', [
            'categories' => Categorie::where('etablissement_id', auth()->user()->etablissement_id)->get()
        ])->layout('layouts.dashboard');
    }
}
