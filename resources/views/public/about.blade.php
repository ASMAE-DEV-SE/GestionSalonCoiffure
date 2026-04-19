@extends('layouts.app')
@section('title', 'À propos de nous')
@section('meta_description', 'Salonify — Découvrez notre histoire, notre mission et notre équipe dédiée à sublimer la beauté marocaine.')

@push('styles')
<style>
.about-hero {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 520px;
  overflow: hidden;
}
.about-hero-left {
  background: var(--p1);
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 5rem 4rem;
}
.about-hero-right {
  position: relative;
  overflow: hidden;
}
.about-hero-right img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.about-rule {
  width: 52px;
  height: 2px;
  background: var(--p4d);
  margin-bottom: 1.4rem;
}
.about-hero-title {
  font-family: var(--fh);
  font-size: 3rem;
  font-weight: 700;
  line-height: 1.12;
  color: var(--ink-h);
  margin-bottom: 1.2rem;
}
.about-hero-text {
  font-size: .95rem;
  color: var(--ink-s);
  line-height: 1.8;
  max-width: 460px;
}

/* Mission section */
.about-mission {
  padding: 5rem 0;
  background: #fff;
}
.about-section-label {
  font-size: .7rem;
  font-weight: 700;
  letter-spacing: 2.5px;
  text-transform: uppercase;
  color: var(--p4d);
  margin-bottom: .7rem;
}
.about-section-title {
  font-family: var(--fh);
  font-size: 2.4rem;
  color: var(--ink-h);
  margin-bottom: 1.2rem;
  line-height: 1.15;
}
.about-section-text {
  font-size: .92rem;
  color: var(--ink-s);
  line-height: 1.85;
  max-width: 560px;
}

/* Values */
.about-values {
  padding: 5rem 0;
  background: var(--bg);
}
.values-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
  margin-top: 3rem;
}
.value-card {
  background: #fff;
  border: 1px solid var(--border2);
  border-radius: 16px;
  padding: 2.4rem 2rem;
  text-align: center;
  transition: var(--tr);
}
.value-card:hover {
  border-color: var(--p4);
  box-shadow: 0 4px 24px rgba(107,122,82,.12);
  transform: translateY(-3px);
}
.value-icon {
  font-size: 2.2rem;
  margin-bottom: 1rem;
  display: block;
}
.value-title {
  font-family: var(--fh);
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--ink-h);
  margin-bottom: .6rem;
}
.value-desc {
  font-size: .85rem;
  color: var(--ink-m);
  line-height: 1.7;
}

/* Two-col content */
.about-split {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  padding: 5rem 0;
}
.about-split.reverse { direction: rtl; }
.about-split.reverse > * { direction: ltr; }
.about-split-img {
  width: 100%;
  aspect-ratio: 4/3;
  border-radius: 24px;
  overflow: hidden;
  position: relative;
}
.about-split-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.about-split-img::before {
  content: '';
  position: absolute;
  inset: -8px;
  border: 2px solid var(--p3);
  border-radius: 28px;
  z-index: -1;
}

/* Stats */
.about-stats {
  background: var(--p4d);
  padding: 4rem 0;
}
.about-stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  text-align: center;
}
.about-stat-val {
  font-family: var(--fh);
  font-size: 3rem;
  font-weight: 700;
  color: #fff;
  line-height: 1;
  margin-bottom: .4rem;
}
.about-stat-lbl {
  font-size: .78rem;
  font-weight: 500;
  color: rgba(255,255,255,.75);
  letter-spacing: .5px;
  text-transform: uppercase;
}

/* Testimonials */
.about-testimonials {
  padding: 5rem 0;
  background: var(--p1);
}
.testimonials-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin-top: 3rem;
}
.testimonial-card {
  background: #fff;
  border-radius: 16px;
  padding: 2rem 1.8rem;
  box-shadow: 0 2px 12px rgba(107,122,82,.08);
}
.testimonial-stars {
  color: #D4A844;
  font-size: 1rem;
  margin-bottom: .8rem;
  letter-spacing: 2px;
}
.testimonial-text {
  font-size: .88rem;
  color: var(--ink-s);
  line-height: 1.75;
  font-style: italic;
  margin-bottom: 1.2rem;
}
.testimonial-author {
  display: flex;
  align-items: center;
  gap: .75rem;
}
.testimonial-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid var(--p3);
  flex-shrink: 0;
}
.testimonial-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.testimonial-name {
  font-size: .82rem;
  font-weight: 700;
  color: var(--ink-h);
  text-transform: uppercase;
  letter-spacing: .5px;
}
.testimonial-role {
  font-size: .75rem;
  color: var(--ink-m);
}

/* CTA bottom */
.about-cta {
  padding: 5rem 0;
  background: #fff;
  text-align: center;
}

@media (max-width: 900px) {
  .about-hero { grid-template-columns: 1fr; }
  .about-hero-right { height: 320px; }
  .about-hero-left { padding: 3rem 1.5rem; }
  .about-hero-title { font-size: 2.2rem; }
  .values-grid { grid-template-columns: 1fr 1fr; }
  .about-split { grid-template-columns: 1fr; gap: 2rem; }
  .about-split.reverse { direction: ltr; }
  .about-stats-grid { grid-template-columns: repeat(2, 1fr); gap: 2rem; }
  .testimonials-grid { grid-template-columns: 1fr; }
}
@media (max-width: 580px) {
  .values-grid { grid-template-columns: 1fr; }
  .about-stats-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

@section('content')

{{-- ══ HERO ══════════════════════════════════════════════════════ --}}
<section class="about-hero">
  <div class="about-hero-left">
    <div class="about-rule"></div>
    <h1 class="about-hero-title">
      À propos<br>de <em style="font-style:italic;color:var(--p4d)">Salonify</em>
    </h1>
    <p class="about-hero-text">
      Salonify est né d'une vision simple : rendre la beauté accessible, la réservation intuitive,
      et l'expérience en salon inoubliable. Nous connectons les Marocains aux meilleurs salons
      de beauté partout dans le pays.
    </p>
    <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:2rem">
      <a href="{{ route('villes.index') }}" class="btn-dk">Découvrir nos salons</a>
      <a href="{{ route('contact.show') }}" class="btn-gh">Nous contacter</a>
    </div>
  </div>
  <div class="about-hero-right">
    <img src="{{ asset('images/arriere_plan2.jpeg') }}" alt="Salon de beauté marocain Salonify">
  </div>
</section>

{{-- ══ NOTRE HISTOIRE ═══════════════════════════════════════════ --}}
<section class="about-mission">
  <div class="wrap">
    <div class="about-split">
      <div>
        <div class="about-section-label">Notre histoire</div>
        <h2 class="about-section-title">
          Une plateforme pensée<br><em style="font-style:italic;color:var(--p4d)">pour vous</em>
        </h2>
        <p class="about-section-text">
          Fondée avec la passion du beau et le souci du service, Salonify est la première plateforme
          marocaine de réservation de salons de beauté en ligne. Nous avons réuni les meilleurs
          établissements — coiffure, soins, onglerie, bien-être — dans une seule application simple
          d'utilisation.
        </p>
        <p class="about-section-text" style="margin-top:1rem">
          Notre mission est de valoriser l'artisanat local de la beauté marocaine tout en offrant
          à chaque client une expérience digitale fluide et moderne. Chaque salon référencé est
          vérifié et validé par notre équipe pour garantir la qualité de service.
        </p>
      </div>
      <div class="about-split-img">
        <img src="{{ asset('images/arriere_plan1.jpeg') }}" alt="Intérieur salon de beauté">
      </div>
    </div>
  </div>
</section>

{{-- ══ NOS VALEURS ═════════════════════════════════════════════ --}}
<section class="about-values">
  <div class="wrap">
    <div style="text-align:center;max-width:520px;margin:0 auto">
      <div class="about-section-label">Ce qui nous guide</div>
      <h2 class="about-section-title" style="max-width:none">Nos <em style="font-style:italic;color:var(--p4d)">valeurs</em></h2>
    </div>
    <div class="values-grid">
      <div class="value-card">
        <span class="value-icon">&#10022;</span>
        <div class="value-title">Excellence</div>
        <p class="value-desc">
          Chaque salon sur notre plateforme est soigneusement sélectionné et validé pour garantir
          des prestations d'exception et une expérience client irréprochable.
        </p>
      </div>
      <div class="value-card">
        <span class="value-icon">&#9825;</span>
        <div class="value-title">Authenticité</div>
        <p class="value-desc">
          Nous célébrons la beauté marocaine dans toute sa richesse. De l'hammam traditionnel aux
          soins modernes, nous valorisons l'héritage et le savoir-faire local.
        </p>
      </div>
      <div class="value-card">
        <span class="value-icon">&#128274;</span>
        <div class="value-title">Confiance</div>
        <p class="value-desc">
          La transparence est au cœur de notre démarche. Avis vérifiés, profils certifiés,
          réservations sécurisées — votre confiance est notre priorité absolue.
        </p>
      </div>
    </div>
  </div>
</section>

{{-- ══ POUR LES PROFESSIONNELS ═════════════════════════════════ --}}
<section style="background:#fff;padding:5rem 0">
  <div class="wrap">
    <div class="about-split reverse">
      <div>
        <div class="about-section-label">Rejoignez le réseau</div>
        <h2 class="about-section-title">
          Développez votre<br><em style="font-style:italic;color:var(--p4d)">clientèle</em>
        </h2>
        <p class="about-section-text">
          Vous êtes professionnel de la beauté ? Salonify met votre talent en lumière.
          Inscription gratuite, aucune commission sur vos réservations, gestion complète
          de votre agenda depuis votre espace dédié.
        </p>
        <ul style="margin-top:1.4rem;list-style:none;display:flex;flex-direction:column;gap:.6rem">
          @foreach(['Profil salon personnalisé', 'Gestion des réservations en temps réel', 'Rappels automatiques pour vos clients', 'Statistiques de performance', 'Validation et visibilité immédiate'] as $item)
          <li style="display:flex;align-items:center;gap:.7rem;font-size:.88rem;color:var(--ink-s)">
            <span style="width:20px;height:20px;background:var(--p4);border-radius:50%;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.65rem;flex-shrink:0">&#10003;</span>
            {{ $item }}
          </li>
          @endforeach
        </ul>
        <div style="margin-top:2rem">
          <a href="{{ route('register') }}?role=salon" class="btn-dk">Inscrire mon salon</a>
        </div>
      </div>
      <div class="about-split-img">
        <img src="{{ asset('images/image1.jpeg') }}" alt="Professionnel beauté Salonify">
      </div>
    </div>
  </div>
</section>

{{-- ══ STATS ═══════════════════════════════════════════════════ --}}
<section class="about-stats">
  <div class="wrap">
    <div class="about-stats-grid">
      <div>
        <div class="about-stat-val">{{ $stats['salons'] }}+</div>
        <div class="about-stat-lbl">Salons référencés</div>
      </div>
      <div>
        <div class="about-stat-val">{{ $stats['villes'] }}</div>
        <div class="about-stat-lbl">Villes couvertes</div>
      </div>
      <div>
        <div class="about-stat-val">{{ number_format($stats['reservations']) }}+</div>
        <div class="about-stat-lbl">Réservations effectuées</div>
      </div>
      <div>
        <div class="about-stat-val">4.8</div>
        <div class="about-stat-lbl">Note moyenne des salons</div>
      </div>
    </div>
  </div>
</section>

{{-- ══ TÉMOIGNAGES ═════════════════════════════════════════════ --}}
<section class="about-testimonials">
  <div class="wrap">
    <div style="text-align:center;max-width:480px;margin:0 auto">
      <div class="about-section-label">Ils nous font confiance</div>
      <h2 class="about-section-title">Ce que disent<br><em style="font-style:italic;color:var(--p4d)">nos clients</em></h2>
    </div>
    <div class="testimonials-grid">
      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">
          "Le salon de beauté marocain a transformé mon look et boosté ma confiance !
          Services exceptionnels et une atmosphère vraiment apaisante."
        </p>
        <div class="testimonial-author">
          <div class="testimonial-avatar">
            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80" alt="Sarah">
          </div>
          <div>
            <div class="testimonial-name">Sarah</div>
            <div class="testimonial-role">Spécialiste Marketing</div>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">
          "Je me suis senti choyé et valorisé. Le personnel est qualifié et attentionné,
          chaque visite est une expérience délicieuse."
        </p>
        <div class="testimonial-author">
          <div class="testimonial-avatar">
            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&q=80" alt="Amir">
          </div>
          <div>
            <div class="testimonial-name">Amir</div>
            <div class="testimonial-role">Graphiste</div>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">
          "Chaque soin est un véritable voyage. L'ambiance et le professionnalisme ici
          sont remarquables, je repars toujours détendue et ressourcée."
        </p>
        <div class="testimonial-author">
          <div class="testimonial-avatar">
            <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100&q=80" alt="Leila">
          </div>
          <div>
            <div class="testimonial-name">Leila</div>
            <div class="testimonial-role">Enseignante</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ══ CTA FINAL ════════════════════════════════════════════════ --}}
<section class="about-cta">
  <div class="wrap" style="max-width:640px">
    <div class="rule" style="margin:0 auto 1.5rem"></div>
    <h2 style="font-family:var(--fh);font-size:2.5rem;color:var(--ink-h);margin-bottom:1.2rem">
      Prêt à vivre l'<em style="font-style:italic;color:var(--p4d)">expérience</em> Salonify ?
    </h2>
    <p style="font-size:.94rem;color:var(--ink-s);line-height:1.8;margin-bottom:2.2rem">
      Rejoignez des milliers de clients qui font confiance à Salonify pour leurs rendez-vous beauté.
      Inscription gratuite, réservation en quelques clics.
    </p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
      <a href="{{ route('villes.index') }}" class="btn-dk">Trouver un salon</a>
      @guest
        <a href="{{ route('register') }}" class="btn-gh">Créer mon compte</a>
      @endguest
    </div>
  </div>
</section>

@endsection
