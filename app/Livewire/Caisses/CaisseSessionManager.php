<?php

namespace App\Livewire\Caisses;

use App\Models\Caisse;
use App\Models\SessionCaisse;
use Livewire\Component;

class CaisseSessionManager extends Component
{
    public $caisseId;
    public $caisse;
    public $montant_ouverture;
    public $montant_fermeture_reel;
    public $notes;

    public function mount($id)
    {
        $this->caisseId = $id;
        $this->caisse = Caisse::findOrFail($id);
    }

    public function ouvrirSession()
    {
        $this->validate([
            'montant_ouverture' => 'required|numeric|min:0',
        ]);

        if ($this->caisse->currentSession()) {
            session()->flash('error', 'Une session est déjà ouverte pour cette caisse.');
            return;
        }

        SessionCaisse::create([
            'caisse_id' => $this->caisseId,
            'user_id' => auth()->id(),
            'date_ouverture' => now(),
            'montant_ouverture' => $this->montant_ouverture,
            'statut' => 'ouverte',
        ]);

        session()->flash('success', 'Session de caisse ouverte.');
        $this->reset(['montant_ouverture']);
    }

    public function fermerSession()
    {
        $session = $this->caisse->currentSession();
        if (!$session)
            return;

        $this->validate([
            'montant_fermeture_reel' => 'required|numeric|min:0',
        ]);

        // Calculer montant théorique
        $totalVentes = $session->transactions()->where('type', 'vente')->sum('montant');
        $totalEntrees = $session->transactions()->where('type', 'entree_caisse')->sum('montant');
        $totalSorties = $session->transactions()->where('type', 'sortie_caisse')->sum('montant');

        $theorique = $session->montant_ouverture + $totalVentes + $totalEntrees - $totalSorties;

        $session->update([
            'date_fermeture' => now(),
            'montant_fermeture_reel' => $this->montant_fermeture_reel,
            'montant_fermeture_theorique' => $theorique,
            'ecart' => $this->montant_fermeture_reel - $theorique,
            'notes' => $this->notes,
            'statut' => 'fermee',
        ]);

        session()->flash('success', 'Session de caisse fermée avec succès.');
        $this->reset(['montant_fermeture_reel', 'notes']);
    }

    public function render()
    {
        $currentSession = $this->caisse->currentSession();
        $history = SessionCaisse::where('caisse_id', $this->caisseId)->latest()->paginate(10);

        return view('livewire.pages.caisses.caisse-session-manager', [
            'currentSession' => $currentSession,
            'history' => $history
        ])->layout('layouts.dashboard');
    }
}
