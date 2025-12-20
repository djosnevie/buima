<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class BackupSettings extends Component
{
    public $backups = [];

    public function mount()
    {
        $this->loadBackups();
    }

    public function loadBackups()
    {
        // For simulation/implementation, we check a 'backups' folder
        if (!Storage::disk('local')->exists('backups')) {
            Storage::disk('local')->makeDirectory('backups');
        }

        $files = Storage::disk('local')->files('backups');
        $this->backups = array_map(function ($file) {
            return [
                'name' => basename($file),
                'size' => round(Storage::disk('local')->size($file) / 1024, 2) . ' KB',
                'date' => date('d/m/Y H:i', Storage::disk('local')->lastModified($file)),
            ];
        }, $files);

        usort($this->backups, function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });
    }

    public function createBackup()
    {
        $filename = 'backup_' . date('Y-m-d_His') . '.sql';

        // This is a simplified simulation of a DB dump
        // In reality, we'd use mysqldump or a package
        $content = "-- O'Menu Backup --\n-- Date: " . date('Y-m-d H:i:s') . "\n-- Simulated Content --";

        Storage::disk('local')->put('backups/' . $filename, $content);

        $this->loadBackups();
        session()->flash('success', 'Nouvlle sauvegarde créée avec succès.');
    }

    public function deleteBackup($filename)
    {
        Storage::disk('local')->delete('backups/' . $filename);
        $this->loadBackups();
        session()->flash('success', 'Sauvegarde supprimée.');
    }

    public function render()
    {
        return view('livewire.admin.settings.backup-settings')->layout('layouts.dashboard');
    }
}
