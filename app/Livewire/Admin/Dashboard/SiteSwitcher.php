<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\Etablissement;

class SiteSwitcher extends Component
{
    public $currentSiteId = null;

    public function mount()
    {
        $this->currentSiteId = session('manager_view_site_id');
    }

    public function switchSite($siteId)
    {
        if ($siteId === 'global') {
            session()->forget('manager_view_site_id');
            $this->currentSiteId = null;
        } else {
            // Verify access
            $user = auth()->user();
            if (in_array($siteId, $user->getAccessibleEtablissementIds())) {
                session(['manager_view_site_id' => $siteId]);
                $this->currentSiteId = $siteId;
            }
        }

        $this->dispatch('dashboard-context-changed');
        return redirect()->route('dashboard');
    }

    public function render()
    {
        $user = auth()->user();
        $sites = Etablissement::whereIn('id', $user->getAccessibleEtablissementIds())
            ->select('id', 'nom')
            ->get();

        return view('livewire.admin.dashboard.site-switcher', [
            'sites' => $sites
        ]);
    }
}
