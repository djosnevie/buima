<?php

namespace App\Livewire\Setup;

use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class RestaurantCreate extends Component
{
    use WithFileUploads;

    // Restaurant details
    public $nom_restaurant;
    public $type_restaurant = 'mixte'; // avec_tables, sans_tables, mixte
    public $adresse;
    public $telephone;
    public $email_restaurant;
    public $devise = 'XAF';
    public $theme_color = '#ff6b35'; // Brand Default Orange
    public $secondary_color = '#ffffff'; // Default secondary
    public $button_color = '#ff6b35'; // Default button
    public $logo;

    // Admin user details (Only for guests)
    public $nom_admin;
    public $email_admin;
    public $password;
    public $password_confirmation;

    protected function rules()
    {
        $rules = [
            'nom_restaurant' => 'required|string|max:255',
            'type_restaurant' => 'required|in:avec_tables,sans_tables,mixte',
            'adresse' => 'nullable|string|max:500',
            'telephone' => 'nullable|string|max:20',
            'email_restaurant' => 'nullable|email|max:255',
            'devise' => 'required|string|max:5',
            'theme_color' => 'required|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'button_color' => 'nullable|string|max:7',
            'logo' => 'nullable|image|max:1024',
        ];

        if (!Auth::check() || (Auth::check() && Auth::user()->isSuperAdmin())) {
            $rules = array_merge($rules, [
                'nom_admin' => 'required|string|max:255',
                'email_admin' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);
        }

        return $rules;
    }

    public function mount()
    {
        // Redirect if user has establishment and is NOT Super Admin
        if (Auth::check() && Auth::user()->etablissement_id && !Auth::user()->isSuperAdmin()) {
            return redirect()->route('dashboard');
        }
    }

    public function createRestaurant()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $logoPath = null;
            if ($this->logo) {
                $logoPath = $this->logo->store('logos', 'public_uploads');
            }

            $etablissement = Etablissement::create([
                'nom' => $this->nom_restaurant,
                'type' => $this->type_restaurant,
                'adresse' => $this->adresse,
                'telephone' => $this->telephone,
                'email' => $this->email_restaurant,
                'devise' => $this->devise,
                'theme_color' => $this->theme_color,
                'secondary_color' => $this->secondary_color,
                'button_color' => $this->button_color,
                'logo' => $logoPath,
                'configuration' => json_encode([]),
                'actif' => true,
            ]);

            if (Auth::check() && !Auth::user()->isSuperAdmin()) {
                // If logged in and NOT Super Admin (e.g. converting a user to an admin of a new place?)
                // Or maybe this case shouldn't happen often if we enforce 1 app 1 resto normally.
                // But let's keep existing logic for non-super admins.
                $user = Auth::user();
                $user->etablissement_id = $etablissement->id;
                $user->role = 'admin';
                $user->save();
            } else {
                // Guest OR Super Admin creating a NEW user for this restaurant
                $user = User::create([
                    'name' => $this->nom_admin,
                    'email' => $this->email_admin,
                    'password' => Hash::make($this->password),
                    'role' => 'admin',
                    'etablissement_id' => $etablissement->id,
                ]);

                if (!Auth::check()) {
                    Auth::login($user);
                }
            }

            DB::commit();

            session()->flash('message', 'Restaurant créé avec succès !');
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.setup.restaurant-create')->layout('layouts.guest');
    }
}
