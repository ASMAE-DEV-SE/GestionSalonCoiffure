<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    public $timestamps = false;

    const CREATED_AT = 'cree_le';

    protected $fillable = [
        'client_id',
        'salon_id',
        'service_id',
        'employe_id',
        'date_heure',
        'duree_minutes',
        'notes_client',
        'statut',
        'annulee_par',
        'date_annul',
        'motif_annul',
        'notes_salon',
        'rappel_24h',
        'rappel_2h',
    ];

    protected $casts = [
        'date_heure'    => 'datetime',
        'date_annul'    => 'datetime',
        'cree_le'       => 'datetime',
        'duree_minutes' => 'integer',
        'rappel_24h'    => 'boolean',
        'rappel_2h'     => 'boolean',
    ];

    /*
    |------------------------------------------------------------------
    | HELPERS
    |------------------------------------------------------------------
    */

    /** Annulable uniquement si le RDV est dans plus de 24h */
    public function peutEtreAnnulee(): bool
    {
        return in_array($this->statut, ['en_attente', 'confirmee'])
            && $this->date_heure->diffInHours(now(), false) < -24;
    }

    public function estConfirmee(): bool  { return $this->statut === 'confirmee'; }
    public function estAnnulee(): bool    { return $this->statut === 'annulee'; }
    public function estEnAttente(): bool  { return $this->statut === 'en_attente'; }

    /*
    |------------------------------------------------------------------
    | SCOPES
    |------------------------------------------------------------------
    */

    public function scopeAVenir($query)
    {
        return $query->whereIn('statut', ['en_attente', 'confirmee'])
                     ->where('date_heure', '>=', now());
    }

    public function scopePassees($query)
    {
        return $query->where('date_heure', '<', now());
    }

    /*
    |------------------------------------------------------------------
    | RELATIONS
    |------------------------------------------------------------------
    */

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class, 'salon_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    public function avis(): HasOne
    {
        return $this->hasOne(Avis::class, 'reservation_id');
    }
}