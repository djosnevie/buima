<?php

namespace App\Livewire\Admin\Sections;

use App\Models\Section;
use App\Models\Etablissement;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SectionManager extends Component
{
    public $sections;
    public $etablissements; // For Super Admin

    public $nom;
    public $description;
    public $actif = true;
    public $etablissement_id = ''; // For Super Admin

    public $selectedSectionId;
    public $isEditing = false;

    protected $rules = [
        'nom' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'actif' => 'boolean',
        'etablissement_id' => 'nullable|exists:etablissements,id',
    ];

    public function mount()
    {
        $this->loadSections();
    }

    public function loadSections()
    {
        if (Auth::user()->isSuperAdmin()) {
            $this->sections = Section::with('etablissement')->latest()->get();
            $this->etablissements = Etablissement::all();
        } else {
            $this->sections = Auth::user()->etablissement->sections()->latest()->get();
            $this->etablissements = collect();
        }
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
    }

    public function edit($id)
    {
        $section = Section::find($id);

        $hasAccess = Auth::user()->isSuperAdmin() ||
            ($section && $section->etablissement_id === Auth::user()->etablissement_id);

        if ($section && $hasAccess) {
            $this->selectedSectionId = $id;
            $this->nom = $section->nom;
            $this->description = $section->description;
            $this->actif = $section->actif;
            $this->etablissement_id = $section->etablissement_id;
            $this->isEditing = true;
        }
    }

    public function save()
    {
        $this->validate();

        // Determine Etablissement ID
        $targetEtablissementId = Auth::user()->isSuperAdmin()
            ? ($this->etablissement_id ?: null)
            : Auth::user()->etablissement_id;

        if (!$targetEtablissementId && Auth::user()->isSuperAdmin()) {
            // Optional: Enforce establishment for sections? Usually a section belongs to a restaurant.
            // We'll require it for now via validation if strict, but let's just use what we have.
        }

        if ($this->isEditing) {
            $section = Section::find($this->selectedSectionId);
            $hasAccess = Auth::user()->isSuperAdmin() ||
                ($section && $section->etablissement_id === Auth::user()->etablissement_id);

            if ($section && $hasAccess) {
                $section->update([
                    'nom' => $this->nom,
                    'description' => $this->description,
                    'actif' => $this->actif,
                    'etablissement_id' => $targetEtablissementId,
                ]);
                session()->flash('message', 'Section mise à jour avec succès.');
            }
        } else {
            // If Super Admin and no establishment selected, we might have an issue.
            // But let's assume valid ID or fail gracefully.
            if ($targetEtablissementId) {
                Section::create([
                    'nom' => $this->nom,
                    'description' => $this->description,
                    'actif' => $this->actif,
                    'etablissement_id' => $targetEtablissementId,
                ]);
                session()->flash('message', 'Section créée avec succès.');
            } else {
                session()->flash('error', 'Impossible de créer une section sans établissement.');
                return;
            }
        }

        $this->resetForm();
        $this->loadSections();
    }

    public function delete($id)
    {
        $section = Section::find($id);
        $hasAccess = Auth::user()->isSuperAdmin() ||
            ($section && $section->etablissement_id === Auth::user()->etablissement_id);

        if ($section && $hasAccess) {
            $section->delete();
            session()->flash('message', 'Section supprimée.');
            $this->loadSections();
        }
    }

    public function resetForm()
    {
        $this->nom = '';
        $this->description = '';
        $this->actif = true;
        $this->etablissement_id = '';
        $this->selectedSectionId = null;
        $this->isEditing = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.sections.section-manager')->layout('layouts.dashboard');
    }
}
