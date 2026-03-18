<?php

namespace App\Exports;

use App\Models\Produit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $user = auth()->user();
        $query = Produit::query()->with(['categorie', 'stock']);

        if ($user->isManager()) {
            $contextSiteId = session('manager_view_site_id');
            if ($contextSiteId) {
                $query->where('etablissement_id', $contextSiteId);
            } else {
                $query->whereIn('etablissement_id', $user->getAccessibleEtablissementIds());
            }
        } else {
            $query->where('etablissement_id', $user->etablissement_id);
        }

        return $query->latest()->get();
    }

    public function map($produit): array
    {
        return [
            $produit->nom,
            $produit->seuil_alerte,
            intval($produit->prix_vente),
            $produit->categorie ? $produit->categorie->nom : 'Sans catégorie',
        ];
    }

    public function headings(): array
    {
        return [
            'Nom produit',
            'Q minimum',
            'Prix',
            'Catégorie',
        ];
    }
}
