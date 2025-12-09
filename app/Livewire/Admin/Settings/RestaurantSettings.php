<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Etablissement;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class RestaurantSettings extends Component
{
    use WithFileUploads;

    public Etablissement $etablissement;

    public $nom;
    public $adresse;
    public $telephone;
    public $email;
    public $devise;
    public $theme_color;
    public $secondary_color;
    public $button_color;
    public $logo;
    public $currentLogo;
    public $type;

    protected $rules = [
        'nom' => 'required|string|max:255',
        'adresse' => 'nullable|string|max:500',
        'telephone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'devise' => 'required|string|max:5',
        'theme_color' => 'required|string|max:7',
        'secondary_color' => 'nullable|string|max:7',
        'button_color' => 'nullable|string|max:7',
        'logo' => 'nullable|image|max:1024',
        'type' => 'required|in:avec_tables,sans_tables,mixte',
    ];

    public function mount()
    {
        $user = Auth::user();

        if (!$user || !$user->etablissement) {
            return redirect()->route('setup.restaurant'); // Redirect if no establishment
        }

        $this->etablissement = $user->etablissement;

        $this->nom = $this->etablissement->nom;
        $this->adresse = $this->etablissement->adresse;
        $this->telephone = $this->etablissement->telephone;
        $this->email = $this->etablissement->email;
        $this->devise = $this->etablissement->devise;
        $this->theme_color = $this->etablissement->theme_color ?? '#ff6b35';
        $this->secondary_color = $this->etablissement->secondary_color ?? '#ffffff';
        $this->button_color = $this->etablissement->button_color ?? '#ff6b35';
        $this->currentLogo = $this->etablissement->logo;
        $this->type = $this->etablissement->type;
    }

    public function updateSettings()
    {
        $this->validate();

        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
            $this->etablissement->logo = $logoPath;
        }

        $this->etablissement->update([
            'nom' => $this->nom,
            'adresse' => $this->adresse,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'devise' => $this->devise,
            'theme_color' => $this->theme_color,
            'secondary_color' => $this->secondary_color,
            'button_color' => $this->button_color,
            'type' => $this->type,
        ]);

        session()->flash('message', 'Paramètres mis à jour avec succès.');

        $this->currentLogo = $this->etablissement->logo;
        $this->logo = null;

        return redirect()->route('settings.restaurant')->with('message', 'Paramètres mis à jour avec succès.');
    }

    public function render()
    {
        return view('livewire.admin.settings.restaurant-settings')->layout('layouts.dashboard');
    }
}
