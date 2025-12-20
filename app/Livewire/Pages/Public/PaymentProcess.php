<?php

namespace App\Livewire\Pages\Public;

use App\Models\Commande;
use App\Models\Transaction;
use App\Models\Etablissement;
use Livewire\Component;

class PaymentProcess extends Component
{
    public $commandeId;
    public $commande;
    public $etablissement;
    public $method = 'mobile_money'; // 'mobile_money', 'card', 'cash'
    public $phoneNumber;
    public $isProcessing = false;
    public $paymentStatus = null; // 'pending', 'success', 'failed'

    public function mount($commandeId)
    {
        $this->commandeId = $commandeId;
        $this->commande = Commande::findOrFail($commandeId);
        $this->etablissement = $this->commande->etablissement;
    }

    public function processPayment()
    {
        $this->isProcessing = true;

        // Simulate a delay for payment processing
        // In a real scenario, this would call an API like CinetPay, Cinolu, Stripe etc.
        sleep(2);

        $transaction = Transaction::create([
            'etablissement_id' => $this->etablissement->id,
            'user_id' => $this->commande->user_id,
            'type_transaction' => 'vente',
            'montant' => $this->commande->total,
            'mode_paiement' => $this->method,
            'description' => 'Paiement commande ' . $this->commande->numero_commande . ($this->phoneNumber ? ' via ' . $this->phoneNumber : ''),
            'statut' => 'complete', // Assuming success for simulation
            'reference_id' => $this->commande->id,
            'reference_type' => Commande::class,
        ]);

        $this->commande->update(['statut' => 'payee']);

        $this->isProcessing = false;
        $this->paymentStatus = 'success';

        session()->flash('success', 'Paiement effectué avec succès !');
    }

    public function render()
    {
        return view('livewire.pages.public.payment-process')->layout('layouts.app');
    }
}
