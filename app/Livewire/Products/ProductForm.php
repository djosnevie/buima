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
    public $prix_achat;
    public $tva;
    public $gestion_stock = false;
    public $type = 'plat';
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
            $this->prix_achat = $this->produit->prix_achat;
            $this->tva = $this->produit->tva;
            $this->type = $this->produit->type ?? 'plat';
            $this->gestion_stock = $this->produit->gestion_stock;
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
            'prix_achat' => 'nullable|numeric|min:0',
            'tva' => 'nullable|numeric|min:0|max:100',
            'type' => 'required|in:entree,plat,dessert,boisson,accompagnement,autre',
            'categorie_id' => 'nullable|exists:categories,id',
            'newImage' => 'nullable|image|max:2048', // 2MB Max
            'disponible' => 'boolean',
            'gestion_stock' => 'boolean',
        ];
    }

    public function save()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $this->validate();

        $data = [
            'nom' => $this->nom,
            'description' => $this->description,
            'prix_vente' => $this->prix_vente,
            'prix_achat' => $this->prix_achat ?: 0,
            'tva' => $this->tva ?: 0,
            'type' => $this->type,
            'gestion_stock' => $this->gestion_stock,
            'categorie_id' => $this->categorie_id ?: null,
            'disponible' => $this->disponible,
            'etablissement_id' => auth()->user()->etablissement_id,
        ];

        if ($this->newImage) {
            $data['image'] = $this->newImage->store('products', 'public_uploads');
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
            'categories' => Categorie::where('etablissement_id', auth()->user()->etablissement_id)->orderBy('nom')->get()
        ])->layout('layouts.dashboard');
    }
}
