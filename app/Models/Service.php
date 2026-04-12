<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'salon_id','nom_service','description',
        'prix','duree_minu','categorie','actif',
    ];

    protected $casts = [
        'prix'       => 'decimal:2',
        'actif'      => 'boolean',
        'cree_le'    => 'datetime',
        'modifie_le' => 'datetime',
    ];

    // Désactiver les timestamps Laravel (on a cree_le / modifie_le custom)
    const CREATED_AT = 'cree_le';
    const UPDATED_AT = 'modifie_le';

    // ── Helpers ────────────────────────────────────────────────────
    /** Durée formatée : 90 → "1h30" */
    public function getDureeFormateeAttribute(): string
    {
        $h = intdiv($this->duree_minu, 60);
        $m = $this->duree_minu % 60;
        if ($h > 0 && $m > 0) return "{$h}h{$m}";
        if ($h > 0)            return "{$h}h";
        return "{$m} min";
    }

    /** Prix formaté : "160 MAD" */
    public function getPrixFormatAttribute(): string
    {
        return number_format($this->prix, 0, ',', ' ') . ' MAD';
    }

    // ── Scopes ─────────────────────────────────────────────────────
    public function scopeActifs($q) { return $q->where('actif', true); }
    public function scopeParCategorie($q, string $cat) {
        return $q->where('categorie', $cat);
    }

    // ── Relations ──────────────────────────────────────────────────
    public function salon(): BelongsTo {
        return $this->belongsTo(Salon::class, 'salon_id');
    }
    public function reservations(): HasMany {
        return $this->hasMany(Reservation::class, 'service_id');
    }
}
