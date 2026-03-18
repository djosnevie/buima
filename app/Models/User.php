<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'etablissement_id',
        'section_id',
        'caisse_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function etablissement(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function section(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function caisse(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Caisse::class);
    }

    // Role Helpers
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager' || $this->isSuperAdmin();
    }

    public function isAdmin(): bool
    {
        // Admin is the branch manager. Manager is the owner.
        return $this->role === 'admin' || $this->isManager();
    }

    public function isCaissier(): bool
    {
        // 'caissier' replaces the old 'user'/'employe' role for POS staff
        return $this->role === 'caissier' || $this->role === 'user';
    }

    public function isUser(): bool
    {
        return $this->isCaissier();
    }

    /**
     * Whether the user has a currently open cash session.
     */
    public function hasOpenSession(): bool
    {
        return $this->activeSession() !== null;
    }

    /**
     * Whether the user can manage (create/edit/delete) other admins.
     * Only super_admin can manage admins.
     */
    public function canManageAdmins(): bool
    {
        return $this->isSuperAdmin();
    }

    public function ownedEstablishments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Etablissement::class, 'manager_id');
    }

    public function hasAccessToSection($sectionId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        return $this->section_id == $sectionId;
    }

    /**
     * Get the current active caisse session for the user
     */
    public function activeSession(): ?SessionCaisse
    {
        return SessionCaisse::where('user_id', $this->id)
            ->where('statut', 'ouverte')
            ->whereHas('caisse', function ($q) {
                $q->where('etablissement_id', $this->etablissement_id);
            })
            ->first();
    }

    /**
     * Get IDs of all establishments the user has access to
     */
    public function getAccessibleEtablissementIds(): array
    {
        if ($this->isSuperAdmin()) {
            return Etablissement::pluck('id')->toArray();
        }

        if ($this->isManager()) {
            return $this->ownedEstablishments()->pluck('id')->toArray();
        }

        return [$this->etablissement_id];
    }

    /**
     * Get the primary (mother) establishment for this user's management
     */
    public function parentEstablishment(): ?Etablissement
    {
        if ($this->isSuperAdmin())
            return null;

        // If the user has a manager_id (owner), their parent is the oldest establishment of that manager
        $managerId = $this->role === 'manager' ? $this->id : $this->etablissement?->manager_id;

        if (!$managerId)
            return $this->etablissement;

        return Etablissement::where('manager_id', $managerId)
            ->oldest()
            ->first();
    }
}
