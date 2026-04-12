@extends('layouts.app')
@section('title', 'Contact')

@section('content')
<div class="contact-page">

  <div class="contact-bg">
    <img src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=1400&q=60" alt="">
    <div class="contact-bg-overlay"></div>
  </div>

  <div class="contact-card">

    {{-- Panneau gauche : formulaire --}}
    <div class="contact-left">
      <div class="contact-title">CONTACT</div>
      <p class="contact-subtitle">Une question, un partenariat ou un problème ? Notre équipe vous répond sous 24h.</p>

      @if(session('success'))
        <div style="background:rgba(197,216,157,.3);border:1.5px solid var(--p3);padding:1rem 1.2rem;border-radius:4px;margin-bottom:1.5rem;font-size:.84rem;color:var(--p4dd);font-weight:600">
          &#10003; {{ session('success') }}
        </div>
      @endif

      <form method="POST" action="{{ route('contact.send') }}">
        @csrf

        <div class="contact-form-group">
          <label class="contact-form-label">Nom complet</label>
          <input type="text" name="nom" class="contact-form-input"
                 placeholder="Votre nom" value="{{ old('nom', auth()->user()?->nomComplet()) }}" required>
          @error('nom')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
        </div>

        <div class="contact-form-group">
          <label class="contact-form-label">Adresse email</label>
          <input type="email" name="email" class="contact-form-input"
                 placeholder="votre@email.com" value="{{ old('email', auth()->user()?->email) }}" required>
          @error('email')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
        </div>

        <div class="contact-form-group">
          <label class="contact-form-label">Sujet</label>
          <select name="sujet" class="contact-form-select" required>
            <option value="">— Sélectionner un sujet —</option>
            <option value="Réservation" {{ old('sujet') === 'Réservation' ? 'selected' : '' }}>Question sur une réservation</option>
            <option value="Salon" {{ old('sujet') === 'Salon' ? 'selected' : '' }}>Inscrire mon salon</option>
            <option value="Technique" {{ old('sujet') === 'Technique' ? 'selected' : '' }}>Problème technique</option>
            <option value="Partenariat" {{ old('sujet') === 'Partenariat' ? 'selected' : '' }}>Partenariat</option>
            <option value="Autre" {{ old('sujet') === 'Autre' ? 'selected' : '' }}>Autre</option>
          </select>
          @error('sujet')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
        </div>

        <div class="contact-form-group">
          <label class="contact-form-label">Message</label>
          <textarea name="message" class="contact-form-input" rows="4"
                    placeholder="Décrivez votre demande..." required>{{ old('message') }}</textarea>
          @error('message')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn-contact-submit">Envoyer le message</button>
      </form>
    </div>

    {{-- Panneau droit : décoratif --}}
    <div class="contact-right">
      <div class="contact-right-zellige"></div>

      <div class="contact-arch-svg">
        <svg viewBox="0 0 200 280" xmlns="http://www.w3.org/2000/svg">
          <path d="M20 280 L20 100 Q20 20 100 20 Q180 20 180 100 L180 280 Z"
                fill="rgba(78,92,56,.15)" stroke="rgba(78,92,56,.4)" stroke-width="2"/>
          <path d="M40 280 L40 105 Q40 45 100 45 Q160 45 160 105 L160 280 Z"
                fill="rgba(197,216,157,.2)" stroke="rgba(78,92,56,.3)" stroke-width="1.5"/>
          <circle cx="100" cy="90" r="18" fill="rgba(137,152,109,.3)" stroke="rgba(78,92,56,.4)" stroke-width="1.5"/>
          <text x="100" y="96" text-anchor="middle" font-family="Cormorant Garamond,serif"
                font-size="18" font-weight="700" fill="rgba(78,92,56,.7)">S</text>
        </svg>
      </div>

      <div style="width:100%;position:relative;z-index:1">
        <div class="contact-info-row">
          <div class="contact-info-ic">&#128205;</div>
          <div>
            <div class="contact-info-txt">Rabat, Maroc</div>
            <div class="contact-info-sub">Siège social Salonify</div>
          </div>
        </div>
        <div class="contact-info-row">
          <div class="contact-info-ic">&#9993;</div>
          <div>
            <div class="contact-info-txt">hello@salonify.ma</div>
            <div class="contact-info-sub">Réponse sous 24h</div>
          </div>
        </div>
        <div class="contact-info-row">
          <div class="contact-info-ic">&#128222;</div>
          <div>
            <div class="contact-info-txt">+(212) 7 63456-7890</div>
            <div class="contact-info-sub">Lun – Sam, 9h – 18h</div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="contact-page-foot">
    &copy; {{ date('Y') }} Salonify — Réservation Beauté Maroc
  </div>
</div>
@endsection
