<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ville extends Model
{
    use HasFactory;

    protected $table  = 'villes';
    public $timestamps = false;

    protected $fillable = ['nom_ville','code_postal','region','pays','actif'];

    protected $casts = [
        'actif'   => 'boolean',
        'cree_le' => 'datetime',
    ];

    // ── Scopes ─────────────────────────────────────────────────────
    public function scopeActives($q)    { return $q->where('actif', true); }
    public function scopeAvecSalons($q) {
        return $q->whereHas('salons', fn($s) => $s->where('valide', 1));
    }

    // ── Relations ──────────────────────────────────────────────────
    public function salons(): HasMany {
        return $this->hasMany(Salon::class, 'ville_id');
    }
    public function salonsValides(): HasMany {
        return $this->hasMany(Salon::class, 'ville_id')->where('valide', 1);
    }
}
