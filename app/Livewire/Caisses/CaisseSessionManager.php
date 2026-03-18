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

        // Rule: only ONE open session per user at a time (professional POS standard)
        $userOpenSession = SessionCaisse::where('user_id', auth()->id())
            ->where('statut', 'ouverte')
            ->first();

        if ($userOpenSession) {
            session()->flash('error', 'Vous avez déjà une session de caisse ouverte. Fermez-la avant d\'en ouvrir une nouvelle.');
            return;
        }

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
        if (!$session) return;

        // Only the session owner or an admin can close it
        if ($session->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            session()->flash('error', 'Vous n\'avez pas la permission de fermer cette session.');
            return;
        }

        $this->validate([
            'montant_fermeture_reel' => 'required|numeric|min:0',
        ]);

        // Calculate all figures
        $totalVentes = $session->transactions()->where('type', 'vente')->sum('montant');
        $totalEntrees = $session->transactions()->where('type', 'entree_caisse')->sum('montant');
        $totalSorties = $session->transactions()->where('type', 'sortie_caisse')->sum('montant');
        $theorique = $session->montant_ouverture + $totalVentes + $totalEntrees - $totalSorties;
        $ecart = $this->montant_fermeture_reel - $theorique;

        $session->update([
            'date_fermeture' => now(),
            'montant_fermeture_reel' => $this->montant_fermeture_reel,
            'montant_fermeture_theorique' => $theorique,
            'ecart' => $ecart,
            'notes' => $this->notes,
            'statut' => 'fermee',
        ]);

        session()->flash('success', 'Session de caisse fermée. Écart : ' . number_format(abs($ecart), 0, ',', ' ') . ' ' . (auth()->user()->etablissement->devise_display ?? 'FCFA') . ($ecart < 0 ? ' de moins' : ($ecart > 0 ? ' de plus' : ' (parfait)')) . '.');
        $this->reset(['montant_fermeture_reel', 'notes']);
    }

    public function supprimerSession($sessionId)
    {
        if (!auth()->user()->isAdmin()) {
            session()->flash('error', 'Seul un administrateur peut supprimer une session.');
            return;
        }

        $session = SessionCaisse::find($sessionId);
        if (!$session) return;

        // Closed sessions cannot be deleted
        if ($session->statut === 'fermee') {
            session()->flash('error', 'Impossible de supprimer une session fermée.');
            return;
        }

        $session->delete();
        session()->flash('success', 'Session supprimée.');
    }

    public function render()
    {
        $currentSession = $this->caisse->currentSession();
        $history = SessionCaisse::where('caisse_id', $this->caisseId)->latest()->paginate(10);

        // Live stats for the active session
        $stats = [];
        if ($currentSession) {
            // Total des ventes : on compte les commandes liées à cette session
            // (couvre le POS et les commandes de salle)
            $totalVentes = \App\Models\Commande::where('session_caisse_id', $currentSession->id)
                ->where('statut', 'payee')
                ->sum('total');

            // Détail par mode de paiement via les transactions (POS uniquement)
            $venteQuery = $currentSession->transactions()->where('type', 'vente');

            $stats = [
                'total_ventes'   => $totalVentes,
                'par_mode'       => [
                    'especes'      => (clone $venteQuery)->where('mode_paiement', 'especes')->sum('montant'),
                    'mobile_money' => (clone $venteQuery)->where('mode_paiement', 'mobile_money')->sum('montant'),
                    'carte'        => (clone $venteQuery)->where('mode_paiement', 'carte')->sum('montant'),
                ],
            ];
            $stats['montant_attendu'] = $stats['total_ventes'];
        }

        return view('livewire.pages.caisses.caisse-session-manager', [
            'currentSession' => $currentSession,
            'history'        => $history,
            'stats'          => $stats,
        ])->layout('layouts.dashboard');
    }
}
