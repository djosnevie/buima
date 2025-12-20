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

    public $usersInSection = [];
    public $isUsersModalOpen = false;
    public $viewingSectionName = '';

    protected $rules = [
        'nom' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'actif' => 'boolean',
        'etablissement_id' => 'nullable|exists:etablissements,id',
    ];

    public function showUsers($id)
    {
        $section = Section::with('users')->find($id);

        $hasAccess = Auth::user()->isSuperAdmin() ||
            ($section && $section->etablissement_id === Auth::user()->etablissement_id);

        if ($section && $hasAccess) {
            $this->usersInSection = $section->users;
            $this->viewingSectionName = $section->nom;
            $this->isUsersModalOpen = true;
        }
    }

    public function closeUsersModal()
    {
        $this->isUsersModalOpen = false;
        $this->usersInSection = [];
        $this->viewingSectionName = '';
    }

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
            // Manager Context
            if (Auth::user()->isManager() && session('manager_view_site_id')) {
                $targetId = session('manager_view_site_id');
                $this->sections = Section::where('etablissement_id', $targetId)->latest()->get();
            } else {
                // Default to auth user's establishment (or maybe aggregated? Sections are usually specific. Let's stick to auth user default)
                // Actually sections are usually per restaurant. If vue global, we might see all?
                // For now, let's target the primary establishment if no context, or stick to existing logic.
                $this->sections = Auth::user()->etablissement->sections()->latest()->get();
            }
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
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

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
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

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
