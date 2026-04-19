<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="@yield('meta_description', 'Salonify — Réservez votre salon de beauté au Maroc')">
<title>@yield('title', 'Salonify') — Réservation Beauté</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@stack('styles')
</head>
<body class="{{ $bodyClass ?? '' }}">

{{-- ══ NAVBAR ══════════════════════════════════════════════════ --}}
<header class="nav">
  <div class="nav-r1">
    <a href="{{ route('home') }}" class="brand">
      <div class="brand-box">
        <img src="{{ asset('images/logo1.png') }}" alt="Salonify" class="logo-img">
      </div>
      <div>
        <div class="brand-name">Salonify</div>
        <div class="brand-sub">Reservation Beaute</div>
      </div>
    </a>

    {{-- Cloche notifications (auth) --}}
    @auth
    <div style="margin-left:auto;margin-right:1rem;position:relative;display:flex;align-items:center;gap:.75rem">
      <a href="{{ route('client.notifications.index') }}" class="notif-btn" style="position:relative">
        &#9993;
        @php $nbNotifs = auth()->user()->notificationsNonLues()->count(); @endphp
        @if($nbNotifs > 0)
          <div class="notif-dot"></div>
        @endif
      </a>
      <span style="font-size:.84rem;font-weight:600;color:var(--ink-s)">
        {{ auth()->user()->prenom }}
      </span>
    </div>
    @endauth

    <div class="nav-acts">
      @guest
        <a href="{{ route('login') }}" class="btn-log">Connexion</a>
        <a href="{{ route('register') }}" class="btn-ins">Inscription</a>
      @else
        @if(auth()->user()->isSalon())
          <a href="{{ route('salon.dashboard') }}" class="btn-log">Mon salon</a>
        @elseif(auth()->user()->isAdmin())
          <a href="{{ route('admin.dashboard') }}" class="btn-log">Admin</a>
        @else
          <a href="{{ route('client.dashboard') }}" class="btn-log">Mon espace</a>
        @endif
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
          @csrf
          <button type="submit" class="btn-ins" style="cursor:pointer;border:none">Déconnexion</button>
        </form>
      @endguest
    </div>

    <button class="burger" id="brg"><span></span><span></span><span></span></button>
  </div>

  <span class="nav-line"></span>

  <div class="nav-r2">
    <a href="{{ route('home') }}"
       class="{{ request()->routeIs('home') ? 'on' : '' }}">Accueil</a>
    <a href="{{ route('villes.index') }}"
       class="{{ request()->routeIs('villes.*') ? 'on' : '' }}">Emplacements</a>
    @auth
      @if(auth()->user()->isClient())
        <a href="{{ route('client.reservations.index') }}"
           class="{{ request()->routeIs('client.reservations.*') ? 'on' : '' }}">Mes réservations</a>
      @endif
    @endauth
    <a href="{{ route('about') }}"
       class="{{ request()->routeIs('about') ? 'on' : '' }}">À propos</a>
    <a href="{{ route('contact.show') }}"
       class="{{ request()->routeIs('contact.*') ? 'on' : '' }}">Contact</a>
  </div>

  {{-- Menu mobile --}}
  <nav class="mob" id="mm">
    <a href="{{ route('home') }}">Accueil</a>
    <a href="{{ route('villes.index') }}">Emplacements</a>
    <a href="{{ route('about') }}">À propos</a>
    <a href="{{ route('contact.show') }}">Contact</a>
    @auth
      <a href="{{ route('client.reservations.index') }}">Mes réservations</a>
    @endauth
    <div class="mob-cta">
      @guest
        <a href="{{ route('login') }}" class="mc">Connexion</a>
        <a href="{{ route('register') }}" class="mi">Inscription</a>
      @else
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="mi" style="width:100%;border:none;cursor:pointer">
            Déconnexion
          </button>
        </form>
      @endguest
    </div>
  </nav>
</header>

{{-- ══ FLASH MESSAGES ══════════════════════════════════════════ --}}
@if(session()->hasAny(['success','error','warning','info']))
  <div class="flash-zone">
    @foreach(['success','error','warning','info'] as $type)
      @if(session($type))
        <div class="flash flash-{{ $type }}">
          {{ session($type) }}
          <button class="flash-close" onclick="this.parentElement.remove()">&#10005;</button>
        </div>
      @endif
    @endforeach
  </div>
@endif

{{-- ══ CONTENU PRINCIPAL ══════════════════════════════════════ --}}
<main>
  @yield('content')
</main>

{{-- ══ FOOTER ═════════════════════════════════════════════════ --}}
<footer class="foot">
  <div class="foot-bar">
    <div class="fl">hello@Salonify.ma</div>
    <div class="fm">
      <div class="fm-t">Pour plus d'informations</div>
      <div class="fm-r"></div>
    </div>
    <div class="fr">+(212) 7 63456-7890</div>
  </div>
</footer>

<script>
// Burger menu
const brg = document.getElementById('brg');
const mm  = document.getElementById('mm');
if (brg) brg.addEventListener('click', () => mm.classList.toggle('open'));

// Fermer les flash automatiquement après 5s
document.querySelectorAll('.flash').forEach(f => {
  setTimeout(() => f.remove(), 5000);
});
</script>
@stack('scripts')
</body>
</html>
