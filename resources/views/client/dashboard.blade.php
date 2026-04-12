@extends('layouts.dashboard')
@section('title', 'Mon espace')

@section('content')

{{-- ── Header ───────────────────────────────────────────────── --}}
<div class="dash-page-header">
  <div>
    <div class="dash-greeting">Bonjour, {{ $user->prenom }} &#128075;</div>
    <div class="dash-date">{{ now()->translatedFormat('l d F Y') }}</div>
  </div>
  <a href="{{ route('villes.index') }}" class="btn-new-booking">+ Réserver un salon</a>
</div>

{{-- ── Stats ────────────────────────────────────────────────── --}}
<div class="dash-stats-row">
  <div class="dash-stat-card green">
    <div class="dash-stat-val">{{ $stats['total'] }}</div>
    <div class="dash-stat-lbl">Réservations</div>
    <div class="dash-stat-sub">Total</div>
  </div>
  <div class="dash-stat-card sage">
    <div class="dash-stat-val">{{ $stats['a_venir'] }}</div>
    <div class="dash-stat-lbl">À venir</div>
    <div class="dash-stat-sub">Confirmées &amp; en attente</div>
  </div>
  <div class="dash-stat-card cream">
    <div class="dash-stat-val">{{ $stats['terminee'] }}</div>
    <div class="dash-stat-lbl">Terminées</div>
    <div class="dash-stat-sub">Prestations effectuées</div>
  </div>
  <div class="dash-stat-card dark">
    <div class="dash-stat-val">{{ $stats['avis'] }}</div>
    <div class="dash-stat-lbl">Avis publiés</div>
    <div class="dash-stat-sub">Contribution Salonify</div>
  </div>
</div>

<div class="dash-two-col">
  <div>

    {{-- Prochain RDV --}}
    <div class="dash-section-title">
      Prochain rendez-vous
      <a href="{{ route('client.reservations.index') }}" class="dash-section-link">Voir tout</a>
    </div>

    @if($prochainRdv)
      <div style="border:2px solid var(--p3);background:var(--p1);padding:1.4rem 1.6rem;margin-bottom:1.8rem">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem">
          <div style="display:flex;align-items:center;gap:1.2rem">
            <div class="upcoming-date-block">
              <div class="upcoming-day">{{ $prochainRdv->date_heure->format('d') }}</div>
              <div class="upcoming-month">{{ $prochainRdv->date_heure->translatedFormat('M') }}</div>
            </div>
            <div>
              <div style="font-family:var(--fh);font-size:1.2rem;font-weight:700;color:var(--ink-h)">{{ $prochainRdv->salon->nom_salon }}</div>
              <div style="font-size:.82rem;color:var(--ink-m);margin-top:.18rem">{{ $prochainRdv->service->nom_service }}</div>
              <div style="font-size:.76rem;color:var(--ink-s);margin-top:.25rem">
                &#128337; {{ $prochainRdv->service->duree_formatee }}
                @if($prochainRdv->employe) · {{ $prochainRdv->employe->nomComplet() }} @endif
              </div>
            </div>
          </div>
          <div style="text-align:right">
            <div style="font-family:var(--fh);font-size:1.6rem;font-weight:700;color:var(--p4)">{{ $prochainRdv->date_heure->format('H:i') }}</div>
            @php $pillLabel = $prochainRdv->statut === 'confirmee' ? 'Confirmée' : 'En attente'; @endphp
            <span class="status-pill {{ $prochainRdv->statut === 'confirmee' ? 'confirmed' : 'pending' }}" style="margin-top:.4rem">
              <span class="status-dot"></span>{{ $pillLabel }}
            </span>
          </div>
        </div>
        <div style="display:flex;gap:.6rem;margin-top:1.2rem">
          <a href="{{ route('salons.show', [$prochainRdv->salon->ville->nom_ville, $prochainRdv->salon->slug]) }}"
             class="btn-apt-primary" style="flex:1;text-decoration:none">Voir le salon</a>
          <form method="POST" action="{{ route('reservations.annuler', $prochainRdv->id) }}"
                onsubmit="return confirm('Annuler ce rendez-vous ?')">
            @csrf
            <button type="submit" class="btn-apt-ghost">Annuler</button>
          </form>
        </div>
      </div>
    @else
      <div style="padding:2rem;text-align:center;border:2px dashed var(--border2);margin-bottom:1.8rem">
        <div style="font-size:2rem;margin-bottom:.6rem">&#128197;</div>
        <p style="color:var(--ink-m);font-size:.88rem;margin-bottom:1rem">Aucun rendez-vous à venir.</p>
        <a href="{{ route('villes.index') }}" class="btn-ol" style="font-size:.76rem">Trouver un salon</a>
      </div>
    @endif

    {{-- RDV à venir (autres) --}}
    @if($rdvAVenir->count())
      <div class="dash-section-title" style="margin-bottom:.8rem">Prochains rendez-vous</div>
      @foreach($rdvAVenir as $rdv)
        <div class="upcoming-card">
          <div class="upcoming-date-block">
            <div class="upcoming-day">{{ $rdv->date_heure->format('d') }}</div>
            <div class="upcoming-month">{{ $rdv->date_heure->translatedFormat('M') }}</div>
          </div>
          <div>
            <div class="upcoming-salon-name">{{ $rdv->salon->nom_salon }}</div>
            <div class="upcoming-service">{{ $rdv->service->nom_service }}</div>
          </div>
          <div class="upcoming-time">{{ $rdv->date_heure->format('H:i') }}</div>
        </div>
      @endforeach
    @endif

    {{-- Avis à publier --}}
    @if($rdvSansAvis->count())
      <div class="dash-section-title" style="margin-top:2rem;margin-bottom:.8rem">
        Laissez un avis &#9733;
      </div>
      @foreach($rdvSansAvis as $rdv)
        <div class="review-prompt">
          <div class="recap-salon-photo" style="width:44px;height:44px;flex-shrink:0;overflow:hidden;border:1.5px solid var(--border2)">
            <img src="{{ $rdv->salon->photo_url }}" alt="" style="width:100%;height:100%;object-fit:cover">
          </div>
          <div>
            <div class="review-prompt-salon">{{ $rdv->salon->nom_salon }}</div>
            <div class="review-prompt-service">{{ $rdv->service->nom_service }} · {{ $rdv->date_heure->translatedFormat('d M Y') }}</div>
          </div>
          <a href="{{ route('avis.create', $rdv->id) }}" style="margin-left:auto;font-size:.72rem;font-weight:700;color:var(--p4d);text-decoration:underline;white-space:nowrap">
            Évaluer &#8594;
          </a>
        </div>
      @endforeach
    @endif

  </div>

  {{-- Sidebar widgets --}}
  <div>

    {{-- Notifications récentes --}}
    @if($notifications->count())
      <div class="sidebar-widget">
        <div class="widget-title">Notifications récentes</div>
        <div class="widget-body" style="padding:0">
          @foreach($notifications as $notif)
            <div style="padding:.8rem 1.2rem;border-bottom:1px solid var(--border2);font-size:.8rem;color:var(--ink-s)">
              <div style="font-weight:700;color:var(--ink-h);margin-bottom:.2rem">{{ $notif->donnees['salon'] ?? 'Salonify' }}</div>
              <div>{{ $notif->donnees['service'] ?? '' }}</div>
              <div style="font-size:.7rem;color:var(--ink-m);margin-top:.2rem">{{ $notif->cree_le->diffForHumans() }}</div>
            </div>
          @endforeach
          <div style="padding:.7rem 1.2rem">
            <a href="{{ route('client.notifications.index') }}" style="font-size:.74rem;font-weight:700;color:var(--p4d);text-decoration:underline">Toutes les notifications</a>
          </div>
        </div>
      </div>
    @endif

    {{-- Derniers RDV --}}
    @if($derniersRdv->count())
      <div class="sidebar-widget">
        <div class="widget-title">Dernières visites</div>
        <div class="widget-body" style="padding:0">
          @foreach($derniersRdv as $rdv)
            <div class="history-row">
              <div class="history-salon-photo">
                <img src="{{ $rdv->salon->photo_url }}" alt="">
              </div>
              <div>
                <div class="history-salon-name">{{ $rdv->salon->nom_salon }}</div>
                <div class="history-service">{{ $rdv->service->nom_service }}</div>
              </div>
              <div class="history-date">{{ $rdv->date_heure->format('d/m/Y') }}</div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

  </div>
</div>

{{-- ── Salons proches de vous ─────────────────────────────── --}}
@if($salonsProches->count())
  <div style="margin-top:2.5rem;margin-bottom:3rem">
    <div class="dash-section-title" style="margin-bottom:1.2rem">
      &#128205; Salons près de vous
      @if($user->quartier)
        <span style="font-size:.72rem;font-weight:500;color:var(--ink-m);margin-left:.5rem">
          — {{ $user->quartier }}{{ $user->ville ? ', '.$user->ville->nom_ville : '' }}
        </span>
      @elseif($user->ville)
        <span style="font-size:.72rem;font-weight:500;color:var(--ink-m);margin-left:.5rem">
          — {{ $user->ville->nom_ville }}
        </span>
      @endif
      <a href="{{ route('client.profil.edit') }}" class="dash-section-link" style="font-size:.7rem">
        Modifier mon quartier
      </a>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem">
      @foreach($salonsProches as $s)
        <a href="{{ route('salons.show', [$s->ville->nom_ville, $s->slug]) }}"
           style="display:block;text-decoration:none;border:1.5px solid var(--border2);background:#fff;overflow:hidden;transition:box-shadow .2s"
           onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)'"
           onmouseout="this.style.boxShadow='none'">
          <div style="height:120px;overflow:hidden;background:var(--p1)">
            <img src="{{ $s->photo_url }}" alt="{{ $s->nom_salon }}"
                 style="width:100%;height:100%;object-fit:cover">
          </div>
          <div style="padding:.9rem 1rem">
            <div style="font-family:var(--fh);font-size:.95rem;font-weight:700;color:var(--ink-h);margin-bottom:.2rem">
              {{ $s->nom_salon }}
            </div>
            <div style="font-size:.72rem;color:var(--ink-m);margin-bottom:.5rem">
              &#128205; {{ $s->quartier ?: $s->adresse }}
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between">
              @if($s->nb_avis > 0)
                <span style="font-size:.78rem;font-weight:700;color:#D4A844">
                  &#9733; {{ number_format($s->note_moy, 1) }}
                  <span style="font-weight:400;color:var(--ink-m)">({{ $s->nb_avis }})</span>
                </span>
              @else
                <span style="font-size:.72rem;color:var(--ink-d)">Nouveau</span>
              @endif
              @if($s->estOuvertMaintenant())
                <span style="font-size:.65rem;font-weight:700;background:var(--p2);color:var(--p4dd);padding:.1rem .5rem">Ouvert</span>
              @endif
            </div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
@else
  {{-- Incitation à définir sa localisation --}}
  @if(!$user->ville_id)
    <div style="margin-top:2rem;padding:1.5rem 2rem;border:2px dashed var(--border2);text-align:center;background:var(--p1)">
      <div style="font-size:1.6rem;margin-bottom:.5rem">&#128205;</div>
      <p style="color:var(--ink-m);font-size:.88rem;margin-bottom:1rem">
        Définissez votre ville et quartier pour découvrir les salons près de chez vous.
      </p>
      <a href="{{ route('client.profil.edit') }}" class="btn-ol" style="font-size:.76rem">
        Compléter mon profil
      </a>
    </div>
  @endif
@endif

@endsection
