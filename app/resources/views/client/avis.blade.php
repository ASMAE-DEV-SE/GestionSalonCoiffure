@extends('layouts.app')
@section('title', 'Publier un avis')

@section('content')

<div class="av-ph">
  <div class="wrap">
    <h1>Publier un avis</h1>
    <p>Partagez votre expérience et aidez la communauté Salonify.</p>
  </div>
</div>

<div class="av-l">
  <div>

    {{-- Récap salon + service --}}
    <div class="salon-recap">
      <div class="sr-ph">
        <img src="{{ $reservation->salon->photo_url }}" alt="{{ $reservation->salon->nom_salon }}">
      </div>
      <div class="sr-body">
        <div class="sr-name">{{ $reservation->salon->nom_salon }}</div>
        <div class="sr-sub">
          &#128205; {{ $reservation->salon->quartier }}, {{ $reservation->salon->ville->nom_ville }}
          &nbsp;·&nbsp; {{ $reservation->date_heure->translatedFormat('d F Y') }}
        </div>
        <span class="sr-svc">{{ $reservation->service->nom_service }}</span>
      </div>
    </div>

    <div class="fcard">
      <div class="fc-ttl">Votre avis</div>
      <p class="fc-sub">Votre retour aide les autres clients à choisir leur salon.</p>

      <form method="POST" action="{{ route('avis.store') }}" id="avisForm">
        @csrf
        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">

        {{-- Note globale --}}
        <div class="stars-wrap">
          <div class="sw-lbl">Note globale *</div>
          <div class="stars-big" id="starsGlobal">
            @for($i = 1; $i <= 5; $i++)
              <span class="star-b {{ $i <= old('note', 0) ? 'on' : '' }}"
                    data-val="{{ $i }}" onclick="setNote({{ $i }})">&#9733;</span>
            @endfor
          </div>
          <div class="stars-hint" id="starsHint">
            @if(old('note')) {{ ['','Mauvais','Médiocre','Moyen','Bien','Excellent'][old('note')] }} @endif
          </div>
          <input type="hidden" name="note" id="noteInput" value="{{ old('note', 0) }}" required>
          @error('note')<div style="color:#C04A3D;font-size:.76rem;margin-top:.4rem">{{ $message }}</div>@enderror
        </div>

        {{-- Notes par critère (optionnelles, informatif) --}}
        <div class="crit-grid">
          @foreach(['Accueil' => 'accueil', 'Propreté' => 'proprete', 'Rapport qualité/prix' => 'qualite', 'Résultat' => 'resultat'] as $lbl => $key)
            <div class="crit-item">
              <div class="crit-nm">{{ $lbl }}</div>
              <div class="stars-sm" data-crit="{{ $key }}">
                @for($i = 1; $i <= 5; $i++)
                  <span class="star-s" data-val="{{ $i }}"
                        onclick="setCrit('{{ $key }}', {{ $i }})">&#9733;</span>
                @endfor
              </div>
            </div>
          @endforeach
        </div>

        {{-- Titre (optionnel) --}}
        <div class="fg">
          <label>Titre de l'avis <span style="font-weight:400;font-size:.78rem;color:var(--ink-m)">(optionnel)</span></label>
          <div class="fg-title-wrap">
            <textarea name="titre" class="fi" rows="1" maxlength="100"
                      placeholder="Résumez en une phrase..."
                      oninput="document.getElementById('ctrTitre').textContent = this.value.length + '/100'">{{ old('titre') }}</textarea>
            <span class="char-ctr" id="ctrTitre">0/100</span>
          </div>
        </div>

        {{-- Commentaire --}}
        <div class="fg">
          <label>Commentaire</label>
          <textarea name="commentaire" class="fi" rows="5"
                    placeholder="Décrivez votre expérience : ambiance, qualité de la prestation, accueil..."
                    oninput="document.getElementById('ctrCom').textContent = this.value.length + '/1000'"
                    maxlength="1000">{{ old('commentaire') }}</textarea>
          <div style="display:flex;justify-content:flex-end;margin-top:.3rem">
            <span class="char-ctr" id="ctrCom" style="position:static">0/1000</span>
          </div>
          @error('commentaire')<div style="color:#C04A3D;font-size:.76rem;margin-top:.3rem">{{ $message }}</div>@enderror
        </div>

        {{-- Recommandation --}}
        <div class="reco-row">
          <div class="reco-opt yes" id="recoOui" onclick="setReco('oui')">
            <div class="reco-ic">&#128077;</div>
            <div class="reco-t">Je recommande</div>
            <div class="reco-s">Ce salon est excellent</div>
          </div>
          <div class="reco-opt no" id="recoNon" onclick="setReco('non')">
            <div class="reco-ic">&#128078;</div>
            <div class="reco-t">Je ne recommande pas</div>
            <div class="reco-s">Des améliorations nécessaires</div>
          </div>
        </div>
        <input type="hidden" name="recommande" id="recommandeInput" value="{{ old('recommande', 'oui') }}">

        <div class="av-nav">
          <a href="{{ route('client.reservations.index') }}" class="btn-back">&#8592; Mes réservations</a>
          <button type="submit" class="btn-pub" id="btnPublier" style="opacity:.5;pointer-events:none">
            Publier mon avis &#9733;
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- Sidebar --}}
  <div class="sb">
    <div class="sb-card">
      <div class="sb-t">Votre note</div>
      <div class="score-live">
        <div class="score-n" id="scoreLive">—</div>
        <div class="score-stars" id="scoreStars">&#9734;&#9734;&#9734;&#9734;&#9734;</div>
        <div class="score-lbl" id="scoreLbl">Choisissez une note</div>
      </div>
    </div>

    <div class="sb-card">
      <div class="sb-t">Conseils</div>
      <div class="tip"><div class="tip-ic">&#10003;</div><div class="tip-t">Soyez précis sur la prestation reçue.</div></div>
      <div class="tip"><div class="tip-ic">&#10003;</div><div class="tip-t">Mentionnez le nom du coiffeur si vous le souhaitez.</div></div>
      <div class="tip"><div class="tip-ic">&#10003;</div><div class="tip-t">Évitez les informations personnelles.</div></div>
    </div>
  </div>
</div>

@push('scripts')
<script>
const labels = ['','Mauvais','Médiocre','Moyen','Bien','Excellent'];

function setNote(n) {
  document.getElementById('noteInput').value = n;
  document.querySelectorAll('#starsGlobal .star-b').forEach((s,i) => {
    s.classList.toggle('on', i < n);
  });
  document.getElementById('starsHint').textContent  = labels[n];
  document.getElementById('scoreLive').textContent  = n + '.0';
  document.getElementById('scoreStars').innerHTML   = '★'.repeat(n) + '☆'.repeat(5-n);
  document.getElementById('scoreLbl').textContent   = labels[n];
  document.getElementById('btnPublier').style.opacity        = '1';
  document.getElementById('btnPublier').style.pointerEvents  = 'auto';
}

function setCrit(key, n) {
  document.querySelectorAll('[data-crit="' + key + '"] .star-s').forEach((s,i) => {
    s.classList.toggle('on', i < n);
  });
}

function setReco(val) {
  document.getElementById('recommandeInput').value = val;
  document.getElementById('recoOui').classList.toggle('sel', val === 'oui');
  document.getElementById('recoNon').classList.toggle('sel', val === 'non');
}

// Restaurer si erreur validation
const oldNote = {{ old('note', 0) }};
if (oldNote) setNote(oldNote);
setReco('{{ old('recommande', 'oui') }}');
</script>
@endpush
@endsection
